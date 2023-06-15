$(document).ready(function () {
  $(".share-btn").click(function (e) {
    e.preventDefault();
    // Get the tweet ID from the data attribute
    var idPub = $(this).data("tweet-id");
    // Send the AJAX request
    $.ajax({
      type: "POST",
      url: "share.php",
      data: { post_id: idPub },
      success: function (response) {
        // Handle the response (e.g., display a success message)
        alert(response);

        // Increment the share count
        var shareCount = $(
          '.tweet[data-tweet-id="' + idPub + '"] .share-count'
        );
        var currentCount = parseInt(shareCount.text());
        shareCount.text(currentCount + 1);
      },
      error: function () {
        // Handle errors (e.g., display an error message)
        alert("Error sharing tweet.");
      },
    });
  });
});
