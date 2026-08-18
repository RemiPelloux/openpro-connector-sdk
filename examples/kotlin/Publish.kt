import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody

fun main() {
    val token = System.getenv("OPENPRO_API_TOKEN") ?: error("Set OPENPRO_API_TOKEN")
    val json = """{"title":"Backend engineer","content":"Ship the API.","location":"Paris","status":"draft","source_url":"https://boards.greenhouse.io/jobs/12","language":"en"}"""
    val body = json.toRequestBody("application/json".toMediaType())
    val request = Request.Builder()
        .url("https://api.openpro.ai/api/job_posts?language=en")
        .addHeader("Authorization", "Bearer $token")
        .addHeader("X-Language", "en")
        .post(body)
        .build()
    OkHttpClient().newCall(request).execute().use { response ->
        check(response.isSuccessful) { "OpenPro API ${response.code}" }
    }
}
