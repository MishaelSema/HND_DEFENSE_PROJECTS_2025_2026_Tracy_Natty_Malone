<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Check status - only approved providers can post
$res_status = $conn->query("SELECT status FROM providers WHERE provider_id = $provider_id");
if ($res_status && $res_status->fetch_assoc()['status'] !== 'approved') {
    header("Location: provider_profile.php?error=pending");
    exit();
}

// Handle Post Upload
$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_post'])) {
    $caption_raw = stripslashes($_POST['caption'] ?? '');
    $caption = $conn->real_escape_string($caption_raw);
    
    // Check for multiple images in post_images[]
    if (isset($_FILES['post_images']) && !empty($_FILES['post_images']['name'][0])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        
        // Debug: check upload errors
        $total_files = count($_FILES['post_images']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['post_images']['error'][$i] > 0) {
                $msg .= "File " . ($i+1) . " error: " . $_FILES['post_images']['error'][$i] . "<br>";
            }
        }
        
        if (!empty($msg)) {
            $msg .= "_common causes: file too large (max 2MB) or wrong format_";
        }
        $post_id = 0;
        
        // Ensure directory exists
        if (!is_dir('../assets/uploads/posts')) {
            mkdir('../assets/uploads/posts', 0777, true);
        }

        $total_files = count($_FILES['post_images']['name']);
        $uploaded_count = 0;

        for ($i = 0; $i < $total_files; $i++) {
            if ($uploaded_count >= 8) break;
            if ($_FILES['post_images']['error'][$i] == 0) {
                $ext = strtolower(pathinfo($_FILES['post_images']['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $filename = 'assets/uploads/posts/post_' . $provider_id . '_' . time() . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($_FILES['post_images']['tmp_name'][$i], '../' . $filename)) {
                        if ($uploaded_count == 0) {
                            // First image is the primary one in provider_posts
                            $stmt = $conn->prepare("INSERT INTO provider_posts (provider_id, image_path, caption) VALUES (?, ?, ?)");
                            if (!$stmt) die("Prepare failed: " . $conn->error);
                            $stmt->bind_param("iss", $provider_id, $filename, $caption);
                            if ($stmt->execute()) {
                                $post_id = $stmt->insert_id;
                            } else {
                                die("Execute failed: " . $stmt->error);
                            }
                            $stmt->close();
                        } else if ($post_id > 0) {
                            // Subsequent images go to post_images table
                            $stmt = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
                            if (!$stmt) die("Prepare failed for extra images: " . $conn->error);
                            $stmt->bind_param("is", $post_id, $filename);
                            if (!$stmt->execute()) {
                                die("Execute failed for extra images: " . $stmt->error);
                            }
                            $stmt->close();
                        }
                        $uploaded_count++;
                    }
                }
            }
        }
    }
}

// Handle Post Editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_post'])) {
    $post_id = intval($_POST['post_id']);
    $caption = $conn->real_escape_string($_POST['caption']);
    
    $stmt = $conn->prepare("UPDATE provider_posts SET caption = ? WHERE id = ? AND provider_id = ?");
    $stmt->bind_param("sii", $caption, $post_id, $provider_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Post Deletion
if (isset($_GET['delete'])) {
    $post_id = intval($_GET['delete']);
    // Verify ownership and get primary image
    $res = $conn->query("SELECT image_path FROM provider_posts WHERE id = $post_id AND provider_id = $provider_id");
    if ($res && $res->num_rows > 0) {
        $primary_img = $res->fetch_assoc()['image_path'];
        
        // 1. Delete extra images from filesystem and table
        $res_extra = $conn->query("SELECT image_path FROM post_images WHERE post_id = $post_id");
        while ($extra = $res_extra->fetch_assoc()) {
            if (file_exists('../' . $extra['image_path'])) {
                unlink('../' . $extra['image_path']);
            }
        }
        $conn->query("DELETE FROM post_images WHERE post_id = $post_id");

        // 2. Delete primary image from filesystem
        if (file_exists('../' . $primary_img)) {
            unlink('../' . $primary_img);
        }

        // 3. Delete from primary table
        $conn->query("DELETE FROM provider_posts WHERE id = $post_id");
    }
}

header("Location: provider_profile.php");
exit();
?>
