#
#  Course: COSC 4P02
#  Assignment: Group Project
#  Group: 9
#  Version: 1.0
#  Date: April 2024
#
from selenium import webdriver # Import the webdriver module.
from selenium.webdriver.chrome.options import Options as ChromeOptions # Import the ChromeOptions class.
from selenium.webdriver.chrome.service import Service as ChromeService # Import the ChromeService class.
from selenium.webdriver.common.by import By  # Import the By class for locating elements.
from webdriver_manager.chrome import ChromeDriverManager # Import the ChromeDriverManager for managing ChromeDriver binaries.
import pytest # Import the pytest module for testing.

URL = "https://group9portal-eehbdxbhcgftezez.canadaeast-01.azurewebsites.net/index.php" # Our website URL to be tested. NOTE: This URL will change before the end of the project, as we will be changing Azure Subscriptions. The tests remain the same, and can be run on the new URL.

@pytest.fixture(scope="module") 
def browser():
    # 
    # Fixture Browser:
    # This fixture provides a browser instance using Selenium WebDriver. It uses the Chrome browser in headless mode for testing (there is a line that can be commented out to view the GUI). This fixture is automatically invoked by 
    # pytest when a test function includes it as an argument. It allows test functions to interact with the browser and perform actions like navigating to URLs, finding elements, and executing JavaScript.
    # The client object is available for interacting with the app's elements, and it will be cleaned up after the test.
    # 
    options = ChromeOptions() # Create an instance of ChromeOptions to configure the Chrome browser.
    options.add_argument("--headless")  # Run Chrome in headless mode (without a GUI). If this line is commented out, the browser will open in a GUI mode. The test moves very fast in headless mode, but it does show the site.
    service = ChromeService(executable_path=ChromeDriverManager().install()) # Create an instance of ChromeService to manage the ChromeDriver executable.
    driver = webdriver.Chrome(service=service, options=options) # Create an instance of the Chrome WebDriver with the specified service and options.
    yield driver # Yield the driver instance to the test function.
    driver.quit() # Quit the driver after the test function completes.

def test_share_buttons_present(browser):
    #
    # Test Case 1: Testing the presence of the social media share buttons. This will confirm that the buttons are present on the page.
    # Execution: python -m pytest social_media_sharing_test.py -k "test_share_buttons_present" -s -v # Only use -s to view the messages in the test.
    # This method will check if the share buttons for Facebook, Twitter, and Email are present on the page. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL 
    # and check for the presence of the share buttons by their class names.
    # Expected Result: Pass. The share buttons should be present on the page.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_facebook").is_displayed() # Check if the Facebook share button is displayed.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_x").is_displayed() # Check if the X share button is displayed.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_email").is_displayed() # Check if the Email share button is displayed.

def test_share_buttons_clickable(browser):
    #
    # Test Case 2: Testing the clickability of the social media share buttons. This will confirm that the buttons are clickable while on the page.
    # Execution: python -m pytest social_media_sharing_test.py -k "test_share_buttons_clickable" -s -v # Only use -s to view the messages in the test.
    # This method will check if the share buttons for Facebook, Twitter, and Email are clickable on the page. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL 
    # and check for the enabled share buttons by their class names.
    # Expected Result: Pass. The share buttons should be clickable on the page.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_facebook").is_enabled() # Check if the Facebook share button is enabled/clickable.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_x").is_enabled() # Check if the X share button is enabled/clickable.
    assert browser.find_element(By.CLASS_NAME, "a2a_button_email").is_enabled() # Check if the Email share button is enabled/clickable.

def test_facebook_button_url(browser):
    #
    # Test Case 3: Testing that the Facebook share button redirects to the correct Facebook Post URL. This will confirm that the opened URL is correct when the Facebook share button is clicked. 
    # Execution: python -m pytest social_media_sharing_test.py -k "test_facebook_button_url" -s -v # Only use -s to view the messages in the test.
    # This method will check if the Facebook share button redirects to the correct Facebook Post URL when clicked. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL
    # and click the Facebook share button. It then checks if the current (redirect) URL contains "facebook.com" to confirm that the redirect was successful.
    # Expected Result: Pass. The Facebook share button should redirect to the correct Facebook Post URL.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    fb_button = browser.find_element(By.CLASS_NAME, "a2a_button_facebook") # Find the Facebook share button by its class name.
    fb_button.click() # Click the Facebook share button.
    browser.switch_to.window(browser.window_handles[-1]) # Switch to the new window that opens after clicking the button.
    assert "facebook.com" in browser.current_url # Check if the current (redirect) URL contains "facebook.com" to confirm the redirect.

