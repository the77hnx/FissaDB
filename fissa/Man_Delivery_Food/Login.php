<?php
// Include the database connection file
include '../connect.php';

// Initialize error message variable
$errorMsg = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL to check if the user exists
    $sql = "SELECT * FROM livreur WHERE E_mail = :email AND Password = :password";
    $stmt = $con->prepare($sql);

    // Bind parameters
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR); // Password should be hashed in a real-world scenario

    // Execute the query
    $stmt->execute();

    // Check if a matching user was found
    if ($stmt->rowCount() > 0) {
        // Login successful
        echo "<p>Login successful! Welcome back, $email.</p>";
    } else {
        // Login failed
        $errorMsg = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
</head>
<body>
    <h2>User Login</h2>
    <form action="" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <!-- Display error message if login fails -->
    <?php
    if (!empty($errorMsg)) {
        echo "<p style='color:red;'>$errorMsg</p>";
    }
    ?>
</body>
</html>
