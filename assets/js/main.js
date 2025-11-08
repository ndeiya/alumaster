// Main JavaScript functionality for AluMaster website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initMobileMenu();
    initScrollEffects();
    initFormValidation();
    initLightbox();
    initCounters();
    initContactForm();
});

// Mobile Menu Toggle
function initMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navbarNav = document.querySelector('.navbar-nav');
    
    if (mobileToggle && navbarNav) {
        // Toggle menu function
        function toggleMenu() {
            const isOpen = navbarNav.classList.contains('mobile-open');
            
            if (isOpen) {
                navbarNav.classList.remove('mobile-open');
                mobileToggle.classList.remove('active');
            } else {
                navbarNav.classList.add('mobile-open');
                mobileToggle.classList.add('active');
            }
        }
        
        // Toggle on button click
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMenu();
        });
        
        // Close button
        const closeButton = document.getElementById('mobileMenuClose');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                toggleMenu();
            });
        }
        
        // Close mobile menu when clicking on links
        const navLinks = navbarNav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navbarNav.classList.remove('mobile-open');
                mobileToggle.classList.remove('active');
            });
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navbarNav.classList.contains('mobile-open')) {
                toggleMenu();
            }
        });
    }
}

// Scroll Effects
function initScrollEffects() {
    const header = document.querySelector('.header');
    
    // Only add scroll effects if header exists (frontend pages)
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            // Skip if href is just '#' or empty
            if (!href || href === '#' || href.length <= 1) {
                return;
            }
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
        
        form.addEventListener('submit', function(e) {
            // Skip validation for contact forms to prevent submission issues
            if (form.classList.contains('contact-form') || form.classList.contains('inquiry-form')) {
                console.log('Skipping JavaScript validation for contact form');
                return true;
            }
            
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields correctly', 'error');
            }
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    
    // Remove existing error styling
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        isValid = false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Phone validation
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!phoneRegex.test(value)) {
            showFieldError(field, 'Please enter a valid phone number');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

// Lightbox for gallery images
function initLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', function() {
            openLightbox(index);
        });
    });
}

function openLightbox(startIndex) {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const images = Array.from(galleryItems).map(item => {
        const img = item.querySelector('.gallery-image');
        return {
            src: img.src,
            alt: img.alt
        };
    });
    
    if (images.length === 0) return;
    
    let currentIndex = startIndex;
    
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox-modal active';
    lightbox.innerHTML = `
        <div class="lightbox-overlay"></div>
        <div class="lightbox-container">
            <button class="lightbox-close" aria-label="Close lightbox">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            ${images.length > 1 ? `
                <button class="lightbox-prev" aria-label="Previous image">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button class="lightbox-next" aria-label="Next image">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            ` : ''}
            <img class="lightbox-image" src="${images[currentIndex].src}" alt="${images[currentIndex].alt}">
        </div>
    `;
    
    document.body.appendChild(lightbox);
    document.body.style.overflow = 'hidden';
    
    const lightboxImage = lightbox.querySelector('.lightbox-image');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    const overlay = lightbox.querySelector('.lightbox-overlay');
    
    function updateImage() {
        lightboxImage.src = images[currentIndex].src;
        lightboxImage.alt = images[currentIndex].alt;
    }
    
    function showPrevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateImage();
    }
    
    function showNextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        updateImage();
    }
    
    function closeLightbox() {
        lightbox.classList.remove('active');
        setTimeout(() => {
            document.body.removeChild(lightbox);
            document.body.style.overflow = '';
        }, 300);
    }
    
    // Event listeners
    closeBtn.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', closeLightbox);
    
    if (prevBtn) prevBtn.addEventListener('click', showPrevImage);
    if (nextBtn) nextBtn.addEventListener('click', showNextImage);
    
    // Keyboard navigation
    document.addEventListener('keydown', function handleKeydown(e) {
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                document.removeEventListener('keydown', handleKeydown);
                break;
            case 'ArrowLeft':
                if (images.length > 1) showPrevImage();
                break;
            case 'ArrowRight':
                if (images.length > 1) showNextImage();
                break;
        }
    });
}

// Counter Animation
function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => {
        observer.observe(counter);
    });
}

function animateCounter(element) {
    const target = parseInt(element.dataset.count);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Contact Form Handling
function initContactForm() {
    const contactForms = document.querySelectorAll('.contact-form, .inquiry-form');
    
    contactForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // The form will submit normally, but we show loading state
            setTimeout(() => {
                if (!this.querySelector('.alert-success')) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            }, 1000);
        });
    });
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icon = type === 'success' ? 
        '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
        '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    
    notification.innerHTML = `
        <div class="notification-icon">${icon}</div>
        <div class="notification-message">${message}</div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add scroll-based animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.service-card, .feature-card, .cta-item');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
}

// Initialize scroll animations
document.addEventListener('DOMContentLoaded', initScrollAnimations);

// Add CSS for notifications
const notificationStyles = `
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-4);
    display: flex;
    align-items: center;
    gap: var(--space-3);
    box-shadow: var(--shadow-xl);
    z-index: 9999;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    max-width: 400px;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification-success {
    border-color: var(--color-primary);
}

.notification-error {
    border-color: var(--color-error);
}

.notification-icon {
    flex-shrink: 0;
    color: var(--color-primary);
}

.notification-error .notification-icon {
    color: var(--color-error);
}

.notification-message {
    flex: 1;
    color: var(--color-text-primary);
    font-size: var(--font-size-sm);
}

.notification-close {
    background: none;
    border: none;
    color: var(--color-text-muted);
    cursor: pointer;
    padding: var(--space-1);
    border-radius: var(--radius-sm);
    transition: all var(--transition-fast);
}

.notification-close:hover {
    background-color: var(--color-bg-tertiary);
    color: var(--color-text-primary);
}

.field-error {
    color: var(--color-error);
    font-size: var(--font-size-xs);
    margin-top: var(--space-1);
}

.form-input.error,
.form-textarea.error,
.form-select.error {
    border-color: var(--color-error);
    box-shadow: 0 0 0 3px rgba(252, 129, 129, 0.1);
}

@media (max-width: 768px) {
    .notification {
        left: 20px;
        right: 20px;
        max-width: none;
    }
}
`;

// Inject notification styles
const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);

// WhatsApp Greeting Functionality
function initWhatsAppGreeting() {
    const greeting = document.getElementById('whatsappGreeting');
    
    if (!greeting) return;
    
    // Check if greeting was already shown in this session
    if (sessionStorage.getItem('whatsappGreetingSeen')) {
        return;
    }
    
    // Show greeting after 5 seconds and keep it visible until user closes it
    setTimeout(() => {
        greeting.classList.add('show');
    }, 5000);
}

function closeWhatsAppGreeting() {
    const greeting = document.getElementById('whatsappGreeting');
    if (greeting) {
        greeting.classList.remove('show');
        sessionStorage.setItem('whatsappGreetingSeen', 'true');
    }
}

// Initialize WhatsApp greeting on page load
document.addEventListener('DOMContentLoaded', initWhatsAppGreeting);
