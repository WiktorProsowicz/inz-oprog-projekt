<?php 

    session_start();

    // $_SESSION["user_username"] = "kerfuś_UwU";
    // $_SESSION["user_profileimgsrc"] = "https://preview.redd.it/u6l1kz9sbmu91.jpg?auto=webp&s=126d0cf3c392e9fd0f704cab5e7bb154072d1fcb";

    // session_destroy();

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
                        $categories = array(
                            "śmieszne", "wiersze", "wiadomości", "zainteresowania", "lifehacki", "opowiadania"
                        );

                        foreach ($categories as $cat) {
                            echo '
                                <li class="p-3 border-start border-secondary">
                                    <a href="/search.php?c='.$cat.'" class="link-secondary">'
                                        .$cat.
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