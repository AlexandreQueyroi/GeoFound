-- Ajout des colonnes pour les récompenses physiques
ALTER TABLE rewards 
ADD COLUMN price DECIMAL(10,2) NULL DEFAULT NULL,
ADD COLUMN stock INT NULL DEFAULT NULL;

-- Mise à jour des récompenses existantes pour avoir des valeurs par défaut
UPDATE rewards SET price = NULL, stock = NULL WHERE type != 'physical'; 