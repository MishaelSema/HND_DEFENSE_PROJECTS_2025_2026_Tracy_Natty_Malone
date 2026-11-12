<?php
// admin/admin_edit_category.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();

$id = intval($_GET['id'] ?? 0);
$error = "";
$success = "";

// Fetch Category
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cat) {
    header("Location: admin_categories.php");
    exit();
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_category'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $slug = $cat['slug']; // Keep slug for consistency unless renamed
    
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        $image_path = $cat['image_path'];
        
        // Handle New Image
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $target_dir = "../assets/img/services/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES["service_image"]["name"], PATHINFO_EXTENSION);
            $new_filename = $slug . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
                $image_path = "assets/img/services/" . $new_filename;
            }
        }

        $sql = "UPDATE services SET name = ?, description = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $image_path, $id);
        
        if ($stmt->execute()) {
            header("Location: admin_categories.php?msg=Category updated successfully!");
            exit();
        } else {
            $error = "Error updating category.";
        }
    }
}

include 'includes/admin_header.php';
?>

<div class="table-container" style="padding: 30px; max-width: 600px; margin: 40px auto;">
    <h3>Edit Service Category</h3>
    <a href="admin_categories.php" style="font-size: 13px; color: var(--primary); text-decoration: none; margin-bottom: 20px; display: inline-block;">&larr; Back to List</a>
    
    <?php if ($error): ?>
        <p style="color: var(--danger); margin-bottom: 15px;"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: var(--primary); margin-bottom: 15px;"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Category Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Description</label>
            <textarea name="description" rows="3" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px; font-family: inherit;"><?= htmlspecialchars($cat['description']) ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Service Image (Background)</label>
            <?php if ($cat['image_path']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?= htmlspecialchars($cat['image_path']) ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                </div>
            <?php endif; ?>
            <input type="file" name="service_image" accept="image/*" style="width:100%; padding:8px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>

        <button type="submit" name="update_category" style="width:100%; background: var(--primary); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Save Changes</button>
    </form>
</div>

</div> <!-- Close main-content -->
</body>
</html>
