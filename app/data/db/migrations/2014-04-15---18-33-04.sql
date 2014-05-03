
-- -----------------------------------------------------
-- Schema talk-talk
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `talk-talk` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `talk-talk` ;

-- -----------------------------------------------------
-- Table `talk-talk`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `talk-talk`.`user` ;

CREATE TABLE IF NOT EXISTS `talk-talk`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `login` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));

