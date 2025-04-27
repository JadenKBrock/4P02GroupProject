import pytest
import requests
import json
import time
from unittest.mock import patch

"""
test.py

This test file is used to test the generation functionality of SmartSummaries. It contains the following test cases:

1. Test basic generation functionality
2. Test invalid input handling
3. Test generation result quality
4. Test concurrent generation request handling
"""

# Configure Azure paths
BASE_URL = "https://group9portal-eehbdxbhcgftezez.canadaeast-01.azurewebsites.net"
GENERATE_URL = f"{BASE_URL}/src/Generate/run_generate_py.php"
SAVE_URL = f"{BASE_URL}/src/Generate/save_post.php"

def test_basic_generation():
    """
    Test Case 1: Test basic generation functionality
    Execute: python -m pytest test.py -k "test_basic_generation" -s -v
    This test case verifies if the generation functionality works properly, including:
    1. Sending generation request
    2. Verifying response status
    3. Verifying generation result quality
    Expected result: Pass. Generation functionality should work properly and return valid results.
    """
    test_keyword = "Canada"
    with requests.Session() as session:
        # 1. Send generation request
        generate_payload = {
            "format_type": "Facebook",
            "keyword": test_keyword,
            "url_index": 0
        }
        headers = {"Content-Type": "application/json"}
        response = session.post(GENERATE_URL, json=generate_payload, headers=headers)
        assert response.status_code == 200, "Generation request failed"
        
        # 2. Verify generation result
        try:
            result = response.json()
            assert "response" in result, "Generation result missing response field"
            assert len(result["response"]) > 0, "Generation result is empty"
            assert isinstance(result["response"], str), "Generation result is not string type"
            print(f"Generation result: {result['response']}")
        except json.JSONDecodeError:
            print(f"Server returned non-JSON response: {response.text}")
            assert False, "Server returned non-JSON response"

def test_invalid_input():
    """
    Test Case 2: Test invalid input handling
    Execute: python -m pytest test.py -k "test_invalid_input" -s -v
    This test case verifies the generation functionality's handling of invalid inputs, including:
    1. Empty input
    2. Special character input
    3. Overly long input
    Expected result: Pass. Generation functionality should handle invalid inputs properly.
    """
    invalid_inputs = ["", "!!!", "a" * 1000]
    headers = {"Content-Type": "application/json"}
    
    for input_text in invalid_inputs:
        with requests.Session() as session:
            generate_payload = {
                "format_type": "Facebook",
                "keyword": input_text,
                "url_index": 0
            }
            response = session.post(GENERATE_URL, json=generate_payload, headers=headers)
            assert response.status_code == 200, f"Invalid input '{input_text}' handling failed"
            try:
                result = response.json()
                assert "error" in result or "response" in result, "Response missing required fields"
            except json.JSONDecodeError:
                print(f"Server returned non-JSON response: {response.text}")
                assert False, "Server returned non-JSON response"

def test_result_quality():
    """
    Test Case 3: Test generation result quality
    Execute: python -m pytest test.py -k "test_result_quality" -s -v
    This test case verifies the quality of generation results, including:
    1. Content completeness
    2. Content coherence
    3. Content length
    Expected result: Pass. Generation results should meet quality requirements.
    """
    test_keyword = "Canada"
    with requests.Session() as session:
        generate_payload = {
            "format_type": "Facebook",
            "keyword": test_keyword,
            "url_index": 0
        }
        headers = {"Content-Type": "application/json"}
        response = session.post(GENERATE_URL, json=generate_payload, headers=headers)
        assert response.status_code == 200, "Generation request failed"
        
        try:
            result = response.json()
            content = result["response"]
            
            # Verify content length
            assert len(content) > 100, "Generated content is too short"
            assert len(content) < 10000, "Generated content is too long"
            
            # Verify content completeness
            assert test_keyword.lower() in content.lower(), "Generated content does not contain keyword"
            assert "." in content, "Generated content missing sentence terminator"
            
            print(f"Generated content length: {len(content)} characters")
        except json.JSONDecodeError:
            print(f"Server returned non-JSON response: {response.text}")
            assert False, "Server returned non-JSON response"

def test_concurrent_generation():
    """
    Test Case 4: Test concurrent generation request handling
    Execute: python -m pytest test.py -k "test_concurrent_generation" -s -v
    This test case verifies the system's ability to handle concurrent generation requests, including:
    1. Sending multiple generation requests simultaneously
    2. Verifying all request processing
    Expected result: Pass. System should handle concurrent requests properly.
    """
    import threading
    
    def make_generation_request(keyword, results):
        try:
            with requests.Session() as session:
                generate_payload = {
                    "format_type": "Facebook",
                    "keyword": keyword,
                    "url_index": 0
                }
                headers = {"Content-Type": "application/json"}
                response = session.post(GENERATE_URL, json=generate_payload, headers=headers)
                results[keyword] = response.status_code == 200
        except Exception as e:
            results[keyword] = str(e)
    
    keywords = ["Canada", "USA", "UK", "France", "Germany"]
    results = {}
    threads = []
    
    # Create and start threads
    for keyword in keywords:
        thread = threading.Thread(
            target=make_generation_request,
            args=(keyword, results)
        )
        threads.append(thread)
        thread.start()
    
    # Wait for all threads to complete
    for thread in threads:
        thread.join()
    
    # Verify results
    for keyword, result in results.items():
        assert result is True, f"Generation request failed for keyword '{keyword}'"
    
    print(f"Concurrent test results: {results}")
