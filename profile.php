<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");

    $message = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['fullname']) && !empty($_POST['dob']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['bio'])) {
        // Check if the uploaded files are valid
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jfif'];

        // Handle uploaded profile picture
        if (!empty($_FILES['profilepic']['name'])) {
            $name = $_FILES['profilepic']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profilepic"]["name"]);

            // Select file type
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Valid file extensions
            $extensions_arr = array("jpg", "jpeg", "png", "gif", "jfif");

            // Check extension
            if (in_array($imageFileType, $extensions_arr)) {
                // Upload file
                if (move_uploaded_file($_FILES['profilepic']['tmp_name'], $target_dir . $name)) {
                    // Convert to base64
                    $image_base64 = base64_encode(file_get_contents($target_file));
                    $image = 'data:image/' . $imageFileType . ';base64,' . $image_base64;

                    $stmt = $db->prepare("UPDATE utilisateur SET name = :name, image = :image WHERE username = :session_username");
                    $stmt->execute([
                        ':name' => $name,
                        // Use the variable containing the file name
                        ':image' => $image,
                        // Use the variable containing the image encoded in base64
                        ':session_username' => $_SESSION['username']
                    ]);

                    // Display success message for profile picture update
                    $message = "Profile picture updated successfully.";
                } else {
                    $message = "Error uploading profile picture. Please try again later.";
                }
            } else {
                $message = "Invalid file type for profile picture.";
            }
        }

        // Update other fields in the database
        $stmt = $db->prepare("UPDATE utilisateur SET fullname = :fullname, email = :email, password = :password, dob = :dob, bio = :bio WHERE username = :session_username");
        $stmt->execute([

            ':fullname' => $_POST['fullname'],
            ':email' => $_POST['email'],
            ':password' => $_POST['password'],
            ':dob' => $_POST['dob'],
            ':bio' => $_POST['bio'],
            ':session_username' => $_SESSION['username']
        ]);

        // Display success message for other field updates
        // $message .= "Profile updated successfully.";
    }

    // Update follower, following, and publication counts
    $queryFollowers = "SELECT COUNT(*) AS followerCount FROM followers WHERE follower_user = :username";
    $stmtFollowers = $db->prepare($queryFollowers);
    $stmtFollowers->execute([':username' => $_SESSION['username']]);
    $followerCount = $stmtFollowers->fetchColumn();

    $queryFollowing = "SELECT COUNT(*) AS followingCount FROM followers WHERE username = :username";
    $stmtFollowing = $db->prepare($queryFollowing);
    $stmtFollowing->execute([':username' => $_SESSION['username']]);
    $followingCount = $stmtFollowing->fetchColumn();

    $queryPub = "SELECT COUNT(*) AS publicationCount FROM publication WHERE username = :username";
    $stmtPub = $db->prepare($queryPub);
    $stmtPub->execute([':username' => $_SESSION['username']]);
    $publicationCount = $stmtPub->fetchColumn();

    // Fetch user data from database and display on profile page
    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username = :username");
    $stmt->execute([':username' => $_SESSION['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $queryT = $db->prepare('SELECT * FROM trends ORDER BY count DESC LIMIT 5');
    $queryT->execute();
    $trends = $queryT->fetchAll(PDO::FETCH_ASSOC);


    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Webtical</title>
        <script src="https://cdn.tailwindcss.com"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script src="https://use.fontawesome.com/fe459689b4.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./js/like.js"></script>

        <style>
            input[type="file"] {
                /* Remove default styles */
                appearance: none;
                -webkit-appearance: none;
                /* Position off-screen */
                position: absolute;
                left: -9999px;
            }

            .like-btn {
                background-color: #eee;
                border: none;
                padding: 10px;
                cursor: pointer;
            }

            .like-btn.active {
                background-color: #f00;
                color: #fff;
            }
        </style>

    </head>

    <body>
        <div class="flex flex-row ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-1/2 max-[970px]:basis-full p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold min-h-screen">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">
                        Profile
                    </span>
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="pt-2"></div>

                <div class="pt-2"></div>
                <div class="border border-gray-400 "></div>
                <!--content-->

                <!-- post div -->
                <div id="post" class="p-4 ">
                    <h1 class="text-2xl font-bold mt-2 mb-4">Edit Profile</h1>

                    <?php if (!empty($message)): ?>
                        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                            role="alert">
                            <div class="flex">
                                <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                    </svg></div>
                                <div>

                                    <p class="font-bold">
                                        <?= $message ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <br>
                    <form method="POST" enctype="multipart/form-data" class="">
                        <div class="bg-gradient-to-r from-blue-300 to-blue-400 h-64 border border-gray-400 rounded-xl">
                        </div>

                        <div class="flex justify-center  border-gray-400 rounded-lg w-full mx-auto max-[1070px]:justify-end ">

                            <div id="postCount" class="flex flex-col items-center">
                                <span class="font-bold text-xl">
                                    <?= $publicationCount ?>
                                </span>
                                <span class="text-gray-600">Posts</span>
                            </div>
                            <div id="followerCount" class="flex flex-col items-center mx-4">
                                <span class="font-bold text-xl">
                                    <?= $followerCount ?>
                                </span>
                                <span class="text-gray-600">Followers</span>
                            </div>
                            <div id="followingCount" class="flex flex-col items-center">
                                <span class="font-bold text-xl">
                                    <?= $followingCount ?>
                                </span>
                                <span class="text-gray-600">Following</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="flex items-center  mt-[-8rem]">
                                <img id="profile-picture"
                                    class="w-32 h-32 rounded-full border-2  border-gray-400 cursor-pointer shadow-lg"
                                    src="./uploads/<?= htmlspecialchars($user['name']) ?>" alt="Profile picture">
                                <input type="file" id="profilepic" name="profilepic" accept="image/*" class="hidden">
                            </div>
                        </div>


                        <br>

                        <div class="mb-4">
                            <label class="block font-bold">Full Name</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>"
                                class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl"
                                placeholder="Full name" required>
                        </div>
                        <!-- <div class="mb-4">
                            <label class="block font-bold">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Username" required>
                        </div> -->
                        <div class="mb-4">
                            <label class="block font-bold">Date of Birth</label>
                            <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>"
                                class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl pointer-events-auto "
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="block font-bold">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl"
                                placeholder="Email" required>
                        </div>
                        <div class="mb-4">

                            <label class="block font-bold">Password</label>
                            <div class="relative text-gray-600 focus-within:text-gray-400">
                                <input type="password" id="password" name="password"
                                    class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl"
                                    placeholder="Password" aria-describedby="password-addon" required>
                                <span class="absolute inset-y-0 left-[340px] pl-3 flex items-center pointer-events-auto">
                                    <button type="button" id="show-password-btn" class="btn btn-outline-secondary"
                                        aria-label="Show password">
                                        <i class="fa fa-eye text-violet-950 hover:text-violet-600" aria-hidden="true"></i>
                                    </button>
                                </span>
                            </div>

                        </div>

                        <script>
                            var passwordInput = document.getElementById("password");
                            var showPasswordBtn = document.getElementById("show-password-btn");

                            showPasswordBtn.addEventListener("click", function () {
                                if (passwordInput.type === "password") {
                                    passwordInput.type = "text";
                                    showPasswordBtn.innerHTML = '<i class="fa fa-eye-slash text-violet-950 hover:text-violet-600" aria-hidden="true"></i>';
                                } else {
                                    passwordInput.type = "password";
                                    showPasswordBtn.innerHTML = '<i class="fa fa-eye text-violet-950 hover:text-violet-600" aria-hidden="true"></i>';
                                }
                            });
                        </script>
                        <div class="mb-4">
                            <label class="block font-bold">Confirm Password</label>
                            <input type="password" name="confirm_password"
                                class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl"
                                placeholder="Confirm password">
                        </div>
                        <div class="mb-4">
                            <label class="block font-bold">Bio</label>
                            <textarea name="bio"
                                class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-xl focus:outline-none focus:drop-shadow-xl"
                                placeholder="Write something..."><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>
                        <div>

                            <button type="submit"
                                class="bg-violet-500 hover:bg-violet-900 duration-300 text-white py-2 mt-2  px-4 rounded-full">
                                Update
                            </button>
                        </div>
                    </form>
                    <script>
                        var profilePicture = document.getElementById("profile-picture");
                        var profilePicInput = document.getElementById("profilepic");

                        profilePicture.addEventListener("click", function () {
                            profilePicInput.click();
                        });
                    </script>

                </div>
                <?php
                // Close the database connection
                $pdo = null;
                ?>
            </div>
            <!--Webtical trending & search-->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </body>

    </html>
    <?php

} else {
    header('Location: index.php');
}
?>