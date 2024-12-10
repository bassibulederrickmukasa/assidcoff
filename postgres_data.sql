-- Insert roles data
INSERT INTO roles (id, name, description) VALUES 
(1, 'admin', 'System administrator with full access'),
(2, 'manager', 'Manager with oversight capabilities'),
(3, 'staff', 'Regular staff member');

-- Insert users data
INSERT INTO users (id, username, password, role_id, created_at, status) VALUES 
(1, 'admin', '$2y$10$T6ko02McfGWGZxOjUdiYCOjR2hhEF7KuwrUr1Fi9tnywWNyekfQyq', 1, '2024-11-23 00:17:49', 'active'),
(2, 'admin22', '$2y$10$EELtnpgxurSRbueqNVUEfOmmEaQ8hBwWNZt9rnGnbTmBIla75r9Aa', 2, '2024-11-30 00:07:14', 'active'),
(3, 'staff', '$2y$10$0.5dWksVHPgWjeUcY6Zv1.LYgskW4U1g77LTRP4brHdi9bEgsAYXO', 3, '2024-11-30 00:09:20', 'active'),
(4, 'fadhil', '$2y$10$KnNQTLALKkpmQavEEOcUEed/lwOqRxZVmcRV3Jpybm8kUrsJQl/be', 2, '2024-12-04 09:01:57', 'active');

-- Insert staff data
INSERT INTO staff (id, name, role, contact) VALUES 
(1, 'Hamza', 'supplier', '0700118085'),
(2, 'wilber', 'supplier', '0756351211');
