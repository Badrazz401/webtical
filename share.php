<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'], $_GET['idPub'])) {
    require("config/connexion.php");

    $idPub = $_GET['idPub'];
    $username = $_SESSION['username'];

    // Check if the user has already shared the post
    $checkSql = "SELECT COUNT(*) FROM shares WHERE post_id = :idPub AND username = :username";
    $stmt = $db->prepare($checkSql);
    $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Prepare the SQL statement
        $shareSql = "INSERT INTO shares (post_id, username) VALUES (:idPub, :username)";

        // Insert the share record
        $stmt = $db->prepare($shareSql);
        $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
    }
}

// Redirect using JavaScript
echo '<script>window.location.href = document.referrer;</script>';
exit;
?>
