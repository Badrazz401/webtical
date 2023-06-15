<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $username = $_SESSION['username'];

    $selUtilisateur = $db->prepare('SELECT * FROM utilisateur WHERE username = :username');
    $selUtilisateur->bindParam(':username', $username);
    $selUtilisateur->execute();

    $query = $db->prepare('SELECT * FROM trends ORDER BY count DESC LIMIT 5');
    $query->execute();
    $trends = $query->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username != :current_user");
    $stmt->execute(['current_user' => $_SESSION['username']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT username FROM followers WHERE follower_user = :currentUsername";
    $stmt = $db->prepare($query);
    $stmt->execute([':currentUsername' => $_SESSION['username']]);
    $followers = $stmt->fetchAll(PDO::FETCH_COLUMN);


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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <body>
        <div class="flex flex-row ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-1/2 max-[970px]:basis-full p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold min-h-screen">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">
                        Friends
                    </span>
                    <svg fill="currentColor" stroke-width="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                        height="1em" width="1em" class="w-6 h-6" style="overflow: visible;">
                        <path
                            d="M60.44 389.17c0 .07 0 .2-.08.38.03-.12.05-.25.08-.38ZM439.9 405.6a26.77 26.77 0 0 1-9.59-2l-56.78-20.13-.42-.17a9.88 9.88 0 0 0-3.91-.76 10.32 10.32 0 0 0-3.62.66c-1.38.52-13.81 5.19-26.85 8.77-7.07 1.94-31.68 8.27-51.43 8.27-50.48 0-97.68-19.4-132.89-54.63A183.38 183.38 0 0 1 100.3 215.1a175.9 175.9 0 0 1 4.06-37.58c8.79-40.62 32.07-77.57 65.55-104A194.76 194.76 0 0 1 290.3 32c52.21 0 100.86 20 137 56.18 34.16 34.27 52.88 79.33 52.73 126.87a177.86 177.86 0 0 1-30.3 99.15l-.19.28-.74 1c-.17.23-.34.45-.5.68l-.15.27a21.63 21.63 0 0 0-1.08 2.09l15.74 55.94a26.42 26.42 0 0 1 1.12 7.11 24 24 0 0 1-24.03 24.03Z">
                        </path>
                        <path
                            d="M299.87 425.39a15.74 15.74 0 0 0-10.29-8.1c-5.78-1.53-12.52-1.27-17.67-1.65a201.78 201.78 0 0 1-128.82-58.75A199.21 199.21 0 0 1 86.4 244.16C85 234.42 85 232 85 232a16 16 0 0 0-28-10.58s-7.88 8.58-11.6 17.19a162.09 162.09 0 0 0 11 150.06C59 393 59 395 58.42 399.5c-2.73 14.11-7.51 39-10 51.91a24 24 0 0 0 8 22.92l.46.39A24.34 24.34 0 0 0 72 480a23.42 23.42 0 0 0 9-1.79l53.51-20.65a8.05 8.05 0 0 1 5.72 0c21.07 7.84 43 12 63.78 12a176 176 0 0 0 74.91-16.66c5.46-2.56 14-5.34 19-11.12a15 15 0 0 0 1.95-16.39Z">
                        </path>
                    </svg>
                </div>
                <div class="flex pt-2">
                </div>
                <div class="pt-2"></div>
                <div class="border border-gray-400 "></div>
                <br>
                <?php if (count($followers) > 0) {
                    // Step 5: Display or use the retrieved follower usernames as needed
                    foreach ($followers as $followerUsername) {
                        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username = :followerUsername");
                        $stmt->execute([':followerUsername' => $followerUsername]);
                        $follower = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($follower) { ?>
                            <div class="flex flex-col sm:flex-row justify-between space-x-2 p-5">
                                <div class="flex space-x-2">
                                    <div>
                                        <img src="<?= $follower['image'] ?>" alt=""
                                            class="rounded-full w-14 h-auto  sm:object-contain">
                                    </div>
                                    <div class="grid">
                                        <span class="font-semibold">
                                            <?= $follower['fullname'] ?>
                                        </span>
                                        <span class="font-thin"><em>@</em>
                                            <?= $follower['username'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="flex items-center ">
                                        <a href="chating.php?recipient=<?= $follower['username'] ?>">
                                            <button
                                                class="rounded-full text-white bg-violet-500 hover:bg-violet-950 duration-300 h-10 w-20 px-4">Chat</button>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2"></div>
                            <div class="border border-gray-200"></div>

                        <?php }
                    }
                } else { ?>
                    <p>No utilisateur trouvé</p>
                <?php } ?>
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