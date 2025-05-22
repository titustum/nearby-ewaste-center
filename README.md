# E-Waste Connect

---

## Project Overview

E-Waste Connect is a simple web application designed to help users find nearby e-waste collection centers easily and responsibly dispose of their electronic waste. This project demonstrates responsive web design using **Tailwind CSS** and leverages the browser's **Geolocation API** to provide location-based services.

## Features

* **Responsive Navbar:** A clean, modern navigation bar that adapts to different screen sizes.
* **Mobile Side Navigation:** A smooth-sliding horizontal menu for mobile devices, enhancing usability on smaller screens.
* **E-Waste Collection Center Finder:**
    * Utilizes the user's current location (with permission) to find nearby centers.
    * Displays a list of collection centers with their addresses and calculated distances (in kilometers) from the user.
    * Provides a "Get Directions" link for each center, opening Google Maps.
* **Clear User Feedback:** Includes loading states and error messages for a better user experience.

## Technologies Used

* **HTML5:** For the basic structure of the web page.
* **Tailwind CSS:** A utility-first CSS framework for rapid UI development and responsive design.
* **JavaScript:** For dynamic functionalities such as:
    * Handling mobile menu interactions (open/close).
    * Accessing the Geolocation API to get the user's coordinates.
    * Calculating distances to collection centers.
    * Dynamically rendering collection center information.

## Setup and Usage

To run this project locally, follow these simple steps:

1.  **Save the Code:** Copy the entire HTML code into a file named `index.html`.
2.  **Open in Browser:** Open the `index.html` file using any modern web browser (e.g., Chrome, Firefox, Edge).

### Interacting with the Application:

* **Navbar:**
    * On desktop or larger screens, the navigation links (`Home`, `About Us`, etc.) will be visible directly in the header.
    * On mobile or smaller screens, a **hamburger icon** (☰) will appear. Click it to reveal the side navigation menu.
* **Mobile Menu:**
    * Click the hamburger icon to slide the menu in from the left.
    * Click the "X" icon or anywhere outside the menu to slide it back out.
* **Finding Collection Centers:**
    * Click the **"Get Nearest E-Waste Collection Points"** button.
    * Your browser will likely ask for **permission to access your location**. You must allow this for the feature to work.
    * The application will then display a list of mock e-waste collection centers (since this is a demo) along with their estimated distances from your location.
    * Click "Get Directions" on any listed center to open Google Maps with the center's location.

## Future Enhancements (Ideas for Development)

* **Real Data Integration:** Replace mock data with actual e-waste collection center data using a service like **Google Places API**, or a custom backend database.
* **Search and Filter:** Add functionality to search for centers by city, address, or type of e-waste accepted.
* **User Submissions:** Allow users to suggest new collection centers.
* **Map Integration:** Embed an interactive map (e.g., Google Maps, Leaflet) directly on the page to visualize center locations.
* **Accessibility Improvements:** Enhance keyboard navigation and screen reader support.

## Contributing

This is a simple demo, but feel free to fork the repository, make improvements, and submit pull requests if you'd like to contribute to its functionality or design!