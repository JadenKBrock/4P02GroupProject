from flask import Flask, render_template, request, redirect, url_for, session, jsonify
from flask_session import Session
import os
import json
import logging
import pyodbc
from datetime import datetime

# here need to change to real connection string
connection_string = "Driver={ODBC Driver 17 for SQL Server};Server=tcp:4p02-sql-server.database.windows.net,1433;Initial Catalog=4P02-SQL-Database;Persist Security Info=False;User ID=4P02-SQL-Server-User;Password=4P02-SQL-Server-Password;MultipleActiveResultSets=False;Encrypt=True;TrustServerCertificate=False;Connection Timeout=30;"

app = Flask(__name__)
app.secret_key = 'your_secret_key'
app.config['SESSION_TYPE'] = 'filesystem'
Session(app)

API_KEY = "56c5026acbe60bebb9eb0a8351618ac5ce5adc2981c9f4e97f059b8b8ea8299d"

# 模拟的用户数据
users = {"user1": "pass1", "user2": "pass2"}

def save_search_history_to_azure_sql(keyword, results):
    try:
        # 连接到Azure SQL数据库
        conn = pyodbc.connect(connection_string)
        cursor = conn.cursor()

        # 创建表（如果不存在）
        cursor.execute('''
            IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='search_history' AND xtype='U')
            CREATE TABLE search_history (
                id INT IDENTITY(1,1) PRIMARY KEY,
                keyword NVARCHAR(255) NOT NULL,
                link NVARCHAR(255) NOT NULL,
                source NVARCHAR(255) NOT NULL,
                timestamp DATETIME DEFAULT GETDATE()
            )
        ''')

        # 提取链接和来源
        search_links = [{'link': item.get('link'), 'source': 'Google Search'} for item in results['google_search'].get('organic_results', [])]
        news_links = [{'link': item.get('link'), 'source': 'Google News'} for item in results['google_news'].get('news_results', [])]

        # 合并所有链接
        all_links = search_links + news_links

        # 插入数据到数据库
        for link_info in all_links:
            cursor.execute('''
                INSERT INTO search_history (keyword, link, source)
                VALUES (?, ?, ?)
            ''', (keyword, link_info['link'], link_info['source']))

        # 提交事务
        conn.commit()
        logging.info(f"Search history saved for keyword: {keyword}")

    except Exception as e:
        logging.error(f"Failed to save search history: {e}")
    finally:
        # 关闭数据库连接
        conn.close()
        
            

@app.route('/')
def index():
    return redirect(url_for('login'))

@app.route('/search', methods=['GET'])
def search():
    keyword = request.args.get('q')
    if not keyword:
        return jsonify({'error': 'Missing query parameter "q"'}), 400
    
    # Base URL 
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

        # error if status code is not 200
        response_search.raise_for_status()
        response_news.raise_for_status()

        data_search = response_search.json()
        data_news = response_news.json()
        
    except requests.RequestException as e:
        return jsonify({'error': f"Error contacting SerpAPI: {e}"}), 500

    # Aggregate results
    aggregated_results = {
        "google_search": data_search,
        "google_news": data_news
    }
    
    # Save search history
    save_search_history_to_azure_sql(keyword, aggregated_results)
    
    return jsonify(aggregated_results)

# 登录页面
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        
        try:
            # 连接到Azure SQL数据库
            conn = pyodbc.connect(connection_string)
            cursor = conn.cursor()

            # 查询用户信息
            cursor.execute('SELECT password FROM users WHERE username = ?', (username,))
            row = cursor.fetchone()

            if row and row.password == password:
                session['username'] = username
                return redirect(url_for('dashboard'))
            else:
                return render_template('login.html', error="Invalid credentials")

        except Exception as e:
            logging.error(f"Failed to authenticate user: {e}")
            return render_template('login.html', error="An error occurred during login")

        finally:
            # 关闭数据库连接
            conn.close()

    return render_template('login.html')

# 仪表盘页面 (登录后可访问)
@app.route('/dashboard')
def dashboard():
    if 'username' in session:
        return render_template('dashboard.html', username=session['username'])
    return redirect(url_for('login'))

@app.route('/history', methods=['GET'])
def get_history():
    try:
        # 连接到Azure SQL数据库
        conn = pyodbc.connect(connection_string)
        cursor = conn.cursor()

        # 查询历史记录
        cursor.execute('SELECT keyword, link, source, timestamp FROM search_history ORDER BY timestamp DESC')
        rows = cursor.fetchall()

        # 将查询结果转换为字典列表
        history = [{'keyword': row.keyword, 'link': row.link, 'source': row.source, 'timestamp': row.timestamp} for row in rows]

        return jsonify(history)

    except Exception as e:
        logging.error(f"Failed to retrieve search history: {e}")
        return jsonify([])

    finally:
        # 关闭数据库连接
        conn.close()


# 登出
@app.route('/logout')
def logout():
    session.pop('username', None)
    return redirect(url_for('login'))

if __name__ == "__main__":
    app.run(debug=True)
