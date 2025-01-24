<?php
require_once 'config.php';

if (isset($_GET['service_id'])) {
    $serviceId = $_GET['service_id'];
    
    $stmt = $pdo->prepare("SELECT s.*, c.name as category_name FROM services s JOIN categories c ON s.category_id = c.id WHERE s.id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        $output = '<div class="service-details fade-in">';
        $output .= '<h2>' . htmlspecialchars($service['name']) . '</h2>';
        $output .= '<p>' . htmlspecialchars($service['description']) . '</p>';
        $output .= '<p class="price">Price: Rp ' . number_format($service['price'], 0, ',', '.') . '</p>';
        $output .= '<p class="duration"><i class="fas fa-clock"></i> Duration: ' . $service['duration'] . ' minutes</p>';
        $output .= '<p>Category: ' . htmlspecialchars($service['category_name']) . '</p>';
        $output .= '<a href="book.php?service_id=' . $service['id'] . '" class="btn">Book Now</a>';
        $output .= '</div>';

        echo $output;
    } else {
        echo '<p>Service not found.</p>';
    }
}

