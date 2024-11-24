<?php

$original_conn = isset($conn) ? $conn : null;

// Only include connection if it hasn't been included yet
if (!isset($conn)) {
    require_once('../server/connection.php');
}

// Initialize admin information
$admin_name = 'Admin';
$admin_email = '';

// Check if session variable is set and fetch admin info from the database
if (isset($_SESSION['admin_email']) && !empty($_SESSION['admin_email'])) {
    $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email FROM admins WHERE admin_email = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $_SESSION['admin_email']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch admin details
        if ($row = $result->fetch_assoc()) {
            $admin_id = $row['admin_id'];
            $admin_name = $row['admin_name'];
            $admin_email = $row['admin_email'];
        }

        $stmt->close();
    }
} else {
    // If session email is not found, redirect to login page
    header('location: login.php?error=Invalid session');
    exit;
}
?>

<div class="header">
    <h1>Dashboard</h1>
    <div class="user-info">
        <span>Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>
