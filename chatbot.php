<?php
// Set response header to JSON
header('Content-Type: application/json');

// Load predefined responses from the JSON file
$responses = json_decode(file_get_contents('responses.json'), true);

// Function to save updated responses to the JSON file
function save_responses($responses) {
    file_put_contents('responses.json', json_encode($responses, JSON_PRETTY_PRINT));
}

// Check if a keyword and reply are sent via GET parameters
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['keyword']) && isset($_GET['reply'])) {
        $keyword = strtolower(trim($_GET['keyword'])); // Get the keyword from the query parameter
        $reply = trim($_GET['reply']); // Get the reply from the query parameter

        // Add the new keyword and reply to the responses array
        $responses[$keyword] = $reply;

        // Save the updated responses back to the JSON file
        save_responses($responses);

        // Return a success message
        echo json_encode([
            'status' => 'success',
            'message' => 'New keyword and reply added successfully.'
        ]);
    } elseif (isset($_GET['message'])) {
        // This is the regular chatbot response flow
        $message = strtolower(trim($_GET['message'])); // Get the message from the query parameter
        $responseText = "Sorry, I don't understand that."; // Default response

        // Check if any predefined response exists for the message
        foreach ($responses as $keyword => $response) {
            if (strpos($message, $keyword) !== false) {
                $responseText = $response;
                break;
            }
        }

        // Return the response as a JSON object
        echo json_encode([
            'status' => 'success',
            'response' => $responseText
        ]);
    } else {
        // If no message or keyword/reply is sent, return an error message
        echo json_encode([
            'status' => 'error',
            'message' => 'No message or keyword/reply provided.'
        ]);
    }
} else {
    // Return an error message if the request method is not GET
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use GET.'
    ]);
}
