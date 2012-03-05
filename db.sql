DROP DATABASE IF EXISTS sasshare;
CREATE DATABASE sasshare;
USE sasshare;

--
-- MySQL 5.1.58
-- Mon, 05 Mar 2012 17:43:50 +0000
--

CREATE TABLE `documents` (
   `id` int(10) unsigned not null auto_increment,
   `snippet_id` int(11),
   `created` int(30),
   `updated` int(10) unsigned not null,
   `title` varchar(255),
   `syntax` varchar(20),
   `data` longtext,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;

INSERT INTO `documents` (`id`, `snippet_id`, `created`, `updated`, `title`, `syntax`, `data`) VALUES 
('1', '1', '1330969294', '1330969294', 'test.scss', 'scss', 'woooo');

CREATE TABLE `sessions` (
   `id` varchar(40) not null,
   `last_activity` int(10) not null,
   `data` text not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sessions` (`id`, `last_activity`, `data`) VALUES 
('J7CPo6ObGpupzubJVekkBNxNsXon2ODDKsfSR9Ke', '1330969398', 'a:4:{s:5:\":new:\";a:0:{}s:5:\":old:\";a:0:{}s:10:\"csrf_token\";s:40:\"Mli4RzS2fKUQBUR0phrFeNZNWysEqbOnf2dBlZIs\";s:15:\"laravel_user_id\";i:2;}');

CREATE TABLE `snippets` (
   `id` int(10) unsigned not null auto_increment,
   `user_id` int(10) unsigned not null,
   `title` varchar(255),
   `description` mediumtext,
   `created` int(30) unsigned not null default '0',
   `updated` int(10) unsigned not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3;

INSERT INTO `snippets` (`id`, `user_id`, `title`, `description`, `created`, `updated`) VALUES 
('1', '2', 'Test', '', '1330955241', '1330955241');

CREATE TABLE `users` (
   `id` int(10) unsigned not null auto_increment,
   `username` varchar(64) not null,
   `email` varchar(255) not null,
   `password` varchar(128) not null,
   `info` mediumtext,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `info`) VALUES 
('1', 'richthegeek', 'richthegeek@gmail.com', '$2a$08$3fWEpHlemNfe2R0wAzmSGuOz0Y5jR3S4XK54g1pjQ9yNkK.8Chr3S', '{}'),
('2', 'test', 'test@test.co.uk', '$2a$08$AuYKPser6FwGZ1CwKRcwye1nfdKEs/H8lcc8J3XAT.3XsQKNqPxum', '{}');

CREATE TABLE `votes` (
   `user_id` int(11),
   `snippet_id` int(10) unsigned not null,
   `direction` tinyint(1) unsigned not null default '1',
   `created` int(30) unsigned not null default '0',
   UNIQUE KEY (`user_id`,`snippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `votes` (`user_id`, `snippet_id`, `direction`, `created`) VALUES 
('2', '2', '1', '1330955998'),
('2', '1', '1', '1330956031');