SET sql_notes = 0;

/* Location */

CREATE TABLE IF NOT EXISTS `locations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Country` VARCHAR(60) NULL,
  `Province` VARCHAR(60) NULL,
  `City` VARCHAR(60) NULL,
  `PostalCode` VARCHAR(20) NULL,
  `Longitude` DECIMAL(18,12) NULL,
  `Latitude` DECIMAL(18,12) NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

/* Company Features */

CREATE TABLE IF NOT EXISTS `company_features` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `MaxTablets` INT(11) NOT NULL DEFAULT 0,
  `MaxAccounts` INT(11) NOT NULL DEFAULT 0,
  `MaxStores` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

/* Company Categories */

CREATE TABLE IF NOT EXISTS `company_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(100) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

/* Companies */

CREATE TABLE IF NOT EXISTS `companies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `DateCreated` DATETIME NULL,
  `Name` VARCHAR(200) NOT NULL DEFAULT '',
  `CategoryID` INT(11) NULL,
  `FeaturesID` INT(11) NULL,
  `Active` BIT(1) NOT NULL DEFAULT 0,
  `Website` VARCHAR(100) NULL,
  `PhoneNumber` VARCHAR(50),
  `ExpiryDate` DATETIME NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`CategoryID`) REFERENCES company_categories(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`FeaturesID`) REFERENCES company_features(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Company Keywords */

CREATE TABLE IF NOT EXISTS `company_keywords` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(100) NOT NULL,
  `CategoryID` INT(11) NOT NULL,
  `Aliases` VARCHAR(200) NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`CategoryID`) REFERENCES company_categories(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Company Keywords Link */

CREATE TABLE IF NOT EXISTS `company_keywords_link` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `CompanyKeywordID` INT(11) NOT NULL,
  `CompanyID` INT(11) NOT NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`CompanyKeywordID`) REFERENCES company_keywords(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`CompanyID`) REFERENCES companies(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Store Features */

CREATE TABLE IF NOT EXISTS `store_features` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `CollectionTemplate` TEXT NULL,
    `CollectionLocation` TINYINT(2) NULL,
    `SessionCheck` TINYINT(1) NOT NULL DEFAULT 1,
    `WelcomeMessage` TEXT NULL,
    `AllowComments` TINYINT(1) NOT NULL DEFAULT 0,
    `CommentMessage` TEXT NULL
) ENGINE=InnoDB;

/* Stores */

