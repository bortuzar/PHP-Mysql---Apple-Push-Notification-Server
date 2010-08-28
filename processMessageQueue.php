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
## $Id: processMessageQueue.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##


require_once('config.php');
require_once('classes/MessageStatus.php');
require_once('classes/DataService.php');
require_once('classes/Apns.php');


echo "<br/>Started processing message queue";

//get the apps
$apps = DataService::singleton()->getApps();


foreach ($apps as $app) {

    //get N new messages from queue. We can get more messages on the next schedule
    $messagesArray = DataService::singleton()->getMessages($app->AppId, MessageStatus::UNPROCESSED, 1000);

    //if no messages for app continue with next
    if (count($messagesArray) == 0) {
        continue;
    }

    //connect to apple push notification server with the app credentials
    $certificatePath = $certificateFolder . '/' . $app->KeyCertFilePath;
    $apns = new apns($apnsPushServer, $certificatePath, $app->Passphrase);

    //send each message
    foreach ($messagesArray as $message) {

        //send payload to device
        $apns->sendMessage($message->DeviceToken, $message->Message, $message->Badge, $message->Sound);

        //mark as sent
        DataService::singleton()->setMessageStatus($message->MessageId, 2);
    }

    //execute the APNS desctructor so the connection is closed.
    unset($apns);
}
echo "<br/>Completed processing messages queue";


?>