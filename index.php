<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEBI Descriptive English Grader</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design for Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css" rel="stylesheet">
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -60px;
            margin-top: -60px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-container {
            position: relative;
            height: 100px;
            text-align: center;
            margin-top: 20px;
        }

        #analysisSection {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        #analysisText {
            font-family: 'Courier New', Courier, monospace;
            white-space: pre-wrap;
            margin-top: 20px;
        }

        footer {
            margin-top: 50px;
            padding: 20px 0;
            text-align: center;
            background-color: #343a40;
            color: #fff;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Essay Writer</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label for="essay-title">Essay Title:</label>
            <input type="text" id="essay-title" class="form-control" name="essayTitle" placeholder="Enter title">
        </div>
        <div class="form-group">
            <label for="essayInput">Enter your essay below:</label>
            <textarea class="form-control" id="essayInput" name="essayInput" rows="10" placeholder="Type your essay here..."></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Analyze Essay</button>
        </div>
    </form>

    <div class="form-group">
        <h5>Total Words: <span id="wordCount">
            <?php 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $essayText = $_POST['essayInput'];
                echo str_word_count($essayText);
            } else {
                echo '0';
            }
            ?>
        </span></h5>
    </div>

    <div id="alertContainer" class="mb-3">
        <?php 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $wordCount = str_word_count($essayText);
            if ($wordCount < 1) {
                echo '<div class="alert alert-warning" role="alert">The essay must be at least 100 words.</div>';
            } elseif ($wordCount > 400) {
                echo '<div class="alert alert-danger" role="alert">Your essay is ' . $wordCount . ' words long. Please stick within the limit of 400 words.</div>';
            }
        }
        ?>
    </div>

    <?php 
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $wordCount >= 1 && $wordCount <= 400) {
        $essayTitle = $_POST['essayTitle'];
        $api_key = 'AIzaSyA47AfKmrE4o7yrisS-TcoizBZ1cjczeQA';
        $api_url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=AIzaSyA47AfKmrE4o7yrisS-TcoizBZ1cjczeQA';

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
                            3. Marks out of 40"
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

        // Display the loader and analysis section
        echo '<div id="loaderContainer" class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';

        echo '<div id="analysisSection" class="mt-4">';
        echo '<h4>Essay Analysis</h4>';
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
            echo '<div id="analysisText">' . nl2br(htmlspecialchars($analysisText)) . '</div>';
        } else {
            echo '<p>Sorry, something went wrong with the analysis. Please try again.</p>';
        }
        echo '</div>';
    }
    ?>
</div>

<footer>
    <p>&copy; 2024 Adarsh</p>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
