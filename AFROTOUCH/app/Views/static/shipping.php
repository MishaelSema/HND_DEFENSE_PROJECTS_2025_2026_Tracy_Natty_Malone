<?php
$page_title = 'Shipping Information';
?>

<div class="static-page">
    <div class="static-container">
        <h1>Shipping Information</h1>
        
        <p class="intro-text">We want to make sure your products reach you safely and on time. Here's everything you need to know about shipping on AfroTouch.</p>
        
        <div class="shipping-partners">
            <h2>Shipping Partners</h2>
            <p class="section-desc">We work with trusted delivery partners across Africa to ensure your orders arrive safely:</p>
            <div class="partners-grid">
                <div class="partner-card">
                    <div class="partner-logo">
                        <i data-lucide="package"></i>
                    </div>
                    <h4>UPS</h4>
                    <p>International and express shipping</p>
                </div>
                <div class="partner-card">
                    <div class="partner-logo">
                        <i data-lucide="truck"></i>
                    </div>
                    <h4>DHL</h4>
                    <p>Fast and reliable delivery</p>
                </div>
                <div class="partner-card">
                    <div class="partner-logo">
                        <i data-lucide="map-pin"></i>
                    </div>
                    <h4>Local Couriers</h4>
                    <p>Country-specific delivery services</p>
                </div>
            </div>
        </div>
        
        <div class="shipping-times">
            <h2>Shipping Times & Costs</h2>
            <div class="times-table-wrapper">
                <table class="times-table">
                    <thead>
                        <tr>
                            <th>Shipping Type</th>
                            <th>Estimated Time</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="type-info">
                                    <strong>Standard Shipping</strong>
                                    <span>Best for non-urgent orders</span>
                                </div>
                            </td>
                            <td>5-10 business days</td>
                            <td class="cost">From 2,000 XAF</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="type-info">
                                    <strong>Express Shipping</strong>
                                    <span>Faster delivery</span>
                                </div>
                            </td>
                            <td>2-3 business days</td>
                            <td class="cost">From 5,000 XAF</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="type-info">
                                    <strong>Same Day Delivery</strong>
                                    <span>Available in select cities</span>
                                </div>
                            </td>
                            <td>Same day</td>
                            <td class="cost">From 8,000 XAF</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="tracking-section">
            <h2>Tracking Your Order</h2>
            <p>Once your order is shipped, you'll receive a tracking number via email. You can track your order by:</p>
            <div class="tracking-steps">
                <div class="track-step">
                    <div class="step-icon">
                        <i data-lucide="log-in"></i>
                    </div>
                    <span>Login to your account</span>
                </div>
                <div class="track-arrow">
                    <i data-lucide="arrow-right"></i>
                </div>
                <div class="track-step">
                    <div class="step-icon">
                        <i data-lucide="shopping-bag"></i>
                    </div>
                    <span>Click "My Orders"</span>
                </div>
                <div class="track-arrow">
                    <i data-lucide="arrow-right"></i>
                </div>
                <div class="track-step">
                    <div class="step-icon">
                        <i data-lucide="map-pin"></i>
                    </div>
                    <span>Use tracking link</span>
                </div>
            </div>
        </div>
        
        <div class="restrictions-section">
            <h2>Shipping Restrictions</h2>
            <div class="restrictions-grid">
                <div class="restriction-card">
                    <i data-lucide="alert-triangle"></i>
                    <h4>Special Handling</h4>
                    <p>Some items may require special handling</p>
                </div>
                <div class="restriction-card">
                    <i data-lucide="map"></i>
                    <h4>Remote Areas</h4>
                    <p>Delivery times may vary for remote areas</p>
                </div>
                <div class="restriction-card">
                    <i data-lucide="globe"></i>
                    <h4>Customs Fees</h4>
                    <p>Customs fees may apply for international orders</p>
                </div>
            </div>
        </div>
        
        <div class="free-shipping-box">
            <div class="free-icon">
                <i data-lucide="gift"></i>
            </div>
            <div class="free-content">
                <h3>Free Shipping</h3>
                <p>Enjoy free standard shipping on orders over <strong>50,000 XAF</strong>! This offer is valid for select countries.</p>
            </div>
        </div>
        
        <div class="returns-section">
            <h2>Returns & Refunds</h2>
            <p>If your item arrives damaged or not as described, you can request a return within <strong>7 days</strong> of delivery.</p>
            <a href="/AFROTOUCH/public/faq" class="btn-faq">View Return Policy</a>
        </div>
        
        <div class="cta-box">
            <i data-lucide="help-circle"></i>
            <h3>Still have questions about shipping?</h3>
            <p>Our support team is here to help.</p>
            <a href="/AFROTOUCH/public/contact" class="btn-contact">Contact Us</a>
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
    margin-bottom: 10px;
    text-align: center;
    color: #241611;
}

