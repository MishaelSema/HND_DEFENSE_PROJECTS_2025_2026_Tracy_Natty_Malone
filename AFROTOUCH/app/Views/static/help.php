<?php
$page_title = 'Help Center';
?>

<div class="static-page">
    <div class="static-container">
        <h1>Help Center</h1>
        
        <p class="intro-text">Find answers to common questions. Can't find what you're looking for? Contact our support team.</p>
        
        <div class="help-categories">
            <div class="help-category">
                <div class="cat-icon">
                    <i data-lucide="shopping-bag"></i>
                </div>
                <h3>For Buyers</h3>
                <ul>
                    <li><a href="/AFROTOUCH/public/faq">How do I place an order?</a></li>
                    <li><a href="/AFROTOUCH/public/faq">What payment methods are available?</a></li>
                    <li><a href="/AFROTOUCH/public/shipping">How does shipping work?</a></li>
                    <li><a href="/AFROTOUCH/public/faq">What is your return policy?</a></li>
                </ul>
            </div>
            
            <div class="help-category">
                <div class="cat-icon">
                    <i data-lucide="store"></i>
                </div>
                <h3>For Sellers</h3>
                <ul>
                    <li><a href="/AFROTOUCH/public/seller-guide">How do I start selling?</a></li>
                    <li><a href="/AFROTOUCH/public/pricing">What are the fees?</a></li>
                    <li><a href="/AFROTOUCH/public/verification">How do I get verified?</a></li>
                    <li><a href="/AFROTOUCH/public/seller-guide">How do I list products?</a></li>
                </ul>
            </div>
            
            <div class="help-category">
                <div class="cat-icon">
                    <i data-lucide="credit-card"></i>
                </div>
                <h3>Payments & Billing</h3>
                <ul>
                    <li><a href="/AFROTOUCH/public/faq">When do I get paid?</a></li>
                    <li><a href="/AFROTOUCH/public/faq">How do payouts work?</a></li>
                    <li><a href="/AFROTOUCH/public/faq">Can I get a refund?</a></li>
                </ul>
            </div>
            
            <div class="help-category">
                <div class="cat-icon">
                    <i data-lucide="headphones"></i>
                </div>
                <h3>Contact Support</h3>
                <ul>
                    <li><a href="/AFROTOUCH/public/contact">Email Support</a></li>
                    <li><a href="/AFROTOUCH/public/contact">Live Chat</a></li>
                </ul>
            </div>
        </div>
        
        <div class="articles-section">
            <h2>Popular Articles</h2>
            <div class="article-list">
                <a href="/AFROTOUCH/public/faq" class="article-item">
                    <div class="article-icon">
                        <i data-lucide="help-circle"></i>
                    </div>
                    <div class="article-content">
                        <h4>Frequently Asked Questions</h4>
                        <p>Answers to the most common questions about AfroTouch</p>
                    </div>
                    <i data-lucide="arrow-right" class="arrow-icon"></i>
                </a>
                <a href="/AFROTOUCH/public/shipping" class="article-item">
                    <div class="article-icon">
                        <i data-lucide="truck"></i>
                    </div>
                    <div class="article-content">
                        <h4>Shipping Information</h4>
                        <p>Learn about shipping times, costs, and tracking</p>
                    </div>
                    <i data-lucide="arrow-right" class="arrow-icon"></i>
                </a>
                <a href="/AFROTOUCH/public/seller-guide" class="article-item">
                    <div class="article-icon">
                        <i data-lucide="book-open"></i>
                    </div>
                    <div class="article-content">
                        <h4>Seller Guide</h4>
                        <p>Everything you need to know about selling on AfroTouch</p>
                    </div>
                    <i data-lucide="arrow-right" class="arrow-icon"></i>
                </a>
            </div>
        </div>
        
        <div class="cta-section">
            <div class="cta-card">
                <i data-lucide="message-circle"></i>
                <h3>Still need help?</h3>
                <p>Our support team is available 24/7 to assist you.</p>
                <a href="/AFROTOUCH/public/contact" class="btn-contact">Contact Us</a>
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

.help-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
    margin-bottom: 60px;
}

.help-category {
    background: white;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #E8E2DE;
    transition: all 0.3s;
}

.help-category:hover {
    border-color: #D35400;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transform: translateY(-4px);
}

.cat-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #FFF8F3 0%, #FEF0E5 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.cat-icon i {
    width: 28px;
    height: 28px;
    color: #D35400;
}

.help-category h3 {
    font-size: 20px;
    color: #241611;
    margin-bottom: 18px;
}

.help-category ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.help-category li {
    margin-bottom: 12px;
}

.help-category a {
    color: #6D5D57;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.2s;
}

.help-category a:hover {
    color: #D35400;
}

.help-category a::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #E8E2DE;
    border-radius: 50%;
    transition: background 0.2s;
}

.help-category a:hover::before {
    background: #D35400;
}

.articles-section {
    margin-bottom: 50px;
}

.articles-section h2 {
    font-size: 28px;
    color: #241611;
    margin-bottom: 25px;
    text-align: center;
}

.article-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.article-item {
    display: flex;
    align-items: center;
    gap: 20px;
    background: white;
    border: 1px solid #E8E2DE;
    border-radius: 16px;
    padding: 20px 25px;
    text-decoration: none;
    transition: all 0.2s;
}

.article-item:hover {
    border-color: #D35400;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.article-icon {
    width: 50px;
    height: 50px;
    background: #FAF7F5;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.article-icon i {
    width: 24px;
    height: 24px;
    color: #D35400;
}

.article-content {
    flex: 1;
}

.article-content h4 {
    font-size: 16px;
    color: #241611;
    margin-bottom: 5px;
}

.article-content p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.arrow-icon {
    width: 20px;
    height: 20px;
    color: #ccc;
}

.cta-section {
    display: flex;
    justify-content: center;
}

.cta-card {
    background: linear-gradient(135deg, #241611 0%, #3D2B1F 100%);
    border-radius: 20px;
    padding: 40px 60px;
    text-align: center;
    color: white;
}

.cta-card i {
    width: 48px;
    height: 48px;
    color: #D35400;
    margin-bottom: 15px;
}

.cta-card h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

.cta-card p {
    color: rgba(255,255,255,0.7);
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
    .article-item {
        flex-direction: column;
        text-align: center;
    }
    
    .arrow-icon {
        display: none;
    }
}
</style>

<script>lucide.createIcons();</script>
