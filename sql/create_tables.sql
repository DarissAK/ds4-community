/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ds_group_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_group_data`;

CREATE TABLE `ds_group_data` (
  `group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `permission` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ds_group_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_group_meta`;

CREATE TABLE `ds_group_meta` (
  `group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `ds_group_meta` WRITE;
/*!40000 ALTER TABLE `ds_group_meta` DISABLE KEYS */;

INSERT INTO `ds_group_meta` (`group`, `description`)
VALUES
  (X'44656661756C74','Default Group');

/*!40000 ALTER TABLE `ds_group_meta` ENABLE KEYS */;
UNLOCK TABLES;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
  /*!50003 CREATE */ /*!50017 DEFINER=`root`@`%` */ /*!50003 TRIGGER `update_user` AFTER DELETE ON `ds_group_meta` FOR EACH ROW DELETE FROM `ds_group_data` WHERE `ds_group_data`.`group` = OLD.`group` */;;
DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;


# Dump of table ds_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_log`;

CREATE TABLE `ds_log` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(10) unsigned NOT NULL,
  `creator` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'SYSTEM',
  `affected` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'SYSTEM',
  `event` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `session` varchar(42) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ds_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_permissions`;

CREATE TABLE `ds_permissions` (
  `permission` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `ds_permissions` WRITE;
/*!40000 ALTER TABLE `ds_permissions` DISABLE KEYS */;

INSERT INTO `ds_permissions` (`permission`, `description`)
VALUES
  (X'64735F61646D696E','Access to the administrator module'),
  (X'64735F61646D696E5F6C6F6773','Access to system logs'),
  (X'64735F61646D696E5F7065726D697373696F6E','Access to the permissions interface'),
  (X'64735F61646D696E5F75736572','Access to user administration'),
  (X'64735F746573745F73616E64626F78','Access to the test script sandbox');

/*!40000 ALTER TABLE `ds_permissions` ENABLE KEYS */;
UNLOCK TABLES;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
  /*!50003 CREATE */ /*!50017 DEFINER=`root`@`%` */ /*!50003 TRIGGER `perm_delete` AFTER DELETE ON `ds_permissions` FOR EACH ROW DELETE FROM `ds_group_data` WHERE OLD.`permission` = `permission` */;;
DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;


# Dump of table ds_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_user`;

CREATE TABLE `ds_user` (
  `user` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `password` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `inactive_time` timestamp NULL DEFAULT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'SYSTEM',
  `login_attempts` tinyint(2) NOT NULL DEFAULT '0',
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `last_login_success` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `administrator` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Primary user data table';

LOCK TABLES `ds_user` WRITE;
/*!40000 ALTER TABLE `ds_user` DISABLE KEYS */;

INSERT INTO `ds_user` (`user`, `password`, `status`, `group`, `inactive_time`, `added`, `added_by`, `login_attempts`, `last_login_attempt`, `last_login_success`, `last_login_ip`, `administrator`) VALUES
  ('root', '$2y$10$gzNe5YLn74qraklVT0s1DepQBRJjMUtTp6t3mSDggdzcZZoqJB4ye', 1, 'Default', NULL, CURRENT_TIMESTAMP, 'SYSTEM', 0, NULL, NULL, NULL, 1);


/*!40000 ALTER TABLE `ds_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
