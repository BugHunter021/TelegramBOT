<?php
// Initialize a cURL session
$ch = curl_init();

// Set the URL
$ProgID= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // Program ID in intigriti.com . "https://api.intigriti.com/external/researcher/swagger/index.html#/Program/Program_GetProgramOverview"
curl_setopt($ch, CURLOPT_URL, 'https://api.intigriti.com/external/researcher/v1/programs/'.$ProgID);

// Set the request method to GET
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// Set the headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'Authorization: Bearer xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' // API Key .get from "https://app.intigriti.com/researcher/personal-access-tokens"
]);

// Return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Close the cURL session
curl_close($ch);


$data = json_decode($response, true);
$status =$data['status']['value'];
if ($status === 'Open') 
{
    echo $data['name'].' Program Opened'.'<br>';
}

echo $data['name'].' '.$status; // Output: Suspended

// Output the response
//echo $response;
////////////////////////////////      Sent to Telegram Channel       /////////////////////////////////////////////

$token = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';  // Token Bot .get from botfather from telegram
$channel_id = '@xxxxxxxxxx';                         // Channel ID in telegram.Bot should admin this channel
$message = $data['name'].' Program Opened';

$url = "https://api.telegram.org/bot$token/sendMessage";

$data = [
    'chat_id' => $channel_id,
    'text' => $message,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);

if ($status === 'Open') 
{
    $message = 'Program Opened';
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        // Handle error
    }
}

?>
