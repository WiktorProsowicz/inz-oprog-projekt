<?php 

    session_start();

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Failed to connect to database " . $e);
    }
    
    if($connection->connect_error) {
        die("Failed to connect to database: " . $connection->connct_error);
    }

    require_once("./reset_vars.php");
    reset_postWorkbench();

    if(isset($_GET["p"])) {
        $search_page = $_GET["p"];
    }
    else {
        $search_page = 0;
    }

    $search_postsLimit = 20;

    if(isset($_GET["c"])) {

        // get number of all appropriate posts
        $query = sprintf("SELECT COUNT(*) FROM posts p JOIN categories c ON c.id = p.category_id WHERE c.name = '%s';",
                        $connection->real_escape_string($_GET["c"]));
        $result = $connection->query($query);

        $search__nPosts = $result->fetch_array()[0];

        $result->free_result();

        // get datafor this page
        $query = sprintf("SELECT p.title AS title, p.id AS id, c.name AS category, CONCAT(SUBSTRING(p.content, 1, 200), '...') AS short, u.username AS author
                        FROM posts p JOIN users u ON p.author_id = u.id JOIN categories c ON c.id = p.category_id
                        WHERE c.name = '%s' ORDER BY p.modified DESC LIMIT %d OFFSET %d;", 
                        $connection->real_escape_string($_GET["c"]), $search_postsLimit, $search_page * $search_postsLimit);
        $result = $connection->query($query);

        $search_rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free_result();

        $search_valid = true;
    }
    else if(isset($_GET["t"])) {

        // get number of all appropriate posts
        $query = sprintf("SELECT COUNT(*) FROM tags_in_posts tip JOIN posts p ON tip.post_id = p.id JOIN tags t ON t.id = tip.tag_id
                        WHERE t.name = '%s';", $connection->real_escape_string($_GET["t"]));
        $result = $connection->query($query);

        $search__nPosts = $result->fetch_array()[0];

        $result->free_result();

        // get data for this page
        $query = sprintf("SELECT p.title AS title, p.id AS id, c.name AS category, CONCAT(SUBSTRING(p.content, 1, 200), '...') AS short, u.username AS author
                        FROM tags_in_posts tip JOIN posts p ON p.id = tip.post_id JOIN categories c ON c.id = p.category_id JOIN users u ON u.id = p.author_id
                        JOIN tags t ON t.id = tip.tag_id

                        WHERE t.name = '%s' ORDER BY p.modified DESC LIMIT %d OFFSET %d;",
                        $connection->real_escape_string($_GET["t"]), $search_postsLimit, $search_page * $search_postsLimit);

        $result = $connection->query($query);

        $search_rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free_result();

        $search_valid = true;
    }
    else if(isset($_GET["q"])) {

        //

    }
    else{
        $search_valid = false;
    }


?>


<!Doctype html>
<html lang="pl">

    <head>

        <meta name="charset" content="utf-8" />
        <meta name="author" content="Wiktor Prosowicz" />
        <meta name="description" content="Forum internetowe, gdzie możesz publikować wiersze, dowcipy i jakąkolwiek inną treść tekstową :)" />
        <meta name="keywords" content="forum, poems, wiersze, publikuj, publikowanie" />
        <meta http-equiv="x-ua-compatibile" content="chrome=1,ie=edge" />
        <meta name="viewport" content="width = device-width, initial-scale = 1.0">

        <link rel="icon" href="/media/logo.png" >

        <title>Szukaj postów</title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="/js/search.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/search.css" rel="stylesheet"/>
        <link href="/style/gridtile.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
            if(!$search_valid) {
                die();
            }
        ?>

        <div class="search-holder container-fluid p-5">
            <div class="search d-flex flex-column">
                <h1 class="text-center fs-3"><?php 
                
                    if(isset($_GET["c"])) {
                        echo '<span class="fw-light me-2">Posty z kategorii</span>' . $_GET["c"];
                    }
                    else if(isset($_GET["t"])) {
                        echo '<span class="fw-light me-2">Posty z tagiem</span>' . $_GET["t"];
                    }

                    echo '<span class="fw-light ms-5">('.$search__nPosts.')</span>';

                ?></h1>

                <div class="search__grid d-flex justify-content-center">

                    <?php 
                        foreach($search_rows as $row) {
                            $tile_short = $row["short"];
                            $tile_title = $row["title"];
                            $tile_cat = $row["category"];
                            $tile_id = $row["id"];
                            $tile_author = $row["author"];

                            include "./components/grid_tile.php";
                        }
                    ?>

                </div>

                <div class="search__pagination mt-auto mb-0 d-flex justify-content-center">
                    <nav class="search__paginationNav d-flex mx-auto">
                        <a href="/search.php?<?php 
                            if(isset($_GET["c"])) {
                                echo "c=" . $_GET["c"];
                            }
                            else if(isset($_GET["t"])) {
                                echo "t=" . $_GET["t"];
                            }


                            if($search_page > 0) echo "&p=" . ($search_page - 1);

                        ?>" <?php 
                            
                            if($search_page == 0) echo 'class="link-secondary pe-none" tabindex=-1" style="opacity: .5;"';
                        
                        ?>><span class="search__paginationLink p-1 rounded">Wstecz</span></a>

                        <?php 
                            if($search_page > 0) {
                                if(isset($_GET["c"])) {
                                    $url = "/search.php?c=" . $_GET["c"] . "&p=" . ($search_page - 1);
                                }
                                else if(isset($_GET["t"])) {
                                    $url = "/search.php?t=" . $_GET["t"] . "&p=" . ($search_page - 1);
                                }

                                echo '<a href="'.$url.'"><span class="search__paginationLink p-1 rounded">'.$search_page.'</span></a>';
                            }
                        ?>

                        <span class="search__paginationLabel rounded px-1"><?php 
                            echo ($search_page + 1);
                        ?></span>

                        <?php
                            if($search__nPosts % $search_postsLimit == 0){
                                $dest_page = intdiv($search__nPosts, $search_postsLimit) - 1;
                            }
                            else {
                                $dest_page = intdiv($search__nPosts, $search_postsLimit);
                            }

                            if($search_page < $dest_page) {

                                if(isset($_GET["c"])) {
                                    $url = "/search.php?c=" . $_GET["c"] . "&p=" . ($search_page + 1);
                                }
                                else if(isset($_GET["t"])) {
                                    $url = "/search.php?t=" . $_GET["t"] . "&p=" . ($search_page + 1);
                                }

                                echo '<a href="'.$url.'"><span class="search__paginationLink p-1 rounded">'.($search_page + 2).'</span></a>';
                            }
                        ?>

                        <a href="search.php?<?php 
                            if(isset($_GET["c"])) {
                                echo "c=" . $_GET["c"];
                            }
                            else if(isset($_GET["t"])) {
                                echo "t=" . $_GET["t"];
                            }

                            echo "&p=" . ($search_page + 1);

                        ?>" <?php
                            
                            if($search__nPosts == 0){
                                $dest_page = 0;
                            }
                            else if($search__nPosts % $search_postsLimit == 0){
                                $dest_page = intdiv($search__nPosts, $search_postsLimit) - 1;
                            }
                            else {
                                $dest_page = intdiv($search__nPosts, $search_postsLimit);
                            }
                            
                            if($search_page == $dest_page) echo 'class="link-secondary pe-none" tabindex=-1" style="opacity: .5;"';
                    
                        ?>><span class="search__paginationLink p-1 rounded">Następny</span></a>
                    </nav>
                </div>
            </div>
        </div>

    </body>

</html>