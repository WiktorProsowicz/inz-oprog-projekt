from argparse import ArgumentParser
from users import UsersGenerator
import requests
from os import path, mkdir


if __name__ == "__main__":
    
    argParser = ArgumentParser()

    argParser.add_argument("link")
    argParser.add_argument("nUsers")
    argParser.add_argument("savePath")

    args = argParser.parse_args()

    with open(path.join(args.savePath, "users_credentials.txt"), "a") as f:
        pass
        
    with open(path.join(args.savePath, "users_credentials.txt"), "r") as creds_f:

        blacklist =  [tuple(line.split(" ")[0:2]) for line in creds_f.readlines()]


    with open(path.join(args.savePath, "users_credentials.txt"), "a") as creds_f:

        session = requests.session()

        users_generator = UsersGenerator(int(args.nUsers), blacklist)
        for user in users_generator.generate_users():
            
            print(f"GenerateSynteticUsers --- registering user {user.username}")

            # registering the user
            request_data = {
                "username": user.username,
                "email": user.email,
                "passwd": user.password,
                "passwdAgain": user.password
            }
            r = session.post(args.link + "/register_val.php", data=request_data)

            # changing description
            request_data = {
                "profileSettings_changedDesc": user.description
            }
            r = session.post(args.link + "/user_bound_scripts.php", data=request_data)

            if not path.isdir(path.join(args.savePath, "profile_imgs")):
                mkdir(path.join(args.savePath, "profile_imgs"))

            # downloading profile image
            r = requests.get(user.profile_img_link)
            
            image_save_path = path.join(args.savePath, "profile_imgs", user.profile_img_link.split("/")[-1])

            with open(image_save_path, "wb") as f:
                f.write(r.content)

            # changing user's profile image
            with open(image_save_path, "rb") as f:
                files = {"added_profileimg": (image_save_path.split("/")[-1], f, image_save_path.split(".")[-1])}
                r = session.post(args.link + "/user_bound_scripts.php", files=files)

            # TODO add users subscriptions generation

            # signing out
            r = session.post(args.link + "/logout.php")

            # dumping info about the user
            creds_f.write(f"{user.username} {user.email} {user.password}\n")

        session.close()

