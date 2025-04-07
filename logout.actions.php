<?php
// require '../src/database.class.php';
// require '../src/function.class.php';
session_start();
session_destroy();
header('Location:../src/login.php');