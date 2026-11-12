<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Identity | Afro Touch</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="verify-style.css">
</head>
<body>
    <div class="verification-container">
        <header>
            <div class="brand">AFRO <span>TOUCH</span></div>
            <div class="header-icons">
                <i data-lucide="shopping-cart"></i>
                <i data-lucide="menu"></i>
            </div>
        </header>

        <div class="title-section">
            <div class="seller-badge">
                <i data-lucide="shield-check" size="16"></i>
                Seller Verification
            </div>
            <h1>Verify Your <span>Identity</span></h1>
            <p class="subtitle">Complete all steps to start selling on Afro Touch</p>
        </div>

        <div class="progress-wrapper">
            <div class="progress-container">
                <div class="progress-line"></div>
                <div class="progress-active-line" style="width: 0%;"></div>
                
                <div class="step-item active" data-step="1">
                    <div class="step-icon">
                        <i data-lucide="fingerprint"></i>
                    </div>
                    <span class="step-label">Biometric</span>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                    <span class="step-label">ID Upload</span>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-icon">
                        <i data-lucide="scan-face"></i>
                    </div>
                    <span class="step-label">Face ID</span>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-icon">
                        <i data-lucide="map-pin"></i>
                    </div>
                    <span class="step-label">Location</span>
                </div>
                <div class="step-item" data-step="5">
                    <div class="step-icon">
                        <i data-lucide="file-text"></i>
                    </div>
                    <span class="step-label">Policy</span>
                </div>
                <div class="step-item" data-step="6">
                    <div class="step-icon">
                        <i data-lucide="package"></i>
                    </div>
                    <span class="step-label">Subscription</span>
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
                <h2>Fingerprint Scan</h2>
                <p class="subtitle">Place your finger on the sensor to verify your identity</p>
                <div class="status-msg" id="status-1">
                    <!-- Dynamic status -->
                </div>
                
                <div class="button-group">
                    <button class="btn btn-back" disabled>
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" id="btn-scan-1">
                        Scan Fingerprint <i data-lucide="arrow-right"></i>
                    </button>
                    <button class="btn btn-continue" id="btn-next-1" style="display: none;">
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
                    <select>
                        <option>National ID Card</option>
                        <option>International Passport</option>
                        <option>Driver's License</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Upload Document Front</label>
                    <input type="file" accept="image/*">
                </div>

                <div class="button-group">
                    <button class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" onclick="nextStep()">
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
                <h2>Face Recognition</h2>
                <p class="subtitle">Position your face in the frame and look directly at the camera</p>
                <div class="status-msg" id="status-3"></div>

                <div class="button-group">
                    <button class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" id="btn-scan-3">
                        Start Scan <i data-lucide="arrow-right"></i>
                    </button>
                    <button class="btn btn-continue" id="btn-next-3" style="display: none;" onclick="nextStep()">
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
                    <input type="text" placeholder="Street Address, City, State">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <select>
                        <option>Nigeria</option>
                        <option>Ghana</option>
                        <option>United Kingdom</option>
                        <option>United States</option>
                    </select>
                </div>

                <div class="button-group">
                    <button class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" onclick="nextStep()">
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
                
                <div style="text-align: left; background: #F8F8F8; padding: 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem; max-height: 150px; overflow-y: auto;">
                    <p>By becoming a seller on Afro Touch, you agree to comply with our marketplace policies regarding product quality, shipping times, and customer service...</p>
                    <p>1. Quality Assurance</p>
                    <p>2. Shipping Responsibilities</p>
                    <p>3. Commission and Payments</p>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="accept-policy" style="width: auto;">
                    <label for="accept-policy" style="margin-bottom: 0;">I accept the seller policy</label>
                </div>

                <div class="button-group">
                    <button class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" id="btn-next-5" disabled onclick="nextStep()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 6: Subscription -->
            <div class="form-content" id="step-6-content">
                <h2>Choose <span>Plan</span></h2>
                <p class="subtitle">Select a subscription plan to start your journey</p>
                
                <div class="plans-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%; margin-top: 20px;">
                    <div class="plan-card active" onclick="selectPlan(this)" style="border: 2px solid var(--primary-color); padding: 20px; border-radius: 16px; background: rgba(211, 84, 0, 0.05); cursor: pointer;">
                        <h3>Starter</h3>
                        <div style="font-size: 1.5rem; font-weight: 700; margin: 10px 0;">Free</div>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Up to 10 products</p>
                    </div>
                    <div class="plan-card" onclick="selectPlan(this)" style="border: 1px solid var(--border-light); padding: 20px; border-radius: 16px; cursor: pointer;">
                        <h3>Pro</h3>
                        <div style="font-size: 1.5rem; font-weight: 700; margin: 10px 0;">$29<span>/mo</span></div>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Unlimited products</p>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-back" onclick="prevStep()">
                        <i data-lucide="arrow-left"></i> Back
                    </button>
                    <button class="btn btn-continue" onclick="finishVerification()">
                        Complete Setup <i data-lucide="check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="verify.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
    <style>
        @keyframes scanLine {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
        .plan-card.active {
            border: 2px solid var(--primary-color) !important;
            background: rgba(211, 84, 0, 0.05) !important;
        }
        .status-msg {
            height: 30px;
            margin-top: 10px;
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</body>
</html>
