
-- ddev provides/suggests a DB called "db"
-- CREATE DATABASE User_2406;

-- ddev provides/suggests a DB called "db"
-- USE User_2406;

USE db;


DROP TABLE IF EXISTS User;

-- Class User
CREATE TABLE User (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50),
    lastname VARCHAR(51),
    age TINYINT
);


-- TODO: Table for Company/Group
-- TODO:


-- not in use!
ALTER TABLE User
    ADD COLUMN haircolor VARCHAR(32) DEFAULT '',
    ADD COLUMN eyecolor VARCHAR(32) DEFAULT '',
    ADD COLUMN dna TEXT DEFAULT '',
    ADD COLUMN fingerprint VARCHAR(666) DEFAULT ''
    ;


USE db;

DROP TABLE IF EXISTS LoginLog;

CREATE TABLE LoginLog (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    time DATETIME,
    comment VARCHAR(128),

    FOREIGN KEY (UserID) REFERENCES User(ID)
);

INSERT INTO LoginLog (UserID, time, comment) VALUES
  (1, '2025-03-01 12:00:00', 'Test 1 User 1')
 ,(2, '2025-03-01 13:00:00', 'Test 2 User 2')
 ,(2, '2025-03-01 14:00:00', 'Test 3 User 2')
 ,(3, '2025-03-01 15:00:00', 'Test 4 User 3')
 ,(3, '2025-03-01 16:00:00', 'Test 5 User 3')
;
