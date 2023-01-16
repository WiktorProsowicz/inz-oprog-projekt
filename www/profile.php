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

    require_once("./reset_vars.php");
    reset_postWorkbench();
    reset_read();

    if(!isset($_GET["u"])) {
        $profile_viewed_username = "";
    }
    else {
        $profile_viewed_username = $_GET["u"];
    }

    $profile_user_valid = true;   // flag for determining what to render

    // checking if username fulfills constraints
    if(!preg_match("/^[a-zA-Z0-9]+$/", $profile_viewed_username)) {
        $profile_user_valid = false;
        // exit();
    }
    else{
        // checking if username exists
        $query = sprintf("SELECT * FROM users WHERE `username` = '%s'", $profile_viewed_username);
        $result = $connection->query($query);

        if($result->num_rows == 0) {
            $profile_user_valid = false;
            $result->free_result();
            // exit();
        }
        else {
            $row = $result->fetch_assoc();

            $profile_viewed_id = $row["id"];
            $profile_viewed_createdaccount = $row["created_account"];
            $profile_viewed_admin = $row["admin"];
            $profile_viewed_description = htmlentities($row["description"]);

            // for watching/unwatching purposes
            $_SESSION["profile_viewedId"] = $profile_viewed_id;
            $_SESSION["profile_viewedUsername"] = $profile_viewed_username;

            if($row["profile_img"] != null) {
                $profile_viewed_profileimg = base64_encode($row["profile_img"]);
            }
            else {
                $profile_viewed_profileimg = null;
            }
            
            $result->free_result();

            // get number of posts
            $query = sprintf("SELECT COUNT(*) FROM posts WHERE `author_id` = %d", $profile_viewed_id);
            $result = $connection->query($query);

            $profile_viewed_nposts = $result->fetch_array()[0];
            $result->free_result();

            // get number of watchers and watched by the user
            $query = sprintf("SELECT COUNT(*) FROM watchers WHERE `user_id` = %d
                            UNION ALL
                            SELECT COUNT(*) FROM watchers WHERE `watcher_id` = %d", $profile_viewed_id, $profile_viewed_id);
            $result = $connection->query($query);

            $profile_viewed_nwatchers = $result->fetch_array()[0];
            $profile_viewed_nwatches = $result->fetch_array()[0];
            $result->free_result();
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

        <title><?php echo $profile_viewed_username . " - Szniorum";?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/profile.js"></script>
        <script src="/js/grid_tile.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/profile.css" rel="stylesheet"/>
        <link href="/style/gridtile.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";

            if(!$profile_user_valid) {
                echo '
                    <h1 class="d-flex justify-content-center mt-5">
                        <span class="fs-4 me-2">Nie znaleziono podanego użytkownika.</span>
                        <a class="link-secondary fs-4" href="/index.php">Wróć do strony głównej.</a>
                    </h1>
                ';
                exit();
            }
        ?>

        <div class="profile container-fluid" style="margin: 0px !important;">
            
            <div class="profile__userBlock row w-75 mx-auto p-1 p-md-5">

                <?php 
                    $user_watching = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
                    if($profile_viewed_id !== $user_watching) {
                        echo '<div class="profile__userReport">
                                    <button class="profile__userReportIcon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-flag" viewBox="0 0 16 16">
                                            <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12.435 12.435 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A19.626 19.626 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a19.587 19.587 0 0 0 1.349-.476l.019-.007.004-.002h.001M14 1.221c-.22.078-.48.167-.766.255-.81.252-1.872.523-2.734.523-.886 0-1.592-.286-2.203-.534l-.008-.003C7.662 1.21 7.139 1 6.5 1c-.669 0-1.606.229-2.415.478A21.294 21.294 0 0 0 3 1.845v6.433c.22-.078.48-.167.766-.255C4.576 7.77 5.638 7.5 6.5 7.5c.847 0 1.548.28 2.158.525l.028.01C9.32 8.29 9.86 8.5 10.5 8.5c.668 0 1.606-.229 2.415-.478A21.317 21.317 0 0 0 14 7.655V1.222z"/>
                                        </svg>
                                    </button>';
                                    
                                    
                        echo    '<div class="profile__userReportPopup profile__userReportPopup-hidden flex-column justify-content-center align-items-center">

                                    <h3><span class="text-dark">Powód zgłoszenia:</span></h3>

                                    <form method="POST" action="notifications_bound_scripts.php" class="d-flex justify-content-center">
                                        <input name="profileUserReportId" value="'.$profile_viewed_id.'" tabindex="-1" style="display: none;"/>
                                        <input type="text" name="profileUserReportContent" class="profile__userReportContent"/>
                                        <input style="display: none;" name="profileUserReportUsername" value="'.$profile_viewed_username.'"/>
                                                
                                        <button type="submit" class="profile__userReportBtn" name="profileUserReport">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16">
                                                <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12.435 12.435 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A19.626 19.626 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a19.587 19.587 0 0 0 1.349-.476l.019-.007.004-.002h.001"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>';

                        if(isset($_SESSION["userreportmsg"])) {

                            echo '<span class="text-danger d-block mt-3" style="font-size: .9em; width: 300px !important;">'.$_SESSION["userreportmsg"].'</span>';

                            unset($_SESSION["userreportmsg"]);
                        }

                        echo '</div>';
                        }
                    ?>

                <?php 
                
                    if(isset($_SESSION["user_id"]) && $profile_viewed_id == $_SESSION["user_id"]) {
                        echo '<div class="profile__settingsHolder">
                            <a href="/profile_settings.php"><img src="/media/settings.png"/></a>
                        </div>';
                    }
                
                ?>

                <div class="profile__profileimgHolder col-12 col-xl-6 d-flex flex-column justify-content-center align-items-center">
                    <?php 
                        if($profile_viewed_profileimg == null){
                            echo '<img src="/media/user_profile_template.png" class="profile__profileimg" />'; 
                        }
                        else {

                            if(isset($_SESSION["user_id"]) && $profile_viewed_id == $_SESSION["user_id"]) {
                                echo '<div class="profile__profileimgSpace">';
                                    echo '<img src="data:image/jpg;charset=utf8;base64,' . $profile_viewed_profileimg . '" class="profile__profileimg" />';
                                    echo '<form action="/user_bound_scripts.php" method="post">
                                        <input type="submit" name="profile_trash" value="Usuń" class="profile__profileimgTrashHolder"/>
                                    </form>';
                                echo '</div>';
                            }

                            else {
                                echo '<img src="data:image/jpg;charset=utf8;base64,' . $profile_viewed_profileimg . '" class="profile__profileimg" />'; 
                            }
                        }

                        if(isset($_SESSION["user_id"]) && $profile_viewed_id == $_SESSION["user_id"]) {
                            echo '<form class="profile__profileimgForm d-flex flex-column" action="/user_bound_scripts.php" method="post" enctype="multipart/form-data"">
                                    <input id="profileImgInput" class="profile__profileimgInput" type="file" name="added_profileimg"/>
                                    <label for="profileImgInput">
                                        <span class="bg-secondary p-2 d-flex align-items-center justify-content-center text-light" style="border-radius: 20px;">
                                            <img src="/media/file_icon.png"/><span>Wybierz zdjęcie profilowe</span>
                                        </span>
                                    </label>
                                    <input type="submit" name="add_profileimg_submit" value="Zastosuj" class="profile__profileimgFormSubmit"/>';
                            
                            if(isset($_SESSION["profile_imgmsg"])) {
                                echo '<span class="text-danger">' .$_SESSION["profile_imgmsg"]. '</span>';
                                unset($_SESSION["profile_imgmsg"]);
                            }

                            echo '</form>';
                        }
                    ?>
                </div>

                <div class="col-12 mt-xl-3 col-xl-6 d-flex align-items-center justify-content-center justify-content-xl-start">
                    
                    <ul class="profile__infoList d-flex flex-column">
                        <li><h3><?php 
                            if($profile_viewed_id == $_SESSION["user_id"]) echo '<span class="fw-normal">Witaj, </span>';
                            echo $profile_viewed_username;
                        
                        ?></h3></li>

                        <li><span>Na forum od: <span class="text-secondary ms-3 fw-bold"><?php echo $profile_viewed_createdaccount;?></span></span></li>

                        <li><span>Typ konta: <span class="text-secondary ms-3 fw-bold"><?php 
                            if(!$profile_viewed_admin) {
                                echo "zwykły użytkownik";   
                            }
                            else {
                                echo '<span class="fw-bold">admin</span>';
                            }
                        
                        ?></span></span></li>

                        <li><span>Liczba postów: <span class="text-secondary ms-3 fw-bold"><?php echo $profile_viewed_nposts;?></span></span></li>

                        <li>
                            <div class="d-flex justify-content-start" style="gap: 20px;">
                                <?php 
                                    echo '<a href="/account_watchers.php?show_watching=true&u='.$profile_viewed_username.'" class="profile__infoListWatching">
                                            <span>
                                                <span>Obserwujących:</span>
                                                <span class="text-secondary fw-bold">'.$profile_viewed_nwatchers.'</span>
                                            </span> 
                                        </a>
                                    
                                        <a href="/account_watchers.php?show_watching=false&u='.$profile_viewed_username.'" class="profile__infoListWatching">
                                            <span>
                                                <span>Obserwuje:</span> 
                                                <span class="text-secondary fw-bold">'.$profile_viewed_nwatches.'</span>
                                            </span>
                                        </a>';
                                ?>
                            </div>
                        </li>

                        <?php 
                        
                            if(isset($_SESSION["user_id"]) && $_SESSION["user_id"] != $profile_viewed_id) {

                                $query = sprintf("SELECT * FROM watchers WHERE `user_id` = %d AND `watcher_id` = %d", $profile_viewed_id, $_SESSION["user_id"]);
                                $result = $connection->query($query);

                                if($result->num_rows == 0) {
                                    $btn_content = '<button type="submit" name="profileWatch">Obserwuj</button>';
                                }
                                else {
                                    $btn_content = '<button type="submit" name="profileUnwatch">Przestań obserwować</button>';
                                }

                                $result->free_result();

                                echo '<li>
                                        <form class="profile__watchForm" action="/user_bound_scripts.php" method="post">
                                        '.$btn_content.'
                                        </form>
                                    </li>';

                                
                            }

                        ?>
                        
                        <li class="mt-3">
                            <div class="d-flex justify-content-center flex-column" style="gap: 10px;">
                                <?php 
                                    echo '<span>Opis użytkownika: </span> 
                                    <span class="text-secondary fw-bold">'.$profile_viewed_description.'</span>';
                                ?>
                            </div>
                        </li>

                    </ul>

                </div>
            </div>

            <?php 
                if(isset($_SESSION["user_id"]) && $profile_viewed_id == $_SESSION["user_id"]) {

                    if($profile_viewed_admin) {
                        $adminPanelHtml = '<a href="/admin_panel.php" class="profile__adminPanel text-dark d-flex justify-content-center align-items-center mx-auto">
                                                <span>Panel administratora</span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
                                                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm.256 7a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382l.045-.148ZM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                                </svg>
                                        </a>';
                    }

                    echo '<div class="row mt-5 d-flex flex-column" style="gap: 10px;">

                        <a href="/post_workbench.php" class="profile__addNewPost text-dark d-flex justify-content-center align-items-center mx-auto">
                            <span>Dodaj nowy post</span>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                        </a>

                        '.$adminPanelHtml.'

                    </div>';
                }
            ?>

            <div class="row mt-5">
                <div class="col-10 offset-1">

                    <h3 class="fs-4 text-secondary my-3"><span class="ms-5">Ostatnie posty:</span></h3>

                    <div class="profile__postsGrid p-4 d-flex justify-content-center">
                        <?php 

                            //hardcoded tiles
                            $query = sprintf("SELECT CONCAT(SUBSTRING(p.content, 1, 200), '...'), p.title, c.name, p.id 
                                FROM posts AS p JOIN categories AS c ON p.category_id = c.id 
                                WHERE p.author_id = '%d' 
                                ORDER BY p.modified DESC LIMIT 10;", $profile_viewed_id);

                            $result = $connection->query($query);
                            $tiles = $result->fetch_all();

                            $result->free_result();

                            foreach($tiles as $tile) {
                                $tile_short = $tile[0];
                                $tile_title = $tile[1];
                                $tile_cat = $tile[2];
                                $tile_id = $tile[3];
                                $tile_author = $profile_viewed_username;

                                include "./components/grid_tile.php";
                            }

                            if(count($tiles) == 0) {
                                echo '<h3 class="text-secondary text-center my-3 fs-5 fw-normal">Brak postów do wyświetlenia</h3>';
                            }

                        ?>
                    </div>

                </div>
            </div>

        <div>


    </body>

</html>