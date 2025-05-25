<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_platform";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email and password are set
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : 'user';
    // Check if user exists
    $sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "User already exists.";
    } else {
        // Register the user
        $insertSql = "INSERT INTO user (name, email, password,role) VALUES ('$name', '$email', '$password' , '$role')";

        if ($conn->query($insertSql) === TRUE) {
            session_start();
            $_SESSION['user_id'] = $conn->insert_id;
            if($role === 'admin') {
                $_SESSION['user_role'] = 'admin';
                header('Location: ./admin-dashboard/index.php');

            } else {
                $_SESSION['user_role'] = 'user';
                header('Location: ./index.php');

            }
        } else {
            echo "Error: " . $insertSql . "<br>" . $conn->error;
        }
    }
} else {
    echo "Email and password are required.";
}

$conn->close();
?>