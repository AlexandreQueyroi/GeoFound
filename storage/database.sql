CREATE DATABASE geofound;
USE geofound;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(256) UNIQUE,
    rank VARCHAR(32),
    password TEXT,
    email VARCHAR(64) UNIQUE,
    description TEXT,
    desactivated BOOLEAN DEFAULT FALSE,
    token TEXT UNIQUE,
    connected DATETIME,
    verified BOOLEAN DEFAULT FALSE,
    verified_at DATETIME,
    avatar TEXT,
    point INT DEFAULT 0
);

CREATE TABLE message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    posted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    content VARCHAR(512),
    state VARCHAR(16) DEFAULT 'sent'
);

CREATE TABLE support (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    content VARCHAR(1024),
    state VARCHAR(50) DEFAULT 'open',
    respond_at DATETIME,
    assigned_to INT,
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE avatar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hair VARCHAR(50),
    hair_color VARCHAR(50),
    eyes VARCHAR(50),
    skin VARCHAR(50),
    mouth VARCHAR(50),
    nose VARCHAR(50),
    head VARCHAR(50),
    accessory VARCHAR(50)
);

CREATE TABLE follow (
    id INT PRIMARY KEY AUTO_INCREMENT,
    follow_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    state VARCHAR(50) DEFAULT 'pending',
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (user1_id, user2_id)
);

CREATE TABLE post_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    content LONGTEXT
);

CREATE TABLE post (
    id INT PRIMARY KEY AUTO_INCREMENT,
    latitude FLOAT,
    longitude FLOAT,
    content_id INT NOT NULL,
    user INT NOT NULL,
    name VARCHAR(256),
    description VARCHAR(512),
    FOREIGN KEY (user) REFERENCES users(id),
    FOREIGN KEY (content_id) REFERENCES post_content(id),
    date DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content VARCHAR(512) NOT NULL,
    comment_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE reaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    state ENUM('like', 'love', 'wow') DEFAULT 'like',
    react_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaction (post_id, user_id)
);

CREATE TABLE bookmarks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
    UNIQUE KEY unique_bookmark (user_id, post_id)
);

CREATE TABLE report (
    id INT PRIMARY KEY AUTO_INCREMENT,
    report_to INT,
    report_reason VARCHAR(512),
    report_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    assigned_to INT,
    response VARCHAR(512),
    status VARCHAR(50) DEFAULT 'not treated',
    commentary TEXT,
    FOREIGN KEY (report_to) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE sanction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50),
    reason VARCHAR(512),
    begin_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_at DATETIME,
    cancel BOOLEAN DEFAULT FALSE
);

CREATE TABLE captcha (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(256),
    response TEXT,
    enabled BOOLEAN DEFAULT FALSE,
    requested BOOLEAN DEFAULT FALSE,
    success_requested BOOLEAN DEFAULT FALSE
);

CREATE TABLE order_table (
    id INT PRIMARY KEY AUTO_INCREMENT,
    made_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deliver_at DATETIME
);

CREATE TABLE reward (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(256),
    description VARCHAR(512),
    stock INT DEFAULT 0,
    point INT DEFAULT 0,
    image LONGTEXT
);

CREATE TABLE user_message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    FOREIGN KEY (message_id) REFERENCES message(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tables pour le système de permissions
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ranks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#3B82F6',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rank_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rank_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rank_id) REFERENCES ranks(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rank_permission (rank_id, permission_id)
);

CREATE TABLE user_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    expires_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_permission (user_id, permission_id)
);

CREATE TABLE page_maintenance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_path VARCHAR(255) NOT NULL,
    page_name VARCHAR(100) NOT NULL,
    is_maintenance BOOLEAN DEFAULT FALSE,
    maintenance_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE page_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_path VARCHAR(255) NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_permission (page_path, permission_id)
);

INSERT INTO permissions (name, description) VALUES
('*', 'Permission administrateur - Accès à tout'),
('admin.access', 'Accès au panel d\'administration'),
('admin.users', 'Gestion des utilisateurs'),
('admin.ranks', 'Gestion des rangs'),
('admin.permissions', 'Gestion des permissions'),
('admin.maintenance', 'Gestion de la maintenance'),
('page.access.*', 'Accès à toutes les pages'),
('page.maintenance.bypass', 'Bypass de la maintenance globale'),
('page.maintenance.manage', 'Gestion de la maintenance des pages'),
('page.permission.manage', 'Gestion des permissions de pages'),
('user.ban', 'Bannir des utilisateurs'),
('user.unban', 'Débannir des utilisateurs'),
('user.edit', 'Modifier les utilisateurs'),
('post.moderate', 'Modérer les posts'),
('comment.moderate', 'Modérer les commentaires'),
('message.global', 'Messagerie globale'),
('log.view', 'Voir les logs'),
('captcha.manage', 'Gestion des captchas');

INSERT INTO ranks (name, color) VALUES
('admin', '#EF4444'),
('moderator', '#F59E0B'),
('user', '#3B82F6');

INSERT INTO rank_permissions (rank_id, permission_id) 
SELECT r.id, p.id FROM ranks r, permissions p 
WHERE r.name = 'admin' AND p.name = '*';

INSERT INTO rank_permissions (rank_id, permission_id) 
SELECT r.id, p.id FROM ranks r, permissions p 
WHERE r.name = 'moderator' AND p.name IN (
    'admin.access', 'admin.users', 'user.ban', 'user.unban', 'user.edit',
    'post.moderate', 'comment.moderate', 'message.global', 'log.view'
);

INSERT INTO users (pseudo, rank, password, email, description, verified) VALUES 
('admin', 'admin', '$2y$10$mjIYy.RcnzPIGytlmqifBudv8b5mqW.0KE/JpIFXmkRiv0WrxpfB2', 'admin@geofound.com', 'Default admin account', TRUE);

CREATE USER IF NOT EXISTS 'geofound'@'%' IDENTIFIED BY 'geofound-2025';
GRANT ALL PRIVILEGES ON geofound.* TO 'geofound'@'%';
FLUSH PRIVILEGES;

exit
