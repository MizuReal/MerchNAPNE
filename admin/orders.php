<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php?error=Please login first');
    exit;
}

include('../server/connection.php');

// Handle bulk actions
if (isset($_POST['bulk_action']) && isset($_POST['order_ids'])) {
    $valid_actions = ['delivered', 'in_transit', 'canceled'];
    $bulk_action = $_POST['bulk_action'];
    $order_ids = $_POST['order_ids'];
    
    if (in_array($bulk_action, $valid_actions) && is_array($order_ids)) {
        $ids = array_map('intval', $order_ids);
        $ids_string = implode(',', $ids);
        
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id IN ($ids_string)");
        $stmt->bind_param("s", $bulk_action);
        
        if ($stmt->execute()) {
            header('location: orders.php?success=Bulk action completed successfully');
            exit;
        }
        $stmt->close();
    }
    header('location: orders.php?error=Invalid bulk action');
    exit;
}

// Handle individual order status updates
if (isset($_GET['action'], $_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $action = $_GET['action'];

    $new_status = $action === 'deliver' ? 'delivered' : 
                 ($action === 'transit' ? 'in_transit' : 
                 ($action === 'cancel' ? 'canceled' : null));

    if ($new_status) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $stmt->close();
            header('location: orders.php?success=Order status updated successfully');
            exit;
        }
        $stmt->close();
    }
    header('location: orders.php?error=Invalid action or order ID');
    exit;
}

// Get filter parameters
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'order_date';
$sort_direction = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Validate sort parameters
$allowed_sort_fields = ['order_date', 'order_cost', 'order_status'];
$sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'order_date';
$sort_direction = $sort_direction === 'ASC' ? 'ASC' : 'DESC';

// Build query with date filter
$query = "SELECT order_id, order_cost, order_status, user_id, user_phone, user_city, user_address, order_date 
          FROM orders WHERE 1=1";

if ($start_date && $end_date) {
    $query .= " AND order_date BETWEEN ? AND ?";
}

$query .= " ORDER BY $sort_by $sort_direction";

$stmt = $conn->prepare($query);

