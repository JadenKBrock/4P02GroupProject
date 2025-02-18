from serpapi import GoogleSearch
import json  # 导入json模块

params = {
    "api_key": "56c5026acbe60bebb9eb0a8351618ac5ce5adc2981c9f4e97f059b8b8ea8299d",
    "engine": "google_news",
    "q": "Trump slaps 25% tariffs on steel and aluminum imports 'without exceptions' ",
    "gl": "us",
    "hl": "en",
    # "num": "10",# not working
    # "so": "0",
    # "location": "canada",
}

search = GoogleSearch(params)
results = search.get_dict()

# 将结果写入JSON文件
with open('results.json', 'w', encoding='utf-8') as f:
    json.dump(results, f, ensure_ascii=False, indent=4)

print(results)


