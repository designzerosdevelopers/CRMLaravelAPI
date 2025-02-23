<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
use OpenAI;
use Smalot\PdfParser\Parser;
use App\Models\Cv;
use Illuminate\Support\Arr;

class TokenController extends Controller
{


    public function generateToken()
    {

        // Check if the user is authenticated
        if (auth()->check()) {
            // Get the authenticated user
            $user = auth()->user();
            // Check if the user already has an API token
            if ($user->tokens()->count() > 0) {
                // Revoke all existing tokens
                $user->tokens()->delete();
            }
            // Issue an API token for the authenticated user
            $token = $user->createToken('api-token')->plainTextToken;

            // Return the token to the client
            return response()->json(['token' => $token]);
        }
    }

    public function chatgpt()
    {

        $pdfFilePaths = 'D:/laravel-code/portfolio/public/cv.pdf';
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFilePaths);
        $text = $pdf->getText();

        $apiKey = env('OPENAI_API_KEY');
        $client = OpenAI::client($apiKey);
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Given a CV extrected data, your task is label information. 
                                  All data in json format.
                    
                    {
                        "name": "John Doe",
                        "email": "john.doe@example.com",
                        "phone": "+1234567890",
                        "address": "Sample address",
                        "education": [
                            {
                                "institute": "Sample institute",
                                "location": "Sample institute location",
                                "degree": "Sample Degree title",
                                "duration": "2010-2014"
                            }
                             // Add more education entries as needed
                        ],
                        "work_experience": [
                            {
                                "company": "Sample Company",
                                "location": "City A, Country X",
                                "position": "Software Engineer",
                                "duration": "Jan-2015 – Dec-2017",
                                "description": "Worked on various software projects."
                            }
                            // Add more work history entries as needed
                        ],
                            "interests": "Technology, Photography, Travel",
                            "achievements": "Received the Innovation Awards.",
                            "nationality": "Citizen of Country Z",
                            "languages": "English, Spanish"
                    }',
                ],
                ['role' => 'user', 'content' => $text],
            ],
        ]);

        $cv = json_decode($result->choices[0]->message->content, true);
        $workExperienceJson = !blank(Arr::get($cv, 'work_experience')) ? json_encode($cv['work_experience']) : null;
        $educationJson = !blank(Arr::get($cv, 'education')) ? json_encode($cv['education']) : null;


        Cv::create([
            'name' => $cv['name'] ?? null,
            'email' => $cv['email'] ?? null,
            'phone' => $cv['phone'] ?? null,
            'address' => $cv['address'] ?? null,
            'interest' => $cv['interests'] ?? null,
            'achievements' => $cv['achievements'] ?? null,
            'nationality' => $cv['nationality'] ?? null,
            'language' => $cv['languages'] ?? null,
            'education' => $educationJson,
            'work_experience' => $workExperienceJson
        ]);
    }
}
