
-- Add "provider" fields to 'forums', 'topics' & 'posts' tables
ALTER TABLE `talk-talk`.`forums` 
ADD COLUMN `provider` VARCHAR(45) NOT NULL AFTER `nb_posts`,
ADD COLUMN `provider_version` DECIMAL(2,1) UNSIGNED NOT NULL DEFAULT 0.1 AFTER `provider`,
ADD COLUMN `provider_data` VARCHAR(255) NULL DEFAULT NULL AFTER `provider_version`;

ALTER TABLE `talk-talk`.`topics` 
ADD COLUMN `provider` VARCHAR(45) NOT NULL AFTER `nb_replies`,
ADD COLUMN `provider_version` DECIMAL(2,1) UNSIGNED NOT NULL DEFAULT 1.0 AFTER `provider`,
ADD COLUMN `provider_data` VARCHAR(255) NULL DEFAULT NULL AFTER `provider_version`;

ALTER TABLE `talk-talk`.`posts` 
ADD COLUMN `provider` VARCHAR(45) NOT NULL AFTER `content`,
ADD COLUMN `provider_version` DECIMAL(2,1) UNSIGNED NOT NULL DEFAULT 1.0 AFTER `provider`,
ADD COLUMN `provider_data` VARCHAR(255) NULL DEFAULT NULL AFTER `provider_version`;

-- Update 'provider_data' length (45 -> 255) on 'users' table
ALTER TABLE `talk-talk`.`users` 
CHANGE COLUMN `provider_data` `provider_data` VARCHAR(255) NULL DEFAULT NULL ;
