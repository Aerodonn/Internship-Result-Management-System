<?php
session_start();
session_unset();        //logging out of session
header("location:login.php");
?>