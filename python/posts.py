from typing import List, Dict, Tuple
from dataclasses import dataclass
from requests import get as r_get
from bs4 import BeautifulSoup, Tag
from random import choice, random


@dataclass(init=True)
class Post:
    title: str
    content: str
    tags: List[str]
    category: str

    def __repr__(self):
        return f"{self.title} - {self.category} - {self.tags}\n{self.content}"


class PostGenerator:

    category_id_map: Dict[str, int] = {}

    def _fill_category_map(self, link) -> None:
        
        r = r_get(link)

        soup = BeautifulSoup(r.content, "html.parser")

        for index, cat_link in enumerate(soup.find_all("a", {"class": "main__categoryLink"}, recursive=True), 1):
            self.category_id_map[cat_link.text] = index

    def _collect_from_wikipedia(self) -> Tuple[str, str, List[str]]:
        
        r = r_get("https://pl.wikipedia.org/wiki/Specjalna:Losowa_strona")

        soup = BeautifulSoup(r.content, "html.parser")

        # collecting title
        heading = soup.find("h1", {"id": "firstHeading"})
        title_span = heading.find("span", {"class": "mw-page-title-main"})

        # collecting content
        body_content = soup.find("div", {"id": "bodyContent"})
        content_text = body_content.find("div", {"id": "mw-content-text"})
        paragraphs_container = content_text.find("div", {"class": "mw-parser-output"})

        content = ""
        for paragraph in paragraphs_container.find_all("p", recursive=False):
            parapgraph_text = ""

            for child in paragraph.children:
                if child.name != "sup":
                    parapgraph_text += child.text

            content += parapgraph_text + "\n"


        # collecting tags
        catlinks = soup.find("div", {"id": "catlinks"})
        catlinks_list = catlinks.find("ul")

        tags = []
        for category_link in catlinks_list.find_all("a", recursive=True):
            tag_chunks = category_link.text.split(" ")
            joined_tag = "_".join(tag_chunks)

            if len(joined_tag) > 20:
                joined_tag = joined_tag[:20]

            tags.append(joined_tag)

        return (title_span.text, content, tags)
        

    def _collect_post_contents(self, category: str) -> Tuple[str, str, List[str]]:
        """harvests title, content and tags based on category
        """

        # dev
        return self._collect_from_wikipedia() 

        if category == "Inne":
            return self._collect_from_wikipedia()

        else:
            return ("", "", [])


    def generate_posts(self) -> List[Post]:
        
        generated_posts = []

        for _ in range(self.n_posts):
            
            # for 'Other' category advantage
            post_category = "Inne" if random() < .5 else choice(list(self.category_id_map.keys()))

            title, content, tags = self._collect_post_contents(post_category)

            generated_posts.append(Post(title, content, tags, post_category))
            
        return generated_posts

    def __init__(self, link: str, n_posts: int = 0) -> None:
        self.n_posts = n_posts

        if len(self.category_id_map) == 0:
            self._fill_category_map(link)


if __name__ == "__main__":
    postgen = PostGenerator("http://localhost", 1)
    print(postgen.generate_posts()[0])