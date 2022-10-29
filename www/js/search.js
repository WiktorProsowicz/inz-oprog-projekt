$(document).ready(() => {
    const search = document.querySelector(".search");
    const searchHolder = document.querySelector(".search-holder");
    const head = document.querySelector(".head");

    const holderPadding = parseInt($(searchHolder).css("padding-top").replace("px", ""));
    const headHeight = head.getBoundingClientRect().height;

    $(search).css("min-height", (window.innerHeight - headHeight - 2*holderPadding) + "px");
});