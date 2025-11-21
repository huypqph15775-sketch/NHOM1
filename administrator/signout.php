<?php
session_start();
<<<<<<< HEAD
session_destroy();
header("Location: /phonestoree/signin.php");
=======
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

session_destroy();


    header("Location: /phonestoree/index.php");

>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
exit();
?>
