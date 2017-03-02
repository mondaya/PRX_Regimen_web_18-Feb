<?php 

class pushmessage{ 
     
     
     
    public $androidAuthKey      = ANDROID_APP_KEY; 
    public $iosApnsCert         = "ck.pem"; 
    
     /** 
     *  For Android FCM
    
     */  
    private function sendMessageAndroid($registration_id, $params) { 
        $this->androidAuthKey = ANDROID_APP_KEY;//Auth Key Herer 
         
        ## data is different from what your app is programmed 
       
        $data= array(
            'to' => $registration_id,
            'notification' => array(
                                'title' => $params["title"], 
                                'body' => $params["msg"]
                                )
                            );
        $headers = array( 
        "Content-Type:application/json",  
        "Authorization:key=".$this->androidAuthKey   
        ); 
         
         
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send"); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
        $result = curl_exec($ch); 
        //result sample {"multicast_id":6375780939476727795,"success":1,"failure":0,"canonical_ids":0,"results":[{"message_id":"0:1390531659626943%6cd617fcf9fd7ecd"}]} 
     
        curl_close($ch); 
        
        $rtn["code"]    = "000";//means result OK
        $rtn["msg"]     = "OK"; 
        $rtn["result"]  = $result;
        return $rtn; 
        
     } 
     
     
    /** 
     * For IOS APNS 
     * $params["msg"] : Expected Message For APNS 
     */ 
    private function sendMessageIos($registration_id, $params) { 
        
     $ssl_url                = 'ssl://gateway.push.apple.com:2195'; 
    // $ssl_url              = 'ssl://gateway.sandbox.push.apple.com:2195'; //For Test 
        
        $payload = array(); 
        $payload['aps'] = array('alert' => array("body"=>$params["msg"], "action-loc-key"=>"View"), 'badge' => 0, 'sound' => 'default'); 
        ## notice : alert, badge, sound 
    
        ## $payload['extra_info'] is different from what your app is programmed, this extra_info transfer to your IOS App
        $payload['extra_info'] = array('apns_msg' => $params["msg"]); 
        $push = json_encode($payload); 
         
        //Create stream context for Push Sever. 
        $streamContext = stream_context_create(); 
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->iosApnsCert); 

        $apns = stream_socket_client($ssl_url, $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext); 
        if (!$apns) { 

            $rtn["code"]    = "001";
            $rtn["msg"]     = "Failed to connect ".$error." ".$errorString; 
            return $rtn; 
        }  
         
        //echo 'error=' . $error; 
        $t_registration_id = str_replace('%20', '', $registration_id); 
        $t_registration_id = str_replace(' ', '', $t_registration_id); 
        $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $t_registration_id)) . chr(0) . chr(strlen($push)) . $push; 
         
        $writeResult = fwrite($apns, $apnsMessage); 
        fclose($apns); 
        
        $rtn["code"]    = "000";//means result OK
        $rtn["msg"]     = "OK"; 
        return $rtn; 

    }
     /**
     * Send message to SmartPhone
     * $params [pushtype, msg, registration_id]
     */
    public function sendMessage($params){
        
        //$parm = array("msg"=>$params["msg"]);
        if($params["registration_id"] && $params["msg"]){
            switch($params["pushtype"]){
                case "ios": 
                    $this->sendMessageIos($params["registration_id"], $params); 
                    break; 
                case "android": 
                   return $this->sendMessageAndroid($params["registration_id"], $params); 
                    break; 
            }
        }

    }
    
} 