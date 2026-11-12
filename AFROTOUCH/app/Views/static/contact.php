<div class="static-page">
    <h1>Contact Us</h1>
    <p>Have questions? We'd love to hear from you.</p>
    
    <div class="contact-form">
        <form>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <select name="subject">
                    <option value="">Select a topic</option>
                    <option value="general">General Inquiry</option>
                    <option value="support">Customer Support</option>
                    <option value="selling">Selling on AfroTouch</option>
                    <option value="partnership">Partnership</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Send Message</button>
        </form>
    </div>
    
    <div class="contact-info">
        <h3>Other ways to reach us</h3>
        <p><strong>Email:</strong> support@afrotouch.com</p>
        <p><strong>Phone:</strong> +237 6XX XXX XXX</p>
        <p><strong>Location:</strong> Cameroon, Africa</p>
    </div>
</div>

<style>
.static-page { max-width: 700px; margin: 0 auto; padding: 40px 20px; }
.static-page h1 { font-size: 32px; margin-bottom: 10px; }
.static-page > p { color: #6D5D57; margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%; padding: 12px; border: 1px solid #E8E2DE; border-radius: 8px; font-size: 14px;
}
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #D35400; }
.btn-submit { background: #D35400; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-submit:hover { background: #b94700; }
.contact-info { margin-top: 40px; padding-top: 30px; border-top: 1px solid #E8E2DE; }
.contact-info h3 { margin-bottom: 15px; }
.contact-info p { margin-bottom: 10px; color: #6D5D57; }
</style>
