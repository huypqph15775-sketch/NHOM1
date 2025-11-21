<?php
session_start();
<<<<<<< HEAD

$user_type = $_SESSION['user_type'] ?? null;

session_destroy();

if ($user_type === 'admin') {
=======
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

session_destroy();

if ($role == 'admin') {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
    header("Location: /phonestoree/signin.php");
} else {
    header("Location: /phonestoree/index.php");
}
exit();
?>
