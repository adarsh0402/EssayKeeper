<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $essayText = $_POST['essayInput'];
    $essayTitle = $_POST['titleInput'];
    $wordCount = str_word_count($essayText);

    if ($wordCount < 350) {
        echo json_encode([
            'wordCount' => $wordCount,
            'alert' => [
                'type' => 'warning',
                'message' => 'The essay must be at least 350 words.'
            ]
        ]);
        exit;
    } elseif ($wordCount > 400) {
        echo json_encode([
            'wordCount' => $wordCount,
            'alert' => [
                'type' => 'danger',
                'message' => 'Your essay is ' . $wordCount . ' words long. Please stick within the limit of 400 words.'
            ]
        ]);
        exit;
    }

    $api_key = 'AIzaSyA47AfKmrE4o7yrisS-TcoizBZ1cjczeQA';
    $api_url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=' . $api_key;

    // Prepare the data to send to the API
    $data = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => "You're an expert essay evaluator with over 15 years of experience in assessing writing quality for competitive examinations and you are an expert in English. You have a keen eye for grammatical accuracy, clarity of expression, and overall coherence of thought. Your goal is to provide constructive feedback to candidates preparing for the SEBI Grade A Descriptive English Exam.
                        Your task is to analyze an essay provided to you. Here are the details about the essay I want you to evaluate: 
                        - Essay Title: " . $essayTitle . "
                        - Essay Text: " . $essayText . " 
                        - Any specific focus areas (if applicable):  
                        Be very detailed.
                        Your responses should be concise, clear, and focused on providing grammatical assistance. Follow the provided guidelines exactly and strictly stick to the output format.

                        Output:
                        1. Grammatical Analysis 
                        2. Overall Clarity
                        3. Redundancy
                        4. Total Marks out of 30 (10 marks each for above 3 analysis)"
                    ]
                ]
            ]
        ]
    ];

    // Initialize cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the API response
    $result = json_decode($response, true);

    // Check if the API response contains the expected content
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
        echo json_encode([
            'wordCount' => $wordCount,
            'analysis' => nl2br(htmlspecialchars($analysisText))
        ]);
    } else {
        echo json_encode([
            'wordCount' => $wordCount,
            'alert' => [
                'type' => 'danger',
                'message' => 'Sorry, something went wrong with the analysis. Please try again.'
            ]
        ]);
    }
}
?>
