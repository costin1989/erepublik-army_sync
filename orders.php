<?php
include('db.php');

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

    // Prepare the SQL query
    $sql = "INSERT INTO orders (battle_id, fight_for, wall, candies) VALUES ('$battle_id', '$fight_for', '$wall', '$candies')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order created successfully');
    } else {
        return array('error' => 'Error creating order: ' . mysqli_error($conn));
    }
}

// Retrieve all orders
function readOrders() {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM orders";

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

// Retrieve order by id
function readOrder($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM orders WHERE id=$id";

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

    // Prepare the SQL query
    $sql = "UPDATE orders SET battle_id='$battle_id', fight_for='$fight_for', wall='$wall', candies='$candies' WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order updated successfully');
    } else {
        return array('error' => 'Error updating order: ' . mysqli_error($conn));
    }
}

// Delete an existing order
function deleteOrder($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "DELETE FROM orders WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        return array('message' => 'Order deleted successfully');
    } else {
        return array('error' => 'Error deleting order: ' . mysqli_error($conn));
    }
}

// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $id = mysqli_real_escape_string($conn, $id);
            $response = readOrder($id);
            echo json_encode($response);
        } else {
             $response = readOrders();
            echo json_encode($response);
        }
       
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $response = createOrder($data);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'];
        $id = mysqli_real_escape_string($conn, $id);
        $response = updateOrder($id, $data);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $id = mysqli_real_escape_string($conn, $id);
        $response = deleteOrder($id);
    }
?>
