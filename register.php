<?php include('layouts/header.php'); ?>

<?php
session_start();
include('server/connection.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Regex for name: Only letters and spaces
    $namePattern = "/^[a-zA-Z\s]+$/";
    // Regex for email: Valid email format
    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";
    // Regex for password: At least 6 characters, at least 1 letter and 1 number
    $passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/";

    // PHP validation: Check if fields are empty
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        header('location: register.php?error=All fields are required');
        exit();
    }

    // Validate name with regex
    if (!preg_match($namePattern, $name)) {
        header('location: register.php?error=Invalid name format');
        exit();
    }

    // Validate email with regex
    if (!preg_match($emailPattern, $email)) {
        header('location: register.php?error=Invalid email format');
        exit();
    }

    // If passwords don't match
    if ($password !== $confirmPassword) {
        header('location: register.php?error=Passwords don\'t match');
        exit();
    }

    // Validate password with regex
    if (!preg_match($passwordPattern, $password)) {
        header('location: register.php?error=Password must be at least 6 characters and include at least 1 letter and 1 number');
        exit();
    }

    // Validate image if one was uploaded
    $hasImage = isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != UPLOAD_ERR_NO_FILE;
    $validImage = true;
    $imageExtension = '';
    
    if ($hasImage) {
        $imageName = $_FILES['profile_image']['name'];
        $imageSize = $_FILES['profile_image']['size'];
        $tmpName = $_FILES['profile_image']['tmp_name'];
        
        // Image validation
        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        
        if (!in_array($imageExtension, $validImageExtension)) {
            header('location: register.php?error=Invalid image format. Please use JPG, JPEG or PNG');
            exit();
        }
        elseif ($imageSize > 1200000) {
            header('location: register.php?error=Image size is too large (max 1.2MB)');
            exit();
        }
    }

    // Check if user is already registered
    $stmt1 = $conn->prepare("SELECT count(*) FROM users WHERE user_email = ?");
    $stmt1->bind_param('s', $email);
    $stmt1->execute();
    $stmt1->bind_result($num_rows);
    $stmt1->store_result();
    $stmt1->fetch();
    $stmt1->close();

    // If the email already exists
    if ($num_rows != 0) {
        header('location: register.php?error=User with this email already exists');
        exit();
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create a new user
            $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $email, md5($password));
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                // Process image upload if one was provided
                if ($hasImage) {
                    // Create directory if it doesn't exist
                    if (!file_exists('assets/imgs/profiles/')) {
                        mkdir('assets/imgs/profiles/', 0755, true);
                    }
                    
                    // Generate safe filename
                    $newImageName = "profile_" . $user_id . "_" . date("Y-m-d-H-i-s") . "." . $imageExtension;
                    $uploadPath = 'assets/imgs/profiles/' . $newImageName;
                    
                    // Move uploaded file
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        chmod($uploadPath, 0644);
                        
                        // Update user profile image in database
                        $stmt2 = $conn->prepare("UPDATE users SET user_profile = ? WHERE user_id = ?");
                        $stmt2->bind_param("si", $newImageName, $user_id);
                        
                        if (!$stmt2->execute()) {
                            throw new Exception("Failed to update profile image in database");
                        }
                        $stmt2->close();
                    } else {
                        throw new Exception("Failed to move uploaded file");
                    }
                }
                
                // If everything succeeded, commit the transaction
                $conn->commit();
                
                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;
                $_SESSION['logged_in'] = true;
                
                $stmt->close();
                header('location: account.php?success=Account created successfully');
                exit();
            } else {
                throw new Exception("Could not create account");
            }
        } catch (Exception $e) {
            // If anything fails, roll back the transaction
            $conn->rollback();
            header('location: register.php?error=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>

<!-- Register -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Register</h2>
        <hr class="mx-auto">
    </div>
    <div class="container mx-auto">
        <form id="register-form" method="POST" action="register.php" enctype="multipart/form-data">
            <p style="color: red;"><?php if(isset($_GET['error'])){echo htmlspecialchars($_GET['error']);}?></p>
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" id="register-name" name="name" placeholder="Name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" id="register-email" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="register-password" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control" id="confirm-password" name="confirmPassword" placeholder="Confirm Password">
            </div>
            <div class="form-group">
                <label>Profile Picture (Optional)</label>
                <input type="file" class="form-control" id="profile_image" name="profile_image" accept=".jpg, .jpeg, .png">
                <small class="form-text text-muted">Max file size: 1.2MB. Supported formats: JPG, JPEG, PNG</small>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" id="register-btn" name="register" value="Register">
            </div>
            <div class="form-group">
                <a id="login-url" class="btn" href="login.php">Do you have an account? Login</a> 
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_image');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, JPEG, or PNG)');
                    this.value = '';
                    return;
                }
                
                // Validate file size (1.2MB = 1200000 bytes)
                if (file.size > 1200000) {
                    alert('Image size must be less than 1.2MB');
                    this.value = '';
                    return;
                }
            }
        });
    }
});
</script>

<?php include('layouts/footer.php'); ?>
