<?php 

    session_start();

    require_once("./reset_vars.php");
    reset_postWorkbench();

    if(!isset($_GET["show_watching"]) || !isset($_GET["u"])) {
        die();
    }

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

    
    // get user id and check whether this user exists
    $query = sprintf("SELECT id FROM users WHERE username = '%s';", $connection->real_escape_string($_GET["u"]));
    $result = $connection->query($query);

    if($result->num_rows == 0) {
        $accountWatchers_userValid = false;
    }
    else {

        $accountWatchers_userId = $result->fetch_array()[0];
        $result->free_result();

        if($_GET["show_watching"] == "true") {
            
            $query = sprintf("SELECT u_w.username AS username, u_w.profile_img AS profile_img
                            FROM watchers w JOIN users u ON w.user_id = u.id JOIN users u_w ON w.watcher_id = u_w.id WHERE u.id = %d;", $accountWatchers_userId);

            $result = $connection->query($query);

            $accountWatchers_accounts = $result->fetch_all(MYSQLI_ASSOC);

            $result->free_result();

        }
        else {
            $query = sprintf("SELECT u.username AS username, u.profile_img AS profile_img
                            FROM watchers w JOIN users u ON w.user_id = u.id JOIN users u_w ON w.watcher_id = u_w.id WHERE u_w.id = %d;", $accountWatchers_userId);

            $result = $connection->query($query);

            $accountWatchers_accounts = $result->fetch_all(MYSQLI_ASSOC);

            $result->free_result();
        }

        $accountWatchers_userValid = true;
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
            if($_GET["show_watching"] == "true") {
                echo 'Obserwujący użytkownika ' . $_GET["u"];
            }
            else {
                echo 'Obserwowani przez ' . $_GET["u"];
            }
        ?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/account_watchers.css" rel="stylesheet"/>

    </head>

    <body>
        
        <?php 

            require("./components/head.php");

            if(!$accountWatchers_userValid) {
                echo '
                    <h1 class="d-flex justify-content-center mt-5">
                        <span class="fs-4 me-2">Nie znaleziono podanego użytkownika.</span>
                        <a class="link-secondary fs-4" href="/index.php">Wróć do strony głównej.</a>
                    </h1>
                ';
                exit();
            }
        ?>

        <div class="container-fluid p-5">

            <div class="accountWatchers">

                <h1 class="d-flex justify-content-center mb-5">
                    <span class="fs-3"><?php 
                        if($_GET["show_watching"] == "true") {
                            echo '<span class="fw-normal">Obserwujący użytkownika </span>' . $_GET["u"];
                        }
                        else {
                            echo '<span class="fw-normal">Obserwowani przez </span>' . $_GET["u"];
                        }
                    ?></span>
                </h1>

                <div class="d-flex flex-wrap justify-content-center">
                    <?php 
                        foreach($accountWatchers_accounts as $account) {

                            if($account["profile_img"] == null) {
                                $img = '<img class="accountTile__img" src="/media/user_profile_template.png"/>';
                            }
                            else {
                                $img = '<img class="accountTile__img" src="data:image/jpg;charset=utf8;base64,'.base64_encode($account["profile_img"]).'"/>';
                            }

                            echo '<div class="accountTile">
                                    <a href="/profile.php?u='.$account["username"].'" class="d-flex p-2">
                                        <div class="d-flex justify-content-center align-items-center w-100">
                                            '.$img.'
                                        </div>

                                        <div class="d-flex justify-content-center align-items-center w-100">
                                            <span class="fw-bold">'.$account["username"].'</span>
                                        </div>
                                    </a>
                                </div>';
                        }
                    ?>
                </div>

            </div>

        </div>

    </body>

</html>