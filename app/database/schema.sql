CREATE DATABASE IF NOT EXISTS tablet_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tablet_survey;

-- Admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tablets table
CREATE TABLE IF NOT EXISTS tablets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    size VARCHAR(80) DEFAULT NULL,
    has_display TINYINT(1) NOT NULL DEFAULT 0,
    price DECIMAL(10,2) DEFAULT NULL,
    release_date DATE DEFAULT NULL,
    pressure_levels VARCHAR(50) DEFAULT NULL,
    connection_type VARCHAR(120) DEFAULT NULL,
    display_resolution VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    image_pos_x TINYINT UNSIGNED DEFAULT 50,
    image_pos_y TINYINT UNSIGNED DEFAULT 50,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tablets_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tablet_id INT UNSIGNED NOT NULL,
    author_name VARCHAR(120) NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_tablet FOREIGN KEY (tablet_id) REFERENCES tablets(id) ON DELETE CASCADE
);

-- Failure reports table
CREATE TABLE IF NOT EXISTS failure_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tablet_id INT UNSIGNED NOT NULL,
    nickname VARCHAR(120) NOT NULL,
    status ENUM('working', 'partially_working', 'broken') NOT NULL DEFAULT 'working',
    years_used DECIMAL(4,1) DEFAULT NULL,
    warranty_expired TINYINT(1) DEFAULT 0,
    severity ENUM('minor', 'moderate', 'severe', 'critical') DEFAULT 'moderate',
    repair_status ENUM('none', 'self_repaired', 'warranty_claim', 'replaced', 'abandoned') DEFAULT 'none',
    failure_reason TEXT DEFAULT NULL,
    extra_comment TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_tablet FOREIGN KEY (tablet_id) REFERENCES tablets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Report issues table (links reports to multiple issue subcategories)
