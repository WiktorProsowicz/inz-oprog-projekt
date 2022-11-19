<?php 
    session_start();

    // if(isset($_SESSION["user_username"])) {
    //     unset($_SESSION["user_username"]);
    //     unset($_SESSION["user_profileimg"]);
    //     unset($_SESSION["user_id"]);
    // }

    session_unset();

    header("Location: /index.php")
?>