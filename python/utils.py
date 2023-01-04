from os import path


def check_user_credentials_path(creds_path: str):

    try:
        with open(path.join(creds_path, "users_credentials.txt")) as creds_file:
            
            lines = creds_file.readlines()

            if not lines:
                raise FileNotFoundError

    except FileNotFoundError:
        print("GenerateSynteticPosts --- there are no users, can't create posts")
        exit(0)