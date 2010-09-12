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

if (!isset($_REQUEST['deviceToken']) || $_REQUEST['deviceToken'] == '') {

    echo $_REQUEST['deviceToken'];
    exit("No deviceToken set");
}

if (!isset($_REQUEST['appId']) || $_REQUEST['appId'] == '') {
    echo $_REQUEST['appId'];
    exit('Not appId set');
}


echo "<br/>Registering Device (if it does not exist already)";
DataService::singleton()->RegisterDevice($_REQUEST['deviceToken']);

echo "<br/>Enabling Device for App: [{$_REQUEST['appId']}] (if not enabled already)";
DataService::singleton()->setDeviceActive($_REQUEST['deviceToken'], $_REQUEST['appId'], 1);

//optional
if(isset($_REQUEST['appSubscriptionId']))
{
	echo "<br/>Registering for Subscription". $_REQUEST['appSubscriptionId'];
	DataService::singleton()->updateAppSubscription($_REQUEST['deviceToken'], $_REQUEST['appSubscriptionId'], 1);
}

if(isset($_REQUEST['feedUrl'])){
	echo "\n<br/>Register Feed: {$_REQUEST['feedUrl']} ";
	$feedId = DataService::singleton()->registerFeed($_REQUEST['feedUrl']);
	echo "\n<br/>FeedId: {$feedId}";
	DataService::singleton()->subscribeDeviceToFeed($_REQUEST['deviceToken'], $_REQUEST['appId'], $feedId, $_REQUEST['feedEnable']);
}
?>