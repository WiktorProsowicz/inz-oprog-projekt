<?php 

    session_start();

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }

    catch(Exception $e) {
        die('<h1">
                <span>Nie udało się połączyć z bazą.</span>
            </h1>');
    }

    if($connection->connect_error) {
        die('<h1">
                <span>Nie udało się połączyć z bazą.</span>
            </h1>');
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

        <title>Forum ziomeczków - strona główna</title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/index.css" rel="stylesheet"/>
        <link href="/style/gridtile.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>

        <div class="main d-flex">

            <div class="main__categories p-2">

                <ul class="main__categoriesList d-flex flex-column mt-3">

                    <?php 

                        // hard-coded categories array
                        $query = "SELECT `name` FROM categories;";
                        $result = $connection->query($query);
                        $categories = $result->fetch_all();

                        $result->free_result();

                        foreach($categories as $cat) {
                            echo '
                                <li class="p-3 border-start border-secondary">
                                    <a href="/search.php?c='.$cat[0].'" class="link-secondary">'
                                        .$cat[0].
                                    '</a>
                                </li>
                            ';
                        }

                    ?>

                </ul>

            </div>


            <div class="main__grid border-start border-light p-1">

                <h1 class="mb-5">
                    <span style="margin-left: 50px" class="text-secondary fs-4">Zobacz najpopularniejsze posty</span>
                </h1>

                <div class="main__gridInner d-flex flex-wrap justify-content-center" style="gap: 30px;">

                <?php 


                    //hardcoded tiles
                    $tiles = array(
                        array("My own poem", 'śak ne maewgewgesz to mewfewggij', "autorr"),
                        array("random post", 'waesgwaesg weasgd WESG EWsg WE', "g"),
                        array("random post", 'waesgwaesg weasgd WESG EWsg WE', "g"),
                        array("random post", 'waesgwaesg weasgd WESG EWsg WE', "g")
                    );

                    foreach($tiles as $tile) {
                        $tile_id = null;
                        $tile_title = $tile[0];
                        $tile_short = $tile[1];
                        $tile_author = $tile[2];
                        include "./components/grid_tile.php";
                    }
                
                ?>

                </div>
            
            </div>

        </div>

    </body>

</html>