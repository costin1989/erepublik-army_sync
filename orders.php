<?php
include('db.php');
include('accounts.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Create a new order
function createOrder($data) {
    global $conn;

    // Extract the data from the request body
    $battle_id = mysqli_real_escape_string($conn, $data['battle_id']);
    $fight_for = mysqli_real_escape_string($conn,  $data['fight_for']);
    $wall = mysqli_real_escape_string($conn, $data['wall']);
    $candies = mysqli_real_escape_string($conn, $data['candies']);
    $added_by = $data["added_by"];

    // Prepare the SQL query
    $sql = "INSERT INTO orders (battle_id, fight_for, wall, candies, added_by) VALUES ('$battle_id', '$fight_for', '$wall', '$candies', '$added_by')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order created successfully');
    } else {
        return array('error' => 'Error creating order: ' . mysqli_error($conn));
    }
}

// Retrieve all orders
function readOrders($account_id) {
    global $conn;
    // Prepare the SQL query
    $sql = "SELECT * FROM orders WHERE added_by=$account_id";

    // Execute the query
    $result1 = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result1) {
        return array('error' => 'Error retrieving orders: ' . mysqli_error($conn));
    }

    // Build the response array
    $orders = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $orders[] = $row;
    }

    return $orders;
}

// Retrieve order by id
function readOrder($id, $account_id) {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM orders WHERE id=$id AND added_by=$account_id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        return array('error' => 'Error retrieving order: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 1) {
        return array('error' => 'Error retrieving order: Multiple orders with same id');
    } else if (mysqli_num_rows($result) < 1) {
        return array('error' => 'Order not found.');
    }

    // Build the response array
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders[0];
}

// Update an existing order
function updateOrder($id, $data) {
    global $conn;

    $account_id = mysqli_real_escape_string($conn,$data['added_by']);
    $response = readOrder($id, $account_id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error updating order: '.$response['error']);
    }

    // Extract the data from the request body
    $battle_id = mysqli_real_escape_string($conn, $data['battle_id']);
    $fight_for = mysqli_real_escape_string($conn, $data['fight_for']);
    $wall = mysqli_real_escape_string($conn, $data['wall']);
    $candies = mysqli_real_escape_string($conn, $data['candies']);
    

    // Prepare the SQL query
    $sql = "UPDATE orders SET battle_id='$battle_id', fight_for='$fight_for', wall='$wall', candies='$candies', added_by = '$account_id' 
                WHERE id=$id AND added_by=$account_id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order updated successfully');
    } else {
        return array('error' => 'Error updating order: ' . mysqli_error($conn));
    }
}

// Delete an existing order
function deleteOrder($id, $account_id) {
    global $conn;

    $response = readOrder($id, $account_id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error deleting order: '.$response['error']);
    }

    // Prepare the SQL query
    $sql = "DELETE FROM orders WHERE id=$id AND added_by = $account_id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order deleted successfully');
    } else {
        return array('error' => 'Error deleting order: ' . mysqli_error($conn));
    }
}


// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if(in_array('orders.php', $uri)){
    //API key validation
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
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $id = mysqli_real_escape_string($conn, $id);
                $response = readOrder($id, $account['id']);
            } else {
                 $response = readOrders($account['id']);
            }
            echo json_encode($response);
            break;
        case 'POST':
            if($account['status'] != 'active'){
                echo json_encode(array('error' => 'Your account is not active anymore. Please contact the administrator.'));
                break;
            }
            $orders =  readOrders($account['id']);
            if(count($orders) >= $account['max_orders']){
                 echo json_encode(array('error' => 'You hit the max limit for active orders.'));
                 break;
            } 
            $data = json_decode(file_get_contents('php://input'), true);
            $data['added_by'] = $account['id'];
            $response = createOrder($data);
            echo json_encode($response);
            break;
        case 'PUT':
            if($account['status'] != 'active'){
                echo json_encode(array('error' => 'Your account is not active anymore. Please contact the administrator.'));
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $data['added_by'] = $account['id'];
            $response = updateOrder($id, $data);
            echo json_encode($response);
            break;
        case 'DELETE':
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $response = deleteOrder($id, $account['id']);
            echo json_encode($response);
        }
    }
?>
