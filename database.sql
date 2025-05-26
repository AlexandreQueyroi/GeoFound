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
    avatar TEXT
);

CREATE TABLE message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    posted_at DATETIME,
    content VARCHAR(512),
    state VARCHAR(16) DEFAULT 'sent'
);

CREATE TABLE support (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATETIME,
    contant VARCHAR(1024),
    state VARCHAR(50),
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
    follow_at DATETIME,
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
    date DATETIME
);

CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_at DATETIME,
    content VARCHAR(256)
);

CREATE TABLE reaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    state VARCHAR(50),
    react_at DATETIME
);

CREATE TABLE report (
    id INT PRIMARY KEY AUTO_INCREMENT,
    report_to INT,
    report_reason VARCHAR(512),
    report_date DATETIME,
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
    begin_at DATETIME,
    end_at DATETIME,
    cancel BOOLEAN
);

CREATE TABLE captcha (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(256),
    response TEXT,
    enabled BOOLEAN,
    requested BOOLEAN,
    success_requested BOOLEAN
);

CREATE TABLE order_table (
    id INT PRIMARY KEY AUTO_INCREMENT,
    made_at DATETIME,
    deliver_at DATETIME
);

CREATE TABLE reward (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(256),
    description VARCHAR(512),
    stock INT
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

INSERT INTO users (pseudo, rank, password, description) VALUES ('admin', "admin", '$2y$10$mjIYy.RcnzPIGytlmqifBudv8b5mqW.0KE/JpIFXmkRiv0WrxpfB2', 'Default admin account');

FLUSH PRIVILEGES;
CREATE USER 'geofound'@'%' IDENTIFIED BY 'geofound-2025';
GRANT ALL PRIVILEGES ON geofound.* TO 'geofound'@'%';
FLUSH PRIVILEGES;

exit
