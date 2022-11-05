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

        $search_nPosts = $result->fetch_array()[0];

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

        $search_nPosts = $result->fetch_array()[0];

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

        // storing the query in the database
        if(isset($_SESSION["user_id"])) {
            $query = sprintf("INSERT INTO search_queries (`user_id`, `date`, `query`) 
                                    VALUES (%d, '%s', SUBSTRING('%s', 1, 100));",
                                    $_SESSION["user_id"], date("Y-m-j H:i:s", time()), $connection->real_escape_string($_GET["q"]));
            
            $connection->query($query);
        }
        else {
            $query = sprintf("INSERT INTO search_queries (`date`, `query`) VALUES ('%s', SUBSTRING('%s', 1, 100));",
                            date("Y-m-j H:i:s", time()), $connection->real_escape_string($_GET["1"]));
            
            $connection->query($query);
        }


        // get unique tokens
        $tokens = explode(" ", $_GET["q"]);
        $tokens = array_diff($tokens, array(""));
        $tokens = array_unique($tokens);

        // collect user ids
        $collected_userIds = array();
        foreach($tokens as $token) {
            $query = sprintf("SELECT id FROM users WHERE username LIKE '%%%s%%';", $connection->real_escape_string($token));
            $result = $connection->query($query);

            if($result->num_rows > 0) {
                foreach($result->fetch_all() as $row) array_push($collected_userIds, $row[0]);
            }

            $result->free_result();
        }
        
        $collected_userIds = array_slice(array_unique($collected_userIds), 0, 10);

        $query_in_array = "";
        for($i = 0; $i < count($collected_userIds); $i += 1) {
            $query_in_array = $query_in_array . $collected_userIds[$i];
            if($i < count($collected_userIds) - 1) $query_in_array = $query_in_array . ",";
        }
        if($query_in_array == "") $query_in_array = "-1";

        $query = sprintf("SELECT username, profile_img FROM users WHERE id IN (%s);", $query_in_array);
        $result = $connection->query($query);

        $search_users = $result->fetch_all(MYSQLI_ASSOC);

        $result->free_result();


        // collect post ids
        $collected_postIds = array();
        foreach($tokens as $token) {
            $query = sprintf("SELECT p.id
                FROM tags_in_posts tip JOIN posts p ON p.id = tip.post_id JOIN tags t ON t.id = tip.tag_id
                WHERE t.name = '%s';", 
                $connection->real_escape_string($token));

            $result = $connection->query($query);

            if($result->num_rows > 0) {
                foreach($result->fetch_all() as $row) array_push($collected_postIds, $row[0]);
            }

            $result->free_result();
        }

        $search_nPosts = count(array_unique($collected_postIds));
        $collected_postIds = array_slice(array_unique($collected_postIds), $search_page, $search_postsLimit);

        $query_in_array = "";
        for($i = 0; $i < count($collected_postIds); $i += 1) {
            $query_in_array = $query_in_array . $collected_postIds[$i];
            if($i < count($collected_postIds) - 1) $query_in_array = $query_in_array . ",";
        }
        if($query_in_array == "") $query_in_array = "-1";

        $query = sprintf("SELECT p.title AS title, p.id AS id, c.name AS category, CONCAT(SUBSTRING(p.content, 1, 200), '...') AS short, u.username AS author
                        FROM posts p JOIN users u ON u.id = p.author_id JOIN categories c ON c.id = p.category_id
                        WHERE p.id IN (%s);", $query_in_array);

        $result = $connection->query($query);
        $search_rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free_result();

        $search_valid = true;

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
                    else if(isset($_GET["q"])) {
                        echo '<span class="fw-light me-2">Wyniki zapytania</span>' . $_GET["q"];
                    }

                    echo '<span class="fw-light ms-5">('.$search_nPosts.')</span>';

                ?></h1>

                <?php 
                    if(isset($search_users) && count($search_users) > 0) {
                        echo '<div class="search__users p-3 d-flex flex-column">';
                        echo '  <h3 class="text-secondary fw-normal fs-4">Użytkownicy:</h3>';
                        echo '  <div class="search__usersList d-flex flex-wrap">';
                        foreach($search_users as $search_user) {

                            if($search_user["profile_img"] == null) {
                                $img_tag = '<img src="/media/user_profile_template.png"/>';
                            }
                            else {
                                $img_tag = '<img src="data:image/jpg;charset=utf8;base64,'.base64_encode($search_user["profile_img"]).'" />';
                            }

                            echo '<div class="search__usersTile ">
                                    <a href="/profile.php?u='.$search_user["username"].'" class="d-flex flex-column align-items-center justify-content-center">
                                '.$img_tag.'
                                <span style="font-size: 1.1em;">'.$search_user["username"].'</span>
                                    </a>
                            </div>';
                        }
                        echo '  </div>';
                        echo '</div>';
                    }
                ?>
                
                <div class="search__gridHolder">
                    <?php 
                        if(isset($search_users) && count($search_users) > 0 && count($search_rows) > 0) {
                            echo '<div class="p-3">
                                    <h3 class="text-secondary fw-normal fs-4" style="margin-left:40px;">Posty:</h3>
                                </div>';
                        }
                    ?>

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
                            else if(isset($_GET["q"])) {
                                echo "q=" . $_GET["q"];
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
                                else if(isset($_GET["q"])) {
                                    $url = "/search.php?q=" . $_GET["q"] . "&p=" . ($search_page - 1);
                                }

                                echo '<a href="'.$url.'"><span class="search__paginationLink p-1 rounded">'.$search_page.'</span></a>';
                            }
                        ?>

                        <span class="search__paginationLabel rounded px-1"><?php 
                            echo ($search_page + 1);
                        ?></span>

                        <?php
                            if($search_nPosts % $search_postsLimit == 0){
                                $dest_page = intdiv($search_nPosts, $search_postsLimit) - 1;
                            }
                            else {
                                $dest_page = intdiv($search_nPosts, $search_postsLimit);
                            }

                            if($search_page < $dest_page) {

                                if(isset($_GET["c"])) {
                                    $url = "/search.php?c=" . $_GET["c"] . "&p=" . ($search_page + 1);
                                }
                                else if(isset($_GET["t"])) {
                                    $url = "/search.php?t=" . $_GET["t"] . "&p=" . ($search_page + 1);
                                }
                                else if(isset($_GET["q"])) {
                                    $url = "/search.php?q=" . $_GET["q"] . "&p=" . ($search_page + 1);
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
                            else if(isset($_GET["q"])) {
                                echo "q=" . $_GET["q"];
                            }

                            echo "&p=" . ($search_page + 1);

                        ?>" <?php
                            
                            if($search_nPosts == 0){
                                $dest_page = 0;
                            }
                            else if($search_nPosts % $search_postsLimit == 0){
                                $dest_page = intdiv($search_nPosts, $search_postsLimit) - 1;
                            }
                            else {
                                $dest_page = intdiv($search_nPosts, $search_postsLimit);
                            }
                            
                            if($search_page == $dest_page) echo 'class="link-secondary pe-none" tabindex=-1" style="opacity: .5;"';
                    
                        ?>><span class="search__paginationLink p-1 rounded">Następny</span></a>
                    </nav>
                </div>
            </div>
        </div>

    </body>

</html>