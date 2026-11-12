    </div>
</main>
<div class="overlay" id="sidebarOverlay"></div>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();
    
    // Mobile Sidebar Toggle
    const burger = document.getElementById('burgerToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (burger) {
        burger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
    }
    
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });

    // Sticky Header Evolution
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.top-nav');
        if (window.scrollY > 20) {
            header.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
        } else {
            header.style.boxShadow = 'none';
        }
    });
</script>
</body>
</html>
