-- #! mysql

-- #{ kits.table
CREATE TABLE IF NOT EXISTS kits (
    name VARCHAR(64) NOT NULL,
    prefix VARCHAR(64) NOT NULL,
    armor TEXT NOT NULL,
    items TEXT NOT NULL,
    cooldown INT DEFAULT NULL,
    price FLOAT DEFAULT NULL,
    permission VARCHAR(128) DEFAULT NULL,
    icon VARCHAR(128) DEFAULT NULL,
    store_in_chest TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (name)
    );
-- #}

-- #{ kits.insert
-- # :name string
-- # :prefix string
-- # :armor string
-- # :items string
-- # :cooldown int default
-- # :price float default
-- # :permission string default
-- # :icon string default
-- # :store_in_chest int
INSERT INTO kits (name, prefix, armor, items, cooldown, price, permission, icon, store_in_chest)
VALUES (:name, :prefix, :armor, :items, :cooldown, :price, :permission, :icon, :store_in_chest)
    ON DUPLICATE KEY UPDATE
    prefix = VALUES(prefix),
    armor = VALUES(armor),
    items = VALUES(items),
     cooldown = VALUES(cooldown),
     price = VALUES(price),
     permission = VALUES(permission),
     icon = VALUES(icon),
     store_in_chest = VALUES(store_in_chest);
-- #}

-- #{ kits.get_all
SELECT * FROM kits;
-- #}

-- #{ kits.delete
-- # :name string
DELETE FROM kits WHERE name = :name;
-- #}

-- #{ categories.table
CREATE TABLE IF NOT EXISTS categories (
    name VARCHAR(64) NOT NULL,
    prefix VARCHAR(64) NOT NULL,
    permission VARCHAR(128) DEFAULT NULL,
    icon VARCHAR(128) DEFAULT NULL,
    PRIMARY KEY (name)
    );
-- #}

-- #{ categories.insert
-- # :name string
-- # :prefix string
-- # :permission string default
-- # :icon string default
INSERT INTO categories (name, prefix, permission, icon)
VALUES (:name, :prefix, :permission, :icon)
    ON DUPLICATE KEY UPDATE
        prefix = VALUES(prefix),
        permission = VALUES(permission),
        icon = VALUES(icon);
-- #}

-- #{ categories.get_all
SELECT * FROM categories;
-- #}

-- #{ categories.delete
-- # :name string
DELETE FROM categories WHERE name = :name;
-- #}

-- #{ category_kits.table
CREATE TABLE IF NOT EXISTS category_kits (
    category_name VARCHAR(64) NOT NULL,
    kit_name VARCHAR(64) NOT NULL,
    PRIMARY KEY (category_name, kit_name)
    );
-- #}

-- #{ category_kits.insert
-- # :category_name string
-- # :kit_name string
INSERT INTO category_kits (category_name, kit_name)
VALUES (:category_name, :kit_name)
    ON DUPLICATE KEY UPDATE kit_name = VALUES(kit_name);
-- #}

-- #{ category_kits.get_for_category
-- # :category_name string
SELECT kit_name FROM category_kits WHERE category_name = :category_name;
-- #}

-- #{ category_kits.get_all
SELECT * FROM category_kits;
-- #}

-- #{ cooldowns.table
CREATE TABLE IF NOT EXISTS cooldowns (
                                         uuid VARCHAR(36) NOT NULL,
    kit VARCHAR(64) NOT NULL,
    cooldown INT NOT NULL,
    PRIMARY KEY (uuid, kit)
    );
-- #}

-- #{ cooldowns.set
-- # :uuid string
-- # :kit string
-- # :cooldown int
INSERT INTO cooldowns (uuid, kit, cooldown)
VALUES (:uuid, :kit, :cooldown)
    ON DUPLICATE KEY UPDATE cooldown = VALUES(cooldown);
-- #}

-- #{ cooldowns.get
-- # :uuid string
-- # :kit string
SELECT cooldown FROM cooldowns WHERE uuid = :uuid AND kit = :kit;
-- #}

-- #{ cooldowns.remove
-- # :uuid string
-- # :kit string
DELETE FROM cooldowns WHERE uuid = :uuid AND kit = :kit;
-- #}

-- #{ cooldowns.cleanup
-- # :time int
DELETE FROM cooldowns WHERE cooldown <= :time;
-- #}

-- #{ cooldowns.get_all
SELECT * FROM cooldowns;
-- #}
