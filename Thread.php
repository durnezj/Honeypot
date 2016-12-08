       <?php session_start()?>
        <html>
        <head lang="en">
            <meta charset="UTF-8">
            <title>HoneyPotForum Group 5</title>
            <link rel="stylesheet" type="text/css" href="style.css">
        </head>
        <body>
        <nav>
            <ul>
                <li><a href="Index.php?action=logout&token=<?php echo($_SESSION['csrfToken']); ?>">logout</a></li>
                <li><a href="Forum.php?newthread">create a new thread in this forum</a></li>
            </ul>
        </nav>
        <hr>