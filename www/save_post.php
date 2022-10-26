<?php 

    session_start();

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }

    catch(Exception $e) {
        die('<h1">
                <span>Nie udało się połączyć z bazą.</span>
                <a href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }

    if($connection->connect_error) {
        die('<h1">
                <span>Nie udało się połączyć z bazą.</span>
                <a href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }

    $_SESSION["postWorkbench_currentPostContent"] = $_POST["editedPostContent"];
    $_SESSION["postWorkbench_currentPostTags"] = $_POST["editedPostTags"];

    if(strlen($_POST["editedPostContent"]) == 0 || strlen($_POST["editedPostContent"]) > 40000) {
        $_SESSION["postWorkbench_contentmsg"] = "Post musi zawierać od 1 do 40000 znaków.";
        
        if(isset($_SESSION["postWorkbench_currentPostId"])) {
            header('Location: /post_workbench.php?p=' . $_SESSION["postWorkbench_currentPostId"]);
        }
        else {
            header("Location: /post_workbench.php");
        }

        exit();
    }
    
    // getting tags from tag string

    $collected_tags = explode(" ", $_SESSION["postWorkbench_currentPostTags"]);
    $collected_tags = array_diff($collected_tags, array(""));
    $collected_tags = array_unique($collected_tags);

    // inserting new tags to database
    $queryFormat = "INSERT IGNORE INTO tags (`name`) VALUES ('%s');";

    foreach($collected_tags as $tagname) {
        $query = sprintf($queryFormat, $connection->real_escape_string($tagname));
        $connection->query($query);
    }

    // getting ids for collected tags
    $queryFormat = "SELECT `id` FROM tags WHERE `name` = '%s';";

    $collected_tags_ids = array();

    foreach($collected_tags as $tagname) {
        $query = sprintf($queryFormat, $connection->real_escape_string($tagname));
        $result = $connection->query($query);

        array_push($collected_tags_ids, $result->fetch_array()[0]);
        $result->free_result();
    }

    $chosen_category_id = intval($_POST["editedPostCat"]);

    // inserting post
    if(isset($_SESSION["postWorkbench_currentPostId"])) {
        $query = sprintf("UPDATE posts SET `content` = '%s', `modified` = '%s', `category_id` = %d;", 
                    $connection->real_escape_string($_SESSION["postWorkbench_currentPostContent"]), date("Y-m-j", time()), $chosen_category_id);
        $connection->query($query);
    }
    else {
        $query = sprintf("INSERT INTO posts (`author_id`, `content`,`created`, `modified`, `likes`, `dislikes`, `category_id`)
                    VALUES (%d, '%s', '%s', '%s', %d, %d, %d);", 
                    $_SESSION["user_id"], $connection->real_escape_string($_SESSION["postWorkbench_currentPostContent"]), 
                    date("Y-m-j", time()), date("Y-m-j", time()), 0, 0, $chosen_category_id);

            $connection->query($query);
        
        $_SESSION["postWorkbench_currentPostId"] = $connection->insert_id;
    }

    // inserting post-tag relationship
    foreach($collected_tags_ids as $tag_id) {
        $query = sprintf("INSERT IGNORE INTO tags_in_posts (`tag_id`, `post_id`) VALUES (%d, %d);", $tag_id, $_SESSION["postWorkbench_currentPostId"]);
        $connection->query($query);
    }

    unset($_SESSION["postWorkbench_currentPostId"]);
    unset($_SESSION["postWorkbench_currentPostContent"]);
    unset($_SESSION["postWorkbench_currentPostTags"]);

    header('Location: /profile.php?u=' . $_SESSION["user_username"]);

?>