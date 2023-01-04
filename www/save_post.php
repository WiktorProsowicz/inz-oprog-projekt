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
    $_SESSION["postWorkbench_currentPostTitle"] = $_POST["editedPostTitle"];

    // checking content
    if(strlen($_POST["editedPostContent"]) == 0 || strlen($_POST["editedPostContent"]) > 20000) {
        $_SESSION["postWorkbench_contentmsg"] = "Post musi zawierać od 1 do 40000 znaków.";
        
        if(isset($_SESSION["postWorkbench_currentPostId"])) {
            header('Location: /post_workbench.php?p=' . $_SESSION["postWorkbench_currentPostId"]);
        }
        else {
            header("Location: /post_workbench.php");
        }

        exit();
    }

    // checking title
    if(strlen($_POST["editedPostTitle"]) == 0 || strlen($_POST["editedPostTitle"]) > 100) {
        $_SESSION["postWorkbench_titlemsg"] = "Tytuł musi zawierać od 1 do 100 znaków.";
        
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

    $tagsMsgSuffix = "";
    foreach($collected_tags as $tag) {
        if(mb_strlen($tag, "utf-8") > 20) {
            $tagsMsgSuffix = $tagsMsgSuffix . $tag . "\n";
        }
    }

    if($tagsMsgSuffix != "") {
        $_SESSION["postWorkbench_tagsmsg"] = str_replace("\n", "<br>", "<span class=\"postWorkbench__tagsMsgHeader\">Limit długości 20 znaków niespełniony dla tagów:</span><hr>" . $tagsMsgSuffix);

        if(isset($_SESSION["postWorkbench_currentPostId"])) {
            header('Location: /post_workbench.php?p=' . $_SESSION["postWorkbench_currentPostId"]);
        }
        else {
            header("Location: /post_workbench.php");
        }

        exit();
    }

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
        $query = sprintf("UPDATE posts SET `content` = '%s', `modified` = '%s', `category_id` = %d, `title` = '%s' WHERE id = %d;", 
                    $connection->real_escape_string($_SESSION["postWorkbench_currentPostContent"]),
                    date("Y-m-j H:i:s", time()),
                    $chosen_category_id, 
                    $connection->real_escape_string($_SESSION["postWorkbench_currentPostTitle"]),
                    $_SESSION["postWorkbench_currentPostId"]) ;
        
        $connection->query($query);
    }
    else {
        $query = sprintf("INSERT INTO posts (`author_id`, `content`,`created`, `modified`, `category_id`, `title`)
                    VALUES (%d, '%s', '%s', '%s', %d, '%s');", 
                    $_SESSION["user_id"],
                    $connection->real_escape_string($_SESSION["postWorkbench_currentPostContent"]), 
                    date("Y-m-j H:i:s", time()),
                    date("Y-m-j H:i:s", time()),
                    $chosen_category_id, 
                    $connection->real_escape_string($_SESSION["postWorkbench_currentPostTitle"]));

            $connection->query($query);
        
        $_SESSION["postWorkbench_currentPostId"] = $connection->insert_id;
    }

    // removing all post_tag relationship
    $query = sprintf("DELETE FROM tags_in_posts WHERE `post_id` = %d;", $_SESSION["postWorkbench_currentPostId"]);
    $connection->query($query);

    // inserting post-tag relationship
    foreach($collected_tags_ids as $tag_id) {
        $query = sprintf("INSERT INTO tags_in_posts (`tag_id`, `post_id`) VALUES (%d, %d);", $tag_id, $_SESSION["postWorkbench_currentPostId"]);
        $connection->query($query);
    }

    unset($_SESSION["postWorkbench_currentPostId"]);
    unset($_SESSION["postWorkbench_currentPostContent"]);
    unset($_SESSION["postWorkbench_currentPostTags"]);
    unset($_SESSION["postWorkbench_currentPostTitle"]);
    // echo(var_dump($collected_tags_ids));
    header('Location: /profile.php?u=' . $_SESSION["user_username"]);

?>