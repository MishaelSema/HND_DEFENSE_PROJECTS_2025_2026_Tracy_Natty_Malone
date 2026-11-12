<?php
// header.php — adaptive header for AFROTOUCH
?>
<header style="background-color:#2e1b0f; padding:15px 20px; display:flex; justify-content:space-between; align-items:center;">
    
    <!-- Logo section -->
    <div class="logo" style="font-size:1.5rem; font-weight:bold;">
        <a href="index.php" style="text-decoration:none;">
            <!-- AFRO in white, TOUCH in yellow -->
            <span style="color:#ffffff;">AFRO</span><span style="color:#ffcc00;">TOUCH</span>
        </a>
    </div>

    <!-- Icons section -->
    <div class="header-icons" style="display:flex; gap:20px; align-items:center;">
        <?php 
        // Show these icons ONLY if the current page is index.php
        if (isset($page) && $page === "index"): ?>
            <img src="assets/image/cart.png" alt="Cart" style="width:24px;">
            <img src="assets/image/menu.png" alt="Menu" style="width:24px;">
        <?php endif; ?>
    </div>
</header>


