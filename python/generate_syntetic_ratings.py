from argparse import ArgumentParser
from os import path
from ratings import RatingsGenerator
from requests import session as r_session
from random import shuffle


if __name__ == "__main__":
    
    argparser = ArgumentParser()
    argparser.add_argument("link", type=str)
    argparser.add_argument("nRatings", type=int)
    argparser.add_argument("maxRatingsPerUser", type=int)
    argparser.add_argument("credsPath", type=str)

    args = argparser.parse_args()

    # collecting credentials
    try:
        with open(path.join(args.credsPath, "users_credentials.txt")) as creds_file:
            
            lines = creds_file.readlines()

            if not lines:
                raise FileNotFoundError

    except FileNotFoundError:
        print("GenerateSynteticRatings --- there are no users, can't create ratings")
        exit(0)

    users_creds = [tuple(line.rstrip("\n").split(" ")) for line in lines]
    shuffle(users_creds)

    if len(users_creds) * args.maxRatingsPerUser < args.nRatings:
        user_input = input(f"There are more ratings to create than the specified limit {args.nRatings} vs {len(users_creds) * args.maxRatingsPerUser}.\n \
        Continue and truncate the amount of ratings? [y/n] ")

        if user_input == "n":
            print("Exiting...")
            exit(0)

    ratings_generator = RatingsGenerator(args.link, args.nRatings)

    generated_ratings = ratings_generator.generate_ratings()


    session = r_session()

    for rating_index, rating in enumerate(generated_ratings):
        
        if rating_index % args.maxRatingsPerUser == 0:

            if not users_creds:
                break

            login, email, passwd = users_creds.pop(0)

        print(f"GenerateSynteticRatings --- rating post {rating.post_link} and posting comment '{rating.comment_content[:20]}' by user {login}")

        # signing in
        r_data = {
            "email": email,
            "passwd": passwd
        }
        r = session.post(args.link + "/login_val.php", data=r_data)

        # visiting the post
        r = session.get(rating.post_link)

        # rating the post
        r_data = {
            "is_like": rating.rating_ispositive
        }
        r = session.post(args.link + "/post_bound_scripts.php", data=r_data)

        # posting a comment
        r_data = {
            "commentsAdded": rating.comment_content
        }
        r = session.post(args.link + "/post_bound_scripts.php", data=r_data)

        # signing out
        r = session.get(args.link + "/logout.php")
        
    session.close()