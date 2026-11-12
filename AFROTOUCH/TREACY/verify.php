<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=verify.php');
    exit;
}

if ($_SESSION['role'] !== 'seller') {
    header('Location: home.php');
    exit;
}

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);

if ($seller && $seller['is_verified']) {
    header('Location: seller/overview.php');
    exit;
}

$debug_log = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_log .= "POST received. Method: " . $_SERVER['REQUEST_METHOD'] . ". ";
    $debug_log .= "POST data: " . json_encode($_POST) . ". ";
    
    if (isset($_POST['verification_data'])) {
        $debug_log .= "Has verification_data. ";
        
        if (!$seller) {
            $debug_log .= "ERROR: No seller found. ";
            $error = 'Please set up your shop first in Settings.';
        } else {
            $debug_log .= "Seller ID: " . $seller['id'] . ". ";
            
            $data = json_decode($_POST['verification_data'], true);
            $debug_log .= "Data decoded: " . json_encode($data) . ". ";
            
            $upload_dir = __DIR__ . '/uploads/verification/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
                $debug_log .= "Created upload dir. ";
            }
            
            $id_front = null;
            $id_back = null;
            
            $debug_log .= "FILES: " . json_encode($_FILES) . ". ";
            
            if (!empty($_FILES['id_front']['name'])) {
                $debug_log .= "Has id_front file. ";
                $ext = strtolower(pathinfo($_FILES['id_front']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $id_front = 'verification/id_front_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['id_front']['tmp_name'], __DIR__ . '/uploads/' . $id_front)) {
                        $debug_log .= "id_front uploaded. ";
                    } else {
                        $debug_log .= "id_front FAILED to upload. ";
                    }
                }
            }
            
            if (!empty($_FILES['id_back']['name'])) {
                $debug_log .= "Has id_back file. ";
                $ext = strtolower(pathinfo($_FILES['id_back']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $id_back = 'verification/id_back_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['id_back']['tmp_name'], __DIR__ . '/uploads/' . $id_back)) {
                        $debug_log .= "id_back uploaded. ";
                    } else {
                        $debug_log .= "id_back FAILED to upload. ";
                    }
                }
            }
            
            $verification_data = json_encode([
                'doc_type' => $data['doc_type'] ?? '',
                'doc_number' => $data['doc_number'] ?? '',
                'address' => $data['address'] ?? '',
                'country' => $data['country'] ?? '',
                'city' => $data['city'] ?? '',
                'id_front' => $id_front,
                'id_back' => $id_back,
                'plan' => $data['plan'] ?? 'free',
                'submitted_at' => date('Y-m-d H:i:s')
            ]);
            
            $debug_log .= "Verification data: " . $verification_data . ". ";
            
            $stmt = $pdo->prepare("UPDATE sellers SET verification_status = 'processing', verification_data = ? WHERE id = ?");
            if ($stmt->execute([$verification_data, $seller['id']])) {
                $debug_log .= "DB UPDATED successfully! ";
                $_SESSION['verification_submitted'] = true;
                header('Location: seller/overview.php?verified=1');
                exit;
            } else {
                $debug_log .= "DB UPDATE FAILED! ";
                $error = 'Failed to submit verification. Please try again.';
            }
        }
    } else {
        $debug_log .= "NO verification_data in POST! ";
    }
}

include 'components/store-layout_start.php';
?>

<?php if (!empty($debug_log)): ?>
<script>alert('DEBUG: <?php echo addslashes($debug_log); ?>');</script>
<div class="alert alert-error" style="margin: 1rem;">
    <strong>DEBUG:</strong> <?php echo htmlspecialchars($debug_log); ?>
</div>
<?php endif; ?>

<style>
:root {
    --primary-color: #D35400;
    --text-dark: #2D1F1A;
    --text-muted: #666;
    --border-color: #e0e0e0;
}

.verification-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.title-section {
    text-align: center;
    margin-bottom: 2rem;
}

.title-section .seller-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(211, 84, 0, 0.1);
    color: var(--primary-color);
    padding: 0.5rem 1rem;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.title-section h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin: 0 0 0.5rem;
    color: var(--text-dark);
}

