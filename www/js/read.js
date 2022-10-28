$(document).ready(() => {
    const likesLink = document.querySelector(".read__ratingLikesLink");
    const dislikesLink = document.querySelector(".read__ratingDislikesLink");

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
            }
          });

    });

});