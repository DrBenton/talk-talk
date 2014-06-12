-- Renaming our "name" column to "title" on Forums & Topics tables for more consistency

ALTER TABLE `forums`
CHANGE `name` `title` varchar(255) NOT NULL AFTER `parent_id`;

ALTER TABLE `topics`
CHANGE `name` `title` varchar(255) NOT NULL AFTER `author_id`;