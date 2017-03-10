<?php

class Config
{
    private static $configInstantie = null;

    private $server;
    private $database;
    private $username;
    private $password;
    private $UploadMap;
    private $mimetypes;

    private function __construct()
    {
        $this->server = "localhost";
        $this->database = "honeypot";
        $this->username = "admin";
        //$this->password = "";

        $this->mimetypes = array('gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'png' => 'image/png');

        $this->UploadMap = "/Uploads";
    }

    public static function getConfigInstantie()
    {
        if (is_null(self::$configInstantie)) {
            self::$configInstantie = new Config();
        }
        return self::$configInstantie;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUploadMap()
    {
        return $this->UploadMap;
    }

    public function getMimetypes()
    {
        return $this->mimetypes;
    }
}

?>
