#!/bin/bash

# generate_content(link, nUsers, nPosts, nRatings)
# runs python scripts for generating syntetic users, posts, comments
function generate_content() {
    # link      url to the szniorum version you want to access      str
    # nUsers    how many users you want to create       int
    # nPosts    how many posts each user has to write (categories, tags are chosen automatically)       int
    # nRatings  number of tuples <post_rating, comment, comment_rating> will be taken into account for each user       int

    echo "Generating content --- $(date)";

    _generate_users()

}


function _generate_users()