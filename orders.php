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
        return array('error' => 'Error retrieving orders: ' . mysqli_error($conn));
    }

    // Build the response array
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

// Update an existing order
function updateOrder($id, $data) {
    global $conn;

    // Extract the data from the request body
    $battle_id = mysqli_real_escape_string($conn, $data['battle_id']);
    $fight_for = mysqli_real_escape_string($conn, $data['fight_for']);
    $wall = mysqli_real_escape_string($conn, $data['wall']);
    $candies = mysqli_real_escape_string($conn, $data['candies']);
    $added_by = $data["added_by"];

    // Prepare the SQL query
    $sql = "UPDATE orders SET battle_id='$battle_id', fight_for='$fight_for', wall='$wall', candies='$candies', $added_by = '$added_by' 
            WHERE id=$id AND added_by = '$added_by'";

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

    // Prepare the SQL query
    $sql = "DELETE FROM orders WHERE id=$id AND added_by = $account_id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order deleted successfully');
    } else {
        return array('error' => 'Error deleting order: ' . mysqli_error($conn));
    }
}


//API key validation
$apiKey = isset($_GET['api_key']) ? $_GET['api_key'] : '';
$account = validate_account($apiKey);
// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if(in_array('orders.php', $uri)){
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
            $data = json_decode(file_get_contents('php://input'), true);
            $data['added_by'] = $account['id'];
            $response = createOrder($data);
            echo json_encode($response);
            break;
        case 'PUT':
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
