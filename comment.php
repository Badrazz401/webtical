<?php
session_start();
// connect to database using PDO
require("config/connexion.php");

if (isset($_POST['ok'])) {
    require("config/connexion.php");
    $username = $_SESSION["username"];
    $idPub = $_POST['idPub'];
    $contenuComment = $_POST['comment'];

    // Insert the comment into the database
    $query = $db->prepare('INSERT INTO comments (username, idPub, contenuComment) VALUES (:username, :idPub, :contenuComment)');
    $query->execute(array(':username' => $username, ':idPub' => $idPub, ':contenuComment' => $contenuComment));

    // Close the database connection
    $db = null;
    header("Location: post.php?idPub=" . $idPub);

    exit();
}
?>