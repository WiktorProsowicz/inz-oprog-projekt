<?php 

    session_start();

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

    $read_postshort = "Random post";

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

        <!-- rendering first x characters of the postfor browser page indentification -->
        <title><?php echo $read_postshort;?></title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/read.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>

        <div class="container-fluid p-5">

            <div class="read mx-auto bg-light border">

                <div class="row p-5">

                    <div class="read__left col-10">
                        <div class="read__mainText border p-3">

                            Razem młodzi przyjaciele!</br>
                            W szczęściu wszystkiego są wszystkich cele;</br>
                            Jednością silni, rozumni szałem,</br>
                            Razem młodzi przyjaciele!</br>
                            I ten szczęśliwy, kto padł wśród zawodu,</br>
                            Jeżeli poległem ciałem</br>
                            Dał innym szczebel do sławy grodu.</br>
                            Razem, młodzi przyjaciele!</br>
                            Choć droga stroma i śliska,</br>
                            Gwałt i słabość bronią wchodu:</br>
                            Gwałt niech się gwałtem odciska,</br>
                            A ze słabością łamać uczmy się za młodu!</br>

                        </div>
                    </div>

                    <div class="read__right col-2 d-flex flex-column">

                        <div class="read__authorInfo d-flex flex-column">

                            <!-- <span class="text-secodary mb-4">Autor: </span> -->

                            <img  class="read__authorInfoImg align-self-center" src="/media/user_profile_template.png"/>
                            <span class="align-self-center"><a href="#" class="link-secondary fw-bold ">Marian Kowalski</a></span>
                            
                            <div class="read__rating d-flex justify-content-center w-100 mt-2">

                                <div class="read__ratingItem">
                                    <img src="/media/like.png"/>
                                    <span>25<span>
                                </div>

                                <div class="read__ratingItem">
                                    <img src="/media/dislike.png"/>
                                    <span>47<span>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </body>

</html>
