<?php

function login(){
    $options = array(
        CURLOPT_URL => 'https://axitraxi.samrental.nl/api/webshop/v1/token',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => '{"client_id":"webshop","client_secret":"d8La9mI7lrZeEIxYUx9ZMwqvUm8nIsRS","grant_type":"client_credentials"}',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json'
        )
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response, true);
    
    if(isset($response['access_token'])) {
        return $response['access_token'];
    }

    return null;
}

function callApi($url, $data = NULL, $method = "POST") {

    $token = login();

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_ENCODING => '',
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer $token"
        )
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    curl_close($curl);

    return $response;

}
