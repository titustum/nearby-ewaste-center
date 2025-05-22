<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ewaste_connect';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get request parameters
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
$radius = isset($_GET['radius']) ? floatval($_GET['radius']) : 10; // Default 10km radius
$item_type = isset($_GET['item_type']) ? $conn->real_escape_string($_GET['item_type']) : null;

// Base query to get centers with their accepted items
$query = "SELECT 
            c.id, 
            c.name, 
            c.address, 
            c.lat, 
            c.lon, 
            c.phone, 
            c.email, 
            c.rating, 
            c.reviews, 
            c.hours, 
            c.certifications, 
            c.website,
            GROUP_CONCAT(a.item_type) as accepted_items
          FROM collection_centers c
          LEFT JOIN accepted_items a ON c.id = a.center_id";

// Add filtering by item type if specified
if ($item_type) {
    $query .= " WHERE a.item_type = '$item_type'";
}

$query .= " GROUP BY c.id";

$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    die(json_encode(['error' => 'Query failed: ' . $conn->error]));
}

$centers = [];
while ($row = $result->fetch_assoc()) {
    $center = [
        'id' => $row['id'],
        'name' => $row['name'],
        'address' => $row['address'],
        'lat' => floatval($row['lat']),
        'lon' => floatval($row['lon']),
        'phone' => $row['phone'],
        'email' => $row['email'],
        'rating' => floatval($row['rating']),
        'reviews' => intval($row['reviews']),
        'hours' => $row['hours'],
        'certifications' => $row['certifications'],
        'website' => $row['website'],
        'acceptedItems' => explode(',', $row['accepted_items'])
    ];

    // Calculate distance if coordinates provided
    if ($lat && $lon) {
        $center['distance'] = calculateDistance($lat, $lon, $center['lat'], $center['lon']);
    }

    $centers[] = $center;
}

// Filter by radius if coordinates provided
if ($lat && $lon) {
    $centers = array_filter($centers, function ($center) use ($radius) {
        return isset($center['distance']) && $center['distance'] <= $radius;
    });

    // Sort by distance
    usort($centers, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });
}

// Return JSON response
echo json_encode(array_values($centers));

$conn->close();

/**
 * Calculate distance between two points in kilometers
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371; // Radius of Earth in kilometers
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a =
        sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($R * $c, 2);
}
