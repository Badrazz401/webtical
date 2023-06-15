<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {

    require("config/connexion.php");

    $username = $_SESSION['username'];

    $selUtilisateur = $db->prepare('SELECT * FROM utilisateur WHERE username =:username');
    $selUtilisateur->bindParam(':username', $username);

    $selUtilisateur->execute();

    $user = $selUtilisateur->fetch(PDO::FETCH_ASSOC);

    $username = $user['username'];
    // $profilepic = $user['profilepic'];
    $fullname = $user['fullname'];

    $query = $db->prepare('SELECT notifications.*, utilisateur.*, publication.contenuPub 
    FROM notifications 
    INNER JOIN utilisateur ON notifications.username = utilisateur.username 
    INNER JOIN publication ON notifications.idPub = publication.idPub AND publication.username = ?
    ORDER BY notifications.dateNotif DESC');

    $query->execute(array($_SESSION['username']));
    $result1 = $query->fetchAll(PDO::FETCH_ASSOC);

    $query2 = $db->prepare('SELECT * FROM notifications WHERE following_user = ? ORDER BY dateNotif DESC');
    $query2->execute(array($_SESSION['username']));
    $result2 = $query2->fetchAll(PDO::FETCH_ASSOC);

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
        <!-- <link href="https://cdn.tailwindcss.com/versions/2.2.7/@tailwindcss/postcss7-compat" rel="stylesheet"> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" /> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- <script src="https://use.fontawesome.com/fe459689b4.js"></script> -->
        <!-- <script src="./js/like.js"></script> -->

    </head>

    <body>
        <div class="flex flex-row ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-1/2 max-[970px]:basis-full p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold min-h-screen ">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">
                        Notification
                    </span>
                    <i class="fi fi-ss-bell"></i>
                </div>
                <div class="pt-4"></div>
                <div class="border border-gray-400 "></div>
                <!--content-->

                <!-- notification div -->
                <!-- <div class=> -->
                <div class="ml-4 mt-4">
                    <?php
                    $result = array_merge($result1, $result2);

                    foreach ($result as $notif) {

                        if ($notif['type'] == "like") {
                            echo "<i class='fi fi-rr-heart m-6  text-lg '></i><span class='text-gray-600' >" . $notif['username'] . " liked your post at <u>" . $notif['dateNotif'] . '</u></span><br>';
                        } elseif ($notif['type'] == "comment") {
                            echo "<i class='fi fi-rr-comments m-6  text-lg '></i><span class='text-gray-600' >" . $notif['username'] . " commented on your post at <u>" . $notif['dateNotif'] . '</u></span><br>';
                        } elseif ($notif['type'] == "follow") {
                            echo "<i class='fi fi-rr-following m-6  text-lg '></i><span class='text-gray-600' >" . $notif['username'] . " followed you at <u>" . $notif['dateNotif'] . '</u></span><br>';
                        } elseif ($notif['type'] == "unfollow") {
                            echo "<i class='fi fi-rr-delete-user m-6  text-lg '></i><span class='text-gray-600' >" . $notif['username'] . " unfollowed you at <u>" . $notif['dateNotif'] . '</u></span><br>';
                        } else {
                            echo "<i class='fi fi-rr-share-square m-6  text-lg '></i><span class='text-gray-600' >" . $notif['username'] . " shared your post at <u>" . $notif['dateNotif'] . '</u></span><br>';
                        }
                        ?>
                        <div class="py-4 "></div>
                        <div class="border border-gray-400 "></div>
                        <?php
                    }

                    ?>
                </div>
                <!-- </div> -->

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