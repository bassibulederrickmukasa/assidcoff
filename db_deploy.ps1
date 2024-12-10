# Set password from parameter
param(
    [Parameter(Mandatory=$true)]
    [string]$Password
)

# PostgreSQL connection parameters
$PGHOST = "dpg-ctal8opu0jms73f0qk00-a.oregon-postgres.render.com"
$PGPORT = "5432"
$PGDATABASE = "assidcoff_inventory"
$PGUSER = "assidcoff_inventory_user"

# PostgreSQL binary path
$PSQL = "C:\Program Files\PostgreSQL\17\bin\psql.exe"

# Set password as environment variable
$env:PGPASSWORD = $Password

# Create backup directory if it doesn't exist
$backupDir = ".\backups"
if (-not (Test-Path -Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir | Out-Null
}

Write-Host "Starting database deployment..."

# First, let's create a backup of the existing data
Write-Host "Creating backup of existing data..."
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c "COPY (SELECT * FROM boxes) TO STDOUT WITH CSV HEADER" > "$backupDir\boxes_$timestamp.csv"
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c "COPY (SELECT * FROM big_boxes) TO STDOUT WITH CSV HEADER" > "$backupDir\big_boxes_$timestamp.csv"
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c "COPY (SELECT * FROM small_boxes) TO STDOUT WITH CSV HEADER" > "$backupDir\small_boxes_$timestamp.csv"

# Update boxes table with new records
Write-Host "Adding box records..."
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c @"
    -- Insert big box type if not exists
    INSERT INTO boxes (box_type, price)
    SELECT 'big', 50000.00
    WHERE NOT EXISTS (
        SELECT 1 FROM boxes WHERE box_type = 'big'
    );

    -- Insert small box type if not exists
    INSERT INTO boxes (box_type, price)
    SELECT 'small', 25000.00
    WHERE NOT EXISTS (
        SELECT 1 FROM boxes WHERE box_type = 'small'
    );
"@

# Create production tracking table
Write-Host "Creating production tracking table..."
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c @"
    -- Create daily_production table if not exists
    CREATE TABLE IF NOT EXISTS daily_production (
        id SERIAL PRIMARY KEY,
        staff_id INTEGER REFERENCES staff(id),
        box_id INTEGER REFERENCES boxes(id),
        quantity INTEGER NOT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Create indexes for performance
    CREATE INDEX IF NOT EXISTS idx_daily_production_date ON daily_production(date);
    CREATE INDEX IF NOT EXISTS idx_daily_production_staff_id ON daily_production(staff_id);
    CREATE INDEX IF NOT EXISTS idx_daily_production_box_id ON daily_production(box_id);
"@

# Migrate production data
Write-Host "Migrating production data..."
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c @"
    -- Migrate big boxes production data
    INSERT INTO daily_production (staff_id, box_id, quantity, date, created_at)
    SELECT 
        b.staff_id,
        (SELECT id FROM boxes WHERE box_type = 'big'),
        b.quantity,
        b.date,
        b.created_at
    FROM big_boxes b
    WHERE NOT EXISTS (
        SELECT 1 FROM daily_production dp
        WHERE dp.staff_id = b.staff_id
        AND dp.date = b.date
        AND dp.box_id = (SELECT id FROM boxes WHERE box_type = 'big')
    );

    -- Migrate small boxes production data
    INSERT INTO daily_production (staff_id, box_id, quantity, date, created_at)
    SELECT 
        s.staff_id,
        (SELECT id FROM boxes WHERE box_type = 'small'),
        s.quantity,
        s.date,
        s.created_at
    FROM small_boxes s
    WHERE NOT EXISTS (
        SELECT 1 FROM daily_production dp
        WHERE dp.staff_id = s.staff_id
        AND dp.date = s.date
        AND dp.box_id = (SELECT id FROM boxes WHERE box_type = 'small')
    );
"@

# Verify migration
Write-Host "Verifying migration..."
& $PSQL -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c @"
    -- Show box types and their prices
    SELECT box_type, price FROM boxes ORDER BY box_type;

    -- Show production summary
    SELECT 
        b.box_type,
        COUNT(*) as records,
        SUM(dp.quantity) as total_quantity
    FROM daily_production dp
    JOIN boxes b ON b.id = dp.box_id
    GROUP BY b.box_type
    ORDER BY b.box_type;
"@

Write-Host "Database deployment completed!"

# Clear password from environment
Remove-Item Env:PGPASSWORD
