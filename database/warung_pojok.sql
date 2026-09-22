-- =====================================================================
-- WARUNG POJOK - Database Website UMKM Kuliner
-- Developer : KelasPojok-Dev
-- Database  : warung_pojok
-- Cara pakai: buka phpMyAdmin > Import > pilih file ini > Go
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Tabel: admins
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100) NOT NULL DEFAULT 'Administrator',
  `username`   VARCHAR(50)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password demo: admin123  (tersimpan sebagai hash bcrypt, bukan plaintext)
INSERT INTO `admins` (`nama`, `username`, `password`) VALUES
('Admin Warung Pojok', 'admin', '$2y$10$KQ7cVxYb3NnPqZs1uWvOme9ytOvMYOs6e7bPA5LHw/opm8fRK9D/i');

-- ---------------------------------------------------------------------
-- Tabel: categories
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(80) NOT NULL,
  `slug`       VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Dimsum Mentai', 'dimsum-mentai'),
(2, 'Dimsum Original', 'dimsum-original'),
(3, 'Paket Hemat', 'paket-hemat'),
(4, 'Frozen Food', 'frozen-food');

-- ---------------------------------------------------------------------
-- Tabel: products
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(180) NOT NULL,
  `description` TEXT,
  `price`       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stock`       INT NOT NULL DEFAULT 0,
  `image`       VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status`      ENUM('tersedia','habis','nonaktif') NOT NULL DEFAULT 'tersedia',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_status` (`status`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`)
    REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products`
(`id`,`category_id`,`name`,`slug`,`description`,`price`,`stock`,`image`,`is_featured`,`status`) VALUES
(1,1,'Dimsum Mentai Original','dimsum-mentai-original','Dimsum ayam udang kukus yang disiram saus mentai creamy, dibakar sebentar sampai wangi, lalu ditaburi tobiko dan daun bawang. Isi 6 pcs, disajikan hangat dalam tray alumunium.',25000.00,40,'dimsum-mentai.jpg',1,'tersedia'),
(2,1,'Dimsum Mentai Pedas','dimsum-mentai-pedas','Versi pedas dari mentai favorit kami. Saus mentai dicampur cabai bubuk pilihan, cocok untuk yang suka sensasi hangat di lidah. Isi 6 pcs.',27000.00,25,'dimsum-pedas.jpg',1,'tersedia'),
(3,1,'Dimsum Mentai Keju','dimsum-mentai-keju','Perpaduan saus mentai dan keju mozzarella yang meleleh di atas dimsum. Manis gurih dan ramah untuk anak-anak. Isi 6 pcs.',28000.00,18,'dimsum-keju.jpg',1,'tersedia'),
(4,2,'Dimsum Ayam Kukus','dimsum-ayam-kukus','Dimsum ayam kukus klasik tanpa saus, disajikan dengan saus sambal dan mayo terpisah. Pilihan aman untuk semua umur. Isi 6 pcs.',20000.00,50,'dimsum-ayam.jpg',0,'tersedia'),
(5,2,'Dimsum Udang','dimsum-udang','Dimsum dengan isian udang cincang yang lebih banyak, tekstur kenyal dan gurih. Isi 6 pcs.',24000.00,0,'dimsum-original.jpg',0,'habis'),
(6,3,'Paket Berdua','paket-berdua','Dua tray dimsum mentai pilihan plus dua es teh manis. Hemat untuk makan berdua di rumah atau di warung.',48000.00,15,'paket-hemat.jpg',1,'tersedia'),
(7,4,'Dimsum Frozen Isi 20','dimsum-frozen-isi-20','Dimsum mentah beku isi 20 pcs, tinggal dikukus 12 menit di rumah. Tahan sampai 1 bulan di freezer. Saus mentai dikemas terpisah.',55000.00,12,'dimsum-original.jpg',0,'tersedia');

-- ---------------------------------------------------------------------
-- Tabel: reviews
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`    INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `rating`        TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `comment`       TEXT,
  `status`        ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reviews_product` (`product_id`),
  KEY `idx_reviews_status` (`status`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reviews` (`product_id`,`customer_name`,`rating`,`comment`,`status`) VALUES
(1,'Rani',5,'Saus mentainya melimpah, masih hangat pas sampai rumah. Pasti pesan lagi.','disetujui'),
(1,'Bayu',4,'Enak banget, cuma porsinya kurang buat saya yang lapar berat.','disetujui'),
(2,'Dimas',5,'Pedasnya pas, nggak bikin sakit perut. Recommended.','disetujui'),
(3,'Salsa',5,'Kejunya beneran meleleh. Anak saya suka.','disetujui'),
(4,'Tono',4,'Dimsum kukusnya lembut, sausnya pas.','disetujui');

