<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use App\Models\Cv;
use Illuminate\Support\Arr;

class PdfLabeler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pdfFilePath;
    protected $user;

    public function __construct($pdfFilePath, $user = null)
    {
        $this->pdfFilePath = $pdfFilePath;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('Starting PdfLabeler job', ['pdfFilePath' => $this->pdfFilePath]);

        // Ensure the file exists before processing.
        if (!file_exists($this->pdfFilePath)) {
            \Log::error('PDF file not found', ['path' => $this->pdfFilePath]);
            return;
        }

        try {
            // Parse PDF to extract text
            $parser = new Parser();
            $pdf = $parser->parseFile($this->pdfFilePath);
            $text = $pdf->getText();
            \Log::info('PDF parsed successfully', ['extracted_text' => substr($text, 0, 200)]);
        } catch (\Exception $e) {
            \Log::error('Error parsing PDF', ['error' => $e->getMessage()]);
            return;
        }

        // Get Together AI API Key
        $apiKey = env('TOGETHER_API_KEY');
        if (!$apiKey) {
            \Log::error('Together AI API key not set');
            return;
        }

        // Define the request payload
        $data = [
            "model" => "meta-llama/Llama-3.3-70B-Instruct-Turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Given a CV extracted data, your task is to label the information in JSON format.

                    {
                        \"name\": \"John Doe\",
                        \"email\": \"john.doe@example.com\",
                        \"phone\": \"+1234567890\",
                        \"address\": \"Sample address\",
                        \"education\": [
                            {
                                \"institute\": \"Sample institute\",
                                \"location\": \"Sample institute location\",
                                \"degree\": \"Sample Degree title\",
                                \"duration\": \"2010-2014\"
                            }
                        ],
                        \"work_experience\": [
                            {
                                \"company\": \"Sample Company\",
                                \"location\": \"City A, Country X\",
                                \"position\": \"Software Engineer\",
                                \"duration\": \"Jan-2015 – Dec-2017\",
                                \"description\": \"Worked on various software projects.\"
                            }
                        ],
                        \"interests\": \"Technology, Photography, Travel\",
                        \"achievements\": \"Received the Innovation Awards.\",
                        \"nationality\": \"Citizen of Country Z\",
                        \"languages\": \"English, Spanish\"
                    }"
                ],
                [
                    "role" => "user",
                    "content" => $text,
                ],
            ],
        ];

        \Log::info('Sending request to Together AI', ['payload' => $data]);

        // Make API request to Together AI
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type'  => 'application/json',
            ])->post('https://api.together.xyz/v1/chat/completions', $data);
        } catch (\Exception $e) {
            \Log::error('HTTP request failed', ['error' => $e->getMessage()]);
            return;
        }

        \Log::info('Received API response', ['status' => $response->status(), 'body' => $response->body()]);

        // Handle API response
        if (!$response->successful()) {
            \Log::error('Together AI API error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return;
        }

        $result = $response->json();
        if (!isset($result['choices'][0]['message']['content'])) {
            \Log::error('Invalid API response format', ['response' => $result]);
            return;
        }

        // Extract JSON using a generic regex that finds the first JSON object.
        try {
            $jsonContent = $result['choices'][0]['message']['content'];
            // Use regex to extract the first JSON object (from the first '{' to the last '}')
            if (preg_match('/(\{.*\})/s', $jsonContent, $matches)) {
                $jsonContent = $matches[1];
            } else {
                throw new \Exception('Unable to extract JSON from response');
            }

            $cvData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }
        } catch (\Exception $e) {
            \Log::error('Error decoding JSON from API', [
                'error' => $e->getMessage(),
                'raw_content' => $result['choices'][0]['message']['content']
            ]);
            return;
        }

        \Log::info('API returned valid JSON', ['cvData' => $cvData]);

        // Map API keys to database fields. Adjust these mappings as needed.
        $dbData = [
            'user_id'       => optional($this->user)->id ?? null,
            'name'          => $cvData['name'] ?? null,
            // Use 'email' directly from the API response
            'email'         => $cvData['email'] ?? null,
            'phone'         => $cvData['phone'] ?? null,
            // If 'address' is not provided, fallback to 'location'
            'address'       => $cvData['address'] ?? ($cvData['location'] ?? null),
            // Map 'interests' if available; otherwise, you might choose to store the career objective or leave null.
            'interest'      => $cvData['interests'] ?? null,
            'achievements'  => $cvData['achievements'] ?? null,
            'nationality'   => $cvData['nationality'] ?? null,
            'language'      => $cvData['languages'] ?? null,
            // Use the API's 'education' key as is.
            'education'     => !empty(Arr::get($cvData, 'education')) ? json_encode($cvData['education']) : null,
            // Note: The API returned 'workExperience' (camelCase) instead of 'work_experience'
            'work_experience' => !empty(Arr::get($cvData, 'workExperience')) ? json_encode($cvData['workExperience']) : null,
        ];

        // Store extracted data into the database
        try {
            Cv::create($dbData);
            \Log::info('CV successfully saved to the database.', ['dbData' => $dbData]);
        } catch (\Exception $e) {
            \Log::error('Error inserting CV into database', ['error' => $e->getMessage()]);
        }
    }
}
