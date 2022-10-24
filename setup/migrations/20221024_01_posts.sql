!!ALTER TABLE `posts` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
--!ENDQUERY--
delete from `posts` where id=0;

