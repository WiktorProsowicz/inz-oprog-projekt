$(document).ready(() => {
    const textarea = document.querySelector("textarea[name=\"profileSettings_changedDesc\"]");

    $(textarea).on("input", () => {

        const textAreaDesc = $(textarea).val().length;

        $(".profileSettings__descriptionCounter > span").html(textAreaDesc + " / 500");
    });
});