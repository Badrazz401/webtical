<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wectical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container min-h-screen min-w-full bg-center block   bg-cover   px-10 py-1.5 selection:bg-violet-500 selection:text-black "
        style="background-image: url('./img/cool.svg');">
        <!--Webtical nav links-->
        <nav class="flex items-center">
            <a href="#"><img src="./img/LG.png" alt="" class="w-24"><h1 class="text-center font-bold ">Webtical</h1></a>
            <ul class="flex-1 text-center">
                <!-- <li class="list-none inline-block px-5 "><a href="#"
                        class="no-underline font-sans font-medium  text-gray-800 px-2 hover:text-gray-200 duration-300 "><i
                            class="fa-solid fa-house"></i> Home</a></li> -->
                <li class="list-none inline-block px-5"><a href="#about"
                        class="no-underline font-sans font-medium text-white px-2  hover:text-black duration-300"><i
                            class="fa-solid fa-circle-exclamation"></i> About</a></li>
                <li class="list-none inline-block px-5"><a href="#contact"
                        class="no-underline font-sans font-medium text-white px-2  hover:text-black duration-300"><i
                            class="fa-solid fa-paper-plane"></i> Contact</a></li>
                <li class="list-none inline-block px-5 "><a href="./login.php"
                        class="no-underline font-sans font-medium text-white px-2  hover:text-black duration-300"><i
                            class="fa-solid fa-right-to-bracket"></i> Login</a></li>
                <li class="list-none inline-block px-5"><a href="./signup.php"
                        class="no-underline font-sans font-medium text-white px-2  hover:text-black duration-300"><i
                            class="fa-solid fa-user-plus"></i> Sign up</a></li>
            </ul>
        </nav>
        <!--webtical home-text-->
        <div class="text-black mt-24 ml-10">
            <h1 class="text-8xl font-semibold leading-normal">WEBTICAL</h1>
            <p class="text-xl font-medium">"Feel free to share your opinions <em>& </em> your ideas."</p>
        </div>
        <!--webtical buttons-->
        <div class="mt-10 ml-10 ">
            <a href="./login.php"
                class="bg-sky-950   border-sky-950 text-white rounded-3xl py-3 px-8 font-medium inline-block mr-4 hover:bg-transparent hover:border-sky-950 hover:text-white duration-300 hover:border border border-transparent">
                <i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="./signup.php"
                class="bg-sky-950   border-sky-950 text-white rounded-3xl py-3 px-8 font-medium hover:bg-transparent hover:border-sky-950 hover:text-white duration-300 hover:border border border-transparent">
                <i class="fa-solid fa-user-plus"></i> Sign up</a>
        </div>
        <!--webtical (about-contact_form)-->
        <div class="grid gap-x-8 gap-y-4 grid-cols-2">
            <!--About-->
            <div class="flex flex-col mt-28 text-center items-center rounded-md shadow-xl backdrop-blur-3xl bg-white/20 md:backdrop-blur-3xl mx-auto p-5"
                id="about">
                <h1 class="text-3xl text-gray-900 font-bold underline underline-offset-8 mb-6 mt-10"><i
                        class="fa-solid fa-circle-exclamation"></i> About Us</h1>
                <div class="w-auto max-w-lg text-center">
                    <p class="text-gray-800 mb-6 italic font-bold">At our core, we believe that everyone deserves access
                        to
                        high-quality information and resources. That's why we created this platform, to empower
                        individuals
                        to learn, grow, and connect with one another.</p>
                    <p class="text-gray-800 mb-6 italic font-bold">Our team is made up of passionate individuals who are
                        dedicated to creating a positive impact in the world. We come from diverse backgrounds and bring
                        a
                        wide range of expertise and perspectives to the table.</p>
                    <p class="text-gray-800 mb-6 italic font-bold">Through our platform, we aim to provide a space where
                        individuals can come together to learn, share ideas, and engage in meaningful conversations.
                        Whether
                        it's through our articles, videos, or online events, we strive to create a welcoming and
                        inclusive
                        community where everyone is heard and valued.</p>
                    <p class="text-gray-800 mb-6 italic font-bold">Thank you for visiting our site. We hope that you'll
                        join us
                        on this journey to create a more informed, connected, and empowered world.</p>
                </div>
            </div>
            <!--Contact-->
            <div class="flex flex-col mx-auto mt-28  backdrop-blur-3xl bg-white/20 md:backdrop-blur-3xl p-12 rounded-md shadow-2xl"
                id="contact">
                <div class="text-center">
                    <h1 class="my-3 text-3xl text-gray-900 font-bold underline underline-offset-8"><i
                            class="fa-solid fa-paper-plane"></i> Contact Us</h1>
                    <p class="text-gray-900 pt-3 font-semibold">Fill up the form below to send us a message.</p>
                </div>
                <div class="m-7">
                    <form>
                        <div class="mb-6">
                            <label for="name" class="block mb-2 text-l font-sans text-white dark:text-black">Full
                                Name</label>
                            <input type="text" name="name" id="name" placeholder="John Doe" required
                                class="w-full px-3 py-2 placeholder-gray-500 placeholder:italic border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-teal-300 focus:border-teal-500">
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block mb-2 text-l font-sans text-gray-900 dark:text-black">Email
                                Address</label>
                            <input type="email" name="email" id="email" placeholder="you@company.com" required
                                class="w-full px-3 py-2 placeholder-gray-500 placeholder:italic border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-teal-300 focus:border-teal-500">
                        </div>
                        <div class="mb-6">
                            <label for="message" class="block mb-2 text-l font-sans text-gray-900 dark:text-black">Your
                                Message</label>
                            <textarea rows="5" name="message" id="message" placeholder="Your Message" required
                                class="w-full px-3 py-2 placeholder-gray-500 placeholder:italic border resize-none border-gray-300 rounded-md focus:outline-none focus:ring  focus:ring-teal-300 focus:border-teal-500"></textarea>
                        </div>
                        <div class="mb-6">
                            <button type="submit"
                                class="w-full px-3 py-4 text-white rounded-lg bg-teal-400 hover:bg-teal-600 duration-150 focus:outline-none">Send
                                Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!--Footer-->
    <footer class="bg-gray-400 py-4 mt-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="text-black">© 2023 My Website. All rights reserved.</div>
                <div class="text-black">Made with ❤️ by badr-azz <em>& </em>oussama-mim</div>
            </div>
        </div>
    </footer>
</body>

</html>