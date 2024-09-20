<?php
// Include the database connection file
include '../connect.php';

// Initialize variables
$statut_magasin = 'مغلق'; // Default status (closed)

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update status based on switch value
    $statut_magasin = isset($_POST['statut_magasin']) ? 'مفتوح' : 'مغلق'; // Use Arabic values
    
    // Update the database
    $stmt = $con->prepare("UPDATE magasin SET Statut_magasin = ? WHERE Id_magasin = 5"); // Assuming you have an id to identify the restaurant
    $stmt->bindValue(1, $statut_magasin, PDO::PARAM_STR);
    $stmt->execute();
}

// Retrieve the current status
$stmt = $con->prepare("SELECT Statut_magasin FROM magasin WHERE Id_magasin = 5"); // Assuming you have an id to identify the restaurant
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $statut_magasin = $row['Statut_magasin'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Status</title>
    <style>
        /* Basic styling for the switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {display: none;}
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <label class="switch">
            <input type="checkbox" name="statut_magasin" <?php echo $statut_magasin === 'مفتوح' ? 'checked' : ''; ?>>
            <span class="slider round"></span>
        </label>
        <button type="submit">تحديث الحالة</button> <!-- "Update Status" in Arabic -->
    </form>
</body>
</html>
