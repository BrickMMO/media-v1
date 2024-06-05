<?php
// Start session to ensure session continuity
session_start();

// Include the navigation bar
include("navbar.php");

// Database connection details
$host = 'localhost';
$dbname = 'brickmmo';
$username = 'root';
$password = 'root';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If connection fails, display error message and terminate script
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // SQL query to check user credentials
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ? AND email = ? AND password = ?");
    $stmt->bind_param("sss", $user, $email, $pass);
    $stmt->execute();
    $stmt->store_result();

    // If user credentials are valid
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username, $email);
        $stmt->fetch();

        // Set session variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        // Set cookies for one hour
        setcookie("username", $username, time() + 3600, "/");
        setcookie("email", $email, time() + 3600, "/");

        // Redirect user to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // If user credentials are invalid, set error message
        $error = "Invalid username, email, or password!";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="my-4 text-center">Login Page</h2>
                <!-- Display error message if exists -->
                <?php if (isset($error)) { echo "<div class='alert alert-danger' role='alert'>$error</div>"; } ?>
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
