<?php
session_start();

if (isset($_SESSION['loggedIn'], $_SESSION['username'])) {
    require("config/connexion.php");
    include("config/functions.php");

    $username = $_SESSION['username'];

    $selUtilisateur = $db->prepare('SELECT * FROM utilisateur WHERE username = :username');
    $selUtilisateur->bindParam(':username', $username);
    $selUtilisateur->execute();

    // Check if the query returned any results

    $query = $db->prepare('SELECT * FROM trends ORDER BY count DESC LIMIT 5');
    $query->execute();
    $trends = $query->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username != :current_user");
    $stmt->execute(['current_user' => $_SESSION['username']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve messages between sender and recipient
    $senderUsername = $_SESSION['username'];
    $recipientUsername = $_GET['recipient'];

    $retrieveMessages = $db->prepare('SELECT * FROM message WHERE (sender = :sender AND receiver = :recipient) OR (sender = :recipient AND receiver = :sender) ORDER BY timestamp ASC');
    $retrieveMessages->bindParam(':sender', $senderUsername);
    $retrieveMessages->bindParam(':recipient', $recipientUsername);
    $retrieveMessages->execute();

    $messages = $retrieveMessages->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve sender's profile picture and username
    $retrieveSender = $db->prepare('SELECT image, fullname FROM utilisateur WHERE username = :sender');
    $retrieveSender->bindParam(':sender', $senderUsername);
    $retrieveSender->execute();
    $senderData = $retrieveSender->fetch(PDO::FETCH_ASSOC);
    $senderProfilePic = $senderData['image'];
    $senderUsername = $senderData['fullname'];

    // Retrieve recipient's profile picture and username
    $retrieveRecipient = $db->prepare('SELECT image, fullname FROM utilisateur WHERE username = :recipient');
    $retrieveRecipient->bindParam(':recipient', $recipientUsername);
    $retrieveRecipient->execute();
    $recipientData = $retrieveRecipient->fetch(PDO::FETCH_ASSOC);
    $recipientProfilePic = $recipientData['image'];
    $recipientUsername = $recipientData['fullname'];

    if ($selUtilisateur->rowCount() > 0) {
        $user = $selUtilisateur->fetch(PDO::FETCH_ASSOC);
        $profilePicture = $user['image'];
        $fullName = $user['fullname'];
    }
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
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
        <link rel="stylesheet" href="./style/lightbox.css">
        <script src="./js/lightbox.js"></script>
        <!-- <link rel="stylesheet" href="./style/dropdown.css"> -->
        <style>
            .options-link {
                display: flex;
                align-items: center;
            }

            #dropdownOptions {
                position: absolute;
                top: -80px;
                right: 20px;
                left: 40px;
                z-index: 10;
            }
        </style>
    </head>

    <body class="overflow-hidden max-[500px]:overflow-auto">
        <div class="flex flex-row  ">
            <!--Webtical links-->
            <?php include 'layouts/header.php'; ?>
            <!--Webtical main-->
            <div class="basis-full max-[970px]:basis-full p-4   bg-gray-200 rounded-md shadow-md text-black font-semibold ">
                <div class="flex justify-between">
                    <!-- <span class="text-lg font-semibold"> -->
                    <div class="flex items-center space-x-4">
                        <img class="w-12 h-12 rounded-full" src="<?= $recipientProfilePic ?>" alt="Profile Picture">
                        <div>
                            <h1 class="text-lg font-semibold">
                                <?= $recipientUsername ?>
                            </h1>
                            <p class="text-gray-500">Online</p>
                        </div>
                    </div>
                    <!-- </span> -->
                    <!-- <svg fill="currentColor" stroke-width="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="1em" width="1em" class="w-6 h-6 " style="overflow: visible;">
                        <path d="M60.44 389.17c0 .07 0 .2-.08.38.03-.12.05-.25.08-.38ZM439.9 405.6a26.77 26.77 0 0 1-9.59-2l-56.78-20.13-.42-.17a9.88 9.88 0 0 0-3.91-.76 10.32 10.32 0 0 0-3.62.66c-1.38.52-13.81 5.19-26.85 8.77-7.07 1.94-31.68 8.27-51.43 8.27-50.48 0-97.68-19.4-132.89-54.63A183.38 183.38 0 0 1 100.3 215.1a175.9 175.9 0 0 1 4.06-37.58c8.79-40.62 32.07-77.57 65.55-104A194.76 194.76 0 0 1 290.3 32c52.21 0 100.86 20 137 56.18 34.16 34.27 52.88 79.33 52.73 126.87a177.86 177.86 0 0 1-30.3 99.15l-.19.28-.74 1c-.17.23-.34.45-.5.68l-.15.27a21.63 21.63 0 0 0-1.08 2.09l15.74 55.94a26.42 26.42 0 0 1 1.12 7.11 24 24 0 0 1-24.03 24.03Z">
                        </path>
                        <path d="M299.87 425.39a15.74 15.74 0 0 0-10.29-8.1c-5.78-1.53-12.52-1.27-17.67-1.65a201.78 201.78 0 0 1-128.82-58.75A199.21 199.21 0 0 1 86.4 244.16C85 234.42 85 232 85 232a16 16 0 0 0-28-10.58s-7.88 8.58-11.6 17.19a162.09 162.09 0 0 0 11 150.06C59 393 59 395 58.42 399.5c-2.73 14.11-7.51 39-10 51.91a24 24 0 0 0 8 22.92l.46.39A24.34 24.34 0 0 0 72 480a23.42 23.42 0 0 0 9-1.79l53.51-20.65a8.05 8.05 0 0 1 5.72 0c21.07 7.84 43 12 63.78 12a176 176 0 0 0 74.91-16.66c5.46-2.56 14-5.34 19-11.12a15 15 0 0 0 1.95-16.39Z">
                        </path>
                    </svg> -->
                </div>
                <div class="flex ">
                </div>
                <div class="pt-2"></div>
                <div class="border border-gray-300 "></div>
                <?php
                if (isset($_GET['recipient'])) {
                    $recipientUsername = $_GET['recipient'];

                    require("config/connexion.php");

                    // Retrieve recipient's information from the database
                    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username = :recipient");
                    $stmt->execute([':recipient' => $recipientUsername]);
                    $recipientData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($recipientData) {
                        $recipientFullName = $recipientData['fullname'];
                        $recipientProfilePic = $recipientData['image'];

                        // Retrieve messages between sender and recipient
                        $retrieveMessages = $db->prepare('SELECT * FROM message WHERE (sender = :sender AND receiver= :recipient) OR (sender = :recipient AND receiver = :sender) ORDER BY timestamp ASC');
                        $retrieveMessages->bindParam(':sender', $_SESSION['username']);
                        $retrieveMessages->bindParam(':recipient', $recipientUsername);
                        $retrieveMessages->execute();

                        $messages = $retrieveMessages->fetchAll(PDO::FETCH_ASSOC);
                ?>
                        <div class="container max-w-full h-auto    max-[690px]:max-w-md">


                            <div class="">
                                <div class="bg-gray-200 rounded-lg drop-shadow-lg py-4 px-2" style="background-image: url('./img/tttend.svg'); background-size: cover; max-width:100% ; max-height: 100%;">
                                    <div class="overflow-auto max-h-[540px] ">
                                        <div class="flex flex-col space-y-2" id="messageContainer">
                                            <?php foreach ($messages as $message) {
                                                $isSender = $message['sender'] === $senderUsername;
                                                $image = $message['image'];
                                                $video = $message['video']; // Add video field
                                            ?>
                                                <?php if ($message['sender'] === $_SESSION['username']) {
                                                    $senderProfilePic = $profilePicture;
                                                ?>
                                                    <div class="flex justify-end">
                                                        <div class="flex w-auto h-auto rounded-br-3xl rounded-l-3xl dark:bg-[#239085] p-1 mr-6">
                                                            <div class=" max-w-[500px] h-auto break-words overflow-hidden">
                                                                <?php if ($image) { ?>
                                                                    <img class="chat-image w-full h-auto rounded-3xl object-contain  cursor-pointer" src="<?= $image ?>" alt="Image" onclick="openLightbox(event, this)">
                                                                <?php } elseif ($video) { ?>
                                                                    <video class="chat-video w-full h-auto aspect-video rounded-3xl  " src="<?= $video ?>" controls></video>
                                                                <?php } ?>
                                                                <p class="px-2 py-1 text-base text-white">
                                                                    <?= $message['message'] ?>
                                                                </p>
                                                            </div>
                                                            <!-- <div>
                                                                <img class="w-8 h-auto rounded-full object-contain  hidden min-[690px]:block" src="<?= $senderProfilePic ?>" alt="Sender Profile Picture">
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="flex justify-start ">
                                                        <div class="flex w-auto h-auto rounded-bl-3xl rounded-r-3xl dark:bg-slate-500 p-1 ml-6">
                                                            <!-- <div>
                                                                <img class="w-8 h-auto rounded-full object-contain hidden min-[690px]:block" src="<?= $recipientProfilePic ?>" alt="Recipient Profile Picture">
                                                            </div> -->
                                                            <div class=" max-w-[500px] h-auto break-words overflow-hidden">
                                                                <?php if ($image) { ?>
                                                                    <img class="chat-image w-full h-auto rounded-3xl object-contain  cursor-pointer" src="<?= $image ?>" alt="Image" onclick="openLightbox(event, this)">
                                                                <?php } elseif ($video) { ?>
                                                                    <video class="chat-video w-full  h-auto aspect-video rounded-3xl " src="<?= $video ?>" controls></video>
                                                                <?php } ?>
                                                                <p class="px-2 py-1 text-base text-white">
                                                                    <?= $message['message'] ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <form class="mt-4" action="send_message.php" method="post" id="messageForm" enctype="multipart/form-data">
                                        <input type="hidden" name="recipient" value="<?= $recipientUsername ?>">
                                        <div class="relative border border-gray-500 bg-white rounded-r-sm flex ">
                                            <input id="messageInput" class=" w-full h-auto rounded-xs bg-gray-300 focus:outline focus:outline-teal-500 focus:caret-teal-950 caret-teal-900 px-2 py-2 pl-10 placeholder-gray-700" name="message" placeholder="Write your message" required />
                                            <div class="options-link absolute left-2 top-2">
                                                <ul id="dropdownOptions" class="hidden bg-slate-900 divide-y divide-gray-100 rounded-lg shadow w-40 py-2 text-sm text-gray-700">
                                                    <li>
                                                        <a href="#" id="imageOption" class="block px-2 py-2 text-white hover:bg-slate-500 rounded-sm">
                                                            <i class="fi fi-rr-graphic-style"></i> Image
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" id="videoOption" class="block px-2 py-2 text-white hover:bg-slate-500 rounded-sm">
                                                            <i class="fi fi-rr-video-camera-alt"></i> Video
                                                        </a>
                                                    </li>
                                                </ul>
                                                <span id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="fas fa-ellipsis-h cursor-pointer max-[600px]:break-words mt-1"></span>
                                            </div>
                                            <input type="file" id="image" name="image" class="hidden">
                                            <input type="file" id="video" name="video" class="hidden">
                                            <button class="px-2 py-2 text-gray-950  hover:bg-slate-300 duration-200 rounded-r-sm">
                                                <i class="fi fi-rr-rocket-lunch w-6 h-6"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <script>
                                        var dropdownButton = document.getElementById('dropdownDefaultButton');
                                        var dropdownOptions = document.getElementById('dropdownOptions');
                                        var imageOption = document.getElementById('imageOption');
                                        var videoOption = document.getElementById('videoOption');

                                        dropdownButton.addEventListener('click', function(event) {
                                            event.preventDefault();
                                            dropdownOptions.classList.toggle('hidden');
                                        });

                                        imageOption.addEventListener('click', function(event) {
                                            event.preventDefault();
                                            var imageInput = document.getElementById('image');
                                            if (imageInput) {
                                                imageInput.click();
                                            }
                                        });

                                        videoOption.addEventListener('click', function(event) {
                                            event.preventDefault();
                                            var videoInput = document.getElementById('video');
                                            if (videoInput) {
                                                videoInput.click();
                                            }
                                        });

                                        window.addEventListener('click', function(event) {
                                            if (!dropdownOptions.contains(event.target) && event.target !== dropdownButton) {
                                                dropdownOptions.classList.add('hidden');
                                            }
                                        });
                                    </script>
                                </div>

                            </div>
                        </div>
                        <div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
                            <img id="lightboxImage" src="" alt="Full-size Image" class="">
                        </div>
                        <!-- </div: -->
                <?php
                    }
                } else {
                    echo "<p>Recipient not specified.</p>";
                }
                ?>
            </div>



    </body>

    </html>
<?php

} else {
    header('Location: index.php');
}
?>