def test_x_button_url(browser):
    #
    # Test Case 4: Testing that the X share button redirects to the correct X Post URL. This will confirm that the opened URL is correct when the X share button is clicked. 
    # Execution: python -m pytest social_media_sharing_test.py -k "test_x_button_url" -s -v # Only use -s to view the messages in the test.
    # This method will check if the X share button redirects to the correct X Post URL when clicked. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL
    # and click the X share button. It then checks if the current (redirect) URL contains "x.com" to confirm that the redirect was successful.
    # Expected Result: Pass. The X share button should redirect to the correct X Post URL.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    x_button = browser.find_element(By.CLASS_NAME, "a2a_button_x") # Find the X share button by its class name.
    x_button.click() # Click the X share button.
    browser.switch_to.window(browser.window_handles[-1]) # Switch to the new window that opens after clicking the button.
    assert "x.com" in browser.current_url # Check if the current (redirect) URL contains "x.com" to confirm the redirect.

def test_email_button_url(browser):
    #
    # Test Case 5: Testing that the email share button redirects to the correct blank URL, and then closes this URL. This will confirm that the redirect window opens and closes correctly when the email share button is clicked. 
    # Execution: python -m pytest social_media_sharing_test.py -k "test_email_button_url" -s -v # Only use -s to view the messages in the test.
    # This method will check if the email share button redirects to the about:blank URL when clicked. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL
    # and click the email share button. It then checks if the current (redirect) URL contains "about:blank" to confirm that a new blank window opens and closes correctly.
    # It also checks if the number of windows is the same as before clicking the email button, which would confirm that the redirect was successful, since Selenium does not check for non-browser windows. This is a workaround to test this functionality.
    # Expected Result: Pass. The email share button should redirect to the correct about:blank URL, and then close this URL.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    email_button = browser.find_element(By.CLASS_NAME, "a2a_button_email") # Find the email share button by its class name.
    initial_window_count = len(browser.window_handles) # Store the initial number of windows before clicking the email button.
    email_button.click() # Click the email share button.
    browser.switch_to.window(browser.window_handles[-1]) # Switch to the new window that opens after clicking the button.
    assert "about:blank" in browser.current_url # Check if the current (redirect) URL contains "about:blank" to confirm the redirect.
    browser.close()  # Close the redirected window
    browser.switch_to.window(browser.window_handles[0]) # Switch back to the original window.
    assert len(browser.window_handles) == initial_window_count # Check if the number of windows is the same as before clicking the email button. This would confirm that the redirect was successful, since Selenium does not check for non-browser windows.

def test_email_template(browser):
    #
    # Test Case 6: Testing the pre-populated email template when the share button is selected. This will confirm that the email template is correct.
    # Execution: python -m pytest social_media_sharing_test.py -k "test_email_template" -s -v # Only use -s to view the messages in the test.
    # This method will check if the email template is pre-populated with the correct subject and body when the Email share button is clicked. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL
    # and check for the pre-populated email template by executing JavaScript to retrieve the subject and body of the email.
    # Expected Result: Pass. The email template should be pre-populated with the correct subject and body.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    subject = browser.execute_script("return a2a_config.templates.email.subject;") # Execute JavaScript to retrieve the subject of the email template.
    body = browser.execute_script("return a2a_config.templates.email.body;") # Execute JavaScript to retrieve the body of the email template.

    assert subject == "Check out this news article" # Check if the subject is correct.
    assert body == "I found this interesting article:\n${link}" # Check if the body is correct.

def test_x_template(browser):
    #
    # Test Case 7: Testing the pre-populated X template when the share button is selected. This will confirm that the X template is correct.
    # Execution: python -m pytest social_media_sharing_test.py -k "test_x_template" -s -v # Only use -s to view the messages in the test.
    # This method will check if the X template is pre-populated with the correct text when the X share button is clicked. It uses the Selenium WebDriver (predefined as a fixture above) to navigate to our URL
    # and check for the pre-populated X template by executing JavaScript to retrieve the text of the X template.
    # Expected Result: Pass. The X template should be pre-populated with the correct text.
    #
    browser.get(URL) # Navigate to the specified URL in our browser instance.
    text = browser.execute_script("return a2a_config.templates.x.text;") # Execute JavaScript to retrieve the text of the X template.
    assert text == "Check out this news article: ${title}\n${link}" # Check if the text is correct.
