<div class="auth-container">
    <div class="auth-header">
        <h2>Create Account</h2>
        <p>Join AfroTouch today</p>
    </div>

    <div class="role-selector">
        <button type="button" class="role-btn active" data-role="buyer">
            <i data-lucide="shopping-bag"></i>
            <span>Buy</span>
            <small>Browse & purchase products</small>
        </button>
        <button type="button" class="role-btn" data-role="seller">
            <i data-lucide="store"></i>
            <span>Sell</span>
            <small>Start your own shop</small>
        </button>
    </div>

    <div id="buyer-form" class="auth-form-container">
        <form action="/AFROTOUCH/public/register" method="POST" class="auth-form">
            <input type="hidden" name="role" value="buyer">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required placeholder="+237 6XX XXX XXX">
            </div>
            
            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country" required>
                    <option value="">Select your country</option>
                    <option value="Cameroon">Cameroon</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Kenya">Kenya</option>
                    <option value="South Africa">South Africa</option>
                    <option value="Ivory Coast">Ivory Coast</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Togo">Togo</option>
                    <option value="DR Congo">DR Congo</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Create a password" minlength="6">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Create Buyer Account</button>
        </form>
    </div>

    <div id="seller-form" class="auth-form-container" style="display: none;">
        <div class="step-indicator">
            <span class="step active" data-step="1">1</span>
            <span class="step-line"></span>
            <span class="step" data-step="2">2</span>
            <span class="step-line"></span>
            <span class="step" data-step="3">3</span>
        </div>
        
        <form action="/AFROTOUCH/public/register" method="POST" class="auth-form" id="sellerForm">
            <input type="hidden" name="role" value="seller">
            
            <!-- Step 1: Account -->
            <div class="form-step" data-step="1">
                <h3>Step 1: Account Details</h3>
                <div class="form-group">
                    <label for="seller-email">Email</label>
                    <input type="email" id="seller-email" name="email" required placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label for="seller-password">Password</label>
                    <input type="password" id="seller-password" name="password" required placeholder="Create a password" minlength="6">
                </div>
                <button type="button" class="btn btn-primary btn-next">Next</button>
            </div>
            
            <!-- Step 2: Personal Info -->
            <div class="form-step" data-step="2" style="display: none;">
                <h3>Step 2: Personal Information</h3>
                <div class="form-group">
                    <label for="seller-name">Full Name</label>
                    <input type="text" id="seller-name" name="name" required placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label for="seller-phone">Phone Number</label>
                    <input type="tel" id="seller-phone" name="phone" required placeholder="+237 6XX XXX XXX">
                </div>
                <div class="form-group">
                    <label for="seller-country">Country</label>
                    <select id="seller-country" name="country" required>
                        <option value="">Select your country</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Kenya">Kenya</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Ivory Coast">Ivory Coast</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Togo">Togo</option>
                        <option value="DR Congo">DR Congo</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="seller-address">Address</label>
                    <textarea id="seller-address" name="address" rows="2" placeholder="Your address"></textarea>
                </div>
                <button type="button" class="btn btn-secondary btn-prev">Back</button>
                <button type="button" class="btn btn-primary btn-next">Next</button>
            </div>
            
            <!-- Step 3: Shop Info -->
            <div class="form-step" data-step="3" style="display: none;">
                <h3>Step 3: Shop Details</h3>
                <div class="form-group">
                    <label for="shop-name">Shop Name</label>
                    <input type="text" id="shop-name" name="shop_name" required placeholder="Your shop name">
                </div>
                <div class="form-group">
                    <label for="shop-location">Location</label>
                    <input type="text" id="shop-location" name="shop_location" placeholder="e.g., Douala, Cameroon">
                </div>
                <div class="form-group">
                    <label for="shop-description">Shop Description</label>
                    <textarea id="shop-description" name="shop_description" rows="3" placeholder="Tell customers about your shop"></textarea>
                </div>
                <button type="button" class="btn btn-secondary btn-prev">Back</button>
                <button type="submit" class="btn btn-primary">Create Seller Account</button>
            </div>
        </form>
    </div>
    
    <p class="auth-link">Already have an account? <a href="/AFROTOUCH/public/login">Login</a></p>
