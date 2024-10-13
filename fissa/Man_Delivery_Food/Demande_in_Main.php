<?php

include "../connect.php"; // Including the database connection file


// Fetch order information where Id_Livreur = 1 and Id_Statut_Commande is 3 or 4
$sql = "
SELECT 
    d.Id_Demandes,
    c.Nom_Client,
    d.Prix_Livraison,
    d.Prix_Demande,
    m.Coordonnes AS Magasin_Coordonnes,
    c.Coordonnes AS Client_Coordonnes
FROM 
    demandes d
JOIN 
    client c ON d.Id_Client = c.Id_Client
JOIN 
    magasin m ON d.Id_Magasin = m.Id_Magasin
WHERE 
    d.Id_Livreur = 1 
    AND (d.Id_Statut_Commande = 3 OR d.Id_Statut_Commande = 4)
";

// Execute the query
$result = $con->query($sql);

// Check if there are results
if ($result->rowCount() > 0) {
    // Output data for each order
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Order ID: " . htmlspecialchars($row['Id_Demandes']) . "<br>";
        echo "Customer Name: " . htmlspecialchars($row['Nom_Client']) . "<br>";
        echo "Delivery Price: " . htmlspecialchars($row['Prix_Livraison']) . "<br>";
        echo "Order Price: " . htmlspecialchars($row['Prix_Demande']) . "<br>";
        echo "Restaurant Location: " . htmlspecialchars($row['Magasin_Coordonnes']) . "<br>";
        echo "Customer Location: " . htmlspecialchars($row['Client_Coordonnes']) . "<br><br>";
    }
} else {
    echo "No orders found.";
}
