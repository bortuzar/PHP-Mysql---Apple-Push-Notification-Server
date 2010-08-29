<?php
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
## $Id: config.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

//set_time_limit(10);

//SQL Connection Parameters
define('DBHOST', 'localhost');
define('DBNAME', '');
define('DBUSERNAME', '');
define('DBPASSWORD', '');

//Certificate folder
$certificateFolder = 'certificates';

//Push and Feedback servers
//These urls are stored in mySQL in the CertificateTypes table.

//Date settings. Apple uses UTC dates for Feedback info
date_default_timezone_set('UTC');
?>