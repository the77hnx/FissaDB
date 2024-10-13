<?php
include("../connect.php"); // Ensure the database connection is properly included

// Check if orderId is provided
if (isset($_GET['orderId'])) {
    $orderId = $_GET['orderId'];

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
            stat_cmd s ON o.Id_Statut_Commande = s.Id_Statut_Commande 
        WHERE 
            o.Id_Demandes = :orderId
    ";

    // Prepare the statement
    if ($stmt = $con->prepare($sql)) {
        // Bind the orderId as an integer
        $stmt->bindValue(':orderId', $orderId, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the results if available
            if ($stmt->rowCount() > 0) {
                // Get order and customer details
                $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
                $response['order'] = array(
                    'orderId' => $orderData['orderId'],
                    'orderPrice' => $orderData['orderPrice'],
                    'deliveryPrice' => $orderData['deliveryPrice'],
                    'orderDate' => $orderData['orderDate'],
                    'restaurantMessage' => $orderData['restaurantMessage'],
                    'customer' => array(
                        'customerId' => $orderData['customerId'],
                        'customerName' => $orderData['customerName'],
                        'customerNumber' => $orderData['customerNumber'],
                        'customerEmail' => $orderData['customerEmail'],
                        'customerCoordinates' => $orderData['customerCoordinates']
                    ),
                    'orderStatus' => $orderData['orderStatus']
                );

                // Now fetch the items in the order from Articles table
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

                if ($stmtItems = $con->prepare($sqlItems)) {
                    // Bind the orderId for the items query
                    $stmtItems->bindValue(':orderId', $orderId, PDO::PARAM_INT);
                    if ($stmtItems->execute()) {
                        $resultItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                        $response['order']['items'] = $resultItems;
                    } else {
                        $response['error'] = "Error fetching order items.";
                    }
                    $stmtItems->closeCursor();
                }
            } else {
                $response['error'] = "No order found with this orderId.";
            }
        } else {
            $response['error'] = "Error executing query.";
        }
        $stmt->closeCursor();
    } else {
        $response['error'] = "Error preparing the SQL statement.";
    }

    // Return the JSON response
    echo json_encode($response);
} else {
    echo json_encode(array("error" => "No orderId provided."));
}
