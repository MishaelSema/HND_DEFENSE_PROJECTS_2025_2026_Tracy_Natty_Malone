<?php
// admin/admin_categories.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();

$error = "";
$success = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Optional: Check if providers are using this category before deleting
    $conn->query("DELETE FROM services WHERE id = $id");
    header("Location: admin_categories.php");
    exit();
}

// Handle Add Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    
    // Generate simple slug (lowercase, replace spaces with hyphens)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Handle Image Upload
        $image_path = "";
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $target_dir = "../assets/img/services/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES["service_image"]["name"], PATHINFO_EXTENSION);
            $new_filename = $slug . "." . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
                $image_path = "assets/img/services/" . $new_filename;
            }
        }

        $sql = "INSERT INTO services (name, slug, description, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $slug, $description, $image_path);
        
        try {
            if ($stmt->execute()) {
                $success = "Category added successfully!";
            } else {
                $error = "Error adding category.";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = "This service category already exists in the database.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch Categories
$res = $conn->query("SELECT * FROM services ORDER BY name ASC");
$categories = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}

include 'includes/admin_header.php';
?>

<div class="table-container" style="padding: 30px; margin-bottom: 40px;">
    <h3 style="margin-bottom:20px; color: var(--dark);">Add New Category (Service)</h3>
    
    <?php if ($error): ?>
        <p style="color: var(--danger); margin-bottom: 15px; font-weight: 600;"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: var(--primary); margin-bottom: 15px; font-weight: 600;"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
        <div>
            <label style="font-size: 13px; font-weight: 600; color: #64748b;">Category Name</label>
            <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
        </div>
        <div>
            <label style="font-size: 13px; font-weight: 600; color: #64748b;">Short Description</label>
            <input type="text" name="description" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
        </div>
        <div>
            <label style="font-size: 13px; font-weight: 600; color: #64748b;">Background Image</label>
            <input type="file" name="service_image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px;">
        </div>
        <button type="submit" name="add_category" style="background: var(--primary); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Add Category</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="5" style="text-align:center; padding: 20px;">No categories found.</td></tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td style="font-family: monospace; font-size: 12px; color: #64748b;"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td style="font-size: 13px; color: #475569;"><?= htmlspecialchars($cat['description']) ?></td>
                        <td>
                            <a href="admin_edit_category.php?id=<?= $cat['id'] ?>" style="background: var(--secondary); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px;">Edit</a>
                            <a href="admin_categories.php?delete=<?= $cat['id'] ?>" style="background: var(--danger); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold;" onclick="return confirm('WARNING: Deleting a category might orphan professionals assigned to it. Proceed?');">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div> <!-- Close main-content -->
</body>
</html>
