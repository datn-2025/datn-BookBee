// Enhanced Contact Form JavaScript - BookBee Project
// Complete JavaScript functionality extracted from blade template
// Fixes for UX/UI improvements: text visibility and step indicator functionality

// Create floating particles
function createParticles() {
  const particlesContainer = document.getElementById('particles');
  if (!particlesContainer) return;
  
  for (let i = 0; i < 50; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 15 + 's';
    particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
    particlesContainer.appendChild(particle);
  }
}

// Enhanced form submission with success animation
function initializeFormSubmission() {
  const contactForm = document.getElementById('contactForm');
  if (!contactForm) return;
  
  contactForm.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const buttonText = submitBtn.querySelector('.button-text');
    const buttonIcon = submitBtn.querySelector('.button-icon');
    const formCard = document.querySelector('.form-card');
    
    // Add submitting state
    formCard.classList.add('form-submitting');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    // Simulate processing stages
    setTimeout(() => {
      if (submitBtn.classList.contains('loading')) {
        buttonText.textContent = 'Đang xử lý...';
        buttonText.style.opacity = '1';
      }
    }, 800);
    
    setTimeout(() => {
      if (submitBtn.classList.contains('loading')) {
        buttonText.textContent = 'Đang gửi...';
      }
    }, 1500);
  });
}

// Enhanced real-time validation with animations
function initializeRealTimeValidation() {
  document.querySelectorAll('.form-input, .form-textarea').forEach(input => {
    const field = input.closest('.form-field');
    const fieldId = input.id;
    const validationMessage = document.getElementById(fieldId + '-validation');
    const typingIndicator = document.getElementById(fieldId + '-typing');
    
    let typingTimer;
    
    input.addEventListener('input', function() {
      const icon = this.parentElement.querySelector('.input-icon');
      
      // Show typing indicator
      if (typingIndicator) {
        typingIndicator.classList.add('show');
        clearTimeout(typingTimer);
        
        typingTimer = setTimeout(() => {
          typingIndicator.classList.remove('show');
        }, 1000);
      }
      
      // Smart focus enhancement
      field.classList.add('smart-focus');
      
      // Real-time validation
      if (this.checkValidity() && this.value.length > 0) {
        this.style.borderColor = '#10b981';
        this.style.background = 'linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%)';
        if (icon) {
          icon.style.color = '#10b981';
          icon.style.animation = 'completionCelebration 0.6s ease-out';
        }
        
        if (validationMessage) {
          validationMessage.innerHTML = '<i class="fas fa-check"></i> Hợp lệ';
          validationMessage.classList.add('success', 'show');
          validationMessage.classList.remove('error');
        }
      } else if (this.value.length > 0 && !this.checkValidity()) {
        this.style.borderColor = '#ef4444';
        this.style.background = 'linear-gradient(135deg, #fef2f2 0%, #ffffff 100%)';
        if (icon) {
          icon.style.color = '#ef4444';
          icon.style.animation = 'shake 0.5s ease-in-out';
        }
        
        // Show validation error
        if (validationMessage) {
          let errorMsg = 'Vui lòng nhập đúng định dạng';
          if (this.type === 'email') errorMsg = 'Email không hợp lệ';
          if (this.type === 'tel') errorMsg = 'Số điện thoại không hợp lệ';
          
          validationMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errorMsg;
          validationMessage.classList.add('show');
          validationMessage.classList.remove('success');
        }
        
        // Add field shake animation
        field.style.animation = 'fieldErrorShake 0.4s ease-in-out';
      } else {
        this.style.borderColor = '#e5e7eb';
        this.style.background = '#fafbfc';
        if (icon) {
          icon.style.color = '#9ca3af';
          icon.style.animation = 'none';
        }
        
        if (validationMessage) {
          validationMessage.classList.remove('show', 'success');
        }
      }
      
      // Reset animations
      setTimeout(() => {
        field.style.animation = 'none';
        if (icon) icon.style.animation = 'none';
      }, 600);
    });
    
    input.addEventListener('focus', function() {
      field.classList.add('smart-focus');
      
      // Create ripple effect
      const rect = this.getBoundingClientRect();
      const ripple = document.createElement('div');
      ripple.className = 'input-ripple';
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = '50%';
      ripple.style.top = '50%';
      ripple.style.transform = 'translate(-50%, -50%) scale(0)';
      
      this.parentElement.appendChild(ripple);
      
      setTimeout(() => {
        if (ripple.parentElement) {
          ripple.parentElement.removeChild(ripple);
        }
      }, 600);
    });
    
    input.addEventListener('blur', function() {
      field.classList.remove('smart-focus');
      const typingIndicator = document.getElementById(this.id + '-typing');
      if (typingIndicator) {
        typingIndicator.classList.remove('show');
      }
    });
  });
}

