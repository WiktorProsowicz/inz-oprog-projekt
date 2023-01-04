from argparse import ArgumentParser
from os import path


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


    