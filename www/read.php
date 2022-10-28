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

    if(!isset($_GET["p"]) || !preg_match("/^[0-9]{1,}$/", $_GET["p"])) {
        die('<h1">
                <span>Podany post nie istnieje.</span>
                <a href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }
    else {
        $read_viewedId = $_GET["p"];

        // get primary info about the post
        $query = sprintf("SELECT p.title as title, p.content as content, u.username as username, u.profile_img as profile_img, u.id as userid
                        FROM posts AS p JOIN categories AS c ON p.category_id = c.id JOIN users AS u ON u.id = p.author_id 
                        WHERE p.id = %d;", $read_viewedId);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            $result->free_result();
            die('<h1">
                <span>Podany post nie istnieje.</span>
                <a href="/index.php">Wróć do strony głównej.</a>
            </h1>');
        }

        // for rating purposes
        $_SESSION["read_currentViewedPost"] = $read_viewedId;

        $row = $result->fetch_assoc();
        $result->free_result();

        $read_viewedTitle = htmlentities($row["title"]);
        $read_viewedShort = substr($read_viewedTitle, 0, 40);
        $read_viewedContent = htmlentities($row["content"]);
        $read_viewedUsername = $row["username"];
        $read_viewedAuthorId = $row["userid"];

        if($row["profile_img"] != null) {
            $read_viewedImg = base64_encode($row["profile_img"]);
        }
        else {
            $read_viewedImg = null;
        }


        // get ratings
        $query = sprintf("SELECT COUNT(*) FROM ratings WHERE post_id = %d AND is_like = true
                        UNION ALL
                        SELECT COUNT(*) FROM ratings WHERE post_id = %d AND is_like = false
                        UNION ALL
                        SELECT is_like FROM ratings WHERE `user_id` = %d AND post_id = %d;", $read_viewedId, $read_viewedId, $read_viewedAuthorId, $read_viewedId);

        $result = $connection->query($query);

        $read_viewedLikes = $result->fetch_array()[0];
        $read_viewedDislikes = $result->fetch_array()[0];

        if($result->num_rows == 2) {
            $likes_class = "";
            $dislikes_class = "";
        }
        else {
            $user_rating_islike = $result->fetch_array()[0];

            if($user_rating_islike == 1) {
                $likes_class = "text-primary";
                $dislikes_class = "";
            }
            else {
                $likes_class = "";
                $dislikes_class = "text-primary";
            }
        }

        $result->free_result();

        // get post tags
        $query = sprintf("SELECT t.name FROM tags t JOIN tags_in_posts tip ON t.id = tip.tag_id WHERE tip.post_id = %d;", $read_viewedId);
        $result = $connection->query($query);

        $rows = $result->fetch_all();

        $read_viewedTagsIds = array();
        foreach($rows as $row) {
            array_push($read_viewedTagsIds, $row[0]);
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

        <!-- rendering first x characters of the postfor browser page indentification -->
        <title><?php echo $read_viewedShort;?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/read.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/read.css" rel="stylesheet"/>
        <link href="/style/comment.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>

        <div class="container-fluid pt-5 px-5">

            <div class="read mx-auto bg-light border">

                <div class="row p-5">

                    <div class="read__left col-10 d-flex flex-column">

                        <div class="read_title">

                            <h1 class="fs-4 text-center"><?php echo $read_viewedTitle;?></h1>

                        </div>

                        <div class="read__mainText border p-3">

                            <?php echo str_replace("\n", "<br>", $read_viewedContent);?>

                        </div>

                        <div class="read__tags d-flex">
                            <?php 
                                foreach($read_viewedTagsIds as $tag) {
                                    echo '<a href="/search.php?t='.$tag.'" class="read__tagsTag"><span class="rounded">'.$tag.'</span></a>';
                                }
                            ?>
                        </div>
                    </div>

                    <div class="read__right col-2 d-flex flex-column justify-content-center">

                        <div class="read__authorInfo d-flex flex-column">

                            <!-- <span class="text-secodary mb-4">Autor: </span> -->

                            
                            <span class="align-self-center">
                                <a href="/profile.php?u=<?php echo $read_viewedUsername;?>" class="read__authorInfoLink link-secondary fw-bold d-flex flex-column align-items-center">
                                    <img  class="read__authorInfoImg mb-2 border rounded p-1" src="data:image/jpg;charset=utf8;base64,<?php echo $read_viewedImg;?>"/>
                                    <?php
                                        echo '<span>' .$read_viewedUsername. '</span>';
                                    ?>
                                </a>
                            </span>
                            
                            <div class="read__rating d-flex justify-content-center w-100 mt-2">

                                <div class="read__ratingItem">
                                    <a href="#" class="read__ratingLikesLink <?php echo $likes_class;?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                                        </svg>
                                        <span class="text-secondary"><?php echo $read_viewedLikes;?><span>
                                    </a>
                                </div>

                                <div class="read__ratingItem">
                                    <a href="#" class="read__ratingDislikesLink <?php echo $dislikes_class;?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z"/>
                                        </svg>
                                        <span class="text-secondary"><?php echo $read_viewedDislikes;?><span>
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="container-fluid p-5">
            <div class="comments mx-auto">
                
                <span class="d-flex flex-column">
                    <h3 class="fs-5 text-secondary">Komentarze:</h3>
                    <hr style="margin: 0;">
                </span>

                <div class="mt-3">
                    
                    <div class="d-flex flex-column" style="gap: 50px;">
                        <form class="comments__addComment flex-column d-flex" action="/post_bound_scripts.php" method="post">
                            <?php 
                                if(isset($_SESSION["read_commentmsg"])) {
                                    echo '<span class="text-danger p-2">' . $_SESSION["read_commentmsg"] . '</span>';
                                    unset($_SESSION["read_commentmsg"]);
                                }
                            ?>

                            <textarea type="text" name="commentsAdded" placeholder="Dodaj komentarz..."><?php 
                                if(isset($_SESSION["read__addedComment"])) {
                                    echo $_SESSION["read__addedComment"];
                                }
                            ?></textarea>

                            <div class="d-flex justify-content-end py-2">
                                <span class="comments__addCommentCounter text-secondary">
                                    <?php 
                                    
                                        if(isset($_SESSION["read__addedComment"])) {
                                            echo strlen($_SESSION["read__addedComment"]) . " / 1500";
                                        }
                                        else {
                                            echo "0 / 1500";
                                        }
                                    
                                    ?>
                                </span>
                                <button type="submit d-flex align-items-center justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                                    </svg>
                                </button>
                            </div>

                        </form>
                        
                        <div class="d-flex flex-column" style="gap: 20px;">

                            <?php 

                                $query = sprintf("SELECT c.content, u.username, c.created, c.id FROM comments c JOIN users u ON c.author_id = u.id 
                                                WHERE post_id = %d ORDER BY created DESC;", $read_viewedId);
                                $result = $connection->query($query);

                                $rows = $result->fetch_all();
                                $result->free_result();

                                foreach($rows as $row) {
                                    $comment_content = str_replace("\n", "<br>", htmlentities($row[0]));
                                    $comment_author = $row[1];
                                    $comment_date = $row[2];
                                    $comment_id = $row[3];

                                    // get ratings
                                    $query = sprintf("SELECT COUNT(*) FROM comments_ratings WHERE comment_id = %d AND is_like = true
                                                    UNION ALL
                                                    SELECT COUNT(*) FROM comments_ratings WHERE comment_id = %d AND is_like = false
                                                    UNION ALL
                                                    SELECT is_like FROM comments_ratings WHERE `user_id` = %d AND comment_id = %d;", 
                                                    $comment_id, $comment_id, $read_viewedAuthorId, $comment_id);

                                    $result = $connection->query($query);

                                    $comment_likes = $result->fetch_array()[0];
                                    $comment_dislikes = $result->fetch_array()[0];

                                    if($result->num_rows == 2) {
                                        $comment_likes_class = "";
                                        $comment_dislikes_class = "";
                                    }
                                    else {
                                        $user_rating_islike = $result->fetch_array()[0];
                            
                                        if($user_rating_islike == 1) {
                                            $comment_likes_class = "text-primary";
                                            $comment_dislikes_class = "";
                                        }
                                        else {
                                            $comment_likes_class = "";
                                            $comment_dislikes_class = "text-primary";
                                        }
                                    }

                                    $result->free_result();

                                    include "./components/comment.php";
                                }

                            ?>

                        </div>
                        
                    </div>

                </div>

            </div>
        </div>

    </body>

</html>
