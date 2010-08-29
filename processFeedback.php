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
## $Id: processFeedback.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

require_once('config.php');
require_once('classes/DataService.php');
require_once('classes/Apns.php');

echo "<br/>Started processing Feedback";

//get the certificates
$certificates = DataService::singleton()->getCertificates();

foreach ($certificates as $certificate) {

	//only process apps that have a certificate associated to it.
	if($certificate->KeyCertFile == ''){
	
		echo "<br/>Certfile not set for App: [{$certificate->CertificateName}]";
		continue;
	}
	//var_dump($certificate);
    //connect to feedback server
    $certificatePath = $certificateFolder . '/' . $certificate->KeyCertFile;
    
    $server = DataService::singleton()->getCertificateServer($certificate->CertificateId, 2);
    $apns = new apns($server->ServerUrl, $certificatePath, $certificate->Passphrase);

    //get tokens
    $feedbackTokens = $apns->getFeedbackTokens();

    //close connection
    unset($apns);

    //print the number of tokens to check for
    $countTotal = count($feedbackTokens);
    echo "<br/>There are [{$countTotal}] tokens notified by feedback";

    //loop trough the tokens
    foreach ($feedbackTokens as $feedbackToken) {

        //only DeActivate devices that where updated before they where removed. Otherwise the user could of installed the app again.
        DataService::singleton()->setDeviceInactive($feedbackToken['devtoken'], $app->AppId, $feedbackToken['timestamp']);
    }
}
echo "<br/>Completed processing Feedback";
?>