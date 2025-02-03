import os
import requests
from flask import Flask, render_template, request

app = Flask(__name__)

# Retrieve API key from the environment variable
NEWS_API_KEY = os.environ.get('NEWS_API_KEY')
print(NEWS_API_KEY)
print("testing")

# Route for the root URL can accept GET and POST
@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        topic = request.form.get('topic') #retrieves search topic entered
        
        
        api_url = 'https://newsapi.org/v2/everything' #end point URL for News API
        params = {
            'q': topic,
            'apiKey': NEWS_API_KEY,
            'language': 'en',
            'sources': 'bbc-news, cbc-news', # here to put the sources of the news, u can find the ID from the newsapi.org ,should change it to a variable (like topic in q)
            'from': '2021-01-01', # Date range for the articles, here is the form, should change it to a variable (like topic in q)
            'to': '2021-01-31',
            'pageSize': 10  # Limit to 10 articles for now
        }
        
        # Make the API request
        response = requests.get(api_url, params=params)
        data = response.json() #converts  JSON response from API
        
        links = [] #holds article links
        if data.get('status') == 'ok' and data.get('articles'):
            # Extract the URL for each article
            links = [article['url'] for article in data['articles']]
        else:
            links = ["No articles found for this topic."]
        
        return render_template('results.html', topic=topic, links=links)
    
    return render_template('index.html')

if __name__ == '__main__':
    app.run(debug=True)
