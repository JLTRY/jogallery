DROP TABLE IF EXISTS `#__jgallery`;

CREATE TABLE `#__jgallery` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10)     NOT NULL DEFAULT '0',
	`directory` VARCHAR(256) NOT NULL DEFAULT '',
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

DROP TABLE IF EXISTS `#__jgallery_foldergroups`;
CREATE TABLE `#__jgallery_foldergroups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `published` tinyint NOT NULL DEFAULT '1',
  `name` varchar(25) NOT NULL,
  `folders` varchar(10000) DEFAULT NULL,
  `catid` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;

