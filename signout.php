<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

session_destroy();

if ($role == 'admin') {
    header("Location: /phonestoree/signin.php");
} else {
    header("Location: /phonestoree/index.php");
}
exit();
?>
