-- =====================================
-- 🗄️  Base de datos: sysventas
-- =====================================

CREATE DATABASE IF NOT EXISTS app_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE app_db;

-- =====================================
-- 🧩 Tabla: roles
-- =====================================
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (name) VALUES ('superadmin'), ('admin'), ('user');

-- =====================================
-- 👤 Tabla: users
-- =====================================
DROP TABLE IF EXISTS users;

CREATE TABLE users (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       role_id INT UNSIGNED NOT NULL DEFAULT 3,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario superadmin por defecto (cambiar contraseña después de login)
INSERT INTO users (username, password, email, role_id)
VALUES ('superadmin', '$2y$10$8mLo3tJHRWk0L7T7h5scI.7zZ5sJXK2fC/4Ifkp4pDnbcLw96ykLq', 'super@local', 1);

-- =====================================
-- 📦 Tabla: products
-- =====================================
DROP TABLE IF EXISTS products;

CREATE TABLE products (
                          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(100) NOT NULL,
                          price DECIMAL(10,2) NOT NULL,
                          stock INT DEFAULT 0,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Productos iniciales (demo)
INSERT INTO products (name, price, stock) VALUES
                                              ('Laptop Lenovo', 1200.00, 5),
                                              ('Mouse inalámbrico', 25.50, 20),
                                              ('Teclado mecánico', 80.00, 10),
                                              ('Monitor 24"', 250.00, 7);

-- =====================================
-- 🧾 Tabla: sales
-- =====================================
DROP TABLE IF EXISTS sales;

CREATE TABLE sales (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       user_id INT UNSIGNED NOT NULL,
                       total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================
-- 📋 Tabla: sale_items
-- =====================================
DROP TABLE IF EXISTS sale_items;

CREATE TABLE sale_items (
                            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            sale_id INT UNSIGNED NOT NULL,
                            product_id INT UNSIGNED NOT NULL,
                            quantity INT NOT NULL,
                            price DECIMAL(10,2) NOT NULL,
                            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
                            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================
-- ✅ Datos verificados
-- =====================================
-- Contraseña del usuario superadmin:
--   StrongP@ssw0rd
-- (hash bcrypt incluido en el insert)
