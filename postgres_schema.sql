-- Create tables with PostgreSQL syntax

-- roles table (create first for foreign key references)
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INTEGER REFERENCES roles(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    status VARCHAR(20) DEFAULT 'active',
    email VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expiry TIMESTAMP
);

-- staff table
CREATE TABLE staff (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    contact VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- boxes table
CREATE TABLE boxes (
    id SERIAL PRIMARY KEY,
    box_type VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- daily_production table
CREATE TABLE daily_production (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER REFERENCES staff(id),
    box_id INTEGER REFERENCES boxes(id),
    quantity INTEGER NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- supplies table
CREATE TABLE supplies (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER REFERENCES staff(id),
    box_id INTEGER REFERENCES boxes(id),
    quantity INTEGER NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- supply_comments table
CREATE TABLE supply_comments (
    id SERIAL PRIMARY KEY,
    supply_id INTEGER REFERENCES supplies(id),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- payments table
CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER REFERENCES staff(id),
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_type VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- activity_logs table
CREATE TABLE activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- backups table
CREATE TABLE backups (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for frequently accessed columns
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_staff_name ON staff(name);
CREATE INDEX idx_daily_production_date ON daily_production(date);
CREATE INDEX idx_supplies_date ON supplies(date);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);

-- Create function for updating timestamps
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updating timestamps
CREATE TRIGGER update_daily_production_updated_at
    BEFORE UPDATE ON daily_production
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_supplies_updated_at
    BEFORE UPDATE ON supplies
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
