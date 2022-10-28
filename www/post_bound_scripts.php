<?php 

    session_start();

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Failed to connect to database " . $e);
    }
    
    if($connection->connect_error) {
        die("Failed to connect to database: " . $connection->connct_error);
    }


    if(isset($_POST["is_like"])) {
        $rating_type = $_POST["is_like"];

        $query = sprintf("SELECT * FROM ratings WHERE `user_id` = %d;", $_SESSION["user_id"]);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            $query = sprintf("INSERT INTO ratings (`user_id`, `post_id`, `is_like`) VALUES (%d, %d, %s);", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"], $rating_type);

            $result->free_result();
            $connection->query($query);
        }
        else {
            $user_rated_as = $result->fetch_assoc()["is_like"];

            $rating_type = $rating_type == "false" ? 0 : 1;
            $user_rated_as = $user_rated_as == "0" ? 0 : 1;

            if($rating_type == $user_rated_as) {
                $query = sprintf("DELETE FROM ratings WHERE `user_id` = %d AND `post_id` = %d;", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"]);

                $result->free_result();
                $connection->query($query);
            }
            else {
                $query = sprintf("DELETE FROM ratings WHERE `user_id` = %d AND `post_id` = %d;", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"]);

                $connection->query($query);

                $query = sprintf("INSERT INTO ratings (`user_id`, `post_id`, `is_like`) VALUES (%d, %d, %s);", 
                                    $_SESSION["user_id"], $_SESSION["read_currentViewedPost"], $rating_type);

                $connection->query($query);
            }
        }

        

        exit();
    }

?>