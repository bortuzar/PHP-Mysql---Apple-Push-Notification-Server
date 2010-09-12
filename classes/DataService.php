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
## $Id: DataService.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

class DataService
{
    //database connection handler
    protected $dbh;
    
    // Hold an instance of the class
    private static $instance;

    function __construct(){

         //database connection details
        $dbHost = DBHOST;
        $dbName = DBNAME;
        $dbUser = DBUSERNAME;
        $dbPass = DBPASSWORD;

        try {

            $this->dbh = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);

            //echo "<br/>Connected to database";
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }

    }

    // The singleton method
    public static function singleton() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

    // Prevent users to clone the singleton instance
    public function __clone() {
        trigger_error('\nClone is not allowed.', E_USER_ERROR);
    }

    /**
     * Gets an array of apps
     * 
     * @return <array>
     */
    public function getApps() {

        $sql = "SELECT AppId, AppName FROM Apps";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $appsArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $appsArray;
    }
    
    /**
     * Gets an array of certificates of type
     * 
     * @return <array>
     */
    public function getCertificates() {

        $sql = "SELECT CertificateId, CertificateName, C.CertificateTypeId, KeyCertFile, Passphrase, AppId FROM Certificates C";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $certificatesArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $certificatesArray;
    }
    
    /**
     * Gets a certificate
     * 
     * @param <int> $certificateId
     * @return <object>
     */
    public function getCertificate($certificateId) {

        $sql = "SELECT CertificateId, CertificateName, C.CertificateTypeId, KeyCertFile, Passphrase, AppId FROM Certificates C WHERE CertificateId = %d LIMIT 1";
	 	$sql = sprintf($sql, (int)$certificateId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $certificate = $sth->fetch(PDO::FETCH_OBJ);

        return $certificate;
    }
    
     /**
     * Gets a certificate
     * 
     * @param <int> $certificateId
     * @return <object>
     */
    public function getCertificateServer($certificateId, $serverTypeId) {

        $sql = "SELECT ServerUrl FROM CertificateServer CS LEFT JOIN Servers S ON CS.ServerId = S.ServerId WHERE CertificateId = %d AND ServerTypeId = %d LIMIT 1";
	 	$sql = sprintf($sql, (int)$certificateId, (int)$serverTypeId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $server = $sth->fetch(PDO::FETCH_OBJ);

        return $server;
    }


    /**
     * Validates is a token pattern is valid
     * 
     * @param <string> $deviceToken
     * @return <bool>
     */
    public function isTokenValid($deviceToken){
        //TODO: add validation method
        return true;
    }

    /**
     * Gets an array of devices
     * 
     * @param <int> $appId
     * @param <bool> $status
     * @return <array>
     */
    public function getDevices($appId, $status) {
        
        $sql = "SELECT AD.DeviceId, IsTestDevice, DeviceNotes FROM AppDevices AD, Devices D WHERE AD.DeviceId = D.DeviceId AND AD.AppId = %d AND AD.DeviceActive = %d";
        $sql = sprintf($sql, (int)$appId, $status);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $devicesArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $devicesArray;
    }
    
    
     /**
     * Gets an array of certificates of type
     * 
     * @return <array>
     */
    public function getAppSubscriptions($appId) {

        $sql = "SELECT AppSubscriptionId, SubscriptionName FROM AppSubscriptions WHERE AppId = %d";
		$sql = sprintf($sql, (int)$appId);
		 
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $subscriptionsArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $subscriptionsArray;
    }
    
     /**
     * Gets an array of devices subscribed to a feature
     * 
     * @param <int> $appId
     * @param <int> $appFeatureId
     * @return <array>
     */
    public function getDevicesSubscribed($appSubscriptionId) {
        
        $sql = "SELECT D.DeviceId, D.DeviceNotes, IsTestDevice
				FROM AppDeviceSubscriptions ADS
				LEFT JOIN AppSubscriptions AppS ON ADS.AppSubscriptionId = AppS.AppSubscriptionId
				LEFT JOIN Devices D ON D.DeviceId = ADS.DeviceId
				LEFT JOIN AppDevices AD ON AD.DeviceID = ADS.DeviceID
				AND AD.AppId = AppS.AppId
				WHERE AppS.AppSubscriptionId = %d
				AND DeviceActive = 1
				AND ADS.SubscriptionEnabled =1";
				
        $sql = sprintf($sql, (int)$appSubscriptionId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $devicesArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $devicesArray;
    }

    /**
     * Checks if a device has been  registered
     * 
     * @param <string> $deviceToken
     * @return <int>
     */
     public function isDeviceRegistered($deviceToken) {

        $sql = "SELECT DeviceId FROM Devices WHERE DeviceToken = %s LIMIT 1";
        $sql = sprintf($sql, $this->dbh->quote($deviceToken));


        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $deviceId = 0;
        if ($sth->rowCount() > 0) {
            $app = $sth->fetch(PDO::FETCH_OBJ);
            $deviceId = $app->DeviceId;
        }

        return $deviceId;
    }

    /**
     * Registers a device if it doesnt exist. Returns false if the deviceToken is not valid.
     *
     * @param <string> $deviceToken
     * @return <bool>
     */
    public function registerDevice($deviceToken) {

        $isTokenValid = $this->isTokenValid($deviceToken);
        if(!$isTokenValid){
            return false;
        }

        //check if device already exists
        $deviceId = $this->isDeviceRegistered($deviceToken);
        if($deviceId > 0){
            //device already exists
            return true;
        }

        $sql = "INSERT INTO Devices (DeviceToken) VALUES (%s)";
        $sql = sprintf($sql, $this->dbh->quote($deviceToken));

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        return true;
        
    }
    
    /**
     * Sets the device status for an app
     *
     * @param <int> $deviceToekn
     * @param <int> $appId
     * @param <int> $active
     * @return <void>
     */
    public function setDeviceActive($deviceToken, $appId, $active) {

        $deviceId = $this->isDeviceRegistered($deviceToken);
        if($deviceId == 0){
            return false;
        }

        $sql = "SELECT DeviceId FROM AppDevices AD WHERE AD.DeviceId = %d AND AD.AppId = %d";
        $sql = sprintf($sql, (int) $deviceId, (int) $appId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        //get the current UTC/GMT time
        $timestamp = gmdate('Y-m-d H:i:s', time());

        if ($sth->rowCount() == 0) {
            $sql = "INSERT INTO AppDevices (AppId, DeviceID, DeviceActive, DateAdded, DateUpdated) Values ($appId, $deviceId, $active, '$timestamp', '$timestamp')";

        }else{
             $sql = "UPDATE AppDevices AD SET DeviceActive = $active, AD.DateUpdated = '{$timestamp}', LaunchCount = LaunchCount +1 WHERE AD.DeviceId = $deviceId  AND AD.AppId = $appId";
           
        }

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

    }
    
    
     /**
     * Updates a subscription for a device
     *
     * @param <int> $deviceToekn
     * @param <int> $appSubscriptionId
     * @param <int> $enable
     * @return <void>
     */
    public function updateAppSubscription($deviceToken, $appSubscriptionId, $enable) {

        $deviceId = $this->isDeviceRegistered($deviceToken);
        if($deviceId == 0){
            return false;
        }

        $sql = "SELECT DeviceId FROM AppDeviceSubscriptions ADS WHERE ADS.DeviceId = %d AND ADS.AppSubscriptionId = %d LIMIT 1";
        $sql = sprintf($sql, (int) $deviceId, (int) $appSubscriptionId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        //get the current UTC/GMT time
        $timestamp = gmdate('Y-m-d H:i:s', time());

        if ($sth->rowCount() == 0) {
            $sql = "INSERT INTO AppDeviceSubscriptions (DeviceID, AppSubscriptionId, DateAdded, DateUpdated, SubscriptionEnabled) Values ($deviceId, $appSubscriptionId, '$timestamp', '$timestamp', $enable)";

        }else{

             $sql = "UPDATE AppDeviceSubscriptions SET SubscriptionEnabled = $enable, DateUpdated = '{$timestamp}' WHERE DeviceId = $deviceId  AND AppSubscriptionId = $appSubscriptionId";
           
        }
		//echo $sql;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

    }


    /**
     * Sets a device Inactive.
     * 
     * @param <string> $deviceToken
     * @param <int> $appId
     * @param <int> $timestamp
     */
    public function setDeviceInactive($deviceToken, $appId, $timestamp){

        $sql = "UPDATE AppDevices SET DeviceActive = 0 WHERE AppId = %d AND UNIX_TIMESTAMP(DateUpdated) < %d AND DeviceId = (SELECT DeviceId FROM Devices WHERE DeviceToken = %s)";
        $sql = sprintf($sql, (int)$appId, (int)$timestamp, $this->dbh->quote($deviceToken));


        $sth = $this->dbh->prepare($sql);
        $sth->execute();

    }

    /**
     * Gets a list of messages
     * 
     * @param <int> $certificateId
     * @param <int> $statusId
     * @param <int> $limit
     * @return <array>
     */
    public function getMessages($certificateId, $statusId, $limit) {

        $sql = "SELECT DeviceToken, MessageId, Message, Badge, Sound FROM MessageQueue MQ, Devices D WHERE D.DeviceId = MQ.DeviceId AND CertificateId = %d  AND MQ.Status = %d LIMIT %d";
        $sql = sprintf($sql, (int) $certificateId, (int)$statusId, (int)$limit);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $messagesArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $messagesArray;
    }

    /**
     * Sets the status of a message in the Queue.
     * 
     * @param <int> $messageId
     * @param <int> $status
     */
    public function setMessageStatus($messageId, $status){

        $sql = "UPDATE MessageQueue SET Status = %d WHERE MessageId = %d";
        $sql = sprintf($sql, (int)$status, (int)$messageId);

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

    }

    /**
     * Adds a message to the Queue
     * 
     * @param <int> $certificateId
     * @param <int> $deviceId
     * @param <string> $message
     * @param <int> $badge
     * @param <string> $sound
     */
    public function addMessage($certificateId, $deviceId, $message, $badge = NULL, $sound = NULL){

        $timestamp = gmdate('Y-m-d H:i:s', time());
        
        $sql = "INSERT INTO MessageQueue (CertificateId, DeviceId, Message, Badge, Sound, DateAdded) VALUES (%d, %d, %s, %d, %s, %s)";
        $sql = sprintf($sql, (int)$certificateId, (int)$deviceId, $this->dbh->quote($message), (int)$badge, $this->dbh->quote($sound), $this->dbh->quote($timestamp));

        //echo '<br/>'. $sql;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
  
        
    }
    
     /**
     * Checks if feed exists.
     *
     * @param <string> $feedUrl
     * @return <int>
     */
    public function isFeedRegistered($feedUrl){
    
    	$sql = "SELECT FeedId FROM Feeds WHERE FeedUrl = %s LIMIT 1";
        $sql = sprintf($sql, $this->dbh->quote($feedUrl));

		echo $sql;
		
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $feedId = 0;
        if ($sth->rowCount() > 0) {
            $feed = $sth->fetch(PDO::FETCH_OBJ);
            $feedId = $feed->FeedId;
        }

        return $feedId;

    
    }
    
     /**
     * Registers a feed if it does not exist.
     *
     * @param <string> $feedUrl
     * @return <int>
     */
    public function registerFeed($feedUrl) {


        //check if device already exists
        $feedId = $this->isFeedRegistered($feedUrl);
        if($feedId > 0){
            //feed already exists
            return $feedId;
        }
        //get the current UTC/GMT time
        $timestamp = gmdate('Y-m-d H:i:s', time());

        $sql = "INSERT INTO Feeds (FeedUrl, DateLastChecked) VALUES (%s, %s)";
        $sql = sprintf($sql, $this->dbh->quote($feedUrl),  $this->dbh->quote($timestamp));

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        
        $lastInsertedId = $this->dbh->lastInsertId();

        return $lastInsertedId;
        
    }
    
    
     /**
     * Subscribes a device to a feed
     * 
     * @param <int> $appDeviceId
     * @param <int> $feedId
     * @param <int> $enable
     * @return <void>
     */
    public function subscribeDeviceToFeed($deviceToken, $appId, $feedId, $enable){
    	
        $deviceId = $this->isDeviceRegistered($deviceToken);
        if($deviceId == 0){
            return false;
        }    	
    	//find the appDeviceId
    	$sql = "SELECT AppDeviceId FROM AppDevices AD WHERE AD.DeviceId = %d AND AD.AppId = %d LIMIT 1";
        $sql = sprintf($sql, (int) $deviceId, (int) $appId);

		echo $sql;

        $sth = $this->dbh->prepare($sql); 
        $sth->execute();       
        $appDevice = $sth->fetch(PDO::FETCH_OBJ);
		var_dump($appDevice);
		
		//check if its already subscribed.
		$sql = "SELECT FeedDeviceId FROM FeedDevices WHERE FeedId = %d AND AppDeviceId = %d LIMIT 1";
		$sql = sprintf($sql, $feedId, $appDevice->AppDeviceId);
		
		echo $sql;
		
		$sth = $this->dbh->prepare($sql);  
		$sth->execute();   
        //get the current UTC/GMT time
        $timestamp = gmdate('Y-m-d H:i:s', time());

        if ($sth->rowCount() == 0) {
            $sql = "INSERT INTO FeedDevices (FeedId, AppDeviceId, DateAdded, DateUpdated, Enabled) Values (%d, %d, %s, %s, %d)";
			$sql = sprintf($sql, $feedId, $appDevice->AppDeviceId,   $this->dbh->quote($timestamp),$this->dbh->quote($timestamp), $enable);

        }else{

             $sql = "UPDATE FeedDevices SET Enabled = %d, DateUpdated = %s WHERE AppDeviceId = %d  AND FeedId = %d";
             $sql = sprintf($sql, $enable, $this->dbh->quote($timestamp), $appDevice->AppDeviceId, $feedId);
           
        }
		echo $sql;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
    }
    
    
     /**
     * Gets a list of feeds in random order
     * 
     * @return <array>
     */
    public function getFeeds() {

        $sql = "SELECT FeedId, FeedName, FeedUrl, DateLastUpdated, DateLastChecked FROM Feeds ORDER BY RAND()";
        
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $feedsArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $feedsArray;
    }
    

    
    
     /**
     * Updates the DateLastChecked for a feed
     * 
     * @param <int> $feedId
     * @return <void>
     */
    public function updateFeedDateLastChecked($feedId){
    
    	 //get the current UTC/GMT time
        $timestamp = gmdate('Y-m-d H:i:s', time());
    	$sql = "UPDATE Feeds SET DateLastChecked = %s WHERE FeedId = %d";
    	$sql = sprintf($sql, $this->dbh->quote($timestamp),  $feedId);
 
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
    
    }
    
    
      /**
     * Updates the DateLastChecked for a feed
     * 
     * @param <int> $feedId
     * @return <void>
     */
    public function updateFeedDateLastUpdated($feedId, $dateTime){
    
    	$sql = "UPDATE Feeds SET DateLastUpdated = %s WHERE FeedId = %d";
    	$sql = sprintf($sql, $this->dbh->quote($dateTime),  $feedId);
 		echo "<br/>". $sql;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
    
    }

    
    
     /**
     * Updates the DateLastChecked for a feed
     * 
     * @param <int> $feedId
     * @return <array>
     */
    public function getFeedDevices($feedId){
    
    	$sql = "SELECT D.DeviceId, D.IsTestDevice, D.DeviceNotes, C.CertificateId
				FROM  Feeds F
				LEFT JOIN FeedDevices FD ON F.FeedId = FD.FeedId
				LEFT JOIN AppDevices AD ON AD.AppDeviceId = FD.AppDeviceId
				LEFT JOIN Devices D ON AD.DeviceId = D.DeviceId
				LEFT JOIN Certificates C ON AD.AppId = C.AppId
				WHERE FD.Enabled = 1
				AND AD.DeviceActive =1
				AND C.CertificateTypeId =1
				AND F.FeedId = %d";
				
				
		$sql = sprintf($sql, $feedId);
 		//echo $sql;
        
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $devicesArray = $sth->fetchAll(PDO::FETCH_OBJ);

        return $devicesArray;		
    }


    function destruct(){
        //close the DB connection
        $this->dbh = null;
    }
}
?>
