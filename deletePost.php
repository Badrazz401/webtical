<?php

session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");

    $username = $_SESSION['username'];
    $postId = $_GET['idPub']; // Récupérer l'ID du post à supprimer

    // Vérifier si l'utilisateur est l'auteur du post
    $selPost = $db->prepare('SELECT * FROM publication WHERE idPub = :postId AND username = :username');
    $selPost->bindParam(':postId', $postId);
    $selPost->bindParam(':username', $username);
    $selPost->execute();

    if ($selPost->rowCount() > 0) {
        // Supprimer les likes associés à la publication
        $deleteLikes = $db->prepare('DELETE FROM likes WHERE idPub = :postId');
        $deleteLikes->bindParam(':postId', $postId);
        $deleteLikes->execute();
        // Supprimer les commentaires associés au post
        $deleteComments = $db->prepare('DELETE FROM comments WHERE idPub = :postId');
        $deleteComments->bindParam(':postId', $postId);
        $deleteComments->execute();

        // Supprimer le post de la base de données
        $deletePost = $db->prepare('DELETE FROM publication WHERE idPub = :postId');
        $deletePost->bindParam(':postId', $postId);
        $deletePost->execute();

        // Rediriger l'utilisateur vers la page d'accueil ou une autre page appropriée
        header("Location: home.php");
        exit();
    } else {
        // L'utilisateur n'est pas autorisé à supprimer ce post
        echo "<script>
    alert('Vous n'
            êtes pas autorisé à supprimer ce post.
            ')
</script>";
        header("location: home.php");
        exit();
    }
} else {
    // L'utilisateur n'est pas connecté
    echo "Vous devez être connecté pour supprimer un post.";
}
