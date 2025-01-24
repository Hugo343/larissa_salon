<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

if (!isset($_GET['service_id'])) {
    header('Location: index.php');
    exit();
}

$serviceId = $_GET['service_id'];

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$serviceId]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];

    $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $serviceId, $appointmentDate, $appointmentTime]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Larissa Salon Studio</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <!-- Navigation content -->
    </nav>

    <section class="booking">
        <h1>Book Appointment</h1>
        <div class="booking-form">
            <h2><?php echo htmlspecialchars($service['name']); ?></h2>
            <p><?php echo htmlspecialchars($service['description']); ?></p>
            <p><strong>Price: Rp <?php echo number_format($service['price'], 0, ',', '.'); ?></strong></p>
            <p><i class="fas fa-clock"></i> Duration: <?php echo $service['duration']; ?> minutes</p>

            <form action="" method="post">
                <div class="form-group">
                    <label for="appointment_date">Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Time:</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>
                </div>
                <button type="submit" class="btn btn-primary">Book Now</button>
            </form>
        </div>
    </section>

    <footer>
        <!-- Footer content -->
    </footer>
</body>
</html>

