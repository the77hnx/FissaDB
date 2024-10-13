<?php
include("../connect.php"); // Ensure the database connection is properly included

// Read the input JSON body
$inputData = json_decode(file_get_contents('php://input'), true);

if (isset($inputData['Id_Demandes'])) {
    $orderId = $inputData['Id_Demandes'];

    // Initialize response array
    $response = array();

    // SQL query to fetch customer details, order information, and order items
    $sql = "
        SELECT 
            o.Id_Demandes AS orderId, 
            o.Prix_Demande AS orderPrice, 
            o.Prix_Livraison AS deliveryPrice, 
            o.Date_commande AS orderDate, 
            o.info_mag AS restaurantMessage, 
            r.Nom_magasin AS restaurant_name, 
            r.Address_magasin AS restaurant_address, 
            r.Statut_magasin AS restaurant_status, 
            r.Evaluation AS restaurant_eval, 
            c.Id_Client AS customerId, 
            c.Nom_Client AS customerName, 
            c.Tel_Client AS customerNumber, 
            c.E_mail AS customerEmail, 
            c.Coordonnes AS customerCoordinates, 
            s.Nom_Statut AS orderStatus 
        FROM 
            demandes o 
        JOIN 
            client c ON o.Id_Client = c.Id_Client
        JOIN 
            magasin r ON o.Id_magasin = r.Id_magasin
        JOIN 
            stat_cmd s ON o.Id_Statut_Commande = s.Id_Statut_Commande 
        WHERE 
            o.Id_Demandes = :orderId
    ";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':orderId', $orderId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Add order information to the response
            $response['order'] = array(
                "orderId" => $order['orderId'],
                "orderPrice" => $order['orderPrice'],
                "deliveryPrice" => $order['deliveryPrice'],
                "orderDate" => $order['orderDate'],
                "restaurantMessage" => $order['restaurantMessage'],
                "restaurant_name" => $order['restaurant_name'],
                "restaurant_address" => $order['restaurant_address'],
                "restaurant_status" => $order['restaurant_status'],
                "restaurant_eval" => $order['restaurant_eval'],
                "customerId" => $order['customerId'],
                "customerName" => $order['customerName'],
                "customerNumber" => $order['customerNumber'],
                "customerEmail" => $order['customerEmail'],
                "customerCoordinates" => $order['customerCoordinates'],
                "orderStatus" => $order['orderStatus']
            );

            // Fetch the items in the order
            $sqlItems = "
                SELECT 
                    Nom_Article AS itemName, 
                    Quantite AS itemQuantity, 
                    Prix AS itemPrice 
                FROM 
                    articles 
                WHERE 
                    Id_Demandes = :orderId
            ";

            $stmtItems = $con->prepare($sqlItems);
            $stmtItems->bindValue(':orderId', $orderId, PDO::PARAM_INT);

            if ($stmtItems->execute()) {
                $resultItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                $response['order']['items'] = $resultItems; // Add items to the response
            } else {
                $response['order']['items'] = array(); // No items found, return an empty array
            }
            $stmtItems->closeCursor();
        } else {
            $response['error'] = "No order found with this orderId.";
        }
    } else {
        $response['error'] = "Error executing query.";
    }

    $stmt->closeCursor();

    // Return the JSON response
    echo json_encode($response);
} else {
    echo json_encode(array("error" => "No Id_Demandes provided."));
}
