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
        $_GET["u"] = "";
    }

    if(!preg_match("/^[0-9]+$/", $_GET["u"])) {
        die('Użytkowik nie istnieje. <a href="/index.php">Wróć na stronę główną.</a>');
    }

    $query = "SELECT FROM ";

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

        <title></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/profile.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>


    </body>

</html>