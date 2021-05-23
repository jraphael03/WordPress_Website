import $ from "jquery";

class Like {
  constructor() {
    this.events();
  }

  events() {
    $(".like-box").on("click", this.ourClickDispatcher.bind(this));
  }

  // methods
  ourClickDispatcher(e) {
    var currentLikeBox = $(e.target).closest(".like-box"); // Whatever element got clicked on find the closest ancestor to find like-box (may click the i icon or number instead of the actual button), like-box also contains the professor's id

    // statement to switch between like and dislike
    if (currentLikeBox.attr("data-exists") == "yes") {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }

  createLike(currentLikeBox) {
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce); // What wordpress has to be on the look out for, then the data that needs to be passed in
      },
      url: universityData.root_url + "/wp-json/university/v1/manageLike", // 1st part created for dynamic url, second part created in like-route.php
      type: "POST",
      // same as passing in url universityData.root_url + '/wp-json/university/v1/manageLike?professorId=789'
      data: { professorId: currentLikeBox.data("professor") }, // Grab the professor id from single-professor.php like-box and send to like-route.php to send to db
      success: (response) => {
        // Fill in the heart when liked
        currentLikeBox.attr("data-exists", "yes"); // 2 args, attr you want to work with, value you want to set

        // Increment like number and display
        var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
        likeCount++;
        currentLikeBox.find(".like-count").html(likeCount);

        // Update data-like field live so we can delete the like without refresh
        currentLikeBox.attr("data-like", response);
        console.log(response);
      },
      error: (response) => {
        console.log(response);
      },
    });
  }

  deleteLike(currentLikeBox) {
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce); // What wordpress has to be on the look out for, then the data that needs to be passed in
      },
      url: universityData.root_url + "/wp-json/university/v1/manageLike", // 1st part created for dynamic url, second part created in like-route.php
      data: {
        like: currentLikeBox.attr("data-like"), // attr data-like comes from single-professor.php
      },
      type: "DELETE",
      success: (response) => {
        // Empty heart when like is deleted
        currentLikeBox.attr("data-exists", "no"); // 2 args, attr you want to work with, value you want to set

        // Decrement like number and display
        var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
        likeCount--;
        currentLikeBox.find(".like-count").html(likeCount);

        // Update data-like field live so we can like without refresh
        currentLikeBox.attr("data-like", "");
        console.log(response);
      },
      error: (response) => {
        console.log(response);
      },
    });
  }
}

export default Like;
