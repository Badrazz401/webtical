<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'], $_GET['idPub'])) {

    require("config/connexion.php");

    $username = $_SESSION['username'];

    $idPub = $_GET['idPub'];

    $selUtilisateur = $db->prepare('SELECT * FROM utilisateur WHERE username =:username');
    $selUtilisateur->bindParam(':username', $username);

    $selUtilisateur->execute();

    $user = $selUtilisateur->fetch(PDO::FETCH_ASSOC);
    // if(is_array($user)){
    $username = $user['username'];
    $profilepic = $user['name'];
    $fullname = $user['fullname'];

    // Prepare a SQL statement to select the user and post data for the specified idPub
    $sql = "SELECT utilisateur.*, publication.* FROM utilisateur JOIN publication ON utilisateur.username = publication.username WHERE publication.idPub = :idPub";

    // Prepare the statement
    $stmt = $db->prepare($sql);


    // Bind the idPub parameter
    $stmt->bindParam(':idPub', $idPub, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch the results as an associative array
    $post = $stmt->fetch(PDO::FETCH_ASSOC);


    $query = $db->prepare('SELECT comments.*, utilisateur.* 
    FROM comments 
    INNER JOIN utilisateur ON comments.username = utilisateur.username 
    WHERE comments.idPub = :idPub
    ORDER BY dateComment DESC
    ');
    $query->execute(array(':idPub' => $idPub));
    $comments = $query->fetchAll(PDO::FETCH_ASSOC);

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
        <link href="https://cdn.tailwindcss.com/versions/2.2.7/@tailwindcss/postcss7-compat" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/fe459689b4.js"></script>
        <script src="./js/like.js"></script>
        <link rel="stylesheet" href="./style/lightbox.css">
        <script src="./js/lightbox.js"></script>
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
        <script src="/js/like.js"></script>
        <div class="flex flex-row ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-1/2 max-[800px]:basis-full p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold min-h-screen">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">
                        Home
                    </span>
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="pt-4"></div>
                <div class="border border-gray-400 "></div>
                <div class="flex pt-2">
                    <div>
                        <img src="./uploads/<?php echo $profilepic; ?>" alt="" class="rounded-full w-14">
                    </div>
                    <form action="postHundler.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="username" value="<?php echo $username; ?>">
                        <input type="text" name="content" class="focus:outline-none placeholder:text-lg placeholder:text-gray-400 placeholder:italic w-96 rounded-full pl-2 p-4 ml-4" placeholder="what's happening?" required>
                </div>
                <div class="pt-2"></div>
                <div class="grid pl-14 divide-y">
                    <div class="flex pt-3 mx-4  justify-between">
                        <div class="flex space-x-2">
                            <label for="file-upload" class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 cursor-pointer ">
                                <i class="fas fa-image cursor-pointer"></i>
                            </label>
                            <input id="file-upload" type="file" name="image" class="hidden">
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
                <div id="post" class="post">
                    <div class="flex pt-2 space-x-2 absolute">
                        <div>
                            <img src="./uploads/<?php echo $post['name']; ?>" alt="" class="rounded-full w-14">
                        </div>
                        <span class="font-semibold"><a href="showprofile.php?id=<?php echo $post['username']; ?>"><?php echo $post['fullname'] ?></a></span>

                        <span class="font-thin"><em>@</em>
                            <?php echo $post['username']; ?>
                        </span>
                        <span class="font-light italic max-[600px]:text-sm max-[600px]:mt-[2px]">
                            <?php echo $post['datePub']; ?>
                        </span>

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
                        <?php }; ?>

                    </div>
                    <div class="pl-14 grid flex">
                        <span class="max-w-xl ml-4 mt-14 h-auto break-words max-[600px]:max-w-sm">
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
                                <i class="fi fi-rr-share-square hover:bg-gray-300 rounded-full py-3 px-3 "></i>
                                <?php
                                $query = $db->query("SELECT COUNT(*) FROM shares WHERE post_id = '" . $idPub . "'");
                                echo $query->fetchColumn();
                                ?>
                            </a>
                            <a href="post.php?idPub=<?php echo $idPub; ?>">
                                <i class="fi fi-rr-comments hover:bg-gray-300 rounded-full py-3 px-3"></i>
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
                                    <button value="<?php echo $idPub; ?>" class="like text-gray-700 font-medium hover:bg-gray-300 rounded-full  ">Like</button>
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
                <div class="flex pt-4 ">
                    <div>
                        <img src="./uploads/<?php echo $profilepic; ?>" alt="" class="rounded-full w-14 h-auto flex justify-center">
                    </div>
                    <form action="comment.php" method="post">

                        <input type="hidden" name="idPub" value="<?php echo $idPub; ?>">
                        <input type="text" name="comment" class="w-96 focus:outline-none focus:border-indigo-500/100 placeholder:text-medium placeholder:text-gray-400 placeholder:italic  rounded-full pl-2 p-4 ml-4 max-[1176px]:w-auto h-12" placeholder="Add a comment ..." required>
                        <button name="ok" class="rounded-full  text-white p-2 bg-teal-500 hover:bg-teal-700 duration-150">Comment</button>
                    </form>
                </div>
                <div class="pt-2"></div>
                <div class="grid pl-14 divide-y">
                    <?php
                    foreach ($comments as $comment) {
                    ?>
                        <div id="post" class="post relative border-2 border-gray-200 w-auto h-24 min-h-full  rounded-md ">
                            <div class="flex space-x-2 ">
                                <div>
                                    <img src="./uploads/<?php echo $comment['name']; ?>" alt="" class="rounded-full w-10 h-auto">
                                </div>
                                <span class="font-semibold text-medium max-[1176px]:text-sm "><a href="showprofile.php?id=<?php echo $comment['username']; ?>"><?php echo $comment['fullname'] ?></a></span>

                                <span class="font-thin max-[1176px]:text-sm"><em>@</em>
                                    <?php echo $comment['username']; ?>
                                </span>
                                <span class="font-light italic max-[1176px]:text-sm">
                                    <?php echo $comment['dateComment']; ?>
                                </span>
                            </div>
                            <div class="pl-14 ml-2 -mt-2  space-y-2  overflow-hidden">
                                <p class="absolute right-0 left-12 text-base mr-4 overflow-x-auto overflow-y-auto ">
                                    <?php echo $comment['contenuComment']; ?>
                                </p>
                            </div>

                        </div>
                        <!-- <div class="pt-2"></div> -->
                        <!-- <div class="border border-gray-200 "></div> -->
                    <?php
                    }
                    ?>
                    <!-- <div class="flex pt-3 mx-4  justify-between">
                        <div class="">
                        </div>
                    </div> -->

                </div>
                <div class="pt-4"></div>
                <div class="border border-gray-200 "></div>

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