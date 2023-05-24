<?php
session_start();
// connect to database using PDO
require("config/connexion.php");
include("config/functions.php");

if (isset($_POST['like'])) {
    $idPub = $_POST['idPub'];
    $query = $db->prepare("SELECT * FROM likes WHERE idPub=:idPub AND username=:username");
    $query->bindParam(':idPub', $idPub);
    $query->bindParam(':username', $_SESSION['username']);
    $query->execute();

    if ($query->rowCount() > 0) {
        $deleteQuery = $db->prepare("DELETE FROM likes WHERE username=:username AND idPub=:idPub");
        $deleteQuery->bindParam(':username', $_SESSION['username']);
        $deleteQuery->bindParam(':idPub', $idPub);
        $deleteQuery->execute();
    } else {
        $insertQuery = $db->prepare("INSERT INTO likes (username, idPub) VALUES (:username, :idPub)");
        $insertQuery->bindParam(':username', $_SESSION['username']);
        $insertQuery->bindParam(':idPub', $idPub);
        $insertQuery->execute();
    }
}
?>