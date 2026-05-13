CREATE DATABASE IF NOT EXISTS tablet_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tablet_survey;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS brands (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

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
    notes TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    image_pos_x TINYINT UNSIGNED DEFAULT 50,
    image_pos_y TINYINT UNSIGNED DEFAULT 50,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tablets_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tablet_id INT UNSIGNED NOT NULL,
    author_name VARCHAR(120) NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_tablet FOREIGN KEY (tablet_id) REFERENCES tablets(id) ON DELETE CASCADE
);

CREATE TABLE failure_reports (
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

CREATE TABLE report_issues (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id INT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(100) NOT NULL,
    severity ENUM('minor', 'moderate', 'severe') DEFAULT 'moderate',
    CONSTRAINT fk_report_issues_report FOREIGN KEY (report_id) REFERENCES failure_reports(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE issue_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL
);