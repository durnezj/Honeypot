<?php
session_start();
$token = base64_encode(openssl_random_pseudo_bytes(32));
$_SESSION['csrfToken'] = $token;

require_once 'Thread.php';
require_once 'Config.php';
require_once 'database.php';
require_once 'recaptchalib.php';
require_once 'CheckUserInput.php';
?>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>HoneyPotForum Group 5</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png"/>
</head>
<body>
<?php

$database = Gebruikers::getGebruikersInstantie(Config::getConfigInstantie()->getServer(),
    Config::getConfigInstantie()->getUsername(),
    Config::getConfigInstantie()->getPassword(),
    Config::getConfigInstantie()->getDatabase());

if (isset($_SESSION["username"])) {
    echo '<h1>Welcome ' . $_SESSION["username"] . '!</h1>';
} else
    echo '<h1>Welcome Anonymous poster!</h1><hr/>';

//ShowKappa();

if (isset($_GET["replyTo"])) {
    if(is_numeric($_GET["replyTo"]))
        $number = $_GET["replyTo"];
     else
        $number = 99;//post reply
    //var_dump($number);
    ?>
    <form action="Forum.php" method=post>
        <h1>Reply to thread: </h1>

        <p>
            <input type="hidden" name="number" value=<?php echo $number ?>>
            <label for=reply>Reply: </label>
            <textarea name="reply" id="reply"></textarea>
        </p>

        <p>
        <div class="g-recaptcha" data-sitekey="6LckHxETAAAAAOS8qJnVXYAQG8hxKBbTt0P1Dawa" data-theme="dark"></div>
        </p>
            <!-- These are not the vulnerabilities you're looking for
            Ben "the l33t h@cker" Kenobi , 1977-->
        </p>
        <p>
            <input type=submit name=replyButton value=Reply>
        </p>
    </form>
    <hr/>
    <?php
}

if (isset($_GET["newthread"])) { //post new thread
    ?>
    <form action="Forum.php" method="POST" enctype="multipart/form-data">
        <h1>create new thread:</h1>

        <p>
            <label for=reply>Title: </label>
            <input type="text" name="title" id="title">
        </p>

        <p>
            <label for=reply>Message: </label>
            <textarea name="message" id="message"></textarea>
        </p>

        <p>
        <div class="g-recaptcha" data-sitekey="6LckHxETAAAAAOS8qJnVXYAQG8hxKBbTt0P1Dawa" data-theme="dark"></div>
        </p>

        <p>
            <!--<input type="hidden" name="MAX_FILE_SIZE" value="1000000">-->
            <label for="photo">Choose a picture to upload</label>
            <input type="file" name="photo" id="photo" value="">
        </p>

        <p>
            <input type="submit" name="postthreadbutton" value="Post thread">
        </p>
    </form>
    <hr/>
    <?php
}

if (isset($_POST["replyButton"])) {//post request to post reply
    if (CheckReCaptcha($_POST["g-recaptcha-response"])) {
        $nr = $_POST["number"];
        $str = $_POST["reply"];
        if (isset($_SESSION["username"]))
            $usr = $_SESSION["username"];
        else
            $usr = "anonymous";

        $database->postReply($nr, $str, $usr);
        echo '<h2>Your reply has been posted!</h2>
              <h3>returning to forum automatically...</h3>';
        header("refresh:2;url=Forum.php");
        exit();
    } else {
        echo '<h1>Invalid captcha, don\'t try to spam please, it\'s just a click of a button :)</h1>';
    }
}

if (isset($_POST["postthreadbutton"])) {//POST request for new thread

	if (CheckReCaptcha($_POST["g-recaptcha-response"])) {
        echo '<h1>files array</h1>';
		var_dump($_FILES);

		$new = sha1_file($_FILES['name']);

		echo '<h1>new filename</h1>';
        //echo '<h1>files array</h1>';
		//var_dump($_FILES);
		//print_r($_FILES);
		//var_dump($_FILES);
		//var_dump($_POST);
		$hashed = $_FILES["photo"]["name"];
		$hashed = md5($hashed);
		$hashed = $hashed . ".jpg";
		
		//echo $hashed;

		//echo '<h1>new filename</h1>';	

        //echo $hashed;

        if(move_uploaded_file($_FILES["photo"]["tmp_name"], 'C:/website/Uploads/'.$hashed))
		{
			$database->createThread($_POST["title"], $_POST["message"],(isset($_SESSION["username"]) ? $_SESSION["username"] : "anon"), $hashed);
		}

		echo '<h2>Your thread has been posted!</h2>
		<h3>returning to forum automatically...</h3>';
		header( "refresh:2;url=Forum.php" );
		exit();
			       
    }
	else 
	{
        echo '<h1>Invalid captcha, don\'t try to spam please, it\'s just a click of a button :)</h1>';
    }

}

makeForum();

function makeForum()
{
    $database = Gebruikers::getGebruikersInstantie(Config::getConfigInstantie()->getServer(),
        Config::getConfigInstantie()->getUsername(),
        Config::getConfigInstantie()->getPassword(),
        Config::getConfigInstantie()->getDatabase());

    $forumTable = $database->getThreads();

    $forum = "<table id=\"forum\">";

    for ($threadNr = 0; $threadNr < sizeof($forumTable); $threadNr++) {


        $replies = $database->getReplies($threadNr + 1);

        $forum .= "<tr><th> by user: &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<b>"
            . $forumTable[$threadNr]->username
            . "</b></br> on: &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp "
            . $forumTable[$threadNr]->postdate
            . "</hr><a href=\"Forum.php?replyTo=" . $forumTable[$threadNr]->thread_id . "\">Reply to this thread</a></hr>
            </br>message: &nbsp &nbsp &nbsp &nbsp"
            . $forumTable[$threadNr]->message
            . "<img src=\"" . Config::getConfigInstantie()->getUploadMap() . "/" . $forumTable[$threadNr]->image . "\"  "
            . " width=\"100\" style=\"float: right\" class=\"photo\"/></th></tr><tr>";


        if (!empty($replies)) {
            for ($replyNr = 0; $replyNr < sizeof($replies); $replyNr++) {
                $forum .= "<tr><td>";
                $forum .= "user: &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<b>" . $replies[$replyNr]->username
                    . "</b></hr></br><p>reply message: &nbsp &nbsp" . $replies[$replyNr]->reply .
                    "</p></hr></br>on: &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $replies[$replyNr]->postdate;
                $forum .= "</tr></td>";
            }
        }


    }

    $forum .= "</tr></table>";

    echo $forum;
}

?>


<script src='https://www.google.com/recaptcha/api.js'></script>
<!--<iframe width="1" height="1" src="https://www.youtube.com/embed/EjMNNpIksaI?autoplay=1" frameborder="0" allowfullscreen></iframe>-->
</body>
</html>

