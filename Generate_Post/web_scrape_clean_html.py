import requests
from bs4 import BeautifulSoup
import re
from langdetect import detect

def get_clean_content(url):
    try:
        # get the page
        headers = {'User-Agent': 'Mozilla/5.0'}
        response = requests.get(url, headers=headers)
        response.raise_for_status()

        # html parser
        soup = BeautifulSoup(response.text, 'html.parser')

        # extract the main content from one of the tags
        main_content = soup.select_one('article, main, .content, .post, .entry')
        
        if not main_content:
            return "Main content not found. The entire page will be returned."
        
        # remove specific tags from the content
        for script in main_content(["script", "style", "nav", "li", "footer", "aside"]):
            script.decompose()

        # extract the text
        text = main_content.get_text(separator='\n', strip=True).split('\n')
       
        
        # extract only english text
        english_lines = []
        for line in text:
            line = line.strip()
            # remove lines <= 10 characters to reduce noise
            if len(line) <= 10:
                continue
            try:
                if detect(line) == "en":  # Detect language
                    english_lines.append(line)
            except:
                continue  # Skip lines that can't be detected
        
        return " ".join(english_lines)

    except requests.exceptions.RequestException as e:
        return f"Error: {e}"

# clean up the text by removing CRLF and quotes
def clean_text(text):
    text = text.replace('\n', ' ').replace('\r', ' ').replace('"', '|') 
    text = re.sub(r'\s+', ' ', text)
    return text.strip()


def scrape_and_clean(url):
    return clean_text(get_clean_content(url))


#url = "https://www.bbc.com/news/articles/cz9eddnnq4go"
#url = "https://www.woodlandtrust.org.uk/blog/2024/03/frodsham-podcast/"
#url = "https://en.wikipedia.org/wiki/Isaac_Newton"
#content = get_clean_content(url)
#content = clean_text(content)
#print(content) 