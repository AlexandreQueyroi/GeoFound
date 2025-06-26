-- Script de vérification et création des tables pour la modal de gestion des permissions et maintenance

-- Vérifier si la table page_maintenance existe
CREATE TABLE IF NOT EXISTS page_maintenance (
    page_path VARCHAR(255) PRIMARY KEY,
    page_name VARCHAR(255) NOT NULL,
    is_maintenance BOOLEAN DEFAULT FALSE,
    maintenance_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vérifier si la table page_permissions existe
CREATE TABLE IF NOT EXISTS page_permissions (
    page_path VARCHAR(255),
    permission_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (page_path, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Vérifier si les permissions nécessaires existent
INSERT IGNORE INTO permissions (name, description) VALUES
('admin.maintenance', 'Gérer la maintenance du site'),
('page.permission.manage', 'Gérer les permissions des pages'),
('user.logged', 'Utilisateur connecté');

-- Vérifier si les tables existent
SHOW TABLES LIKE 'page_maintenance';
SHOW TABLES LIKE 'page_permissions';

-- Afficher la structure des tables
DESCRIBE page_maintenance;
DESCRIBE page_permissions;

-- Vérifier les permissions
SELECT * FROM permissions WHERE name IN ('admin.maintenance', 'page.permission.manage', 'user.logged'); 