<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $username = $_SESSION['username'];

    $selUtilisateur = $db->prepare('SELECT * FROM utilisateur WHERE username = :username');
    $selUtilisateur->bindParam(':username', $username);
    $selUtilisateur->execute();

    // Vérifier si la requête a retourné des résultats
    if ($selUtilisateur->rowCount() > 0) {
        $user = $selUtilisateur->fetch(PDO::FETCH_ASSOC);

        $username = $user['username'];
        $profilepic = $user['name'];
        $fullname = $user['fullname'];

        $selPub = $db->prepare('SELECT * FROM utilisateur JOIN publication ON utilisateur.username = publication.username ORDER BY datePub DESC');
        $selPub->execute();
        $posts = $selPub->fetchAll(PDO::FETCH_ASSOC);
    }
    $query = $db->prepare('SELECT * FROM trends ORDER BY count DESC LIMIT 5');
    $query->execute();
    $trends = $query->fetchAll(PDO::FETCH_ASSOC);
?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Webtical</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- <script src="https://use.fontawesome.com/fe459689b4.js"></script> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./js/like.js"></script>
        <script src="./js/delete.js"></script>


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

    <body></body>
    <div class="flex flex-row ">
        <!--Webtical links-->
        <div class="basis-1/5 p-3 bg-gray-100 rounded-md shadow-md text-black font-semibold min-h-screen ">
            <div class="grid space-y-8 space-x-4 justify-center fixed">
                <div class="pl-4 inline-flex justify-center">
                    <div>
                        <a href="#"><img src="./img/LOGO.png" alt="" class="w-24"></a>

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
                    Home
                </span>
                <i class="fa-solid fa-house"></i>
            </div>
            <div class="flex pt-2">
                <div>
                    <img src="./uploads/<?php echo $profilepic; ?>" alt="" class="rounded-full w-14 h-auto">
                </div>
                <form action="postHundler.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="username" value="<?php echo $username; ?>">
                    <!-- <input type="text" name="content" class="focus:outline-none placeholder:text-lg placeholder:text-gray-400 placeholder:italic w-96 rounded-full pl-2 p-4 ml-4" placeholder="what's happening?" required> -->
                    <input type="text" name="content" class="focus:outline-none placeholder:text-lg placeholder:text-gray-400 placeholder:italic w-96 rounded-full pl-2 p-4 ml-4 emoji-input" placeholder="What's happening?" required>

            </div>



            <div class="pt-2"></div>

            <div class="grid pl-14 divide-y">
                <div class="flex pt-3 mx-4  justify-between">
                    <div class="flex space-x-2">
                        <label for="file-upload" class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 cursor-pointer ">
                            <!-- <img src="./img/image-photography-icon.svg" alt="" class="w-6 h-6 mr-2 rounded-sm"> -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="add-image" class="w-6 h-6  ">
                                <path d="M19,10a1,1,0,0,0-1,1v3.38L16.52,12.9a2.79,2.79,0,0,0-3.93,0l-.7.71L9.41,11.12a2.79,2.79,0,0,0-3.93,0L4,12.61V7A1,1,0,0,1,5,6h8a1,1,0,0,0,0-2H5A3,3,0,0,0,2,7V19.22A2.79,2.79,0,0,0,4.78,22H17.22a2.88,2.88,0,0,0,.8-.12h0a2.74,2.74,0,0,0,2-2.65V11A1,1,0,0,0,19,10ZM5,20a1,1,0,0,1-1-1V15.43l2.89-2.89a.78.78,0,0,1,1.1,0L15.46,20Zm13-1a1,1,0,0,1-.18.54L13.3,15l.71-.7a.77.77,0,0,1,1.1,0L18,17.21ZM21,4H20V3a1,1,0,0,0-2,0V4H17a1,1,0,0,0,0,2h1V7a1,1,0,0,0,2,0V6h1a1,1,0,0,0,0-2Z"></path>
                            </svg>
                            <!-- <span>Choose a file</span> -->
                        </label>
                        <input id="file-upload" type="file" name="image" class="hidden">
                    </div>
                    <div class="flex space-x-2">
                        <div id="emoji-picker"></div>

                    </div>
                    <div class="">
                        <button name="ok" class="rounded-full  text-white h-10 w-20 bg-teal-500 hover:bg-teal-700 duration-150">Post</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="pt-2"></div>
            <div class="border border-gray-400 "></div>
            <!--content-->

            <!-- post div -->

            <?php
            foreach ($posts as $post) {
                // $username = $post['username'];
                $idPub = $post['idPub'];
            ?>
                <div id="post" class="post">
                    <script src="./js/delete.js"></script>
                    <div class="flex pt-2 space-x-2 absolute">
                        <div>
                            <img src="./uploads/<?php echo $post['name']; ?>" alt="" class="rounded-full w-14">
                        </div>
                        <span class="font-semibold"><?php echo $post['fullname'] ?></span>
                        <span class="font-thin"><em>@</em><?php echo $post['username']; ?></span>
                        <span class="font-light"><?php echo $post['datePub']; ?></span>
                        <!-- <span class="options-link"><i id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="fas fa-ellipsis-h cursor-pointer "></i></span> -->

                        <!-- Contenu de votre page home.php -->

                        <?php
                        $isAuthenticated = isset($_SESSION['username']);
                        ?>
                        <?php if ($isAuthenticated) { ?>
                            <span class="options-link">
                                <i id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="fas fa-ellipsis-h cursor-pointer"></i>
                            </span>

                            <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-40 h-auto dark:bg-gray-700">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                                    <li>
                                        <a href="deletePost.php?idPub=<?php echo $idPub; ?>" class="block px-2 py-2 hover:bg-gray-100 rounded-md dark:hover:bg-gray-600 dark:hover:text-white">
                                            <i class="fa-solid fa-trash text-violet-500"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                                <script src="./js/dropdown.js">
                                    var dropdownButton = document.getElementById('dropdownDefaultButton');
                                    var dropdown = document.getElementById('dropdown');
                                    var isAuthenticated = <?php echo $isAuthenticated ? 'true' : 'false'; ?>;

                                    dropdownButton.addEventListener('click', function() {
                                        if (isAuthenticated) {
                                            dropdown.classList.toggle('hidden');
                                        }
                                    });

                                    window.addEventListener('click', function(event) {
                                        if (!dropdown.contains(event.target) && event.target !== dropdownButton) {
                                            dropdown.classList.add('hidden');
                                        }
                                    });
                                </script>
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                    <div class="pl-14 grid ">
                        <br>
                        <br>
                        <span class="ml-4"><?php echo $post['contenuPub']; ?></span>
                        <div class="rounded-md  p-4">
                            <div class="flex space-x-3">

                                <?php
                                if ($post['image']) {
                                    echo '<img src="uploads/' . $post['image'] . '" class="rounded-lg w-96 h-auto ml-14" >';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="flex justify-between my-0  p-4">&nbsp;
                            <i class="fi fi-rr-share-square ml-20"></i>
                            <!-- <i class="fa-solid fa-share text-violet-950 hover:text-violet-600 duration-300"></i> -->
                            <a href="post.php?idPub=<?php echo $idPub; ?>"><i class="fi fi-rr-comments"></i>
                                <span class="" id="show_comments<?php echo $idPub; ?>">
                                    <?php
                                    $query = $db->query("SELECT COUNT(*) FROM comments WHERE idPub = '" . $idPub . "'");
                                    echo $query->fetchColumn();
                                    ?>
                                </span>
                            </a>
                            <a class="mr-28">
                                <!-- <i class="fi fi-rr-heart "></i> -->
                                <?php
                                $stmt = $db->prepare("SELECT * FROM likes WHERE idPub = :idPub AND username = :username");
                                $stmt->execute(array(':idPub' => $idPub, ':username' => $_SESSION['username']));
                                if ($stmt->rowCount() > 0) {
                                ?>
                                    <button value="<?php echo $idPub; ?>" class="unlike text-gray-700 font-medium">Unlike</button>
                                <?php
                                } else {
                                ?>
                                    <button value="<?php echo $idPub; ?>" class="like text-gray-700 font-medium">Like</button>
                                <?php } ?>
                                <span id="show_like<?php echo $idPub; ?>">
                                    <?php
                                    $query3 = $db->query("SELECT COUNT(*) FROM likes WHERE idPub = '" . $idPub . "'");
                                    echo $query3->fetchColumn();
                                    ?>
                                </span>
                            </a>&nbsp;
                        </div>
                    </div>
                </div>
                <div class="pt-2"></div>
                <div class="border border-gray-400 "></div>
            <?php
            }
            ?>

        </div>
        <!--Webtical trending & search-->
        <div class="basis-1/4 p-4 bg-gray-300 rounded-md shadow-md text-black font-semibold h-fit">
            <div class="relative text-gray-600 focus-withi:text-gray-400">
                <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                    <button type="submit" class="p-1 focus:outline-none focus:border-indigo-500/100  focus:shadow-outline">
                        <i class="fi fi-rr-search h-6 w-6 text-violet-950 hover:text-violet-700 duration-300"></i>
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
                            <span class="font-bold  text-sm"><?php echo $trend['hashtag'];?></span>
                            <span class="font-thin text-sm"><?php echo $trend['count'];?> Posts</span>
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
                }?>
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
                                <script src="./js/delete.js"></script>
                            </div>
                            <div class="border border-gray-200"></div>
                        <?php endforeach; ?>
                    </div>


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