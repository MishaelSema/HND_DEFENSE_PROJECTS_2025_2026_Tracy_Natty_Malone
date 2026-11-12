<?php
// index.php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
restrictAdminFromStorefront();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ALLO_SERVICE - Home Services Marketplace</title>
  <link rel="stylesheet" href="assets/css/style.css?v=1.1">
  <style>
    /* Hero Section Enhancements */
    .hero {
      background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('assets/img/photo.jpg') center/cover no-repeat;
      height: 700px; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      text-align: center; 
      color: white;
      padding: 0 20px;
    }
    .hero-content h1 { font-size: 4rem; font-weight: 900; margin-bottom: 25px; line-height: 1.1; letter-spacing: -2px; }
    .hero-content p { font-size: 1.5rem; margin-bottom: 40px; opacity: 0.9; max-width: 850px; margin-left: auto; margin-right: auto; line-height: 1.4; }
    
    .hero-btns { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
    .btn-hero { padding: 18px 45px; font-size: 1.1rem; border-radius: 50px; font-weight: 700; }
    .btn-hero.secondary { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px); }
    .btn-hero.secondary:hover { background: rgba(255,255,255,0.2); transform: translateY(-3px); }

    /* General Section Styles */
    section { padding: 100px 20px; }
    .section-tag { color: #007A5E; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; display: block; }
    .section-title { font-size: 3rem; font-weight: 900; color: #1e293b; margin-bottom: 20px; }
    .section-subtitle { font-size: 1.25rem; color: #64748b; max-width: 700px; margin: 0 auto 60px; line-height: 1.6; }

    /* Why Choose Us & Different */
    .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 50px; }
    .feature-card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; transition: 0.3s; }
    .feature-card:hover { transform: translateY(-10px); border-color: #007A5E; }
    .feature-card h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: 15px; color: #1e293b; }
    .feature-card p { color: #64748b; line-height: 1.6; }

    /* How It Works Modern */
    .how-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; position: relative; }
    .step-card { text-align: center; position: relative; z-index: 2; }
    .step-number { width: 60px; height: 60px; background: #007A5E; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; margin: 0 auto 25px; box-shadow: 0 10px 20px rgba(0, 122, 94, 0.3); }
    .step-card h3 { font-size: 1.3rem; font-weight: 800; margin-bottom: 12px; color: #1e293b; }
    .step-card p { color: #64748b; font-size: 0.95rem; }

    /* Provider Section */
    .provider-promo { background: #0f172a; color: white; border-radius: 40px; padding: 80px 60px; display: flex; align-items: center; gap: 60px; overflow: hidden; position: relative; }
    .provider-content { flex: 1; z-index: 2; }
    .provider-image { flex: 1; position: relative; z-index: 2; }
    .provider-image img { width: 100%; border-radius: 24px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
    .check-list { list-style: none; padding: 0; margin: 30px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .check-list li { display: flex; align-items: center; gap: 10px; font-weight: 600; opacity: 0.9; }
    .check-list li::before { content: '✔'; color: #00B894; font-weight: 900; }

    /* Services Modern Grid & Mobile Tiles */
    .services-modern-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .service-v-card {
      position: relative;
      border-radius: 16px;
      overflow: hidden;
      display: flex;
      align-items: flex-end;
      min-height: 200px;
      padding: 24px;
      color: white;
      background-color: #2c3e50; /* Fallback if image fails */
      background-size: cover;
      background-position: center;
      transition: 0.3s;
    }
    .service-v-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(to top, rgba(0,122,94,0.9), rgba(0,0,0,0.1));
      z-index: 1;
    }
    .service-v-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    .service-v-card .card-content {
      position: relative;
      z-index: 2;
    }
    .service-v-card h3 {
      font-size: 1.25rem;
      font-weight: 800;
      margin-bottom: 8px;
    }
    .service-v-card p {
      font-size: 0.9rem;
      opacity: 0.9;
      line-height: 1.4;
      margin: 0;
    }

    @media (max-width: 991px) {
      .provider-promo { flex-direction: column; padding: 60px 30px; text-align: center; }
      .check-list { justify-content: center; }
    }

    @media (max-width: 768px) {
      .hero { height: auto; padding: 120px 20px; }
      .hero-content h1 { font-size: 2.5rem; }
      .hero-content p { font-size: 1.1rem; }
      .section-title { font-size: 2.2rem; }
      .btn-hero { padding: 12px 25px; font-size: 1rem; width: auto; min-width: 200px; max-width: 100%; }
    }

    @media (max-width: 600px) {
      .services-modern-grid {
        grid-template-columns: 1fr !important; /* Force List Stack */
        gap: 15px;
      }
      .service-v-card {
        flex-direction: row;
        min-height: 100px;
        align-items: center;
        padding: 15px;
        text-align: left;
        gap: 20px;
      }
      .service-v-card h3 { 
        font-size: 1.1rem; 
        margin-bottom: 5px; 
      }
      .service-v-card p { 
        font-size: 0.85rem; 
        display: block !important;
        opacity: 0.8;
      }
    }
    
    .service-v-card {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="hero">
  <div class="hero-content">
    <span style="color: #00B894; font-weight: 800; text-transform: uppercase; letter-spacing: 4px; display: inline-block; margin-bottom: 15px;">Cameroon's First marketplace</span>
    <h1>Your Local Experts, <br>Just a Click Away</h1>
    <p>Connecting people who need help with people who know how to get it done. Simple, reliable, and built for everyday life.</p>
    <div class="hero-btns">
        <a href="all_services.php" class="btn btn-primary btn-hero">Find a Service</a>
        <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
            <a href="provider/provider_signup.php" class="btn btn-hero secondary">Offer Your Skills</a>
        <?php endif; ?>
    </div>
    <p style="font-size: 1rem; margin-top: 30px; opacity: 0.7;">No middlemen. No stress. Just real work, done right.</p>
  </div>
</section>

<!-- Why Choose Us -->
<section class="container" style="padding: 100px 20px;">
    <div style="text-align:center;">
        <span class="section-tag">Benefits</span>
        <h2 class="section-title">Why Choose Us</h2>
        <p class="section-subtitle">Finding help shouldn’t feel like a gamble. Our platform is designed to make every connection smooth, transparent, and dependable.</p>
    </div>
    
    <div class="feature-grid">
        <div class="feature-card">
            <h3 style="color: #007A5E; margin-bottom: 15px;">Verified Pros</h3>
            <p>Connect with service providers you can trust, vetted for quality and reliability.</p>
        </div>
        <div class="feature-card">
            <h3 style="color: #007A5E; margin-bottom: 15px;">Direct Chat</h3>
            <p>Easy communication and flexible scheduling that fits your busy day.</p>
        </div>
        <div class="feature-card">
            <h3 style="color: #007A5E; margin-bottom: 15px;">Transparent Pricing</h3>
            <p>Fair and upfront pricing agreed upon before the work even starts.</p>
        </div>
        <div class="feature-card">
            <h3 style="color: #007A5E; margin-bottom: 15px;">Flexible Scheduling</h3>
            <p>Book services at times that suit your lifestyle without the hassle.</p>
        </div>
        <div class="feature-card">
            <h3 style="color: #007A5E; margin-bottom: 15px;">Local Experts</h3>
            <p>Local professionals who understand your specific needs and environment.</p>
        </div>
    </div>
</section>

<!-- How It Works -->
<section style="background: #f8fafc;">
    <div class="container">
        <div style="text-align:center;">
            <span class="section-tag">Process</span>
            <h2 class="section-title">From Need to Solution</h2>
            <p class="section-subtitle">Get things done in just four simple steps. Efficient, fast, and secure.</p>
        </div>
        
        <div class="how-steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Search</h3>
                <p>Browse services or describe exactly what you need.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Connect</h3>
                <p>Chat directly with professionals and discuss your job.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Choose</h3>
                <p>Pick the provider that fits your expectations.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Get It Done</h3>
                <p>Sit back while the work gets handled perfectly.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Fetch Core Services
$core_services = [];
$res_s = $conn->query("SELECT * FROM services ORDER BY id ASC LIMIT 8");
if ($res_s) {
    while ($row = $res_s->fetch_assoc()) $core_services[] = $row;
}
?>
<section class="container" style="padding: 100px 20px;">
  <div style="text-align:center; margin-bottom: 60px;">
    <span class="section-tag">Expertise</span>
    <h2 class="section-title">Our Core Services</h2>
  </div>
  
  <div class="services-modern-grid">
    <?php if (empty($core_services)): ?>
        <p style="text-align: center; color: #64748b; grid-column: 1 / -1;">No core services have been added to the platform yet.</p>
    <?php else: ?>
        <?php foreach ($core_services as $srv): 
            $img = !empty($srv['image_path']) ? htmlspecialchars($srv['image_path']) : 'assets/img/service-placeholder.jpg';
        ?>
        <a href="all_services.php?cat=<?= urlencode($srv['slug']) ?>" class="service-v-card" style="background-image: url('<?= $img ?>'); display: block; text-decoration: none;">
            <div class="card-content">
                <h3><?= htmlspecialchars($srv['name']) ?></h3>
                <p><?= htmlspecialchars($srv['description']) ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- For Providers -->
<section class="container">
    <div class="provider-promo">
        <div class="provider-content">
            <span style="color: #00B894; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; display: block;">Join Our Community</span>
            <h2 style="font-size: 2.8rem; font-weight: 900; line-height: 1.1; margin-bottom: 25px;">Turn Your Skills Into Opportunities</h2>
            <p style="font-size: 1.1rem; opacity: 0.8; line-height: 1.6;">Whether you’re a skilled technician, artisan, or creative professional, we give you visibility and access to people actively looking for your expertise.</p>
            <ul class="check-list">
                <li>Showcase your work</li>
                <li>Connect with real clients</li>
                <li>Build your reputation</li>
                <li>Grow at your own pace</li>
            </ul>
            <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
                <a href="provider/provider_signup.php" class="btn btn-primary" style="padding: 15px 40px; border-radius: 50px;">Apply as a Pro</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- What Makes It Different -->
<section style="background: #f8fafc;">
    <div class="container" style="text-align: center;">
        <span class="section-tag">Community</span>
        <h2 class="section-title">What Makes It Different</h2>
        <p class="section-subtitle">This isn’t just a marketplace. It’s a community where reliable help meets recognized talent.</p>
        
        <div class="feature-grid" style="text-align: left;">
            <div class="feature-card">
                <h3>Reliable Connections</h3>
                <p>People find reliable help without endless searching or second-guessing.</p>
            </div>
            <div class="feature-card">
                <h3>Recognition</h3>
                <p>Skilled workers get recognized and rewarded for what they do best.</p>
            </div>
            <div class="feature-card">
                <h3>Trust-Based</h3>
                <p>Every job completed builds a stronger layer of trust for the next one.</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="container" style="text-align:center; padding: 120px 20px;">
    <h2 style="font-size: 3.5rem; font-weight: 900; color: #1e293b; margin-bottom: 25px;">Ready to Get Started?</h2>
    <p style="font-size: 1.25rem; color: #64748b; margin-bottom: 40px;">Find the help you need or start offering your services today.</p>
    <div class="hero-btns">
        <a href="all_services.php" class="btn btn-primary btn-hero">Explore Services</a>
        <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
            <a href="provider/provider_signup.php" class="btn btn-hero secondary" style="color: #1e293b; border-color: #e2e8f0;">Join as a Provider</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
