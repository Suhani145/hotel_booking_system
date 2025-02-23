<?php
session_start();
session_destroy();
echo "Success: Logged out!";
?>
<a href="login.php">Login Again</a>
