<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "INSERT INTO Users (name, username, password, email, user_type) VALUES (:name, :username, :password, :email, :user_type)";

    $statement = $db->prepare($query);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $statement->bindParam(':name', $name);
    $statement->bindParam(':username', $username);
    $statement->bindParam(':password', $hashed_password);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':user_type', $user_type);

    if ($statement->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Error adding user.";
    }
}
?>
