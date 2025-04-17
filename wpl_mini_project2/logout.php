<?php
require_once 'config/config.php';

$_SESSION = array();

session_destroy();

redirect('login.php');
?>