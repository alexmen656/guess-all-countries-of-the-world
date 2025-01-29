<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Handle GET request to fetch leaderboard
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid'])) {
        // Fetch count for a specific user
        $uuid = $_GET['uuid'];
        $sql = "SELECT count FROM leaderboard WHERE uuid = '$uuid'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(["count" => $row['count']]);
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    } else {
        // Fetch leaderboard
        $sql = "SELECT name, count FROM leaderboard ORDER BY count DESC";
        $result = $conn->query($sql);

        $leaderboard = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $leaderboard[] = $row;
            }
        }
        echo json_encode($leaderboard);
    }
}

// Handle POST request to register a new user or update count
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'];

    if ($action === 'register') {
        $name = $data['name'];
        $count = $data['count'] ?? 0;
        $uuid = uniqid();

        // Validate username length
        /*if (strlen($name) > 10) {
            echo json_encode(["error" => "Username must be 10 characters or less"]);
            http_response_code(400);
            exit();
        }*/

        $sql = "INSERT INTO leaderboard (uuid, name, count) VALUES ('$uuid', '$name', $count)";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "uuid" => $uuid]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } elseif ($action === 'update') {
        $uuid = $data['uuid'];
        $newCount = $data['count'];

        // Fetch the current score
        $sql = "SELECT count FROM leaderboard WHERE uuid = '$uuid'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentCount = $row['count'];

            // Update the score only if the new score is better
            if ($newCount > $currentCount) {
                $sql = "UPDATE leaderboard SET count = $newCount WHERE uuid = '$uuid'";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "error" => $conn->error]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "New score is not better than the current score"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "User not found"]);
        }
    }
}

$conn->close();
?>