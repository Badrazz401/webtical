<?php
session_start();

// Get the username of the follower from the session
$follower_username = $_SESSION['username'];

// Get the username of the user being unfollowed from the POST parameter
$unfollowed_username = $_POST['unfollowed_user'];

// Connect to the database
require('./config/connexion.php');

// Prepare and execute the query to delete the follower from the followers table
$stmt = $db->prepare('DELETE FROM followers WHERE username = :follower_username AND follower_user = :followed_username');
$stmt->execute([
    'follower_username' => $follower_username,
    'followed_username' => $unfollowed_username,
]);

// Get the updated counts
// Count the number of posts
$stmt = $db->prepare('SELECT COUNT(*) AS post_count FROM posts WHERE username = :username');
$stmt->execute(['username' => $unfollowed_username]);
$post_count = $stmt->fetch(PDO::FETCH_ASSOC)['post_count'];

// Count the number of followers
$stmt = $db->prepare('SELECT COUNT(*) AS follower_count FROM followers WHERE username = :username');
$stmt->execute(['username' => $unfollowed_username]);
$follower_count = $stmt->fetch(PDO::FETCH_ASSOC)['follower_count'];

// Count the number of following
$stmt = $db->prepare('SELECT COUNT(*) AS following_count FROM followers WHERE follower_user = :username');
$stmt->execute(['username' => $unfollowed_username]);
$following_count = $stmt->fetch(PDO::FETCH_ASSOC)['following_count'];

// Create an array with the updated counts
$response = [
    'postCount' => $post_count,
    'followerCount' => $follower_count,
    'followingCount' => $following_count
];

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
