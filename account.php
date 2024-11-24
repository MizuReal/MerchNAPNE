<?php include('layouts/header.php');?>

<?php
session_start();
include('server/connection.php');

// If user is NOT logged in, redirect to login page
if(!isset($_SESSION['logged_in'])){
    header('location: login.php');
    exit;
}

// Handle logout
if(isset($_GET['logout'])){
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    header('location: login.php');
    exit;
}

// Handle profile image upload
if(isset($_FILES["profile_image"]["name"])){
    $user_id = $_SESSION['user_id'];
    
    $imageName = $_FILES["profile_image"]["name"];
    $imageSize = $_FILES["profile_image"]["size"];
    $tmpName = $_FILES["profile_image"]["tmp_name"];

    // Image validation
    $validImageExtension = ['jpg', 'jpeg', 'png'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    
    if (!in_array($imageExtension, $validImageExtension)){
        header('location: account.php?error=Invalid image format. Please use JPG, JPEG or PNG');
        exit;
    }
    elseif ($imageSize > 1200000){
        header('location: account.php?error=Image size is too large (max 1.2MB)');
        exit;
    }
    else {
        // Generate safe filename
        $newImageName = "profile_" . $user_id . "_" . date("Y-m-d-H-i-s") . "." . $imageExtension;
        $uploadPath = 'assets/imgs/profiles/' . $newImageName;
        
        // Create directory if it doesn't exist
        if (!file_exists('assets/imgs/profiles/')) {
            mkdir('assets/imgs/profiles/', 0755, true);
        }
        
        // Use prepared statement for update
        $stmt = $conn->prepare("UPDATE users SET user_profile = ? WHERE user_id = ?");
        $stmt->bind_param("si", $newImageName, $user_id);
        
        if($stmt->execute()){
            if(move_uploaded_file($tmpName, $uploadPath)){
                chmod($uploadPath, 0644);
                header('location: account.php?message=Profile image updated successfully');
                exit;
            } else {
                header('location: account.php?error=Failed to move uploaded file');
                exit;
            }
        } else {
            header('location: account.php?error=Database update failed');
            exit;
        }
        $stmt->close();
    }
}

// Handle password change
if(isset($_POST['change_password'])){
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $user_email = $_SESSION['user_email'];

    if($password !== $confirmPassword){
        header('location: account.php?error=Passwords do not match');
        exit;
    } else if(strlen($password) < 6){
        header('location: account.php?error=Password must be at least 6 characters');
        exit;
    } else {
        $hashed_password = md5($password);
        
        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $stmt->bind_param('ss', $hashed_password, $user_email);
        
        if($stmt->execute()){
            header('location: account.php?message=Password updated successfully');
            exit;
        } else {
            header('location: account.php?error=Could not update password');
            exit;
        }
    }
}

if(isset($_POST['update_name'])) {
    $user_id = $_SESSION['user_id'];
    $new_name = $_POST['name'];
    
    // Basic validation
    if(empty($new_name)) {
        header('location: account.php?error=Name cannot be empty');
        exit;
    }

    // Update user name
    $stmt = $conn->prepare("UPDATE users SET user_name = ? WHERE user_id = ?");
    $stmt->bind_param('si', $new_name, $user_id);
    
    if($stmt->execute()) {
        // Update session variable
        $_SESSION['user_name'] = $new_name;
        header('location: account.php?message=Name updated successfully');
        exit;
    } else {
        header('location: account.php?error=Could not update name');
        exit;
    }
    $stmt->close();
}

if(isset($_POST['update_email'])) {
    $user_id = $_SESSION['user_id'];
    $new_email = $_POST['email'];
    $current_email = $_SESSION['user_email'];
    
    // Basic validation
    if(empty($new_email)) {
        header('location: account.php?error=Email cannot be empty');
        exit;
    }

    // Email validation
    if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        header('location: account.php?error=Invalid email format');
        exit;
    }

    // Check if new email already exists (only if email is being changed)
    if($new_email !== $current_email) {
        $stmt = $conn->prepare("SELECT count(*) FROM users WHERE user_email = ? AND user_id != ?");
        $stmt->bind_param('si', $new_email, $user_id);
        $stmt->execute();
        $stmt->bind_result($num_rows);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();

        if($num_rows > 0) {
            header('location: account.php?error=Email already exists');
            exit;
        }
    }

    // Update user email
    $stmt = $conn->prepare("UPDATE users SET user_email = ? WHERE user_id = ?");
    $stmt->bind_param('si', $new_email, $user_id);
    
    if($stmt->execute()) {
        // Update session variable
        $_SESSION['user_email'] = $new_email;
        header('location: account.php?message=Email updated successfully');
        exit;
    } else {
        header('location: account.php?error=Could not update email');
        exit;
    }
    $stmt->close();
}


// Fetch user's profile image
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_profile FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_image = $user['user_profile'] ?? 'default-profile.png';

// Fetch user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>


<!--THIS IS FOR FETCHING THE FORMS SINCE THIS PAGE HAS OVER 400 LINES OF CODES-->
<?php include('account_forms.php');?>


<!-- For Image Upload -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_image');
    const profileForm = document.getElementById('profile-form');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, JPEG, or PNG)');
                    return;
                }
                
                // Validate file size (1.2MB = 1200000 bytes)
                if (file.size > 1200000) {
                    alert('Image size must be less than 1.2MB');
                    return;
                }
                
                // If validation passes, submit the form
                profileForm.submit();
            }
        });
    }
})

// Replace the existing form validation JavaScript with this updated version
document.addEventListener('DOMContentLoaded', function() {
    // Add form validation for the name form
    const nameForm = document.getElementById('name-form');
    if (nameForm) {
        nameForm.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('account-name');
            
            if (nameInput.value.trim() === '') {
                alert('Name cannot be empty');
                e.preventDefault();
                return;
            }
        });
    }
    
    // Add form validation for the email form
    const emailForm = document.getElementById('email-form');
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('account-email');
            
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                alert('Please enter a valid email address');
                e.preventDefault();
                return;
            }
        });
    }
});

</script>


<?php include('layouts/footer.php');?><form id="profile-form" action="account.php" method="POST" enctype="multipart/form-data">
