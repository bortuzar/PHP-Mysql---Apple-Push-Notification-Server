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
## $Id: form.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

require_once("../config.php");
require_once("../classes/DataService.php");

if ($_POST['submit']) {

    echo "<br/>Started submitting messages to the queue";

	$certificate = DataService::singleton()->GetCertificate($_POST['certificateId']);
	
	//Get the devices associated to the app that are enabled
    $devicesArray = DataService::singleton()->GetDevices($certificate->AppId, 1);

	echo "<br/>Found ". count($devicesArray) ." Devices";
	
    //create a new message on the queue for each of them
    foreach ($devicesArray as $device) {
    
    	//if we are in test mode, only submit to test devices
    	if($_POST['onlyTestDevices'] == 1 && $device->IsTestDevice == 0){
    		continue;
    	}
		
		echo "<br/>Message submitted to queue for DeviceId: [{$device->DeviceId}] DeviceNotes: [{$device->DeviceNotes}]";
        DataService::singleton()->addMessage($_POST['certificateId'], $device->DeviceId, $_POST['message']);
    }

     echo "<br/>Completed submitting messages to the queue";
}
?>
<html>
    <head>
    	<link rel="stylesheet" href="css/style.css?v=1">
        <script type="text/javascript">
            <!--
            function confirmSubmit() {
                var answer = confirm("Are you sure you want to submit?")
                if (answer){
                    return true;
                }
                else{
                    return false;
                }
            }
            //-->
        </script>
    </head>
    <body>
        
        <form method="POST" action="" onsubmit="javascript:return confirmSubmit()">
        	<label for="message">Message:</label><br/>
            <textarea cols="20" rows="4" name="message" id="message"></textarea>

            <br/>
            <label for="certificateId">Certificate:</label><br/>
            <select name="certificateId">
            <?php
            //get all apps
            $certificatesArray = DataService::singleton()->getCertificates();

            foreach ($certificatesArray as $certificate) {

                echo "<option value='{$certificate->CertificateId}'>{$certificate->CertificateName}</option>";
            }
            ?>
            </select>

            <br/><br/>
            
            <label for="onlyTestDevices">Test Devices Only: </label><input type="checkbox" name="onlyTestDevices" id="onlyTestDevices" value="1" checked>
            <br/><br/>
            <input type="submit" name="submit" value="Submit to message queue">
        </form>
    </body>
</html>
