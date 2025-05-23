<?php
require "partials/header.php";
require "db_connect.php";

// Set a default user location for demonstration. In a real app, this would come from geolocation.
$userLat = -0.5333; // Latitude for Embu, Kenya
$userLon = 37.4500; // Longitude for Embu, Kenya

// Initialize variables for filtering
$searchTerm = $_GET['search'] ?? ''; // Get search term from URL, default to empty string
$filterCategory = $_GET['category'] ?? ''; // Get category from URL, default to empty string

// Initialize an empty array to hold our collection centers
$centers = [];

// --- Prepare SQL for Collection Centers ---
// We'll join with 'accepted_items' if a category filter is applied
$sql_centers = "SELECT cc.id, cc.name, cc.address, cc.lat, cc.lon, cc.phone, cc.email, cc.rating, cc.reviews, cc.hours, cc.certifications, cc.website 
                FROM collection_centers cc";

// If a category filter is provided, join with accepted_items and add a WHERE clause
if (!empty($filterCategory)) {
    $sql_centers .= " INNER JOIN accepted_items ai ON cc.id = ai.center_id";
}

// Add WHERE clause for search term (name or address)
$conditions = [];
if (!empty($searchTerm)) {
    // Use prepared statements for search term to prevent SQL injection
    $conditions[] = "(cc.name LIKE ? OR cc.address LIKE ?)";
}

// Add WHERE clause for category filter
if (!empty($filterCategory)) {
    // Use prepared statements for category to prevent SQL injection
    $conditions[] = "ai.item_type = ?";
}

// Combine all conditions with AND
if (!empty($conditions)) {
    $sql_centers .= " WHERE " . implode(" AND ", $conditions);
}

// Add GROUP BY to avoid duplicate centers when joining with accepted_items
if (!empty($filterCategory)) {
    $sql_centers .= " GROUP BY cc.id";
}

// --- Execute SQL for Collection Centers ---
$stmt_centers = $conn->prepare($sql_centers);

// Bind parameters if conditions exist
$paramTypes = '';
$params = [];

if (!empty($searchTerm)) {
    $paramTypes .= 'ss';
    $likeSearchTerm = '%' . $searchTerm . '%';
    $params[] = $likeSearchTerm;
    $params[] = $likeSearchTerm;
}
if (!empty($filterCategory)) {
    $paramTypes .= 's';
    $params[] = $filterCategory;
}

if (!empty($params)) {
    $stmt_centers->bind_param($paramTypes, ...$params);
}

$stmt_centers->execute();
$result_centers = $stmt_centers->get_result();

if ($result_centers->num_rows > 0) {
    while ($row = $result_centers->fetch_assoc()) {
        $row['acceptedItems'] = []; // Initialize for accepted items
        $centers[$row['id']] = $row;
    }
}
$stmt_centers->close();


// --- Fetch Accepted Items from Database and merge with Centers ---
// We still fetch all accepted items for the displayed centers to list them,
// regardless of the filter applied, so we can show all items a center accepts.
// This is done separately to correctly populate the 'acceptedItems' array for each center.
$sql_items = "SELECT center_id, item_type FROM accepted_items ORDER BY center_id, item_type";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    while ($row_item = $result_items->fetch_assoc()) {
        $centerId = $row_item['center_id'];
        $itemType = $row_item['item_type'];
        if (isset($centers[$centerId])) { // Only add if the center was already selected by the main query
            $centers[$centerId]['acceptedItems'][] = $itemType;
        }
    }
}

$centers = array_values($centers); // Convert associative array to indexed array

// --- Function to calculate distance (Haversine formula) ---
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
    return round($R * $c, 2); // Round to 2 decimal places
}

// --- Loop through centers and add distance ---
foreach ($centers as &$center) {
    $centerLat = $center['lat'];
    $centerLon = $center['lon'];
    $distance = calculateDistance($userLat, $userLon, $centerLat, $centerLon);
    $center['distance'] = $distance;
}

// Optional: Sort centers by distance (closest first)
usort($centers, function ($a, $b) {
    return $a['distance'] <=> $b['distance'];
});

$conn->close(); // Close database connection
?>