.intro-text {
    text-align: center;
    font-size: 18px;
    color: #6D5D57;
    margin-bottom: 50px;
}

.shipping-partners, .shipping-times, .tracking-section, .restrictions-section, .returns-section {
    margin-bottom: 50px;
}

.shipping-partners h2, .shipping-times h2, .tracking-section h2, .restrictions-section h2, .returns-section h2 {
    font-size: 28px;
    color: #241611;
    margin-bottom: 15px;
}

.section-desc {
    color: #6D5D57;
    margin-bottom: 25px;
}

.partners-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.partner-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    border: 1px solid #E8E2DE;
    text-align: center;
}

.partner-logo {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #FFF8F3 0%, #FEF0E5 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.partner-logo i {
    width: 32px;
    height: 32px;
    color: #D35400;
}

.partner-card h4 {
    font-size: 18px;
    color: #241611;
    margin-bottom: 5px;
}

.partner-card p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.times-table-wrapper {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #E8E2DE;
}

.times-table {
    width: 100%;
    border-collapse: collapse;
}

.times-table th {
    background: #FAF7F5;
    padding: 18px;
    text-align: left;
    font-weight: 600;
    color: #241611;
}

.times-table td {
    padding: 18px;
    border-bottom: 1px solid #E8E2DE;
}

.times-table tr:last-child td {
    border-bottom: none;
}

.type-info strong {
    display: block;
    color: #241611;
    margin-bottom: 4px;
}

.type-info span {
    font-size: 13px;
    color: #6D5D57;
}

.cost {
    font-weight: 600;
    color: #D35400;
}

.tracking-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.track-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.step-icon {
    width: 60px;
    height: 60px;
    background: white;
    border: 2px solid #E8E2DE;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.step-icon i {
    width: 28px;
    height: 28px;
    color: #D35400;
}

.track-step span {
    font-size: 14px;
    color: #6D5D57;
}

.track-arrow {
    color: #D35400;
}

.restrictions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.restriction-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    border: 1px solid #E8E2DE;
    text-align: center;
}

.restriction-card i {
    width: 40px;
    height: 40px;
    color: #F1C40F;
    margin-bottom: 15px;
}

.restriction-card h4 {
    font-size: 16px;
    color: #241611;
    margin-bottom: 8px;
}

.restriction-card p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.free-shipping-box {
    background: linear-gradient(135deg, #D35400 0%, #E67E22 100%);
    border-radius: 20px;
    padding: 30px 40px;
    display: flex;
    align-items: center;
    gap: 25px;
    margin: 40px 0;
}

.free-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.free-icon i {
    width: 36px;
    height: 36px;
    color: white;
}

.free-content h3 {
    font-size: 24px;
    color: white;
    margin-bottom: 8px;
}

.free-content p {
    color: rgba(255,255,255,0.9);
    margin: 0;
}

.free-content strong {
    font-weight: 700;
}

.btn-faq {
    display: inline-block;
    background: #241611;
    color: white;
    padding: 12px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 15px;
}

.cta-box {
    background: #FAF7F5;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-top: 50px;
}

.cta-box i {
    width: 48px;
    height: 48px;
    color: #D35400;
    margin-bottom: 15px;
}

.cta-box h3 {
    font-size: 24px;
    color: #241611;
    margin-bottom: 10px;
}

.cta-box p {
    color: #6D5D57;
    margin-bottom: 25px;
}

.btn-contact {
    display: inline-block;
    background: #D35400;
    color: white;
    padding: 12px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-contact:hover {
    background: #b94700;
}

@media (max-width: 768px) {
    .partners-grid, .restrictions-grid {
        grid-template-columns: 1fr;
    }
    
    .tracking-steps {
        flex-direction: column;
    }
    
    .track-arrow {
        transform: rotate(90deg);
    }
    
    .free-shipping-box {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>lucide.createIcons();</script>
