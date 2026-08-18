# frozen_string_literal: true

require "json"
require "net/http"

token = ENV.fetch("OPENPRO_API_TOKEN")
uri = URI("https://api.openpro.ai/api/job_posts?language=en")
request = Net::HTTP::Post.new(uri)
request["Authorization"] = "Bearer #{token}"
request["Accept"] = "application/json"
request["Content-Type"] = "application/json"
request["X-Language"] = "en"
request.body = JSON.dump(
  title: "Backend engineer",
  content: "Ship the API.",
  location: "Paris",
  status: "draft",
  source_url: "https://boards.greenhouse.io/jobs/12",
  language: "en",
)

response = Net::HTTP.start(uri.host, uri.port, use_ssl: true) { |http| http.request(request) }
raise "OpenPro API #{response.code}" if response.code.to_i >= 400
