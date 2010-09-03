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
## $Id: subscriptionForm.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

require_once("../config.php");
require_once("../classes/DataService.php");

if ($_POST['submit']) {
	
    echo "<br/>Started submitting messages to the queue";

	$certificate = DataService::singleton()->GetCertificate($_POST['certificateId']);
	
	if(isset($_POST['subscriptionId']) && $_POST['subscriptionId'] > 0){
		//get the apps associated to the specific subscription
		$devicesArray = DataService::singleton()->getDevicesSubscribed($_POST['subscriptionId']);
	}else{
		//get the devices associated to the app that are enabled
    	$devicesArray = DataService::singleton()->GetDevices($certificate->AppId, 1);
	}
	
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
<!doctype html>
<html>
    <head>
    	<meta charset="utf-8">
    	<title>Sample Push Notification Form</title>
    	<link rel="stylesheet" href="css/style.css?v=1">
    </head>
    <body>
        
        <div id="container">
	        
	        <header>
			
			</header>
			
	        <div id="main">
	        
		        <form method="POST" action="" onsubmit="javascript:return confirmSubmit()">
		        	<fieldset>
    				<legend>Push Notifications</legend>
		        	
		        	<label for="message">Message:</label><br/>
		            <textarea cols="20" rows="4" name="message" id="message"></textarea>
		
					<br/>
					<label for="certificateId">Certificate:</label><br/>
		            <select name="certificateId" id="certificateId">
		            <?php
		            //get all apps
		            $certificatesArray = DataService::singleton()->getCertificates();
		
		            foreach ($certificatesArray as $certificate) {
		
		                echo "<option value='{$certificate->CertificateId}'>{$certificate->CertificateName}</option>";
		            }
		            ?>
		            </select>
			
		
		            <br/>
		            
		            <!--! subscriptions container-->
		            <div id="subscriptions">
		            	<label for="subscriptionId">Subscription:</label><br/>
		            	<select name="subscriptionId" id="subscriptionId">
						</select>
		            </div>
		
		            <br/>
		            
		            <label for="onlyTestDevices">Test Devices Only: </label>
		            <input type="checkbox" name="onlyTestDevices" id="onlyTestDevices" value="1" checked="checked">
		            <br/><br/>
		            
		            <input type="submit" name="submit" value="Submit to message queue">
		            
		            </fieldset>
		        </form>
		        
	        </div><!--! end of #main -->
	        	
	       	<footer>
			
	  		</footer>
	        
        </div><!--! end of #container -->

        <!-- Javascript at the bottom for fast page loading -->
        <!-- Grab Google CDN's jQuery. -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<!-- Grab Google CDN's jQuery. -->
		<script src="js/script.js"></script>
		<script>
			$(document).ready(function(){
			
				//Populate the second selectbox				
				var certificateId = $("#certificateId");
				populateSubscriptions(certificateId);
				
				// bing the an event handler to the js change event.
				$("#certificateId").change(certificateOnSelectChange);
				
				
	
			});
		</script>

		
    </body>
</html>
