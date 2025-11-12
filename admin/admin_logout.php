<?php
session_start();
$_SESSION['admin_logged_in'] = false;
session_destroy();
header('Location: admin_login.php');
exit;