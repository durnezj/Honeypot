<?php
require_once 'showStuff.php';

class Gebruikers
{
    private static $gebruikersInstantie = null;

    private $dbh;

    private function __construct($server, $username, $password, $database)
    {
        try {
            $this->dbh = new PDO("mysql:host=$server; dbname=$database", $username, $password);
            //Bij error: exception opwerpen
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getGebruikersInstantie($server, $username, $password, $database)
    {
        if (is_null(self::$gebruikersInstantie)) {
            self::$gebruikersInstantie = new Gebruikers($server, $username, $password, $database);
        }
        return self::$gebruikersInstantie;
    }

    public function sluitDB()
    {
        $dbh = null;
    }

    public function bestaatGebruikerMetNaam($gebruikersnaam)
    {
        try {
            $sql = "SELECT * FROM db_userdata
                        WHERE user_name = :gebruikersnaam";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(":gebruikersnaam", $gebruikersnaam);
            $stmt->execute();
            $werknemer = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        $bestaat = (empty($werknemer) ? false : true);

        return $bestaat;
    }

    public function bestaatGebruiker($gebruikersnaam, $wachtwoord)
    {
        try {
            $sql = "SELECT * FROM db_userdata
                        WHERE user_name = :gebruikersnaam";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(":gebruikersnaam", $gebruikersnaam);
            $stmt->execute();
            $werknemer = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        $bestaat = false;
        if (!empty($werknemer) && password_verify($wachtwoord, $werknemer->user_pw)) {
            $bestaat = true;
        }

        return $bestaat;
    }

    public function voegtoeGebruiker($gebruikersnaam, $wachtwoord, $email, $ip)
    {
        try {
            $sql = "INSERT INTO db_userdata(user_id,user_name, user_pw,
 											user_email, user_ip)
					VALUES(NULL, :gebruikersnaam, :wachtwoord, :email, :ip)";

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindParam(":gebruikersnaam", $gebruikersnaam);
            $stmt->bindParam(":wachtwoord", $wachtwoord);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":ip", $ip);

            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getThreads()
    {
        $sql = "SELECT * FROM threads";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $threadObj = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $threadObj;
    }

    public function getReplies($threadNr)
    {
        $sql = "SELECT * FROM replies
				WHERE parent_id = :threadNr";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(":threadNr", $threadNr);
        $stmt->execute();
        $threadObj = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $threadObj;
    }

    public function postReply($threadNr, $replystring, $username)
    {
        try {
            $ip = getUserIp();
            $time = date('Y-m-d H:i:s');
            $name = checkUserInput($username);
            $reply = checkUserInput($replystring);
            $sql = "INSERT INTO replies(parent_id,reply_id,reply,username,postdate,ip)
					VALUES(:threadnr, NULL,:replystring,:username,:datum, :adres)";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(":threadnr", $threadNr);
            $stmt->bindParam(":replystring", $reply);
            $stmt->bindParam(":username", $name);
            $stmt->bindParam(":datum", $time);
            $stmt->bindParam(":adres", $ip);
            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }

    }

    public function createThread($title, $msg, $username, $image)
    {
        try {
            $ip = getUserIp();
            $message = checkUserInput($msg);
            $name = checkUserInput($username);
            $filename = checkUserInput($image);
            $time = date('Y-m-d H:i:s');
            $sql = "INSERT INTO threads(thread_id, title, message, username, postdate, ip, image)
                    VALUES(NULL,:title,:message,:username,:datum,:ip,:image)";
            $stmt = $this->dbh->prepare($sql);

            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":message", $message);
            $stmt->bindParam(":username", $name);
            $stmt->bindParam(":datum", $time);
            $stmt->bindParam(":ip", $ip);
            $stmt->bindParam(":image", $filename);

            $stmt->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }


}

?>
