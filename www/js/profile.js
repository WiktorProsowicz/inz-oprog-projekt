$(document).ready(() => {

    const userReport = document.querySelector(".profile__userReport");
    if(userReport !== null) {

        const userReportInput = document.querySelector(".profile__userReportContent");
        const userReportBtn = document.querySelector(".profile__userReportBtn");

        const checkReportInput = () => {

            const inputValue = $(userReportInput).val();
            

            if(inputValue.length == 0) {
                $(userReportBtn).addClass("pe-none");
                $(userReportBtn).attr("tabindex", -1);
                $(userReportBtn).css("opacity", .8);
            }
            else {
                $(userReportBtn).removeClass("pe-none");
                $(userReportBtn).attr("tabindex", 0);
                $(userReportBtn).css("opacity", 1);

                if(inputValue.length > 200) {
                    $(userReportInput).val(inputValue.substring(0, 200))
                }
            }

        };

        $(userReportInput).on("input", () => {checkReportInput()});
        checkReportInput();

        const userReportIcon = document.querySelector(".profile__userReportIcon");
        const userReportPopup = document.querySelector(".profile__userReportPopup");

        $(userReportIcon).on("click", () => {
            $(userReportPopup).toggleClass("profile__userReportPopup-hidden");
        });
    }
});