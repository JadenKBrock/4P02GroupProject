import subprocess
import requests
import time

# 启动 Flask 应用
subprocess.Popen(['python', 'app.py'])

# 等待应用启动
time.sleep(10)

# 测试登录功能
login_url = 'http://localhost/Login/login.php'  # 修改为实际的PHP登录页面URL
login_data = {
    'email': 'tongshijie4@gmail.com',
    'password': 'Tsj123456+'
}

try:
    response = requests.post(login_url, data=login_data)
    if response.status_code == 200:
        print('登录成功')
    else:
        print('登录失败')
except requests.exceptions.ConnectionError:
    print('无法连接到服务器，请检查应用是否已启动。')
