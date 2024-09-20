<?php

error_reporting(1);



echo 'Active V 0.9<br>';
$ch = curl_init();

////// Telegram Setting 
$token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$channel_id = '@IntigritiMonitor';

////// SQL Setting 
$servername = "localhost";
$username = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

////// Intigriti API key
$Auth_Bearer = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

///// Other variable
date_default_timezone_set('Asia/Tehran');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//////////////////// Get all updates message from intigriti with API
curl_setopt($ch, CURLOPT_URL, 'https://api.intigriti.com/external/researcher/v1/programs/activities');

// Set the request method to GET
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// Set the headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'Authorization: Bearer '.$Auth_Bearer
]);

// Return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);
//echo $response;
// Close the cURL session
curl_close($ch);
$Ch_N=0;
$data = json_decode($response, true);
foreach ($data['records'] as $record) {
    $Ch_N= $Ch_N +1;
    $programId = $record['programId'];
    echo $programId . "////".$Ch_N."////";
    $value_prog = $record['type']['value'];
    switch ($value_prog) {
        case "New program status available":
            echo $value_prog. " -----> "; 
            $to_status = $record['activity']['toStatus']['value'];
            echo $to_status . "----->"; 
            //echo $record['createdAt']. "<br>";
                $time_change = date("Y-m-d H:i:s", $record['createdAt']);
                echo $time_change. "<br>"; 
            
            // Check Exist 
            $stmt = $conn->prepare("SELECT * FROM intigriti WHERE program_ID = ? AND time = ?");
            $stmt->bind_param("ss", $programId, $time_change);
            
            // Execute the statement
            $stmt->execute();
            
            // Get the result
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "Exist";
            } else {
                echo "Not";
                echo "*********".GetNameprg($programId). "*********". "<br>";
                
                // SQL query to insert data
                $sql = "INSERT INTO intigriti (program_ID, title, status, time) VALUES ('$programId', '$value_prog','$to_status', '$time_change')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully". "<br>";
                    if ($to_status == 'Open' )
                    {
                        $StatusEmoji='✅';
                    }
                    else
                    {
                        $StatusEmoji='⛔️';
                    }
                    $MessageToTelegram = '🎯*Program Name: *'.GetNameprg($programId).chr(10).
                    '⚠️*Type Change: *'.$value_prog.chr(10).
                    $StatusEmoji.'*Status: *'.$to_status.chr(10).
                    '⏰*Change Time: * '.$time_change;
                    //$MessageToTelegram = urlencode($MessageToTelegram);
                    echo "*********".SentTelegram($MessageToTelegram);

                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error. "<br>";
                }
            }
          break;

        case "New domains version added":
            echo $value_prog. " -----> "; 
            //echo $record['createdAt']. "<br>";
                $time_change = date("Y-m-d H:i:s", $record['createdAt']);
                echo $time_change. "<br>"; 
            
            // Check Exist 
            $stmt = $conn->prepare("SELECT * FROM intigriti WHERE program_ID = ? AND time = ?");
            $stmt->bind_param("ss", $programId, $time_change);
            
            // Execute the statement
            $stmt->execute();
            
            // Get the result
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "Exist";
            } else {
                echo "Not";
                echo "*********".GetNameprg($programId). "*********". "<br>";
                
                // SQL query to insert data
                $sql = "INSERT INTO intigriti (program_ID, title, status, time) VALUES ('$programId', '$value_prog','$to_status', '$time_change')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully". "<br>";
                    $MessageToTelegram = '🎯*Program Name: *'.GetNameprg($programId).chr(10).
                    '⚠️*Type Change: *'.$value_prog.chr(10).
                    '⏰*Change Time: * '.$time_change;
                    //$MessageToTelegram = urlencode($MessageToTelegram);
                    echo "*********".SentTelegram($MessageToTelegram);

                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error. "<br>";
                }
            }
        break;

        default:
            echo $value_prog. " -----> "; 
            //echo $record['createdAt']. "<br>";
                $time_change = date("Y-m-d H:i:s", $record['createdAt']);
                echo $time_change. "<br>"; 
            
            // Check Exist 
            $stmt = $conn->prepare("SELECT * FROM intigriti WHERE program_ID = ? AND time = ?");
            $stmt->bind_param("ss", $programId, $time_change);
            
            // Execute the statement
            $stmt->execute();
            
            // Get the result
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "Exist";
            } else {
                echo "Not";
                echo "*********".GetNameprg($programId). "*********". "<br>";
                
                // SQL query to insert data
                $sql = "INSERT INTO intigriti (program_ID, title, status, time) VALUES ('$programId', '$value_prog','$to_status', '$time_change')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully". "<br>";
                    $MessageToTelegram = '🎯*Program Name: *'.GetNameprg($programId).chr(10).
                    '⚠️*Type Change: *'.$value_prog.chr(10).
                    '⏰*Change Time: * '.$time_change;
                    //$MessageToTelegram = urlencode($MessageToTelegram);
                    echo "*********".SentTelegram($MessageToTelegram);

                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error. "<br>";
                }
            }
        break;

        }

   
}

function GetNameprg($prog_id) {
    $ch = curl_init();
    global $token ;
    global $channel_id ;
    global $Auth_Bearer ;


    curl_setopt($ch, CURLOPT_URL, 'https://api.intigriti.com/external/researcher/v1/programs/'.$prog_id);

    // Set the request method to GET
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // Set the headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'Authorization: Bearer '.$Auth_Bearer
    ]);

    // Return the response instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);


    $data = json_decode($response, true);
return $data['name'];
}

///////////////////////////////////Send Message to Telegram Channel///////////////////////////////////

function SentTelegram($message) {
    
    global $token ;
    global $channel_id ;
    $message = $message.chr(10).chr(10).'@IntigritiMonitor';
    
        $url = "https://api.telegram.org/bot$token/sendMessage";

        $data = [
            'chat_id' => $channel_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);


        return $result;
}



function UpdateBioChanell() {
    
    global $token ;
    global $channel_id ;
    
    //////////////////////////////// Change BIO Channel ////////////////////////////////////////
    date_default_timezone_set('Asia/Tehran');
    $current_time = date('Y-m-d H:i:s');
    
    $new_bio = 'Monitor all program update on Intigriti.com'.chr(10).
    'for Hunters'.chr(10).
    '.:. Happy Hunting .:.'.chr(10).
    'Last Check (TimeZone:Iran) : '.$current_time;
    
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
        echo 'Update BIO Channel error';
    }
    return $result;
}








$foo = UpdateBioChanell();

$stmt->close();
$conn->close();
?>
