import os

from googleapiclient.discovery import build
import argparse
from google.cloud import vision
import os

parser = argparse.ArgumentParser()
parser.add_argument('--image_url', default='https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/University_of_Cambridge_coat_of_arms.svg/800px-University_of_Cambridge_coat_of_arms.svg.png')
parser.add_argument('--page', default=1, type=int)
parser.add_argument('--results_per_page', default=10, type=int)
parser.add_argument('--claim_date', default="30/09/2021")
args = parser.parse_args()

os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "averitec-a4c4d8a92874.json"

client = vision.ImageAnnotatorClient()
image = vision.Image()
image.source.image_uri = args.image_url

date, month, year = args.claim_date.split("/")
sort_date = year + month + date
start_idx = args.page * args.results_per_page

web_detection = client.web_detection(image=image).web_detection

formatted_results = {
    "items": []
}

if web_detection.pages_with_matching_images:
    for page in web_detection.pages_with_matching_images:
        item = {
            "page_url": page.url,
            "page_title": page.page_title
        }

        if "full_matching_images" in page:
            for image in page.full_matching_images:
                if "matching_images" not in item:
                    item["matching_images"] = [image.url]
                else:
                    item["matching_images"].append(image.url)

        if "partial_matching_images" in page:
            for image in page.full_matching_images:
                if "matching_images" not in item:
                    item["matching_images"] = [image.url]
                else:
                    item["matching_images"].append(image.url)

        formatted_results["items"].append(item)

print(formatted_results)