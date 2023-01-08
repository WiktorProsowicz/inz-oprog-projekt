from dataclasses import dataclass
from typing import List, Tuple, Dict
from bs4 import BeautifulSoup, Tag
from random import choice
import requests

@dataclass(init=True, repr=True)
class User:
    username: str
    email: str
    password: str
    description: str
    profile_img_link: str = ""


class UsersGenerator:

    source_link = "https://www.wykop.pl/aktywne/"

    email_suffixes = ["gmail.com", "interia.pl", "o2.pl", "wp.pl", "onet.pl", "outlook.com", "gazeta.pl"]

    def _get_profile_img_link(self, username: str):

        r = requests.get(f"https://www.wykop.pl/ludzie/{username}/")

        soup = BeautifulSoup(r.content, "html.parser")

        photo_div = soup.find("div", {"class": "photo"}, recursive=True)
        profile_img_tag: Tag = photo_div.find("img")

        return profile_img_tag.get("src", "")

    def _collect_usernames_and_descriptions(self) -> Dict[str, str]:
        
        usernames_blacklist = {pair[0] for pair in self.blacklist}
        collected_usernames = dict()

        # collecting links to posts
        posts_to_traverse = []
        r = requests.get(self.source_link)

        soup = BeautifulSoup(r.content, "html.parser")

        for post_container in soup.find_all("div", {"class": "article", "data-type": "link"}, recursive=True):

            title_link: Tag = post_container.find("h2")

            if title_link is None:
                continue

            title_link = title_link.find("a")
            posts_to_traverse.append(title_link.get("href"))

        print(f"UsersGenerator --- traversing {len(posts_to_traverse)} posts")

        for post_nr, post_link in enumerate(posts_to_traverse, 1):

            print(f"UsersGenerator --- post nr {post_nr} - {post_link}")

            r = requests.get(post_link)
            soup = BeautifulSoup(r.content, "html.parser")

            for comment in soup.find_all("div", {"class": "wblock", "data-type": "comment"}, recursive=True):

                author_div = comment.find("div", {"class": "author"})
                nickname: Tag = author_div.find("a")

                text_div: Tag = comment.find("div", {"class": "text"}).find("p")
                comment_content = text_div.contents[-1].strip(": \t\n@")
                
                if nickname.text not in usernames_blacklist:
                    collected_usernames[nickname.text] = comment_content

                    if len(collected_usernames) >= self.n_users:
                        return collected_usernames

            
        print(f"UsersGenerator --- collected only {len(collected_usernames)} usernames")
        return dict()


    def generate_users(self) -> List[User]:

        generated_users = []
        
        print(f"UsersGenerator --- collecting usernames for {self.n_users} users")
        
        unames_descs = self._collect_usernames_and_descriptions()
        emails_blacklist = {pair[1] for pair in self.blacklist}

        for username, description in unames_descs.items():
            email = username.lower() + "@" + choice(self.email_suffixes)

            # email must be unique
            while email in emails_blacklist:
                email = "k" + email

            password = username[::-1] + "123"
            
            profile_img = self._get_profile_img_link(username)

            password = "".join(char for char in password if char.isalnum())
            username = "".join(char for char in username if char.isalnum())

            generated_users.append(User(username, email, password, description, profile_img))

        return generated_users

    def __init__(self, n_users: int = 0, blacklist: List[Tuple[str, str]] = []) -> None:
        
        self.n_users = n_users
        self.blacklist = blacklist

