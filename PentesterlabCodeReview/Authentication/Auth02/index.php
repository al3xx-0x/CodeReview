<?php
$site = "Pentesterlab &raquo; Authentication 02";
require "header.php";
?>

<div class="row">
    <div class="col-lg-12">
        <h1>Authentication 02</h1>
        <p>The objective of this exercise is to find a way to get logged in as the user "admin"...</p>

        <?php if (isset($user)) { ?>
            <?php if ($user === 'admin') { ?>
                <span class="text text-success">
                    You are currently logged in as <?php echo htmlspecialchars($user); ?>! The key for this exercise is <b><?php echo htmlspecialchars(getenv("PTLAB_KEY")); ?></b>.
                </span>
            <?php } else { ?>
                <span class="text text-warning">
                    You are currently logged in as <?php echo htmlspecialchars($user); ?>!
                </span>
            <?php } ?>
        <?php } else { ?>
            <p>To start, you will need to create a user (<a href="/register.php">register</a>) and then <a href="/login.php">log in</a> to exploit this vulnerability.</p>
        <?php } ?>
    </div>
</div>