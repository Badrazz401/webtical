<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");

    $message = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['fullname']) &&  !empty($_POST['dob']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['bio'])) {
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
                        ':name' => $name, // Use the variable containing the file name
                        ':image' => $image, // Use the variable containing the image encoded in base64
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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
            <div class="basis-1/5 p-3 bg-gray-100 rounded-md shadow-md text-black font-semibold min-h-screen ">
                <div class="grid space-y-8 space-x-4 justify-center fixed">
                    <div class="pl-4 inline-flex">
                        <div>
                            <a href="#"><img src="./img/LOGO.png" alt="" class="w-24 ml-3"></a>
                        </div>
                    </div>
                    <div class="flex space-x-4 ">
                        <a href="home.php" class="p-4 flex justify-between  font-medium text-lg text-black  hover:bg-gray-700 rounded-full hover:text-white duration-300 ">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                            </svg>
                            <div class="px-2 ">
                                <span class="text-xl  "> Home</span>
                            </div>
                        </a>
                    </div>
                    <div class="flex space-x-4">
                        <div>
                            <a href="profile.php" class="p-4 flex justify-between items-center font-medium text-lg text-black  hover:bg-gray-700 rounded-full hover:text-white duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="px-2">
                                    <span class="text-xl">Profile</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <a href="notification.php" class="p-4 flex justify-between items-center font-medium text-lg text-black  hover:bg-gray-700 rounded-full hover:text-white duration-300 ">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.118 1 8.22 8.22 0 011.987 4.124.75.75 0 001.48-.248A9.72 9.72 0 0019.266 2.5z" />
                                <path fill-rule="evenodd" d="M12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z" clip-rule="evenodd" />
                            </svg>

                            <div class="px-2">
                                <span class="text-xl">Notifications</span>
                            </div>
                        </a>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#" class="p-4 flex justify-between items-center font-medium text-lg text-black  hover:bg-gray-700 rounded-full hover:text-white duration-300 ">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" />
                            </svg>
                            <div class="px-2">
                                <span class="text-xl">Settings</span>
                            </div>
                        </a>
                    </div>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-full m-4 shadow-md">Post</button>
                    <button class="bg-violet-500 mt-auto md:mt-0 sm:mt-0  hover:bg-violet-700 text-white font-bold py-2 px-4 rounded-full m-4 shadow-md"><i class="fa-solid fa-right-from-bracket"></i><a href="logout.php"> Logout</a></button>

                </div>

            </div>
            <!--Webtical main-->
            <div class="basis-1/2 p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold">
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

                    <?php if (!empty($message)) : ?>
                        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
                            <div class="flex">
                                <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                    </svg></div>
                                <div>

                                    <p class="font-bold"><?= $message ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <br>
                    <form method="POST" enctype="multipart/form-data" class="">
                        <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 h-64 border border-gray-400 rounded-xl"></div>

                        <div class="flex justify-center border border-gray-400 rounded-lg w-full mx-auto">

                            <div id="postCount" class="flex flex-col items-center">
                                <span class="font-bold text-xl"><?= $publicationCount ?></span>
                                <span class="text-gray-600">Posts</span>
                            </div>
                            <div id="followerCount" class="flex flex-col items-center mx-4">
                                <span class="font-bold text-xl"><?= $followerCount ?></span>
                                <span class="text-gray-600">Followers</span>
                            </div>
                            <div id="followingCount" class="flex flex-col items-center">
                                <span class="font-bold text-xl"><?= $followingCount ?></span>
                                <span class="text-gray-600">Following</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="flex items-center  mt-[-8rem]">
                                <img id="profile-picture" class="w-32 h-32 rounded-full border-2  border-gray-400 cursor-pointer shadow-lg" src="./uploads/<?= htmlspecialchars($user['name']) ?>" alt="Profile picture">
                                <input type="file" id="profilepic" name="profilepic" accept="image/*" class="hidden">
                            </div>
                        </div>


                        <br>

                        <div class="mb-4">
                            <label class="block font-bold">Full Name</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Full name" required>
                        </div>
                        <!-- <div class="mb-4">
                            <label class="block font-bold">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Username" required>
                        </div> -->
                        <div class="mb-4">
                            <label class="block font-bold">Date of Birth</label>
                            <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl cursor-pointer " required>
                        </div>
                        <div class="mb-4">
                            <label class="block font-bold">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Email" required>
                        </div>
                        <div class="mb-4">

                            <label class="block font-bold">Password</label>
                            <div class="relative text-gray-600 focus-withi:text-gray-400">
                                <input type="password" id="password" name="password" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Password" aria-describedby="password-addon" required>
                                <span class="absolute inset-y-0 left-80 flex items-center pl-2">

                                    <button type="button" id="show-password-btn" class="btn btn-outline-secondary " aria-label="Show password">
                                        <i class="fa fa-eye text-violet-950 hover:text-violet-600" aria-hidden="true"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <script>
                            var passwordInput = document.getElementById("password");
                            var showPasswordBtn = document.getElementById("show-password-btn");

                            showPasswordBtn.addEventListener("click", function() {
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
                            <input type="password" name="confirm_password" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-full focus:outline-none focus:drop-shadow-xl" placeholder="Confirm password">
                        </div>
                        <div class="mb-4">
                            <label class="block font-bold">Bio</label>
                            <textarea name="bio" class="block w-96 border-2 focus:border-indigo-500/100 p-2 mt-2 py-3 rounded-xl focus:outline-none focus:drop-shadow-xl" placeholder="Write something..."><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>
                        <div>
                            <button type="submit" class="bg-violet-500 hover:bg-violet-900 duration-300 text-white py-2 mt-2  px-4 rounded-full">Update</button>
                        </div>
                    </form>
                    <script>
                        var profilePicture = document.getElementById("profile-picture");
                        var profilePicInput = document.getElementById("profilepic");

                        profilePicture.addEventListener("click", function() {
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
            <div class="basis-1/4 p-4 bg-gray-300 rounded-md shadow-md text-black font-semibold h-fit">
                <div class="relative text-gray-600 focus-withi:text-gray-400">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <button type="submit" class="p-1 focus:outline-none focus:shadow-outline">
                            <i class="fa-solid fa-magnifying-glass h-6 w-6 text-violet-950 hover:text-violet-700 duration-300"></i>
                        </button>
                    </span>
                    <input type="text" placeholder="Search" class="placeholder:italic py-4 rounded-full text-sm text-white  pl-10 focus:outline-none  focus:text-black w-full">
                </div>
                <div class="pt-3"></div>
                <div class="rounded-lg bg-white p-4">
                    <div class="flex justify-between">
                        <span class="text-xl text-black underline-offset-4">Trends fro you</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-violet-950 ">
                            <path fill-rule="evenodd" d="M11.097 1.515a.75.75 0 01.589.882L10.666 7.5h4.47l1.079-5.397a.75.75 0 111.47.294L16.665 7.5h3.585a.75.75 0 010 1.5h-3.885l-1.2 6h3.585a.75.75 0 010 1.5h-3.885l-1.08 5.397a.75.75 0 11-1.47-.294l1.02-5.103h-4.47l-1.08 5.397a.75.75 0 01-1.47-.294l1.02-5.103H3.75a.75.75 0 110-1.5h3.885l1.2-6H5.25a.75.75 0 010-1.5h3.885l1.08-5.397a.75.75 0 01.882-.588zM10.365 9l-1.2 6h4.47l1.2-6h-4.47z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="pt-4"></div>
                    <div class="border border-gray-200 "></div>
                    <div class="pt-4"></div>
                    <?php
                    foreach ($trends as $trend) {
                    ?>
                        <div class="grid space-y-6">
                            <div class="flex justify-between">
                                <div class="grid">
                                    <!-- <span class="font-thin italic text-sm">Sports. Trending</span> -->
                                    <span class="font-bold  text-sm"><?php echo $trend['hashtag']; ?></span>
                                    <span class="font-thin text-sm"><?php echo $trend['count']; ?> Posts</span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5  text-violet-950 ">
                                        <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <br>
                    <?php
                    } ?>
                </div>
                <div class="pt-4"></div>
                <div class="rounded-lg bg-white p-4">

                    <?php
                    require('config/connexion.php');
                    // Fetch users from database
                    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username != :current_user");
                    $stmt->execute(['current_user' => $_SESSION['username']]);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="rounded-lg bg-white p-4">
                        <div class="grid space-y-4">
                            <?php foreach ($users as $user) : ?>
                                <div class="flex justify-between space-x-2">
                                    <div class="flex space-x-2">
                                        <div>
                                            <img src="<?= $user['image'] ?>" alt="" class="rounded-full w-14">
                                        </div>
                                        <div class="grid">
                                            <span class="font-semibold"><?= $user['fullname'] ?></span>
                                            <span class="font-thin"><em>@</em><?= $user['username'] ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <?php
                                        // Check if the user is already being followed
                                        $stmt = $db->prepare("SELECT * FROM followers WHERE username = ? AND follower_user = ?");
                                        $stmt->execute([$_SESSION['username'], $user['username']]);
                                        $followed = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($followed) {
                                            // If the user is already being followed, show an unfollow button
                                            echo '<button type="button" class="rounded-full text-white bg-violet-500 hover:bg-violet-950 duration-300 h-10 w-20" onclick="unfollowUser(event, \'' . $user['username'] . '\')">Unfollow</button>';
                                        } else {
                                            // If the user is not being followed, show a follow button
                                            echo '<button type="button" class="rounded-full text-white bg-violet-500 hover:bg-violet-950 duration-300 h-10 w-20" onclick="followUser(event, \'' . $user['username'] . '\')">Follow</button>';
                                        }
                                        ?>
                                    </div>
                                    <script>
                                        function followUser(event, username) {
                                            event.preventDefault();
                                            var xhr = new XMLHttpRequest();
                                            xhr.onreadystatechange = function() {
                                                if (this.readyState == 4 && this.status == 200) {
                                                    // handle the response here, if necessary
                                                    // Update the button or perform any other desired actions
                                                    var button = event.target;
                                                    button.textContent = "Unfollow";
                                                    button.onclick = function(event) {
                                                        unfollowUser(event, username);
                                                    };
                                                }
                                            };
                                            xhr.open("POST", "follow.php", true);
                                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                            xhr.send("followed_user=" + username);
                                        }

                                        function unfollowUser(event, username) {
                                            event.preventDefault();
                                            var xhr = new XMLHttpRequest();
                                            xhr.onreadystatechange = function() {
                                                if (this.readyState == 4 && this.status == 200) {
                                                    // handle the response here, if necessary
                                                    // Update the button or perform any other desired actions
                                                    var button = event.target;
                                                    button.textContent = "Follow";
                                                    button.onclick = function(event) {
                                                        followUser(event, username);
                                                    };
                                                }
                                            };
                                            xhr.open("POST", "unfollow.php", true);
                                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                            xhr.send("unfollowed_user=" + username);
                                        }
                                    </script>


                                </div>
                                <div class="border border-gray-200"></div>
                            <?php endforeach; ?>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php

} else {
    header('Location: index.php');
}
?>