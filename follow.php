<?php
session_start();

// Get the username of the follower from the session
$follower_username = $_SESSION['username'];

// Get the username of the user being followed from the POST parameter
$followed_username = $_POST['followed_user'];

// Connect to the database
require('./config/connexion.php');

// Prepare and execute the query to insert a new follower into the followers table
$stmt = $db->prepare('INSERT INTO followers (username, follower_user) VALUES (:follower_username, :followed_username)');
$stmt->execute([
    'follower_username' => $follower_username,
    'followed_username' => $followed_username,
]);

// Get the updated counts
// Count the number of posts
$stmt = $db->prepare('SELECT COUNT(*) AS post_count FROM posts WHERE username = :username');
$stmt->execute(['username' => $followed_username]);
$post_count = $stmt->fetch(PDO::FETCH_ASSOC)['post_count'];

// Count the number of followers
$stmt = $db->prepare('SELECT COUNT(*) AS follower_count FROM followers WHERE username = :username');
$stmt->execute(['username' => $followed_username]);
$follower_count = $stmt->fetch(PDO::FETCH_ASSOC)['follower_count'];

// Count the number of following
$stmt = $db->prepare('SELECT COUNT(*) AS following_count FROM followers WHERE follower_user = :username');
$stmt->execute(['username' => $followed_username]);
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
