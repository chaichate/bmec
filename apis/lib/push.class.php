<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Push{
    var $path = "../";
    public $FCMServerKey = "AAAAYAVXDu0:APA91bESF5vY27K6o6tTW1qY3Bv_u3urFCOlT5C07dbDvviWuTx1jDnUjGx1wxtSYcZzNOeztZ1mNr5niUwhkd06YK-JAg9HtxsI5qCbrIHoXxoNoX_cGwAqjEdYVgGU9-KF2XxKw8JTDXCk1fiuUYC31CbG1D8fjQ";
    public $certificatesPassword = "room2017";
    function __construct(){
            echo $this->path .'cer/pushcert.pem';
    }

    public function push2client($token , $data){
        if(!empty($token)) {
            if (strlen($token) == 152) { //use FCM for android
                $result = $this->FCM($token, $data);
            } else { // use APNS for ios
                $result = $this->APNS($token, $data);
                // $result = "";
            }
        }

        return $result;
    }

    public function FCM($target,$data){
        //FCM api URL
        $url = 'https://fcm.googleapis.com/fcm/send';
        //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $server_key = $this->FCMServerKey;

        $fields = array();
        $fields['data'] = $data;
        if(is_array($target)){
            $fields['registration_ids'] = $target;
        }else{
            $fields['to'] = $target;
        }
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function APNS($deviceToken,$data,$sound='received5.caf',$iosType='pro') {
        $pass = $this->certificatesPassword;
        $badge = 0;

        // Construct the notification payload
        $body = array();
        $body['aps'] = array(
            'alert' => $data,
            'sound' => 'default',
            'badge' => $badge,
        );

        if($iosType=='pro'){
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->path .'cer/pushcert.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            // for production change the server to ssl://gateway.push.apple.com:2195
            if (!$fp) {
                return "Failed to connect ".$err." ".$errstr;
            } else {
                $payload = json_encode($body);
                $msg  = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
                fwrite($fp, $msg);
                fclose($fp);
                return $fp;
            }
        }else if($iosType=='dev'){
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->path .'cer/pushcert_dev.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            // for production change the server to ssl://gateway.push.apple.com:2195
            if (!$fp) {
                return "Failed to connect ".$err." ".$errstr;
            } else {
                $payload = json_encode($body);
                $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
                fwrite($fp, $msg);
                fclose($fp);
                return $payload;
            }
        }
    }
}