$(document).ready(() => {
    const searchBtn = document.querySelector(".head__searchBtn");
    const searchInput = document.querySelector(".head__searchInput");
    const searchPopup = document.querySelector(".head__searchPopup");

    $(searchBtn).addClass("pe-none");
    $(searchBtn).attr("tabindex", -1);
    $(searchBtn).css("opacity", .8);

    $(searchPopup).hide();

    $(searchInput).on("input", () => {
        if($(searchInput).val() === "") {
            $(searchBtn).addClass("pe-none");
            $(searchBtn).attr("tabindex", -1);
            $(searchBtn).css("opacity", .8);

            $(searchPopup).hide();

        }
        else {
            $(searchBtn).removeClass("pe-none");
            $(searchBtn).attr("tabindex", 0);
            $(searchBtn).css("opacity", 1);


            $(searchPopup).show();
            $(searchPopup).css("width", searchInput.getBoundingClientRect().width + "px");

            data = {
                "q": $(searchInput).val()
            }

            const phpLink = "/process_query.php";

            $.ajax({
                type: "POST",
                url: phpLink,
                data: data,
                success: (lines) => {
                    $(".head__searchPopupList").html(lines);
                    // console.log(lines);
                }
            });
        }
    })

    $(searchInput).on("blur", () => {
        $(searchPopup).hide();
    });
});