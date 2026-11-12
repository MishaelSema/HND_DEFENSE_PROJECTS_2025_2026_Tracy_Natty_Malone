<?php
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Seller Guide</h1>
    <p>Everything you need to succeed on AfroTouch</p>
</section>

<section class="guide-section">
    <div class="guide-content">
        <div class="guide-nav">
            <a href="#getting-started" class="active">Getting Started</a>
            <a href="#listing-products">Listing Products</a>
            <a href="#shipping">Shipping</a>
            <a href="#payments">Payments</a>
            <a href="#marketing">Marketing</a>
        </div>
        
        <div class="guide-articles">
            <article id="getting-started">
                <h2>Getting Started</h2>
                <p>Welcome to AfroTouch! This guide will help you set up your seller account and make your first sale.</p>
                
                <div class="step-box">
                    <h3>Step 1: Create Your Account</h3>
                    <p>Sign up for a seller account and complete your profile with your business information.</p>
                </div>
                
                <div class="step-box">
                    <h3>Step 2: Set Up Your Store</h3>
                    <p>Add your store name, description, logo, and banner to make your shop stand out.</p>
                </div>
                
                <div class="step-box">
                    <h3>Step 3: Verify Your Account</h3>
                    <p>Submit your verification documents to build trust with buyers. Verified sellers get more visibility!</p>
                </div>
            </article>
            
            <article id="listing-products">
                <h2>Listing Products</h2>
                <p>Learn how to create compelling product listings that convert.</p>
                
                <h4>Product Photos</h4>
                <ul>
                    <li>Use natural lighting for authentic colors</li>
                    <li>Show multiple angles of each item</li>
                    <li>Include scale reference (e.g., next to a coin)</li>
                    <li>Use white or neutral backgrounds</li>
                </ul>
                
                <h4>Product Descriptions</h4>
                <ul>
                    <li>Tell the story behind each piece</li>
                    <li>List dimensions and materials</li>
                    <li>Mention care instructions</li>
                    <li>Highlight what makes it unique</li>
                </ul>
            </article>
            
            <article id="shipping">
                <h2>Shipping</h2>
                <p>Effective shipping is crucial for customer satisfaction.</p>
                
                <h4>Shipping Options</h4>
                <ul>
                    <li><strong>Standard Shipping:</strong> 7-14 business days</li>
                    <li><strong>Express Shipping:</strong> 3-5 business days</li>
                    <li><strong>Free Shipping:</strong> For orders above 60,000 FCA</li>
                </ul>
                
                <h4>Packing Tips</h4>
                <ul>
                    <li>Use protective packaging materials</li>
                    <li>Include a thank you note</li>
                    <li>Add tracking information</li>
                    <li>Consider gift wrapping options</li>
                </ul>
            </article>
            
            <article id="payments">
                <h2>Payments</h2>
                <p>Understanding your earnings and payment schedule.</p>
                
                <h4>Payment Schedule</h4>
                <p>Payments are processed every Friday for the previous week's orders. Funds take 2-3 business days to reach your account.</p>
                
                <h4>Platform Fees</h4>
                <ul>
                    <li>Free Plan: 15% commission</li>
                    <li>Basic Plan: 10% commission</li>
                    <li>Premium Plan: 5% commission</li>
                </ul>
            </article>
            
            <article id="marketing">
                <h2>Marketing</h2>
                <p>Promote your products and grow your customer base.</p>
                
                <h4>Tips for Success</h4>
                <ul>
                    <li>Share your story - buyers love artisan stories</li>
                    <li>Use social media to showcase your work</li>
                    <li>Offer discounts for repeat customers</li>
                    <li>Respond quickly to customer inquiries</li>
                    <li>Encourage happy customers to leave reviews</li>
                </ul>
            </article>
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

.guide-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.guide-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 3rem;
}

.guide-nav {
    position: sticky;
    top: 100px;
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    height: fit-content;
}

.guide-nav a {
    display: block;
    padding: 0.75rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
    margin-bottom: 0.25rem;
}

.guide-nav a:hover,
.guide-nav a.active {
    background: #FAF7F5;
    color: var(--text-dark);
    font-weight: 600;
}

.guide-articles article {
    margin-bottom: 3rem;
    padding-bottom: 3rem;
    border-bottom: 1px solid var(--border-color);
}

.guide-articles h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.guide-articles h4 {
    font-weight: 600;
    margin: 1.5rem 0 0.75rem;
    font-size: 1.1rem;
}

.guide-articles ul {
    list-style: none;
    padding-left: 0;
}

.guide-articles ul li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    color: var(--text-muted);
}

.guide-articles ul li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: var(--accent-orange);
}

.step-box {
    background: #FAF7F5;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.step-box h3 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.step-box p {
    color: var(--text-muted);
}

@media (max-width: 1024px) {
    .guide-content {
        grid-template-columns: 1fr;
    }
    .guide-nav {
        position: static;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .guide-nav a {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2.5rem;
    }
}
</style>

<?php include 'components/store-layout_end.php'; ?>
