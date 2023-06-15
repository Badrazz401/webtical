<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $username = $_SESSION['username'];

   
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
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <?php include 'layouts/header.php'; ?>
        <!--Webtical main-->
        <div class="basis-1/2 p-4 bg-gray-200 rounded-md shadow-md text-black font-semibold">
            <div class="flex justify-between">
                <span class="text-lg font-semibold">
                    Messages
                </span>
                <i class="fi fi-sr-envelope"></i>
                
            </div>
            
            <div class="pt-2"></div>
            <div class="border border-gray-400 "></div>
            <!--content-->

            <!-- post div -->

            

        </div>
        <!--Webtical trending & search-->
        
    </div>
    </div>
    </body>

    </html>

<?php

} else {
    header('Location: index.php');
}
?>