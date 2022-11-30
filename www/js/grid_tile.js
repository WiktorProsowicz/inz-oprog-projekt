$(document).ready(() => {

    var BreakCommand = {};

    const isHover = e => e.parentElement.querySelector(':hover') === e; 

    setInterval(() => {

        try {
            document.querySelectorAll(".gridtile").forEach((tile) => {

                const textSpan = tile.querySelector(".gridtile__content span");
    
                if(isHover(tile)) {
                    const contentHeight = tile.querySelector(".gridtile__content").getBoundingClientRect().height;
                    const spanHeight = tile.querySelector(".gridtile__content span").getBoundingClientRect().height;
                    
                    if(spanHeight > contentHeight) {
                        $(textSpan).animate({"top": -(spanHeight - contentHeight) + "px"}, (spanHeight - contentHeight) * 30, "linear");
                    }
                    
                    throw BreakCommand;
                }   
                else {
                    $(textSpan).stop(true, true);
                    textSpan.style.top = "0px";
                }                                            
    
            });
        }
        catch (BreakCommand) {}
        

    }, 10);

});