// Smart phone number formatting
function initializePhoneFormatting() {
  const phoneInput = document.getElementById('phone');
  if (!phoneInput) return;
  
  phoneInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length >= 10) {
      value = value.substring(0, 10);
      // Vietnamese phone format
      if (value.startsWith('0')) {
        value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
      } else {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
      }
    }
    
    e.target.value = value;
  });
}

// Email autocomplete suggestions
function initializeEmailAutocomplete() {
  const emailSuggestions = ['@gmail.com', '@yahoo.com', '@hotmail.com', '@outlook.com'];
  const emailInput = document.getElementById('email');
  const emailSuggestionsContainer = document.getElementById('email-suggestions');
  
  if (!emailInput || !emailSuggestionsContainer) return;

  emailInput.addEventListener('input', function() {
    const value = this.value;
    const atIndex = value.indexOf('@');
    
    if (atIndex > 0 && atIndex === value.length - 1) {
      // Show suggestions when user types @
      emailSuggestionsContainer.innerHTML = '';
      emailSuggestions.forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.innerHTML = `<i class="fas fa-at"></i> ${value}${suggestion.substring(1)}`;
        item.addEventListener('click', () => {
          emailInput.value = value + suggestion.substring(1);
          emailSuggestionsContainer.classList.remove('show');
          emailInput.focus();
        });
        emailSuggestionsContainer.appendChild(item);
      });
      emailSuggestionsContainer.classList.add('show');
    } else {
      emailSuggestionsContainer.classList.remove('show');
    }
  });

  // Hide suggestions when clicking outside
  document.addEventListener('click', function(e) {
    if (!emailInput.contains(e.target) && !emailSuggestionsContainer.contains(e.target)) {
      emailSuggestionsContainer.classList.remove('show');
    }
  });
}

// Step Indicator System
class StepIndicator {
  constructor() {
    this.currentStep = 1;
    this.totalSteps = 4;
    this.initializeSteps();
  }
  
  initializeSteps() {
    const stepIndicator = document.querySelector('.form-step-indicator');
    if (!stepIndicator) return;
    
    const steps = stepIndicator.querySelectorAll('.step-item');
    steps.forEach((step, index) => {
      step.addEventListener('click', () => this.goToStep(index + 1));
    });
    
    // Set initial state
    this.updateStepIndicator();
  }
  
  goToStep(stepNumber) {
    if (stepNumber < 1 || stepNumber > this.totalSteps) return;
    
    this.currentStep = stepNumber;
    this.updateStepIndicator();
    this.animateStepTransition();
  }
  
  updateStepIndicator() {
    const steps = document.querySelectorAll('.step-item');
    steps.forEach((step, index) => {
      const stepNum = index + 1;
      step.classList.remove('active', 'completed');
      
      if (stepNum === this.currentStep) {
        step.classList.add('active');
      } else if (stepNum < this.currentStep) {
        step.classList.add('completed');
      }
    });
    
    // Update any progress lines if they exist
    const progressLine = document.querySelector('.step-progress-line');
    if (progressLine) {
      const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
      progressLine.style.width = `${progress}%`;
    }
  }
  
  animateStepTransition() {
    // Add visual feedback for step changes
    const activeSteps = document.querySelectorAll('.step-item.active');
    activeSteps.forEach(step => {
      step.style.transform = 'scale(1.15)';
      setTimeout(() => {
        step.style.transform = 'scale(1.1)';
      }, 200);
    });
  }
}

