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
-- Generation Time: Aug 28, 2010 at 10:43 AM
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
  `AppId` int(32) NOT NULL,
  `DeviceId` int(32) NOT NULL,
  `DeviceActive` tinyint(1) NOT NULL default '1',
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateUpdated` timestamp NOT NULL default '0000-00-00 00:00:00',
  KEY `AppId` (`AppId`),
  KEY `DeviceId` (`DeviceId`),
  KEY `DeviceEnabled` (`DeviceActive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Apps`
--

CREATE TABLE IF NOT EXISTS `Apps` (
  `AppId` int(32) NOT NULL auto_increment,
  `AppName` varchar(255) collate utf8_unicode_ci NOT NULL,
  `KeyCertFilePath` varchar(100) collate utf8_unicode_ci NOT NULL,
  `Passphrase` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`AppId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- Table structure for table `MessageQueue`
--

CREATE TABLE IF NOT EXISTS `MessageQueue` (
  `MessageId` int(32) NOT NULL auto_increment,
  `AppId` int(32) NOT NULL,
  `DeviceId` int(32) NOT NULL,
  `Message` varchar(250) collate utf8_unicode_ci NOT NULL,
  `Badge` int(11) NOT NULL default '0',
  `Sound` varchar(100) collate utf8_unicode_ci NOT NULL,
  `DateAdded` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `Status` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`MessageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
