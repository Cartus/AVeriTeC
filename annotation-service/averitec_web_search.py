from googleapiclient.discovery import build
import argparse

parser = argparse.ArgumentParser()
parser.add_argument('--query', default='gay frogs infowars')
parser.add_argument('--page', default=1, type=int)
parser.add_argument('--results_per_page', default=10, type=int)
parser.add_argument('--claim_date', default="30/09/2021")
args = parser.parse_args()

api_key = " AIzaSyAzGwvRt9C8KfdxcNtdXIsEGvI0hYTdo2g"
search_engine_id = "14256c6500607070e"

start_idx = args.page * args.results_per_page

date, month, year = args.claim_date.split("/")
sort_date = year + month + date

misinfo_list = [
    ""
]

def google_search(search_term, api_key, cse_id, **kwargs):
    service = build("customsearch", "v1", developerKey=api_key)
    res = service.cse().list(q=search_term, cx=cse_id, **kwargs).execute()
    return res['items']

results = google_search(
    args.query,
    api_key,
    search_engine_id,
    num=args.results_per_page,
    start=start_idx,
    sort="date:r:19000101:"+sort_date,
    dateRestrict=None
)

formatted_results = {
    "items": []
}

for result in results:
    item = {
        "url": result["link"],
        "header": result["htmlTitle"],
        "abstract": result["htmlSnippet"]
    }
    formatted_results["items"].append(item)

print(formatted_results)