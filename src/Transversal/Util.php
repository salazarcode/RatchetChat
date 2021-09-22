<?php

namespace RatchetChat\Transversal;

class Util
{
    public static function Result($success, $message = "", $data = null)
    {
        $response =  [
            "success" => $success,
        ];

        if($message != "")
        {
            $response[] = $message;
        }

        if($data != null)
        {
            $response[] = $data;
        }
        return json_encode($response);
    }    

    public static function Ok($data = null)
    {
        return Util::Result(true, "", $data);
    }

    public static function Error(\Exception $error)
    {
        return Util::Result(false, $error->getMessage(), $error);
    }
}

