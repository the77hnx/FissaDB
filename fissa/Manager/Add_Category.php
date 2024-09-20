<?php 
// Include database connection
include '../connect.php';

// Check if form data is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data and sanitize it
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    if (empty($name)) {
        echo "Category name is required.";
        exit;
    }

    try {
        // Prepare and execute SQL query
        $sql = $con->prepare("INSERT INTO categories (Nom_Cat) VALUES (:name)");
        $sql->bindParam(':name', $name, PDO::PARAM_STR);

        if ($sql->execute()) {
            echo "New category created successfully";
        } else {
            echo "Error: " . $sql->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $con = null;
} else {
    echo "Invalid request method.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Insert Category</title>
</head>
<body>
    <h1>Insert New Category</h1>
    <form action="Add_category.php" method="post">
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" required>
        <br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
