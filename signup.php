<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Webtical Signup</title>
    <link rel="stylesheet" type="text/css" href="./style/signup.css">
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body style="background-image: url('./img/tttend.svg');
    
    background-repeat:no-repeat;
    background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form class="sp" action="./signup.php" enctype="multipart/form-data" method="post">
                    <div class="logo">
                        <a href="index.php" style="text-decoration: none;"><img src="./img/LG.png">
                            <h2 class="logo-text" style="color: black;font-weight: bold;font-size: 30px">Webtical</h2>
                        </a>
                    </div>
                    <h2 class="signup" style="color: #43ceb0;text-align: center">Sign up</h2>
                    <div class="form-group">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="fullname" class="form-control" placeholder="Full name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8"
                            placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of birth</label>
                        <input type="date" id="dob" name="dob" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="profilepic">Profile picture</label>
                        <input type="file" id="profilepic" name="file" accept="image/*" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="3" maxlength="160"></textarea>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#">terms and conditions</a>.
                        </label>
                    </div>
                    <button type="submit" name="ok" class="btn btn-primary">Sign up</button>
                </form>


            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS script link -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
    if (isset($_POST['ok'])) {
        if (isset($_POST['fullname'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['dob'], $_POST['bio'])) {
            $_SESSION['loggedIn'] = true;
            // Connexion à la base de données
            require("config/connexion.php");

            // Préparation de la requête d'insertion
            $stmt = $db->prepare("INSERT INTO utilisateur (username, fullname, email, password, dob, bio, name, image) VALUES (:nom_utilisateur, :nom_complet, :email, :mot_de_passe, :date_naissance, :bio, :name, :image)");

            // Bind des paramètres de la requête
            $stmt->bindParam(':nom_utilisateur', $_POST['username']);
            $stmt->bindParam(':nom_complet', $_POST['fullname']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':mot_de_passe', $_POST['password']); // On hash le mot de passe
            $stmt->bindParam(':date_naissance', $_POST['dob']);
            $stmt->bindParam(':bio', $_POST['bio']);
            $stmt->bindParam(':name', $_FILES['file']['name']);

            // Upload de l'image
            $name = $_FILES['file']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);

            // Select file type
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png", "gif", "jfif");

            // Check extension
            if (in_array($imageFileType, $extensions_arr)) {
                // Upload file
                if (move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name)) {
                    // Convert to base64
                    $image_base64 = base64_encode(file_get_contents('uploads/' . $name));
                    $image = 'data:image/' . $imageFileType . ';base64,' . $image_base64;

                    $stmt->bindParam(':image', $image);
                } else {
                    $stmt->bindValue(':image', null, PDO::PARAM_NULL);
                }
            } else {
                $stmt->bindValue(':image', null, PDO::PARAM_NULL);
            }

            // Exécution de la requête d'insertion
            if ($stmt->execute()) {
                // Redirection vers la page de connexion
                $username = $_POST['username'];
    
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $username;
                header('Location: home.php');
                exit();
            } else {
                echo "Erreur lors de l'insertion des données dans la base de données.";
            }
        }
    }

    ?>
</body>

</html>