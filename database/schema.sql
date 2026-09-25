CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  icon VARCHAR(255) NULL,
  accent VARCHAR(30) NOT NULL DEFAULT 'emerald',
  description TEXT NULL,
  seo_title VARCHAR(255) NULL,
  seo_description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX categories_active_idx (is_active),
  INDEX categories_sort_idx (sort_order)
);

CREATE TABLE stores (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  logo VARCHAR(255) NULL,
  website_url VARCHAR(255) NULL,
  description TEXT NULL,
  seo_title VARCHAR(255) NULL,
  seo_description TEXT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX stores_featured_idx (is_featured),
  INDEX stores_active_idx (is_active),
  INDEX stores_sort_idx (sort_order)
);

CREATE TABLE category_store (
  category_id BIGINT UNSIGNED NOT NULL,
  store_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (category_id, store_id),
  CONSTRAINT category_store_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  CONSTRAINT category_store_store_fk FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

CREATE TABLE coupons (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  store_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  type ENUM('code','deal') NOT NULL DEFAULT 'code',
  code VARCHAR(255) NULL,
  discount_label VARCHAR(255) NULL,
  description TEXT NULL,
  destination_url VARCHAR(255) NULL,
  starts_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  clicks BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX coupons_type_idx (type),
  INDEX coupons_expiry_idx (expires_at),
  INDEX coupons_verified_idx (is_verified),
  INDEX coupons_featured_idx (is_featured),
  INDEX coupons_active_idx (is_active),
  CONSTRAINT coupons_store_fk FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
  CONSTRAINT coupons_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  store_id BIGINT UNSIGNED NULL,
  category_id BIGINT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt VARCHAR(500) NULL,
  content LONGTEXT NOT NULL,
  image VARCHAR(255) NULL,
  type ENUM('review','guide') NOT NULL DEFAULT 'review',
  rating DECIMAL(3,1) NULL,
  seo_title VARCHAR(255) NULL,
  seo_description TEXT NULL,
  published_at TIMESTAMP NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX reviews_type_idx (type),
  INDEX reviews_publish_idx (published_at),
  INDEX reviews_featured_idx (is_featured),
  INDEX reviews_active_idx (is_active),
  CONSTRAINT reviews_store_fk FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
  CONSTRAINT reviews_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
