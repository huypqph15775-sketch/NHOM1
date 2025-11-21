<?php
session_start();

$user_type = $_SESSION['user_type'] ?? null;

session_destroy();

if ($user_type === 'admin') {
    header("Location: /phonestoree/signin.php");
} else {
    header("Location: /phonestoree/index.php");
}
exit();
?>
