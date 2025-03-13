from flask import Flask, request, jsonify, render_template_string
import requests

app = Flask(__name__)

# Your SerpAPI key – store this securely in production!
API_KEY = "56c5026acbe60bebb9eb0a8351618ac5ce5adc2981c9f4e97f059b8b8ea8299d"

# Home route that serves a simple HTML page
@app.route('/')
def index():
    html_content = '''
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Keyword Search Aggregator</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            input, button { padding: 10px; font-size: 16px; }
            .result { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        </style>
    </head>
    <body>
        <h1>Keyword Search Aggregator</h1>
        <input type="text" id="keyword" placeholder="Enter keyword">
        <button onclick="search()">Search</button>
        <div id="results"></div>

        <script>
            function search() {
                const keyword = document.getElementById('keyword').value.trim();
                if (!keyword) {
                    alert("Please enter a keyword.");
                    return;
                }
                fetch('/search?q=' + encodeURIComponent(keyword))
                    .then(response => response.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('results');
                        resultsDiv.innerHTML = "";

                        // Display Google Search results if available
                        if (data.google_search && data.google_search.organic_results) {
                            let searchSection = document.createElement('div');
                            searchSection.innerHTML = '<h2>Google Search Results</h2>';
                            data.google_search.organic_results.forEach(item => {
                                let div = document.createElement('div');
                                div.className = 'result';
                                div.innerHTML = `<a href="${item.link}" target="_blank">${item.title}</a><p>${item.snippet || ''}</p>`;
                                searchSection.appendChild(div);
                            });
                            resultsDiv.appendChild(searchSection);
                        }

                        // Display Google News results if available
                        if (data.google_news && data.google_news.news_results) {
                            let newsSection = document.createElement('div');
                            newsSection.innerHTML = '<h2>Google News Results</h2>';
                            data.google_news.news_results.forEach(item => {
                                let div = document.createElement('div');
                                div.className = 'result';
                                div.innerHTML = `<a href="${item.link}" target="_blank">${item.title}</a><p>${item.snippet || ''}</p>`;
                                newsSection.appendChild(div);
                            });
                            resultsDiv.appendChild(newsSection);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching data:", error);
                        alert("An error occurred while fetching data.");
                    });
            }
        </script>
    </body>
    </html>
    '''
    return render_template_string(html_content)

# /search endpoint that queries SerpAPI for Google Search and Google News
@app.route('/search', methods=['GET'])
def search():
    # Get the keyword from query parameters
    keyword = request.args.get('q')
    if not keyword:
        return jsonify({'error': 'Missing query parameter "q"'}), 400

    # Base URL for SerpAPI
    base_url = "https://serpapi.com/search"

    # Parameters for Google Search
    params_search = {
        "engine": "google",
        "q": keyword,
        "gl": "us",
        "hl": "en",
        "num": 5,
        "api_key": API_KEY
    }

    # Parameters for Google News
    params_news = {
        "engine": "google_news",
        "q": keyword,
        "gl": "us",
        "hl": "en",
        "api_key": API_KEY
    }

    try:
        # Make the requests to both endpoints
        response_search = requests.get(base_url, params=params_search)
        response_news = requests.get(base_url, params=params_news)

        # Raise an error if status code is not 200
        response_search.raise_for_status()
        response_news.raise_for_status()

        data_search = response_search.json()
        data_news = response_news.json()

    except requests.RequestException as e:
        return jsonify({'error': f"Error contacting SerpAPI: {e}"}), 500

#Old results
    # Aggregate results
    #aggregated_results = {
     #   "google_search": data_search,
      #  "google_news": data_news
    #}

    #transforming into list format
    google_search_results=data_search.get("organic_results",[])
    google_news_results=data_news.get("news_results",[])

    #Just the link
    google_search_links=[result.get("link") for result in google_search_results if result.get("link")]
    google_news_links=[result.get("link") for result in google_news_results if result.get("link")]

    #keeps results at 5 cause there is no official parameter
    google_news_links=google_news_links[:5]
    
    combined_links=google_search_links+google_news_links

    print(type(combined_links))
    print(combined_links)
    
    return jsonify(combined_links)

    '''
    #Seaparate results
    aggregated_results={
        "google_search_results":google_search_results,
        "google_news_results":google_news_results
    }

    #combine lists
    combined_results=google_search_results+google_news_results

    print(type(combined_results))
    print(combined_results)
    

    return jsonify(combined_results)'
    '''


if __name__ == '__main__':
    app.run(debug=True)
