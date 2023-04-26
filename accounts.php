<?php
include('db.php');
include('api_key.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Create a new account
function createAccount($data) {
    global $conn;

    // Extract the data from the request body
    $api_key = generate_api_key();
    $username = mysqli_real_escape_string($conn,  $data['username']);
    $expires_on = mysqli_real_escape_string($conn, $data['expires_on']);
    $max_orders = mysqli_real_escape_string($conn, $data['max_orders']);
    $status = 'to_be_verified';

    // Prepare the SQL query
    $sql = "INSERT INTO accounts (api_key, username, expires_on, max_orders, status) 
            VALUES ('$api_key', '$username', '$expires_on', '$max_orders', '$status')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account created successfully');
    } else {
        return array('error' => 'Error creating account: ' . mysqli_error($conn));
    }
}

// Retrieve all accounts
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

// Retrieve account by id
function readAccount($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM accounts WHERE id=$id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (mysqli_num_rows($result) > 1) {
        return array('error' => 'Error retrieving account: Multiple accounts with same id');
    } else if (mysqli_num_rows($result) < 1) {
        return array('error' => 'Account not found.');
    }

    // Build the response array
    $accounts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $accounts[] = $row;
    }

    return $accounts[0];
}

// Retrieve account by api key
function readAccountByKey($api_key){
    global $conn;

    // Prepare the SQL query
    $query = "SELECT a.id, a.max_orders, a.status, r.name as role_name FROM accounts a
                LEFT JOIN accounts_roles ar ON ar.account_id = a.id
                LEFT JOIN roles r ON ar.role_id = r.id WHERE api_key = '$api_key'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) != 1) {
        return array('error' => 'Error retrieving account with api key: '. $api_key);
    } else {
        // Build the response array
        $accounts = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $accounts[] = $row;
        }
        return $accounts[0];
    }
}

// Update an existing account
function updateAccount($id, $data) {
    global $conn;

    $response = readAccount($id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error updating account: '.$response['error']);
    }

    // Extract the data from the request body
    $api_key = mysqli_real_escape_string($conn, $data['api_key']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $expires_on = mysqli_real_escape_string($conn, $data['expires_on']);
    $max_orders = mysqli_real_escape_string($conn, $data['max_orders']);
    $status = mysqli_real_escape_string($conn, $data['status']);

    // Prepare the SQL query
    $sql = "UPDATE accounts SET api_key='$api_key', username='$username', expires_on='$expires_on', max_orders='$max_orders', status='$status' 
                WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account updated successfully');
    } else {
        return array('error' => 'Error updating account: ' . mysqli_error($conn));
    }
}


//Update status of an existing account
function updateAccountStatus($id, $data) {
    global $conn;

    $response = readAccount($id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error updating account: '.$response['error']);
    }

    if (isset($data['status'])) {
        $status = mysqli_real_escape_string($conn, $data['status']);

        // Prepare the SQL query
        $sql = "UPDATE accounts SET status='$status' WHERE id=$id";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            return array('message' => 'Account updated successfully');
        } else {
            return array('error' => 'Error updating account: ' . mysqli_error($conn));
        }
    } else {
        return array('error' => 'Error updating account: Status field is missing');
    }
}

// Delete an existing account
function deleteAccount($id) {
    global $conn;

    $response = readAccount($id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error deleting account: '.$response['error']);
    }

    if($response['role_name'] == 'admin'){

    }

    // Prepare the SQL query
    $sql = "DELETE FROM accounts WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Account deleted successfully');
    } else {
        return array('error' => 'Error deleting account: ' . mysqli_error($conn));
    }
}

//API key validation: return the account based on the provided api key
function validateApiKey(){
    if(isset($_GET['api_key'])){
        $apiKey = $_GET['api_key'];
    } else {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(array('error' => 'You need to use an api_key param in order to execute requests.'));
        exit();
    }

    $account = readAccountByKey($apiKey);
    if(array_key_exists('error', $account)){
        header('HTTP/1.1 401 Unauthorized');
        exit();
    }
    return $account;
}

// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if(in_array('accounts.php', $uri)){
    $account = validateApiKey();
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $id = mysqli_real_escape_string($conn, $id);
                if($account['role_name'] == 'admin' || $account['id'] == $id){
                    $response = readAccount($id);
                    echo json_encode($response);
                    break;
                } 
            } else if($account['role_name'] == 'admin') {
                $response = readAccounts();
                echo json_encode($response);
                break;
            } 
            header('HTTP/1.1 403 Forbidden');
            exit();
        case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $response = createAccount($data);
                echo json_encode($response);
            break;
        case 'PUT':
            if($account['role_name'] == 'admin'){      
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $_GET['id'];
                $id = mysqli_real_escape_string($conn, $id);
                $response = updateAccount($id, $data);
                echo json_encode($response);
                break;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit();
            }
        case 'PATCH':
            if($account['role_name'] == 'admin'){    
                $id = $_GET['id'];
                $id = mysqli_real_escape_string($conn, $id);
                $data = json_decode(file_get_contents('php://input'), true);
                $response = updateAccountStatus($id, $data);
                echo json_encode($response);
                break;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit();
            }
        case 'DELETE':
            if($account['role_name'] == 'admin'){
                $id = $_GET['id'];
                $id = mysqli_real_escape_string($conn, $id);
                $response = deleteAccount($id);
                echo json_encode($response);
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit();
            }
            break;
        default:
            header('HTTP/1.1 405 Method Not Allowed');
        }
    }
?>
