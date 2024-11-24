<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php?error=Please login first');
    exit;
}

require_once('../server/connection.php');

// Handle user actions first
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $action = $_GET['action'];
    
    if ($action == 'deactivate') {
        $stmt = $conn->prepare("UPDATE users SET user_status = 0 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
    } elseif ($action == 'reactivate') {
        $stmt = $conn->prepare("UPDATE users SET user_status = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
    }

    if ($stmt) {
        $stmt->execute();
        $stmt->close();
        header('location: users.php');
        exit;
    }
}

// Store the query result in a variable BEFORE including header
$query = "SELECT user_id, user_name, user_email, user_status FROM users";
$users_result = $conn->query($query);

if (!$users_result) {
    die("Query failed: " . $conn->error);
}

// Store users in an array to prevent result set from being closed
$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}
$users_result->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <?php include('sidemenu.php'); ?>
        
        <div class="main-content">
            <?php include('header.php'); ?>
            
            <div class="users-section">
                <h2>Manage Users</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td>
                                    <span class="status-circle <?php echo $row['user_status'] == 1 ? 'active' : 'inactive'; ?>"></span>    
                                    <?php echo $row['user_status'] == 1 ? 'Active' : 'Inactive'; ?>
                                </td>
                                <td>
                                    <?php if ($row['user_status'] == 1) : ?>
                                        <a href="users.php?action=deactivate&user_id=<?php echo $row['user_id']; ?>" class="btn deactivate-btn">Deactivate</a>
                                    <?php else : ?>
                                        <a href="users.php?action=reactivate&user_id=<?php echo $row['user_id']; ?>" class="btn reactivate-btn">Reactivate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>