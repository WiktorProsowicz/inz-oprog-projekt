from typing import List, Dict, Tuple
from dataclasses import dataclass
from requests import get as r_get
from bs4 import BeautifulSoup, Tag
from random import choice, random, shuffle, randint


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
    forum_historia_categories_links: List["str"] = []
    wattpad_stories_links: List[str] = []

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

            joined_tag = joined_tag[:20]

            tags.append(joined_tag)

        return (title_span.text, content, tags)

    def _collect_from_forumhistoria(self) -> Tuple[str, str, List[str]]:

        if not self.forum_historia_categories_links:

            r = r_get("https://dzieje.pl/")
            soup = BeautifulSoup(r.content, "html.parser")

            modal_view = soup.find("section", {"class": "s99"})
            categories = modal_view.find("ul", {"class": "categories"})

            for category in categories.find_all("div", {"class": "category"}):

                anchor = category.find("a")
                self.forum_historia_categories_links.append(anchor.get("href"))

        r = r_get("https://dzieje.pl" + choice(self.forum_historia_categories_links))
        soup = BeautifulSoup(r.content, "html.parser")

        main_section = soup.find("section", {"class": "s19"})
        articles_list = main_section.find("div", {"class": "m49"})

        list_item = choice(articles_list.find_all("li"))
        anchor = list_item.find("a")

        r = r_get("https://dzieje.pl" + anchor.get("href"))
        soup = BeautifulSoup(r.content, "html.parser")

        title_block = soup.find("header", {"class": "titleblock"})
        header = title_block.find("h1")

        article_block = soup.find("article", {"class": "articleBlock"})
        field = article_block.find("div", {"class": "field"})

        post_content = ""
        for paragraph in field.find_all("p", recursive=False):
            post_content += "\n" + paragraph.text

        tags = []
        tags_div = soup.find("div", {"class": "m45"})
        for tag_item in tags_div.find_all("li", {"class": "tag"}):
            tag = "_".join(tag_item.text.split(" "))
            tags.append(tag[:20])

        return (header.text, post_content, tags)

    def _collect_from_wattpad(self) -> Tuple[str, str, List[str]]:

        if not self.wattpad_stories_links:
            r = r_get("https://www.wattpad.com/stories/fanfik/new?locale=pl_PL", headers={'User-Agent': 'Mozilla/5.0'})
            soup = BeautifulSoup(r.content, "html.parser")

            items_list = soup.find("article", {"id": "browse-results-item-view"})
            for item in items_list.find_all("div", {"class": "browse-story-item"}, recursive=False):

                link_anchor = item.find("a", {"class": "title"})
                self.wattpad_stories_links.append("https://www.wattpad.com" + link_anchor.get("href"))

        r = r_get(choice(self.wattpad_stories_links), headers={'User-Agent': 'Mozilla/5.0'})
        soup = BeautifulSoup(r.content, "html.parser")

        story_title_div = soup.find("div", {"class": "story-info__title"})

        tag_items = soup.find("ul", {"class": "tag-items"})
        tags = []
        for tag_li in tag_items.find_all("li"):
            tags.append(tag_li.text[:20])

        story_parts = soup.find("div", {"class": "story-parts"})
        story_parts_list = story_parts.find_all("a", {"class": "story-parts__part"})

        chosen_story_part = choice(story_parts_list)
        story_link = "https://www.wattpad.com" + chosen_story_part.get("href")

        r = r_get(story_link, headers={'User-Agent': 'Mozilla/5.0'})
        soup = BeautifulSoup(r.content, "html.parser")

        reading_panel = soup.find("div", {"class": "panel-reading"})
        story_content = ""
        for paragraph in reading_panel.find_all("p"):
            story_content += "\n" + paragraph.text

        return (story_title_div.text + " - " + chosen_story_part.text, story_content, tags)

    def _collect_from_jeja(self) -> Tuple[str, str, List[str]]:

        r = r_get(f"https://dowcipy.jeja.pl/nowe,0,0,{randint(1, 888)}.html")
        soup = BeautifulSoup(r.content, "html.parser")

        ob_left_box = soup.find("div", {"class": "ob-left-box"})
        boxes = ob_left_box.find_all("div", {"class": "dow-box"})

        chosen_box = choice(boxes)

        text_box = chosen_box.find("div", {"class": "dow-left-text"})

        return (text_box.text[:20] + "...", text_box.text, [])

    

    def _collect_post_contents(self, category: str) -> Tuple[str, str, List[str]]:
        """harvests title, content and tags based on category
        """

        # return self._collect_from_jeja()

        if category == "Inne":
            return self._collect_from_wikipedia()

        elif category == "Historia":
            return self._collect_from_forumhistoria()

        elif category == "Fanfiki":
            return self._collect_from_wattpad()

        elif category == "Kawały":
            return self._collect_from_jeja()

        else:
            return ("", "", [])


    def generate_posts(self) -> List[Post]:
        
        generated_posts = []

        for _ in range(self.n_posts):
            
            # for 'Other' category advantage
            post_category = "Inne" if random() < .3 else choice(list(self.category_id_map.keys()))

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