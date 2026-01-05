<?php
session_start();
session_destroy();
header("Location: /phonestoree/signin.php");
exit();
?>
