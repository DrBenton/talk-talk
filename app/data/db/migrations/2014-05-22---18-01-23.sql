CREATE TABLE IF NOT EXISTS `talk-talk`.`settings` (
  `key` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key`))
ENGINE = InnoDB;