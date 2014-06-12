-- Add missing "UNSIGNED" to our Users table
ALTER TABLE `talk-talk`.`users` 
CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `provider_version` `provider_version` DECIMAL(2,1) UNSIGNED NOT NULL DEFAULT 1.0;

-- Topics table creation
CREATE TABLE IF NOT EXISTS `talk-talk`.`topics` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `forum_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `author_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `nb_replies` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;