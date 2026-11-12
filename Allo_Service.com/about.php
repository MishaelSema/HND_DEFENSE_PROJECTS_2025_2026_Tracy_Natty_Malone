<?php
// about.php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
restrictAdminFromStorefront();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
    <style>
        :root { --primary: #007A5E; --secondary: #00B894; --dark: #1e293b; --light: #f8fafc; }
        
        .about-hero {
            background: #f8fafc;
            padding: 80px 20px;
            text-align: center;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            color: var(--dark);
            margin-bottom: 20px;
            font-weight: 800;
        }
        
        .section-header {
            text-align: center;
            margin: 80px 0 50px;
        }
        
        .section-header h2 {
            font-size: 2.2rem;
            color: var(--dark);
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #e2e8f0;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: 2px;
        }

        .timeline-item {
            padding: 10px 40px;
            position: relative;
            background-color: inherit;
            width: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            right: -10px;
            background-color: white;
            border: 4px solid var(--primary);
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }

        .left { left: 8px; }
        .right { left: 50%; }

        .right::after { left: -10px; }

        .timeline-content {
            padding: 20px 30px;
            background-color: white;
            position: relative;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }

        .timeline-content h3 {
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .timeline-content p {
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }

        @media screen and (max-width: 768px) {
            .timeline::after { left: 31px; }
            .timeline-item { width: 100%; padding-left: 70px; padding-right: 25px; }
            .timeline-item::after { left: 21px; }
            .left { left: -20px; }
            .right { left: -20px; }
        }

        /* Values Group */
        .values-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }
        
        .value-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: 0.3s;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }
        
        .value-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }

        .btn-container {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="about-hero">
    <div class="container">
        <h1>Empowering Cameroon's Talent</h1>
        <p style="max-width: 700px; margin: 0 auto; color: #64748b; font-size: 1.2rem;">Allo_Service is more than a marketplace. It's a bridge between trusted local professionals and individuals who need their expertise.</p>
    </div>
</section>

<div class="container">
  
    <div class="section-header">
        <h2>For Clients</h2>
    </div>

    <div class="timeline">
        <div class="timeline-item left">
            <div class="timeline-content">
                <h3>1. Simple Signup</h3>
                <p>Register for free in seconds to access a wide network of local professionals ready to help you.</p>
            </div>
        </div>
        <div class="timeline-item right">
            <div class="timeline-content">
                <h3>2. Smart Search</h3>
                <p>Find exact services like plumbing, cleaning, or repairs using our optimized search filters.</p>
            </div>
        </div>
        <div class="timeline-item left">
            <div class="timeline-content">
                <h3>3. Verified Profiles</h3>
                <p>Review professional bio, photo, location, and authentic community ratings before deciding.</p>
            </div>
        </div>
        <div class="timeline-item right">
            <div class="timeline-content">
                <h3>4. Direct Connection</h3>
                <p>No intermediaries. Message pros directly on WhatsApp to discuss details and set appointments.</p>
            </div>
        </div>
    </div>
    <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
    <div class="btn-container">
        <a href="client/client_login.php" class="btn btn-primary" style="padding: 12px 25px; min-width: 200px;">Login as Client</a>
    </div>
    <?php endif; ?>

    <div class="section-header">
        <h2>For Professionals</h2>
    </div>

    <div class="timeline">
        <div class="timeline-item left">
            <div class="timeline-content">
                <h3>1. Register Pro</h3>
                <p>Create your professional profile. Showcase your expertise, region, and availability to thousands.</p>
            </div>
        </div>
        <div class="timeline-item right">
            <div class="timeline-content">
                <h3>2. Gain Visibility</h3>
                <p>Your profile appears in search results matching your expertise and location automatically.</p>
            </div>
        </div>
        <div class="timeline-item left">
            <div class="timeline-content">
                <h3>3. Grow Business</h3>
                <p>Receive direct inquiries on WhatsApp. No commission is taken—your earnings are 100% yours.</p>
            </div>
        </div>
        <div class="timeline-item right">
            <div class="timeline-content">
                <h3>4. Build Trust</h3>
                <p>Client reviews boost your credibility, helping you stand out and attract higher-quality leads.</p>
            </div>
        </div>
    </div>
    <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
    <div class="btn-container">
        <a href="provider/provider_login.php" class="btn btn-primary" style="padding: 12px 25px; min-width: 200px;">Login as Professional</a>
    </div>
    <?php endif; ?>

    <div class="section-header">
        <h2>Why Choose ALLO_SERVICE?</h2>
    </div>

    <div class="values-group">
        <div class="value-card">
            <h3>Free Forever</h3>
            <p>We take zero commission. Our mission is to connect talent with needs at no cost to either party.</p>
        </div>
        <div class="value-card">
            <h3>Seamless UX</h3>
            <p>A fast, mobile-friendly interface designed for the modern user on the go.</p>
        </div>
        <div class="value-card">
            <h3>Direct Privacy</h3>
            <p>Talk directly with your service provider. Your data and conversations are private to you.</p>
        </div>
        <div class="value-card">
            <h3>Reliability</h3>
            <p>Transparency through reviews and ratings ensures a high-trust environment for everyone.</p>
        </div>
    </div>
    
    <?php if(!isset($_SESSION['client_id']) && !isset($_SESSION['provider_id'])): ?>
    <div class="btn-container" style="margin-bottom: 80px;">
        <a href="choose_role.php" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem; min-width: 240px;">Get Started Now</a>
    </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>