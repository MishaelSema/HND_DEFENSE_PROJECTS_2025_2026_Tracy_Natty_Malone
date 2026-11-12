</main>

<footer class="store-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <h2>AFRO <span>TOUCH</span></h2>
            <p>Africa's premier marketplace for authentic cultural artisan crafts. We connect talented creators with global buyers.</p>
        </div>
        
        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="products-store.php">All Categories</a></li>
                <li><a href="new-arrivals.php">New Arrivals</a></li>
                <li><a href="best-sellers.php">Best Sellers</a></li>
                <li><a href="verified-artisans.php">Verified Artisans</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h4>Sell</h4>
            <ul>
                <li><a href="sell.php">Become a Seller</a></li>
                <li><a href="seller-guide.php">Seller Guide</a></li>
                <li><a href="pricing-plans.php">Pricing Plans</a></li>
                <li><a href="verification.php">Verification</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="help-center.php">Help Center</a></li>
                <li><a href="contact-us.php">Contact Us</a></li>
                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                <li><a href="terms-of-service.php">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2026 Afro Touch. All rights reserved. Celebrating African culture worldwide.</p>
    </div>
</footer>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    // Sticky Header Evolution
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.store-header');
        if (window.scrollY > 20) {
            header.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
            header.style.padding = '1rem 2rem';
        } else {
            header.style.boxShadow = 'none';
            header.style.padding = '1.5rem 2rem';
        }
    });
</script>
</body>
</html>