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


        const commentReport = comment.querySelector(".comment__commentReport");
        if(commentReport !== null) {
            const commentReportInput = comment.querySelector(".comment__commentReportContent");
            const commentReportBtn = comment.querySelector(".comment__commentReportBtn");

            const checkCommentReportInput = () => {

                const inputValue = $(commentReportInput).val();
                

                if(inputValue.length == 0) {
                    $(commentReportBtn).addClass("pe-none");
                    $(commentReportBtn).attr("tabindex", -1);
                    $(commentReportBtn).css("opacity", .8);
                }
                else {
                    $(commentReportBtn).removeClass("pe-none");
                    $(commentReportBtn).attr("tabindex", 0);
                    $(commentReportBtn).css("opacity", 1);

                    if(inputValue.length > 200) {
                        $(commentReportInput).val(inputValue.substring(0, 200))
                    }
                }

            };

            $(commentReportInput).on("input", () => {checkCommentReportInput()});
            checkCommentReportInput();

            const commentReportIcon = comment.querySelector(".comment__commentReportIcon");
            const commentReportPopup = comment.querySelector(".comment__commentReportPopup");

            $(commentReportIcon).on("click", () => {
                $(commentReportPopup).toggleClass("comment__commentReportPopup-hidden");
            });
        }

    });

    const authorInfo = document.querySelector(".read__authorInfo");
    const titleHeight = document.querySelector(".read__title").getBoundingClientRect().height;

    $(authorInfo).css("margin-top", (titleHeight + 30) + "px");

    const postReport = document.querySelector(".read__postReport");
    if(postReport !== null) {

        const postReportInput = document.querySelector(".read__postReportContent");
        const postReportBtn = document.querySelector(".read__postReportBtn");

        const checkReportInput = () => {

            const inputValue = $(postReportInput).val();
            

            if(inputValue.length == 0) {
                $(postReportBtn).addClass("pe-none");
                $(postReportBtn).attr("tabindex", -1);
                $(postReportBtn).css("opacity", .8);
            }
            else {
                $(postReportBtn).removeClass("pe-none");
                $(postReportBtn).attr("tabindex", 0);
                $(postReportBtn).css("opacity", 1);

                if(inputValue.length > 200) {
                    $(postReportInput).val(inputValue.substring(0, 200))
                }
            }

        };

        $(postReportInput).on("input", () => {checkReportInput()});
        checkReportInput();

        const postReportIcon = document.querySelector(".read__postReportIcon");
        const postReportPopup = document.querySelector(".read__postReportPopup");

        $(postReportIcon).on("click", () => {
            $(postReportPopup).toggleClass("read__postReportPopup-hidden");
        });
    }

    

});