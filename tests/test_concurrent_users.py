import pytest
import threading
import requests
import time
import json

"""
test_concurrent_users.py

This test simulates 100 concurrent users interacting with SmartSummaries.
It rotates between 3 real user accounts to simulate multiple sessions, each performing the following actions:

1. Logs into the web portal using a valid username and password.
2. Submits a dummy "generate" request (does not trigger the full backend generation delay).
3. Simulates saving a generated post via the save_post.php API.
4. Verifies that the saved post appears in the user’s dashboard with a retry loop.
5. Records success or failure for each user session and fails the test if any user flow fails.

This script uses threading and isolated sessions to accurately mimic real concurrent usage behavior on the backend.
To run: pytest test_concurrent_users.py -s -v
"""


# CONFIGURATION of azure paths

#endpoints for login, dashboard and generate
BASE_URL = "https://group9portal-eehbdxbhcgftezez.canadaeast-01.azurewebsites.net"
LOGIN_URL = f"{BASE_URL}/src/Login/login_pageNew.php"
DASHBOARD_URL = f"{BASE_URL}/index.php"
GENERATE_URL = f"{BASE_URL}/src/Generate/generate_page.php"
SAVE_URL = f"{BASE_URL}/src/Generate/save_post.php"

# List of login credentials to simulate different sessions
test_accounts = [
    {"username": "tom", "password": "tom"},
    {"username": "admin5", "password": "try5"},
    {"username": "admin", "password": "try1"}
]

# Generate 100 "virtual" users reusing the 3 real accounts (this is just so I wouldn't have to type in 100 different credentials
# technically you'd be able to use 1 real account if you wanted too)
simulated_users = []
for i in range(100):
    account = test_accounts[i % len(test_accounts)]
    simulated_users.append({
        "id": i,
        "username": account["username"],
        "password": account["password"]
    })



#simulate the behaviour of one user: login, generate post, and save post while verifying its in the dashboard
#These run threads simulating concurrent simulation
def simulate_user_flow(user_id, username, password, results):
    try:
        with requests.Session() as session:
            # 1. Login
            login_payload = {"username": username, "password": password}
            login_response = session.post(LOGIN_URL, data=login_payload)
            assert login_response.status_code == 200, f"[{username}-{user_id}] Login failed"

            # 2. Navigates to dashboard
            dash_response = session.get(DASHBOARD_URL)
            assert dash_response.status_code == 200, f"[{username}-{user_id}] Dashboard failed to load"

            # 3. Simulate post generation request (not actually using server-side and the "generate" button)
            #As we are using free resources and APIs the time it takes to generate a post will always be around 30 seconds
            unique_keyword = f"pytest-load-{user_id}-{int(time.time())}"
            generate_payload = {"Canada": unique_keyword}
            session.post(GENERATE_URL, data=generate_payload)

            # 4. Simulate saving a generated post ("fake" post)
            fake_post = {
                "post_content": f"This is a test post for {unique_keyword}",
                "post_type": "Facebook"
            }

            #sets content type to JSON for save_post.php to parse it correctly
            headers = {"Content-Type": "application/json"}
            save_response = session.post(SAVE_URL, data=json.dumps(fake_post), headers=headers)
            assert save_response.status_code == 200, f"[{username}-{user_id}] Save post failed"
            assert "Post saved successfully" in save_response.text, f"[{username}-{user_id}] Unexpected save response: {save_response.text}"
            print(f"[{username}-{user_id}] Saved test post.")

            # 5. Retry dashboard to confirm post appeared 
            #error loop just in case the post does not go through the first time
            found = False
            for attempt in range(3):
                dashboard_check = session.get(DASHBOARD_URL)
                if dashboard_check.status_code == 200 and fake_post["post_content"] in dashboard_check.text:
                    found = True
                    break
                print(f"[{username}-{user_id}] Retry {attempt+1}: Post not found yet...")
                time.sleep(3)

            assert found, f"[{username}-{user_id}] Saved post not found in dashboard after retries"
            results[user_id] = "Success"

    except Exception as e:
        results[user_id] = f"Fail: {e}"



#test for all users in parallel using threads
def test_concurrent_100_users():
    threads = []
    results = [None] * len(simulated_users) #To store success or failure for each user
    start_time = time.time() #track test duration

#thred for each user
    for user in simulated_users:
        thread = threading.Thread(
            target=simulate_user_flow,
            args=(user["id"], user["username"], user["password"], results)
        )
        threads.append(thread)
        thread.start()

    for thread in threads:
        thread.join()

    duration = time.time() - start_time
    print(f"\nAll 100 users completed in {duration:.2f} seconds")
    for i, result in enumerate(results):
        print(f"User {i+1}: {result}")

    # Fail test if any result failed
    for i, result in enumerate(results):
        assert result == "Success", f"[User {i+1}] failed: {result}"
