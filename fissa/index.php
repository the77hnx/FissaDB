<?php 

include "connect.php" ;
$stmt = $con->prepare("SELECT Address FROM client") ;
$stmt->execute() ;
$client = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($client) ;