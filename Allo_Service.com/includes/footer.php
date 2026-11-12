<?php
// includes/footer.php
$base_path = (strpos($_SERVER['PHP_SELF'], '/client/') !== false || strpos($_SERVER['PHP_SELF'], '/provider/') !== false) ? '../' : '';
?>
<footer class="main-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="<?php echo $base_path; ?>index.php" class="logo">
                <img src="<?php echo $base_path; ?>assets/img/logoi.png" alt="Logo">
                <span>ALLO_SERVICE</span>
            </a>
            <p>Connecting local talent with home and office needs across Cameroon. Trusted, Verified, Fast.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base_path; ?>all_services.php">Find Services</a></li>
                <li><a href="<?php echo $base_path; ?>about.php">About Us</a></li>
                <li><a href="<?php echo $base_path; ?>contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Get in Touch</h4>
            <p>Email: alloservice@Cameroon.com</p>
            <p>Phone: +237 6 57 97 92 87</p>
            <p>Location: Douala, Cameroon</p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Allo_Service Cameroon. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.main-footer {
    background: #0f172a;
    color: #94a3b8;
    padding: 80px 0 0;
    margin-top: 100px;
    border-top: 1px solid #1e293b;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr;
    gap: 60px;
    padding-bottom: 60px;
}

.footer-brand .logo {
    color: white;
    margin-bottom: 20px;
}

.footer-brand p {
    line-height: 1.7;
    margin-top: 15px;
    max-width: 300px;
}

.footer-links h4, .footer-contact h4 {
    color: white;
    margin-bottom: 25px;
    font-size: 1.2rem;
    font-weight: 700;
}

.footer-links ul {
    list-style: none;
    padding: 0;
}

.footer-links ul li {
    margin-bottom: 12px;
}

.footer-links ul li a {
    color: #94a3b8;
    text-decoration: none;
    transition: 0.3s;
}

.footer-links ul li a:hover {
    color: #00B894;
    padding-left: 5px;
}

.footer-bottom {
    background: #020617;
    padding: 25px 0;
    text-align: center;
    border-top: 1px solid #1e293b;
}

.footer-bottom p {
    font-size: 0.9rem;
    margin: 0;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    .footer-brand .logo {
        justify-content: center;
    }
    .footer-brand p {
        margin-left: auto;
        margin-right: auto;
    }
    .main-footer {
        padding-top: 60px;
    }
}
</style>
