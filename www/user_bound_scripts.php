<?php 
    session_start();

    require_once "./connect.php";

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die('<h1 class="d-flex justify-content-center mt-5">
                <span class="fs-4 me-2">Nie udało się połączyć z bazą.</span>
                <a class="link-secondary fs-4" href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }

    if($connection->connect_error) {
        die('<h1 class="d-flex justify-content-center mt-5">
                <span class="fs-4 me-2">Nie udało się połączyć z bazą.</span>
                <a class="link-secondary fs-4" href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }

    if(isset($_FILES["added_profileimg"])) {

        $added_file = $_FILES["added_profileimg"];

        if(!preg_match("/png$|jpeg$|jpg$/", $added_file["type"])) {
            
            $_SESSION["profile_imgmsg"] = "Plik musi mieć rozszerzenie .png lub .jpg";

            header('Location: /profile.php?u=' . $_SESSION["user_username"]);
            unset($_FILES["added_profileimg"]);
            exit();
        }

        if($added_file["size"] > 1000000) {
            $_SESSION["profile_imgmsg"] = "Plik musi być mniejszy niż 1 Mb.";

            header('Location: /profile.php?u=' . $_SESSION["user_username"]);
            unset($_FILES["added_profileimg"]);
            exit();
        }

        $blob = file_get_contents($added_file["tmp_name"]);

        // echo $connection->real_escape_string($blob);
        $query = sprintf("UPDATE users SET `profile_img` = '%s' WHERE `username` = '%s';", $connection->real_escape_string($blob), $_SESSION["user_username"]);
        $connection->query($query);        

        $_SESSION["user_profileimg"] = base64_encode($blob);

        header('Location: /profile.php?u=' . $_SESSION["user_username"]);
        unset($_POST["added_profileimg"]);

        exit();
    }



    if(isset($_POST["profile_trash"])) {

        $query = sprintf("UPDATE users SET `profile_img` = null WHERE `username` = '%s'", $_SESSION["user_username"]);
        $connection->query($query);

        $_SESSION["user_profileimg"] = null;

        unset($_POST["profile_trash"]);
        header('Location: /profile.php?u=' . $_SESSION["user_username"]);
        exit();
    }

    if(isset($_POST["profileSettings_changedDesc"])) {

        $changed_desc = $_POST["profileSettings_changedDesc"];
        $_SESSION["profileSettings_passedDesc"] = $_POST["profileSettings_changedDesc"];

        if(strlen($changed_desc) == 0 || strlen($changed_desc) > 500) {
            $_SESSION["profileSettings_descmsg"] = "Opis musi zawierać od 1 do 500 znaków.";

        }
        else {
            $query = sprintf("UPDATE users SET `description` = '%s' WHERE `id` = %d;", $connection->real_escape_string($changed_desc), $_SESSION["user_id"]);
            $connection->query($query);

            unset($_POST["profileSettings_changedDesc"]);
        }

        unset($_POST["profileSettings_changedDesc"]);
        header("Location: /profile_settings.php");
        exit();
    }

    if(isset($_POST["profileSettings_changedPasswd"])) {

        $changedPasswd = $_POST["profileSettings_changedPasswd"];
        $changedPaswdAgain = $_POST["profileSettings_changedPasswdAgain"];

        if($changedPasswd == "") {
            $_SESSION["profileSettings_passwdmsg"] = "Hasło nie może być puste.";
        }
        else if(!preg_match('/^[a-zA-Z0-9]{6,20}$/', $changedPasswd)) {
            $_SESSION["profileSettings_passwdmsg"] = "Hasło może składać się wyłącznie z małych liter, wielkich liter i cyfr oraz mieć 6-20 znaków.";
        }
        else if($changedPasswd != $changedPaswdAgain) {
            $_SESSION["profileSettings_passwdmsg"] = "Hasła muszą się zgadzać.";
        }
        else {
            $query = sprintf("UPDATE users SET `password` = '%s' WHERE `id` = %d;", password_hash($changedPasswd, PASSWORD_DEFAULT), $_SESSION["user_id"]);
            $connection->query($query);
        }

        unset($_POST["profileSettings_changedPasswd"]);
        unset($_POST["profileSettings_changedPasswdAgain"]);
        header("Location: /profile_settings.php");
        exit();
    }


    if(isset($_POST["profileWatch"])) {

        $query = sprintf("INSERT INTO watchers (`user_id`, `watcher_id`) VALUES (%d, %d);", $_SESSION["profile_viewedId"], $_SESSION["user_id"]);
        $connection->query($query);
        
        header("Location: /profile.php?u=" . $_SESSION["profile_viewedUsername"]);

        unset($_SESSION["profile_viewedId"]);
        unset($_SESSION["profile_viewedUsername"]);
        exit();

    }

    if(isset($_POST["profileUnwatch"])) {

        $query = sprintf("DELETE FROM watchers WHERE `user_id` = %d AND `watcher_id` = %d;", $_SESSION["profile_viewedId"], $_SESSION["user_id"]);
        $connection->query($query);

        header("Location: /profile.php?u=" . $_SESSION["profile_viewedUsername"]);

        unset($_SESSION["profile_viewedId"]);
        unset($_SESSION["profile_viewedUsername"]);
        exit();
    }

?>