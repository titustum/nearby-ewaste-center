<?php
require "partials/header.php";
require "db_connect.php";

// In a real application, you'd get this from geolocation APIs or user input.
$userLat = -0.5333; // Latitude for Embu
$userLon = 37.4500; // Longitude for Embu

// --- Fetch Collection Centers from Database ---
$centers = [];
$sql_centers = "SELECT id, name, address, lat, lon, phone, email, rating, reviews, hours, certifications, website FROM collection_centers";
$result_centers = $conn->query($sql_centers);

if ($result_centers->num_rows > 0) {
    while ($row = $result_centers->fetch_assoc()) {
        // Initialize 'acceptedItems' as an empty array for each center
        $row['acceptedItems'] = [];
        $centers[$row['id']] = $row; // Store by ID for easier merging with accepted items
    }
}

// --- Fetch Accepted Items from Database and merge with Centers ---
$sql_items = "SELECT center_id, item_type FROM accepted_items ORDER BY center_id, item_type";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    while ($row_item = $result_items->fetch_assoc()) {
        $centerId = $row_item['center_id'];
        $itemType = $row_item['item_type'];
        // Add item type to the correct center's acceptedItems array
        if (isset($centers[$centerId])) {
            $centers[$centerId]['acceptedItems'][] = $itemType;
        }
    }
}

// Convert associative array back to indexed array if preferred for iteration
$centers = array_values($centers);

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
foreach ($centers as &$center) { // Use & to modify the original array elements
    $centerLat = $center['lat'];
    $centerLon = $center['lon'];
    $distance = calculateDistance($userLat, $userLon, $centerLat, $centerLon);
    $center['distance'] = $distance;
}

// Optional: Sort centers by distance (closest first)
usort($centers, function ($a, $b) {
    return $a['distance'] <=> $b['distance'];
});

// Close database connection
$conn->close();

?>


<!-- Hero Section -->
<section id="home" class="gradient-bg text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-4xl mx-auto fade-in-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">Dispose of E-Waste Responsibly</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">Find certified e-waste collection centers near you and help protect our environment</p>

            <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-12">
                <div class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-shield-alt text-green-300"></i>
                    <span>Certified Centers</span>
                </div>
                <div class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-leaf text-green-300"></i>
                    <span>Eco-Friendly</span>
                </div>
                <div class="flex items-center space-x-2 text-lg">
                    <i class="fas fa-clock text-green-300"></i>
                    <span>Reliable Hours</span>
                </div>
            </div>

            <a href="#searchFilterSection" class="inline-block bg-white text-gray-800 hover:bg-gray-100 font-bold py-4 px-8 rounded-full text-xl shadow-lg transition duration-300 ease-in-out pulse-animation">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Find Collection Centers Near Me
            </a>
        </div>
    </div>
</section>



<!-- Search and Filter Section -->
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

        <!-- Results Section -->
        <div id="collectionCentersList">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Nearby Collection Centers</h2>
                <p id="resultsCount" class="text-gray-600"></p>
            </div>

            <div id="centersResults" class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">


                <?php foreach ($centers as $center): ?>

                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in-up">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-gray-800"><?= $center['name'] ?></h3>
                            <div class="flex items-center text-sm">
                                <div class="rating-stars mr-1">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?= $i < round($center['rating']) ? 'text-yellow-500' : 'text-gray-300' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-gray-600">(<?= $center['reviews'] ?>)</span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                                <span class="text-sm"><?= $center['address'] ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-route mr-2 text-blue-600"></i>
                                <span class="text-sm font-semibold"><?= $center['distance'] ?> km away</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clock mr-2 text-purple-600"></i>
                                <span class="text-sm"><?= $center['hours'] ?></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Accepts:</p>
                            <div class="accepted-items">
                                <?php foreach ($center['acceptedItems'] as $item): ?>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs mr-2"><?= $item ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $center['lat'] ?>,<?= $center['lon'] ?>}"
                                target="_blank"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition duration-200">
                                <i class="fas fa-directions mr-1"></i>Get Directions
                            </a>
                            <a href="tel:<?= $center['phone'] ?>"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition duration-200">
                                <i class="fas fa-phone mr-1"></i>Call Now
                            </a>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>

                                    <!-- loop certifications -->
                                    <?php foreach (explode(',', $center['certifications']) as $item): ?>
                                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs mr-2"><?= $item ?></span>
                                    <?php endforeach ?>

                                    <a href="http://<?php echo $center['website'] ?>" target="_blank" class="text-blue-600 hover:underline">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                            </div>
                        </div>
                    </div>


                <?php endforeach ?>


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



<!-- Statistics Section -->
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