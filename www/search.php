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

    if(isset($_POST["p"])) {
        $search_page = $_POST["p"];
    }
    else {
        $search_page = 0;
    }

    $search_postsLimit = 20;

    if(isset($_POST["c"])) {
        $query = sprintf("SELECT p.title AS title, p.id AS id, c.name AS category, CONCAT(SUBSTRING(p.content, 1, 200), '...') AS short 
                        FROM posts p JOIN users u ON p.author_id = u.id JOIN categories c ON c.id = p.category_id
                        WHERE c.name = '%s' ORDER BY p.modified DESC LIMIT %d OFFSET %d;", 
                        $connection->real_escape_string($_POST["c"]), $search_postsLimit, $search_page * $search_postsLimit);

        $result = $connection->query($query);

        $search_rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free_result();

        $search_valid = true;
    }
    else if(isset($_POST["t"])) {
        $query = sprintf("SELECT p.title AS title, p.id AS id, c.name AS category, CONCAT(SUBSTRING(p.content, 1, 200), '...') AS short 
                        FROM tags_in_posts tip JOIN posts p ON p.id = tip.post_id JOIN categories c ON c.id = p.category_id 
                        JOIN users u ON u.id = p.author_id JOIN tags t ON t.id = tip.tag_id

                        WHERE t.name = '%s' ORDER BY p.modified DESC LIMIT %d OFFSET %d;",
                        $connection->real_escape_string($_POST["t"]), $search_postsLimit, $search_page * $search_postsLimit);

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

        <div class="container-fluid p-5">

        </div>

    </body>

</html>