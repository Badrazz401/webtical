function followUser(event, username) {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // handle the response here, if necessary
            // Update the button or perform any other desired actions
            var button = event.target;
            button.textContent = "Follow";
            button.onclick = function(event) {
                unfollowUser(event, username);
            };
        }
    };
    xhr.open("POST", "follow.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("unfollowed_user=" + username);
}

function unfollowUser(event, username) {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // handle the response here, if necessary
            // Update the button or perform any other desired actions
            var button = event.target;
            button.textContent = "Unfollow";
            button.onclick = function(event) {
                followUser(event, username);
            };
        }
    };
    xhr.open("POST", "unfollow.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("followed_user=" + username);
}