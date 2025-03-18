-- #! sqlite

-- #{ kits.table
CREATE TABLE IF NOT EXISTS kits (
    name TEXT NOT NULL,
    prefix TEXT NOT NULL,
    armor TEXT NOT NULL,
    items TEXT NOT NULL,
    cooldown INTEGER DEFAULT NULL,
    price REAL DEFAULT NULL,
    permission TEXT DEFAULT NULL,
    icon TEXT DEFAULT NULL,
    store_in_chest INTEGER NOT NULL DEFAULT 1,
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
    ON CONFLICT(name) DO UPDATE SET
    prefix = excluded.prefix,
        armor = excluded.armor,
        items = excluded.items,
        cooldown = excluded.cooldown,
        price = excluded.price,
        permission = excluded.permission,
        icon = excluded.icon,
        store_in_chest = excluded.store_in_chest;
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
    name TEXT NOT NULL,
    prefix TEXT NOT NULL,
    permission TEXT DEFAULT NULL,
    icon TEXT DEFAULT NULL,
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
    ON CONFLICT(name) DO UPDATE SET
    prefix = excluded.prefix,
        permission = excluded.permission,
        icon = excluded.icon;
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
    category_name TEXT NOT NULL,
    kit_name TEXT NOT NULL,
    PRIMARY KEY (category_name, kit_name)
    );
-- #}

-- #{ category_kits.insert
-- # :category_name string
-- # :kit_name string
INSERT INTO category_kits (category_name, kit_name)
VALUES (:category_name, :kit_name)
    ON CONFLICT(category_name, kit_name) DO UPDATE SET kit_name = excluded.kit_name;
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
    uuid TEXT NOT NULL,
    kit TEXT NOT NULL,
    cooldown INTEGER NOT NULL,
    PRIMARY KEY (uuid, kit)
    );
-- #}

-- #{ cooldowns.set
-- # :uuid string
-- # :kit string
-- # :cooldown int
INSERT INTO cooldowns (uuid, kit, cooldown)
VALUES (:uuid, :kit, :cooldown)
    ON CONFLICT(uuid, kit) DO UPDATE SET cooldown = excluded.cooldown;
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
