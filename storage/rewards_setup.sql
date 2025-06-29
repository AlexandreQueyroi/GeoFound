-- Script de création des tables pour le système de récompenses

-- Table des récompenses
CREATE TABLE IF NOT EXISTS rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('badge', 'title', 'avatar_frame', 'background', 'emote', 'achievement') NOT NULL DEFAULT 'badge',
    icon VARCHAR(50) DEFAULT 'trophy',
    required_level INT NOT NULL DEFAULT 1,
    rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
    points_value INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table de liaison utilisateur-récompenses
CREATE TABLE IF NOT EXISTS user_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_equipped BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_user_reward (user_id, reward_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE
);

-- Ajout de la colonne level à la table users si elle n'existe pas
ALTER TABLE users ADD COLUMN IF NOT EXISTS level INT DEFAULT 1;
ALTER TABLE users ADD COLUMN IF NOT EXISTS experience INT DEFAULT 0;

-- Insertion de quelques récompenses d'exemple
INSERT INTO rewards (name, description, type, icon, required_level, rarity, points_value) VALUES
-- Récompenses de niveau
('Debutant', 'Aventurez-vous dans le monde de GeoFound', 'badge', 'seedling', 1, 'common', 10),
('Explorateur', 'Vous commencez a explorer activement', 'badge', 'map', 5, 'common', 25),
('Voyageur', 'Un voyageur experimente', 'badge', 'compass', 10, 'rare', 50),
('Aventurier', 'Un aventurier chevronne', 'badge', 'sword', 20, 'rare', 100),
('Conquerant', 'Vous avez conquis de nombreux territoires', 'badge', 'crown', 35, 'epic', 200),
('Legende', 'Une legende vivante de GeoFound', 'badge', 'star', 50, 'legendary', 500),

-- Récompenses de posts
('Premier Post', 'Vous avez cree votre premier post', 'achievement', 'edit', 1, 'common', 15),
('Auteur Prolifique', 'Vous avez cree 10 posts', 'achievement', 'pen', 1, 'rare', 75),
('Influenceur', 'Vous avez cree 50 posts', 'achievement', 'megaphone', 1, 'epic', 300),
('Createur de Contenu', 'Vous avez cree 100 posts', 'achievement', 'palette', 1, 'legendary', 750),

-- Récompenses de commentaires
('Commentateur', 'Vous avez laisse votre premier commentaire', 'achievement', 'message-circle', 1, 'common', 10),
('Debateur', 'Vous avez laisse 25 commentaires', 'achievement', 'message-square', 1, 'rare', 50),
('Orateur', 'Vous avez laisse 100 commentaires', 'achievement', 'mic', 1, 'epic', 200),

-- Récompenses sociales
('Sociable', 'Vous avez ajoute votre premier ami', 'achievement', 'hand', 1, 'common', 20),
('Populaire', 'Vous avez 10 amis', 'achievement', 'star', 1, 'rare', 100),
('Influenceur Social', 'Vous avez 25 amis', 'achievement', 'users', 1, 'epic', 250),

-- Récompenses de temps
('Fidele', 'Vous utilisez GeoFound depuis 7 jours', 'achievement', 'calendar', 1, 'common', 30),
('Regulier', 'Vous utilisez GeoFound depuis 30 jours', 'achievement', 'calendar-days', 1, 'rare', 150),
('Veteran', 'Vous utilisez GeoFound depuis 100 jours', 'achievement', 'building', 1, 'epic', 500),

-- Titres speciaux
('Nouveau Venu', 'Bienvenue dans la communaute', 'title', 'new', 1, 'common', 5),
('Membre Actif', 'Un membre actif de la communaute', 'title', 'check', 5, 'common', 25),
('Contributeur', 'Vous contribuez activement a la communaute', 'title', 'handshake', 10, 'rare', 75),
('Pilier', 'Un pilier de la communaute GeoFound', 'title', 'building', 20, 'epic', 200),
('Legende Vivante', 'Une legende de la communaute', 'title', 'crown', 35, 'legendary', 1000);

-- Index pour optimiser les performances
CREATE INDEX idx_user_rewards_user_id ON user_rewards(user_id);
CREATE INDEX idx_user_rewards_reward_id ON user_rewards(reward_id);
CREATE INDEX idx_rewards_required_level ON rewards(required_level);
CREATE INDEX idx_rewards_type ON rewards(type);
CREATE INDEX idx_rewards_rarity ON rewards(rarity); 