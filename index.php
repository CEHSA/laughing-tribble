<?php
// index.php

// Simple router
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to a 'home' page or some other default

if ($page === 'client_dashboard') {
    // If the page is 'client_dashboard', then we need to include the main client dashboard template.
    // The client_dashboard.php template will itself handle the 'view' parameter.
    $client_dashboard_template = 'templates/client_dashboard.php';
    if (file_exists($client_dashboard_template)) {
        include $client_dashboard_template;
    } else {
        echo "<p>Error: Client dashboard template not found.</p>";
        // Optionally, redirect to a 404 page or show a generic error.
    }
} else {
    // For other pages (or a default home page)
    // For now, let's just show a message or include a default home page template if one exists.
    echo "<h1>Welcome to ArchiAxis</h1>";
    echo "<p>This is the main entry point. Content for '<strong>" . htmlspecialchars($page) . "</strong>' would go here.</p>";
    echo '<p><a href="index.php?page=client_dashboard&view=dashboard">Go to Client Dashboard</a></p>';
    echo '<p><a href="index.php?page=freelancer_dashboard&view=dashboard">Go to Freelancer Dashboard</a></p>';
    // Example: include 'templates/home.php';

} elseif ($page === 'freelancer_dashboard') {
    // If the page is 'freelancer_dashboard', include the main freelancer dashboard template.
    // The freelancer_dashboard.php template will itself handle the 'view' parameter.
    $freelancer_dashboard_template = 'templates/freelancer_dashboard.php';
    if (file_exists($freelancer_dashboard_template)) {
        include $freelancer_dashboard_template;
    } else {
        echo "<p>Error: Freelancer dashboard template not found.</p>";
    }
}

?>
