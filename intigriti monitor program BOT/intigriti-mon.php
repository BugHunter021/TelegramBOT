<?php
// Initialize a cURL session
$ch = curl_init();

// Set the URL
$ProgID= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // Program ID in intigriti.com . "https://api.intigriti.com/external/researcher/swagger/index.html#/Program/Program_GetProgramOverview"
$token = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';  // Token Bot .get from botfather from telegram
$channel_id = '@xxxxxxxxxx';                         // Channel ID in telegram.Bot should admin this channel

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

//////////////////////////////// Change BIO Channel ////////////////////////////////////////
date_default_timezone_set('Asia/Tehran');
$current_time = date('Y-m-d H:i:s');
//echo "Current time in Tehran: " . $current_time;

$new_bio = 'Last check : '.$current_time;

$url2 = "https://api.telegram.org/bot$token/setChatDescription";

$data2 = [
    'chat_id' => $channel_id,
    'description' => $new_bio
];

$options2 = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data2),
    ],
];

$context2  = stream_context_create($options2);
$result2 = file_get_contents($url2, false, $context2);

if ($result2 === FALSE) { 
    // Handle error
}





///////////////////////////////////Send Messageto Channel///////////////////////////////////


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
