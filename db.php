<?php
define('DB_SERVER', 'localhost'); // Database server
define('DB_USERNAME', 'inventur_root'); // Database Username
define('DB_PASSWORD', 'root'); // Database Password
define('DB_DATABASE', 'inventur_linkprofile'); // Database Name
$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE); // connecting with database
?>