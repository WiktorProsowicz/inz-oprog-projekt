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

    if(strlen($_POST["editedPostContent"]) == 0 || strlen($_POST["editedPostContent"]) > 1) {
        $_SESSION["postWorkbench_contentmsg"] = "Post musi zawierać od 1 do 40000 znaków.";
        if(isset($_SESSION["postWorkbench_currentPostId"])) {
            header('Location: /post_workbench.php?p=' . $_SESSION["postWorkbench_currentPostId"]);
        }
        else {
            header("Location: /post_workbench.php");
        }

        exit();
    }
    
    

?>