.title-section h1 span {
    color: var(--primary-color);
}

.title-section .subtitle {
    color: var(--text-muted);
    margin: 0;
}

.progress-wrapper {
    margin-bottom: 2rem;
}

.progress-container {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 0 1rem;
}

.progress-line {
    position: absolute;
    top: 20px;
    left: 50px;
    right: 50px;
    height: 2px;
    background: #E0E0E0;
    z-index: 0;
}

.progress-active-line {
    position: absolute;
    top: 20px;
    left: 50px;
    height: 2px;
    background: var(--primary-color);
    z-index: 1;
    transition: width 0.3s ease;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    z-index: 2;
}

.step-item.active .step-icon {
    background: var(--primary-color);
    color: white !important;
    border-color: var(--primary-color);
}

.step-item.completed .step-icon {
    background: #E9F7EF;
    color: #188038;
    border-color: #188038;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #E0E0E0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    transition: all 0.3s;
    color: #666;
}

.step-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 500;
}

.step-item.active .step-label {
    color: var(--primary-color);
}

.step-card {
    background: white;
    border-radius: 24px;
    border: 1px solid var(--border-color);
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.form-content {
    display: none;
}

.form-content.active {
    display: block;
}

.biometric-visual {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #f5f5f5, #fff);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    border: 2px solid #E0E0E0;
}

.biometric-visual .fingerprint-icon,
.biometric-visual i {
    color: var(--primary-color) !important;
}

.scanning-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
    animation: pulse 2s infinite;
    opacity: 0;
}

@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.5; }
    50% { transform: scale(1.05); opacity: 0; }
    100% { transform: scale(0.95); opacity: 0; }
}

.fingerprint-icon {
    width: 48px;
    height: 48px;
    color: var(--text-muted);
}

.form-content h2 {
    text-align: center;
    font-family: var(--font-heading);
    font-size: 1.5rem;
    margin: 0 0 0.5rem;
}

.form-content h2 span {
    color: var(--primary-color);
}

.form-content .subtitle {
    text-align: center;
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary-color);
    outline: none;
}

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
    background-color: #f5f5f5;
    color: var(--text-muted);
}

.btn-back {
    background-color: #f5f5f5 !important;
    color: #666 !important;
}

.btn-back:hover {
    background-color: #e0e0e0 !important;
}

.btn-continue {
    background-color: var(--primary-color) !important;
    color: white !important;
}

.btn-continue:hover {
    background-color: #b84700 !important;
    transform: translateY(-2px);
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn[type="button"] {
    background-color: #f5f5f5;
    color: var(--text-muted);
}

button.btn[type="button"].btn-continue {
    background-color: var(--primary-color) !important;
    color: white !important;
}

.status-msg {
    text-align: center;
    font-weight: 600;
    color: var(--primary-color);
    min-height: 24px;
}

.plans-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin: 1.5rem 0;
}

.plan-card {
    padding: 1.5rem;
    border-radius: 16px;
    border: 2px solid var(--border-color);
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.plan-card:hover {
    border-color: var(--primary-color);
}

.plan-card.selected {
    border-color: var(--primary-color);
    background: rgba(211, 84, 0, 0.05);
}

.plan-card h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
}

.plan-card .price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.plan-card .price span {
    font-size: 0.9rem;
    font-weight: 400;
    color: var(--text-muted);
}

.plan-card p {
    margin: 0.5rem 0 0;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #E9F7EF;
    color: #188038;
}

.alert-error {
    background: #FDECEC;
    color: #C62828;
}

.policy-content {
    background: #f8f8f8;
    padding: 1.25rem;
    border-radius: 12px;
    font-size: 0.85rem;
    max-height: 150px;
    overflow-y: auto;
    margin-bottom: 1rem;
    text-align: left;
}

