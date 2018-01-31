SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `users` (
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `views` int(11) NOT NULL,
  `mindate` date NOT NULL,
  `maxdate` date NOT NULL,
  `zon` tinyint(1) NOT NULL,
  `nacht` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `users`
  ADD PRIMARY KEY (`name`);
COMMIT;
