<?php
include "../connect.php";

// Set Id_Livreur to 1 initially (for the logged-in user)
$Id_Livreur = 0;

// Query to get all orders for the logged-in Livreur
$sql_orders = "SELECT Id_Demandes, Date_commande, Heure_commande 
               FROM demandes 
               WHERE Id_Livreur = :Id_Livreur
               AND Id_Statut_Commande IN (3, 4, 6)";

$stmt_orders = $con->prepare($sql_orders);
$stmt_orders->bindParam(':Id_Livreur', $Id_Livreur, PDO::PARAM_INT);
$stmt_orders->execute();
$result_orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Prepare the order details for the HTML form below
$orders = [];
foreach($result_orders as $row_orders) {
    $Id_Demandes = $row_orders['Id_Demandes'];
    $Date_commande = $row_orders['Date_commande'];
    $Heure_commande = $row_orders['Heure_commande'];

    // Query to get the number of items for the current order
    $sql_items = "SELECT COUNT(*) AS num_items 
                  FROM articles 
                  WHERE Id_Demandes = :Id_Demandes";
    $stmt_items = $con->prepare($sql_items);
    $stmt_items->bindParam(':Id_Demandes', $Id_Demandes, PDO::PARAM_INT);
    $stmt_items->execute();
    $row_items = $stmt_items->fetch(PDO::FETCH_ASSOC);
    $num_items = $row_items['num_items'];

    // Store the order information
    $orders[] = [
        'Id_Demandes' => $Id_Demandes,
        'num_items' => $num_items,
        'Date_commande' => $Date_commande,
        'Heure_commande' => $Heure_commande
    ];
}
header('Content-Type: application/json');
    echo json_encode($orders);