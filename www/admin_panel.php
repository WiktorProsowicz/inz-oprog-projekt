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

    $adminpanel_reports_chunk_size = 20;

    if(!isset($_POST["adminpanel_less_chunks"]) && !isset($_POST["adminpanel_more_chunks"])) {
        $adminpanel_current_chunk = 0;
    }
    else if(isset($_POST["adminpanel_less_chunks"])) {
        $adminpanel_current_chunk = intval($_POST["adminpanel_less_chunks"]);
        unset($_POST["adminpanel_less_chunks"]);
    }
    else {
        $adminpanel_current_chunk = intval($_POST["adminpanel_more_chunks"]);
        unset($_POST["adminpanel_more_chunks"]);
    }


    // collecting reports
    $query = sprintf("SELECT r.content AS content, r.comment_id as comment_id, r.user_id as user_id, r.post_id as post_id, 
                        ua.username AS author_username, u.username AS user_username, SUBSTRING(p.title, 1, 10) AS post_title,
                        SUBSTRING(c.content, 1, 70) AS comment_content, c.post_id AS comment_post_id
                        FROM reports r
                        JOIN users ua ON r.author_id = ua.id
                        LEFT JOIN users u ON r.user_id = u.id
                        LEFT JOIN comments c ON r.comment_id = c.id
                        LEFT JOIN posts p ON r.post_id = p.id
                        ORDER BY r.date DESC
                        LIMIT %d
                        OFFSET %d;",
                    $adminpanel_reports_chunk_size,
                    $adminpanel_reports_chunk_size * $adminpanel_current_chunk);

    $result = $connection->query($query);

    $adminpanel_reports = $result->fetch_all(MYSQLI_ASSOC);

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

        <title><?php echo "Panel administratora - Szniorum";?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom scripts -->
        <script src="/js/admin_panel.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js" integrity="sha384-XrBQI0kDtx9BrWRwpNT9b09Lwj2M8nnf2tO+zYxJ6IyROBmC05l4AdGWLt2ix1cs" crossorigin="anonymous"></script>

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

                    <div class="col-12 col-md-6">
                        <ul class="adminpanel__yearStatsList w-100 d-flex flex-column justify-content-start align-items-left p-5">

                            <li class="adminpanel__yearStatsPosts"><h5>Ilość utworzonych postów:</h5><span></span></li>

                            <li class="adminpanel__yearStatsComments"><h5>Ilość wstawionych komentarzy:</h5><span></span></li>

                            <li class="adminpanel__yearStatsUsers"><h5>Ilość założonych kont:</h5><span></span></li>

                            <li class="adminpanel__yearStatsReports"><h5>Ilość zgłoszeń:</h5><span></span></li>

                        </ul>
                    </div>

                </div>

                <div class="row p-3 g-1">
                    <div class="col-12 p-3" style="position: relative;">

                        <?php 
                            // get number of all reports
                            $query = "SELECT COUNT(*) FROM reports;";
                            $result = $connection->query($query);
                            $nAllReports = $result->fetch_array()[0];
                            $result->free_result();

                            $lower_bound = $adminpanel_current_chunk * $adminpanel_reports_chunk_size;
                            $upper_bound = ($adminpanel_current_chunk + 1) * $adminpanel_reports_chunk_size;

                            if($upper_bound > $nAllReports)
                                $upper_bound = $nAllReports;

                            echo '<div class="adminpanel__reportsNav">';
                            echo '<div class="adminpanel__reportsBanner">
                                    '.$lower_bound.' -
                                    '.$upper_bound.' /
                                    '.$nAllReports.'
                                </div>';

                            if($adminpanel_current_chunk > 0)
                                echo '<form action="/admin_panel.php" method="post">
                                        <input style="display: none;" value="'.($adminpanel_current_chunk-1).'" name="adminpanel_less_chunks"/>
                                        <button type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                                                <path d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z"/>
                                            </svg>
                                        </button>
                                    </form>';

                            if($upper_bound < $nAllReports)
                                echo '<form action="/admin_panel.php" method="post">
                                        <input style="display: none;" value="'.($adminpanel_current_chunk+1).'" name="adminpanel_more_chunks"/>
                                        <button type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                                <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                            </svg>
                                        </button>
                                    </form>';

                            echo '</div>';
                        ?>

                        <h2 class="adminpanel__reportsHeading d-flex justify-content-center"><span>Zgłoszenia:</span></h2>
                        <ul class="adminpanel__reportsList d-flex flex-wrap justify-content-center w-100">
                            <?php 
                                foreach($adminpanel_reports as $report) {
                                    if($report["post_id"] != null) {
                                        echo '<li>
                                                <div class="adminpanel__reportsItemAuthor">
                                                    <span>Autor zgłoszenia:</span> 
                                                    <a class="link-secondary" href="/profile.php?u='.$report["author_username"].'">'.$report["author_username"].'</a>
                                                </div>
                                                <div>
                                                    <span>Zgłoszony post:</span>
                                                    <span><a class="link-secondary" href="/read.php?p='.$report["post_id"].'">'.$report["post_title"].'...</a></span>
                                                </div>
                                                <div>
                                                    <span>Powód zgłoszenia:</span>
                                                    <span>'.$report["content"].'</span>
                                                </div>
                                            </li>';
                                    }
                                    else if($report["comment_id"] != null) {
                                        echo '<li>
                                                <div class="adminpanel__reportsItemAuthor">
                                                    <span>Autor zgłoszenia:</span> 
                                                    <a class="link-secondary" href="/profile.php?u='.$report["author_username"].'">'.$report["author_username"].'</a>
                                                </div>
                                                <div>
                                                    <span>Zgłoszony komentarz:</span>
                                                    <span><a class="link-secondary" href="/read.php?p='.$report["comment_post_id"].'#'.$report["comment_id"].'">'.$report["comment_content"].'...</a></span>
                                                </div>
                                                <div>
                                                    <span>Powód zgłoszenia:</span>
                                                    <span>'.$report["content"].'</span>
                                                </div>
                                            </li>';
                                    }
                                    else {
                                        echo '<li>
                                                <div class="adminpanel__reportsItemAuthor">
                                                    <span>Autor zgłoszenia:</span> 
                                                    <a class="link-secondary" href="/profile.php?u='.$report["author_username"].'">'.$report["author_username"].'</a>
                                                </div>
                                                <div>
                                                    <span>Zgłoszony użytkownik:</span>
                                                    <span><a class="link-secondary" href="/profile.php?u='.$report["user_username"].'">'.$report["user_username"].'</a></span>
                                                </div>
                                                <div>
                                                    <span>Powód zgłoszenia:</span>
                                                    <span>'.$report["content"].'</span>
                                                </div>
                                            </li>';
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
        
            </div>

            
        </div>

    </body>

</html>