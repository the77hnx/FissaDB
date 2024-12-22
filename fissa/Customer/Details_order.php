<?php
include("../connect.php");

// Read the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Get the order ID and new status from the decoded JSON data
$orderId = isset($data['Id_Demandes']) ? $data['Id_Demandes'] : null;
$newStatus = isset($data['Id_Statut_Commande']) ? $data['Id_Statut_Commande'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($newStatus !== null) {
        updateOrderStatus($con, $orderId, $newStatus);
    } else {
        getOrderDetails($con, $orderId);
    }
} else {
    echo json_encode(["error" => true, "message" => "Invalid request method."]);
}

function updateOrderStatus($con, $orderId, $newStatus) {
    if (!$orderId || !is_numeric($orderId) || !$newStatus) {
        echo json_encode(["error" => true, "message" => "Invalid order ID or status."]);
        return;
    }

    $sql = "UPDATE demandes SET Id_Statut_Commande = ? WHERE Id_Demandes = ?";
    $stmt = $con->prepare($sql);
    
    if ($stmt->execute([$newStatus, $orderId])) {
        echo json_encode(["error" => false, "message" => "Order status updated successfully."]);
    } else {
        echo json_encode(["error" => true, "message" => "Failed to update order status."]);
    }
}

function getOrderDetails($con, $orderId) {
    if (!$orderId || !is_numeric($orderId)) {
        echo json_encode(["error" => true, "message" => "Invalid order ID."]);
        return;
    }

    // SQL to fetch only the necessary details  
    $sql = "SELECT 
            r.Nom_magasin AS restaurant_name, 
            r.Address_magasin AS restaurant_address, 
            r.Statut_magasin AS restaurant_status, 
            r.Evaluation AS restaurant_eval, 
            r.Image_path AS restaurant_image_path, 
            l.Nom_Livreur AS delivery_worker_name, 
            l.Image_path AS delivery_worker_image_path,
            d.info_mag AS additional_info_magasin,
            d.info_liv AS additional_info_livreur
        FROM demandes d 
        JOIN magasin r ON d.Id_magasin = r.Id_magasin
        JOIN livreur l ON d.Id_Livreur = l.Id_Livreur
        WHERE d.Id_Demandes = ?";

    $stmt = $con->prepare($sql);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Fetching order items
            $sqlItems = "
                        SELECT 
                                a.Nom_Article AS itemName, 
                                a.Quantite AS itemQuantity, 
                                a.Prix AS itemPrice, 
                                p.Image_path AS itemImage 
                        FROM 
                                articles a
                        JOIN 
                                produits p ON a.Nom_Article = p.Nom_Prod
                        WHERE 
                                a.Id_Demandes = :orderId
                        ";    
        $stmt_items = $con->prepare($sql_items);
        $stmt_items->execute([$orderId]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $order['items'] = $items; // Add items to order data

        echo json_encode(["error" => false, "order" => $order]); // Return order data as JSON
    } else {
        echo json_encode(["error" => true, "message" => "Order details not found."]);
    }
}