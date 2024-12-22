<?php 
$dsn = "mysql:host=mysql.hostinger.com;dbname=u351090189_fissa"; 
$user = "u351090189_fissa";
$pass = "Fissa2024"; 
$option = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8" // FOR Arabic
);
try {
    $con = new PDO($dsn, $user, $pass, $option); 
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo $e->getMessage();
}