<?php
require "partials/header.php";
require "db_connect.php";

// Set a default user location for demonstration. In a real app, this would come from geolocation.
$userLat = -0.5333; // Latitude for Embu, Kenya
$userLon = 37.4500; // Longitude for Embu, Kenya

// Initialize an empty array to hold our collection centers
$centers = [];

// --- Fetch Collection Centers from Database ---
// Select all necessary columns from the 'collection_centers' table
$sql_centers = "SELECT id, name, address, lat, lon, phone, email, rating, reviews, hours, certifications, website FROM collection_centers";
$result_centers = $conn->query($sql_centers);

if ($result_centers->num_rows > 0) {
    // Loop through each row fetched from the database
    while ($row = $result_centers->fetch_assoc()) {
        // Add an empty 'acceptedItems' array to each center to store its accepted waste types
        $row['acceptedItems'] = [];
        // Store centers in an associative array using their 'id' as the key for easy merging later
        $centers[$row['id']] = $row;
    }
}

// --- Fetch Accepted Items from Database and merge with Centers ---
// Select 'center_id' and 'item_type' from the 'accepted_items' table, ordered for consistency
$sql_items = "SELECT center_id, item_type FROM accepted_items ORDER BY center_id, item_type";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    // Loop through each accepted item record
    while ($row_item = $result_items->fetch_assoc()) {
        $centerId = $row_item['center_id'];
        $itemType = $row_item['item_type'];
        // If the center exists in our $centers array, add the item type to its 'acceptedItems'
        if (isset($centers[$centerId])) {
            $centers[$centerId]['acceptedItems'][] = $itemType;
        }
    }
}

// After merging, convert the associative $centers array back into an indexed array for easier iteration
$centers = array_values($centers);

// --- Function to calculate distance between two points using the Haversine formula ---
function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371; // Earth's radius in kilometers
    $dLat = deg2rad($lat2 - $lat1); // Difference in latitudes, converted to radians
    $dLon = deg2rad($lon2 - $lon1); // Difference in longitudes, converted to radians
    $a =
        sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2); // Haversine formula part 1
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); // Haversine formula part 2
    return round($R * $c, 2); // Calculate and round the distance to 2 decimal places
}

// --- Loop through centers and add calculated distance ---
foreach ($centers as &$center) { // Use '&' to modify the original array elements directly
    $centerLat = $center['lat'];
    $centerLon = $center['lon'];
    $distance = calculateDistance($userLat, $userLon, $centerLat, $centerLon);
    $center['distance'] = $distance;
}

// --- Optional: Sort centers by distance (closest first) ---
usort($centers, function ($a, $b) {
    return $a['distance'] <=> $b['distance']; // PHP 7+ spaceship operator for comparison
});

// Close the database connection to free up resources
$conn->close();

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
                                <a href="centers.php"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out"
                                    data-filter="all">All Items</a>
                                <a href="centers.php?category=Phones"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out"
                                    data-filter="phones">Phones</a>
                                <a href="centers.php?category=Laptops"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out"
                                    data-filter="laptops">Laptops</a>
                                <a href="centers.php?category=Batteries"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out"
                                    data-filter="batteries">Batteries</a>
                                <a href="centers.php?category=Appliances"
                                    class="filter-button px-4 py-2 text-sm font-medium border border-gray-300 rounded-full text-gray-700 bg-gray-50 hover:bg-green-100 hover:border-green-500 transition duration-200 ease-in-out"
                                    data-filter="appliances">Appliances</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="collectionCentersList">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Nearby Collection Centers</h2>
                <p id="resultsCount" class="text-gray-600"></p>
            </div>

            <div id="centersResults" class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($centers)): // Check if there are any centers to display 
                ?>
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
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= htmlspecialchars($center['lat']) ?>,<?= htmlspecialchars($center['lon']) ?>"
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
                                        // Explode certifications by comma and trim whitespace
                                        $certifications = array_map('trim', explode(',', $center['certifications']));
                                        foreach ($certifications as $cert):
                                            if (!empty($cert)): // Ensure there's content to display
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
                <?php else: // No centers found 
                ?>
                    <p class="col-span-full text-center text-gray-600">No e-waste collection centers found matching your criteria.</p>
                <?php endif; ?>
            </div>

            <div id="errorMessages" class="text-center mt-8">
                <p id="locationError" class="text-red-500 hidden">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Unable to retrieve your location. Please ensure location services are enabled.
                </p>
                <p id="noCentersFound" class="text-gray-600 hidden">
                    <i class="fas fa-search mr-2"></i>
                    No e-waste collection centers found matching your criteria.
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