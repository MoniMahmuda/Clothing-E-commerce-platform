<?php
session_start();
require_once 'db.php'; // DB connection

$error = "";

// Process login request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Please enter both email and password.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM user_t WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password hash
            if (password_verify($pass, $user['password'])) {
                // Store full user info in session
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role']
                ];

                // Also store role separately for easy access
                $_SESSION['role'] = $user['role'];

                // Redirect by role
                if ($user['role'] === 'customer') {
                    header("Location: home.html");
                } elseif ($user['role'] === 'supplier') {
                    header("Location: dashboard-supplier.html");
                } elseif ($user['role'] === 'admin') {
                    header("Location: dashboard-admin.html"); 
                }
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>
