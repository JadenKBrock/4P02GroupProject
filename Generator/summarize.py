from web_scrape_clean_html import scrape_and_clean
from testAIFoundryLLM import get_content
import sys
import json

if __name__ == "__main__":
    input = sys.argv[1] if len(sys.argv) > 1 else "No Input"


    #url = "https://www.bbc.com/news/articles/cz9eddnnq4go"
    #url = "https://www.woodlandtrust.org.uk/blog/2024/03/frodsham-podcast/"
    url = "https://en.wikipedia.org/wiki/Isaac_Newton"
    content_type = "FACEBOOK POST"
    clean_html = scrape_and_clean(url)

    post = get_content(clean_html, content_type)
    result = {"message": post}
    print(json.dumps(result))

# possibly make it so less content is sent back to the LLM from the web scraper
# also make the LLM prompt better - make it so it sounds more like a social media post and not simply just a summary.
# 


