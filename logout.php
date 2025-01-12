<?php
session_start();
session_unset();
session_destroy();
header("Location: soilder_login.php");
exit();
?>
