CREATE TABLE `prefix_admins` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(254) NOT NULL DEFAULT '',
	`username` VARCHAR(32) NOT NULL DEFAULT '',
	`password` VARCHAR(64) NOT NULL DEFAULT '',
	`role` ENUM('base','full','main','super') NOT NULL,
	`token` VARCHAR(32) NOT NULL DEFAULT '',
	`logins` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`last_login` INT(10) UNSIGNED NULL DEFAULT NULL,
	`attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`last_attempt` INT(10) UNSIGNED NULL DEFAULT NULL,
	`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`creator_id` INT(10) NOT NULL DEFAULT '0',
	`updated` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updater_id` INT(10) NOT NULL DEFAULT '0',
	`deleted` TINYINT(1) NOT NULL DEFAULT '0',
	`deleter_id` INT(10) NOT NULL DEFAULT '0',
	`delete_bit` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;


CREATE TABLE `prefix_sites` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(255) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`logo` VARCHAR(255) NOT NULL DEFAULT '',
	`vkontakte_link` VARCHAR(255) NOT NULL DEFAULT '',
	`twitter_link` VARCHAR(255) NOT NULL DEFAULT '',
	`facebook_link` VARCHAR(255) NOT NULL DEFAULT '',
	`youtube_link` VARCHAR(255) NOT NULL DEFAULT '',
	`odnoklassniki_link` VARCHAR(255) NOT NULL DEFAULT '',
	`google_link` VARCHAR(255) NOT NULL DEFAULT '',
	`instagram_link` VARCHAR(255) NOT NULL DEFAULT '',
	`active` TINYINT(1) UNSIGNED ZEROFILL NOT NULL DEFAULT '1',
	`vk_api_id` VARCHAR(255) NOT NULL DEFAULT '',
	`vk_group_id` VARCHAR(255) NOT NULL DEFAULT '',
	`fb_app_id` VARCHAR(255) NOT NULL DEFAULT '',
	`fb_group_link` VARCHAR(255) NOT NULL DEFAULT '',
	`tw_widget` TEXT NOT NULL,
	`title_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`keywords_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`description_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`creator_id` INT(10) NOT NULL DEFAULT '0',
	`updated` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updater_id` INT(10) NOT NULL DEFAULT '0',
	`deleted` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleter_id` INT(10) NOT NULL DEFAULT '0',
	`delete_bit` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;


CREATE TABLE `prefix_pages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`uri` VARCHAR(255) NOT NULL,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`image` VARCHAR(255) NOT NULL DEFAULT '',
	`text` TEXT NOT NULL,
	`type` ENUM('static','module','page','url') NOT NULL DEFAULT 'static',
	`data` TEXT NOT NULL,
	`position` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`level` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`keywords_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`description_tag` VARCHAR(255) NOT NULL DEFAULT '',
	`sm_changefreq` ENUM('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'daily',
	`sm_priority` VARCHAR(3) NOT NULL DEFAULT '0.5',
	`sm_separate_file` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`creator_id` INT(10) NOT NULL DEFAULT '0',
	`updated` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updater_id` INT(10) NOT NULL DEFAULT '0',
	`deleted` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleter_id` INT(10) NOT NULL DEFAULT '0',
	`delete_bit` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `parent_id_uri_deleted` (`parent_id`, `uri`, `deleted`),
	INDEX `id_deleted_name` (`id`, `deleted`, `name`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;


CREATE TABLE `prefix_settings` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`value` VARCHAR(255) NOT NULL DEFAULT '',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`creator_id` INT(10) NOT NULL DEFAULT '0',
	`updated` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updater_id` INT(10) NOT NULL DEFAULT '0',
	`deleted` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleter_id` INT(10) NOT NULL DEFAULT '0',
	`delete_bit` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;

CREATE TABLE `prefix_admin_fail_logins` (
	`login` VARCHAR(63) NOT NULL DEFAULT '',
	`password` VARCHAR(127) NOT NULL DEFAULT '',
	`ip` VARCHAR(63) NOT NULL DEFAULT '',
	`user_agent` VARCHAR(127) NOT NULL DEFAULT '',
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	INDEX `login` (`login`(16)),
	INDEX `ip` (`ip`(16)),
	INDEX `time` (`time`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;


INSERT INTO `prefix_sites` ( `url`, `name`, `active` ) VALUES ( 'http://reklama78-rus.loc', 'Реклама-78', 1 );
INSERT INTO `prefix_admins` (`email`, `username`, `password`, `active`, `role`) VALUES ('gregor.zoonoid@gmail.com', 'superadmin', '$2a$12$Uc5UVUwpOMtMGUHOAOcRS.ut0bYqxVss.SmpUjrWj05iextmv5CQW', 1, 'super');

