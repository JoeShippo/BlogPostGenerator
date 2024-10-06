<?php
require 'vendor/autoload.php'; // Load Composer dependencies
require 'config.php';

use GuzzleHttp\Client;
use PhpOffice\PhpWord\PhpWord;

$apiKey = getenv('OPENAI_API_KEY'); // Fetch API key from environment variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is actually a CSV
    $fileMime = mime_content_type($_FILES["file"]["tmp_name"]);
    if ($fileType != "csv" || $fileMime != 'text/plain') {
        echo "Sorry, only CSV files are allowed.";
        exit;
    }

    // Attempt to upload file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
        processCSV($target_file, $_POST['context'], $_POST['cta']);
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

function sanitizeFileName($fileName) {
    return preg_replace('/[^a-zA-Z0-9-_\.]/', '', $fileName);
}

function processCSV($file, $context, $cta) {
    $handle = fopen($file, "r");
    if ($handle !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $topic = trim($data[0]);  // Trim topics to remove any unexpected whitespace
            $blogPost = generateBlogPostWithAI($topic, $context, $cta);
            $fileName = sanitizeFileName($topic);
            $filePath = saveToWord($blogPost, $fileName);
            echo "<p>Document generated: <a href='{$filePath}'>" . htmlspecialchars($fileName) . "</a></p>";
        }
        fclose($handle);
    }
}


function generateBlogPostWithAI($topic, $context, $cta) {
    global $apiKey;  // Ensure the apiKey is accessible
    $client = new Client(); // Guzzle HTTP client
    $apiUrl = 'https://api.openai.com/v1/chat/completions';

    $data = [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => "Write a detailed and engaging blog post about {$topic}. The blog post should include an introduction that captures the readers interest, a main body with informative and actionable content, and a conclusion that uses the {$cta}. Be sure to incorporate relevant keywords for SEO. Aim for a word count of 800-1,200 words."],
            ['role' => 'user', 'content' => "Topic: {$topic}. Context: {$context}. Call to Action: {$cta}."]
        ]
    ];

    try {
        $response = $client->request('POST', $apiUrl, [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ],
            'json' => $data
        ]);

        $body = $response->getBody();
        $responseArray = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($responseArray['choices'][0]['message'])) {
            echo 'JSON decode error or missing data. Response: ' . $body;
            return 'No content generated or invalid API response structure.';
        }

        // Correctly accessing the content within the 'message' object
        $content = $responseArray['choices'][0]['message']['content'];
        return $content ?? 'No content generated.';
    } catch (Exception $e) {
        echo "API request failed: " . $e->getMessage();
        return "Failed to generate content: " . $e->getMessage();
    }
}

function saveToWord($content, $baseFileName) {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // Define styles and create a new section
    $phpWord->addTitleStyle(1, array('bold' => true, 'size' => 16, 'name' => 'Arial'));
    $phpWord->addTitleStyle(2, array('bold' => true, 'size' => 14, 'name' => 'Arial'));
    $phpWord->addFontStyle('myBodyStyle', array('size' => 12, 'name' => 'Arial'));

    $section = $phpWord->addSection();

    // Handling text and lists
    $paragraphs = explode("\n", $content);
    foreach ($paragraphs as $paragraph) {
        if (preg_match('/^Title:/', $paragraph)) {
            $section->addTitle(trim(substr($paragraph, 6)), 1);
        } else if (preg_match('/^Introduction|^Body|^Conclusion/', $paragraph)) {
            $section->addTitle($paragraph, 2);
        } else {
            $section->addText($paragraph, 'myBodyStyle');
        }
    }

    // Save file
    $fileName = sanitizeFileName($baseFileName) . "_" . time() . ".docx";
    $filePath = "results/" . $fileName . ".docx";
    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($filePath);
    return $filePath; // Return the file path for linking on the results page
}




