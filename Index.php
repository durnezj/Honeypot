<?php session_start();?>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>HoneyPotForum Group 5</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png"/>
</head>
<body>
<?php
require_once 'database.php';
require_once 'Config.php';
require_once 'showStuff.php';
require_once 'CheckUserInput.php';
require_once 'recaptchalib.php';

ShowHeader();
ShowKappa();

if (!isset($_SESSION["username"])) {
    //Gebruiker is NIET ingelogd
    showLoginregisterForm();
    if (isset($_GET["action"])) {
        $database = Gebruikers::getGebruikersInstantie(Config::getConfigInstantie()->getServer(),
            Config::getConfigInstantie()->getUsername(),
            Config::getConfigInstantie()->getPassword(),
            Config::getConfigInstantie()->getDatabase());

        switch (checkUserInput($_GET["action"])) {
            case "register":
                if (empty($_POST)) {
                    showRegisterform();
                } else {
                    if (isset($_POST["registerButton"]))// register button is clicked
                    {
                        if (CheckReCaptcha($_POST["g-recaptcha-response"])) {
                            $gebruikersnaam = checkUserInput($_POST["username"]);
                            $wachtwoord = $_POST["password"];
                            $errortabel = CheckConditions($gebruikersnaam, $wachtwoord);
                            if (empty($errortabel)/* && strlen($gebruikersnaam) < 20 && strlen($wachtwoord) < 20*/) {
                                if (!$database->bestaatGebruikerMetNaam($gebruikersnaam)) {
                                    $wachtwoord = password_hash($_POST["password"], PASSWORD_DEFAULT);

                                    $database->voegtoeGebruiker($gebruikersnaam, $wachtwoord, $_POST["email"], getUserIp());
                                } else {
                                    $errortabel[] = "A user with this name already exists, please choose another name";
                                }
                            }
                            if (!empty($errortabel)) {
                                toonErrors($errortabel);
                            } else {
                                echo '<h1>Your account has been successfully registered!</h1>';
                                echo '<h2>You can now login with the account you just created.</h2>';
                            }
                        } else {
                            echo '<h1>The captcha was invalid!</h1>
                                      <h2>Please try again!</h2>';
                            header("refresh:2;url=Index.php?action=register");
                        }
                    }
                }
                break;
            case "login":
                if (empty($_POST)) {
                    showLoginForm();
                } else {
                    if (isset($_POST["loginButton"])) {
                    //    if (CheckReCaptcha($_POST["g-recaptcha-response"])) {
                            $gebruikersnaam = checkUserInput($_POST["username"]);
                            $wachtwoord = $_POST["password"];
                            $errortabel = CheckConditions($gebruikersnaam, $wachtwoord);
                            if (empty($errortabel)) {
                                if ($database->bestaatGebruiker($gebruikersnaam, $wachtwoord)) {
                                    //Inloggen
                                    $_SESSION["username"] = $gebruikersnaam;
                                    //var_dump($_SESSION["gebruikersnaam"]);
                                    header("Location:Index.php");
                                } else {
                                    $errortabel[] = "the provided credentials are not correct";
                                }
                            }
                            if (!empty($errortabel)) {
                                toonErrors($errortabel);
                            }
                        } else {
                            echo '<h1>The captcha was invalid, please try again</h1>';
                            header('refresh:2;Location:index.php?action=login');
                        }
                    }
                //}
                break;
        }
        $database->sluitDB();
    }

} else {
    //Gebruiker is WEL ingelogd
    if (isset($_GET["action"])) {
        switch (checkUserInput($_GET["action"])) {
            case "logout":
                //var_dump($_SESSION['csrfToken']);
                //var_dump($_GET['token']);
                if(strcmp($_SESSION['csrfToken'],$_GET['token']) == 0 )//token is valid
                {
                    //Uitloggen
                    session_destroy();
                    header('Location:Index.php');
                }
                else
                {
                    echo '<h2>Token not valid, CSRF detected!</h2>';
                }
                break;
        }
    }
    else
    {
        RedirectToForum();
    }
}
?>

<script src='https://www.google.com/recaptcha/api.js'></script>
</body>
</html>

