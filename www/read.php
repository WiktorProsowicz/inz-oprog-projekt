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

    require_once("./reset_vars.php");
    reset_postWorkbench();

    if(!isset($_GET["p"]) || !preg_match("/^[0-9]{1,}$/", $_GET["p"])) {
        die('<h1">
                <span>Podany post nie istnieje.</span>
                <a href="/index.php">Wróć do strony głównej.</a>
            </h1>');
    }
    else {
        $read_viewedId = $_GET["p"];

        // get primary info about the post
        $query = sprintf("SELECT p.title as title, p.content as content, u.username as username, u.profile_img as profile_img, u.id as userid, c.name AS category
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

        $row = $result->fetch_assoc();
        $result->free_result();

        $read_viewedTitle = htmlentities($row["title"]);
        $read_viewedShort = substr($read_viewedTitle, 0, 40);
        $read_viewedContent = htmlentities($row["content"]);
        $read_viewedUsername = $row["username"];
        $read_viewedAuthorId = $row["userid"];
        $read_viewedCategory = $row["category"];

        // for rating purposes
        if(!isset($_SESSION["read_currentViewedPost"]) || $_SESSION["read_currentViewedPost"] != $read_viewedId) {
            $_SESSION["read_currentViewedPost"] = $read_viewedId;
            if(isset($_SESSION["user_id"])){

                if($_SESSION["user_id"] != $read_viewedAuthorId) {
                    // for standard post view (i.e. not when returning after writing a comment)
                    $query = sprintf("INSERT INTO post_views (`user_id`, `post_id`, `date`) VALUES (%d, %d, '%s');",
                    $_SESSION["user_id"],
                    $read_viewedId,
                    date("Y-m-j H:i:s", time()));
                    $connection->query($query);
                }
                
            }
            else {
                $query = sprintf("INSERT INTO post_views (`post_id`, `date`) VALUES (%d, '%s');",
                        $read_viewedId,
                        date("Y-m-j H:i:s", time()));
                $connection->query($query);
            }

            
        }

        // get views
        $query = sprintf("SELECT COUNT(*) FROM post_views WHERE post_id = %d;", 
                        $read_viewedId);
        $result = $connection->query($query);

        $read_viewedViews = $result->fetch_array()[0];

        if($row["profile_img"] != null) {
            $read_viewedImg = base64_encode($row["profile_img"]);
        }
        else {
            $read_viewedImg = null;
        }

        $read_commentsInterval = 2; // defines the initial limit and number of additional loaded comments

        if(!isset($_SESSION["read_commentsLimit"])) {
            $_SESSION["read_commentsLimit"] = $read_commentsInterval;
        }
        else if(isset($_POST["read_commentsLimit"])){
            $_SESSION["read_commentsLimit"] = $_POST["read_commentsLimit"];
        }

        if(isset($_SESSION["user_id"]))
            $viewer_id = $_SESSION["user_id"];
        else
            $viewer_id = null;

        // get ratings
        $query = sprintf("SELECT COUNT(*) FROM ratings WHERE post_id = %d AND is_like = true
                        UNION ALL
                        SELECT COUNT(*) FROM ratings WHERE post_id = %d AND is_like = false
                        UNION ALL
                        SELECT is_like FROM ratings WHERE `user_id` = %d AND post_id = %d;", $read_viewedId, $read_viewedId, $viewer_id, $read_viewedId);

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
                $likes_class = "text-main-theme-dark";
                $dislikes_class = "";
            }
            else {
                $likes_class = "";
                $dislikes_class = "text-main-theme-dark";
            }
        }

        $result->free_result();

        // get post tags
        $query = sprintf("SELECT t.name FROM tags t JOIN tags_in_posts tip ON t.id = tip.tag_id WHERE tip.post_id = %d;", $read_viewedId);
        $result = $connection->query($query);

        $rows = $result->fetch_all();

        $read_viewedTagsNames = array();
        foreach($rows as $row) {
            array_push($read_viewedTagsNames, $row[0]);
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

        <div class="read-holder container-fluid pt-5 px-5">

            <div class="read mx-auto">

                <div class="row pe-3 p-5 position-relative">

                    <?php 
                        $user_watching = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
                        if($read_viewedAuthorId !== $user_watching) {
                            echo '<div class="read__postReport">
                                        <button class="read__postReportIcon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-flag" viewBox="0 0 16 16">
                                                <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12.435 12.435 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A19.626 19.626 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a19.587 19.587 0 0 0 1.349-.476l.019-.007.004-.002h.001M14 1.221c-.22.078-.48.167-.766.255-.81.252-1.872.523-2.734.523-.886 0-1.592-.286-2.203-.534l-.008-.003C7.662 1.21 7.139 1 6.5 1c-.669 0-1.606.229-2.415.478A21.294 21.294 0 0 0 3 1.845v6.433c.22-.078.48-.167.766-.255C4.576 7.77 5.638 7.5 6.5 7.5c.847 0 1.548.28 2.158.525l.028.01C9.32 8.29 9.86 8.5 10.5 8.5c.668 0 1.606-.229 2.415-.478A21.317 21.317 0 0 0 14 7.655V1.222z"/>
                                            </svg>
                                        </button>';
                                    
                            if(isset($_SESSION["read_reportpostmsg"])) {

                                echo '<div class="read__postReportMessage">
                                        <span class="text-danger">'.$_SESSION["read_reportpostmsg"].'</span>
                                    </div>';

                                unset($_SESSION["read_reportpostmsg"]);
                            }
                                    
                            echo    '<div class="read__postReportPopup read__postReportPopup-hidden flex-column justify-content-center align-items-center">

                                        <h3><span class="text-dark">Powód zgłoszenia:</span></h3>

                                        <form method="POST" action="notifications_bound_scripts.php" class="d-flex justify-content-center">
                                            <input name="readPostReportId" value="'.$read_viewedId.'" tabindex="-1" style="display: none;"/>
                                            <input type="text" name="readPostReportContent" class="read__postReportContent"/>
                                                
                                            <button type="submit" class="read__postReportBtn" name="readPostReport">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16">
                                                    <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12.435 12.435 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A19.626 19.626 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a19.587 19.587 0 0 0 1.349-.476l.019-.007.004-.002h.001"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>';
                        }
                    ?>

                    <div class="read__left col-lg-9 col-12 d-flex flex-column">

                        <div class="read__title">

                            <h1 class="fs-4 text-center"><?php echo $read_viewedTitle;?></h1>

                            <h3 class="fs-5 text-center text-secondary">
                                <a href="search.php?c=<?php echo $read_viewedCategory;?>" class="read__categoryHeader">
                                    <?php echo $read_viewedCategory; ?>
                                </a>
                            </h3>

                        </div>

                        <div class="read__mainText p-3">

                            <?php echo str_replace("\n", "<br>", $read_viewedContent);?>

                        </div>

                        <div class="read__tags d-flex">
                            <?php 
                                foreach($read_viewedTagsNames as $tag) {
                                    echo '<a href="/search.php?t='.$tag.'" class="read__tagsTag"><span class="rounded">'.$tag.'</span></a>';
                                }
                            ?>
                        </div>
                    </div>

                    <div class="read__right col-12 col-lg-3 d-flex flex-column justify-content-start align-items-center">

                        <div class="read__authorInfo d-flex flex-column">

                            <!-- <span class="text-secodary mb-4">Autor: </span> -->

                            
                            <span class="align-self-center">
                                <a href="/profile.php?u=<?php echo $read_viewedUsername;?>" class="read__authorInfoLink link-secondary fw-bold d-flex flex-column align-items-center">
                                    
                                    <?php 
                                        if($read_viewedImg != null) {
                                            echo '<img class="read__authorInfoImg mb-2 border" src="data:image/jpg;charset=utf8;base64,'.$read_viewedImg.'"/>';

                                        }
                                        else {
                                            echo '<img class="read__authorInfoImg mb-2" style="max-width: 120px;" src="/media/user_profile_template.png"/>';
                                        }
                                    ?>
                                    <?php
                                        echo '<span>' . $read_viewedUsername. '</span>';
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

                            <div class="read__views d-flex justify-content-center align-items-center w-100 mt-1">
                                <span class="text-dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                </span>
                                <span class="text-secondary">
                                    <?php echo $read_viewedViews; ?>
                                </span>
                            </div>

                            <?php 
                                // displaying settings
                                if(isset($_SESSION["user_username"]) && $_SESSION["user_id"] == $read_viewedAuthorId) {
                                    echo '<div class="read__settings d-flex flex-column justify-content-center align-items-center mt-3">
                                            <form action="/post_workbench.php" method="get">

                                                <input style="display:none" tabindex="-1" name="p" value="'.$read_viewedId.'"/>
                                                <button type="submit" class="read__settingsEdit py-1 px-5 rounded border border-secondary text-secondary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            <form action="/post_bound_scripts.php" method="post">

                                                <input style="display:none" tabindex="-1" name="readRemovedPostId" value="'.$read_viewedId.'"/>
                                                <button type="submit" name="" class="read__settingsDelete py-1 px-5 rounded border-main-theme-darker border text-main-theme-darker">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                                    </svg>
                                                </button>

                                            </form>
                                        </div>';
                                }
                            
                            ?>

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

                                $query = sprintf("SELECT c.content, u.username, c.created, c.id, u.id FROM comments c JOIN users u ON c.author_id = u.id 
                                                WHERE post_id = %d ORDER BY created DESC LIMIT %d;", $read_viewedId, $_SESSION["read_commentsLimit"]);
                                $result = $connection->query($query);

                                $rows = $result->fetch_all();
                                $result->free_result();

                                foreach($rows as $row) {
                                    $comment_content = str_replace("\n", "<br>", htmlentities($row[0]));
                                    $comment_author = $row[1];
                                    $comment_date = $row[2];
                                    $comment_id = $row[3];
                                    $comment_authorId = $row[4];

                                    // get ratings
                                    $query = sprintf("SELECT COUNT(*) FROM comments_ratings WHERE comment_id = %d AND is_like = true
                                                    UNION ALL
                                                    SELECT COUNT(*) FROM comments_ratings WHERE comment_id = %d AND is_like = false
                                                    UNION ALL
                                                    SELECT is_like FROM comments_ratings WHERE `user_id` = %d AND comment_id = %d;", 
                                                    $comment_id, $comment_id, $_SESSION["user_id"], $comment_id);

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
                                            $comment_likes_class = "text-main-theme-dark";
                                            $comment_dislikes_class = "";
                                        }
                                        else {
                                            $comment_likes_class = "";
                                            $comment_dislikes_class = "text-main-theme-dark";
                                        }
                                    }

                                    $user_watching = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

                                    if($user_watching !== $comment_authorId) {
                                        $comment_enableReporting = true;
                                    }
                                    else $comment_enableReporting = false;

                                    $result->free_result();

                                    include "./components/comment.php";
                                }

                                // display a button if there are more comments than current limit
                                $query = sprintf("SELECT COUNT(*) FROM comments WHERE post_id = %d;", $read_viewedId);
                                $result = $connection->query($query);

                                $read_nAllComments = $result->fetch_row()[0];
                                $result->free_result();

                                if($read_nAllComments > $_SESSION["read_commentsLimit"]) {
                                    echo '<form class="read__moreComments" action="/read.php?p='.$read_viewedId.'" method="post">

                                            <input style="display: none;" name="read_commentsLimit" value="'.($_SESSION["read_commentsLimit"] + $read_commentsInterval).'"/>
                                            <button type="submit">Więcej komentarzy</button>

                                         </form>';
                                }

                            ?>

                        </div>
                        
                    </div>

                </div>

            </div>
        </div>

    </body>

</html>
