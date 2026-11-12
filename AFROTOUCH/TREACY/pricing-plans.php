<?php
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Pricing Plans</h1>
    <p>Choose the plan that fits your selling needs</p>
</section>

<section class="pricing-section">
    <div class="pricing-grid">
        <div class="pricing-card">
            <div class="plan-header">
                <h3>Free</h3>
                <div class="plan-price">
                    <span class="amount">0</span>
                    <span class="period">/month</span>
                </div>
                <p>Perfect for trying out the platform</p>
            </div>
            <ul class="plan-features">
                <li><i data-lucide="check"></i> 5 products maximum</li>
                <li><i data-lucide="check"></i> Basic listing</li>
                <li><i data-lucide="check"></i> Standard support</li>
                <li><i data-lucide="check"></i> 15% commission on sales</li>
            </ul>
            <a href="become-seller.php?plan=free" class="btn-plan">Get Started</a>
        </div>
        
        <div class="pricing-card featured">
            <div class="featured-badge">Most Popular</div>
            <div class="plan-header">
                <h3>Basic</h3>
                <div class="plan-price">
                    <span class="amount">5,994</span>
                    <span class="period">/month</span>
                </div>
                <p>For growing your business</p>
            </div>
            <ul class="plan-features">
                <li><i data-lucide="check"></i> 50 products</li>
                <li><i data-lucide="check"></i> Priority listing</li>
                <li><i data-lucide="check"></i> Email support</li>
                <li><i data-lucide="check"></i> Basic analytics</li>
                <li><i data-lucide="check"></i> 10% commission on sales</li>
            </ul>
            <a href="become-seller.php?plan=basic" class="btn-plan btn-featured">Get Started</a>
        </div>
        
        <div class="pricing-card">
            <div class="plan-header">
                <h3>Premium</h3>
                <div class="plan-price">
                    <span class="amount">17,994</span>
                    <span class="period">/month</span>
                </div>
                <p>For serious artisans</p>
            </div>
            <ul class="plan-features">
                <li><i data-lucide="check"></i> Unlimited products</li>
                <li><i data-lucide="check"></i> Featured listing</li>
                <li><i data-lucide="check"></i> 24/7 priority support</li>
                <li><i data-lucide="check"></i> Advanced analytics</li>
                <li><i data-lucide="check"></i> Custom storefront</li>
                <li><i data-lucide="check"></i> 5% commission on sales</li>
            </ul>
            <a href="become-seller.php?plan=premium" class="btn-plan">Get Started</a>
        </div>
    </div>
    
    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Can I switch plans later?</h4>
                <p>Yes! You can upgrade or downgrade your plan at any time. Changes take effect at the start of your next billing cycle.</p>
            </div>
            <div class="faq-item">
                <h4>Is there a free trial?</h4>
                <p>The Free plan lets you try the platform indefinitely. You can upgrade to a paid plan anytime.</p>
            </div>
            <div class="faq-item">
                <h4>How do commissions work?</h4>
                <p>We deduct the commission from each sale before calculating your earnings. The commission rate depends on your plan.</p>
            </div>
            <div class="faq-item">
                <h4>What payment methods do you accept?</h4>
                <p>We accept all major credit cards, PayPal, and mobile money in supported regions.</p>
            </div>
        </div>
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

.pricing-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 5rem;
}

.pricing-card {
    background: white;
    border-radius: 24px;
    padding: 2.5rem;
    border: 1px solid var(--border-color);
    position: relative;
    transition: all 0.3s;
}

.pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.pricing-card.featured {
    border: 2px solid var(--accent-orange);
    transform: scale(1.05);
}

.pricing-card.featured:hover {
    transform: scale(1.05) translateY(-8px);
}

.featured-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--accent-orange);
    color: white;
    padding: 6px 16px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-header h3 {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.plan-price {
    margin-bottom: 0.5rem;
}

.plan-price .currency {
    font-size: 1.5rem;
    vertical-align: top;
}

.plan-price .amount {
    font-size: 3.5rem;
    font-weight: 700;
}

.plan-price .period {
    color: var(--text-muted);
}

.plan-header p {
    color: var(--text-muted);
}

.plan-features {
    list-style: none;
    margin-bottom: 2rem;
}

.plan-features li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f5f5f5;
}

.plan-features li:last-child {
    border-bottom: none;
}

.plan-features li i {
    color: #188038;
}

.btn-plan {
    display: block;
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    border: 2px solid var(--border-color);
    color: var(--text-dark);
}

.btn-plan:hover {
    border-color: var(--accent-orange);
    color: var(--accent-orange);
}

.btn-plan.btn-featured {
    background: var(--accent-orange);
    border-color: var(--accent-orange);
    color: white;
}

.btn-plan.btn-featured:hover {
    background: #c44d00;
}

.faq-section h2 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 3rem;
}

.faq-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.faq-item {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
}

.faq-item h4 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.faq-item p {
    color: var(--text-muted);
    line-height: 1.6;
}

@media (max-width: 1024px) {
    .pricing-grid {
        grid-template-columns: 1fr;
        max-width: 500px;
        margin: 0 auto 5rem;
    }
    .pricing-card.featured {
        transform: none;
    }
    .pricing-card.featured:hover {
        transform: translateY(-8px);
    }
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
    .faq-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
