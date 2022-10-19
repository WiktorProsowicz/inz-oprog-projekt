<!Doctype html>
<html lang="pl">

    <head>

        <meta name="charset" content="utf-8">
        <meta name="author" content="Wiktor Prosowicz" />
        <meta name="description" content="Forum internetowe, gdzie możesz publikować wiersze, dowcipy i jakąkolwiek inną treść tekstową :)">
        <meta name="keywords" content="forum, poems, wiersze, publikuj, publikowanie" />
        <meta http-equiv="x-ua-compatibile" content="chrome=1,ie=edge" />
        <meta name="viewport" content="width = device-width, initial-scale = 1.0">

        <title>Forum ziomeczków - strona główna</title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <style>
            a {
                text-decoration: none !important;
            }
        </style>
        <link href="/style/head.css" rel="stylesheet"/>
        <link href="/style/index.css" rel="stylesheet"/>

    </head>

    <body>

        <?php 
            require "./components/head.php";
        ?>

        <div class="main d-flex">

            <div class="main__categories">

                <ul class="main__categoriesList">

                    <?php 

                        // hard-coded categories array
                        $categories = array(
                            "śmieszne", "wiersze", "wiadomości", "zainteresowania", "lifehacki", "opowiadania", "gore", "opowiadania erotyczne"
                        );

                        foreach ($categories as $cat) {
                            echo '
                                <li>
                                    <a href="/search.php?c='.$cat.'">'
                                        .$cat.
                                    '</a>
                                </li>
                            ';
                        }

                    ?>

                </ul>

            </div>


            <div class="main__grid">


            </div>

        </div>

    </body>

</html>