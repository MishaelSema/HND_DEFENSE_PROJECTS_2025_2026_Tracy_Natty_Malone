<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Start Selling</h1>
    <p>Share your African craft creations with the world</p>
</section>

<section class="sell-section">
    <div class="sell-grid">
        <div class="sell-content">
            <h2>Join Africa's Premier Artisan Marketplace</h2>
            <p>AfroTouch connects talented artisans with buyers from around the globe. Whether you're a seasoned seller or just starting, we provide the tools and support you need to succeed.</p>
            
            <div class="benefits-list">
                <div class="benefit-item">
                    <i data-lucide="users"></i>
                    <div>
                        <h4>Access to Global Buyers</h4>
                        <p>Reach customers from 150+ countries</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i data-lucide="shield-check"></i>
                    <div>
                        <h4>Secure Payments</h4>
                        <p>Safe transaction processing</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i data-lucide="trending-up"></i>
                    <div>
                        <h4>Growth Tools</h4>
                        <p>Analytics and marketing support</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i data-lucide="headphones"></i>
                    <div>
                        <h4>24/7 Support</h4>
                        <p>Dedicated seller assistance</p>
                    </div>
                </div>
            </div>
            
            <div class="sell-actions">
                <a href="become-seller.php" class="btn-primary-lg">Become a Seller</a>
                <a href="pricing-plans.php" class="btn-secondary-lg">View Pricing</a>
            </div>
        </div>
        
        <div class="sell-stats">
            <div class="stat-card">
                <span class="stat-number">50K+</span>
                <span class="stat-label">Active Sellers</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">500K+</span>
                <span class="stat-label">Products Listed</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">6M+</span>
                <span class="stat-label">Paid to Artisans</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">4.9★</span>
                <span class="stat-label">Seller Rating</span>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <h3>Create Account</h3>
            <p>Sign up and complete your seller profile</p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <h3>List Products</h3>
            <p>Add your crafts with photos and descriptions</p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <h3>Start Selling</h3>
            <p>Receive orders and ship to customers</p>
        </div>
        <div class="step-card">
            <div class="step-number">4</div>
            <h3>Get Paid</h3>
            <p>Receive secure payments to your account</p>
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

.sell-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.sell-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 4rem;
    align-items: center;
}

.sell-content h2 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
}

.sell-content > p {
    color: var(--text-muted);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.benefits-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.benefit-item i {
    color: var(--accent-orange);
    flex-shrink: 0;
}

.benefit-item h4 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.benefit-item p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.sell-actions {
    display: flex;
    gap: 1rem;
}

.btn-primary-lg {
    background: var(--accent-orange);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 100px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s;
}

.btn-primary-lg:hover {
    transform: scale(1.02);
}

.btn-secondary-lg {
    background: transparent;
    color: var(--text-dark);
    border: 2px solid var(--border-color);
    padding: 1rem 2rem;
    border-radius: 100px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-secondary-lg:hover {
    border-color: var(--accent-orange);
    color: var(--accent-orange);
}

.sell-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.stat-card {
    background: #F8F5F2;
    padding: 2rem;
    border-radius: 16px;
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-orange);
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.how-it-works {
    background: #FAF7F5;
    padding: 5rem 2rem;
    text-align: center;
}

.how-it-works h2 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 3rem;
}

.steps-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
}

.step-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    position: relative;
}

.step-number {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 30px;
    background: var(--accent-orange);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.step-card h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.step-card p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

@media (max-width: 1024px) {
    .sell-grid {
        grid-template-columns: 1fr;
    }
    .sell-stats {
        grid-template-columns: repeat(4, 1fr);
    }
    .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
    .benefits-list {
        grid-template-columns: 1fr;
    }
    .sell-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .steps-grid {
        grid-template-columns: 1fr;
    }
    .sell-actions {
        flex-direction: column;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
