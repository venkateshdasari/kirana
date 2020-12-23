<?php

class ModelAccountMailchimpAPI extends Model {
    private static $APIKEY = "5abb1839167f10fc03184a012edb8ffa-us3";
    // $listID = 'e4df506d7b' --- String
    // $email = test@gmail.com --- String
    // $mergeFields = array()
    
   	public function sendMember($listID,$email,$mergeFields) {
        //$listID = 'e4df506d7b';
        $memberID = md5(strtolower($email));
        $dataCenter = substr($APIKEY,strpos($APIKEY,'-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;
        
        // member information
        $json = json_encode([
            'email_address' => $email,
            'status'        => 'subscribed',
            'merge_fields'  => [
                'FNAME'     => $mergeFields['fname'],
                'LNAME'     => $mergeFields['lname'],
                'COUNTRY'   => $mergeFields['country']
            ]
        ]);
	}
    // Return httpCode Like 200 , 404 , 500
	public function sendPostData($url,$json) {
	    $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $APIKEY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		return $httpCode;
	}
}