<?php
$page_title = 'Pricing Plans';
?>

<div class="static-page">
    <div class="static-container">
        <h1>Pricing Plans</h1>
        
        <div class="pricing-intro">
            <p>Choose the plan that works best for your business. All plans include access to our seller dashboard and customer support.</p>
        </div>
        
        <div class="pricing-grid">
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Starter</h3>
                    <div class="price">
                        <span class="amount">Free</span>
                    </div>
                    <p>Perfect for testing the platform</p>
                </div>
                <ul class="pricing-features">
                    <li>Up to 10 products</li>
                    <li>Basic analytics</li>
                    <li>Standard support</li>
                    <li>5% transaction fee</li>
                </ul>
                <a href="/AFROTOUCH/public/register" class="btn-pricing">Get Started</a>
            </div>
            
            <div class="pricing-card featured">
                <div class="badge">Most Popular</div>
                <div class="pricing-header">
                    <h3>Professional</h3>
                    <div class="price">
                        <span class="currency">XAF</span>
                        <span class="amount">5,000</span>
                        <span class="period">/month</span>
                    </div>
                    <p>For growing businesses</p>
                </div>
                <ul class="pricing-features">
                    <li>Unlimited products</li>
                    <li>Advanced analytics</li>
                    <li>Priority support</li>
                    <li>3% transaction fee</li>
                    <li>Featured listings</li>
                </ul>
                <a href="/AFROTOUCH/public/register" class="btn-pricing">Get Started</a>
            </div>
            
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Enterprise</h3>
                    <div class="price">
                        <span class="currency">XAF</span>
                        <span class="amount">15,000</span>
                        <span class="period">/month</span>
                    </div>
                    <p>For large sellers</p>
                </div>
                <ul class="pricing-features">
                    <li>Unlimited products</li>
                    <li>Premium analytics</li>
                    <li>Dedicated support</li>
                    <li>1% transaction fee</li>
                    <li>API access</li>
                    <li>White-label options</li>
                </ul>
                <a href="/AFROTOUCH/public/contact" class="btn-pricing">Contact Us</a>
            </div>
        </div>
    </div>
</div>

<style>
.static-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 20px;
}

.static-container h1 {
    font-size: 36px;
    margin-bottom: 20px;
    text-align: center;
    color: #241611;
}

.pricing-intro {
    text-align: center;
    margin-bottom: 50px;
    color: #6D5D57;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.pricing-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    border: 2px solid #E8E2DE;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}

.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.pricing-card.featured {
    border-color: #D35400;
    background: #FFF8F3;
}

.pricing-card .badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #D35400;
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.pricing-header {
    text-align: center;
    margin-bottom: 25px;
}

.pricing-header h3 {
    font-size: 24px;
    color: #241611;
    margin-bottom: 15px;
}

.pricing-header .price {
    margin-bottom: 10px;
}

.pricing-header .amount {
    font-size: 42px;
    font-weight: 700;
    color: #241611;
}

.pricing-header .currency {
    font-size: 20px;
    vertical-align: top;
}

.pricing-header .period {
    font-size: 14px;
    color: #6D5D57;
}

.pricing-header p {
    color: #6D5D57;
    font-size: 14px;
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 25px;
}

.pricing-features li {
    padding: 10px 0;
    border-bottom: 1px solid #E8E2DE;
    color: #333;
}

.pricing-features li:last-child {
    border-bottom: none;
}

.btn-pricing {
    display: block;
    width: 100%;
    padding: 14px;
    text-align: center;
    background: #D35400;
    color: white;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-pricing:hover {
    background: #b94700;
}

.pricing-card.featured .btn-pricing {
    background: #241611;
}

.pricing-card.featured .btn-pricing:hover {
    background: #3D2B1F;
}
</style>