<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div id="searchFilterSection" class="fade-in-up">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8 transform transition-all duration-300 hover:shadow-xl">
                    <form action="centers.php" method="GET" class="space-y-6">
                        <div class="flex flex-col md:flex-row gap-4 items-center">
                            <div class="flex-1 relative">
                                <input type="text" id="searchInput" name="search" placeholder="Search by center name or location..."
                                    value="<?= htmlspecialchars($searchTerm) ?>"
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 ease-in-out placeholder-gray-400 text-gray-700">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div>
                                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-white transition duration-200 ease-in-out flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-search"></i> Search
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-base font-semibold text-gray-700 mb-3">Filter by accepted items:</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="centers.php?search=<?= htmlspecialchars($searchTerm) ?>"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out
                                    <?= empty($filterCategory) ? 'bg-green-600 text-white border-green-600 hover:bg-green-700' : '' ?>"
                                    data-filter="all">All Items</a>

                                <?php
                                $categories = ['Phones', 'Laptops', 'Batteries', 'Appliances']; // Define your categories
                                foreach ($categories as $cat):
                                ?>
                                <a href="centers.php?category=<?= urlencode($cat) ?>&search=<?= htmlspecialchars($searchTerm) ?>"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out
                                    <?= ($filterCategory === $cat) ? 'bg-green-600 text-white border-green-600 hover:bg-green-700' : '' ?>"
                                    data-filter="<?= strtolower($cat) ?>"><?= htmlspecialchars($cat) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="collectionCentersList">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Nearby Collection Centers</h2>
                <p id="resultsCount" class="text-gray-600">Showing <?= count($centers) ?> results</p>
            </div>

            <div id="centersResults" class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($centers)): ?>
                    <?php foreach ($centers as $center): ?>
                        <div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in-up">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($center['name']) ?></h3>
                                <div class="flex items-center text-sm">
                                    <div class="rating-stars mr-1">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <i class="fas fa-star <?= $i < round($center['rating']) ? 'text-yellow-500' : 'text-gray-300' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-gray-600">(<?= htmlspecialchars($center['reviews']) ?>)</span>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                                    <span class="text-sm"><?= htmlspecialchars($center['address']) ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-route mr-2 text-blue-600"></i>
                                    <span class="text-sm font-semibold"><?= htmlspecialchars($center['distance']) ?> km away</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-clock mr-2 text-purple-600"></i>
                                    <span class="text-sm"><?= htmlspecialchars($center['hours']) ?></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Accepts:</p>
                                <div class="accepted-items">
                                    <?php foreach ($center['acceptedItems'] as $item): ?>
                                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs mr-2"><?= htmlspecialchars($item) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="http://maps.google.com/maps?q=<?= htmlspecialchars($center['lat']) ?>,<?= htmlspecialchars($center['lon']) ?>"
                                    target="_blank"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition duration-200">
                                    <i class="fas fa-directions mr-1"></i>Get Directions
                                </a>
                                <a href="tel:<?= htmlspecialchars($center['phone']) ?>"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition duration-200">
                                    <i class="fas fa-phone mr-1"></i>Call Now
                                </a>
                            </div>

                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <div>
                                        <?php
                                        $certifications = array_map('trim', explode(',', $center['certifications']));
                                        foreach ($certifications as $cert):
                                            if (!empty($cert)):
                                        ?>
                                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs mr-2"><?= htmlspecialchars($cert) ?></span>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                    <?php if (!empty($center['website'])): ?>
                                        <a href="http://<?= htmlspecialchars($center['website']) ?>" target="_blank" class="text-blue-600 hover:underline">
                                            <i class="fas fa-external-link-alt"></i> Website
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-gray-600">No e-waste collection centers found matching your criteria.</p>
                <?php endif; ?>
            </div>

            <div id="errorMessages" class="text-center mt-8">
                <p id="locationError" class="text-red-500 hidden">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Unable to retrieve your location. Please ensure location services are enabled.
                </p>
                </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-12">Our Impact</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg p-8 shadow-md">
                <div class="text-4xl text-green-600 mb-4">
                    <i class="fas fa-recycle"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">50,000+</h3>
                <p class="text-gray-600">Devices Recycled</p>
            </div>
            <div class="bg-white rounded-lg p-8 shadow-md">
                <div class="text-4xl text-blue-600 mb-4">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">200+</h3>
                <p class="text-gray-600">Collection Centers</p>
            </div>
            <div class="bg-white rounded-lg p-8 shadow-md">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">25,000+</h3>
                <p class="text-gray-600">Happy Users</p>
            </div>
        </div>
    </div>
</section>

<?php require "partials/footer.php"; ?>