<?php
/****************************************************
get_twitter.php - Obtain Twitter feed JSON for a user
****************************************************/

// Application-Only Authentication key and secret.
$consumer_key = 'twitter_consumer_key';
$consumer_secret = 'twitter_consumer_secret';

$request = array(
    'screen_name' => 'twitterapi',
    'count'       => '20'
);


//====================================================================================================//

function generate_bearer_token($key, $secret) {
    $curlopts = array(
        CURLOPT_URL             => "https://api.twitter.com/oauth2/token",
        CURLOPT_POST            => 1,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_POSTFIELDS      => "grant_type=client_credentials",
        CURLOPT_HEADER          => 1,
        CURLOPT_HTTPHEADER      => array(
                                        "POST /oauth2/token HTTP/1.1", 
                                        "Host: api.twitter.com", 
                                        "User-Agent: my Twitter App v.1",
                                        "Authorization: Basic ".base64_encode(urlencode($key) . ":" . urlencode($secret))."",
                                        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
                                        "Content-Length: 29"
                                   )
    );
    $auth = curl_init();
    curl_setopt_array($auth, $curlopts);
    $bearer_token = json_decode(preg_replace('/HTTP(.*)block/s',"",curl_exec($auth)));
    curl_close($auth);
    return $bearer_token->access_token;
}

$curlopts = array( 
    CURLOPT_HTTPHEADER => array("Authorization: Bearer ".generate_bearer_token($consumer_key, $consumer_secret).""),
    CURLOPT_URL => "https://api.twitter.com/1.1/statuses/user_timeline.json?" . http_build_query($request),
    CURLOPT_SSL_VERIFYPEER => false
);
$feed = curl_init();
curl_setopt_array($feed, $curlopts);
curl_exec($feed);
curl_close($feed);

?>
