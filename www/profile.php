<?php 

    session_start();

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Nie udało się połączyć z bazą.");
    }

    if($connection->connect_error) {
        die("Nie udało się połączyć z bazą.");
    }

    

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
            $profile_viewed_profileimg = base64_encode($row["profile_img"]);

            $result->free_result();

            $query = sprintf("SELECT COUNT(*) FROM posts WHERE `author_id` = '%s'", $profile_viewed_username);
            $result = $connection->query($query);

            $profile_viewed_nposts = $result->fetch_array()[0];
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

        <title><?php echo $profile_viewed_username . " - Forum ziomeczków";?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/profile.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/profile.css" rel="stylesheet"/>

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

        <div class="profile container-fluid pt-5">
            
            <div class="row w-75 border mx-auto p-5">
                <div class="profile__profileimgHolder col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <?php 
                        if($profile_viewed_profileimg == null){
                            echo '<img src="/media/user_profile_template.png" class="profile__profileimg" />'; 
                        }
                        else {

                            if(isset($_SESSION["user_id"]) && $profile_viewed_id == $_SESSION["user_id"]) {
                                echo '<div class="profile__profileimgSpace">';
                                    echo '<img src="data:image/jpg;charset=utf8;base64,' . $profile_viewed_profileimg . '" class="profile__profileimg" />';
                                    echo '<form  action="/user_bound_scripts.php" method="post">
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
                                    <span class="badge bg-secondary p-2 d-flex align-items-center justify-content-center">
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

                <div class="col-md-6 d-flex align-items-center">
                    
                    <ul class="profile__infoList d-flex flex-column">
                        <li><h3><?php 
                            if($profile_viewed_id == $_SESSION["user_id"]) echo '<span class="fw-normal">Witaj, </span>';
                            echo $profile_viewed_username;
                        
                        ?></h3></li>

                        <li><span>Na forum od: <span class="text-secondary ms-3"><?php echo $profile_viewed_createdaccount;?></span></span></li>

                        <li><span>Typ konta: <span class="text-secondary ms-3"><?php 
                            if(!$profile_viewed_admin) {
                                echo "zwykły użytkownik";   
                            }
                            else {
                                echo "admin";
                            }
                        
                        ?></span></span></li>

                        <li><span>Liczba postów: <span class="text-secondary ms-3"><?php echo $profile_viewed_nposts;?></span></span></li>


                    </ul>

                </div>
            </div>

        <div>


    </body>

</html>