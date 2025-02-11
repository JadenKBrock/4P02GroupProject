import os
import requests
from flask import Flask, render_template, request
from newspaper import Article

app = Flask(__name__)

# Retrieve API key from the environment variable
NEWS_API_KEY = os.environ.get('NEWS_API_KEY')

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        topic = request.form.get('topic')
        url = request.form.get('url')

        if url:
            article_data = parse_article_data(url)
            links = [url]
            return render_template('results.html', topic=topic, links=links, article_data=article_data)
        else:
            api_url = 'https://newsapi.org/v2/everything'
            params = {
                'q': topic,
                'apiKey': NEWS_API_KEY,
                # 'apiKey': 'a8df4da9d1404336829dc02e9d89de51',     <- remove hardcoded API key
                'language': 'en',
                'sources': 'bbc-news, cbc-news',
                'from_param': '2001-01-01',
                'to': '2025-01-31',
                'pageSize': 10
            }
            
            session = requests.Session()
            response = session.get(api_url, params=params)

            # Handle API failure
            if response.status_code != 200:
                return render_template('results.html', topic=topic, links=["Failed to fetch articles."])

            data = response.json()

            links = []
            if data.get('status') == 'ok' and data.get('articles'):
                links = [article['url'] for article in data['articles']]
            else:
                links = ["No articles found for this topic."]

            return render_template('results.html', topic=topic, links=links)

    return render_template('index.html')

def parse_article_data(url):
    article = Article(url)
    # Handle article parsing error
    try:
        article.download()
        article.parse()
    except Exception as e:
        return {'error': f'Failed to parse article: {str(e)}'}


    return {
        'content': article.text,
        'author': article.authors,
        'publish_date': article.publish_date,
    }

if __name__ == '__main__':
    app.run(debug=True)