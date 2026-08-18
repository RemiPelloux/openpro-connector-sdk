import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public final class Publish {
    public static void main(String[] args) throws Exception {
        String token = System.getenv("OPENPRO_API_TOKEN");
        if (token == null || token.isBlank()) {
            throw new IllegalStateException("Set OPENPRO_API_TOKEN");
        }

        String json = """
            {"title":"Backend engineer","content":"Ship the API.","location":"Paris","status":"draft","source_url":"https://boards.greenhouse.io/jobs/12","language":"en"}
            """;

        HttpRequest request = HttpRequest.newBuilder()
            .uri(URI.create("https://api.openpro.ai/api/job_posts?language=en"))
            .header("Authorization", "Bearer " + token)
            .header("Accept", "application/json")
            .header("Content-Type", "application/json")
            .header("X-Language", "en")
            .POST(HttpRequest.BodyPublishers.ofString(json))
            .build();

        HttpResponse<String> response = HttpClient.newHttpClient()
            .send(request, HttpResponse.BodyHandlers.ofString());
        if (response.statusCode() >= 400) {
            throw new IllegalStateException("OpenPro API " + response.statusCode());
        }
    }
}
