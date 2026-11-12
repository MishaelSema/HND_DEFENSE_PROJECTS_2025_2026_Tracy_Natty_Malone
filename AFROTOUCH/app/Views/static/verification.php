<?php
$page_title = 'Seller Verification';
?>

<div class="static-page">
    <div class="static-container">
        <h1>Seller Verification</h1>
        
        <div class="static-content">
            <p class="intro-text">AfroTouch verification helps build trust with buyers by confirming your identity and business credentials.</p>
            
            <div class="highlight-box">
                <div class="highlight-icon">
                    <i data-lucide="shield-check"></i>
                </div>
                <h3>Why Get Verified?</h3>
                <ul class="benefit-list">
                    <li><i data-lucide="check-circle"></i> Blue checkmark badge on your shop</li>
                    <li><i data-lucide="check-circle"></i> Higher search rankings</li>
                    <li><i data-lucide="check-circle"></i> Access to verification-only features</li>
                    <li><i data-lucide="check-circle"></i> Increased buyer confidence</li>
                </ul>
            </div>
            
            <div class="steps-section">
                <h2>How to Get Verified</h2>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Complete your profile</h4>
                        <p>Fill in all required business information in your dashboard settings.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Verify your email</h4>
                        <p>Make sure your email address is confirmed.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Submit documents</h4>
                        <p>Upload a valid ID and business registration (if applicable).</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4>Wait for review</h4>
                        <p>Our team typically reviews applications within 2-3 business days.</p>
                    </div>
                </div>
            </div>
            
            <div class="docs-section">
                <h2>Required Documents</h2>
                <div class="docs-grid">
                    <div class="doc-card">
                        <i data-lucide="id-card"></i>
                        <h4>Government-issued ID</h4>
                        <p>Passport, national ID, or driver's license</p>
                    </div>
                    <div class="doc-card">
                        <i data-lucide="building-2"></i>
                        <h4>Business documents</h4>
                        <p>Business registration certificate or trade license</p>
                    </div>
                    <div class="doc-card">
                        <i data-lucide="map-pin"></i>
                        <h4>Proof of address</h4>
                        <p>Utility bill or bank statement</p>
                    </div>
                </div>
            </div>
            
            <div class="tiers-section">
                <h2>Verification Tiers</h2>
                <div class="tiers-grid">
                    <div class="tier-card">
                        <div class="tier-badge gray">
                            <i data-lucide="check"></i>
                        </div>
                        <h3>Basic</h3>
                        <p class="tier-req">Email + Phone verified</p>
                    </div>
                    <div class="tier-card">
                        <div class="tier-badge blue">
                            <i data-lucide="check"></i>
                        </div>
                        <h3>Verified</h3>
                        <p class="tier-req">ID verified</p>
                    </div>
                    <div class="tier-card">
                        <div class="tier-badge gold">
                            <i data-lucide="award"></i>
                        </div>
                        <h3>Trusted</h3>
                        <p class="tier-req">Business verified</p>
                    </div>
                </div>
            </div>
            
            <div class="cta-box">
                <h3>Ready to get verified?</h3>
                <p>Start your seller account today and build trust with thousands of buyers.</p>
                <a href="/AFROTOUCH/public/register?role=seller" class="btn-cta">Become a Seller</a>
            </div>
        </div>
    </div>
</div>

<style>
.static-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 60px 20px;
}

.static-container h1 {
    font-size: 42px;
    margin-bottom: 15px;
    text-align: center;
    color: #241611;
}

.intro-text {
    text-align: center;
    font-size: 18px;
    color: #6D5D57;
    margin-bottom: 40px;
}

.highlight-box {
    background: linear-gradient(135deg, #FFF8F3 0%, #FEF0E5 100%);
    border: 1px solid #F5DDCC;
    border-radius: 20px;
    padding: 35px;
    margin: 30px 0;
}

.highlight-icon {
    width: 60px;
    height: 60px;
    background: #D35400;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.highlight-icon i {
    width: 30px;
    height: 30px;
    color: white;
}

.highlight-box h3 {
    font-size: 22px;
    color: #241611;
    margin-bottom: 20px;
}

.benefit-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.benefit-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    font-size: 15px;
}

.benefit-list li i {
    width: 20px;
    height: 20px;
    color: #D35400;
    flex-shrink: 0;
}

.steps-section, .docs-section, .tiers-section {
    margin: 50px 0;
}

.steps-section h2, .docs-section h2, .tiers-section h2 {
    font-size: 28px;
    color: #241611;
    margin-bottom: 25px;
    text-align: center;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.step-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    border: 1px solid #E8E2DE;
    position: relative;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #D35400;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 15px;
}

.step-card h4 {
    font-size: 16px;
    color: #241611;
    margin-bottom: 10px;
}

.step-card p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.docs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.doc-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    border: 1px solid #E8E2DE;
    text-align: center;
}

.doc-card i {
    width: 48px;
    height: 48px;
    color: #D35400;
    margin-bottom: 15px;
}

.doc-card h4 {
    font-size: 18px;
    color: #241611;
    margin-bottom: 10px;
}

.doc-card p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.tiers-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.tier-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    border: 2px solid #E8E2DE;
    text-align: center;
}

.tier-badge {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.tier-badge i {
    width: 28px;
    height: 28px;
    color: white;
}

.tier-badge.gray {
    background: #6D5D57;
}

.tier-badge.blue {
    background: #3498DB;
}

.tier-badge.gold {
    background: linear-gradient(135deg, #F1C40F 0%, #E67E22 100%);
}

.tier-card h3 {
    font-size: 22px;
    color: #241611;
    margin-bottom: 10px;
}

.tier-req {
    color: #6D5D57;
    font-size: 14px;
}

.cta-box {
    background: linear-gradient(135deg, #241611 0%, #3D2B1F 100%);
    color: white;
    padding: 50px;
    border-radius: 20px;
    text-align: center;
    margin-top: 50px;
}

.cta-box h3 {
    font-size: 28px;
    margin-bottom: 10px;
}

.cta-box p {
    color: rgba(255,255,255,0.8);
    margin-bottom: 25px;
    font-size: 16px;
}

.btn-cta {
    display: inline-block;
    background: #D35400;
    color: white;
    padding: 14px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s;
}

.btn-cta:hover {
    background: #b94700;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(211, 84, 0, 0.3);
}

@media (max-width: 768px) {
    .tiers-grid {
        grid-template-columns: 1fr;
    }
    
    .steps-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>lucide.createIcons();</script>