-- ---------------------------------------------------------------------
-- Tabel: gallery
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gallery` (`title`,`image`,`description`) VALUES
('Dimsum mentai siap antar','galeri-1.jpg','Pesanan yang baru keluar dari kukusan.'),
('Detail topping','galeri-2.jpg','Tobiko dan daun bawang di atas saus mentai.'),
('Tray porsi besar','galeri-3.jpg','Paket berdua untuk makan bareng.'),
('Proses pembakaran saus','galeri-4.jpg','Saus dibakar sebentar supaya wangi.'),
('Kemasan bawa pulang','galeri-5.jpg','Dikemas rapat supaya tidak tumpah.'),
('Menu andalan warung','galeri-6.jpg','Dimsum mentai, menu yang paling dicari.');

-- ---------------------------------------------------------------------
-- Tabel: orders
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code`     VARCHAR(30) NOT NULL,
  `customer_name`  VARCHAR(100) NOT NULL,
  `whatsapp`       VARCHAR(30) NOT NULL,
  `address`        TEXT,
  `notes`          TEXT,
  `payment_method` ENUM('whatsapp','transfer') NOT NULL DEFAULT 'whatsapp',
  `payment_status` ENUM('belum_bayar','menunggu_verifikasi','lunas') NOT NULL DEFAULT 'belum_bayar',
  `total`          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `proof_image`    VARCHAR(255) DEFAULT NULL,
  `order_status`   ENUM('baru','diproses','selesai','batal') NOT NULL DEFAULT 'baru',
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_code` (`order_code`),
  KEY `idx_orders_status` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: order_items
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `price`        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `quantity`     INT NOT NULL DEFAULT 1,
  `subtotal`     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_items_order` (`order_id`),
  KEY `idx_items_product` (`product_id`),
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: settings  (semua teks/gambar yang bisa diubah admin)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(80) NOT NULL,
  `setting_value` TEXT,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES
('site_name','WARUNG POJOK'),
('tagline','Dimsum mentai buatan warung, dibuat setelah pesanan masuk.'),
('logo','logo.png'),
('favicon','logo.png'),
('hero_title_1','DIMSUM'),
('hero_title_2','MENTAI'),
('hero_desc','Dimsum kukus hangat dengan saus mentai creamy yang dibakar sebentar. Dibuat harian di warung kami, bukan stok kemarin.'),
('hero_image','hero-dimsum.png'),
('hero_bg','hero-bg.jpg'),
('hero_badge','Special!'),
('cta_primary_text','Lihat menu'),
('cta_secondary_text','Pesan Sekarang'),
('bg_image',''),
('color_primary','#D13D34'),
('color_accent','#F6D04A'),
('color_leaf','#B0BA1C'),
('color_orange','#EC9736'),
('whatsapp','6285747245030'),
('email','warungpojok.dimsum@gmail.com'),
('address','Jl. Pojok Raya No. 12, Pekalongan, Jawa Tengah'),
('maps_link','https://maps.app.goo.gl/u1mPnh1K7p43wzY19'),
('maps_embed',''),
('open_hours','Setiap hari, 10.00 - 21.00 WIB'),
('instagram',''),
('facebook',''),
('tiktok',''),
('about_title','Warung kecil di pojok jalan, rasa yang tidak kecil'),
('about_story','WARUNG POJOK berawal dari dapur rumah pada 2023. Awalnya kami hanya menerima pesanan tetangga lewat WhatsApp, lalu berkembang menjadi warung kecil yang buka setiap hari. Semua dimsum dibuat setelah pesanan masuk supaya sampai di tangan pelanggan dalam keadaan hangat.'),
('about_vision','Menjadi warung dimsum rumahan yang paling dipercaya di Pekalongan.'),
('about_mission','Memakai bahan segar setiap hari, menjaga harga tetap terjangkau, dan melayani pesanan dengan cepat.'),
('about_target','Pelajar, keluarga, dan pekerja yang mencari camilan hangat dengan harga wajar.'),
('advantage_1','Dibuat setelah pesanan masuk'),
('advantage_2','Saus mentai diracik sendiri'),
('advantage_3','Harga mulai 20 ribu'),
('advantage_4','Antar sekitar Pekalongan'),
('bank_name',''),
('bank_account',''),
('bank_holder',''),
('payment_instruction','Setelah transfer, kirim bukti pembayaran melalui halaman konfirmasi atau WhatsApp kami. Pesanan diproses setelah pembayaran diverifikasi admin.'),
('developer_name','KelasPojok-Dev'),
('developer_url',''),
('copyright_text','');

SET FOREIGN_KEY_CHECKS = 1;
