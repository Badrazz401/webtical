<?php
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Get the comment data from the form
  $comment = $_POST['comment'];

  // Get the user ID from the session
  $user_id = $_SESSION['username'];

  // Connect to the database
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "webticale";

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Prepare the SQL statement to insert the comment into the database
  $stmt = $conn->prepare("INSERT INTO comments (comment,post_id, username, created_at) VALUES (?,:idPub ,?, NOW())");
  $stmt->bind_param("si", $comment, $user_id);

  // Execute the SQL statement
  if ($stmt->execute()) {
    // Comment was saved successfully
    echo "Comment saved successfully";

    // Display the comment
    $comment_id = $stmt->insert_id;
    $sql = "SELECT comment FROM comments WHERE idcmt = $comment_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Get the user name from the database
    $user_id = $row['user_id'];
    $user_sql = "SELECT fullname FROM utilisateur WHERE username = $user_id";
    $user_result = $conn->query($user_sql);
    $user_row = $user_result->fetch_assoc();

    echo "<p><strong>" . $user_row['name'] . ":</strong> " . $row['comment'] . "</p>";
  } else {
    // Error saving the comment
    echo "Error: " . $stmt->error;
  }

  // Close the database connection
  $stmt->close();
  $conn->close();
}
?>
