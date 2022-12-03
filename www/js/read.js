$(document).ready(() => {
    const likesLink = document.querySelector(".read__ratingLikesLink");
    const dislikesLink = document.querySelector(".read__ratingDislikesLink");
    const commentArea = document.querySelector(".comments__addComment textarea");

    $(likesLink).on("click", (event) => {
        event.preventDefault();
        
        const phpLink = "/post_bound_scripts.php";
        const data = {"is_like": true};

        // $.post(phpLink, data);
        $.ajax({
            type: "POST",
            url: phpLink,
            data: data,
            success: (msg) => {
                location.reload();
                // console.log(msg);
            }
          });

    });

    $(dislikesLink).on("click", (event) => {
        event.preventDefault();
        
        const phpLink = "/post_bound_scripts.php";
        const data = {"is_like": false};

        $.ajax({
            type: "POST",
            url: phpLink,
            data: data,
            success: (msg) => {
                location.reload();
                // console.log(msg);
            }
          });

    });

    $(commentArea).on("input", () => {

        const contentAreaText = $(commentArea).val().length;

        $(".comments__addCommentCounter").html(contentAreaText + " / 1500");
    });

    const toggleComment = (comment) => {
        if($(comment).hasClass("comment__folded")) {
            $(comment.querySelector(".comment__toggleBtn span")).html("pokaż mniej");
        }
        else {
            $(comment.querySelector(".comment__toggleBtn span")).html("pokaż więcej");
        }

        $(comment).toggleClass("comment__folded");
    };

    const comments = document.querySelectorAll(".comment");

    comments.forEach((comment) => {
        const commentId = $(comment).attr("id");
        const commentLikesLink = document.querySelector(".comment[id=\"" + commentId + "\"] .comment__ratingLikesLink");
        const commentDislikesLink = document.querySelector(".comment[id=\"" + commentId + "\"] .comment__ratingDislikesLink");

        $(commentLikesLink).on("click", (event) => {
            event.preventDefault();
            
            const phpLink = "/post_bound_scripts.php";
            const data = {
                "comment_is_like": true,
                "comment_id": commentId
            };
    
            // $.post(phpLink, data);
            $.ajax({
                type: "POST",
                url: phpLink,
                data: data,
                success: (msg) => {
                    location.reload();
                    // console.log(msg);
                }
              });
    
        });

        $(commentDislikesLink).on("click", (event) => {
            event.preventDefault();
            
            const phpLink = "/post_bound_scripts.php";
            const data = {
                "comment_is_like": false,
                "comment_id": commentId
            };
    
            // $.post(phpLink, data);
            $.ajax({
                type: "POST",
                url: phpLink,
                data: data,
                success: (msg) => {
                    location.reload();
                    // console.log(msg);
                }
              });
    
        });

        $(comment).ready(() => {

            if(comment.getBoundingClientRect().height > 200) {
                $(comment.querySelector(".comment__toggleBtn")).on("click", () => {
                    toggleComment(comment);
                });
                toggleComment(comment);
            }
            else {
                comment.querySelector(".comment__toggleBtnHolder").style.display = "none";
            }

        });

    });

    const authorInfo = document.querySelector(".read__authorInfo");
    const titleHeight = document.querySelector(".read__title").getBoundingClientRect().height;

    $(authorInfo).css("margin-top", (titleHeight + 30) + "px");

});