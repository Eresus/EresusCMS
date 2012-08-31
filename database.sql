-- --------------------------------------------------------
--
-- ��������� ������� `pages`
--

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `owner` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) default NULL,
  `caption` varchar(64) default NULL,
  `description` varchar(255) default NULL,
  `hint` varchar(255) default NULL,
  `keywords` varchar(255) default NULL,
  `position` smallint(5) unsigned default NULL,
  `active` tinyint(1) NOT NULL default '0',
  `access` tinyint(1) unsigned default NULL,
  `visible` tinyint(1) unsigned default NULL,
  `template` varchar(64) default NULL,
  `type` varchar(32) NOT NULL default 'default',
  `content` longtext,
  `options` text,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `owner` (`owner`),
  KEY `position` (`position`),
  KEY `active` (`active`),
  KEY `access` (`access`),
  KEY `visibility` (`visible`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) TYPE=MyISAM PACK_KEYS=0 COMMENT='��������� � �������� �����' AUTO_INCREMENT=2 ;

--
-- ���� ������ ������� `pages`
--

INSERT INTO `pages` VALUES (1, 'main', 0, '�������', '�������', '', '������� ��������', '', 0, 1, 5, 1, 'default', 'html', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- ��������� ������� `plugins`
--

CREATE TABLE `plugins` (
  `name` varchar(32) NOT NULL default '',
  `type` set('client','admin','content','ondemand') default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `position` int(10) unsigned default NULL,
  `settings` text,
  `title` varchar(64) default NULL,
  `version` varchar(16) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`name`),
  KEY `active` (`active`),
  KEY `priority` (`position`),
  KEY `class` (`type`)
) TYPE=MyISAM COMMENT='������ ����������';

--
-- ���� ������ ������� `plugins`
--

INSERT INTO `plugins` VALUES ('html', 'client,content,ondemand', 1, 2, '', 'HTML', '2.00b', 'HTML ��������');

-- --------------------------------------------------------
--
-- ��������� ������� `users`
--

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(16) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `active` tinyint(1) unsigned NOT NULL default '1',
  `lastVisit` datetime default NULL,
  `lastLoginTime` int(10) unsigned default NULL,
  `loginErrors` int(10) unsigned default NULL,
  `access` tinyint(3) unsigned default NULL,
  `name` varchar(64) default NULL,
  `mail` varchar(64) default NULL,
  `profile` text default NULL,
  PRIMARY KEY  (`id`),
  KEY `login` (`login`),
  KEY `active` (`active`)
) TYPE=MyISAM PACK_KEYS=0 COMMENT='������������' AUTO_INCREMENT=2;

--
-- ���� ������ ������� `users`
--

INSERT INTO `users` VALUES (1, 'root', 'd41d8cd98f00b204e9800998ecf8427e', 1, '0000-00-00 00:00:00', 0, 0, 1, '������� �������������', '', '');
INSERT INTO `users` VALUES (2, 'admin', 'd41d8cd98f00b204e9800998ecf8427e', 1, '0000-00-00 00:00:00', 0, 0, 2, '�������������', '', '');
