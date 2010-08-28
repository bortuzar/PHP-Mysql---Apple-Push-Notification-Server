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
## $Id: registerDevice.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

require_once('config.php');
require_once('classes/DataService.php');
require_once('classes/Device.php');

if (!isset($_POST['deviceToken']) || $_POST['deviceToken'] == '') {

    echo $_POST['deviceToken'];
    exit("No deviceToken set");
}

if (!isset($_POST['appId']) || $_POST['appId'] == '') {
    echo $_POST['appId'];
    exit('Not appId set');
}


echo "Registering Device (if it does not exist already)";
DataService::singleton()->RegisterDevice($_POST['deviceToken']);

echo "Enabling Device for App: [{$_POST['appId']}] (if not enabled already)";
DataService::singleton()->setDeviceActive($_POST['deviceToken'], $_POST['appId'], 1)

?>