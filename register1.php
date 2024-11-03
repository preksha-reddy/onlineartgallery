<?php
session_start();

// Initialize variables
$errors = array();

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "Preksha@12";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Form validation: ensure that the form is correctly filled
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // Check the database to make sure a user does not already exist with the same username and/or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Register user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password); // Encrypt the password before saving in the database
        $stmt = $conn->prepare("INSERT INTO gallery (username, email, password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        echo "Registration successful";
        exit();
    }
}