CREATE TABLE IF NOT EXISTS report_issues (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id INT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(100) NOT NULL,
    severity ENUM('minor', 'moderate', 'severe') DEFAULT 'moderate',
    CONSTRAINT fk_report_issues_report FOREIGN KEY (report_id) REFERENCES failure_reports(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Issue categories reference table
CREATE TABLE IF NOT EXISTS issue_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    UNIQUE INDEX idx_category_subcategory (category, subcategory),
    INDEX idx_category (category)
);

-- ========== SEED DATA ==========

-- Admin (password: admin)
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$CZTlkZfEn.oqmEaEcwykoe92p61DTZkVsLi4zBCn3YPQ4k6j/CQfW')
ON DUPLICATE KEY UPDATE username = username;

-- Brands
INSERT INTO brands (id, name) VALUES
(1, 'Wacom'),
(2, 'Huion'),
(3, 'XP-Pen'),
(4, 'Xencelabs'),
(5, 'Gaomon')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Issue categories
INSERT INTO issue_categories (category, subcategory, description) VALUES
('pen_input', 'no_pen_input', 'The pen is detected poorly or does not move the cursor'),
('pen_input', 'pressure_not_working', 'Pressure sensitivity is missing or inconsistent'),
('pen_input', 'cursor_offset', 'The cursor does not line up with the pen tip'),
('pen_input', 'jitter_or_wobble', 'Slow lines look shaky or wobbly'),
('pen_input', 'dead_area', 'Part of the drawing area does not respond'),
('pen_input', 'pen_buttons', 'Pen side buttons do not work'),
('display', 'no_signal_black_screen', 'The pen display shows no image'),
('display', 'flicker_or_dropout', 'The image flickers or loses signal'),
('display', 'dead_pixels', 'One or more pixels are stuck'),
('display', 'dim_backlight', 'The screen is unusually dim'),
('display', 'color_shift', 'Color looks wrong or uneven'),
('display', 'cracked_panel', 'The display glass is cracked'),
('connection', 'usb_not_detected', 'The computer does not detect the tablet'),
('connection', 'hdmi_dp_issue', 'Video works inconsistently over HDMI or DisplayPort'),
('connection', 'three_in_one_cable', 'The cable is unstable or damaged'),
('connection', 'loose_port', 'The port feels loose or damaged'),
('driver', 'driver_not_detecting', 'The driver reports not connected'),
('driver', 'after_update', 'Problems started after a driver update'),
('driver', 'no_pressure_in_app', 'Pressure works in driver but not in apps'),
('driver', 'wrong_monitor_mapping', 'The pen maps to wrong display'),
('controls', 'express_keys', 'Shortcut keys do not register'),
('controls', 'dial_or_touch_strip', 'The dial or touch strip does not respond'),
('controls', 'touch_input', 'Touch gestures fail'),
('power', 'wont_power_on', 'The device does not turn on'),
('power', 'power_adapter', 'The power adapter appears faulty'),
('body', 'stand_or_mount', 'The stand or mounting is broken'),
('body', 'overheating_or_fan', 'The tablet runs unusually hot'),
('body', 'housing_damage', 'The body has cracked or separated')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Tablets (Wacom = 1, Huion = 2, XP-Pen = 3, Xencelabs = 4, Gaomon = 5)
INSERT INTO tablets (id, brand_id, name, size, has_display, price, release_date, pressure_levels, connection_type, display_resolution, notes, image_path, image_pos_x, image_pos_y) VALUES
-- Wacom tablets (brand_id = 1)
(1, 1, 'Intuos Pro Small', '7 in', 0, 179.00, '2023-04-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(2, 1, 'Intuos Pro Medium', '8.5 x 5.5 in', 0, 279.00, '2023-04-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(3, 1, 'Intuos Pro Large', '12 x 8.5 in', 0, 379.00, '2023-04-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(4, 1, 'Intuos Small', '6 x 3.7 in', 0, 79.00, '2022-06-01', '4096', 'USB', NULL, NULL, NULL, 50, 50),
(5, 1, 'Intuos Medium', '8.5 x 5.5 in', 0, 129.00, '2022-06-01', '4096', 'USB', NULL, NULL, NULL, 50, 50),
(6, 1, 'Cintiq 16', '15.6 in', 1, 649.00, '2021-09-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(7, 1, 'Cintiq 22', '21.5 in', 1, 1099.00, '2020-03-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(8, 1, 'One by Wacom Small', '6 x 3.7 in', 0, 55.00, '2021-03-01', '2048', 'USB', NULL, NULL, NULL, 50, 50),
(9, 1, 'One by Wacom Medium', '8.3 x 5.8 in', 0, 85.00, '2021-03-01', '2048', 'USB', NULL, NULL, NULL, 50, 50),
-- Huion tablets (brand_id = 2)
(10, 2, 'Kamvas 12', '11.6 in', 1, 179.00, '2022-05-01', '8192', 'USB-C, HDMI', '1920 x 1080', NULL, 'tablets/tablet_1781184980_85eb63ca.png', 50, 50),
(11, 2, 'Kamvas 13', '13.3 in (display)', 1, 229.00, '2022-05-01', '8192', 'USB-C, HDMI', '1920 x 1080', NULL, 'tablets/tablet_1781184935_7af91cb9.png', 50, 50),
(12, 2, 'Kamvas Pro 16', '15.6 in', 1, 379.00, '2021-03-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(13, 2, 'Kamvas Pro 24', '23.8 in', 1, 899.00, '2021-09-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(14, 2, 'HS610', '10 x 6 in', 0, 129.00, '2020-01-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(15, 2, 'HS64', '6 x 4 in', 0, 89.00, '2021-03-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(16, 2, 'H610 Pro', '10 x 6 in', 0, 99.00, '2018-06-01', '8192', 'USB', NULL, NULL, NULL, 50, 50),
(36, 2, 'Kamvas 16 gen 3', NULL, 1, 500.00, '2025-01-07', '16384', 'USB-C, HDMI', '1920 x 1080', 'good tablet if you get it with warranty', 'tablets/tablet_1778699022_2023ef2b.jpg', 37, 47),
(37, 2, 'Kamvas 16 (2021)', '16 in', 1, 300.00, '2020-11-30', '8192', 'USB-C, HDMI', '1920 x 1080', NULL, 'tablets/tablet_1778710701_e47c1ea6.jpg', 52, 40),
(38, 2, 'Kamvas 13 gen 3', '13.3 in', 1, 249.00, NULL, '16384', 'USB-C, HDMI', '1920 x 1080', NULL, 'tablets/tablet_1781178895_ed2d266f.png', 80, 69),
-- XP-Pen tablets (brand_id = 3)
(17, 3, 'Artist 12', '11.6 in', 1, 149.00, '2022-09-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(18, 3, 'Artist 13.3 Pro', '13.3 in', 1, 249.00, '2022-03-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(19, 3, 'Artist 15.6', '15.6 in', 1, 329.00, '2021-06-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(20, 3, 'Artist 24 Pro', '23.8 in', 1, 799.00, '2022-12-01', '8192', 'USB-C', '3840 x 2160', NULL, NULL, 50, 50),
(21, 3, 'Deco 01', '10 x 6.25 in', 0, 89.00, '2020-05-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(22, 3, 'Deco 02', '9 x 6 in', 0, 79.00, '2021-02-01', '8192', 'USB', NULL, NULL, NULL, 50, 50),
(23, 3, 'Deco Pro Small', '8 x 5 in', 0, 139.00, '2022-02-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
-- Xencelabs tablets (brand_id = 4)
(24, 4, 'Graphic Tablet 13', '13.3 in', 1, 399.00, '2021-09-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(25, 4, 'Graphic Tablet 16', '15.6 in', 1, 599.00, '2021-06-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50),
(26, 4, 'Smart Pad', '10 x 6.5 in', 0, 249.00, '2022-06-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(27, 4, 'Tablet Standard', '10 x 6 in', 0, 199.00, '2019-12-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
-- Gaomon tablets (brand_id = 5)
(28, 5, 'S56', '5 x 3.5 in', 0, 35.00, '2020-06-01', '2048', 'USB', NULL, NULL, NULL, 50, 50),
(29, 5, 'S632', '6 x 4 in', 0, 49.00, '2020-06-01', '8192', 'USB', NULL, NULL, NULL, 50, 50),
(30, 5, 'S840', '8 x 5 in', 0, 69.00, '2021-03-01', '8192', 'USB', NULL, NULL, NULL, 50, 50),
(31, 5, 'UT610', '10 x 6.25 in', 0, 89.00, '2019-12-01', '8192', 'USB-C', NULL, NULL, NULL, 50, 50),
(32, 5, 'PD1161', '11.6 in', 1, 199.00, '2021-06-01', '8192', 'USB-C', '1920 x 1080', NULL, 'tablets/tablet_1778660112_a4665ecf.jpg', 53, 51),
(33, 5, 'PD1560', '15.6 in', 1, 349.00, '2021-06-01', '8192', 'USB-C', '1920 x 1080', NULL, NULL, 50, 50)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Failure reports
INSERT INTO failure_reports (id, tablet_id, nickname, status, years_used, warranty_expired, severity, repair_status, failure_reason, extra_comment, created_at) VALUES
(1, 36, '', 'partially_working', NULL, 0, 'moderate', 'warranty_claim', 'known issue with the shortcut wheels happened just on the second day after I got it started not working properly', '', '2026-05-13 19:10:11'),
(3, 37, '', 'partially_working', 4.5, 0, 'severe', 'abandoned', '2 lines appeared where the pen doesnt work it served me well for 4.5 years but yeah this is kinda a deal breaker when half of the screen doesnt work', '', '2026-05-14 07:58:01'),
(5, 6, 'Artist123', 'working', 2.5, 0, 'minor', 'none', 'No issues so far, great tablet!', NULL, '2026-01-14 23:00:00'),
(6, 7, 'ProUser', 'broken', 3.0, 1, 'critical', 'abandoned', 'Screen died after 3 years, not worth repairing', NULL, '2026-01-19 23:00:00'),
(7, 10, 'Student1', 'partially_working', 2.0, 1, 'moderate', 'self_repaired', 'Pen pressure stopped working after update, fixed with driver rollback', NULL, '2026-02-09 23:00:00'),
(8, 1, 'DesignerPro', 'working', 4.0, 1, 'minor', 'none', 'Still working great after 4 years', NULL, '2026-02-14 23:00:00'),
(9, 6, 'Hobbyist', 'broken', 1.0, 0, 'severe', 'warranty_claim', 'Dead pixels appeared within warranty, got replacement', NULL, '2026-02-19 23:00:00'),
(10, 9, 'WacomFan', 'broken', 3.5, 1, 'moderate', 'replaced', 'Express keys stopped working, replaced under warranty', NULL, '2026-03-09 23:00:00'),
(11, 11, 'NewUser', 'broken', 0.3, 0, 'critical', 'abandoned', 'Tablet stopped turning on after 4 months', NULL, '2026-03-17 23:00:00'),
(12, 13, 'Creative', 'partially_working', 1.8, 0, 'moderate', 'self_repaired', 'Had driver issues, reinstalled drivers', NULL, '2026-03-21 23:00:00'),
(13, 17, 'StudentArt', 'broken', 1.5, 0, 'severe', 'warranty_claim', 'Screen flickering, warranty replacement', NULL, '2026-03-27 23:00:00'),
(14, 14, 'Freelance', 'partially_working', 2.5, 1, 'moderate', 'none', 'Sometimes pen offset issues', NULL, '2026-04-07 22:00:00'),
(15, 15, 'ArtStudent', 'broken', 2.0, 1, 'critical', 'abandoned', 'HDMI port failed completely', NULL, '2026-04-09 22:00:00'),
(16, 32, 'HuionUser', 'broken', 1.0, 0, 'minor', 'self_repaired', 'Stand broke, bought new stand', NULL, '2026-04-15 22:00:00'),
(17, 1, 'TabletPro', 'working', 3.5, 1, 'minor', 'none', 'Still going strong after years', NULL, '2026-04-16 22:00:00'),
(18, 24, 'NewArtist', 'partially_working', 0.8, 0, 'minor', 'none', 'Occasional driver crashes', NULL, '2026-04-17 22:00:00'),
(19, 7, 'TabletPro2', 'broken', 2.5, 1, 'critical', 'replaced', 'Power issues, manufacturer replaced', NULL, '2026-04-18 22:00:00'),
(20, 30, 'BeginnerArtist', 'broken', 0.5, 0, 'severe', 'abandoned', 'Dead zones appeared in center', NULL, '2026-04-20 22:00:00'),
(21, 33, 'TabletNewbie', 'partially_working', 1.0, 0, 'moderate', 'none', 'Pen buttons sometimes unresponsive', NULL, '2026-04-20 22:00:00'),
(22, 5, 'Illustrator', 'working', 2.0, 1, 'minor', 'none', 'No problems at all', NULL, '2026-02-28 23:00:00'),
(23, 2, 'ProArtist', 'working', 5.0, 0, 'minor', 'none', 'Still works perfectly after 5 years of daily use', NULL, '2026-04-22 12:00:00'),
(24, 3, 'DesignerPro', 'working', 6.0, 0, 'minor', 'none', 'Rock solid, no issues at all', NULL, '2026-04-25 14:00:00'),
(25, 4, 'Student', 'broken', 2.0, 1, 'moderate', 'abandoned', 'USB port stopped working, not worth fixing for a budget tablet', NULL, '2026-04-28 10:00:00'),
(26, 8, 'Beginner', 'partially_working', 1.5, 0, 'moderate', 'self_repaired', 'Connection drops when cable moves, replaced cable and it helped', NULL, '2026-05-01 16:00:00'),
(27, 12, 'DigitalPainter', 'working', 2.0, 0, 'minor', 'none', 'Great display tablet, no problems so far', NULL, '2026-05-03 09:00:00'),
(28, 38, 'EarlyAdopter', 'broken', 0.2, 0, 'severe', 'warranty_claim', 'Screen flickered from day one, sent for warranty replacement', NULL, '2026-05-05 11:00:00'),
(29, 16, 'BudgetUser', 'working', 4.0, 0, 'minor', 'none', 'Old reliable, still going strong after 4 years', NULL, '2026-05-07 15:00:00'),
(30, 18, 'ArtistUser', 'partially_working', 1.0, 0, 'moderate', 'none', 'Colors look slightly washed out on the left side', NULL, '2026-05-09 13:00:00'),
(31, 19, 'Freelancer', 'broken', 2.5, 1, 'critical', 'abandoned', 'Cracked the screen, repair costs more than replacement', NULL, '2026-05-11 10:00:00'),
(32, 20, 'ProEditor', 'working', 1.5, 0, 'minor', 'none', 'Amazing 4K display, no issues yet', NULL, '2026-05-13 08:00:00'),
(33, 21, 'SketchArtist', 'broken', 3.0, 1, 'moderate', 'replaced', 'USB port became loose, manufacturer sent replacement', NULL, '2026-05-15 17:00:00'),
(34, 22, 'Hobbyist', 'partially_working', 2.0, 0, 'minor', 'self_repaired', 'Driver stops working sometimes, reinstall fixes it', NULL, '2026-05-17 14:00:00'),
(35, 23, 'ProUser', 'working', 1.0, 0, 'minor', 'none', 'Good budget option, working fine', NULL, '2026-05-19 12:00:00'),
(36, 25, 'Designer', 'broken', 1.8, 1, 'critical', 'abandoned', 'Wont power on anymore, out of warranty', NULL, '2026-05-21 09:00:00'),
(37, 26, 'TechUser', 'partially_working', 0.5, 0, 'moderate', 'none', 'Bluetooth connection drops randomly, using wired instead', NULL, '2026-05-23 11:00:00'),
(38, 27, 'PenArtist', 'broken', 3.5, 1, 'severe', 'replaced', 'Pen completely stopped being detected, replaced under warranty', NULL, '2026-05-25 16:00:00'),
(39, 28, 'StudentUser', 'working', 2.0, 0, 'minor', 'none', 'Basic but works fine for my needs', NULL, '2026-05-27 10:00:00'),
(40, 29, 'GameArtist', 'broken', 1.5, 0, 'moderate', 'abandoned', 'Driver stopped detecting the tablet after Windows update', NULL, '2026-05-29 15:00:00'),
(41, 31, 'DIYUser', 'partially_working', 1.0, 0, 'minor', 'self_repaired', 'Stand clip broke, 3D printed a replacement', NULL, '2026-05-31 13:00:00'),
(42, 1, 'WacomVet', 'broken', 4.5, 1, 'severe', 'abandoned', 'USB-C port wore out after years of use, not repairable', NULL, '2026-06-02 08:00:00')
ON DUPLICATE KEY UPDATE failure_reason = VALUES(failure_reason);

-- Report issues
INSERT INTO report_issues (id, report_id, category, subcategory, severity) VALUES
(1, 1, 'controls', 'dial_or_touch_strip', 'moderate'),
(3, 3, 'pen_input', 'dead_area', 'moderate'),
(20, 6, 'display', 'no_signal_black_screen', 'moderate'),
(21, 7, 'driver', 'after_update', 'moderate'),
(22, 9, 'display', 'dead_pixels', 'moderate'),
(23, 6, 'display', 'flicker_or_dropout', 'moderate'),
(24, 11, 'power', 'wont_power_on', 'moderate'),
(25, 12, 'driver', 'after_update', 'moderate'),
(26, 13, 'display', 'flicker_or_dropout', 'moderate'),
(27, 14, 'pen_input', 'cursor_offset', 'moderate'),
(28, 15, 'connection', 'loose_port', 'moderate'),
(29, 16, 'body', 'stand_or_mount', 'moderate'),
(30, 19, 'power', 'wont_power_on', 'moderate'),
(31, 20, 'pen_input', 'dead_area', 'moderate'),
(32, 21, 'controls', 'express_keys', 'moderate'),
(33, 19, 'display', 'no_signal_black_screen', 'moderate'),
(34, 13, 'display', 'no_signal_black_screen', 'moderate'),
(35, 10, 'controls', 'express_keys', 'moderate'),
(36, 25, 'connection', 'loose_port', 'moderate'),
(37, 26, 'connection', 'three_in_one_cable', 'moderate'),
(38, 28, 'display', 'flicker_or_dropout', 'severe'),
(39, 30, 'display', 'color_shift', 'moderate'),
(40, 31, 'display', 'cracked_panel', 'severe'),
(41, 33, 'connection', 'loose_port', 'moderate'),
(42, 34, 'driver', 'after_update', 'minor'),
(43, 36, 'power', 'wont_power_on', 'critical'),
(44, 37, 'connection', 'usb_not_detected', 'moderate'),
(45, 38, 'pen_input', 'no_pen_input', 'severe'),
(46, 40, 'driver', 'driver_not_detecting', 'moderate'),
(47, 41, 'body', 'stand_or_mount', 'minor'),
(48, 42, 'connection', 'loose_port', 'moderate'),
(49, 25, 'pen_input', 'dead_area', 'moderate'),
(50, 31, 'display', 'no_signal_black_screen', 'severe'),
(51, 38, 'pen_input', 'pressure_not_working', 'moderate')
ON DUPLICATE KEY UPDATE subcategory = VALUES(subcategory);

-- Comments
INSERT INTO comments (id, tablet_id, author_name, comment_text, created_at) VALUES
(1, 1, 'GreatTablet', 'Best tablet I have ever owned. Very responsive pen!', '2026-01-09 23:00:00'),
(2, 6, 'ProfessionalArtist', 'Had issues with driver updates but overall good.', '2026-01-24 23:00:00'),
(3, 10, 'StudentUser', 'Perfect for my art classes. Affordable and reliable.', '2026-02-04 23:00:00'),
(4, 6, 'DigitalPainter', 'The screen quality is amazing for the price.', '2026-02-14 23:00:00'),
(5, 1, 'HobbyistArt', 'Great entry-level tablet. Highly recommend for beginners.', '2026-02-28 23:00:00'),
(6, 1, 'WacomLover', 'Intuos Pro is the best investment for digital art.', '2026-03-09 23:00:00'),
(7, 6, 'Reviewer', 'Had dead pixels within first year. Warranty handled it.', '2026-03-14 23:00:00'),
(8, 7, 'NewArtist', 'Easy to set up and use. Great pen feel.', '2026-03-19 23:00:00'),
(9, 4, 'TabletUser', 'Good tablet for the price. Some connectivity issues.', '2026-03-24 23:00:00'),
(10, 9, 'ProDesigner', 'Excellent build quality. Still working after 3 years.', '2026-03-31 22:00:00'),
(11, 11, 'DigitalArtist', 'Good budget option for beginners.', '2026-04-04 22:00:00'),
(12, 17, 'ArtStudent', 'Decent tablet for the price point.', '2026-04-09 22:00:00'),
(13, 24, 'ProUser', 'Great display quality and pen accuracy.', '2026-04-14 22:00:00'),
(14, 2, 'DigitalArtist', 'Intuos Pro Medium is the perfect size for professional work', '2026-04-20 22:00:00'),
(15, 3, 'IllustratorPro', 'The large surface area is great for big monitors', '2026-04-25 22:00:00'),
(16, 16, 'BudgetUser', 'H610 Pro is a tank, survived 4 years of abuse', '2026-05-01 22:00:00'),
(17, 20, 'ProEditor', 'Artist 24 Pro 4K display is incredible for photo editing', '2026-05-05 22:00:00'),
(18, 25, 'Designer', 'Graphic Tablet 16 has great color accuracy', '2026-05-10 22:00:00'),
(19, 27, 'PenArtist', 'Tablet Standard has the best pen feel I have tried', '2026-05-15 22:00:00'),
(20, 28, 'Student', 'S56 is cheap but gets the job done for notes', '2026-05-20 22:00:00'),
(21, 12, 'CreativePro', 'Kamvas Pro 16 is a beast for digital painting', '2026-05-25 22:00:00'),
(22, 38, 'EarlyAdopter', 'Kamvas 13 gen 3 had flickering out of the box', '2026-05-28 22:00:00'),
(23, 2, 'WacomFan', 'Upgraded from Small to Medium, worth every penny', '2026-06-01 22:00:00')
ON DUPLICATE KEY UPDATE comment_text = VALUES(comment_text);

-- Reset AUTO_INCREMENT to next available values
SET @max_brands = (SELECT MAX(id) FROM brands);
SET @max_tablets = (SELECT MAX(id) FROM tablets);
SET @max_reports = (SELECT MAX(id) FROM failure_reports);
SET @max_issues = (SELECT MAX(id) FROM report_issues);
SET @max_comments = (SELECT MAX(id) FROM comments);

ALTER TABLE brands AUTO_INCREMENT = @max_brands + 1;
ALTER TABLE tablets AUTO_INCREMENT = @max_tablets + 1;
ALTER TABLE failure_reports AUTO_INCREMENT = @max_reports + 1;
ALTER TABLE report_issues AUTO_INCREMENT = @max_issues + 1;
ALTER TABLE comments AUTO_INCREMENT = @max_comments + 1;