<link rel="stylesheet" href="./style/scroll.css">
<script src="./js/scroll.js"></script>
<!--Webtical trending & search-->
<div class="basis-1/4   hidden min-[970px]:block p-4 bg-gray-300 rounded-md shadow-md text-black font-semibold h-fit  ">
    <div class="relative text-gray-600 focus-withi:text-gray-400">
        <span class="absolute inset-y-0 left-0 flex items-center pl-2">
            <button type="submit" class="p-1 focus:outline-none focus:shadow-outline">
                <i class="fa-solid fa-magnifying-glass h-6 w-6 text-violet-950 hover:text-violet-700 duration-300"></i>
            </button>
        </span>
        <input type="text" placeholder="Search"
            class="placeholder:italic py-4 rounded-full text-sm text-white  pl-10 focus:outline-none  focus:text-black w-full">
    </div>
    <div class="pt-3"></div>
    <div class="rounded-lg bg-white p-4">
        <div class="flex justify-between">
            <span class="text-xl text-black underline-offset-4">Trends fro you</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-6 h-6 text-violet-950 ">
                <path fill-rule="evenodd"
                    d="M11.097 1.515a.75.75 0 01.589.882L10.666 7.5h4.47l1.079-5.397a.75.75 0 111.47.294L16.665 7.5h3.585a.75.75 0 010 1.5h-3.885l-1.2 6h3.585a.75.75 0 010 1.5h-3.885l-1.08 5.397a.75.75 0 11-1.47-.294l1.02-5.103h-4.47l-1.08 5.397a.75.75 0 01-1.47-.294l1.02-5.103H3.75a.75.75 0 110-1.5h3.885l1.2-6H5.25a.75.75 0 010-1.5h3.885l1.08-5.397a.75.75 0 01.882-.588zM10.365 9l-1.2 6h4.47l1.2-6h-4.47z"
                    clip-rule="evenodd" />
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
                        <span class="font-bold  text-sm">
                            <?php echo $trend['hashtag']; ?>
                        </span>
                        <span class="font-thin text-sm">
                            <?php echo $trend['count']; ?> Posts
                        </span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5  text-violet-950 ">
                            <path fill-rule="evenodd"
                                d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <br>
            <?php
        } ?>
    </div>
    <div class="pt-4"></div>
    <div class="rounded-lg bg-white p-4 fixed-div" id="myDiv">

        <?php
        require('config/connexion.php');
        // Fetch users from database
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE username != :current_user LIMIT 5");
        $stmt->execute(['current_user' => $_SESSION['username']]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="rounded-lg bg-white p-4 ">
            <div class="grid space-y-4 ">
                <?php foreach ($users as $user): ?>
                    <div class="flex flex-col sm:flex-row justify-between space-x-2">
                        <div class="flex space-x-2">
                            <div>
                                <img src="<?= $user['image'] ?>" alt="" class="rounded-full w-14 h-auto  ">
                            </div>
                            <div class="grid">
                                <span class="font-semibold"><a href="showprofile.php?id=<?php echo $user['username']; ?>">
                                        <?= $user['fullname'] ?></a></span>
                                <span class="font-thin"><em>@</em>
                                    <?= $user['username'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0">
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
                    </div>
                    <div class="border border-gray-200"></div>
                <?php endforeach; ?>
            </div>
            <script>
                function followUser(event, username) {
                    event.preventDefault();
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            // handle the response here, if necessary
                            // Update the button or perform any other desired actions
                            var button = event.target;
                            button.textContent = "Unfollow";
                            button.onclick = function (event) {
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
                    xhr.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            // handle the response here, if necessary
                            // Update the button or perform any other desired actions
                            var button = event.target;
                            button.textContent = "Follow";
                            button.onclick = function (event) {
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
    </div>
</div>