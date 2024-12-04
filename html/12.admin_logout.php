<?php
session_start();
session_destroy();
header("Location: 12.admin_login.php");
exit();
?>
