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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./js/like.js"></script>
        <script src="./js/delete.js"></script>
        <link rel="stylesheet" href="./style/lightbox.css">
        <script src="./js/lightbox.js"></script>
    </head>

    <body>
        <div class="flex flex-row ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-1/2  max-[970px]:basis-full p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold min-h-screen">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">
                        Home
                    </span>
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="pt-4"></div>
                <div class="border border-gray-400 "></div>
                <div class="flex pt-4">
                    <div>
                        <img src="./uploads/<?php echo $profilepic; ?>" alt="" class="rounded-full w-14 h-auto">
                    </div>
                    <form action="postHundler.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="username" value="<?php echo $username; ?>">
                        <!-- <input type="text" name="content" class="focus:outline-none placeholder:text-lg placeholder:text-gray-400 placeholder:italic w-96 rounded-full pl-2 p-4 ml-4" placeholder="what's happening?" required> -->
                        <input type="text" name="content" class="focus:outline-none placeholder:text-lg placeholder:text-gray-400 placeholder:italic w-96 h-auto rounded-full pl-2 p-4 ml-4 " placeholder="What's happening?" required>
                </div>
                <div class="pt-2"></div>
                <div class="grid pl-14 divide-y">
                    <div class="flex pt-3 mx-4  justify-between">
                        <div class="flex space-x-2">
                            <label for="file-upload" class="flex items-center px-4 py-2 bg-gray-200  rounded-lg hover:bg-gray-300 cursor-pointer ">
                                <i class="fas fa-image cursor-pointer"></i>
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
                        <div class="flex pt-2 space-x-2 absolute">
                            <div>
                                <img src="./uploads/<?php echo $post['name']; ?>" alt="" class="rounded-full w-14 h-auto">
                            </div>
                            <span class="font-semibold"><a href="showprofile.php?id=<?php echo $post['username']; ?>"><?php echo $post['fullname'] ?></a></span>
                            <span class="font-thin"><em>@</em>
                                <?php echo $post['username']; ?>
                            </span>
                            <span class="font-light italic max-[600px]:text-sm max-[600px]:mt-[2px]">
                                <?php echo $post['datePub']; ?>
                            </span>
                            <!-- <span class="options-link"><i id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="fas fa-ellipsis-h cursor-pointer "></i></span> -->
                            <!-- Contenu de votre page home.php -->
                            <?php
                            $isAuthenticated = isset($_SESSION['username']);
                            ?>
                            <?php if ($isAuthenticated) { ?>
                                <span class="options-link  ">
                                    <i id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="fas fa-ellipsis-h cursor-pointer max-[600px]:break-words"></i>
                                </span>
                                <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-40 h-auto dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                                        <li>
                                            <a href="deletePost.php?idPub=<?php echo $idPub; ?>" class="block px-2 py-2 hover:bg-gray-100 rounded-md dark:hover:bg-gray-600 dark:hover:text-white">
                                                <i class="fa-solid fa-trash text-violet-500"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                    <script>
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

                        <div class="pl-14 grid flex">
                            <span class="w-auto h-auto ml-4 mt-14  break-words max-[600px]:max-w-sm">
                                <?php echo $post['contenuPub']; ?>
                            </span>
                            <div class="rounded-md p-4">
                                <div class="flex">
                                    <?php if ($post['image']) { ?>
                                        <img src="uploads/<?php echo $post['image']; ?>" class="post-image rounded-lg w-auto h-auto cursor-pointer" onclick="openLightbox(event, this)">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="flex justify-between p-4">
                                <a href="share.php?idPub=<?php echo $idPub; ?>" class="share-btn">
                                    <i class="fi fi-rr-share-square hover:bg-gray-300 rounded-full py-3 px-3"></i>
                                    <?php
                                    $query = $db->query("SELECT COUNT(*) FROM shares WHERE post_id = '" . $idPub . "'");
                                    echo $query->fetchColumn();
                                    ?>
                                </a>
                                <a href="post.php?idPub=<?php echo $idPub; ?>">
                                    <i class="fi fi-rr-comments hover:bg-gray-300 rounded-full py-3 px-3 "></i>
                                    <span class="" id="show_comments<?php echo $idPub; ?>">
                                        <?php
                                        $query = $db->query("SELECT COUNT(*) FROM comments WHERE idPub = '" . $idPub . "'");
                                        echo $query->fetchColumn();
                                        ?>
                                    </span>
                                </a>
                                <a class="">
                                    <?php
                                    $stmt = $db->prepare("SELECT * FROM likes WHERE idPub = :idPub AND username = :username");
                                    $stmt->execute(array(':idPub' => $idPub, ':username' => $_SESSION['username']));
                                    if ($stmt->rowCount() > 0) {
                                    ?>
                                        <button value="<?php echo $idPub; ?>" class="unlike text-gray-700 font-medium hover:bg-gray-300 rounded-full ">Unlike</button>
                                    <?php } else { ?>
                                        <button value="<?php echo $idPub; ?>" class="like text-gray-700 font-medium hover:bg-gray-300 rounded-full ">Like</button>
                                    <?php } ?>
                                    <span id="show_like<?php echo $idPub; ?>">
                                        <?php
                                        $query3 = $db->query("SELECT COUNT(*) FROM likes WHERE idPub = '" . $idPub . "'");
                                        echo $query3->fetchColumn();
                                        ?>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
                            <img id="lightboxImage" src="" alt="Full-size Image" class="">
                        </div>

                    </div>
                    <div class="pt-2"></div>
                    <div class="border border-gray-400 "></div>
                <?php
                }
                ?>
            </div>
            <div class="hidden min-[970px]:block">
                <!--Webtical trending & search-->
                <?php include 'layouts/footer.php'; ?>
            </div>
        </div>

    </body>

    </html>
<?php
} else {
    header('Location: index.php');
}
?>