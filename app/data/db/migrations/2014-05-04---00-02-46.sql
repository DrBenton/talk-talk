ALTER TABLE `talk-talk`.`users`
CHANGE COLUMN `provider_version` `provider_version` DECIMAL(2,1) NOT NULL DEFAULT '1.0' AFTER `provider`;