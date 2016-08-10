<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- -----------------------------------------------------
-- Table `mydb`.`sdc_feedportal`
-- -----------------------------------------------------
DROP TABLE IF EXISTS {$this->getTable('sdc_feedportal')} ;

CREATE  TABLE IF NOT EXISTS {$this->getTable('sdc_feedportal')} (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `id_store` INT NULL ,    
  `namejoin_join` VARCHAR(255) NULL ,
  `namejoin_url` VARCHAR(255) NULL ,
  `namelogin_join` VARCHAR(255) NULL ,
  `namelogin_url` VARCHAR(255) NULL COMMENT 'Look up table to contain Shopping.com feeds join - name/url & login - name/url.' ,
  `country` VARCHAR(255) NULL ,
  `country_code` VARCHAR(45) NULL ,
  `created_at` DATETIME NULL ,
  `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`sdc_feed`
-- -----------------------------------------------------
DROP TABLE IF EXISTS {$this->getTable('sdc_feed')} ;

CREATE  TABLE IF NOT EXISTS {$this->getTable('sdc_feed')} (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `id_feedportal` INT NULL ,
  `status` TINYINT(1) NULL ,
  `ftp` VARCHAR(255) NULL ,
  `username` VARCHAR(255) NULL ,
  `password` VARCHAR(255) NULL ,
  `filename` VARCHAR(255) NULL ,  
  `id_frequency` INT NULL ,
  `id_store` INT NULL ,  
  `successful_update` TIMESTAMP NULL ,
  `successful_upload` TIMESTAMP NULL ,
  `successful_export` TIMESTAMP NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`sdc_feedproducts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS {$this->getTable('sdc_feedproducts')} ;

CREATE  TABLE IF NOT EXISTS {$this->getTable('sdc_feedproducts')} (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `product_ids` LONGTEXT NULL ,
  `id_feed` VARCHAR(45) NULL ,
  `id_feedportal` INT NULL COMMENT 'Feed frequency Lookup table.' ,
  `id_frequency` INT NULL ,
  `id_store` INT NULL ,    
  `created_at` DATETIME NULL ,
  `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



-- -----------------------------------------------------
-- Table `mydb`.`sdc_updatefrequency`
-- -----------------------------------------------------
DROP TABLE IF EXISTS {$this->getTable('sdc_updatefrequency')} ;

CREATE  TABLE IF NOT EXISTS {$this->getTable('sdc_updatefrequency')} (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `label` LONGTEXT NULL ,
  `cron_code` VARCHAR(255) NULL ,
  `other` TEXT NULL COMMENT 'Feed frequency Lookup table.' ,
  `description` TEXT NULL ,
  `created_at` DATETIME NULL ,
  `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



-- -----------------------------------------------------
-- Data for table `mydb`.`sdc_feedportal`
-- -----------------------------------------------------
INSERT INTO {$this->getTable('sdc_feedportal')} (`id`, `namejoin_join`, `namejoin_url`, `namelogin_join`, `namelogin_url`, `country`, `country_code`, `created_at`, `updated_at`) VALUES (NULL, 'USA Join', 'https://merchant.shopping.com/enroll/HomePage.html?country=US', 'USA Login', 'https://merchant.shopping.com/mac/app', 'United States', 'US', NULL, NULL);
INSERT INTO {$this->getTable('sdc_feedportal')} (`id`, `namejoin_join`, `namejoin_url`, `namelogin_join`, `namelogin_url`, `country`, `country_code`, `created_at`, `updated_at`) VALUES (NULL, 'AU Join', 'https://au.merchant.shopping.com/enroll/HomePage.html?country=AU', 'AU Login', 'https://au.merchant.shopping.com/mac/app ', 'Australia', 'AU', NULL, NULL);
INSERT INTO {$this->getTable('sdc_feedportal')} (`id`, `namejoin_join`, `namejoin_url`, `namelogin_join`, `namelogin_url`, `country`, `country_code`, `created_at`, `updated_at`) VALUES (NULL, 'UK Join', 'https://ukmerchant.shopping.com/enroll/HomePage.html?country=GB', 'UK Login', 'https://ukmerchant.shopping.com/mac/app ', 'United Kingdom', 'UK', NULL, NULL);
INSERT INTO {$this->getTable('sdc_feedportal')} (`id`, `namejoin_join`, `namejoin_url`, `namelogin_join`, `namelogin_url`, `country`, `country_code`, `created_at`, `updated_at`) VALUES (NULL, 'DE Join', 'https://haendler.shopping.com/enroll/Startseite.html', 'DE Login', 'https://haendler.shopping.com/mac/app?service=locale-changer/Login/de ', 'Germany', 'DE', NULL, NULL);
INSERT INTO {$this->getTable('sdc_feedportal')} (`id`, `namejoin_join`, `namejoin_url`, `namelogin_join`, `namelogin_url`, `country`, `country_code`, `created_at`, `updated_at`) VALUES (NULL, 'FR Join', 'https://marchand.shopping.com/enroll/Pagedaccueil.html', 'FR Login', 'https://marchand.shopping.com/mac/app?service=locale-changer/Login/fr', 'France', 'FR', NULL, NULL);


-- -----------------------------------------------------
-- Data for table `mydb`.`sdc_updatefrequency`
-- -----------------------------------------------------
INSERT INTO {$this->getTable('sdc_updatefrequency')} (`id`, `label`, `cron_code`, `other`, `description`, `created_at`, `updated_at`) VALUES (NULL, 'Daily', '* * 1 * *', NULL, NULL, NULL, NULL);
INSERT INTO {$this->getTable('sdc_updatefrequency')} (`id`, `label`, `cron_code`, `other`, `description`, `created_at`, `updated_at`) VALUES (NULL, 'Weekly', '* * * * * 1', NULL, NULL, NULL, NULL);

");


$installer->endSetup();

?>