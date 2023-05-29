<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $senderUsername = $_SESSION['username'];
    $recipientUsername = $_POST['recipient'];
    $message = $_POST['message'];

    // Insert the message into the database
    $insertMessage = $db->prepare('INSERT INTO message (sender, receiver, message, timestamp) VALUES (:sender, :receiver, :message, NOW())');
    $insertMessage->bindParam(':sender', $senderUsername);
    $insertMessage->bindParam(':receiver', $recipientUsername);
    $insertMessage->bindParam(':message', $message);
    $insertMessage->execute();

    // Redirect the user to the chat page with the recipient parameter
    header("Location: chating.php?recipient=$recipientUsername");
    exit();
} else {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}
?>
