<?php 

    session_start();

    require_once("./connect.php");

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

    if(isset($_GET["p"])) {

        $postWorkbench_id = $_GET["p"];

        if(!preg_match("/^[0-9]{1,}$/", $postWorkbench_id)) {
            $postWorkbench_post_valid = false;
        }

        else{
            $query = sprintf("SELECT p.content, c.name, c.id FROM posts AS p JOIN categories AS c ON `p.category_id` = `c.id` WHERE `p.id` = '%d';", $_GET["p"]);
            $result = $connection->query($query);

            if($result->num_rows == 0){
                $postWorkbench_post_valid = false;
            }
            else {

                $row = $result->fetch_array();

                $postWorkbench__content = htmlentities($row[0]);
                $postWorkbench__category = $row[1];
                $postWorkbench__categoryId = $row[2];

                $_SESSION["postWorkbench__currentPostId"] = $postWorkbench__categoryId;

                $result->free_result();

                $query = sprintf("SELECT `t.name` FROM tags_in_posts AS tip JOIN tags AS t ON `tip.tag_id` = `t.id` WHERE `tip.post_id` = '%d';", $postWorkbench_id);
                $result = $connection->query($query);

                $tag_assoc = $result->fetch_all();

                $postWorkbench__taglist = array();

                foreach($tag_assoc as $tag){
                    array_push($postWorkbench__taglist, $tag[0]);
                }

                $postWorkbench_post_valid = true;
            }
        }

    }
    else {
        $postWorkbench_post_valid = true;

        // wiping out saved post id
        if(isset($_SESSION["postWorkbench_currentPostId"])) {
            unset($_SESSION["postWorkbench_currentPostId"]);
            unset($_SESSION["postWorkbench_currentPostContent"]);
        }
    }

?>


<!Doctype html>
<html lang="pl">

    <head>

        <meta name="charset" content="utf-8">
        <meta name="author" content="Wiktor Prosowicz" />
        <meta name="description" content="Forum internetowe, gdzie możesz publikować wiersze, dowcipy i jakąkolwiek inną treść tekstową :)">
        <meta name="keywords" content="forum, poems, wiersze, publikuj, publikowanie" />
        <meta http-equiv="x-ua-compatibile" content="chrome=1,ie=edge" />
        <meta name="viewport" content="width = device-width, initial-scale = 1.0">

        <link rel="icon" href="/media/logo.png" >

        <title><?php 
            if($postWorkbench_post_valid) {
                echo "Post użytkownika " . $_SESSION["user_username"];
            }
            else{
                echo "Coś poszło nie tak";
            }
        ?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/post_workbench.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/post_workbench.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
        
            require "./components/head.php";
        
            if(!$postWorkbench_post_valid) {
                echo '
                    <h1 class="d-flex justify-content-center mt-5">
                        <span class="fs-4 me-2">Ten post nie istnieje.</span>
                        <a class="link-secondary fs-4" href="/index.php">Wróć do strony głównej.</a>
                    </h1>
                ';
                exit();
            }
        
        ?>

        <div class="container-fluid p-5">

            <div class="postWorkbench bg-light border mx-auto">

                <span class="postWorkbench__turnBack badge bg-secondary">
                    <a href="/profile.php?u=<?php echo $_SESSION["user_username"];?>">Wróć</a>
                </span>

                <form class="row g-2 py-3" action="/save_post.php" method="post">

                    <div class="postWorkbench__left col-9 p-5 d-flex flex-column">
                        
                        <div class="d-flex justify-content-between">
                            <label class="text-secondary" to="#postWorkbenchContent">Zawartość posta</label>
                            <div class="postWorkbench__contentCount text-secondary">
                                <span>
                                    <?php 
                                        if(isset($postWorkbench__content)) {
                                            echo strlen($postWorkbench__content) .' / 40000';
                                        }
                                        else {
                                            echo '0 / 40000';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div> 

                        <?php 
                            if(isset($_SESSION["postWorkbench_contentmsg"])) {

                                echo '<span class="text-danger">'.$_SESSION["postWorkbench_contentmsg"].'</span>';

                                unset($_SESSION["postWorkbench_contentmsg"]);
                            }
                        ?>

                        <textarea class="postWorkbench__content" id="postWorkbenchContent" name="editedPostContent"></textarea>

                    </div>

                    <div class="postWorkbench__right col-3 d-flex flex-column px-2 py-5">

                        <div class="row postWorkbench__categoriesHolder d-flex flex-column">

                            <label to="#postWorkbenchCategories" class="text-secondary">Kategoria</label>
                            <select id="postWorkbenchCategories" name="editedPostCat">

                            <?php
                                // get all category names except the current one
                                $query = "SELECT * FROM `categories`;";
                                $result = $connection->query($query);

                                $rows = $result->fetch_all(MYSQLI_ASSOC);

                                if(isset($postWorkbench__categoryId)) {
                                    echo '<option value="'.$postWorkbench__categoryId.'">'.$postWorkbench__category.'</option>';

                                    foreach($rows as $row) {
                                        if($row["id"] != $postWorkbench__categoryId) {
                                            echo '<option value="'.$row["id"].'">'.$row["name"].'</option>';
                                        }
                                    }
                                }
                                else {
                                    foreach($rows as $row) {
                                        echo '<option value="'.$row["id"].'">'.$row["name"].'</option>';
                                    }
                                }
                                
                            ?>

                            </select>

                        </div>

                        <div class="row postWorkbench__tagsHolder d-flex flex-column">

                            <label to="#postWorkbenchTags" class="text-secondary">Tagi oddzielone spacją</label>
                            <textarea class="postWorkbench__tags" id="postWorkbenchTags" name="editedPostTags"></textarea>

                        </div>
                        
                        <div class="row">
                            <input type="submit" value="Zapisz post" class="postWorkbench__submit"/>
                        <div>
                    </div>

                </form>

            </div>

        </div>


    </body>

</html>