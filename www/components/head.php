<script src="/js/head.js"></script>

<div class="w-100 head-holder">
    <div class="head d-flex justify-content-between align-items-center">

        <a class="head__logoHolder" href="/">
            <img src="/media/logo.png" alt="site's logo"/>
        </a>

        <div class="head__search">
            <form action="/search.php" method="get" class="d-flex align-items-center">
                <input type="text" placeholder="wpisz tag, kategorię, tytuł" name="q" class="head__searchInput" autocomplete="off" tabindex="0"/>

                <button type="submit" value="Wyszukaj" class="head__searchBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>    
                </button>
            </form>

            <div class="head__searchPopup px-3 py-1">
                <ul class="head__searchPopupList d-flex flex-column">

                </ul>
            </div>
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
                    <div class="head__profilePopup border rounded">

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