// Smart Step Progression
function initializeSmartStepProgression() {
  const personalInputs = document.querySelectorAll('[data-group="personal"] input');
  const contactInputs = document.querySelectorAll('[data-group="contact"] input');
  const messageInput = document.querySelector('[data-group="message"] textarea');
  
  // Monitor personal info completion
  personalInputs.forEach(input => {
    input.addEventListener('input', () => {
      if (arePersonalFieldsComplete()) {
        window.stepIndicator.goToStep(2);
      }
    });
  });
  
  // Monitor contact info completion
  contactInputs.forEach(input => {
    input.addEventListener('input', () => {
      if (arePersonalFieldsComplete() && areContactFieldsComplete()) {
        window.stepIndicator.goToStep(3);
      }
    });
  });
  
  // Monitor message completion
  if (messageInput) {
    messageInput.addEventListener('input', () => {
      if (arePersonalFieldsComplete() && areContactFieldsComplete() && messageInput.value.trim().length > 10) {
        window.stepIndicator.goToStep(4);
      }
    });
  }
}

function arePersonalFieldsComplete() {
  const nameInput = document.getElementById('name');
  const phoneInput = document.getElementById('phone');
  return nameInput && nameInput.value.trim().length > 2 && 
         phoneInput && phoneInput.value.trim().length > 9;
}

function areContactFieldsComplete() {
  const emailInput = document.getElementById('email');
  const addressInput = document.getElementById('address');
  return emailInput && emailInput.checkValidity() && emailInput.value.trim().length > 0 &&
         addressInput && addressInput.value.trim().length > 5;
}

// Enhanced Input Text Visibility
function enhanceInputVisibility() {
  const inputs = document.querySelectorAll('.form-input, .form-textarea');
  
  inputs.forEach(input => {
    // Force text visibility on all events
    const forceVisibility = () => {
      input.style.color = '#1f2937 !important';
      input.style.webkitTextFillColor = '#1f2937 !important';
      input.style.background = '#ffffff !important';
      input.style.opacity = '1 !important';
    };
    
    input.addEventListener('input', forceVisibility);
    input.addEventListener('focus', forceVisibility);
    input.addEventListener('blur', forceVisibility);
    input.addEventListener('change', forceVisibility);
    
    // Initial visibility setup
    forceVisibility();
    
    // Handle autofill
    setTimeout(forceVisibility, 100);
    setTimeout(forceVisibility, 500);
  });
}

// Performance Optimizations
function optimizePerformance() {
  // Enable GPU acceleration for animations
  const animatedElements = document.querySelectorAll('.gpu-accelerated, .form-card, .submit-button');
  animatedElements.forEach(element => {
    element.style.transform = 'translateZ(0)';
    element.style.willChange = 'transform, opacity';
  });
  
  // Optimize input performance
  const inputs = document.querySelectorAll('.optimized-input');
  inputs.forEach(input => {
    input.style.willChange = 'border-color, background-color';
  });
  
  // Debounce resize events
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      // Recalculate layouts if needed
      console.log('Layout recalculated');
    }, 100);
  });
}

// Form Validation Enhancements
function enhanceFormValidation() {
  const form = document.getElementById('contactForm');
  if (!form) return;
  
  const inputs = form.querySelectorAll('input[required], textarea[required]');
  
  inputs.forEach(input => {
    input.addEventListener('blur', () => {
      validateField(input);
    });
    
    input.addEventListener('input', () => {
      // Clear previous validation styles
      input.classList.remove('error', 'success');
      
      // Real-time validation
      if (input.value.trim()) {
        validateField(input);
      }
    });
  });
}

function validateField(input) {
  const isValid = input.checkValidity() && input.value.trim().length > 0;
  
  input.classList.remove('error', 'success');
  
  if (isValid) {
    input.classList.add('success');
    input.style.borderColor = '#10b981';
  } else {
    input.classList.add('error');
    input.style.borderColor = '#ef4444';
    
    // Shake animation for errors
    input.style.animation = 'inputShake 0.4s ease-in-out';
    setTimeout(() => {
      input.style.animation = '';
    }, 400);
  }
}

// Initialize all systems when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Initialize step indicator system
  window.stepIndicator = new StepIndicator();
  
  // Add smart step progression
  initializeSmartStepProgression();
  
  // Enhance input text visibility
  enhanceInputVisibility();
  
  // Add performance optimizations
  optimizePerformance();
  
  // Enhance form validation
  enhanceFormValidation();
  
  console.log('BookBee Contact Form Enhanced - All systems initialized');
});

// Export for external use
window.BookBeeContact = {
  StepIndicator,
  enhanceInputVisibility,
  initializeSmartStepProgression,
  arePersonalFieldsComplete,
  areContactFieldsComplete
};
