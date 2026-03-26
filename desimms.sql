-- ============================================
--  desimms — Run this in phpMyAdmin > SQL tab
-- ============================================

CREATE DATABASE IF NOT EXISTS desimms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE desimms;

CREATE TABLE IF NOT EXISTS videos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) NOT NULL UNIQUE,
  description  TEXT,
  category     VARCHAR(100) DEFAULT 'general',
  tags         VARCHAR(500) DEFAULT '',
  embed_url    VARCHAR(1000) DEFAULT '',
  video_file   VARCHAR(500) DEFAULT '',
  thumbnail    VARCHAR(500) DEFAULT '',
  views        INT DEFAULT 0,
  featured     TINYINT DEFAULT 0,
  seo_title    VARCHAR(255) DEFAULT '',
  seo_desc     VARCHAR(500) DEFAULT '',
  seo_keywords VARCHAR(500) DEFAULT '',
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  position    ENUM('header','footer','below_title','popup','sidebar') NOT NULL,
  ad_code     TEXT NOT NULL,
  popup_url   VARCHAR(1000) DEFAULT '',
  popup_delay INT DEFAULT 3,
  is_active   TINYINT DEFAULT 1,
  label       VARCHAR(100) DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
 icon VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name,slug,icon) VALUES
('Entertainment','entertainment','🎭'),('Music','music','🎵'),
('Comedy','comedy','😂'),('News','news','📰'),
('Sports','sports','⚽'),('Technology','technology','💻'),
('Education','education','📚'),('Travel','travel','✈️'),
('Food','food','🍕'),('Fitness','fitness','💪');

INSERT IGNORE INTO settings (`key`,`value`) VALUES
('site_name','desimms'),
('site_tagline','Watch Anything, Anywhere'),
('site_description','desimms is a free video sharing platform. Watch and share the best videos online.'),
('site_keywords','desimms, free videos, watch online, entertainment, india'),
('admin_password','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('videos_per_page','12'),
('popup_enabled','0'),
('maintenance_mode','0'),
('footer_text','desimms is a free video sharing platform for entertainment purposes only.');

INSERT IGNORE INTO ads (position,ad_code,popup_url,popup_delay,is_active,label) VALUES
('header','<img src="https://placehold.co/728x90/e8ecf5/e94560?text=Your+Header+Ad+Here+%7C+728x90" style="max-width:100%;display:block;margin:0 auto"/>','',0,1,'Header 728x90'),
('footer','<img src="https://placehold.co/728x90/e8ecf5/0077cc?text=Your+Footer+Ad+Here+%7C+728x90" style="max-width:100%;display:block;margin:0 auto"/>','',0,1,'Footer 728x90'),
('below_title','<img src="https://placehold.co/468x60/e8ecf5/e94560?text=In-Content+Ad+%7C+468x60" style="max-width:100%;display:block;margin:0 auto"/>','',0,1,'Below Title 468x60'),
('popup','','https://example.com',3,0,'Popup (disabled)');

INSERT IGNORE INTO videos (title,slug,description,category,tags,embed_url,thumbnail,views,featured,seo_title,seo_desc,seo_keywords) VALUES
('Beautiful Kashmir – Travel Vlog 2024','beautiful-kashmir-travel-vlog-2024','Explore the stunning valleys, lakes, and mountains of Kashmir. A must-watch for every travel lover.','travel','kashmir,travel,india,vlog,mountains','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s1/400/225',15420,1,'Beautiful Kashmir Travel Vlog 2024 | desimms','Explore stunning Kashmir on desimms.','kashmir travel vlog, india travel 2024'),
('Best Bollywood Songs 2024','best-bollywood-songs-2024','Top 50 Bollywood hits of 2024. Romantic songs, dance numbers and more.','music','bollywood,songs,hindi,2024,hits','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s2/400/225',89340,1,'Best Bollywood Songs 2024 | desimms','Watch top Bollywood songs 2024 on desimms.','bollywood songs 2024, hindi songs'),
('Stand-Up Comedy Night – Hilarious Set','standup-comedy-night','Incredible stand-up comedy covering everyday Indian life with sharp, relatable humor.','comedy','comedy,standup,india,funny,hindi','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s3/400/225',42100,0,'Stand-Up Comedy Night | desimms','Watch hilarious Indian stand-up on desimms.','indian comedy, stand up'),
('IPL 2024 Highlights – Best Moments','ipl-2024-highlights','Relive the most exciting IPL 2024 moments. Sixes, wickets, and dramatic finishes.','sports','ipl,cricket,india,sports,2024','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s4/400/225',210500,1,'IPL 2024 Highlights | desimms','Watch IPL 2024 best moments on desimms.','ipl 2024, cricket highlights'),
('Street Food Tour – Mumbai','street-food-tour-mumbai','Mouth-watering street food tour across Mumbai from vada pav to pav bhaji.','food','mumbai,street food,india,food vlog','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s5/400/225',33200,0,'Mumbai Street Food Tour | desimms','Best Mumbai street food vlog on desimms.','mumbai food, street food india'),
('Learn Python in 1 Hour','learn-python-1-hour','Complete beginner Python tutorial. Zero to first program in under an hour.','education','python,coding,programming,beginners','https://www.youtube.com/embed/dQw4w9WgXcQ','https://picsum.photos/seed/s6/400/225',67800,0,'Learn Python in 1 Hour | desimms','Best Python tutorial for beginners on desimms.','python tutorial, learn programming');
