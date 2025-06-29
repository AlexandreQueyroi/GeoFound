-- Corriger la colonne type pour accepter 'physical'
ALTER TABLE rewards MODIFY COLUMN type VARCHAR(32) NOT NULL; 