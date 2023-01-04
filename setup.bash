#!/bin/bash

HOME_PATH=$(cd "$(dirname -- "${BASH_SOURCE[0]}")" && pwd);

# reinitialize()
# cleans and initializes the project
function reinitzialize() {
    # cleaning the project
    rm -fr "$HOME_PATH/python/venv";
    rm -fr "$HOME_PATH/.cache"

    initialize;
}

# initialize()
# prepares project
function initialize() {
    
    mkdir -p "$HOME_PATH/.cache/profile_imgs";
    
    cd "$HOME_PATH/python" || return;

    python3 -m venv venv;
    source ./venv/bin/activate;
    ./venv/bin/python3 -m pip install --ignore-installed -r "./requirements.txt";

    deactivate;
    cd ..;

}

# generate_content(link, nUsers, nPosts, nRatings)
# runs python scripts for generating syntetic users, posts, comments
function generate_content() {
    # link      url to the szniorum version you want to access      str
    # nUsers    how many users you want to create       int
    # nPosts    how many posts each user has to write (categories, tags are chosen automatically)       int
    # nRatings  number of tuples <post_rating, comment, comment_rating> will be taken into account for each user       int

    _check_args $# 4 || return;

    _check_link $1 || return;

    echo "Generating content --- $(date) - $1";

    # _generate_users $1 $2 "$HOME_PATH/.cache";

    _generate_posts $1 $3 3 "$HOME_PATH/.cache";

}

# _generate_users(link, nUsers, savePath)
# script creates n random users and saves their credentials in txt file
function _generate_users() {
    # link      url to szniorum version     str
    # nUsers    how many users to create    int
    # savePath  path to txt file to save credentials to     path

    _check_args $# 3 || return;

    _check_link $1 || return;

    touch $3;
    "$HOME_PATH/python/venv/bin/python3" "$HOME_PATH/python/generate_syntetic_users.py" $1 $2 $3;
}

# _generate_posts(link, nPosts, maxPostsPerUser, credsPath)
# script generates n random posts based on sources connected with target categories
# i.e category 'Informations' -> get data from wikipedia
function _generate_posts() {
    # link      url to szniorum version      str
    # nPosts    how many posts to generate   int
    # maxPostsPerUSer   maximal number of posts per user to create  int
    # credsPath path to take users credentials from     str

    _check_args $# 4 || return;

    _check_link $1 || return;

    "$HOME_PATH/python/venv/bin/python3" "$HOME_PATH/python/generate_syntetic_posts.py" $1 $2 $3 $4;
}

# _generate_ratings(link, nRatings, maxRatingsPerUser, credsPath)
function _generate_ratings() {
    # link      url to szniorum version      str
    # nRatings    how many <comment, postRating, commentRating> to generate   int
    # maxRatingsPerUSer   maximal number of ratings per user to create  int
    # credsPath path to take users credentials from     str

    _check_args $# 4 || return;

    _check_link $1 || return;

    "$HOME_PATH/python/venv/bin/python3" "$HOME_PATH/python/generate_syntetic_ratings.py" $1 $2 $3 $4;
}


# _check_args(gotArgs, expectedArgs)
function _check_args() {

    if [ $1 != $2 ]; then
        echo "[${FUNCNAME[1]}] Passed $1 args instead of expected $2. Aborting";
        return 1;
    fi

}

# _check_link(link)
function _check_link() {
    
    isGood=$(curl -Is $1 | head -1 | grep -c "200 OK");

    if [ "$isGood" != 1 ]; then
        echo "$1 is not working. Exiting"
        return 1;
    fi

    return 0;

}