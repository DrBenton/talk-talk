-- add Foreign Keys to our Topics table
ALTER TABLE `talk-talk`.`topics` 
ADD CONSTRAINT `topics_forum_id_fk`
  FOREIGN KEY (`forum_id`)
  REFERENCES `talk-talk`.`forums` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `topics_author_id_fk`
  FOREIGN KEY (`author_id`)
  REFERENCES `talk-talk`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
ALTER TABLE `talk-talk`.`topics` 
ADD INDEX `topics_forum_id_fk_idx` (`forum_id` ASC),
ADD INDEX `topics_author_id_fk_idx` (`author_id` ASC);
  
-- Posts table creation
CREATE TABLE IF NOT EXISTS `talk-talk`.`posts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `forum_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `topic_id` INT(10) UNSIGNED NOT NULL,
  `author_id` INT(10) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `posts_user_id_fk_idx` (`author_id` ASC),
  INDEX `posts_topic_id_fk_idx` (`topic_id` ASC),
  INDEX `posts_forum_id_fk_idx` (`forum_id` ASC),
  CONSTRAINT `posts_forum_id_fk`
    FOREIGN KEY (`forum_id`)
    REFERENCES `talk-talk`.`forums` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `posts_topic_id_fk`
    FOREIGN KEY (`topic_id`)
    REFERENCES `talk-talk`.`topics` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `posts_user_id_fk`
    FOREIGN KEY (`author_id`)
    REFERENCES `talk-talk`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

