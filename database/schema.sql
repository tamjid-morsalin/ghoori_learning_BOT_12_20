CREATE SCHEMA `tds_bot` ;

CREATE TABLE `tds_bot`.`subscription` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL DEFAULT NULL,
  `fb_name` VARCHAR(256) NULL DEFAULT NULL,
  `messenger_id` VARCHAR(50) NOT NULL,
  `msisdn` VARCHAR(15) NULL DEFAULT NULL,
  `subscribed_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`));