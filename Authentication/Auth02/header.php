<?php
require 'classes/user.php';

if (isset($_COOKIE['auth'])) {
    $user = User::getuserfromcookie($_COOKIE['auth']);
}
?>
<html>
<head>
    <title><?php echo $site; ?></title>
    <link rel="stylesheet" media="screen" href="/css/bootstrap.css" />
    <link rel="stylesheet" media="screen" href="/css/pentesterlab.css" />
    <script src="https://pentesterlab.com/tracking/authe_02.js"></script>
</head>
<body>
<div class="container-narrow">
    <div class="header">
        <div class="navbar navbar-default navbar-fixed-top">
            <ul class="nav navbar-nav navbar-right">
                <?php if (!isset($user)) { ?>
                    <li><a href="/login.php">Login</a></li>
                    <li><a href="/register.php">Register</a></li>
                <?php } else { ?>
                    <li><a href="/logout.php">Logout</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>