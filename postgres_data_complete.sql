-- Disable triggers temporarily
ALTER TABLE activity_logs DISABLE TRIGGER ALL;
ALTER TABLE backups DISABLE TRIGGER ALL;
ALTER TABLE boxes DISABLE TRIGGER ALL;
ALTER TABLE daily_production DISABLE TRIGGER ALL;
ALTER TABLE payments DISABLE TRIGGER ALL;
ALTER TABLE staff DISABLE TRIGGER ALL;
ALTER TABLE supplies DISABLE TRIGGER ALL;
ALTER TABLE supply_comments DISABLE TRIGGER ALL;
ALTER TABLE users DISABLE TRIGGER ALL;

-- Reset sequences
ALTER SEQUENCE activity_logs_id_seq RESTART WITH 1;
ALTER SEQUENCE backups_id_seq RESTART WITH 1;
ALTER SEQUENCE boxes_id_seq RESTART WITH 1;
ALTER SEQUENCE daily_production_id_seq RESTART WITH 1;
ALTER SEQUENCE payments_id_seq RESTART WITH 1;
ALTER SEQUENCE staff_id_seq RESTART WITH 1;
ALTER SEQUENCE supplies_id_seq RESTART WITH 1;
ALTER SEQUENCE supply_comments_id_seq RESTART WITH 1;
ALTER SEQUENCE users_id_seq RESTART WITH 1;

-- Clear existing data
TRUNCATE TABLE activity_logs CASCADE;
TRUNCATE TABLE backups CASCADE;
TRUNCATE TABLE boxes CASCADE;
TRUNCATE TABLE daily_production CASCADE;
TRUNCATE TABLE payments CASCADE;
TRUNCATE TABLE staff CASCADE;
TRUNCATE TABLE supplies CASCADE;
TRUNCATE TABLE supply_comments CASCADE;
TRUNCATE TABLE users CASCADE;

-- Insert boxes data
INSERT INTO boxes (id, box_type, price) VALUES
(1, 'small', 300.00),
(2, 'big', 500.00);

-- Insert activity_logs data
INSERT INTO activity_logs (id, user_id, action, details, created_at) VALUES
(1, NULL, 'login_failed', 'Failed login attempt for user: admin', '2024-11-23 00:25:02'),
(2, NULL, 'login_failed', 'Failed login attempt for user: admin', '2024-11-23 23:07:26'),
(3, NULL, 'login_failed', 'Failed login attempt for user: admin', '2024-11-23 23:16:17'),
(4, NULL, 'login_failed', 'Failed login attempt for user: admin', '2024-11-23 23:17:37'),
(5, 1, 'login', 'Successful login', '2024-11-23 23:39:51');

-- Re-enable triggers
ALTER TABLE activity_logs ENABLE TRIGGER ALL;
ALTER TABLE backups ENABLE TRIGGER ALL;
ALTER TABLE boxes ENABLE TRIGGER ALL;
ALTER TABLE daily_production ENABLE TRIGGER ALL;
ALTER TABLE payments ENABLE TRIGGER ALL;
ALTER TABLE staff ENABLE TRIGGER ALL;
ALTER TABLE supplies ENABLE TRIGGER ALL;
ALTER TABLE supply_comments ENABLE TRIGGER ALL;
ALTER TABLE users ENABLE TRIGGER ALL;
