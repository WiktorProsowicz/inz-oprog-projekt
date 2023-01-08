<?php 

    session_start();

    require_once("./connect.php");

    redirect_if_not_logged();

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

    // making sure the user is an admin
    $query = sprintf("SELECT `admin` FROM users WHERE id = %d;", $_SESSION["user_id"]);
    $result = $connection->query($query);

    $adminpanel_user_isadmin = $result->fetch_array()[0];

    if(!$adminpanel_user_isadmin) {
        header("Location: /index.php");
        exit();
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

        <title><?php echo "Panel administratora - Szniorum";?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom scripts -->
        <script src="/js/admin_panel.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/admin_panel.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>

        <div class="adminpanel container-fluid px-5 pt-5">
            <div class="row p-3">
            
                <div class="col-12 offset-0 col-lg-8 offset-lg-2">

                    <div class="adminpanel__activityStats">

                        <div class="adminpanel__activityStatsHead">
                            <div class="adminpanel__activityStatsHeadInner">
                                
                                <div class="adminpanel__activityStatsBanner">
                                    <span class="adminpanel__activityStatsBannerContent"></span>
                                </div>

                                <div class="adminpanel__activityStatsButtons">
                                    <button class="adminpanel__activityStatsLeft">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-caret-left" viewBox="0 0 16 16">
                                            <path d="M10 12.796V3.204L4.519 8 10 12.796zm-.659.753-5.48-4.796a1 1 0 0 1 0-1.506l5.48-4.796A1 1 0 0 1 11 3.204v9.592a1 1 0 0 1-1.659.753z"/>
                                        </svg>
                                    </button>

                                    <button class="adminpanel__activityStatsRight">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-caret-right" viewBox="0 0 16 16">
                                            <path d="M6 12.796V3.204L11.481 8 6 12.796zm.659.753 5.48-4.796a1 1 0 0 0 0-1.506L6.66 2.451C6.011 1.885 5 2.345 5 3.204v9.592a1 1 0 0 0 1.659.753z"/>
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <div class="adminpanel__activityStatsCanvas">
                            <canvas style="width: 100%"></canvas>
                        </div>

                    </div>

                </div>

                <div class="row p-3 g-1 mt-5">

                    <div class="adminpanel__activityStatsHead">
                        <div class="adminpanel__activityStatsHeadInner">
                                
                            <div class="adminpanel__yearStatsBanner">
                                <span class="adminpanel__yearStatsBannerContent"></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 col-md-6">

                        <div class="adminpanel__yearStatsCanvas">
                            <canvas style="width: 100%"></canvas>
                        </div>

                    </div>

                <div class="col-12 col-md-6"></div>

            </div>
        
            </div>

            
        </div>

    </body>

</html>