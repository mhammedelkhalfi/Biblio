<?php
require 'session_start.php';

// Détruire la session
session_destroy();
header("Location: login.php");
exit;
?>
