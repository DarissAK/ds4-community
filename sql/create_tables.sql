SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
-- --------------------------------------------------------

--
-- Table structure for table `ds_log`
--

CREATE TABLE IF NOT EXISTS `ds_log` (
  `log_id` int(10) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_type` int(10) NOT NULL,
  `log_creator` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `log_affected` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `log_event` varchar(255) NOT NULL,
  `log_ip` varchar(45) NOT NULL,
  `log_session_id` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ds_perm`
--

CREATE TABLE IF NOT EXISTS `ds_perm` (
  `ds_group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ds_perm` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ds_perm_groups`
--

CREATE TABLE IF NOT EXISTS `ds_perm_groups` (
  `ds_perm_group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ds_perm_group_desc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ds_perm_groups`
--

INSERT INTO `ds_perm_groups` (`ds_perm_group`, `ds_perm_group_desc`) VALUES
('Default', 'Default Group');

--
-- Triggers `ds_perm_groups`
--
DELIMITER $$
CREATE TRIGGER `update_user` AFTER DELETE ON `ds_perm_groups`
 FOR EACH ROW DELETE FROM `ds_perm` WHERE `ds_perm`.`ds_group` = OLD.`ds_perm_group`
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ds_perm_meta`
--

CREATE TABLE IF NOT EXISTS `ds_perm_meta` (
  `ds_perm` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ds_perm_desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ds_perm_meta`
--

INSERT INTO `ds_perm_meta` (`ds_perm`, `ds_perm_desc`) VALUES
('ds_admin', 'Access to the administrator module'),
('ds_admin_logs', 'Access to system logs'),
('ds_admin_permission', 'Access to the permissions interface'),
('ds_admin_user', 'Access to user administration'),
('ds_test_sandbox', 'Access to the test script sandbox');

--
-- Triggers `ds_perm_meta`
--
DELIMITER $$
CREATE TRIGGER `perm_delete` AFTER DELETE ON `ds_perm_meta`
 FOR EACH ROW DELETE FROM ds_perm WHERE OLD.ds_perm = ds_perm
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ds_user`
--

CREATE TABLE IF NOT EXISTS `ds_user` (
  `ds_user` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Unique username',
  `ds_user_password` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'User''s password (hashed)',
  `ds_user_status` tinyint(1) NOT NULL DEFAULT '0',
  `ds_user_group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ds_user_inactive_timestamp` timestamp NULL DEFAULT NULL,
  `ds_user_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time the user was added',
  `ds_user_added_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'SYSTEM' COMMENT 'Username of who added the user to the system',
  `ds_user_login_attempts` tinyint(2) NOT NULL DEFAULT '0',
  `ds_user_last_login_attempt` timestamp NULL DEFAULT NULL,
  `ds_user_last_login_success` timestamp NULL DEFAULT NULL,
  `ds_user_last_login_ip` varchar(45) DEFAULT NULL COMMENT 'IP address where the user last logged in from',
  `ds_user_administrator` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the user has administrator rights'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Primary user data table';

--
-- Dumping data for table `ds_user`
--

INSERT INTO `ds_user` (`ds_user`, `ds_user_password`, `ds_user_status`, `ds_user_group`, `ds_user_inactive_timestamp`, `ds_user_added`, `ds_user_added_by`, `ds_user_login_attempts`, `ds_user_last_login_attempt`, `ds_user_last_login_success`, `ds_user_last_login_ip`, `ds_user_administrator`) VALUES
('root', '$2y$10$gzNe5YLn74qraklVT0s1DepQBRJjMUtTp6t3mSDggdzcZZoqJB4ye', 1, 'Default', NULL, CURRENT_TIMESTAMP, 'SYSTEM', 0, NULL, NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ds_log`
--
ALTER TABLE `ds_log`
  ADD PRIMARY KEY (`log_id`),
  ADD UNIQUE KEY `log_id` (`log_id`);

--
-- Indexes for table `ds_perm_groups`
--
ALTER TABLE `ds_perm_groups`
  ADD PRIMARY KEY (`ds_perm_group`);

--
-- Indexes for table `ds_perm_meta`
--
ALTER TABLE `ds_perm_meta`
  ADD PRIMARY KEY (`ds_perm`),
  ADD UNIQUE KEY `em_permission` (`ds_perm`);

--
-- Indexes for table `ds_user`
--
ALTER TABLE `ds_user`
  ADD PRIMARY KEY (`ds_user`),
  ADD UNIQUE KEY `ds_user_acct` (`ds_user`);

--
-- AUTO_INCREMENT for table `ds_log`
--
ALTER TABLE `ds_log`
  MODIFY `log_id` int(10) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
