CREATE DATABASE IF NOT EXISTS shrdvms_db;
USE shrdvms_db;

-- Users & Roles
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('ADMIN','NGO','GOV','LEADER','VOLUNTEER','BENEFICIARY'),
    organization VARCHAR(255),
    phone VARCHAR(20),
    mfa_secret VARCHAR(32) DEFAULT NULL,
    is_mfa_enabled BOOLEAN DEFAULT 0,
    is_approved BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Geography
CREATE TABLE states (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100));
CREATE TABLE lgas (id INT AUTO_INCREMENT PRIMARY KEY, state_id INT, name VARCHAR(100), FOREIGN KEY (state_id) REFERENCES states(id));

CREATE TABLE communities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    lga_id INT,
    ward VARCHAR(100),
    population_estimate INT,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lga_id) REFERENCES lgas(id)
);

-- Beneficiaries (Encrypted fields)
CREATE TABLE beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    community_id INT,
    full_name_encrypted TEXT,
    household_size INT,
    age INT,
    gender CHAR(1),
    has_disability BOOLEAN,
    livelihood VARCHAR(100),
    poverty_level VARCHAR(20),
    is_verified BOOLEAN DEFAULT 0,
    FOREIGN KEY (community_id) REFERENCES communities(id)
);

-- Vulnerability
CREATE TABLE vulnerability_indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    community_id INT,
    date_recorded DATE DEFAULT (CURRENT_DATE),
    food_security_score FLOAT,
    flood_risk_score FLOAT,
    disease_outbreak_risk FLOAT,
    conflict_proximity FLOAT,
    water_sanitation_score FLOAT,
    nutrition_score FLOAT,
    vulnerability_index FLOAT DEFAULT NULL,
    risk_level ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT NULL,
    FOREIGN KEY (community_id) REFERENCES communities(id)
);

-- Logistics
CREATE TABLE relief_items (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), unit VARCHAR(20));
CREATE TABLE warehouses (id INT AUTO_INCREMENT PRIMARY KEY, community_id INT, name VARCHAR(255), latitude DECIMAL(10,7), longitude DECIMAL(10,7));
CREATE TABLE inventory (id INT AUTO_INCREMENT PRIMARY KEY, warehouse_id INT, item_id INT, quantity FLOAT, FOREIGN KEY (warehouse_id) REFERENCES warehouses(id), FOREIGN KEY (item_id) REFERENCES relief_items(id));

CREATE TABLE distribution_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT,
    destination_community_id INT,
    optimized_route_geojson JSON,
    total_distance_km FLOAT,
    estimated_trucks INT,
    priority_score FLOAT,
    status ENUM('PENDING','IN_TRANSIT','COMPLETED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (destination_community_id) REFERENCES communities(id)
);

-- Incidents
CREATE TABLE incident_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reported_by INT,
    community_id INT,
    incident_type ENUM('FLOOD','CONFLICT','DISEASE','FIRE','CROP'),
    description TEXT,
    severity ENUM('LOW','MEDIUM','HIGH'),
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Audit
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    details TEXT,
    ip VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Seed States
INSERT INTO states (name) VALUES ('Borno'), ('Yobe'), ('Adamawa');
INSERT INTO lgas (state_id, name) VALUES (1, 'Maiduguri'), (1, 'Monguno'), (1, 'Gubio'), (1, 'Dikwa'), (2, 'Damaturu'), (2, 'Gashua'), (3, 'Yola'), (3, 'Mubi');
INSERT INTO relief_items (name, unit) VALUES ('Food (Rice)', 'kg'), ('Medicine', 'Units'), ('Blankets', 'Pieces'), ('Water', 'Liters'), ('Seeds', 'kg');
INSERT INTO warehouses (community_id, name, latitude, longitude) VALUES (1, 'Central Maiduguri Depot', 11.8311, 13.1510);
INSERT INTO inventory (warehouse_id, item_id, quantity) VALUES (1, 1, 5000), (1, 3, 2000);