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
## $Id: getSubscriptions.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

require_once("../config.php");
require_once("../classes/DataService.php");

if(!isset($_REQUEST['certificateId']) || $_REQUEST['certificateId'] == ''){
	exit("\nParameter not set");
}

//Get the certificate object
$certificate = DataService::singleton()->GetCertificate($_REQUEST['certificateId']);
//var_dump($certificate);

//get the subscriptions
$subscriptionsArray = DataService::singleton()->getAppSubscriptions($certificate->AppId);
//var_dump($subscriptionsArray);


echo json_encode($subscriptionsArray); 

?>