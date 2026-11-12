<?php
// how_it_works.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>How it Works | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
    <style>
        :root { --primary: #007A5E; --secondary: #00B894; --bg: #f8fafc; --dark: #1e293b; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; margin: 0; }
        
        .how-hero {
            background: #f8fafc;
            padding: 60px 20px;
            text-align: center;
        }

        .how-container {
            max-width: 800px;
            margin: -30px auto 80px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            padding: 50px;
            position: relative;
            z-index: 2;
        }

        h1 { color: var(--dark); font-size: 2.5rem; margin-bottom: 40px; text-align: center; font-weight: 800; }
        
        .step-list { list-style: none; padding: 0; }
        .step-item { margin-bottom: 40px; display: flex; gap: 25px; align-items: flex-start; }
        
        .step-number {
            background: var(--primary);
            color: #fff;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(0, 122, 94, 0.2);
        }
        
        .step-content h3 { margin: 0 0 12px 0; color: var(--primary); font-size: 1.3rem; font-weight: 800; }
        .step-content p { color: #64748b; line-height: 1.7; margin: 0; font-size: 1.05rem; }
        
        .help-box {
            background: #f1f5f9;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            margin-top: 50px;
            border-left: 6px solid var(--primary);
        }
        
        @media (max-width: 600px) {
            .how-container { padding: 30px 20px; }
            .step-item { flex-direction: column; gap: 15px; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="how-hero">
    <div class="container">
        <p style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Guide</p>
    </div>
</section>

<div class="how-container">
    <h1>How it Works</h1>
    <ul class="step-list">
        <li class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
                <h3>Find a Professional</h3>
                <p>Use our search engine or browse by category to find the right professional for your needs. We cover everything from plumbing to hairdressing.</p>
            </div>
        </li>
        <li class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
                <h3>Check the Profile</h3>
                <p>Click on a provider to see their experience, skills, location, and authentic reviews from other clients. Transparency is our priority.</p>
            </div>
        </li>
        <li class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
                <h3>Book Securely</h3>
                <p>Book the service through our secure platform with escrow payment protection. Your money is safe until the job is done.</p>
            </div>
        </li>
        <li class="step-item">
            <div class="step-number">4</div>
            <div class="step-content">
                <h3>Secure Payment</h3>
                <p>Pay securely through the platform. Transparent fees - small platform fee supports the service.</p>
            </div>
        </li>
        <li class="step-item">
            <div class="step-number">5</div>
            <div class="step-content">
                <h3>Leave Feedback</h3>
                <p>Once the job is done, share your experience by leaving a verified review on their profile.</p>
            </div>
        </li>
    </ul>

    <div class="help-box">
        Need assistance? <a href="mailto:support@allo_service.com" style="color:var(--primary); font-weight:800; text-decoration:none;">Contact our Support Team</a>
    </div>
    
    <div style="text-align:center; margin-top: 40px;">
        <a href="all_services.php" class="btn btn-primary" style="padding: 14px 35px;">Back to Services</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>