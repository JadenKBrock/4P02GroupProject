import os
import re
from azure.ai.inference import ChatCompletionsClient
from azure.core.credentials import AzureKeyCredential
from dotenv import load_dotenv
from azure.ai.inference.models import SystemMessage, UserMessage


def get_content(content_text, content_type):
    load_dotenv()

    AZURE_ENDPOINT = os.getenv("AZURE_ENDPOINT")
    AZURE_KEY = os.getenv("AZURE_KEY")

    client = ChatCompletionsClient(
        endpoint=os.environ["AZURE_ENDPOINT"],
        credential=AzureKeyCredential(os.environ["AZURE_KEY"])
    )

    MAX_CHARACTERS = "63206" # Facebook: 63,206  Twitter: 280

    response = client.complete(
        messages=[
            SystemMessage(content="You are a helpful assistant."),
            UserMessage(content="Summarize the following article(s) and convert it into a social media post for Facebook. Your task is to read the given article(s), extract key insights, and generate a compelling human-like social media post intended for Facebook - note, the summary is intended to help you get your key ideas out. Make the post sound like a social media post and not simply just a summary of the article(s). Keep in mind that the character limit of a Facebook post is " + MAX_CHARACTERS + " - and that is the MAX, you don't need to necessarily go to or near that limit.\nArticle: " + content_text),
        ],
    )
    cleaned_response = re.sub(r'<think>.*?</think>', '', response.choices[0].message.content, flags=re.DOTALL).strip()
    return cleaned_response

#print("Response:", response.choices[0].message.content)