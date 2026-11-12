<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$message = '';
$success = false;

// Get client details
$stmt = $conn->prepare("SELECT * FROM clients WHERE client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($client['id_verified'] == 1) {
    header("Location: client_dashboard.php");
    exit();
}

$is_pending = !empty($client['id_card_front']) && $client['id_verified'] == 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = "Invalid request.";
    } else {
        $id_front = $client['id_card_front'] ?? '';
        $id_back = $client['id_card_back'] ?? '';
        $selfie = $client['selfie'] ?? '';
        
        if (isset($_FILES['selfie']) && $_FILES['selfie']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $selfie = 'assets/uploads/ids/client_selfie_' . $client_id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['selfie']['tmp_name'], '../' . $selfie);
            }
        }
        
        if (isset($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['id_card_front']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $id_front = 'assets/uploads/ids/client_front_' . $client_id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['id_card_front']['tmp_name'], '../' . $id_front);
            }
        }
        
        if (isset($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['id_card_back']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $id_back = 'assets/uploads/ids/client_back_' . $client_id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['id_card_back']['tmp_name'], '../' . $id_back);
            }
        }
        
        if (!empty($selfie) && !empty($id_front) && !empty($id_back)) {
            $stmt = $conn->prepare("UPDATE clients SET selfie = ?, id_card_front = ?, id_card_back = ?, id_verified = 0 WHERE client_id = ?");
            $stmt->bind_param("sssi", $selfie, $id_front, $id_back, $client_id);
            $stmt->execute();
            $stmt->close();
            $message = "Documents uploaded successfully! Your verification is being reviewed.";
            $success = true;
        } else {
            $message = "Please upload your selfie and both front and back of your ID card.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Verification | Allo_Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size: 24px; margin-bottom: 10px; color: #1e293b; }
        p { color: #64748b; margin-bottom: 30px; line-height: 1.6; }
        .upload-box { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; margin-bottom: 20px; transition: 0.3s; }
        .upload-box:hover { border-color: #007a33; background: #f0fdf4; }
        .upload-box input { display: none; }
        .upload-box label { cursor: pointer; display: block; }
        .upload-box i { font-size: 40px; color: #94a3b8; margin-bottom: 15px; }
        .upload-box .filename { display: block; margin-top: 10px; font-size: 14px; color: #64748b; }
        .btn { background: #007a33; color: white; padding: 14px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn:hover { background: #006028; }
        .btn:disabled { background: #94a3b8; cursor: not-allowed; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .preview-img { max-width: 100%; height: 150px; object-fit: contain; margin-top: 10px; display: none; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-id-card"></i> ID Verification</h1>
        <p>To book services and contact professionals, please upload a selfie and a valid ID card (National ID, Passport, or Driver's License). Both front and back images are required.</p>
        
        <?php if ($is_pending): ?>
        <div class="alert" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
            <i class="fas fa-clock"></i> <strong>Verification Pending</strong><br>
            Your documents have been submitted and are being reviewed. This usually takes 24-48 hours.
        </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="upload-box" onclick="document.getElementById('selfie').click()">
                <input type="file" id="selfie" name="selfie" accept="image/*" capture="user" onchange="previewImage(this, 'preview_selfie')">
                <label>
                    <i class="fas fa-user-circle"></i>
                    <strong>Selfie Photo</strong>
                    <span class="filename">Click to take/upload</span>
                    <img id="preview_selfie" class="preview-img">
                </label>
            </div>
            
            <div class="upload-box" onclick="document.getElementById('id_front').click()">
                <input type="file" id="id_front" name="id_card_front" accept="image/*" onchange="previewImage(this, 'preview_front')">
                <label>
                    <i class="fas fa-camera"></i>
                    <strong>ID Card Front</strong>
                    <span class="filename">Click to upload</span>
                    <img id="preview_front" class="preview-img">
                </label>
            </div>
            
            <div class="upload-box" onclick="document.getElementById('id_back').click()">
                <input type="file" id="id_back" name="id_card_back" accept="image/*" onchange="previewImage(this, 'preview_back')">
                <label>
                    <i class="fas fa-camera"></i>
                    <strong>ID Card Back</strong>
                    <span class="filename">Click to upload</span>
                    <img id="preview_back" class="preview-img">
                </label>
            </div>
            
            <button type="submit" class="btn" <?= $success ? 'disabled' : '' ?>>
                <?= $success ? 'Documents Submitted' : 'Submit for Verification' ?>
            </button>
        </form>
        
        <a href="client_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
    
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
            input.parentElement.querySelector('.filename').textContent = input.files[0].name;
        }
    }
    </script>
</body>
</html>
