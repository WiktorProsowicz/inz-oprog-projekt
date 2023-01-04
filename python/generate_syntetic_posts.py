from argparse import ArgumentParser
from posts import PostGenerator
from random import choice
from os import path
import requests

# number of posts that will be posted by each one user
# POSTS_PER_USER = 3

if __name__ == "__main__":
    
    argparser = ArgumentParser()
    argparser.add_argument("link")
    argparser.add_argument("nPosts")
    argparser.add_argument("maxPostsPerUser")
    argparser.add_argument("credsPath")
    args = argparser.parse_args()

    post_generator = PostGenerator(args.link, int(args.nPosts))

    generated_posts = post_generator.generate_posts()
        
    # collecting credentials
    try:

        with open(path.join(args.credsPath, "users_credentials.txt")) as creds_file:
            
            lines = creds_file.readlines()

            if not lines:
                raise FileNotFoundError

    except FileNotFoundError:
        print("GenerateSynteticPosts --- there are no users, can't create posts")
        exit(0)
    
    users_creds = [tuple(line.rstrip("\n").split(" ")) for line in lines]


    if len(users_creds) * int(args.maxPostsPerUser) < int(args.nPosts):
        user_input = input(f"There are more posts to create than the specified limit {args.nPosts} vs {len(users_creds) * int(args.maxPostsPerUser)}.\n \
        Continue and truncate the amount of posts? [y/n] ")

        if user_input == "n":
            print("Exiting...")
            exit(0)
    
    session = requests.session()

    for post_index, post in enumerate(generated_posts):

        # getting a new user
        if post_index % int(args.maxPostsPerUser) == 0:
            u_login, u_email, u_passwd = users_creds.pop(0)

        print(f"GenerateSynteticPosts --- saving post '{post.title}' by {u_login}")

        # signing in
        request_data = {
            "email": u_email,
            "passwd": u_passwd
        }
        r = session.post(args.link + "/login_val.php", request_data)

        request_data = {
            "editedPostContent": post.content,
            "editedPostTitle": post.title,
            "editedPostTags": " ".join(post.tags),
            "editedPostCat": PostGenerator.category_id_map[post.category]
        }
        r = session.post(args.link + "/save_post.php", request_data)

        

        # signing out
        r = session.post(args.link + "/logout.php")

        
    session.close()

    