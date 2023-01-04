from bs4 import BeautifulSoup
from requests import get as r_get
from dataclasses import dataclass
from typing import List
from time import time
from random import shuffle


@dataclass(init=True)
class Rating:
    rating_ispositive: bool
    comment_content: str
    post_link: str


class RatingsGenerator:

    def _get_comments_from_link(self, link: str) -> List[str]:
        
        print(f"RatingsGenerator --- traversing topic - {link}")

        r = r_get(link)

        soup = BeautifulSoup(r.content, "html.parser")

        

    def _collect_comments(self) -> List[str]:
        
        # there will be picked random category with a specific amount of topics
        categories_to_traverse = []

        r = r_get("https://forum.gildia.pl/index.php")

        soup = BeautifulSoup(r.content, "html.parser")

        table_list = soup.find("table", {"class": "table_list"})
        for topic_tr in table_list.find_all("tr", {"class": "windowbg2"}, recursive=True):

            subject_anchor = topic_tr.find("a", {"class": "subject"}, recursive=True)
            categories_to_traverse.append(subject_anchor.get("href"))

        shuffle(categories_to_traverse)

        collected_comments = []
        for cat_index, category_link in enumerate(categories_to_traverse, 1):

            r = r_get(category_link)

            soup = BeautifulSoup(r.content, "html.parser")

            table_grid = soup.find("table", {"class": "table_grid"}, recursive=True)
            for tr in table_grid.find_all("tr"):

                main_part = tr.find("td", {"class": "subject"})
                first_span = main_part.find("span", recursive=True)
                topic_anchor = first_span.find("a")

                for comment in self._get_comments_from_link(topic_anchor.get("href")):
                    collected_comments.append(comment)

                if len(collected_comments) >= self.n_ratings:
                    return collected_comments[:self.n_ratings]


    def generate_ratings(self) -> List[Rating]:
        pass

    def __init__(self, link: str, n_ratings: int = 0) -> None:
        
        self.link = link
        self.n_ratings = n_ratings


if __name__ == "__main__":

    link = "https://www.youtube.com/watch?v=9fRLACBPb9E&ab_channel=mietczynski"

    t1 = time()

    r = r_get(link)

    t2 = time()

    print(t2 - t1, "\n\n\n")

    with open("file.txt", "w") as f:
        f.write(BeautifulSoup(r.content, "html.parser").prettify())