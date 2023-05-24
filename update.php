<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

// Récupère les informations de l'utilisateur à partir de la session
$username = $_SESSION['username'];

// Vérifie si le formulaire a été soumis
if (isset($_POST['update'])) {
  // Récupère les valeurs du formulaire
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm-password'];

  // Vérifie si les mots de passe correspondent
  if ($password !== $confirm_password) {
    $error_message = 'Les mots de passe ne correspondent pas.';
  } else {
    // Met à jour les informations de l'utilisateur dans la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=webticale', 'root', '');
    $query = "UPDATE utilisateurs SET fullname = ?, email = ?, password = ? WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fullname, $email, $password, $username]);

    // Redirige l'utilisateur vers la page de profil avec un message de confirmation
    header('Location: profile.php?message=Mise à jour réussie.');
    exit();
  }
}
?>
