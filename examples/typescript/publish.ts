const token = process.env.OPENPRO_API_TOKEN;
if (!token) {
  throw new Error('Set OPENPRO_API_TOKEN');
}

const response = await fetch('https://api.openpro.ai/api/job_posts?language=en', {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${token}`,
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Language': 'en',
  },
  body: JSON.stringify({
    title: 'Backend engineer',
    content: 'Ship the API.',
    location: 'Paris',
    status: 'draft',
    source_url: 'https://boards.greenhouse.io/jobs/12',
    language: 'en',
  }),
});

if (!response.ok) {
  throw new Error(`OpenPro API ${response.status}: ${await response.text()}`);
}
