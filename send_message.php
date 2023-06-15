<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $senderUsername = $_SESSION['username'];
    $recipientUsername = $_POST['recipient'];
    $message = $_POST['message'];

    // Traitement du fichier image
    $image = $_FILES['image'];
    $video = $_FILES['video'];
    $imagePath = null;
    $videoPath = null;

    if ($image['name'] != '') {
        $uploadDirectory = 'uploads/';
        $fileName = basename($image['name']);
        $targetPath = $uploadDirectory . $fileName;

        // Déplacer l'image téléchargée vers le répertoire de destination
        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        } else {
            echo "Failed to upload image.";
            exit();
        }
    }

    if ($video['name'] != '') {
        $uploadDirectory = 'uploads/';
        $fileName = basename($video['name']);
        $targetPath = $uploadDirectory . $fileName;

        // Déplacer la vidéo téléchargée vers le répertoire de destination
        if (move_uploaded_file($video['tmp_name'], $targetPath)) {
            $videoPath = $targetPath;
        } else {
            echo "Failed to upload video.";
            exit();
        }
    }

    // Insertion du message dans la base de données
    $insertMessage = $db->prepare('INSERT INTO message (sender, receiver, message, timestamp, image, video) VALUES (:sender, :receiver, :message, NOW(), :image, :video)');
    $insertMessage->bindParam(':sender', $senderUsername);
    $insertMessage->bindParam(':receiver', $recipientUsername);
    $insertMessage->bindParam(':message', $message);
    $insertMessage->bindParam(':image', $imagePath);
    $insertMessage->bindParam(':video', $videoPath);
    $insertMessage->execute();

    // Redirection de l'utilisateur vers la page de chat avec le paramètre du destinataire
    header("Location: chating.php?recipient=$recipientUsername");
    exit();
} else {
    // Redirection de l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: login.php");
    exit();
}
