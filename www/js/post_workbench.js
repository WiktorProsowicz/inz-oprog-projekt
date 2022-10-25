$(document).ready(() => {

    const contentArea = document.querySelector(".postWorkbench__content");

    $(contentArea).on("keydown", (event) => {
        if(event.keyCode == 9) {
            event.preventDefault();
            
            const target = event.target;

            var s = target.selectionStart;
            target.value = target.value.substring(0,target.selectionStart) + "    " + target.value.substring(target.selectionEnd);
            target.selectionEnd = s+4; 
        }
    });

    $(contentArea).on("input", () => {

        const contentAreaText = $(contentArea).val().length;

        $(".postWorkbench__contentCount > span").html(contentAreaText + " / 40000");
    });

});