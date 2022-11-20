<?php 

    session_start();

    if(isset($_SESSION["user_username"])) {
        header('Location: index.php');
    }

    require_once("./reset_vars.php");
    reset_postWorkbench();
    reset_read();
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

        <title>Forum ziomeczków - zaloguj</title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/login.css" rel="stylesheet"/>

    </head>

<body>

    <div class="container-fluid d-flex justify-content-center">

        <div class="login mt-5 d-flex flex-column justify-content-evenly align-items-center border raounded p-4">

            <h1><span class="fs-3 text-secondary text-center">Zaloguj się na swoje konto</span></h1>

            <form class="login__form d-flex flex-column justify-content-center align-items-center h-100" action="/login_val.php" method="post">

                <?php 
                    
                    if(isset($_SESSION["login_msg"])) {
                        echo '<span class="text-danger mb-2">' .$_SESSION["login_msg"]. '</span>';
                        unset($_SESSION["login_msg"]);
                    }

                ?>

                <div class="login__segment">
                    <label to="loginInput">Adres email</label>

                    <!-- email i preserved if the user typed it before -->
                    <input type="text" id="loginInput" name="email" value="<?php 
                        if(isset($_SESSION["login_passedemail"])) echo $_SESSION["login_passedemail"];
                        ?>"/>
                </div>

                <div class="login__segment">
                    <label to="passwdInput">Hasło</label>
                    <input type="password" id="passwdInput" name="passwd"/>
                </div>

                <input type="submit" class="login__formSubmit" value="Zaloguj"/>

            </form>

            <div class="login__noAccount w-100 d-flex justify-content-center">
                <span>Nie masz konta?</span>
                <a href="/register.php" class="link-secondary">Zarejestruj się za darmo</a>
            </div>

        </div>

    </div>

</body>
</html>


