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
  `group_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `group_data` (`group_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `group_id` FOREIGN KEY (`group_id`) REFERENCES `ds_group_meta` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_id` FOREIGN KEY (`permission_id`) REFERENCES `ds_permissions` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ds_group_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_group_meta`;

CREATE TABLE `ds_group_meta` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `ds_group_meta` WRITE;
/*!40000 ALTER TABLE `ds_group_meta` DISABLE KEYS */;

INSERT INTO `ds_group_meta` (`group_id`, `name`, `description`)
VALUES
  (1,'Default','Default group');

/*!40000 ALTER TABLE `ds_group_meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ds_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_logs`;

CREATE TABLE `ds_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(10) unsigned NOT NULL,
  `creator` varchar(32) DEFAULT NULL,
  `affected` varchar(32) DEFAULT NULL,
  `event` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(45) DEFAULT NULL,
  `session` varchar(42) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ds_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_permissions`;

CREATE TABLE `ds_permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `ds_permissions` WRITE;
/*!40000 ALTER TABLE `ds_permissions` DISABLE KEYS */;

INSERT INTO `ds_permissions` (`permission_id`, `name`, `description`)
VALUES
  (1,'ds_admin','Access to the administrator module'),
  (2,'ds_admin_logs','Access to system logs'),
  (3,'ds_admin_permission','Access to the permissions interface'),
  (4,'ds_admin_user','Access to user administration'),
  (5,'ds_test_sandbox','Access to the test script sandbox'),
  (6,'ds_task_scheduler','Access to the task scheduler');

/*!40000 ALTER TABLE `ds_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ds_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ds_users`;

CREATE TABLE `ds_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` char(60) DEFAULT NULL,
  `group` int(10) unsigned DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `administrator` tinyint(1) NOT NULL DEFAULT '0',
  `inactive_time` timestamp NULL DEFAULT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` varchar(32) DEFAULT NULL,
  `login_attempts` tinyint(2) NOT NULL DEFAULT '0',
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `last_login_success` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `group` (`group`),
  CONSTRAINT `ds_users_ibfk_1` FOREIGN KEY (`group`) REFERENCES `ds_group_meta` (`group_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Primary user data table';

LOCK TABLES `ds_users` WRITE;
/*!40000 ALTER TABLE `ds_users` DISABLE KEYS */;

INSERT INTO `ds_users` (`user_id`, `username`, `password`, `group`, `status`, `administrator`, `inactive_time`, `added`, `added_by`, `login_attempts`, `last_login_attempt`, `last_login_success`, `last_login_ip`)
VALUES
  (1,'root','$2y$10$6aHZ9PKvl5PdUXSEI2gUNey6hJJAJm5ZO/j/T5iTZiWxBMpAWcoVa',1,1,1,'0000-00-00 00:00:00',CURRENT_TIMESTAMP(),'SYSTEM',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','null');

/*!40000 ALTER TABLE `ds_users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
