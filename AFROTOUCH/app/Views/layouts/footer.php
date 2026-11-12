<footer class="main-footer">
    <div class="footer-grid">
        <div class="footer-col">
            <h4 class="footer-brand">AFRO TOUCH</h4>
            <p>Discover authentic African craftsmanship. We connect artisans across the continent with buyers worldwide.</p>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="/AFROTOUCH/public/products">All Products</a></li>
                <li><a href="/AFROTOUCH/public/shops">Sellers</a></li>
                <li><a href="/AFROTOUCH/public/products?category=Clothing">Clothing</a></li>
                <li><a href="/AFROTOUCH/public/products?category=Jewelry">Jewelry</a></li>
                <li><a href="/AFROTOUCH/public/products?category=Art">Art</a></li>
                <li><a href="/AFROTOUCH/public/orders">My Orders</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Sell</h4>
            <ul>
                <li><a href="/AFROTOUCH/public/register">Start Selling</a></li>
                <li><a href="/AFROTOUCH/public/dashboard">Seller Dashboard</a></li>
                <li><a href="/AFROTOUCH/public/terms">Terms of Service</a></li>
                <li><a href="/AFROTOUCH/public/privacy">Privacy Policy</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="/AFROTOUCH/public/contact">Contact Us</a></li>
                <li><a href="/AFROTOUCH/public/faq">FAQ</a></li>
                <li><a href="/AFROTOUCH/public/shipping">Shipping Info</a></li>
                <li><a href="/AFROTOUCH/public/returns">Returns</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> AfroTouch. All rights reserved.</p>
    </div>
</footer>

<style>
.main-footer {
    background: #241611;
    color: white;
    padding: 60px 30px 30px;
    margin-top: auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto 40px;
}

.footer-brand {
    font-family: 'Instrument Serif', serif;
    font-size: 24px;
    color: #E8A87C;
    margin-bottom: 15px;
}

.footer-col h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #fff;
}

.footer-col ul {
    list-style: none;
}

.footer-col ul li {
    margin-bottom: 12px;
}

.footer-col a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.2s;
}

.footer-col a:hover {
    color: #F1C40F;
}

.footer-col p {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.6;
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 20px;
    text-align: center;
}

.footer-bottom p {
    color: rgba(255,255,255,0.5);
    font-size: 13px;
    margin: 0;
}
</style>
