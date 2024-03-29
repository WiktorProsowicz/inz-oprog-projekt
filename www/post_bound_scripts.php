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
        die("Failed to connect to database: " . $connection->connect_error);
    }


    if(isset($_POST["is_like"])) {

        redirect_if_not_logged();

        $rating_type = $_POST["is_like"];

        $query = sprintf("SELECT * FROM ratings WHERE `user_id` = %d AND `post_id` = %d;", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"]);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            $query = sprintf("INSERT INTO ratings (`user_id`, `post_id`, `is_like`) VALUES (%d, %d, %s);", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"], $rating_type);

            $result->free_result();
            $connection->query($query);
        }
        else {
            $user_rated_as = $result->fetch_assoc()["is_like"];
            $result->free_result();

            // echo "Now clicked " . $rating_type . " rated as " . $user_rated_as;

            $rating_type = $rating_type == "false" ? 0 : 1;
            $user_rated_as = $user_rated_as == "0" ? 0 : 1;

            // echo $rating_type . " " . $user_rated_as;

            if($rating_type == $user_rated_as) {
                $query = sprintf("DELETE FROM ratings WHERE `user_id` = %d AND `post_id` = %d;", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"]);

                $connection->query($query);
            }
            else {
                $query = sprintf("DELETE FROM ratings WHERE `user_id` = %d AND `post_id` = %d;", $_SESSION["user_id"], $_SESSION["read_currentViewedPost"]);

                $connection->query($query);

                $rating_type = $rating_type == 1 ? "true" : "false";
                $query = sprintf("INSERT INTO ratings (`user_id`, `post_id`, `is_like`) VALUES (%d, %d, %s);", 
                                    $_SESSION["user_id"], $_SESSION["read_currentViewedPost"], $rating_type);

                $connection->query($query);
            }
        }

        exit();
    }

    if(isset($_POST["commentsAdded"])) {

        redirect_if_not_logged();

        $comment_text = $_POST["commentsAdded"];
        $_SESSION["read__addedComment"] = $comment_text;

        if(strlen($comment_text) == 0 || strlen($comment_text) > 1500) {
            $_SESSION["read_commentmsg"] = "Komentarz powinien zawierać od 1 do 1500 znaków.";
            header("Location: read.php?p=" . $_SESSION["read_currentViewedPost"]);
            exit();
        }

        $query = sprintf("INSERT INTO comments (author_id, post_id, content, created) VALUES (%d, %d, '%s', '%s');",
                        $_SESSION["user_id"], $_SESSION["read_currentViewedPost"], $connection->real_escape_string($comment_text), date("Y-m-j H:i:s", time()));
        
        $connection->query($query);

        unset($_SESSION["read__addedComment"]);
        header("Location: read.php?p=" . $_SESSION["read_currentViewedPost"]);
        exit();
    }

    if(isset($_POST["comment_is_like"])) {

        redirect_if_not_logged();

        $rating_type = $_POST["comment_is_like"];
        $comment_id = intval($_POST["comment_id"]);

        $query = sprintf("SELECT * FROM comments_ratings WHERE `user_id` = %d AND `comment_id` = %d;", $_SESSION["user_id"], $comment_id);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            $query = sprintf("INSERT INTO comments_ratings (`user_id`, `comment_id`, `is_like`) VALUES (%d, %d, %s);", 
                            $_SESSION["user_id"], $comment_id, $rating_type);

            $result->free_result();
            $connection->query($query);
        }
        else {
            $user_rated_as = $result->fetch_assoc()["is_like"];
            $result->free_result();

            // echo "Now clicked " . $rating_type . " rated as " . $user_rated_as;

            $rating_type = $rating_type == "false" ? 0 : 1;
            $user_rated_as = $user_rated_as == "0" ? 0 : 1;

            // echo $rating_type . " " . $user_rated_as;

            if($rating_type == $user_rated_as) {
                $query = sprintf("DELETE FROM comments_ratings WHERE `user_id` = %d AND `comment_id` = %d;", $_SESSION["user_id"], $comment_id);

                $connection->query($query);
            }
            else {
                $query = sprintf("DELETE FROM comments_ratings WHERE `user_id` = %d AND `comment_id` = %d;", $_SESSION["user_id"], $comment_id);

                $connection->query($query);

                $rating_type = $rating_type == 1 ? "true" : "false";
                $query = sprintf("INSERT INTO comments_ratings (`user_id`, `comment_id`, `is_like`) VALUES (%d, %d, %s);", 
                                    $_SESSION["user_id"], $comment_id, $rating_type);

                $connection->query($query);
            }
        }
        
        exit();
    }

    if(isset($_POST["readRemovedPostId"])) {

        redirect_if_not_logged();

        $postId = $_POST["readRemovedPostId"];

        // deleting comment ratings
        $query = sprintf("DELETE FROM comments_ratings 
                        WHERE comment_id IN (SELECT id FROM comments WHERE post_id = %d);", $postId);
        $connection->query($query);

        // deleting post ratings
        $query = sprintf("DELETE FROM ratings WHERE post_id = %d;", $postId);
        $connection->query($query);

        // deleting comments
        $query = sprintf("DELETE FROM comments WHERE post_id = %d;", $postId);
        $connection->query($query);

        // deleting tag-post connection
        $query = sprintf("DELETE FROM tags_in_posts WHERE post_id = %d;", $postId);
        $connection->query($query);

        // deleting post views
        $query = sprintf("DELETE FROM post_views WHERE post_id = %d;", $postId);
        $connection->query($query);

        // deleting the post
        $query = sprintf("DELETE FROM posts WHERE id = %d;", $postId);
        $connection->query($query);

        header("Location: index.php");

        exit();
    }





    
    // development purposes - for generating syntetic content
    // __get_posts_ids - triggers script
    // __limit - max number of ids
    if(isset($_POST["__get_posts_ids"])) {
        
        $query = sprintf("SELECT id FROM posts LIMIT %d;", $_POST["__limit"]);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            exit();
        }

        $rows = $result->fetch_all();

        foreach($rows as $row) {
            echo " " . $row[0];
        }

        $rows->free_result();

        exit();
    }

?>