<?php
require "partials/header.php";
?>

<main>
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 fade-in-up">About Us</h1>
            <p class="text-xl md:text-2xl opacity-90 fade-in-up delay-200">Our commitment to a sustainable future through responsible e-waste management.</p>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="fade-in-left">
                <img src="assets/images/mission-vision.jpg" alt="People collaborating for a mission" class="rounded-lg shadow-xl">
            </div>
            <div class="fade-in-right">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Our Mission</h2>
                <p class="text-lg text-gray-700 leading-relaxed mb-4">
                    Our mission is to facilitate the responsible disposal and recycling of electronic waste, making it easy and accessible for everyone. We aim to minimize the environmental impact of e-waste by connecting individuals and businesses with certified collection and recycling centers.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    We are dedicated to promoting a circular economy where valuable materials are recovered and reused, reducing the need for new raw materials and preventing harmful substances from contaminating our planet.
                </p>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="md:order-2 fade-in-right">
                <img src="assets/images/future-vision.jpg" alt="Sustainable future with green energy" class="rounded-lg shadow-xl">
            </div>
            <div class="md:order-1 fade-in-left">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Our Vision</h2>
                <p class="text-lg text-gray-700 leading-relaxed">
                    We envision a world free from the hazards of improperly disposed e-waste. A future where electronic waste is seen as a valuable resource, and where communities actively participate in sustainable recycling practices, ensuring a healthier planet for generations to come.
                </p>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12 fade-in-up">Our Core Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-blue-50 rounded-lg p-8 shadow-md hover:shadow-xl transition-shadow duration-300 fade-in-up delay-100">
                    <div class="text-5xl text-blue-600 mb-4">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Environmental Stewardship</h3>
                    <p class="text-gray-700">Committed to protecting our planet by promoting responsible recycling and reducing pollution.</p>
                </div>
                <div class="bg-green-50 rounded-lg p-8 shadow-md hover:shadow-xl transition-shadow duration-300 fade-in-up delay-200">
                    <div class="text-5xl text-green-600 mb-4">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Integrity & Transparency</h3>
                    <p class="text-gray-700">Operating with honesty and openness in all our partnerships and practices.</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-8 shadow-md hover:shadow-xl transition-shadow duration-300 fade-in-up delay-300">
                    <div class="text-5xl text-purple-600 mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Community Empowerment</h3>
                    <p class="text-gray-700">Empowering communities with knowledge and resources for sustainable e-waste management.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 gradient-bg text-white text-center">
        <div class="container mx-auto px-4 fade-in-up">
            <h2 class="text-4xl font-bold mb-6">Join Us in Our Mission</h2>
            <p class="text-xl mb-8">Every responsible disposal makes a difference. Find a center near you today!</p>
            <a href="centers.php" class="inline-block bg-white text-gray-800 hover:bg-gray-100 font-bold py-4 px-8 rounded-full text-xl shadow-lg transition duration-300 ease-in-out pulse-animation">
                <i class="fas fa-map-marker-alt mr-2"></i> Find a Center
            </a>
        </div>
    </section>

</main>

<?php
require "partials/footer.php";
?>