CREATE TABLE IF NOT EXISTS `stores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `DateCreated` DATETIME NULL,
  `Name` VARCHAR(200) NOT NULL DEFAULT '',
  `CompanyID` INT(11) NULL,
  `Active` BIT(1) NOT NULL DEFAULT 0,
  `URLName` VARCHAR(100) NULL,
  `Website` VARCHAR(100) NULL,
  `PhoneNumber` VARCHAR(50) NULL,
  `LocationID` INT(11) NULL,
  `FeaturesID` INT(11) NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`LocationID`) REFERENCES locations(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`CompanyID`) REFERENCES companies(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`FeaturesID`) REFERENCES store_features(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Session Data */

CREATE TABLE IF NOT EXISTS session_data (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `SessionCode` VARCHAR(100) NOT NULL UNIQUE KEY,
    `SubmissionTime` INT(11) NOT NULL,
    `Acknowledged` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

/* Session Data Field */

CREATE TABLE IF NOT EXISTS `session_data_field` (
    `SessionDataID` INT(11) NOT NULL,
    `DataLabel` VARCHAR(255) NOT NULL,
    `DataKey` VARCHAR(255) NOT NULL,
    `DataValueLarge` TEXT NULL,
    `DataValueSmall` VARCHAR(255) NULL,
    PRIMARY KEY (`SessionDataID`, `DataKey`)
) ENGINE=InnoDB;

/* Tablets */

CREATE TABLE IF NOT EXISTS `tablets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `SerialCode` VARCHAR(80) NOT NULL,
  `StoreID` INT(11) NULL,
  `Status` VARCHAR(50) NOT NULL DEFAULT '',
  `OnlineSince` INT(11) NULL,
  `IPAddress` VARCHAR(45) NULL,
  `BatteryPercent` FLOAT(5,2) NULL,
  `BatteryPluggedIn` BIT(1) NULL,
  `PositionLatitude` DECIMAL(18,12) NULL,
  `PositionLongitude` DECIMAL(18,12) NULL,
  `PositionTimestamp` INT(11) NULL,
  `StoredDataCount` INT(11) NULL DEFAULT 0,
  `DeviceVersion` VARCHAR(30) NULL,
  `DeviceModel` VARCHAR(50) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`StoreID`) REFERENCES stores(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Tablet Commands */

CREATE TABLE IF NOT EXISTS `tablet_commands` (
    `id` INT(11) AUTO_INCREMENT,
    `TabletID` INT(11) NOT NULL,
    `DateIssued` INT(11) NOT NULL,
    `Command` VARCHAR(100) NOT NULL,
    `Received` BIT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`TabletID`) REFERENCES tablets(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Dashboard Settings TODO: DROP. */

CREATE TABLE IF NOT EXISTS `dashboard_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

/* Dashboard */

CREATE TABLE IF NOT EXISTS `dashboard` (
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `StoreID` INT(11) NULL,
   `SettingsID` INT(11) NULL,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`SettingsID`) REFERENCES dashboard_settings(`id`)
     ON DELETE CASCADE
     ON UPDATE CASCADE,
   FOREIGN KEY (`StoreID`) REFERENCES stores(`id`)
     ON DELETE CASCADE
     ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Aspect Type */

CREATE TABLE IF NOT EXISTS `aspect_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(50) NULL DEFAULT '',
  `Description` VARCHAR(200) NULL,
  `CompanyID` INT(11) NULL,
  PRIMARY KEY (`id`),
   FOREIGN KEY (`CompanyID`) REFERENCES companies(`id`)
     ON DELETE SET NULL
     ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Aspects */

CREATE TABLE IF NOT EXISTS `aspects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `StoreID` INT(11) NULL,
  `AspectTypeID` INT(11) NULL,
  `Description` VARCHAR(200) NULL DEFAULT '',
  `Active` BIT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`StoreID`) REFERENCES stores(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`AspectTypeID`) REFERENCES aspect_type(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* User Agents */

CREATE TABLE IF NOT EXISTS `user_agents` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `UserAgent` TEXT NULL,
  `TabletID` INT(11) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`TabletID`) REFERENCES tablets(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Feedback Text TODO: Remove. */

CREATE TABLE IF NOT EXISTS `feedback_text` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `FeedbackText` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

/* Feedback TODO: Optimize. */

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `AspectID` INT(11) NULL,
  `Date` DATETIME NULL,
  `FeedbackTextID` INT(11) NULL,
  `Rating` FLOAT(5,2) NULL DEFAULT -1,
  `IPAddress` VARCHAR(45) NULL,
  `UserAgentID` INT(11) NULL,
  `LocationID` INT(11) NULL,
  `SessionCode` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`AspectID`) REFERENCES aspects(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`FeedbackTextID`) REFERENCES feedback_text(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`UserAgentID`) REFERENCES user_agents(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`LocationID`) REFERENCES locations(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Subscription */

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `StoreID` INT(11) NULL,
  `Date` DATETIME NULL,
  `EmailAddress` VARCHAR(100) NOT NULL,
  `IPAddress` VARCHAR(45) NULL,
  `UserAgentID` INT(11) NULL,
  `LocationID` INT(11) NULL,
  `UniqueCode` VARCHAR(20) NULL,
  `SessionCode` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`StoreID`) REFERENCES stores(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`UserAgentID`) REFERENCES user_agents(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`LocationID`) REFERENCES locations(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;


/* Accounts */

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `DateCreated` DATETIME NULL,
  `FirstName` VARCHAR(100) NULL,
  `LastName` VARCHAR(100) NULL,
  `EmailAddress` VARCHAR(100) NOT NULL, /* Added NOT NULL */
  `Password` VARCHAR(130) NOT NULL,
  `CompanyID` INT(11) NULL,
  `StoreID` INT(11) NULL,
  `Permissions` TINYINT(3) UNSIGNED NOT NULL DEFAULT 82,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`CompanyID`) REFERENCES companies(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`StoreID`) REFERENCES stores(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Transactions */

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Date` DATETIME NOT NULL,
  `CompanyID` INT(11) NULL,
  `Value` INT(11) NOT NULL,
  `Currency` ENUM('CAD', 'USD') NOT NULL,
  `Product` VARCHAR(100) NULL,
  `Confirmed` BIT(1) NOT NULL DEFAULT 0,
  `PaypalTransactionID` VARCHAR(20) NULL,
  `PaypalPayerEmail` VARCHAR(100) NULL,
  `Fraud` VARCHAR(500) NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`CompanyID`) REFERENCES companies(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Promo Codes */

CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `DateIssued` DATETIME NOT NULL,
  `IssuerID` INT(11) NULL,
  `DiscountedValue` INT(11) NOT NULL,
  `Used` BIT(1) NOT NULL DEFAULT 0,
  `Code` VARCHAR(50) NOT NULL,
  `PaypalItemName` VARCHAR(100) NULL,
  PRIMARY KEY(`id`),
  FOREIGN KEY (`IssuerID`) REFERENCES accounts(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Data Cache TODO: Drop. */

CREATE TABLE IF NOT EXISTS data_cache (
    `DaysBack` INT DEFAULT -1, -- number of days from end date
    `EndDate` DATETIME DEFAULT '0000-00-00 00:00:00.000000',
    `LastModified` DATETIME DEFAULT NULL,
    `Domain_AspectID` INT DEFAULT -1, -- aspect type
    `Domain_StoreID` INT DEFAULT -1,
    `Domain_CompanyID` INT DEFAULT -1,
    `Domain_IndustryID` INT DEFAULT -1,
    `NumberOfClusters` INT DEFAULT 0,
    `TotalAverage` FLOAT(5,2) DEFAULT 0,
    `TotalDataSize` INT DEFAULT 0,
    `CachedData` TEXT DEFAULT NULL,
    PRIMARY KEY (
        `DaysBack`, `EndDate`, `Domain_AspectID`,
        `Domain_StoreID`, `Domain_CompanyID`,
        `Domain_IndustryID`
    )
) ENGINE=InnoDB;

/* Support */

CREATE TABLE IF NOT EXISTS `support` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `AccountID` INT(11) NULL,
    `Date` DATETIME NOT NULL,
    `Message` TEXT NOT NULL,
    `Resolved` BIT NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`),
    FOREIGN KEY (`AccountID`) REFERENCES accounts(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Support Responses */

CREATE TABLE IF NOT EXISTS `support_responses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `SupportID` INT(11) NULL,
    `AccountID` INT(11) NULL,
    `Date` DATETIME NOT NULL,
    `Message` TEXT NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY (`AccountID`) REFERENCES accounts(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (`SupportID`) REFERENCES support(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Notification Type */

CREATE TABLE IF NOT EXISTS `notification_type` (
    `id` INT(11) AUTO_INCREMENT NOT NULL,
    `Title` VARCHAR(100) NULL,
    `Icon` VARCHAR(100) NULL,
    PRIMARY KEY(`id`)
) ENGINE=InnoDB;

/* Notifications */

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT(11) AUTO_INCREMENT NOT NULL,
    `ToAccount` INT(11) NOT NULL,
    `NType` INT(11) NOT NULL,
    `Dismissed` BIT NOT NULL DEFAULT 0,
    `Silent` BIT NOT NULL DEFAULT 0,
    `Title` VARCHAR(255) NOT NULL,
    `Description` TEXT NULL,
    `Date` DATETIME NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY (`ToAccount`) REFERENCES accounts(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (`NType`) REFERENCES notification_type(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Milestones */

CREATE TABLE IF NOT EXISTS milestones (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `StoreID` INT(11) NOT NULL,
    `Title` VARCHAR(100) NOT NULL,
    `FromDate` DATETIME NOT NULL,
    `ToDate` DATETIME NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`StoreID`) REFERENCES stores(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    UNIQUE KEY(`StoreID`, `Title`, `FromDate`, `ToDate`)
) ENGINE=InnoDB;

/* Milestone Aspects */

CREATE TABLE IF NOT EXISTS milestone_aspects (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `MilestoneID` INT(11) NOT NULL,
    `AspectID` INT(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE KEY(`MilestoneID`, `AspectID`),
    FOREIGN KEY(`MilestoneID`) REFERENCES milestones(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY(`AspectID`) REFERENCES aspects(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

SET sql_notes = 1;
