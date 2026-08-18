package main

import (
	"bytes"
	"log"
	"net/http"
	"os"
)

func main() {
	token := os.Getenv("OPENPRO_API_TOKEN")
	if token == "" {
		log.Fatal("Set OPENPRO_API_TOKEN")
	}

	payload := []byte(`{"title":"Backend engineer","content":"Ship the API.","location":"Paris","status":"draft","source_url":"https://boards.greenhouse.io/jobs/12","language":"en"}`)
	req, err := http.NewRequest(http.MethodPost, "https://api.openpro.ai/api/job_posts?language=en", bytes.NewBuffer(payload))
	if err != nil {
		log.Fatal(err)
	}
	req.Header.Set("Authorization", "Bearer "+token)
	req.Header.Set("Accept", "application/json")
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("X-Language", "en")

	res, err := http.DefaultClient.Do(req)
	if err != nil {
		log.Fatal(err)
	}
	defer res.Body.Close()
	if res.StatusCode >= 400 {
		log.Fatalf("OpenPro API %s", res.Status)
	}
}