</div>

<style>
.auth-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 40px 20px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h2 {
    font-size: 28px;
    margin: 0 0 8px;
}

.auth-header p {
    color: #6D5D57;
    margin: 0;
}

.role-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 30px;
}

.role-btn {
    background: white;
    border: 2px solid #E8E2DE;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}

.role-btn:hover {
    border-color: #D35400;
}

.role-btn.active {
    border-color: #D35400;
    background: #FFF4E5;
}

.role-btn i {
    width: 32px;
    height: 32px;
    color: #D35400;
    margin-bottom: 10px;
}

.role-btn span {
    display: block;
    font-weight: 600;
    font-size: 16px;
}

.role-btn small {
    display: block;
    font-size: 12px;
    color: #6D5D57;
    margin-top: 5px;
}

.auth-form .form-group {
    margin-bottom: 20px;
}

.auth-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.auth-form input,
.auth-form select,
.auth-form textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #E8E2DE;
    border-radius: 8px;
    font-size: 14px;
}

.auth-form input:focus,
.auth-form select:focus,
.auth-form textarea:focus {
    outline: none;
    border-color: #D35400;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: #D35400;
    color: white;
}

.btn-primary:hover {
    background: #b94700;
}

.btn-secondary {
    background: #f5f5f5;
    color: #333;
}

.btn-block {
    width: 100%;
}

.btn-prev {
    margin-right: 10px;
}

.step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30px;
}

.step {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #E8E2DE;
    color: #6D5D57;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.step.active {
    background: #D35400;
    color: white;
}

.step-line {
    width: 40px;
    height: 2px;
    background: #E8E2DE;
    margin: 0 10px;
}

.form-step h3 {
    font-size: 18px;
    margin-bottom: 20px;
}

.auth-link {
    text-align: center;
    margin-top: 20px;
    color: #6D5D57;
}

.auth-link a {
    color: #D35400;
    font-weight: 600;
}
</style>

<script>
lucide.createIcons();

const urlParams = new URLSearchParams(window.location.search);
const defaultRole = urlParams.get('role');

if (defaultRole === 'seller') {
    const sellerBtn = document.querySelector('.role-btn[data-role="seller"]');
    if (sellerBtn) {
        sellerBtn.click();
    }
}

const roleBtns = document.querySelectorAll('.role-btn');
const buyerForm = document.getElementById('buyer-form');
const sellerForm = document.getElementById('seller-form');

roleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        roleBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        const role = btn.dataset.role;
        if (role === 'buyer') {
            buyerForm.style.display = 'block';
            sellerForm.style.display = 'none';
        } else {
            buyerForm.style.display = 'none';
            sellerForm.style.display = 'block';
        }
    });
});

const nextBtns = document.querySelectorAll('.btn-next');
const prevBtns = document.querySelectorAll('.btn-prev');

nextBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const currentStep = btn.closest('.form-step');
        const stepNum = currentStep.dataset.step;
        
        const inputs = currentStep.querySelectorAll('input[required], select[required]');
        let valid = true;
        inputs.forEach(input => {
            if (!input.value) {
                input.style.borderColor = '#C62828';
                valid = false;
            } else {
                input.style.borderColor = '#E8E2DE';
            }
        });
        
        if (valid && stepNum < 3) {
            currentStep.style.display = 'none';
            currentStep.nextElementSibling.style.display = 'block';
            updateStepIndicator(parseInt(stepNum) + 1);
        }
    });
});

prevBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const currentStep = btn.closest('.form-step');
        const stepNum = currentStep.dataset.step;
        
        currentStep.style.display = 'none';
        currentStep.previousElementSibling.style.display = 'block';
        updateStepIndicator(parseInt(stepNum) - 1);
    });
});

function updateStepIndicator(step) {
    document.querySelectorAll('.step').forEach(s => {
        s.classList.remove('active');
        if (s.dataset.step <= step) {
            s.classList.add('active');
        }
    });
}
</script>
