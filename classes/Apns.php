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
## $Id: Apns.php 168 2010-08-28 01:24:04Z Benjamin Ortuzar Seconde $
##

/**
 * Apple Push Notification Server
 */
class Apns
{
	protected $server;
	protected $keyCertFilePath;
	protected $passphrase;
	protected $stream;

        
        /**
         * Connects to the APNS server with a certificate and a passphrase
         *
         * @param <string> $server
         * @param <string> $keyCertFilePath
         * @param <string> $passphrase
         */
	function __construct($server, $keyCertFilePath ,$passphrase){
		
		$this->server = $server;
		$this->keyCertFilePath = $keyCertFilePath;
		$this->passphrase = $passphrase;
		
		$this->connect();
	
	}

        
	/**
         * Connects to the server with the certificate and passphrase
         *
         * @return <void>
         */
	private function connect(){
	
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $this->keyCertFilePath);
		// assume the private key passphase was removed.
		stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

		$this->stream = stream_socket_client($this->server, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		if (!$this->stream) {
			//print "<br/>Failed to connect $err $errstrn";
			throw new Exception("<br/>Failed to connect $err $errstrn");
		}
		else {
			print "<br/>Opening connection to: {$this->server}";
		}

	}

        
        /**
         * Sends a message to device
         * 
         * @param <string> $deviceToken
         * @param <string> $message
         * @param <int> $badge
         * @param <string> $sound
         */
        public function sendMessage($deviceToken, $message, $badge = NULL, $sound = NULL){

            //generate the payload
            $payload = $this->generatePayload($message, $badge, $sound);

            //send payload to the device.
            $this->sendPayload($deviceToken, $payload);
            
            
        }

        
        /**
         * Generates the payload
         * 
         * @param <string> $message
         * @param <int> $badge
         * @param <string> $sound
         * @return <string>
         */
        protected function generatePayload($message, $badge = NULL, $sound = NULL) {

            $body = array();

             //message
            $body['aps'] = array('alert' => $message);

             //badge
             if ($badge) {
                 $body['aps']['badge'] = $badge;
             }

              //sound
             if ($sound) {
                  $body['aps']['sound'] = $sound;
            }

            $payload = json_encode($body);

            return $payload;
        }


        /**
         * Writes the contents of payload to the file stream
         * 
         * @param <string> $deviceToken
         * @param <string> $payload
         */
	protected function sendPayload($deviceToken, $payload){

		$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		//print "<br/>Sending message :". $msg;
		fwrite($this->stream, $msg);		
	
	}


	/**
         * Gets an array of feedback tokens
         *
         * @return <array>
         */
	public function getFeedbackTokens() {
	    
	    $feedback_tokens = array();
	    //and read the data on the connection:
	    while(!feof($this->stream)) {
	        $data = fread($this->stream, 38);
	        if(strlen($data)) {	   
	        	//echo $data;     	
	            $feedback_tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
	        }
	    }
	   
	    return $feedback_tokens;
	}


	/**
         * Closes the stream
         */
	function __destruct(){
            print "<br/>Clossing connection to: {$this->server}";
            fclose($this->stream);
	}

}//end of class
?>

