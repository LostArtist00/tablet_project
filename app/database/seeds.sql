USE tablet_survey;

INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$KKKKKKKKKKKKKKKKKKKKKKO9Ij0.pN9xG.J0KXqX.ywJXvK6L4rXi')
ON DUPLICATE KEY UPDATE username = username;

INSERT INTO brands (name) VALUES
    ('Wacom'),
    ('Huion'),
    ('XP-Pen'),
    ('Xencelabs'),
    ('Gaomon'),
    ('One by Wacom')
ON DUPLICATE KEY UPDATE name = VALUES(name);

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
('body', 'housing_damage', 'The body has cracked or separated');

INSERT INTO tablets (brand_id, name, size, has_display, price, release_date, pressure_levels, connection_type, notes) VALUES
(1, 'Intuos Pro Small', '7 in', 0, 179.00, '2023-04-01', '8192', 'USB-C', 'Compact pen tablet.'),
(1, 'Intuos Pro Medium', '8.5 x 5.5 in', 0, 279.00, '2023-04-01', '8192', 'USB-C', 'Popular pro tablet.'),
(1, 'Intuos Pro Large', '12 x 8.5 in', 0, 379.00, '2023-04-01', '8192', 'USB-C', 'Large work area.'),
(1, 'Intuos Small', '6 x 3.7 in', 0, 79.00, '2022-06-01', '4096', 'USB', 'Starter tablet.'),
(1, 'Intuos Medium', '8.5 x 5.5 in', 0, 129.00, '2022-06-01', '4096', 'USB', 'Mid-range tablet.'),
(1, 'Cintiq 16', '15.6 in', 1, 649.00, '2021-09-01', '8192', 'USB-C', 'Affordable display tablet.'),
(1, 'Cintiq 22', '21.5 in', 1, 1099.00, '2020-03-01', '8192', 'USB-C', 'Mid-size display tablet.'),
(1, 'One by Wacom Small', '6 x 3.7 in', 0, 55.00, '2021-03-01', '2048', 'USB', 'Basic starter tablet.'),
(1, 'One by Wacom Medium', '8.3 x 5.8 in', 0, 85.00, '2021-03-01', '2048', 'USB', 'Mid-size basic tablet.'),

(2, 'Kamvas 12', '11.6 in', 1, 179.00, '2022-05-01', '8192', 'USB-C', 'Compact display tablet.'),
(2, 'Kamvas 13', '13.3 in', 1, 229.00, '2022-05-01', '8192', 'USB-C', 'Full HD display.'),
(2, 'Kamvas Pro 16', '15.6 in', 1, 379.00, '2021-03-01', '8192', 'USB-C', 'Popular display tablet.'),
(2, 'Kamvas Pro 24', '23.8 in', 1, 899.00, '2021-09-01', '8192', 'USB-C', 'Large 4K display.'),
(2, 'HS610', '10 x 6 in', 0, 129.00, '2020-01-01', '8192', 'USB-C', 'Premium pen tablet.'),
(2, 'HS64', '6 x 4 in', 0, 89.00, '2021-03-01', '8192', 'USB-C', 'Compact pen tablet.'),
(2, 'H610 Pro', '10 x 6 in', 0, 99.00, '2018-06-01', '8192', 'USB', 'Popular budget tablet.'),

(3, 'Artist 12', '11.6 in', 1, 149.00, '2022-09-01', '8192', 'USB-C', 'Compact display tablet.'),
(3, 'Artist 13.3 Pro', '13.3 in', 1, 249.00, '2022-03-01', '8192', 'USB-C', 'QHD display.'),
(3, 'Artist 15.6', '15.6 in', 1, 329.00, '2021-06-01', '8192', 'USB-C', 'Full HD display tablet.'),
(3, 'Artist 24 Pro', '23.8 in', 1, 799.00, '2022-12-01', '8192', 'USB-C', 'Large 4K display.'),
(3, 'Deco 01', '10 x 6.25 in', 0, 89.00, '2020-05-01', '8192', 'USB-C', 'Entry-level pen tablet.'),
(3, 'Deco 02', '9 x 6 in', 0, 79.00, '2021-02-01', '8192', 'USB', 'Budget pen tablet.'),
(3, 'Deco Pro Small', '8 x 5 in', 0, 139.00, '2022-02-01', '8192', 'USB-C', 'Professional pen tablet.'),

(4, 'Graphic Tablet 13', '13.3 in', 1, 399.00, '2021-09-01', '8192', 'USB-C', 'Portable display tablet.'),
(4, 'Graphic Tablet 16', '15.6 in', 1, 599.00, '2021-06-01', '8192', 'USB-C', 'Professional display.'),
(4, 'Smart Pad', '10 x 6.5 in', 0, 249.00, '2022-06-01', '8192', 'USB-C', 'Wireless pen pad.'),
(4, 'Tablet Standard', '10 x 6 in', 0, 199.00, '2019-12-01', '8192', 'USB-C', 'Professional pen tablet.'),

(5, 'S56', '5 x 3.5 in', 0, 35.00, '2020-06-01', '2048', 'USB', 'Ultra-budget tablet.'),
(5, 'S632', '6 x 4 in', 0, 49.00, '2020-06-01', '8192', 'USB', 'Entry-level tablet.'),
(5, 'S840', '8 x 5 in', 0, 69.00, '2021-03-01', '8192', 'USB', 'Affordable tablet.'),
(5, 'UT610', '10 x 6.25 in', 0, 89.00, '2019-12-01', '8192', 'USB-C', 'Budget pen tablet.'),
(5, 'PD1161', '11.6 in', 1, 199.00, '2021-06-01', '8192', 'USB-C', 'Full HD display.'),
(5, 'PD1560', '15.6 in', 1, 349.00, '2021-06-01', '8192', 'USB-C', 'Large 4K display.'),

(6, 'One by Wacom Small', '6 x 3.7 in', 0, 55.00, '2021-03-01', '2048', 'USB', 'Simple starter tablet.'),
(6, 'One by Wacom Medium', '8.3 x 5.8 in', 0, 85.00, '2021-03-01', '2048', 'USB', 'Mid-size basic.')
ON DUPLICATE KEY UPDATE name = VALUES(name);