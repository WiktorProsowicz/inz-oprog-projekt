<div class="w-100">
    <div class="head d-flex justify-content-between align-items-center">

        <a class="head__logoHolder" href="/">
            <img src="/media/logo.png" alt="site's logo"/>
        </a>

        <div class="head__search">
            <form action="/search.php" method="get" class="d-flex align-items-center">
                <input type="text" placeholder="wpisz tag, kategorię" name="q" class="head__searchInput"/>
                <input type="submit" value="Wyszukaj" class="head__searchBtn"/>
            </form>
        </div>

        <div class="head__profile">

        <?php

            if(isset($_SESSION["user_username"])) {

                if($_SESSION["user_profileimg"] != null) {
                    $img_tag = '<img class="head__profileImg" src="data:image/jpg;charset=utf8;base64,' .$_SESSION["user_profileimg"]. '" alt="user profile img"/>';
                }
                else {
                    $img_tag = '<img class="head__profileImgTemplate" src="/media/user_profile_template.png" alt="user profile img"/>';
                }

                echo '
                    <div class="head__profileBanner d-flex align-items-center justify-content-center link-secondary">
                        '.$img_tag.'
                
                        <span class="head__profileName user-select-none text-center">' .$_SESSION["user_username"]. '</span>

                        <img src="/media/arrow_down.png" class="head__profileArrow"/>
                    </div>
                    ';

                echo '
                    <div class="head__profilePopup">

                        <ul class="d-flex flex-column justify-content-center p-2">

                            <li><a href="/profile.php?u='.$_SESSION["user_username"].'">Mój profil</a></li>
                            <hr />
                            <li><a href="/logout.php">Wyloguj</a></li>

                        </ul>

                    </div>
                    ';
            }
            else {

                echo '
                    <a href="/login.php" class="head__profileBanner d-flex align-items-center justify-content-center link-secondary">
                        <img class="head__profileImgTemplate" src="/media/user_profile_template.png" alt="user profile img"/>
                
                        <span class="head__profileName">zaloguj się</span>
                    </a>
                    ';
            }
  
        ?>

        </div>

    </div>
</div>