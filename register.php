<?php
session_start();

// Initializing variables
$username = "";
$email    = "";
$errors = array(); 

// Connect to the database
$db = mysqli_connect('127.0.0.1', 'root', 'your_password', 'artgallerydb');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Form validation: ensure that the form is correctly filled
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

    // Check the database to make sure a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM gallery WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
  
    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    if (count($errors) == 0) {
        $password = md5($password); 
        $query = "INSERT INTO gallery (username, email, password) VALUES ('$username', '$email', '$password')";
        if (mysqli_query($db, $query)) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            echo "Registration successful";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($db);
        }
        exit();
    }
}