DROP TABLE IF EXISTS `posts`;
--!ENDQUERY--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `title` varchar(400) COLLATE utf8_bin DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `categoryid` int(11) DEFAULT NULL,
  `imagefile` varchar(400) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime NOT NULL,
  `lastupdated` datetime NOT NULL,
  `shortdesc` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `content` longtext COLLATE utf8_bin DEFAULT NULL,
  `featuredpos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
--!ENDQUERY--

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created` (`created`),
  ADD KEY `all cats by date` (`deleted`,`created`) USING BTREE,
  ADD KEY `per category` (`deleted`,`categoryid`,`created`) USING BTREE,
  ADD KEY `category all` (`categoryid`,`created`) USING BTREE,
  ADD KEY `userId` (`userid`),
  ADD KEY `featuredpos` (`featuredpos`);

--!ENDQUERY--
INSERT IGNORE INTO `posts` (`id`, `deleted`, `title`, `userid`, `categoryid`, `imagefile`, `created`, `lastupdated`, `shortdesc`, `content`, `featuredpos`) VALUES
(1, 0, 'Какво рече момчето? част 1', 0, NULL, '0.998044001665647659.jpg', '2022-10-13 08:01:17', '2022-10-13 11:11:02', 'Нещо се случило в Карнобат по време на гроздобера.', 'Нещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.\r\nНещо се случило в Карнобат по време на гроздобера.', NULL),
(2, 0, 'Test title 2022-10-13 08:36:36', 1, NULL, 'testimage5.jpg', '2022-10-13 09:36:36', '2022-10-13 09:36:36', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(3, 0, 'Test title 2022-10-13 08:38:59', 1, NULL, 'testimage4.jpg', '2022-10-13 09:38:59', '2022-10-13 09:38:59', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(4, 0, 'Test title 2022-10-13 08:39:13', 1, NULL, 'testimage2.jpg', '2022-10-13 09:39:13', '2022-10-13 09:39:13', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(5, 0, 'Test title 2022-10-13 08:39:43', 1, NULL, 'testimage1.jpg', '2022-10-13 09:39:43', '2022-10-13 09:39:43', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(6, 0, 'Test title 2022-10-13 08:40:10', 1, NULL, 'testimage2.jpg', '2022-10-13 09:40:10', '2022-10-13 09:40:10', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(7, 0, 'Test title 2022-10-13 08:40:46', 1, NULL, 'testimage3.jpg', '2022-10-13 09:40:46', '2022-10-13 09:40:46', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(8, 1, 'Test title 2022-10-13 08:41:04', 1, NULL, 'testimage1.jpg', '2022-10-13 09:41:04', '2022-10-13 09:41:04', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(9, 1, 'Test title 2022-10-13 08:41:13', 1, NULL, 'testimage1.jpg', '2022-10-13 09:41:13', '2022-10-13 09:41:13', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(10, 1, 'Test title 2022-10-13 08:45:59', 1, NULL, 'testimage3.jpg', '2022-10-13 09:45:59', '2022-10-13 09:45:59', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(11, 0, 'Test title 2022-10-13 08:45:59', 1, NULL, 'testimage5.jpg', '2022-10-13 09:45:59', '2022-10-13 09:45:59', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(12, 0, 'Test title 2022-10-13 08:46:25', 1, NULL, 'testimage3.jpg', '2022-10-13 09:46:25', '2022-10-13 09:46:25', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(13, 0, 'Test title 2022-10-13 08:46:25', 1, NULL, '0.827414001665647709.jpg', '2022-10-13 09:46:25', '2022-10-13 10:55:09', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(14, 0, 'Test title 2022-10-13 08:46:49', 1, NULL, 'testimage2.jpg', '2022-10-13 09:46:49', '2022-10-13 09:46:49', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(15, 0, 'EDITTest title 2022-10-13 08:46:49', 1, NULL, 'testimage1.jpg', '2022-10-13 09:46:49', '2022-10-13 09:46:49', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(16, 0, 'Test title 2022-10-13 08:47:16', 1, NULL, 'testimage4.jpg', '2022-10-13 09:47:16', '2022-10-13 09:47:16', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(17, 0, 'EDITTest title 2022-10-13 08:47:16', 1, NULL, 'testimage3.jpg', '2022-10-13 09:47:16', '2022-10-13 09:47:16', 'Невероятно, но факт- извънземните имат бази на Венера.', 'Нещо си там да пишем.', 0),
(18, 0, 'fdfddf', 1, NULL, 'testimage.jpg', '2022-10-13 11:20:18', '2022-10-13 11:20:18', 'ffddf', 'fdfdfd', NULL),
(19, 0, 'ssa', 1, NULL, 'upload/0.275450001665649360.jpg', '2022-10-13 11:22:40', '2022-10-13 11:38:42', 'sasa', '<p>sasa</p>', NULL);

