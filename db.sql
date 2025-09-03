-- Core schema for Moonlight PRO (minimal starter)
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120),
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS site_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) UNIQUE NOT NULL,
  setting_value TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  category VARCHAR(80) DEFAULT 'drink',
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS item_prices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  effective_date DATE NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_item_date (item_id,effective_date),
  FOREIGN KEY (item_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS slider (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200),
  slug VARCHAR(240) UNIQUE,
  content MEDIUMTEXT,
  author_id INT,
  published_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(150),
  customer_phone VARCHAR(30) NOT NULL,
  party_size INT NOT NULL DEFAULT 1,
  reserve_date DATE NOT NULL,
  reserve_time TIME NOT NULL,
  note TEXT,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_id INT NULL,
  item_name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  method ENUM('cash','transfer','card','paystack') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  provider_ref VARCHAR(100),
  status ENUM('pending','success','failed') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(255) NOT NULL,
  details TEXT,
  ip_address VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default roles
INSERT IGNORE INTO roles (id,name) VALUES (1,'admin'),(2,'manager'),(3,'waiter'),(4,'finance'),(5,'hr'),(6,'customer');

SET FOREIGN_KEY_CHECKS=1;


CREATE TABLE menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE SET NULL
);

-- Demo categories
INSERT INTO menu_categories (name) VALUES ('Drinks'), ('Cocktails'), ('Food');

-- Demo items
INSERT INTO menu_items (category_id, name, description, price) VALUES
(1, 'Whiskey', 'Premium Scotch Whiskey', 25.00),
(2, 'Mojito', 'Classic Cuban cocktail', 15.00),
(3, 'Grilled Chicken', 'Served with spicy sauce', 18.50);


CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Demo post
INSERT INTO blog_posts (title, content) 
VALUES ('Grand Opening Night', 'Join us for Moonlight’s grand opening this Friday with free cocktails for the first 50 guests!');


CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL,
    guests INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    message TEXT,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE finance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income','expense') NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Example entries
INSERT INTO finance (type, category, amount, description) 
VALUES 
('income','Ticket Sales', 500.00, 'Entry tickets Friday night'),
('expense','Drinks Purchase', 200.00, 'Supplier payment');


-- Attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    check_in DATETIME NOT NULL,
    check_out DATETIME DEFAULT NULL,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Leave requests table
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);


-- slider & gallery (if not already present)
CREATE TABLE IF NOT EXISTS slider (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

