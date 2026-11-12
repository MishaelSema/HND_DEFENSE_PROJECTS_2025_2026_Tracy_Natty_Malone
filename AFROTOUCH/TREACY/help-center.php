<?php
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Help Center</h1>
    <p>Find answers to common questions and get support</p>
</section>

<section class="help-section">
    <div class="search-help">
        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search for help...">
        </div>
    </div>

    <div class="help-categories">
        <h2>Browse by Category</h2>
        <div class="category-grid">
            <a href="help-category.php?cat=getting-started" class="help-category-card">
                <i data-lucide="rocket"></i>
                <h4>Getting Started</h4>
                <p>How to set up your account and start selling</p>
            </a>
            <a href="help-category.php?cat=orders" class="help-category-card">
                <i data-lucide="shopping-cart"></i>
                <h4>Orders & Shipping</h4>
                <p>Managing orders and shipping information</p>
            </a>
            <a href="help-category.php?cat=payments" class="help-category-card">
                <i data-lucide="credit-card"></i>
                <h4>Payments</h4>
                <p>Payment methods, fees, and payouts</p>
            </a>
            <a href="help-category.php?cat=account" class="help-category-card">
                <i data-lucide="user"></i>
                <h4>Account</h4>
                <p>Profile settings and security</p>
            </a>
            <a href="help-category.php?cat=returns" class="help-category-card">
                <i data-lucide="rotate-ccw"></i>
                <h4>Returns & Refunds</h4>
                <p>Return policy and refund process</p>
            </a>
            <a href="help-category.php?cat=verification" class="help-category-card">
                <i data-lucide="badge-check"></i>
                <h4>Verification</h4>
                <p>Getting verified as a seller</p>
            </a>
        </div>
    </div>

    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3>How do I create a seller account?</h3>
                <p>Click on "Sell" in the navigation menu, then "Become a Seller". Fill out the form with your store details and submit. You'll be able to list products once your account is approved.</p>
            </div>
            <div class="faq-item">
                <h3>What payment methods do you accept?</h3>
                <p>We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and mobile money in supported African countries.</p>
            </div>
            <div class="faq-item">
                <h3>How long does shipping take?</h3>
                <p>Shipping times vary by location. Standard shipping typically takes 7-14 business days. Express shipping is 3-5 business days.</p>
            </div>
            <div class="faq-item">
                <h3>Can I return a product?</h3>
                <p>Yes, our return policy allows returns within 14 days of delivery if the item is not as described or arrives damaged.</p>
            </div>
            <div class="faq-item">
                <h3>How do I contact seller support?</h3>
                <p>You can reach our support team at support@afrotouch.com or use the Contact Us form. Premium sellers have access to 24/7 priority support.</p>
            </div>
        </div>
    </div>

    <div class="contact-support">
        <h2>Still Need Help?</h2>
        <p>Our support team is here to assist you</p>
        <a href="contact-us.php" class="btn-contact">Contact Support</a>
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

.help-section {
    max-width: 1000px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.search-help {
    margin-bottom: 4rem;
}

.search-box {
    display: flex;
    align-items: center;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 100px;
    border: 1px solid var(--border-color);
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.search-box i {
    color: var(--text-muted);
    margin-right: 1rem;
}

.search-box input {
    border: none;
    outline: none;
    font-size: 1rem;
    width: 100%;
}

.help-categories {
    margin-bottom: 4rem;
}

.help-categories h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.help-category-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.3s;
}

.help-category-card:hover {
    border-color: var(--accent-orange);
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.help-category-card i {
    color: var(--accent-orange);
    margin-bottom: 1rem;
}

.help-category-card h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.help-category-card p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.faq-section {
    margin-bottom: 4rem;
}

.faq-section h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

.faq-list {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    margin-bottom: 1rem;
}

.faq-item h3 {
    font-weight: 600;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.faq-item p {
    color: var(--text-muted);
    line-height: 1.6;
}

.contact-support {
    background: #2D1F1A;
    color: white;
    padding: 4rem 2rem;
    border-radius: 24px;
    text-align: center;
}

.contact-support h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.contact-support p {
    color: rgba(255,255,255,0.7);
    margin-bottom: 1.5rem;
}

.btn-contact {
    display: inline-block;
    background: var(--accent-orange);
    color: white;
    padding: 1rem 2rem;
    border-radius: 100px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.2s;
}

.btn-contact:hover {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
    .category-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 576px) {
    .category-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
