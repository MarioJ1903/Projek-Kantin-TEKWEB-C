DROP TABLE IF EXISTS order_items, orders, cart, menu, users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price INT,
    stock INT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    menu_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price INT,
    status ENUM('pending','selesai') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    menu_id INT,
    quantity INT,
    price INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE SET NULL
);

INSERT INTO `menu` (`name`, `price`, `stock`, `image`) VALUES
-- Makanan Berat & Ringan
('Nasi Goreng Spesial', 15000, 20, 'nasigoreng.jpg'),
('Ayam Geprek Sambal Bawang', 13000, 25, 'ayamgeprek.jpg'),
('Soto Ayam Lamongan', 12000, 15, 'soto.jpg'),
('Nasi Uduk Komplit', 14000, 10, 'nasiuduk.jpg'),
('Mie Goreng Telur', 10000, 30, 'miegoreng.jpg'),
('Gorengan Bakwan', 1000, 100, 'bakwan.jpg'),

-- Minuman Segar
('Es Teh Manis', 3000, 50, 'esteh.jpg'),
('Es Jeruk Peras', 4000, 40, 'esjeruk.jpg'),
('Kopi Hitam Kapal Api', 3000, 20, 'kopi.jpg'),
('Cappucino Cincau', 5000, 25, 'capcin.jpg');