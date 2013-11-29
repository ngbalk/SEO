-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2013 at 05:52 AM
-- Server version: 5.5.32
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `seotest`
--
CREATE DATABASE IF NOT EXISTS `seotest` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `seotest`;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET latin1 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `raw_data_id` int(11) NOT NULL COMMENT 'data_id points to corresponding raw_data table',
  PRIMARY KEY (`id`),
  KEY `data_id` (`raw_data_id`),
  KEY `raw_data_id` (`raw_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `machine_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Table structure for table `raw_data`
--

CREATE TABLE IF NOT EXISTS `raw_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `data_type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scraper_data` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=236 ;

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE IF NOT EXISTS `relationships` (
  `raw_data_id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL COMMENT 'the parent_domain_id is the id of the domain that is being scraped, so all pages within that domain would have the same parent_domain_id',
  PRIMARY KEY (`raw_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT 'The parent_id is the id of the document/page/file from which this data comes from',
  `tag` varchar(255) NOT NULL,
  `parent_type` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6581 ;

-- --------------------------------------------------------

--
-- Table structure for table `webpages`
--

CREATE TABLE IF NOT EXISTS `webpages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_title` text CHARACTER SET latin1 NOT NULL,
  `doctype` varchar(255) CHARACTER SET latin1 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `raw_data_id` int(11) NOT NULL COMMENT 'data_id points to the corresponding raw_data table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `data_id` (`raw_data_id`),
  KEY `raw_data_id` (`raw_data_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=236 ;

-- --------------------------------------------------------

--
-- Table structure for table `web_host`
--

CREATE TABLE IF NOT EXISTS `web_host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_name` varchar(255) NOT NULL,
  `registrar` varchar(255) NOT NULL,
  `whois_server` varchar(255) NOT NULL,
  `referral_url` text NOT NULL,
  `name_server` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `updated_date` date NOT NULL,
  `creation_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `administrative_contact` text NOT NULL,
  `technical_contact` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE IF NOT EXISTS `words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT 'id of the document/page/file where these words came from',
  `word` varchar(255) NOT NULL,
  `frequency` int(11) DEFAULT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_type` varchar(255) NOT NULL COMMENT 'i.e. HTML, PDF, JS, CSS etc',
  PRIMARY KEY (`id`),
  KEY `page_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23007 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
