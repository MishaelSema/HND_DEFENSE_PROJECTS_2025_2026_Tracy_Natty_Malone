<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Base path logic to handle nested directories
$base_path = (strpos($_SERVER['PHP_SELF'], '/client/') !== false || strpos($_SERVER['PHP_SELF'], '/provider/') !== false) ? '../' : '';
?>
<header>
  <div class="container header-container">
    <a href="<?php echo $base_path; ?>index.php" class="logo">
      <img src="<?php echo $base_path; ?>assets/img/logoi.png" alt="Allo_Service Logo">
      <span>ALLO_SERVICE</span>
    </a>
    
    <button class="menu-toggle" aria-label="Toggle Menu">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="main-nav">
      <ul>
        <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
        <li><a href="<?php echo $base_path; ?>all_services.php">Services</a></li>
        <li><a href="<?php echo $base_path; ?>about.php">About</a></li>
        <li><a href="<?php echo $base_path; ?>contact.php">Contact</a></li>
        <li class="nav-cta">
          <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
              <a href="<?php echo $base_path; ?>admin/users.php" class="btn btn-primary">Admin Panel</a>
              <a href="<?php echo $base_path; ?>admin/logout.php" class="btn-logout">Logout</a>
          <?php elseif (isset($_SESSION['provider_id'])): ?>
              <a href="<?php echo $base_path; ?>provider/provider_profile.php" class="btn btn-primary">My Dashboard</a>
              <a href="<?php echo $base_path; ?>logout.php" class="btn-logout">Logout</a>
          <?php elseif (isset($_SESSION['client_id'])): ?>
              <a href="<?php echo $base_path; ?>client/client_dashboard.php" class="btn btn-primary">My Dashboard</a>
              <a href="<?php echo $base_path; ?>client/logout.php" class="btn-logout">Logout</a>
          <?php else: ?>
              <a href="<?php echo $base_path; ?>choose_role.php" class="btn btn-primary">Get Started</a>
          <?php endif; ?>
        </li>
      </ul>
    </nav>
  </div>
</header>

<style>
/* Global Header Styles */
header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 15px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 1000;
  height: 80px;
  width: 100%;
}

.header-container {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 800;
  font-size: 1.5rem;
  color: #007A5E;
  text-decoration: none;
  flex-shrink: 0;
}

.logo img {
  height: 45px;
  width: auto;
}

.main-nav ul {
  list-style: none;
  display: flex;
  align-items: center;
  gap: 30px;
  margin: 0;
  padding: 0;
}

.main-nav ul li a {
  text-decoration: none;
  color: #1e293b;
  font-weight: 600;
  font-size: 1rem;
  transition: 0.3s;
}

.main-nav ul li a:hover {
  color: #007A5E;
}

.btn-primary {
  background: #007A5E;
  color: white !important;
  padding: 10px 25px;
  border-radius: 50px;
  font-weight: 700;
  transition: 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: max-content;
  text-decoration: none;
}

.btn-logout {
  background: #CE1126;
  color: white !important;
  padding: 10px 25px;
  border-radius: 50px;
  font-weight: 700;
  text-decoration: none;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.3s;
}

.nav-cta {
  display: flex;
  gap: 10px;
  align-items: center;
}

/* Mobile Menu Toggle */
.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 6px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  z-index: 1001;
}

.menu-toggle span {
  display: block;
  width: 28px;
  height: 3px;
  background: #007A5E;
  border-radius: 2px;
  transition: 0.3s;
}

/* Responsive Styles */
@media (max-width: 991px) {
  .main-nav {
    position: fixed;
    top: 80px;
    left: -100%;
    width: 100%;
    height: calc(100vh - 80px);
    background: white;
    transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 999;
    overflow-y: auto;
  }

  .main-nav.active {
    left: 0;
  }

  .main-nav ul {
    flex-direction: column;
    padding: 40px 20px;
    gap: 20px;
    align-items: center;
    text-align: center;
  }

  .main-nav ul li {
    width: 100%;
  }

  .main-nav ul li a {
    font-size: 1.25rem;
    display: block;
    padding: 10px;
  }

  .menu-toggle {
    display: flex;
  }

  .menu-toggle.active span:nth-child(1) {
    transform: translateY(9px) rotate(45deg);
  }

  .menu-toggle.active span:nth-child(2) {
    opacity: 0;
  }

  .menu-toggle.active span:nth-child(3) {
    transform: translateY(-9px) rotate(-45deg);
  }
  
  .nav-cta {
    margin-top: 20px;
    width: 100%;
    padding: 0 20px;
    box-sizing: border-box;
    flex-direction: column;
    align-items: center;
    gap: 15px;
  }
  
  .nav-cta .btn-primary, .nav-cta .btn-logout {
    width: 100%;
    max-width: 280px;
    padding: 12px 20px;
    font-size: 1.1rem;
    margin: 0 auto;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('.menu-toggle');
  const mainNav = document.querySelector('.main-nav');

  if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', () => {
      menuToggle.classList.toggle('active');
      mainNav.classList.toggle('active');
      document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu when clicking a link
    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.classList.remove('active');
        mainNav.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }
});
</script>
