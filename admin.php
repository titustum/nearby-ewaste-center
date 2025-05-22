<?php
// admin.php - Simple admin interface (add proper authentication in production)
session_start();

// Database connection using MySQLi
$conn = new mysqli("localhost", "root", "", "ewaste_connect");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_center'])) {
        // Prepare data for insertion
        $name = $conn->real_escape_string($_POST['name']);
        $address = $conn->real_escape_string($_POST['address']);
        $lat = floatval($_POST['lat']);
        $lon = floatval($_POST['lon']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $rating = floatval($_POST['rating']);
        $reviews = intval($_POST['reviews']);
        $hours = $conn->real_escape_string($_POST['hours']);
        $certifications = isset($_POST['certifications']) ? $conn->real_escape_string($_POST['certifications']) : '';
        $website = isset($_POST['website']) ? $conn->real_escape_string($_POST['website']) : '';

        // Add new collection center
        $sql = "INSERT INTO collection_centers 
                (name, address, lat, lon, phone, email, rating, reviews, hours, certifications, website) 
                VALUES ('$name', '$address', $lat, $lon, '$phone', '$email', $rating, $reviews, '$hours', '$certifications', '$website')";

        if ($conn->query($sql) === TRUE) {
            $centerId = $conn->insert_id;

            // Add accepted items
            if (isset($_POST['accepted_items'])) {
                foreach ($_POST['accepted_items'] as $item) {
                    $item = $conn->real_escape_string($item);
                    $sql = "INSERT INTO accepted_items (center_id, item_type) VALUES ($centerId, '$item')";
                    $conn->query($sql);
                }
            }

            $_SESSION['message'] = "Center added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    header("Location: admin.php");
    exit();
}

// Get all centers for listing
$centers = [];
$result = $conn->query("SELECT * FROM collection_centers");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $centers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>E-Waste Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">E-Waste Connect Admin Panel</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['message'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Add New Center Form -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Add New Collection Center</h2>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Name</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Address</label>
                        <textarea name="address" class="w-full px-3 py-2 border rounded" required></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Latitude</label>
                            <input type="number" step="any" name="lat" class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Longitude</label>
                            <input type="number" step="any" name="lon" class="w-full px-3 py-2 border rounded" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Phone</label>
                            <input type="text" name="phone" class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Rating (1-5)</label>
                            <input type="number" min="1" max="5" step="0.1" name="rating" class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Reviews Count</label>
                            <input type="number" name="reviews" class="w-full px-3 py-2 border rounded" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Operating Hours</label>
                        <input type="text" name="hours" class="w-full px-3 py-2 border rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Certifications (comma separated)</label>
                        <input type="text" name="certifications" class="w-full px-3 py-2 border rounded">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Website URL</label>
                        <input type="url" name="website" class="w-full px-3 py-2 border rounded">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Accepted Items</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="phones" class="mr-2">
                                Phones
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="laptops" class="mr-2">
                                Laptops
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="tablets" class="mr-2">
                                Tablets
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="batteries" class="mr-2">
                                Batteries
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="cables" class="mr-2">
                                Cables
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="accepted_items[]" value="appliances" class="mr-2">
                                Appliances
                            </label>
                        </div>
                    </div>

                    <button type="submit" name="add_center" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Add Center
                    </button>
                </form>
            </div>

            <!-- Existing Centers List -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Existing Collection Centers</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Name</th>
                                <th class="py-2 px-4 border-b">Location</th>
                                <th class="py-2 px-4 border-b">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($centers as $center): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($center['name']) ?></td>
                                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($center['address']) ?></td>
                                    <td class="py-2 px-4 border-b"><?= $center['rating'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
// Close connection
$conn->close();
?>