<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];
$message = '';
$success = false;

$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

$is_pending = !empty($provider['id_card_front']) && $provider['id_verified'] == 0;
$needs_selfie = empty($provider['selfie']);

if ($provider['id_verified'] == 1 && !$needs_selfie) {
    header("Location: provider_profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = "POST received! Processing...";
    
    $id_front = $provider['id_card_front'] ?? '';
    $id_back = $provider['id_card_back'] ?? '';
    $selfie = $provider['selfie'] ?? '';
    
    if (isset($_FILES['selfie']) && $_FILES['selfie']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $selfie = 'assets/uploads/ids/selfie_p' . $provider_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['selfie']['tmp_name'], '../' . $selfie)) {
                $message .= "<br>Selfie uploaded: " . $selfie;
            } else {
                $message .= "<br>Failed to move selfie";
            }
        }
    } else {
        $message .= "<br>No selfie file or error: " . ($_FILES['selfie']['error'] ?? 'no file');
    }
    
    if (isset($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['id_card_front']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $id_front = 'assets/uploads/ids/id_front_p' . $provider_id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['id_card_front']['tmp_name'], '../' . $id_front);
            $message .= "<br>Front uploaded: " . $id_front;
        }
    }
    
    if (isset($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['id_card_back']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $id_back = 'assets/uploads/ids/id_back_p' . $provider_id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['id_card_back']['tmp_name'], '../' . $id_back);
            $message .= "<br>Back uploaded: " . $id_back;
        }
    }
    
    $message .= "<br>Selfie: " . ($selfie ? 'set' : 'empty');
    $message .= "<br>Front: " . ($id_front ? 'set' : 'empty');
    $message .= "<br>Back: " . ($id_back ? 'set' : 'empty');
    
    if (!empty($selfie) && !empty($id_front) && !empty($id_back)) {
        $stmt = $conn->prepare("UPDATE providers SET selfie = ?, id_card_front = ?, id_card_back = ?, id_verified = 0 WHERE provider_id = ?");
        $stmt->bind_param("sssi", $selfie, $id_front, $id_back, $provider_id);
        $stmt->execute();
        $stmt->close();
        $message = "Documents uploaded successfully! Your verification is being reviewed.";
        $success = true;
    } else {
        $message .= "<br><strong>Please upload all 3 documents!</strong>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #1e293b; color: white; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #334155; padding: 40px; border-radius: 16px; }
        h1 { font-size: 24px; margin-bottom: 10px; }
        p { color: rgba(255,255,255,0.7); margin-bottom: 30px; line-height: 1.6; }
        .upload-box { border: 2px dashed rgba(255,255,255,0.3); border-radius: 12px; padding: 30px; text-align: center; margin-bottom: 20px; transition: 0.3s; cursor: pointer; }
        .upload-box:hover { border-color: #10b981; background: rgba(16, 185, 129, 0.1); }
        .upload-box input { display: block; margin: 0 auto; }
        .upload-box label { cursor: pointer; display: block; }
        .upload-box i { font-size: 40px; color: rgba(255,255,255,0.5); margin-bottom: 15px; }
        .upload-box .filename { display: block; margin-top: 10px; font-size: 14px; color: rgba(255,255,255,0.6); }
        .btn { background: #10b981; color: white; padding: 14px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn:hover { background: #059669; }
        .btn:disabled { background: #64748b; cursor: not-allowed; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .alert-error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #f87171; }
        .alert-debug { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #60a5fa; white-space: pre-wrap; }
        .preview-img { max-width: 100%; height: 150px; object-fit: contain; margin-top: 10px; display: none; border-radius: 8px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: rgba(255,255,255,0.6); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-id-card"></i> ID Verification</h1>
        <p>To start receiving bookings, please upload a selfie and a valid ID card (National ID, Passport, or Driver's License). Both front and back images are required.</p>
        
        <?php if ($is_pending): ?>
        <div class="alert" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #60a5fa;">
            <i class="fas fa-clock"></i> <strong>Verification Pending</strong><br>
            Your documents have been submitted and are being reviewed. This usually takes 24-48 hours.
        </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert <?= $success ? 'alert-success' : 'alert-debug' ?>">
            <?= $message ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="upload-box">
                <label>
                    <i class="fas fa-user-circle"></i><br>
                    <strong>Selfie Photo</strong><br>
                    <input type="file" id="selfie" name="selfie" accept="image/*" capture="user" onchange="previewImage(this, 'preview_selfie')">
                    <span class="filename" id="selfie_name">Click to take/upload</span>
                    <img id="preview_selfie" class="preview-img">
                </label>
            </div>
            
            <div class="upload-box">
                <label>
                    <i class="fas fa-camera"></i><br>
                    <strong>ID Card Front</strong><br>
                    <input type="file" id="id_front" name="id_card_front" accept="image/*" onchange="previewImage(this, 'preview_front')">
                    <span class="filename" id="front_name">Click to upload</span>
                    <img id="preview_front" class="preview-img">
                </label>
            </div>
            
            <div class="upload-box">
                <label>
                    <i class="fas fa-camera"></i><br>
                    <strong>ID Card Back</strong><br>
                    <input type="file" id="id_back" name="id_card_back" accept="image/*" onchange="previewImage(this, 'preview_back')">
                    <span class="filename" id="back_name">Click to upload</span>
                    <img id="preview_back" class="preview-img">
                </label>
            </div>
            
            <button type="submit" class="btn" <?= $success ? 'disabled' : '' ?>>
                <?= $success ? 'Documents Submitted' : 'Submit for Verification' ?>
            </button>
        </form>
        
        <a href="provider_profile.php" class="back-link">Back to Dashboard</a>
    </div>
    
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const nameSpan = document.getElementById(input.id === 'selfie' ? 'selfie_name' : (input.id === 'id_front' ? 'front_name' : 'back_name'));
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
            nameSpan.textContent = input.files[0].name;
        }
    }
    </script>
</body>
</html>
