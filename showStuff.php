<?php
session_start();
function ShowHeader()
{
    ?>
    <h1>Welcome to the Honeypot Forum of Group 5!</h1>
    <!--<h2>"designed from scratch in php"</h2>-->
    <?php
}

function ShowKappa()
{
    echo '<div id="kappa" ><img src="/images/kappaHack.png"></div>';
}

function showLoginregisterForm()
{
    ?>
    <nav>
        <ul>
            <li><a href="Index.php?action=register">Register for a free account</a></li>
            <li><a href="Index.php?action=login">Login</a></li>
            <li><a href="Forum.php">Start posting anonymously on the forum</a></li>
        </ul>
    </nav>
    <hr/>
    <img src="images/astronaut.png" id="poohbear">
    <?php
}

function showRegisterform()
{
    ?>
    <form action="Index.php?action=register" method=post>
        <h1>Register for a free account and start posting in minutes!</h1>

        <p>
            <label for=username>Username:</label>
            <input type=text name=username id=username>
        </p>

        <p>
            <label for=email>Email: </label>
            <input type=email name=email id=email>
        </p>

        <p>
            <label for=password>Wachtwoord</label>
            <input type=password name=password id=password>
        </p>
        <p>
            <div class="g-recaptcha" data-sitekey="6LckHxETAAAAAOS8qJnVXYAQG8hxKBbTt0P1Dawa" data-theme="dark"></div>
        </p>
        <p>
            <input type=submit name=registerButton value=Register>
        </p>
    </form>
    <hr/>
    <?php
}

function showLoginForm()
{
    ?>
    <form action="Index.php?action=login" method=post>
        <h1>Inloggen</h1>

        <div>
            <label for=username>Username: </label>
            <input type=text name=username id=username>
        </div>

        <div>
            <label for=password>Password: </label>
            <input type=password name=password id=password>
        </div>
        <!--<p>
            <div class="g-recaptcha" data-sitekey="6LckHxETAAAAAOS8qJnVXYAQG8hxKBbTt0P1Dawa" data-theme="dark"></div>
        </p>-->
        <div>
            <input type=submit name=loginButton value=Login>
        </div>
    </form>
    <hr/>
    <?php
}

function RedirectToForum()
{
    if (isset($_SESSION["username"])) {
        header("Location:Forum.php");
        echo "<h2>Welcome" . $_SESSION["username"] . " to our forum!</h2>";
    }
}


function toonErrors($errortabel)
{
    $resultaatstring = "Errors: ";
    foreach ($errortabel as $error) {
        $resultaatstring .= $error . "; ";
    }
    $resultaatstring .= "<hr />";
    echo $resultaatstring;
}

function getUserIp()
{
    // best attempt at getting real ip, as user can edit header
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

?>
