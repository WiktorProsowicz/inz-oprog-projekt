<?php 

    session_start();

    // retrieving info from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $passwd = $_POST["passwd"];
    $passwdAgain = $_POST["passwdAgain"];

    // preserved values so that the user does not have to retype them
    $_SESSION["register_passedemail"] = $email;
    $_SESSION["register_passedusername"] = $username;


    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Failed to connect to database ");
    }
    
    if($connection->connect_error) {
        die("Failed to connect to database: " . $connection->connct_error);
    }

    // username validation
    if($username == "") {
        $_SESSION["username_msg"] = "Login nie może być pusty";

        header("Location: register.php");
        exit();
    }
    else if(!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $_SESSION["username_msg"] = "Login może składać się tylko z małych i wielkich liter oraz cyfr.";

        header("Location: register.php");
        exit();
    }

    $query = sprintf("SELECT COUNT(*) AS cnt FROM users WHERE email = '%s';", $username);
    $result = $connection->query($query);

    $same_username_cnt = $result->fetch_array()[0];

    if($same_username_cnt > 0) {
        $_SESSION["username_msg"] = "Użytkownik o takim loginie już istnieje.";

        header("Location: register.php");;
        exit();
    }
    
    $result->free_result();


    // email validation
    if($email == "") {
        $_SESSION["email_msg"] = "Email nie może być pusty.";
        header("Location: register.php");
        exit();
    }

    else if(!filter_var($email, FILTER_VALIDATE_EMAIL) || filter_var($email, FILTER_SANITIZE_EMAIL) != $email) {
        $_SESSION["email_msg"] = "Podany adres email jest niepoprawny.";
        header("Location: register.php");
        exit();
    }

    $query = sprintf("SELECT COUNT(*) AS cnt FROM users WHERE email = '%s';", $email);
    $result = $connection->query($query);

    $same_email_cnt = $result->fetch_array()[0];

    if($same_email_cnt > 0) {
        $_SESSION["email_msg"] = "Zostało już założone konto na ten adres email.";
        header("Location: register.php");
        exit();
    }
    

    // password validation
    if($passwd == "") {
        $_SESSION["passwd_msg"] = "Hasło nie może być puste.";
        header("Location: register.php");
        exit();
    }

    else if(!preg_match('/^[a-zA-Z0-9]{6,20}$/', $passwd)) {
        $_SESSION["passwd_msg"] = "Hasło może składać się wyłącznie z małych liter, wielkich liter i cyfr oraz mieć 6-20 znaków.";
        header("Location: register.php");
        exit();
    }

    else if($passwd != $passwdAgain) {
        $_SESSION["passwdAgain_msg"] = "Hasła nie pasują do siebie.";
        header("Location: register.php");
        exit();
    }

    // validation successful
    unset($_SESSION["register__passedemail"]);
    unset($_SESSION["register__passedusername"]);

    $query = sprintf("INSERT INTO users 
    (`username`, `email`, `password`, `created_account`, `blocked`, `admin`) 
    VALUES ('%s', '%s', '%s', '%s', false, false);", 
    $username, $email, $connection->real_escape_string(password_hash($passwd, PASSWORD_DEFAULT)), date("Y-m-j", time()));
    $connection->query($query);

    $query = sprintf("SELECT id from users WHERE `email` = '%s'", $email);
    $result = $connection->query($query);

    $user_id = $result->fetch_array()[0];
    $result->free_result();


    $_SESSION["user_id"] = $user_id;
    $_SESSION["user_username"] = $username;
    $_SESSION["user_profileimg"] = null;

    header("Location: profile.php?u=$username");

?>