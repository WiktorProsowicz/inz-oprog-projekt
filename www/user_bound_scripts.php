<?php 
    session_start();

    require_once "./connect.php";

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die($e);
    }

    if($connection->connect_error) {
        die("Nie udało się połączyć z bazą.");
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
    }



    if(isset($_POST["profile_trash"])) {

        $query = sprintf("UPDATE users SET `profile_img` = null WHERE `username` = '%s'", $_SESSION["user_username"]);
        $connection->query($query);

        $_SESSION["user_profileimg"] = null;

        unset($_POST["profile_trash"]);
        header('Location: /profile.php?u=' . $_SESSION["user_username"]);
        exit();
    }

?>