CREATE TABLE contacts (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, countryCode VARCHAR(255) DEFAULT NULL, timeZone VARCHAR(255) DEFAULT NULL, insertedOn DATETIME NOT NULL, updatedOn DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
