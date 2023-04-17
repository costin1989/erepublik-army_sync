<?php
include('db.php');
include('api_key.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Create a new order
function createAccount($data) {
    global $conn;

    // Extract the data from the request body
    $api_key = generate_api_key();
    $username = mysqli_real_escape_string($conn,  $data['username']);
    $expires_on = mysqli_real_escape_string($conn, $data['expires_on']);

    // Prepare the SQL query
    $sql = "INSERT INTO accounts (api_key, username, expires_on) VALUES ('$api_key', '$username', '$expires_on')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account created successfully');
    } else {
        return array('error' => 'Error creating account: ' . mysqli_error($conn));
    }
}

// Retrieve all orders
function readAccounts() {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM accounts";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        return array('error' => 'Error retrieving accounts: ' . mysqli_error($conn));
    }

    // Build the response array
    $accounts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $accounts[] = $row;
    }

    return $accounts;
}

// Retrieve order by id
function readAccount($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM accounts WHERE id=$id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        return array('error' => 'Error retrieving accounts: ' . mysqli_error($conn));
    }

    // Build the response array
    $accounts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $accounts[] = $row;
    }

    return $accounts;
}

function validate_account($api_key){
    global $conn;

    // Prepare the SQL query
    $query = "SELECT id FROM accounts WHERE api_key = '$api_key'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) != 1) {
        header('HTTP/1.1 401 Unauthorized');
        exit();
    } else {
        // Build the response array
        $accounts = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $accounts[] = $row;
        }
        return $accounts[0];
    }
}

// Update an existing order
function updateAccount($id, $data) {
    global $conn;

    // Extract the data from the request body
    $api_key = mysqli_real_escape_string($conn, $data['api_key']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $expires_on = mysqli_real_escape_string($conn, $data['expires_on']);

    // Prepare the SQL query
    $sql = "UPDATE accounts SET api_key='$api_key', username='$username', expires_on='$expires_on' WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account updated successfully');
    } else {
        return array('error' => 'Error updating account: ' . mysqli_error($conn));
    }
}

// Delete an existing order
function deleteAccount($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "DELETE FROM accounts WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account deleted successfully');
    } else {
        return array('error' => 'Error deleting account: ' . mysqli_error($conn));
    }
}

// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if(in_array('accounts.php', $uri)){
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $id = mysqli_real_escape_string($conn, $id);
                $response = readAccount($id);
            } else {
                $response = readAccounts();
            }
            echo json_encode($response);
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $response = createAccount($data);
            echo json_encode($response);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $response = updateAccount($id, $data);
            echo json_encode($response);
            break;
        case 'DELETE':
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $response = deleteAccount($id);
            echo json_encode($response);
        }
    }
?>
