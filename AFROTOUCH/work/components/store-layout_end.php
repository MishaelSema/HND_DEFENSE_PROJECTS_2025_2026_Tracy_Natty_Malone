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
                <li><a href="#">All Categories</a></li>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Best Sellers</a></li>
                <li><a href="#">Verified Artisans</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h4>Sell</h4>
            <ul>
                <li><a href="#">Become a Seller</a></li>
                <li><a href="#">Seller Guide</a></li>
                <li><a href="#">Pricing Plans</a></li>
                <li><a href="#">Verification</a></li>
            </ul>
        </div>
        
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="https://help.afrotouch.com">Help Center</a></li>
                <li><a href="https://contact.afrotouch.com">Contact Us</a></li>
                <li><a href="https://privacy.afrotouch.com">Privacy Policy</a></li>
                <li><a href="https://terms.afrotouch.com">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>© 2026 Afro Touch. All rights reserved. Celebrating African culture worldwide.</p>
    </div>
</footer>

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
