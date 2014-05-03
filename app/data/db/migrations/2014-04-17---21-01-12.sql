ALTER TABLE `talk-talk`.`user` 
CHANGE COLUMN `login` `login` VARCHAR(45) NOT NULL ,
CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL ,
CHANGE COLUMN `password` `password` VARCHAR(255) NOT NULL ,
ADD COLUMN `provider` VARCHAR(45) NOT NULL AFTER `password`,
ADD COLUMN `provider_version` DECIMAL(2) NOT NULL DEFAULT 1.0 AFTER `provider`,
ADD COLUMN `provider_data` VARCHAR(45) NULL DEFAULT NULL AFTER `provider_version`;
