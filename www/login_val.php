<?php 

    session_start();

    $email = $_POST["email"];
    $passwd = $_POST["passwd"];

    $_SESSION["login_passedemail"] = $email;

    // texts to be displayed if validation goes wrong

    if($email == "") {
        $_SESSION["email_msg"] = "Email nie może być pusty.";
        header("Location: login.php");
    }

    else if(!filter_var($email, FILTER_VALIDATE_EMAIL) || filter_var($email, FILTER_SANITIZE_EMAIL) != $email) {
        $_SESSION["email_msg"] = "Podany adres email jest niepoprawny.";
        header("Location: login.php");
    }

    else if($passwd == "") {
        $_SESSION["passwd_msg"] = "Hasło nie może być puste.";
        header("Location: login.php");
    }

    else if(!preg_match('/[a-zA-Z0-9]+/', $passwd)) {
        $_SESSION["passwd_msg"] = "Hasło może składać się wyłącznie z małych liter, wielkich liter i cyfr.";
        header("Location: login.php");
    }

    else if(!preg_match('/[a-zA-Z0-9]{6,}/', $passwd)) {
        $_SESSION["passwd_msg"] = "Hasło musi się składać z conajmniej 6 znaków.";
        header("Location: login.php");
    }

    else{
        // $email = htmlentities($email, ENT_QUOTES, "utf-8");

        
    }


?>