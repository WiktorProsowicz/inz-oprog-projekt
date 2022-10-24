<?php 

    session_start();

    if(isset($_SESSION["user_username"])) {
        header('Location: index.php');
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

        <title>Forum ziomeczków - załóż konto</title>

        <!-- offline downloaded jquery files for developement-->
        <script src="/jquery/jquery-3.6.1.min.js"></script>
        
        <!-- offline downloaded bootstrap files for developement -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- custom stylesheets -->
        <link href="/style/clearfix.css" rel="stylesheet"/>
        <link href="/style/register.css" rel="stylesheet"/>

    </head>

<body>

    <div class="container-fluid d-flex justify-content-center">

        <div class="register mt-5 d-flex flex-column justify-content-evenly align-items-center border raounded p-4">

            <h1><span class="fs-3 text-secondary text-center">Wpisz dane do rejestracji</span></h1>

            <form class="register__form d-flex flex-column justify-content-center align-items-center h-100" action="/register_val.php" method="post">

                <div class="register__segment">
                    <label to="usernameInput">Login</label>

                    <!-- email i preserved if the user typed it before -->
                    <input type="text" id="usernameInput" name="username" value="<?php 
                        if(isset($_SESSION["register_passedusername"])) echo $_SESSION["register_passedusername"];
                        ?>"/>

                    <?php 
                    
                    if(isset($_SESSION["username_msg"])) {
                        echo '<span class="text-danger">' .$_SESSION["username_msg"]. '</span>';
                        unset($_SESSION["username_msg"]);
                    }

                    ?>

                </div>

                <div class="register__segment">
                    <label to="emailInput">Adres email</label>

                    <!-- email i preserved if the user typed it before -->
                    <input type="email" id="emailInput" name="email" value="<?php 
                        if(isset($_SESSION["register_passedemail"])) echo $_SESSION["register_passedemail"];
                        ?>"/>

                    <?php 
                    
                    if(isset($_SESSION["email_msg"])) {
                        echo '<span class="text-danger">' .$_SESSION["email_msg"]. '</span>';
                        unset($_SESSION["email_msg"]);
                    }

                    ?>

                </div>

                <div class="register__segment">
                    <label to="passwdInput">Hasło</label>
                    <input type="password" id="passwdInput" name="passwd"/>

                    <?php 
                    
                        if(isset($_SESSION["passwd_msg"])) {
                            echo '<span class="text-danger">' .$_SESSION["passwd_msg"]. '</span>';
                            unset($_SESSION["passwd_msg"]);
                        }

                    ?>

                </div>

                <div class="register__segment">
                    <label to="passwdAgainInput">Powtórz hasło</label>
                    <input type="password" id="passwdAgainInput" name="passwdAgain"/>

                    <?php 
                    
                        if(isset($_SESSION["passwdAgain_msg"])) {
                            echo '<span class="text-danger">' .$_SESSION["passwdAgain_msg"]. '</span>';
                            unset($_SESSION["passwdAgain_msg"]);
                        }

                    ?>

                </div>

                <input type="submit" class="register__formSubmit" value="Załóż konto"/>

            </form>

            <div class="register__noAccount w-100 d-flex justify-content-center">
                <span>Masz już konto?</span>
                <a href="/login.php" class="link-secondary">Zaloguj się</a>
            </div>

        </div>

    </div>

</body>
</html>


