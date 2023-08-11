
DROP TABLE IF EXISTS `#__jgallery_foldergroups`;
CREATE TABLE `#__jgallery_foldergroups` (
  `id` int(12) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(25) NOT NULL,
  `folders` varchar(1024) DEFAULT NULL
  `catid`	    int(11)    NOT NULL DEFAULT '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

