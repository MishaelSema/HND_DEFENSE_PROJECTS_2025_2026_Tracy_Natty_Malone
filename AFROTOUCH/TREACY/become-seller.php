<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Become a Seller</h1>
    <p>Start your journey as an AfroTouch artisan</p>
</section>

<section class="form-section">
    <div class="form-container">
        <form class="seller-form">
            <h2>Create Your Seller Account</h2>
            <p class="form-subtitle">Join thousands of artisans selling their crafts on AfroTouch</p>
            
            <div class="form-section-title">Store Information</div>
            
            <div class="form-group">
                <label>Store Name *</label>
                <input type="text" placeholder="e.g., Felix Crafts" required>
            </div>
            
            <div class="form-group">
                <label>Store Description *</label>
                <textarea rows="4" placeholder="Tell buyers about your store and what makes your products special..." required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Country *</label>
                    <select required>
                        <option value="">Select Country</option>
                        <option value="cameroon">Cameroon</option>
                        <option value="ghana">Ghana</option>
                        <option value="nigeria">Nigeria</option>
                        <option value="kenya">Kenya</option>
                        <option value="south-africa">South Africa</option>
                        <option value="mali">Mali</option>
                        <option value="senegal">Senegal</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" placeholder="e.g., Douala" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" placeholder="Your business address">
            </div>
            
            <div class="form-section-title">Contact Information</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" placeholder="+237 600 000 000" required>
                </div>
                <div class="form-group">
                    <label>WhatsApp (optional)</label>
                    <input type="tel" placeholder="+237 600 000 000">
                </div>
            </div>
            
            <div class="form-group">
                <label>Return Address *</label>
                <textarea rows="3" placeholder="Where should returns be sent?" required></textarea>
            </div>
            
            <div class="form-section-title">Verification (Optional)</div>
            
            <p class="form-hint">Get verified to build trust with buyers. Upload your ID and business documents.</p>
            
            <div class="upload-area">
                <i data-lucide="upload"></i>
                <p>Upload ID Document</p>
                <span>JPG, PNG or PDF (max 5MB)</span>
            </div>
            
            <div class="upload-area">
                <i data-lucide="upload"></i>
                <p>Upload Business Documents</p>
                <span>JPG, PNG or PDF (max 5MB)</span>
            </div>
            
            <div class="form-check">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="terms-of-service.php">Terms of Service</a> and <a href="privacy-policy.php">Privacy Policy</a></label>
            </div>
            
            <button type="submit" class="btn-submit">Create My Store</button>
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

.form-section {
    max-width: 800px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.form-container {
    background: white;
    border-radius: 24px;
    padding: 3rem;
    border: 1px solid var(--border-color);
}

.seller-form h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.form-section-title {
    font-weight: 600;
    font-size: 1.1rem;
    margin: 2rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
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
    color: var(--text-dark);
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
    font-family: var(--font-body);
    transition: all 0.2s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: var(--accent-orange);
    outline: none;
    box-shadow: 0 0 0 3px rgba(211, 84, 0, 0.1);
}

.form-hint {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 1rem;
}

.upload-area:hover {
    border-color: var(--accent-orange);
    background: rgba(211, 84, 0, 0.02);
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

.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 1.5rem 0;
}

.form-check input {
    width: 20px;
    height: 20px;
    accent-color: var(--accent-orange);
}

.form-check label {
    font-size: 0.9rem;
}

.form-check a {
    color: var(--accent-orange);
    text-decoration: none;
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
    transition: transform 0.2s;
}

.btn-submit:hover {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
    .form-container {
        padding: 1.5rem;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
