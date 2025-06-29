-- Ajout de récompenses physiques style mug et autres objets
INSERT INTO rewards (name, description, type, icon, required_level, rarity, points_value, price, stock) VALUES
-- Mugs et tasses
('Mug GeoFound Premium', 'Un mug exclusif avec le logo GeoFound gravé', 'physical', 'tabler:cup', 5, 'rare', 500, 15.99, 50),
('Tasse à café Vintage', 'Tasse rétro avec design géographique', 'physical', 'tabler:cup', 10, 'epic', 1000, 24.99, 25),
('Mug Collector Édition Limitée', 'Mug numéroté avec design unique', 'physical', 'tabler:cup', 20, 'legendary', 2500, 49.99, 10),

-- Vêtements et accessoires
('T-shirt GeoFound', 'T-shirt 100% coton avec logo brodé', 'physical', 'tabler:tshirt', 3, 'common', 200, 19.99, 100),
('Casquette Explorer', 'Casquette avec broderie GeoFound', 'physical', 'tabler:hat', 7, 'rare', 750, 12.99, 75),
('Hoodie Premium', 'Pull à capuche avec design exclusif', 'physical', 'tabler:tshirt', 15, 'epic', 1500, 39.99, 30),

-- Objets de bureau
('Stylo Gravé', 'Stylo personnalisé avec nom gravé', 'physical', 'tabler:pen', 2, 'common', 100, 8.99, 200),
('Carnet de Notes', 'Carnet avec couverture personnalisée', 'physical', 'tabler:notebook', 4, 'rare', 300, 14.99, 80),
('Souris Gaming', 'Souris RGB avec logo GeoFound', 'physical', 'tabler:mouse', 12, 'epic', 1200, 29.99, 40),

-- Objets décoratifs
('Poster Carte du Monde', 'Poster haute qualité avec design unique', 'physical', 'tabler:photo', 6, 'rare', 400, 9.99, 60),
('Figurine Explorer', 'Figurine en résine peinte à la main', 'physical', 'tabler:user', 18, 'legendary', 3000, 79.99, 5),
('Lampe de Bureau', 'Lampe LED avec base personnalisée', 'physical', 'tabler:bulb', 8, 'epic', 800, 34.99, 35),

-- Accessoires tech
('Coque de téléphone', 'Coque personnalisée pour smartphone', 'physical', 'tabler:device-mobile', 5, 'common', 150, 11.99, 120),
('Chargeur sans fil', 'Chargeur Qi avec logo GeoFound', 'physical', 'tabler:device-mobile', 14, 'epic', 1300, 44.99, 25),
('Enceinte Bluetooth', 'Enceinte portable avec design exclusif', 'physical', 'tabler:speaker', 16, 'legendary', 2000, 89.99, 15),

-- Objets de collection
('Pin Collector', 'Pin émaillé avec design unique', 'physical', 'tabler:pin', 1, 'common', 50, 4.99, 300),
('Médaillon Explorer', 'Médaillon en métal avec gravure', 'physical', 'tabler:medal', 9, 'rare', 600, 18.99, 45),
('Trophée Achievement', 'Trophée en verre avec base gravée', 'physical', 'tabler:trophy', 25, 'legendary', 5000, 149.99, 3); 