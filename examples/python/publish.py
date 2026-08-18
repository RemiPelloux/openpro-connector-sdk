import os
import sys

import requests

token = os.environ.get("OPENPRO_API_TOKEN")
if not token:
    sys.exit("Set OPENPRO_API_TOKEN")

response = requests.post(
    "https://api.openpro.ai/api/job_posts",
    headers={
        "Authorization": f"Bearer {token}",
        "Accept": "application/json",
        "X-Language": "en",
    },
    json={
        "title": "Backend engineer",
        "content": "Ship the API.",
        "location": "Paris",
        "status": "draft",
        "source_url": "https://boards.greenhouse.io/jobs/12",
        "language": "en",
    },
    timeout=30,
)
response.raise_for_status()
