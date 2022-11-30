<?php 

    @session_start();

    

    // set of functions to reset session variabled that store data in forms
    function reset_postWorkbench() {
        unset($_SESSION["postWorkbench_currentPostId"]);
        unset($_SESSION["postWorkbench_currentPostContent"]);
        unset($_SESSION["postWorkbench_currentPostTags"]);
        unset($_SESSION["postWorkbench_currentPostTitle"]);
    }

    function reset_read() {
        unset($_SESSION["read_commentsLimit"]);
        unset($_SESSION["read_currentViewedPost"]);
        unset($_SESSION["read_addedComment"]);
    }

?>