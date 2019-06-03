<?php

namespace PHPSkyCore\Response;

class Response
{

    public static function json($data = array(), $status_message = null, $status = null)
    {
        header("Content-Type: application/json; charset=utf-8");
        header("HTTP/1.1 " . $status);
        
        $response['status_message'] = $status_message;
        $response['data' ] = $data;
        $response['status'] = $status;

        $json_response = json_encode($response);
        echo $json_response;
    }
}