<?php
session_start();

require 'connect-db.php'; 


if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];

    try {
        $stmt = $db->prepare("DELETE FROM User WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
   

    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header("Location: login.php");
    exit();
} else {
    echo "hi";
}
?>