<?php
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Verification</h1>
    <p>Build trust with buyers through verified status</p>
</section>

<section class="verification-section">
    <div class="verification-intro">
        <h2>Why Get Verified?</h2>
        <p>Verified sellers enjoy higher visibility, better search rankings, and increased buyer trust. Stand out from the crowd with the verification badge.</p>
    </div>

    <div class="verification-benefits">
        <div class="benefit-card">
            <i data-lucide="badge-check"></i>
            <h4>Verified Badge</h4>
            <p>Display the trusted verification badge on your store</p>
        </div>
        <div class="benefit-card">
            <i data-lucide="trending-up"></i>
            <h4>Higher Visibility</h4>
            <p>Get优先展示 in search results and categories</p>
        </div>
        <div class="benefit-card">
            <i data-lucide="shield"></i>
            <h4>Buyer Trust</h4>
            <p>Build confidence with verified purchase protection</p>
        </div>
        <div class="benefit-card">
            <i data-lucide="headphones"></i>
            <h4>Priority Support</h4>
            <p>Access to dedicated seller support team</p>
        </div>
    </div>

    <div class="verification-process">
        <h2>How to Get Verified</h2>
        
        <div class="process-steps">
            <div class="process-step">
                <div class="step-icon">1</div>
                <div class="step-content">
                    <h4>Submit Documents</h4>
                    <p>Upload your ID and business registration documents</p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-icon">2</div>
                <div class="step-content">
                    <h4>Complete Profile</h4>
                    <p>Fill in your store details and add products</p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-icon">3</div>
                <div class="step-content">
                    <h4>Wait for Review</h4>
                    <p>Our team reviews your application within 48 hours</p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-icon">4</div>
                <div class="step-content">
                    <h4>Get Verified!</h4>
                    <p>Receive your badge and start enjoying benefits</p>
                </div>
            </div>
        </div>
    </div>

    <div class="verification-form-section">
        <h2>Submit for Verification</h2>
        <p>Ready to get verified? Submit your application now.</p>
        
        <form class="verification-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" placeholder="Your full legal name" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth *</label>
                    <input type="date" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>ID Type *</label>
                <select required>
                    <option value="">Select ID Type</option>
                    <option value="passport">Passport</option>
                    <option value="national-id">National ID</option>
                    <option value="drivers-license">Driver's License</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>ID Number *</label>
                <input type="text" placeholder="Your ID document number" required>
            </div>
            
            <div class="upload-area">
                <i data-lucide="upload"></i>
                <p>Upload ID Document (front)</p>
                <span>JPG, PNG or PDF (max 5MB)</span>
            </div>
            
            <div class="upload-area">
                <i data-lucide="upload"></i>
                <p>Upload ID Document (back)</p>
                <span>JPG, PNG or PDF (max 5MB)</span>
            </div>
            
            <div class="form-group">
                <label>Business Name (if applicable)</label>
                <input type="text" placeholder="Registered business name">
            </div>
            
            <button type="submit" class="btn-submit">Submit Verification</button>
        </form>
    </div>
</section>

<style>
.page-hero {
    background: #2D1F1A;
    color: white;
    padding: 5rem 2rem;
    text-align: center;
}

.page-hero h1 {
    font-family: var(--font-heading);
    font-size: 3.5rem;
    margin-bottom: 1rem;
}

.page-hero p {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.7);
}

.verification-section {
    max-width: 1000px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.verification-intro {
    text-align: center;
    margin-bottom: 3rem;
}

.verification-intro h2 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.verification-intro p {
    color: var(--text-muted);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.verification-benefits {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 4rem;
}

.benefit-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    text-align: center;
}

.benefit-card i {
    color: var(--accent-orange);
    margin-bottom: 1rem;
}

.benefit-card h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.benefit-card p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.verification-process {
    margin-bottom: 4rem;
}

.verification-process h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.process-step {
    text-align: center;
    position: relative;
}

.step-icon {
    width: 60px;
    height: 60px;
    background: var(--accent-orange);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 1rem;
}

.step-content h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.step-content p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.verification-form-section h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 0.5rem;
}

.verification-form-section > p {
    text-align: center;
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.verification-form {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    border: 1px solid var(--border-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    margin-bottom: 1rem;
}

.upload-area:hover {
    border-color: var(--accent-orange);
}

.upload-area i {
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.upload-area p {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.upload-area span {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.btn-submit {
    width: 100%;
    background: var(--accent-orange);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 1rem;
}

@media (max-width: 1024px) {
    .verification-benefits,
    .process-steps {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
    .verification-benefits,
    .process-steps {
        grid-template-columns: 1fr;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
