let currentStep = 1;
const totalSteps = 6;

function updateProgress() {
    const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.querySelector('.progress-active-line').style.width = `${progressPercent}%`;

    document.querySelectorAll('.step-item').forEach((item, index) => {
        const stepNum = index + 1;
        if (stepNum < currentStep) {
            item.classList.add('completed');
            item.classList.remove('active');
        } else if (stepNum === currentStep) {
            item.classList.add('active');
            item.classList.remove('completed');
        } else {
            item.classList.remove('active', 'completed');
        }
    });
}

function showStep(step) {
    document.querySelectorAll('.form-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`step-${step}-content`).classList.add('active');
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        updateProgress();
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateProgress();
        showStep(currentStep);
    }
}

// Step 1: Fingerprint Simulation
document.getElementById('btn-scan-1').addEventListener('click', function() {
    const btn = this;
    const ring = document.getElementById('ring-1');
    const status = document.getElementById('status-1');
    
    btn.disabled = true;
    btn.innerHTML = 'Scanning...';
    ring.style.display = 'block';
    status.innerHTML = 'Place finger on sensor...';

    setTimeout(() => {
        status.innerHTML = 'Processing biometric data...';
        setTimeout(() => {
            ring.style.display = 'none';
            status.innerHTML = '<i data-lucide="check-circle" style="display:inline-block; vertical-align:middle; margin-right:5px;"></i> Fingerprint captured';
            lucide.createIcons();
            btn.style.display = 'none';
            document.getElementById('btn-next-1').style.display = 'flex';
        }, 2000);
    }, 1500);
});

document.getElementById('btn-next-1').addEventListener('click', nextStep);

// Step 3: Face ID Simulation
document.getElementById('btn-scan-3').addEventListener('click', function() {
    const btn = this;
    const ring = document.getElementById('ring-3');
    const status = document.getElementById('status-3');
    
    btn.disabled = true;
    btn.innerHTML = 'Scanning Face...';
    ring.style.display = 'block';
    status.innerHTML = 'Adjusting focus...';

    setTimeout(() => {
        status.innerHTML = 'Matching features...';
        setTimeout(() => {
            ring.style.display = 'none';
            status.innerHTML = '<i data-lucide="check-circle" style="display:inline-block; vertical-align:middle; margin-right:5px;"></i> Face verified';
            lucide.createIcons();
            btn.style.display = 'none';
            document.getElementById('btn-next-3').style.display = 'flex';
        }, 2500);
    }, 1500);
});

// Step 5: Policy Checkbox
document.getElementById('accept-policy').addEventListener('change', function() {
    document.getElementById('btn-next-5').disabled = !this.checked;
});

// Step 6: Plan Selection
function selectPlan(element) {
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('active');
        card.style.borderColor = 'var(--border-light)';
        card.style.background = 'transparent';
    });
    element.classList.add('active');
    element.style.borderColor = 'var(--primary-color)';
    element.style.background = 'rgba(211, 84, 0, 0.05)';
}

function finishVerification() {
    const card = document.querySelector('.step-card');
    card.innerHTML = `
        <div class="biometric-visual" style="background: #E9F7EF;">
            <i data-lucide="party-popper" class="fingerprint-icon" style="color: #188038;"></i>
        </div>
        <h2>Verification Complete!</h2>
        <p class="subtitle">Your seller account is now being reviewed. You will be notified once approved.</p>
        <div class="button-group" style="justify-content: center;">
            <a href="overview.php" class="btn btn-continue">Go to Dashboard</a>
        </div>
    `;
    lucide.createIcons();
    
    // Update progress to 100% just in case
    document.querySelector('.progress-active-line').style.width = `100%`;
    document.querySelectorAll('.step-item').forEach(item => item.classList.add('completed'));
}

// Global scope functions for onclick handlers
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPlan = selectPlan;
window.finishVerification = finishVerification;
