$(document).ready(() => {
    const searchBtn = document.querySelector(".head__searchBtn");
    const searchInput = document.querySelector(".head__searchInput");
    const searchPopup = document.querySelector(".head__searchPopup");

    const search = document.querySelector(".head__search");

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

            data = {
                "q": $(searchInput).val()
            }

            const phpLink = "/process_query.php";

            $.ajax({
                type: "POST",
                url: phpLink,
                data: data,
                success: (lines) => {

                    if(lines === "") {
                        $(searchPopup).hide();
                    }
                    else {
                        $(searchPopup).show();
                        $(searchPopup).css("width", searchInput.getBoundingClientRect().width + "px");
                        $(".head__searchPopupList").html(lines);
                    }

                    // console.log(lines);
                }
            });
        }
    })

    $(searchInput).on("blur", () => {
        setTimeout(() => {
            $(".head__SearchPopupList").empty();
            $(searchPopup).hide();
        }, 300);
    });
});