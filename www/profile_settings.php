<?php 

    session_start();

    if(!isset($_SESSION["user_username"])) {
        header("Locaton: /index.php");
        exit();
    }

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

    $query = sprintf("SELECT `description` FROM users WHERE `username` = '%s';", $_SESSION["user_username"]);

    $result = $connection->query($query);
    $profileSettings_description = $result->fetch_array()[0];

    $result->free_result();

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

        <title><?php echo $_SESSION["user_username"] . " - ustawienia konta";?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/profile_settings.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/profile_settings.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            include "./components/head.php";
        ?>

        <div class="profileSettings container-fluid p-5">

            <div class="w-50 p-5 d-flex bg-light border mx-auto d-flex flex-column align-items-center" style="border-radius: 50px; position: relative;">

                <span class="profileSettings__turnBack badge bg-secondary">
                    <a href="/profile.php?u=<?php echo $_SESSION["user_username"];?>">Wróć</a>
                </span>

                <div class="profileSettings__stack mt-4 d-flex flex-column align-items-center w-100">

                    <div>
                        <h3 class="fs-5">Zmień swój opis:</h3>
                        <form class="profileSettings__description d-flex flex-column mb-2" action="/user_bound_scripts.php" method="post">

                            <textarea name="profileSettings_changedDesc"><?php
                                if(isset($_SESSION["profileSettings_passedDesc"])){
                                    echo $_SESSION["profileSettings_passedDesc"];
                                }
                                else {
                                    echo $profileSettings_description;  
                                }
                                
                            ?></textarea>

                            <input type="submit" name="profileSettings_changedDescSubmit" value="Zmień"/>

                            <div class="profileSettings__descriptionCounter text-secondary">
                                <span><?php 
                                    if(isset($_SESSION["profileSettings_passedDesc"])){
                                        echo strlen($_SESSION["profileSettings_passedDesc"]) . " / 500";
                                    }
                                    else {
                                        echo strlen($profileSettings_description) . " / 500";  
                                    }
                                ?></span>
                            </div>

                        </form>

                        <?php 
                            if(isset($_SESSION["profileSettings_descmsg"])) {

                                echo '<span class="text-danger">'.$_SESSION["profileSettings_descmsg"].'</span>';

                                unset($_SESSION["profileSettings_descmsg"]);
                            }
                        ?>
                    </div>
                    
                    <div>
                        <h3 class="fs-5">Zmień hasło:</h3>
                        <form class="profileSettings__password d-flex flex-column mb-2" action="/user_bound_scripts.php" method="post">
                            <input type="password" name="profileSettings_changedPasswd"/>

                            <input type="password" name="profileSettings_changedPasswdAgain"/>

                            <input type="submit" value="Zmień"/>

                        </form>

                        <?php 
                            if(isset($_SESSION["profileSettings_passwdmsg"])) {

                                echo '<span class="text-danger">'.$_SESSION["profileSettings_passwdmsg"].'</span>';

                                unset($_SESSION["profileSettings_passwdmsg"]);
                            }
                        ?>
                    </div>

                </div>
            </div>

        </div>

    </body>

</html>