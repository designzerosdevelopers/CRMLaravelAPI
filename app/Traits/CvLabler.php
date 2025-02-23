<?php

namespace App\Traits;
use OpenAI;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use App\Models\Cv;
use Illuminate\Support\Arr;


trait CvLabler {
    
    public function chatgpt($pdfFilePath)
    {

    $pdfFilePaths = 'D:/laravel-code/portfolio/public/cv.pdf';
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFilePaths);
    $text = $pdf->getText();
        
        $apiKey = getenv('OPENAI_API_KEY');
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
                [  'role' => 'user', 'content' => $text],
            ],
        ]);

      $cv = json_decode($result->choices[0]->message->content, true);
      dd($cv);
      $workExperienceJson = !blank(Arr::get($cv, 'work_experience')) ? json_encode($cv['work_experience']) : null;
      $educationJson = !blank(Arr::get($cv, 'education')) ? json_encode($cv['education']) : null;


    Cv::create([
        'name' => $cv['name']?? null,
        'email' => $cv['email']?? null,
        'phone' => $cv['phone']?? null,
        'address' => $cv['address']?? null,
        'interest' => $cv['interests']?? null,
        'achievements' => $cv['achievements']?? null,
        'nationality' => $cv['nationality']?? null,
        'language' => $cv['languages']?? null,
        'education' => $educationJson,
        'work_experience' => $workExperienceJson
    ]);

    }

}

?>