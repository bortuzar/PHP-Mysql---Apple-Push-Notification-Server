# -*- coding: utf-8 -*-
##
##     Copyright (c) 2010 Benjamin Ortuzar Seconde <bortuzar@gmail.com>
##
##     This file is part of APNS.
##
##     APNS is free software: you can redistribute it and/or modify
##     it under the terms of the GNU Lesser General Public License as
##     published by the Free Software Foundation, either version 3 of
##     the License, or (at your option) any later version.
##
##     APNS is distributed in the hope that it will be useful,
##     but WITHOUT ANY WARRANTY; without even the implied warranty of
##     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##     GNU General Public License for more details.
##
##     You should have received a copy of the GNU General Public License
##     along with APNS.  If not, see <http://www.gnu.org/licenses/>.
##
##
## $Id: APNS.sql 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##


-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 12, 2010 at 02:40 AM
-- Server version: 5.0.91
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `APNS`
--

-- --------------------------------------------------------

--
-- Table structure for table `AppDevices`
--

CREATE TABLE IF NOT EXISTS `AppDevices` (
  `AppDeviceId` int(11) NOT NULL auto_increment,
  `AppId` int(32) NOT NULL,
  `DeviceId` int(32) NOT NULL,
  `DeviceActive` tinyint(1) NOT NULL default '1',
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateUpdated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `LaunchCount` int(11) NOT NULL,
  PRIMARY KEY  (`AppDeviceId`),
  KEY `AppId` (`AppId`),
  KEY `DeviceId` (`DeviceId`),
  KEY `DeviceEnabled` (`DeviceActive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `AppDeviceSubscriptions`
--

CREATE TABLE IF NOT EXISTS `AppDeviceSubscriptions` (
  `AppDeviceSubscriptionId` int(11) NOT NULL auto_increment,
  `DeviceId` int(11) NOT NULL,
  `AppSubscriptionId` int(11) NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateUpdated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `SubscriptionEnabled` tinyint(1) NOT NULL,
  PRIMARY KEY  (`AppDeviceSubscriptionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AppDeviceSubscriptions`
--

INSERT INTO `AppDeviceSubscriptions` VALUES
(1, 8, 1, '2010-08-30 15:06:47', '2010-09-03 18:36:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Apps`
--

CREATE TABLE IF NOT EXISTS `Apps` (
  `AppId` int(32) NOT NULL auto_increment,
  `AppName` varchar(255) collate utf8_unicode_ci NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`AppId`),
  KEY `DateAdded` (`DateAdded`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Apps`
--

INSERT INTO `Apps` (`AppId`, `AppName`, `DateAdded`) VALUES
(1, 'Sample App', '0000-00-00 00:00:00');


-- --------------------------------------------------------

--
-- Table structure for table `AppSubscriptions`
--

CREATE TABLE IF NOT EXISTS `AppSubscriptions` (
  `AppSubscriptionId` int(11) NOT NULL auto_increment,
  `AppId` int(11) NOT NULL,
  `SubscriptionName` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`AppSubscriptionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AppSubscriptions`
--



-- --------------------------------------------------------

--
-- Table structure for table `Certificates`
--

CREATE TABLE IF NOT EXISTS `Certificates` (
  `CertificateId` int(11) NOT NULL auto_increment,
  `CertificateName` varchar(200) collate utf8_unicode_ci NOT NULL,
  `AppId` int(11) NOT NULL,
  `KeyCertFile` varchar(100) collate utf8_unicode_ci NOT NULL,
  `Passphrase` varchar(100) collate utf8_unicode_ci NOT NULL,
  `CertificateTypeId` int(11) NOT NULL,
  PRIMARY KEY  (`CertificateId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Certificates`
--

INSERT INTO `Certificates` (`CertificateId`, `CertificateName`, `AppId`, `KeyCertFile`, `Passphrase`, `CertificateTypeId`) VALUES
(1, 'Sample App - Development', 1, 'sample_ck_dev.pem', 'samplePassKey', 1);

-- --------------------------------------------------------

--
-- Table structure for table `CertificateServer`
--

CREATE TABLE IF NOT EXISTS `CertificateServer` (
  `CertificateServerId` int(11) NOT NULL auto_increment,
  `CertificateId` int(11) NOT NULL,
  `ServerId` int(11) NOT NULL,
  PRIMARY KEY  (`CertificateServerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `CertificateServer`
--

INSERT INTO `CertificateServer` VALUES
(1, 1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `CertificateTypes`
--

CREATE TABLE IF NOT EXISTS `CertificateTypes` (
  `CertificateTypeId` int(11) NOT NULL auto_increment,
  `CertificateTypeName` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`CertificateTypeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `CertificateTypes`
--

INSERT INTO `CertificateTypes` (`CertificateTypeId`, `CertificateTypeName`) VALUES
(1, 'Development Push SSL Certificate'),
(2, 'Production Push SSL Certificate'),
(3, 'Development Feedback SSL Certificate'),
(4, 'Production Feedback SSL Certificate');

-- --------------------------------------------------------

--
-- Table structure for table `Devices`
--

CREATE TABLE IF NOT EXISTS `Devices` (
  `DeviceId` int(32) NOT NULL auto_increment,
  `DeviceToken` varchar(71) collate utf8_unicode_ci NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `IsTestDevice` tinyint(1) NOT NULL,
  `DeviceNotes` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`DeviceId`),
  KEY `DeviceToken` (`DeviceToken`),
  KEY `DeviceToken_test` (`DeviceToken`,`IsTestDevice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `FeedDevices`
--

CREATE TABLE IF NOT EXISTS `FeedDevices` (
  `FeedDeviceId` int(11) NOT NULL auto_increment,
  `FeedId` int(11) NOT NULL,
  `AppDeviceId` int(11) NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateUpdated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `Enabled` tinyint(1) NOT NULL,
  PRIMARY KEY  (`FeedDeviceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `Feeds`
--

CREATE TABLE IF NOT EXISTS `Feeds` (
  `FeedId` int(11) NOT NULL auto_increment,
  `FeedName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `FeedUrl` varchar(500) collate utf8_unicode_ci NOT NULL,
  `DateLastChecked` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`FeedId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `MessageQueue`
--

CREATE TABLE IF NOT EXISTS `MessageQueue` (
  `MessageId` int(32) NOT NULL auto_increment,
  `CertificateId` int(32) NOT NULL,
  `DeviceId` int(32) NOT NULL,
  `Message` varchar(250) collate utf8_unicode_ci NOT NULL,
  `Badge` int(11) NOT NULL default '0',
  `Sound` varchar(100) collate utf8_unicode_ci NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `Status` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`MessageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Table structure for table `Servers`
--

CREATE TABLE IF NOT EXISTS `Servers` (
  `ServerId` int(11) NOT NULL auto_increment,
  `Server Name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `ServerUrl` varchar(300) collate utf8_unicode_ci NOT NULL,
  `ServerTypeId` int(11) NOT NULL,
  PRIMARY KEY  (`ServerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Servers`
--

INSERT INTO `Servers` (`ServerId`, `Server Name`, `ServerUrl`, `ServerTypeId`) VALUES
(1, 'Development Push Notitification Server', 'ssl://gateway.sandbox.push.apple.com:2195', 1),
(2, 'Production - Push Notification Server', 'ssl://gateway.push.apple.com:2195', 1),
(3, 'Development - Feedback Server', 'ssl://feedback.sandbox.push.apple.com:2196', 2),
(4, 'Production - Feedback Server', 'ssl://feedback.push.apple.com:2196', 2);
