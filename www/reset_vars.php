<?php 

    @session_start();

    

    // set of functions to reser session variabled that store data in forms
    function reset_postWorkbench() {
        unset($_SESSION["postWorkbench_currentPostId"]);
        unset($_SESSION["postWorkbench_currentPostContent"]);
        unset($_SESSION["postWorkbench_currentPostTags"]);
        unset($_SESSION["postWorkbench_currentPostTitle"]);
    }

?>