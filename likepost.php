<?php
session_start();
// connect to database using PDO
require("config/connexion.php");

// Get the post ID and the user's username
$idPub = $_POST["idPub"];
$username = $_SESSION["username"];

// Check if the user has already liked the post
$stmt = $db->prepare("SELECT * FROM likes WHERE idPub = ? AND username = ?");
$stmt->execute([$idPub, $username]);
$like = $stmt->fetch();

if ($like) {
    // The user has already liked the post, so delete the like
    $stmt = $db->prepare("DELETE FROM likes WHERE idPub = ? AND username = ?");
    $stmt->execute([$idPub, $username]);
} else {
    // The user has not liked the post yet, so insert a new like
    $stmt = $db->prepare("INSERT INTO likes (idPub, username) VALUES (?, ?)");
    $stmt->execute([$idPub, $username]);
}
$stmt = $db->prepare('SELECT COUNT(*) FROM likes WHERE idPub = :idPub');
$stmt->bindParam(':idPub', $idPub);
$stmt->execute();
$likeCount = $stmt->fetchColumn();
// echo $likeCount;

// Close the database connection
$db = null;
header("Location: post.php?idPub=". $idPub . "?likecount=" . $likeCount);
exit();