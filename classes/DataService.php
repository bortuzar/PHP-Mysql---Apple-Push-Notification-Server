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

            echo "<br/>Connected to database";
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
     * @return <bool>
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
             $sql = "UPDATE AppDevices AD SET DeviceActive = $active, AD.DateUpdated = '{$timestamp}' WHERE AD.DeviceId = $deviceId  AND AD.AppId = $appId";
           
        }

        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        return true;

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


    function destruct(){
        //close the DB connection
        $this->dbh = null;
    }
}
?>
