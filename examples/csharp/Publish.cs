using System.Net.Http.Headers;
using System.Text;

var token = Environment.GetEnvironmentVariable("OPENPRO_API_TOKEN")
    ?? throw new InvalidOperationException("Set OPENPRO_API_TOKEN");

var json = """
{"title":"Backend engineer","content":"Ship the API.","location":"Paris","status":"draft","source_url":"https://boards.greenhouse.io/jobs/12","language":"en"}
""";

using var client = new HttpClient();
client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
client.DefaultRequestHeaders.Add("X-Language", "en");

var response = await client.PostAsync(
    "https://api.openpro.ai/api/job_posts?language=en",
    new StringContent(json, Encoding.UTF8, "application/json"));
response.EnsureSuccessStatusCode();
