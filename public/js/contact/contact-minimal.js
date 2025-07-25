// BookBee Contact Form - Minimal JavaScript
// Tối giản và hiệu quả cho form liên hệ

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  
  if (!form || !submitBtn) return;
  
  // Simple form validation
  form.addEventListener('submit', function(e) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('error');
        isValid = false;
      } else {
        input.classList.remove('error');
        input.classList.add('success');
      }
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput && emailInput.value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value)) {
        emailInput.classList.add('error');
        isValid = false;
      }
    }
    
    // Phone validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput && phoneInput.value) {
      const phoneRegex = /^[0-9+\-\s()]{10,}$/;
      if (!phoneRegex.test(phoneInput.value)) {
        phoneInput.classList.add('error');
        isValid = false;
      }
    }
    
    if (isValid) {
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
      
      // Change button text
      const buttonText = submitBtn.querySelector('.button-text');
      if (buttonText) {
        buttonText.textContent = 'Đang gửi...';
      }
    } else {
      e.preventDefault();
    }
  });
  
  // Remove error state on input
  form.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('input', function() {
      this.classList.remove('error');
      if (this.value.trim()) {
        this.classList.add('success');
      } else {
        this.classList.remove('success');
      }
    });
    
    // Add focus effects
    input.addEventListener('focus', function() {
      this.closest('.form-field')?.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
      this.closest('.form-field')?.classList.remove('focused');
    });
  });
  
  // Phone number formatting
  const phoneInput = document.getElementById('phone');
  if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 0) {
        if (value.length <= 3) {
          value = value;
        } else if (value.length <= 6) {
          value = value.slice(0, 3) + ' ' + value.slice(3);
        } else if (value.length <= 10) {
          value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
        } else {
          value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 10);
        }
      }
      e.target.value = value;
    });
  }
  
  console.log('BookBee Contact Form - Minimal version loaded');
});
