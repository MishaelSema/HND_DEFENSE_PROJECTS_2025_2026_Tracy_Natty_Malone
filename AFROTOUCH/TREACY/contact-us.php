<?php
include 'components/store-layout_start.php';
?>

<section class="page-hero">
    <h1>Contact Us</h1>
    <p>We'd love to hear from you</p>
</section>

<section class="contact-section">
    <div class="contact-grid">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>Have questions? We're here to help. Reach out through any of these channels.</p>
            
            <div class="contact-methods">
                <div class="contact-method">
                    <i data-lucide="mail"></i>
                    <div>
                        <h4>Email Us</h4>
                        <p>support@afrotouch.com</p>
                    </div>
                </div>
                <div class="contact-method">
                    <i data-lucide="phone"></i>
                    <div>
                        <h4>Call Us</h4>
                        <p>+237 600 000 000</p>
                    </div>
                </div>
                <div class="contact-method">
                    <i data-lucide="map-pin"></i>
                    <div>
                        <h4>Visit Us</h4>
                        <p>Douala, Cameroon</p>
                    </div>
                </div>
                <div class="contact-method">
                    <i data-lucide="clock"></i>
                    <div>
                        <h4>Hours</h4>
                        <p>Mon-Fri: 9AM - 6PM WAT</p>
                    </div>
                </div>
            </div>
            
            <div class="social-links">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#"><i data-lucide="facebook"></i></a>
                    <a href="#"><i data-lucide="instagram"></i></a>
                    <a href="#"><i data-lucide="twitter"></i></a>
                    <a href="#"><i data-lucide="youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="contact-form-wrapper">
            <form class="contact-form">
                <h3>Send a Message</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" placeholder="Your name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" placeholder="your@email.com" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Subject *</label>
                    <select required>
                        <option value="">Select a topic</option>
                        <option value="general">General Inquiry</option>
                        <option value="support">Customer Support</option>
                        <option value="seller">Seller Support</option>
                        <option value="partnership">Partnership</option>
                        <option value="press">Press</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea rows="5" placeholder="How can we help you?" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
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

.contact-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 4rem;
}

.contact-info h2 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.contact-info > p {
    color: var(--text-muted);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.contact-method {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.contact-method i {
    color: var(--accent-orange);
    flex-shrink: 0;
}

.contact-method h4 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.contact-method p {
    color: var(--text-muted);
}

.social-links h4 {
    font-weight: 600;
    margin-bottom: 1rem;
}

.social-icons {
    display: flex;
    gap: 1rem;
}

.social-icons a {
    width: 44px;
    height: 44px;
    background: #F8F5F2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-dark);
    transition: all 0.2s;
}

.social-icons a:hover {
    background: var(--accent-orange);
    color: white;
}

.contact-form-wrapper {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    border: 1px solid var(--border-color);
}

.contact-form h3 {
    font-family: var(--font-heading);
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
    font-family: var(--font-body);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--accent-orange);
    outline: none;
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
    .contact-grid {
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
