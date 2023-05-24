<?php
function userLikes($idPub, $username)
{
    require("config/connexion.php");
    $sql = "SELECT COUNT(*) FROM likes WHERE username=:username 
        AND idPub=:idPub";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_INT);
    $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function getLikes($idPub)
{
    require("config/connexion.php");
    $sql = "SELECT COUNT(*) FROM likes WHERE idPub = :idPub";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);
    $stmt->execute();
    $number_of_rows = $stmt->fetchColumn();
    return $number_of_rows;
}

function insert_like($username, $idPub)
{
    require("config/connexion.php");

    // Check if user has already liked the post
    $stmt = $db->prepare("SELECT COUNT(*) FROM likes WHERE idPub = ? AND username = ?");
    $stmt->execute([$idPub, $username]);
    $count = $stmt->fetchColumn();
    
    // If user has not already liked the post, insert the new like
    if ($count == 0) {
        $stmt = $db->prepare("INSERT INTO likes (idPub, username) VALUES (?, ?)");
        $stmt->execute([$idPub, $username]);
    }
}


function delete_Like($username, $idPub)
{
    require("config/connexion.php");
    $sql = "DELETE FROM likes WHERE username=:username AND idPub=:idPub";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_INT);
    $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);
    $stmt->execute();
}

function getRating($idPub)
{
    require("config/connexion.php");
    
    $stmt = $db->prepare("SELECT COUNT(*) as likes FROM likes WHERE idPub = ?");
    $stmt->execute([$idPub]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return json_encode($result);
}

function hasUserLiked($username, $idPub) {
    global $dbh;
  
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM likes WHERE username = :username AND idPub = :idPub");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':idPub', $idPub);
    $stmt->execute();
    $count = $stmt->fetchColumn();
  
    return ($count > 0);
  }
  

// function getRating($idPub)
// {
//     require("config/connexion.php");
//     $rating = array();
//     $likes = getLikes($idPub);
//     $rating = [
//         'likes' => $likes,
//     ];
//     return json_encode($rating);
// }



