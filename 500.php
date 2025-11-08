<?php
$page_title = "Server Error - AluMaster Aluminum System";
$page_description = "We're experiencing technical difficulties. Please try again later or contact us for assistance.";

include 'includes/header.php';
?>

<!-- 500 Error Page -->
<section class="error-page">
    <div class="container">
        <div class="error-content">
            <div class="error-illustration">
                <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" stroke="#e2e8f0" stroke-width="2" fill="none"/>
                    <path d="M100 60V120M100 140V150" stroke="#f56565" stroke-width="4" stroke-linecap="round"/>
                    <text x="100" y="180" text-anchor="middle" fill="#718096" font-size="24" font-weight="bold">500</text>
                </svg>
            </div>
            
            <div class="error-text">
                <h1>Server Error</h1>
                <p>We're experiencing technical difficulties right now. Our team has been notified and is working to resolve the issue.</p>
                
                <div class="error-suggestions">
                    <h3>What you can do:</h3>
                    <ul>
                        <li>Try refreshing the page in a few minutes</li>
                        <li>Go back to the previous page</li>
                        <li>Visit our homepage</li>
                        <li>Contact us if the problem persists</li>
                    </ul>
                </div>
                
                <div class="error-actions">
                    <button onclick="location.reload()" class="btn btn-primary">Try Again</button>
                    <a href="index.php" class="btn btn-secondary">Go Home</a>
                    <a href="contact.php" class="btn btn-outline">Contact Support</a>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="error-contact">
            <h3>Need Immediate Help?</h3>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h4>Call Us</h4>
                        <a href="tel:+233541737575">+233-541-737-575</a>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h4>Email Us</h4>
                        <a href="mailto:contact@alumastergh.com">contact@alumastergh.com</a>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h4>WhatsApp</h4>
                        <a href="https://wa.me/233541737575" target="_blank">Chat with us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.error-page {
    padding: 80px 0;
    min-height: 60vh;
}

.error-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    margin-bottom: 60px;
}

.error-illustration {
    text-align: center;
}

.error-text h1 {
    font-size: 3rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.error-text p {
    font-size: 1.125rem;
    color: #718096;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-suggestions {
    margin-bottom: 2rem;
}

.error-suggestions h3 {
    font-size: 1.25rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.error-suggestions ul {
    color: #718096;
    padding-left: 1.5rem;
}

.error-suggestions li {
    margin-bottom: 0.5rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.error-contact {
    border-top: 1px solid #e2e8f0;
    padding-top: 40px;
    text-align: center;
}

.error-contact h3 {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 2rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background-color: #f7fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.contact-icon {
    width: 48px;
    height: 48px;
    background-color: #3182ce;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-info h4 {
    font-size: 1rem;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.contact-info a {
    color: #3182ce;
    text-decoration: none;
    font-weight: 500;
}

.contact-info a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .error-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .error-text h1 {
        font-size: 2rem;
    }
    
    .error-actions {
        justify-content: center;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-item {
        justify-content: center;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>