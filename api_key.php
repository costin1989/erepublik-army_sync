<?php
function generate_api_key() {
    // Generate a random string of 32 characters
    $apiKey = bin2hex(random_bytes(16));

    // Return the API key
    return $apiKey;
}

?>
