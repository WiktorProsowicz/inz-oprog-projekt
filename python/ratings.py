from bs4 import BeautifulSoup
from requests import get as r_get, post as r_post
from dataclasses import dataclass
from typing import List
from time import time
from random import shuffle
from random import random


@dataclass(init=True)
class Rating:
    rating_ispositive: bool
    comment_content: str
    post_link: str


class RatingsGenerator:

    def _get_comments_from_link(self, link: str) -> List[str]:
        
        print(f"RatingsGenerator --- traversing topic - {link}")

        collected_comments = []

        r = r_get(link)

        soup = BeautifulSoup(r.content, "html.parser")

        forumposts = soup.find("div", {"id": "forumposts"})
        form = forumposts.find("form")

        for comment in form.find_all("div", recursive=False):
            inner = comment.find("div", {"class": "inner"})

            collected_comments.append(inner.text)

        return collected_comments


    """
        @param random_shuffle - whether to shuffle ids
        @param wrap_threshold - if > number of colected ids - redo collection to fit, else return all ids
    """
    def _get_posts_ids(self, random_shuffle: bool = True, wrap_threshold: int = 0):

        # getting posts ids
        request_data = {
            "__get_posts_ids": True,
            "__limit": max(self.n_ratings, 100)
        }
        r = r_post(self.link + "/post_bound_scripts.php", data=request_data)

        ids = [int(chunk) for chunk in r.text.strip().replace("\n", "").split(" ")]

        while wrap_threshold > len(ids):
            ids += ids

        if random_shuffle:
            shuffle(ids)

        return ids[:wrap_threshold]

    def _collect_comments(self) -> List[str]:
        
        # there will be picked random category with a specific amount of topics
        categories_to_traverse = []

        r = r_get("https://forum.gildia.pl/index.php")

        soup = BeautifulSoup(r.content, "html.parser")

        table_list = soup.find("table", {"class": "table_list"})
        for topic_tr in table_list.find_all("tr", {"class": "windowbg2"}):

            subject_anchor = topic_tr.find("a", {"class": "subject"})
            categories_to_traverse.append(subject_anchor.get("href"))

        shuffle(categories_to_traverse)

        collected_comments = []
        for cat_index, category_link in enumerate(categories_to_traverse, 1):

            r = r_get(category_link)

            soup = BeautifulSoup(r.content, "html.parser")

            table_grid = soup.find("table", {"class": "table_grid"})
            tbody = table_grid.find("tbody")

            trs = tbody.find_all("tr")
            shuffle(trs)
            for tr in trs:

                main_part = tr.find("td", {"class": "subject"})

                if main_part is None:
                    continue

                first_span = main_part.find("span")
                topic_anchor = first_span.find("a")

                for comment in self._get_comments_from_link(topic_anchor.get("href")):
                    collected_comments.append(comment)

                if len(collected_comments) >= self.n_ratings:
                    return collected_comments[:self.n_ratings]


    def generate_ratings(self) -> List[Rating]:
        
        contents = self._collect_comments()

        posts_ids = self._get_posts_ids(wrap_threshold=self.n_ratings)

        generated_ratings = []

        for comment_content, post_id in zip(contents, posts_ids):
            
            ispositive = random() < .5

            post_link = self.link + f"/read.php?p={post_id}"

            generated_ratings.append(Rating(ispositive, comment_content, post_link))

        return generated_ratings

    def __init__(self, link: str, n_ratings: int = 0) -> None:
        
        self.link = link
        self.n_ratings = n_ratings


if __name__ == "__main__":

    pass