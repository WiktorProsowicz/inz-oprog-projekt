<?php 

    session_start();

    $email = $_POST["email"];
    $passwd = $_POST["passwd"];

    $_SESSION["login_passedemail"] = $email;

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Nie udało się połączyć z bazą.");
    }

    if($connection->connect_error) {
        die("Nie udało się połączyć z bazą.");
    }

    $query = sprintf("SELECT * FROM users WHERE `email`='%s'", $connection->real_escape_string($email));
    $result = $connection->query($query);

    if($result->num_rows == 0) {
        $_SESSION["login_msg"] = "Użytkownik o podanym adresie email nie istnieje.";

        header("Location: login.php");
        $result->free_result();
        exit();
    }

    $user_row = $result->fetch_assoc();

    if(!password_verify($passwd, $user_row["password"])) {
        $_SESSION["login_msg"] = "Podane hasło jest nieprawidłowe.";

        header("Location: login.php");
        $result->free_result();
        exit();
    }

    $_SESSION["user_username"] = $user_row["username"];
    $_SESSION["user_id"] = $user_row["id"];
    $_SESSION["user_profileimg"] = base64_encode($user_row["profile_img"]);
    $_SESSION["user_blocked"] = $user_row["blocked"];
    $_SESSION["user_admin"] = $user_row["admin"];

    $result->free_result();

    unset($_SESSION["login_passedemail"]);
    header("Location: /index.php");

?>