<?php 
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['email'];

    // Check for duplicate report (same title, category, location within last 5 minutes)
    $stmt = $pdo->prepare("SELECT id FROM issues WHERE user_id = ? AND title = ? AND category = ? AND location = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $stmt->execute([$user_id, $title, $category, $location]);
    if ($stmt->rowCount() > 0) {
        $message = 'You have already submitted a similar report recently. Please wait a few minutes before submitting again.';
        $messageType = 'error';
    } elseif (empty($title) || empty($description) || empty($category) || empty($location)) {
        $message = 'All fields are required';
        $messageType = 'error';
    } else {
        $image_paths = [];
        
        // Handle multiple image uploads
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $upload_dir = 'uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $file_count = count($_FILES['images']['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_type = $_FILES['images']['type'][$i];
                    
                    if (in_array($file_type, $allowed_types)) {
                        $file_name = time() . '_' . $i . '_' . basename($_FILES['images']['name'][$i]);
                        $target_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_path)) {
                            $image_paths[] = $target_path;
                        }
                    }
                }
            }
        }
        
        $image_path = !empty($image_paths) ? json_encode($image_paths) : null;

        // Insert issue into database
        $stmt = $pdo->prepare("INSERT INTO issues (user_id, title, description, category, location, image_path, is_public) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $title, $description, $category, $location, $image_path, $is_public])) {
            $issue_id = $pdo->lastInsertId();
            
            // Send confirmation email
            $email_subject = "Issue Reported - #$issue_id";
            $email_body = "
                <h2>Issue Reported Successfully</h2>
                <p>Dear User,</p>
                <p>Your issue has been submitted successfully. Here are the details:</p>
                <ul>
                    <li><strong>Issue ID:</strong> #$issue_id</li>
                    <li><strong>Title:</strong> $title</li>
                    <li><strong>Category:</strong> $category</li>
                    <li><strong>Location:</strong> $location</li>
                    <li><strong>Status:</strong> Pending</li>
                </ul>
                <p>We will review your issue and update you shortly.</p>
                <p>Best regards,<br>IssueSpot Team</p>
            ";
            
            sendEmail($user_email, $email_subject, $email_body);
            
            $message = 'Issue reported successfully! A confirmation email has been sent to your email address.';
            $messageType = 'success';
        } else {
            $message = 'Failed to submit issue. Please try again.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue - IssueSpot</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo"><a href="index.php" style="color: white; text-decoration: none;">IssueSpot</a></div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
            <nav id="main-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="report_issue.php">Report Issue</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <script>
    function toggleMenu() {
        document.getElementById('main-nav').classList.toggle('active');
    }
    </script>

    <main>
        <div class="container">
            <div class="card">
                <h2>Report an Issue</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Issue Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g., Pothole on Main Street" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <option value="Road Damage">Road Damage</option>
                            <option value="Waste Management">Waste Management</option>
                            <option value="Water Supply">Water Supply</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Public Safety">Public Safety</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="e.g., Near Market Square, Douala" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe the issue in detail..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="images">Images (Optional) - Select multiple</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple>
                        <small style="color: #666;">Supported formats: JPEG, PNG, GIF. You can select multiple images.</small>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" id="is_public" name="is_public" style="width: auto;">
                            <span>Make this report <strong>Public</strong></span>
                        </label>
                        <small style="color: #666;">Public reports can be viewed by everyone. Private reports can only be seen by you and admin.</small>
                    </div>

                    <button type="submit" class="btn">Submit Issue</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