if ($start_date && $end_date) {
    $stmt->bind_param("ss", $start_date, $end_date);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .orders-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .date-filter {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .date-filter input[type="text"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 150px;
        }

        .filter-btn {
            background-color: #000000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filter-btn:hover {
            background-color: #F29A30;
        }

        .bulk-actions {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .bulk-actions select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }

        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
        }

        .orders-table th {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            text-align: left;
        }

        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .orders-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: opacity 0.3s;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .delivered-btn { background-color: #28a745; color: white; }
        .transit-btn { background-color: #ffc107; color: black; }
        .cancel-btn { background-color: #dc3545; color: white; }

        .status-circle.delivered { background-color: #28a745; }
        .status-circle.in_transit { background-color: #ffc107; }
        .status-circle.canceled { background-color: #dc3545; }

        .sort-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-cell {
            width: 30px;
            text-align: center;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* change this if you dont want the cell to expand */
        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            position: relative; /* Needed for absolute positioning of hover effect */
        }

        .orders-table td.address-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px; /* Adjust the width as needed */
        }

        .orders-table td.address-cell:hover {
            white-space: normal;
            max-width: none;
            background-color: #f8f9fa; /* Optional: Add background on hover */
            z-index: 10; /* Ensure it's above other content */
        }

        /* Tooltip for showing full address when hovering */
        .orders-table td.address-cell:hover::after {
            content: attr(data-full-address);
            position: absolute;
            top: 0;
            left: 100%;
            padding: 5px;
            background-color: rgba(0, 0, 0, 0.75);
            color: white;
            border-radius: 4px;
            white-space: normal;
            z-index: 9999;
            max-width: 300px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <?php include('sidemenu.php'); ?>

        <div class="main-content">
            <?php include('header.php')?>
            <div class="orders-section">
                <div class="orders-header">
                    <h2>Manage Orders</h2>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="get" class="date-filter">
                    <div>
                        <label for="start_date">Start Date:</label>
                        <input type="text" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="date-picker">
                    </div>
                    <div>
                        <label for="end_date">End Date:</label>
                        <input type="text" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="date-picker">
                    </div>
                    <button type="submit" class="filter-btn">Apply Filter</button>
                </form>

                <form method="post" id="bulk-action-form">
                    <div class="bulk-actions">
                        <select name="bulk_action" id="bulk-action-select">
                            <option value="">Select Bulk Action</option>
                            <option value="delivered">Mark as Delivered</option>
                            <option value="in_transit">Mark as In Transit</option>
                            <option value="canceled">Mark as Canceled</option>
                        </select>
                        <button type="submit" class="btn filter-btn">Apply to Selected</button>
                    </div>

                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>ID</th>
                                <th>
                                    <a href="?sort=order_cost&direction=<?php echo $sort_by === 'order_cost' && $sort_direction === 'ASC' ? 'DESC' : 'ASC'; ?>" class="sort-link">
                                        Cost
                                        <?php if ($sort_by === 'order_cost'): ?>
                                            <i class="fas fa-sort-<?php echo $sort_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=order_status&direction=<?php echo $sort_by === 'order_status' && $sort_direction === 'ASC' ? 'DESC' : 'ASC'; ?>" class="sort-link">
                                        Status
                                        <?php if ($sort_by === 'order_status'): ?>
                                            <i class="fas fa-sort-<?php echo $sort_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <!-- <th>User ID</th> -->
                                <th>Phone</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>
                                    <a href="?sort=order_date&direction=<?php echo $sort_by === 'order_date' && $sort_direction === 'ASC' ? 'DESC' : 'ASC'; ?>" class="sort-link">
                                        Order Date
                                        <?php if ($sort_by === 'order_date'): ?>
                                            <i class="fas fa-sort-<?php echo $sort_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="checkbox-cell">
                                        <input type="checkbox" name="order_ids[]" value="<?php echo $order['order_id']; ?>" class="order-checkbox">
                                    </td>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_cost']); ?></td>
                                    <td>
                                        <span class="status-circle <?php echo htmlspecialchars($order['order_status']); ?>"></span>
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </td>
                                    <!-- <td><?php echo htmlspecialchars($order['user_id']); ?></td> -->
                                    <td><?php echo htmlspecialchars($order['user_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_city']); ?></td>
                                    <!-- change this if you dont want the cell to expand -->
                                    <td class="address-cell" data-full-address="<?php echo htmlspecialchars($order['user_address']); ?>">
                                        <?php echo htmlspecialchars(substr($order['user_address'], 0, 30)); ?> <!-- Shorten the address for display -->
                                    </td>
                                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                    <td>
                                        <?php if ($order['order_status'] !== 'delivered' && $order['order_status'] !== 'canceled'): ?>
                                            <a href="orders.php?action=deliver&order_id=<?php echo $order['order_id']; ?>" class="btn delivered-btn">D</a>
                                            <a href="orders.php?action=transit&order_id=<?php echo $order['order_id']; ?>" class="btn transit-btn">T</a>
                                            <a href="orders.php?action=cancel&order_id=<?php echo $order['order_id']; ?>" class="btn cancel-btn">C</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        // Initialize date pickers
        flatpickr(".date-picker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Handle select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.getElementsByClassName('order-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        // Validate bulk action form submission
        document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
            const action = document.getElementById('bulk-action-select').value;
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');

            if (!action) {
                e.preventDefault();
                alert('Please select a bulk action');
                return;
            }

            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one order');
                return;
            }

            if (!confirm('Are you sure you want to perform this action on the selected orders?')) {
                e.preventDefault();
            }
        });

        // Confirm individual order actions
        document.querySelectorAll('.btn').forEach(button => {
            if (button.classList.contains('delivered-btn') || 
                button.classList.contains('transit-btn') || 
                button.classList.contains('cancel-btn')) {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to change the status of this order?')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>