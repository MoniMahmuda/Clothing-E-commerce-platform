<?php
// Include database connection
require_once 'db.php'; // Make sure db.php has $conn = new mysqli(...);

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and trim inputs
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = trim($_POST['role'] ?? '');

    // Basic validation
    if ($name === '' || $email === '' || $password === '' || $role === '') {
        die("Please fill all fields.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Escape strings for SQL
    $name  = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $role  = $conn->real_escape_string($role);

    // Check if email already exists
    $check_sql = "SELECT id FROM User_t WHERE email = '$email' LIMIT 1";
    $result = $conn->query($check_sql);
    if ($result && $result->num_rows > 0) {
        die("Email already registered. Please use another email.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO User_t (name, email, password, role) 
            VALUES ('$name', '$email', '$hashed_password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Signup successful! <a href='login.html'>Go to Login</a>";
        // Or redirect directly:
        // header("Location: login.html");
        // exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>