.policy-content p {
    margin: 0 0 0.5rem;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox-group input {
    width: auto;
}

.checkbox-group label {
    margin: 0;
    font-weight: 400;
}

@media (max-width: 576px) {
    .progress-container {
        padding: 0;
    }
    .progress-line {
        left: 20px;
        right: 20px;
    }
    .step-label {
        font-size: 0.65rem;
    }
    .plans-grid {
        grid-template-columns: 1fr;
    }
    .button-group {
        flex-direction: column;
    }
}
</style>

<div class="verification-container">
    <?php if ($success): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <div>
            <strong>Submitted Successfully!</strong>
            <p><?php echo $success; ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-error">
        <i data-lucide="alert-circle"></i>
        <div>
            <strong>Error</strong>
            <p><?php echo $error; ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($seller && in_array($seller['verification_status'], ['pending', 'processing'])): ?>
    <div class="alert" style="background: #FFF4E5; color: #B45D00; margin-bottom: 2rem;">
        <i data-lucide="clock"></i>
        <div>
            <strong>Verification Pending</strong>
            <p>Your documents are being reviewed. You'll be notified once processing is complete.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($seller && $seller['is_verified']): ?>
    <div class="alert alert-success">
        <i data-lucide="badge-check"></i>
        <div>
            <strong>Already Verified!</strong>
            <p>Your shop is verified and visible on AfroTouch.</p>
            <a href="seller/overview.php" class="btn" style="background: var(--primary-color); color: white; margin-top: 0.5rem; text-decoration: none; display: inline-block;">Go to Dashboard</a>
        </div>
    </div>
    <?php elseif (!$seller): ?>
    <div class="alert alert-error">
        <i data-lucide="alert-triangle"></i>
        <div>
            <strong>Shop Not Set Up</strong>
            <p>Please set up your shop first before requesting verification.</p>
            <a href="seller/settings.php" class="btn" style="background: var(--primary-color); color: white; margin-top: 0.5rem; text-decoration: none; display: inline-block;">Set Up Shop</a>
        </div>
    </div>
    <?php else: ?>

    <div class="title-section">
        <div class="seller-badge">
            <i data-lucide="shield-check" size="16"></i>
            Seller Verification
        </div>
        <h1>Verify Your <span>Identity</span></h1>
        <p class="subtitle">Complete all steps to start selling on AfroTouch</p>
    </div>

    <form method="POST" enctype="multipart/form-data" id="verificationForm">
        <input type="hidden" name="verification_data" id="verificationData">
        
        <div class="progress-wrapper">
            <div class="progress-container">
                <div class="progress-line"></div>
                <div class="progress-active-line" id="progressLine" style="width: 0%;"></div>
                
                <div class="step-item active" data-step="1">
                    <div class="step-icon"><i data-lucide="fingerprint"></i></div>
                    <span class="step-label">Biometric</span>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-icon"><i data-lucide="credit-card"></i></div>
                    <span class="step-label">ID Upload</span>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-icon"><i data-lucide="scan-face"></i></div>
                    <span class="step-label">Face ID</span>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-icon"><i data-lucide="map-pin"></i></div>
                    <span class="step-label">Location</span>
                </div>
                <div class="step-item" data-step="5">
                    <div class="step-icon"><i data-lucide="file-text"></i></div>
                    <span class="step-label">Policy</span>
                </div>
                <div class="step-item" data-step="6">
                    <div class="step-icon"><i data-lucide="package"></i></div>
                    <span class="step-label">Plan</span>
                </div>
            </div>
        </div>

        <div class="step-card">
            <!-- Step 1: Biometric -->
            <div class="form-content active" id="step-1-content">
                <div class="biometric-visual">
                    <div class="scanning-ring" id="ring-1"></div>
                    <i data-lucide="fingerprint" class="fingerprint-icon"></i>
                </div>
                <h2>Fingerprint <span>Scan</span></h2>
                <p class="subtitle">Place your finger on the sensor to verify your identity</p>
                <div class="status-msg" id="status-1"></div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-back" disabled>
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" id="btn-scan-1" onclick="simulateBiometric()">
                        Scan Fingerprint <i data-lucide="arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-continue" id="btn-next-1" style="display: none;" onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: ID Upload -->
            <div class="form-content" id="step-2-content">
                <div class="biometric-visual">
                    <i data-lucide="credit-card" class="fingerprint-icon"></i>
                </div>
                <h2>Upload <span>ID Document</span></h2>
                <p class="subtitle">Upload a clear photo of your National ID or Passport</p>
                
                <div class="form-group">
                    <label>Select Document Type</label>
                    <select name="doc_type" id="doc_type">
                        <option value="">Select Document</option>
                        <option value="national_id">National ID Card</option>
                        <option value="passport">International Passport</option>
                        <option value="drivers_license">Driver's License</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Document Number</label>
                    <input type="text" name="doc_number" id="doc_number" placeholder="Enter document number">
                </div>
                
                <div class="form-group">
                    <label>Upload Document Front *</label>
                    <input type="file" name="id_front" accept="image/*,.pdf" id="id_front">
                </div>
                
                <div class="form-group">
                    <label>Document Back (Optional)</label>
                    <input type="file" name="id_back" accept="image/*,.pdf" id="id_back">
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Face ID -->
            <div class="form-content" id="step-3-content">
                <div class="biometric-visual" style="border-radius: 24px; overflow: hidden; background: #000;">
                    <div class="scanning-ring" id="ring-3" style="border-radius: 0; width: 100%; height: 2px; top: 0; background: var(--primary-color); animation: scanLine 2s infinite linear;"></div>
                    <i data-lucide="user" class="fingerprint-icon" style="color: #444;"></i>
                </div>
                <h2>Face <span>Recognition</span></h2>
                <p class="subtitle">Position your face in the frame and look directly at the camera</p>
                <div class="status-msg" id="status-3"></div>

                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" id="btn-scan-3" onclick="simulateFaceScan()">
                        Start Scan <i data-lucide="arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-continue" id="btn-next-3" style="display: none;" onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Location -->
            <div class="form-content" id="step-4-content">
                <div class="biometric-visual">
                    <i data-lucide="map-pin" class="fingerprint-icon"></i>
                </div>
                <h2>Store <span>Location</span></h2>
                <p class="subtitle">Provide your business address for verification</p>
                
                <div class="form-group">
                    <label>Business Address</label>
                    <input type="text" name="address" id="address" placeholder="Street Address, City, State">
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" id="city" placeholder="City">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <select name="country" id="country">
                        <option value="">Select Country</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Kenya">Kenya</option>
                        <option value="South Africa">South Africa</option>
                        <option value="USA">United States</option>
                        <option value="UK">United Kingdom</option>
                        <option value="France">France</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 5: Policy -->
            <div class="form-content" id="step-5-content">
                <div class="biometric-visual">
                    <i data-lucide="file-check" class="fingerprint-icon"></i>
                </div>
                <h2>Seller <span>Policy</span></h2>
                <p class="subtitle">Please review and accept our seller terms and conditions</p>
                
                <div class="policy-content">
                    <p><strong>Terms and Conditions</strong></p>
                    <p>By becoming a seller on AfroTouch, you agree to:</p>
                    <p>1. Provide authentic African handmade products</p>
                    <p>2. Ship orders within 5 business days</p>
                    <p>3. Maintain responsive customer service</p>
                    <p>4. Compute with AfroTouch quality standards</p>
                    <p>5. Accept payment via platform secure methods</p>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="accept-policy">
                    <label for="accept-policy">I accept the seller policy and terms</label>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" id="btn-next-5" disabled onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 6: Plan -->
            <div class="form-content" id="step-6-content">
                <h2>Choose <span>Plan</span></h2>
                <p class="subtitle">Select a subscription plan to start your journey</p>
                
                <div class="plans-grid">
                    <div class="plan-card selected" data-plan="free" onclick="selectPlan(this)">
                        <h3>Starter</h3>
                        <div class="price">Free</div>
                        <p>Up to 10 products</p>
                    </div>
                    <div class="plan-card" data-plan="pro" onclick="selectPlan(this)">
                        <h3>Pro</h3>
                        <div class="price">5,994 FCA<span>/mo</span></div>
                        <p>Unlimited products</p>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-continue" onclick="finishVerification()">
                        Complete Setup <i data-lucide="check"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php endif; ?>
</div>

<style>
@keyframes scanLine {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}
</style>

<script>
let currentStep = 1;
let selectedPlan = 'free';
let completedSteps = {};

function updateProgress() {
    const progressLine = document.getElementById('progressLine');
    const width = ((currentStep - 1) / 5) * 100;
    progressLine.style.width = width + '%';
    
    document.querySelectorAll('.step-item').forEach((item, index) => {
        item.classList.remove('active', 'completed');
        if (index + 1 < currentStep) {
            item.classList.add('completed');
        } else if (index + 1 === currentStep) {
            item.classList.add('active');
        }
    });
}

function nextStep() {
    // Validation for Step 2 (ID Upload)
    if (currentStep === 2) {
        const docType = document.getElementById('doc_type').value;
        const docNumber = document.getElementById('doc_number').value;
        const idFront = document.getElementById('id_front').value;
        
        if (!docType || !docNumber || !idFront) {
            alert('Please fill in Document Type, Document Number, and upload the ID Document Front');
            return;
        }
    }
    
    // Validation for Step 4 (Location)
    if (currentStep === 4) {
        const address = document.getElementById('address').value;
        const city = document.getElementById('city').value;
        const country = document.getElementById('country').value;
        
        if (!address || !city || !country) {
            alert('Please fill in Address, City, and Country');
            return;
        }
    }
    
    if (currentStep < 6) {
        document.getElementById('step-' + currentStep + '-content').classList.remove('active');
        currentStep++;
        document.getElementById('step-' + currentStep + '-content').classList.add('active');
        updateProgress();
    }
}

function prevStep() {
    if (currentStep > 1) {
        document.getElementById('step-' + currentStep + '-content').classList.remove('active');
        currentStep--;
        document.getElementById('step-' + currentStep + '-content').classList.add('active');
        updateProgress();
    }
}

function simulateBiometric() {
    const btn = document.getElementById('btn-scan-1');
    const status = document.getElementById('status-1');
    const ring = document.getElementById('ring-1');
    
    btn.disabled = true;
    btn.innerHTML = 'Scanning... <i data-lucide="loader"></i>';
    lucide.createIcons();
    
    ring.style.animation = 'pulse 1s infinite';
    status.textContent = 'Scanning fingerprint...';
    
    setTimeout(() => {
        ring.style.animation = 'none';
        status.textContent = 'Fingerprint matched successfully!';
        status.style.color = '#188038';
        completedSteps[1] = true;
        
        btn.style.display = 'none';
        document.getElementById('btn-next-1').style.display = 'flex';
        document.getElementById('btn-next-1').innerHTML = 'Continue <i data-lucide="arrow-right"></i>';
        lucide.createIcons();
    }, 2500);
}

function simulateFaceScan() {
    const btn = document.getElementById('btn-scan-3');
    const status = document.getElementById('status-3');
    
    btn.disabled = true;
    btn.innerHTML = 'Scanning... <i data-lucide="loader"></i>';
    lucide.createIcons();
    
    status.textContent = 'Detecting face...';
    
    setTimeout(() => {
        status.textContent = 'Analyzing facial features...';
    }, 1500);
    
    setTimeout(() => {
        status.textContent = 'Face recognized successfully!';
        status.style.color = '#188038';
        completedSteps[3] = true;
        
        btn.style.display = 'none';
        document.getElementById('btn-next-3').style.display = 'flex';
    }, 3500);
}

function selectPlan(card) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    selectedPlan = card.dataset.plan;
}

document.getElementById('accept-policy').addEventListener('change', function() {
    document.getElementById('btn-next-5').disabled = !this.checked;
});

function finishVerification() {
    const form = document.getElementById('verificationForm');
    
    const data = {
        doc_type: document.getElementById('doc_type').value,
        doc_number: document.getElementById('doc_number').value,
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        country: document.getElementById('country').value,
        plan: selectedPlan
    };
    
    document.getElementById('verificationData').value = JSON.stringify(data);
    form.submit();
}

updateProgress();
</script>

<?php include 'components/store-layout_end.php'; ?>