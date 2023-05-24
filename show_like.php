<?php
session_start();
require("config/connexion.php");

if (isset($_POST['showlike'])) {
  require("config/connexion.php");
  $idPub = $_POST['idPub'];
  $query2 = $db->prepare("SELECT COUNT(*) FROM likes WHERE idPub = ?");
  $query2->execute([$idPub]);
  $count = $query2->fetchColumn();
  echo $count;
}
?>