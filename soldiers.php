<?php
include('db.php');
include('cors.php');

// Register a new player
function createSoldier($data) {
    global $conn;

    // Extract the data from the request body
    $username = mysqli_real_escape_string($conn, $data['username']);
    $link = mysqli_real_escape_string($conn,  $data['link']);
    $ff = mysqli_real_escape_string($conn, $data['ff']);
    $candies = mysqli_real_escape_string($conn, $data['candies']);
    $boosters_100 = mysqli_real_escape_string($conn, $data["boosters_100"]);
    $boosters_50 = mysqli_real_escape_string($conn, $data["boosters_50"]);
    $energy_bar = mysqli_real_escape_string($conn, $data["energy_bar"]);
    $ground_rank = mysqli_real_escape_string($conn, $data["ground_rank"]);
    $air_rank = mysqli_real_escape_string($conn, $data["air_rank"]);
    $strength = mysqli_real_escape_string($conn, $data["strength"]);

    // Prepare the SQL query
    $sql = "INSERT INTO soldiers (username, link, ff, candies, boosters_100, boosters_50, energy_bar, ground_rank, air_rank, strength) 
            VALUES ('$username', '$link', $ff, $candies, $boosters_100, $boosters_50, $energy_bar, $ground_rank, $air_rank, $strength)";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        header('HTTP/1.1 201 Created');
        return array('message' => 'Soldier created successfully');
    } else {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error creating soldier: ' . mysqli_error($conn));
    }
}

// Retrieve all soldiers
function readSoldiers() {
    global $conn;
    
    // Prepare the SQL query
    $sql = "SELECT * FROM soldiers";

    // Execute the query
    $result1 = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result1) {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error retrieving soldiers: ' . mysqli_error($conn));
    }

    // Build the response array
    $soldiers = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $soldiers[] = $row;
    }

    return $soldiers;
}

// Retrieve soldier by id
function readSoldier($id) {
    global $conn;

    // Prepare the SQL query
    $sql = "SELECT * FROM soldiers WHERE id=$id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error retrieving soldier: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 1) {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error retrieving soldier: Multiple soldiers with same id');
    } else if (mysqli_num_rows($result) < 1) {
        header('HTTP/1.1 404 Not Found');
        return array('error' => 'Soldier not found.');
    }

    // Build the response array
    $soldiers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $soldiers[] = $row;
    }

    return $soldiers[0];
}

// Update an existing soldier
function updateSoldier($id, $data) {
    global $conn;

    $response = readSoldier($id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error updating order: '.$response['error']);
    }

    // Extract the data from the request body
    $username = mysqli_real_escape_string($conn, $data['username']);
    $link = mysqli_real_escape_string($conn,  $data['link']);
    $ff = mysqli_real_escape_string($conn, $data['ff']);
    $candies = mysqli_real_escape_string($conn, $data['candies']);
    $boosters_100 = mysqli_real_escape_string($conn, $data["boosters_100"]);
    $boosters_50 = mysqli_real_escape_string($conn, $data["boosters_50"]);
    $energy_bar = mysqli_real_escape_string($conn, $data["energy_bar"]);
    $ground_rank = mysqli_real_escape_string($conn, $data["ground_rank"]);
    $air_rank = mysqli_real_escape_string($conn, $data["air_rank"]);
    $strength = mysqli_real_escape_string($conn, $data["strength"]);

    // Prepare the SQL query
    $sql = "UPDATE soldiers SET username='$username', link='$link', ff=$ff, candies=$candies, 
            boosters_100=$boosters_100, boosters_50=$boosters_50, energy_bar=$energy_bar, 
            ground_rank=$ground_rank, air_rank=$air_rank, strength=$strength";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        header('HTTP/1.1 200 OK');
        return array('message' => 'Soldier updated successfully');
    } else {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error updating soldier: ' . mysqli_error($conn));
    }
}

// Delete an existing soldier
function deleteSoldier($id) {
    global $conn;

    $response = readSoldier($id);
    if(array_key_exists('error', $response)){
       return array('error' => 'Error deleting soldier: '.$response['error']);
    }

    // Prepare the SQL query
    $sql = "DELETE FROM soldiers WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        header('HTTP/1.1 200 OK');
        return array('message' => 'Soldier deleted successfully');
    } else {
        header('HTTP/1.1 400 Bad Request');
        return array('error' => 'Error deleting soldier: ' . mysqli_error($conn));
    }
}


// Handle HTTP requests
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
if(in_array('soldiers.php', $uri)){
    //$account = validateApiKey();
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $id = mysqli_real_escape_string($conn, $id);
                $response = readSoldier($id);
            } else {
                 $response = readSoldiers();
            }
            echo json_encode($response);
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $response = createSoldier($data);
            echo json_encode($response);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $response = updateSoldier($id, $data);
            echo json_encode($response);
            break;
        case 'DELETE':
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($conn, $id);
            $response = deleteSoldier($id);
            echo json_encode($response);
            break;
        default:
            header('HTTP/1.1 405 Method Not Allowed');
        }
    }
?>
