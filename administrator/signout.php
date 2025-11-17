<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

session_destroy();


    header("Location: /phonestoree/index.php");

exit();
?>
