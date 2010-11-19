BEGIN;
CREATE TABLE `picture` (
    `id` integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `image` varchar(100) NOT NULL,
    `album_id` integer NOT NULL
);
CREATE TABLE `album` (
    `id` integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `name` varchar(60) NOT NULL,
    `slug` varchar(50) NOT NULL UNIQUE,
    `description` longtext NOT NULL,
    `created` datetime NOT NULL
);
ALTER TABLE `picture` ADD CONSTRAINT `album_id_refs_id_ccd3755a` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`);COMMIT;
