ALTER TABLE `talk-talk`.`forums` 
ADD COLUMN `metadata` TEXT NULL DEFAULT NULL AFTER `nb_posts`;

ALTER TABLE `talk-talk`.`topics` 
ADD COLUMN `metadata` TEXT NULL DEFAULT NULL AFTER `nb_replies`;

ALTER TABLE `talk-talk`.`posts` 
ADD COLUMN `metadata` TEXT NULL DEFAULT NULL AFTER `content`;