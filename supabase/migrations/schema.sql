-- 1. USERS
CREATE TABLE Users (
                       user_id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(50) NOT NULL,
                       email VARCHAR(100) UNIQUE NOT NULL,
                       password VARCHAR(255) NOT NULL
);

-- 2. CARDS
CREATE TABLE Cards (
                       card_id INT AUTO_INCREMENT PRIMARY KEY,
                       card_num VARCHAR(20) NOT NULL,
                       expiry_date DATE NOT NULL,
                       cvc VARCHAR(4) NOT NULL,
                       `default` BOOLEAN DEFAULT FALSE,
                       user_id INT NOT NULL,
                       FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 3. STATIONS
CREATE TABLE Stations (
                          station_id INT AUTO_INCREMENT PRIMARY KEY,
                          address_street VARCHAR(100) NOT NULL,
                          latitude DECIMAL(9,6),
                          longitude DECIMAL(9,6),
                          columns_num INT,
                          address_city VARCHAR(50),
                          address_municipality VARCHAR(50),
                          address_civic_num VARCHAR(10),
                          address_zipcode VARCHAR(10)
);

-- 4. CHARGING_POINTS
CREATE TABLE Charging_Points (
                                 charging_point_id INT AUTO_INCREMENT PRIMARY KEY,
                                 occupied BOOLEAN DEFAULT FALSE,
                                 slots_num INT NOT NULL,
                                 station_id INT NOT NULL,
                                 FOREIGN KEY (station_id) REFERENCES Stations(station_id)
);

-- 5. CHARGINGS
CREATE TABLE Chargings (
                           charging_id INT AUTO_INCREMENT PRIMARY KEY,
                           start_datetime DATETIME NOT NULL,
                           end_datetime DATETIME DEFAULT NULL,
                           cost DECIMAL(10,2) DEFAULT NULL,
                           energy_consumed DECIMAL(10,2),
                           status VARCHAR(20),
                           user_id INT NOT NULL,
                           charging_point_id INT NOT NULL,
                           card_num VARCHAR(20),
                           expiry_date DATE,
                           cvc VARCHAR(4),
                           FOREIGN KEY (user_id) REFERENCES Users(user_id),
                           FOREIGN KEY (charging_point_id) REFERENCES Charging_Points(charging_point_id)
);

-- 6. BOOKINGS
CREATE TABLE Bookings (
                          booking_id INT AUTO_INCREMENT PRIMARY KEY,
                          booking_datetime DATETIME NOT NULL,
                          booking_end_datetime DATETIME NOT NULL,
                          user_id INT NOT NULL,
                          charging_point_id INT NOT NULL,
                          FOREIGN KEY (user_id) REFERENCES Users(user_id),
                          FOREIGN KEY (charging_point_id) REFERENCES Charging_Points(charging_point_id)
);

-- 7. ADMINS
CREATE TABLE Admins (
                        admin_id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 8. OPERATORS
CREATE TABLE Operators (
                           operator_id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT NOT NULL,
                           FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 9. CENTRAL_OFFICES
CREATE TABLE Central_Offices (
                                 office_id INT AUTO_INCREMENT PRIMARY KEY,
                                 office_name VARCHAR(100),
                                 office_city VARCHAR(50),
                                 office_street VARCHAR(100),
                                 office_civic VARCHAR(10),
                                 phone_number VARCHAR(20),
                                 operator_id INT NOT NULL,
                                 FOREIGN KEY (operator_id) REFERENCES Operators(operator_id)
);

-- 10. REPORTS (aggiornato: usa operator_id)
CREATE TABLE Reports (
                         report_id INT AUTO_INCREMENT PRIMARY KEY,
                         operator_id INT NOT NULL,
                         FOREIGN KEY (operator_id) REFERENCES Operators(operator_id)
);

-- 11. MALFUNCTIONS
CREATE TABLE Malfunctions (
                              malfunction_id INT AUTO_INCREMENT PRIMARY KEY,
                              description TEXT NOT NULL,
                              report_id INT,
                              state VARCHAR(20),
                              FOREIGN KEY (report_id) REFERENCES Reports(report_id)
);

-- 12. TRIGGER: riempie dati carta se non specificati
DELIMITER $$

CREATE TRIGGER trg_fill_default_card
    BEFORE INSERT ON Chargings
    FOR EACH ROW
BEGIN
    DECLARE default_card_num VARCHAR(20);
    DECLARE default_expiry DATE;
    DECLARE default_cvc VARCHAR(4);

    IF NEW.card_num IS NULL OR NEW.expiry_date IS NULL OR NEW.cvc IS NULL THEN
    SELECT card_num, expiry_date, cvc
    INTO default_card_num, default_expiry, default_cvc
    FROM Cards
    WHERE user_id = NEW.user_id AND `default` = TRUE
        LIMIT 1;

    IF default_card_num IS NOT NULL THEN
            SET NEW.card_num = default_card_num;
            SET NEW.expiry_date = default_expiry;
            SET NEW.cvc = default_cvc;
    ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No default card found for this user';
END IF;
END IF;
END$$

DELIMITER ;

