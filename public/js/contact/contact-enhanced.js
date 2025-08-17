// BookBee Contact Form - Enhanced JavaScript with Adidas/Minimal Style
// Tăng cường UX với phong cách tối giản và hiện đại

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  
  if (!form || !submitBtn) return;
  
  // Initialize enhanced features
  initSkeletonLoader();
  
  // Skeleton loader functionality
  function initSkeletonLoader() {
    const skeleton = document.getElementById('formSkeleton');
    const actualForm = document.getElementById('contactForm');
    
    if (!skeleton || !actualForm) return;
    
    // Hide skeleton and show form after a delay
    setTimeout(() => {
      skeleton.style.display = 'none';
      actualForm.style.display = 'block';
      actualForm.parentElement.style.display = 'block';
      
      // Add fade-in animation to form
      actualForm.style.opacity = '0';
      actualForm.style.transition = 'opacity 0.5s ease';
      
      setTimeout(() => {
        actualForm.style.opacity = '1';
      }, 100);
      
      // Initialize other features after form is shown
      setTimeout(() => {
        initFormValidation();
        initFormAnimations();
        initProgressIndicator();
        initRippleEffects();
        initScrollAnimations();
        initKeyboardNavigation();
        initTouchGestures();
        initFormPersistence();
        initLoadingStates();
        initBackToTop();
        initPageAnimations();
        initAutoSaveNotification();
      }, 200);
      
    }, 1500); // Show skeleton for 1.5 seconds
  }
  
  console.log('BookBee Contact Form - Enhanced version loaded');
  
  // Form validation with real-time feedback
  function initFormValidation() {
    const inputs = form.querySelectorAll('input, textarea');
    
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      const requiredFields = form.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        if (!validateField(field)) {
          isValid = false;
        }
      });
      
      if (isValid) {
        submitForm();
      } else {
        // Shake form on error
        form.classList.add('shake');
        setTimeout(() => form.classList.remove('shake'), 600);
        
        // Focus first error field
        const firstError = form.querySelector('.error');
        if (firstError) {
          firstError.focus();
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
    
    // Real-time validation
    inputs.forEach(input => {
      input.addEventListener('input', function() {
        debounce(() => validateField(this), 300)();
      });
      
      input.addEventListener('blur', function() {
        validateField(this);
      });
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
      phoneInput.addEventListener('input', formatPhoneNumber);
    }
  }
  
  // Validate individual field
  function validateField(field) {
    const value = field.value.trim();
    const fieldWrapper = field.closest('.form-field');
    
    // Remove previous states
    field.classList.remove('error', 'success');
    fieldWrapper?.classList.remove('error', 'success');
    
    // Clear existing messages
    const existingMessage = fieldWrapper?.querySelector('.validation-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    let isValid = true;
    let message = '';
    
    // Required field check
    if (field.hasAttribute('required') && !value) {
      isValid = false;
      message = 'Trường này là bắt buộc';
    }
    
    // Email validation
    else if (field.type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        isValid = false;
        message = 'Email không hợp lệ';
      }
    }
    
    // Phone validation
    else if (field.name === 'phone' && value) {
      const phoneRegex = /^[0-9+\-\s()]{10,}$/;
      if (!phoneRegex.test(value)) {
        isValid = false;
        message = 'Số điện thoại không hợp lệ';
      }
    }
    
    // Name validation
    else if (field.name === 'name' && value) {
      if (value.length < 2) {
        isValid = false;
        message = 'Tên phải có ít nhất 2 ký tự';
      }
    }
    
    // Message validation
    else if (field.name === 'message' && value) {
      if (value.length < 10) {
        isValid = false;
        message = 'Tin nhắn phải có ít nhất 10 ký tự';
      }
    }
    
    // Apply validation state
    if (isValid && value) {
      field.classList.add('success');
      fieldWrapper?.classList.add('success');
      createValidationMessage(fieldWrapper, '✓ Hợp lệ', 'success');
    } else if (!isValid) {
      field.classList.add('error');
      fieldWrapper?.classList.add('error');
      createValidationMessage(fieldWrapper, message, 'error');
      
      // Shake animation for error
      field.classList.add('shake');
      setTimeout(() => field.classList.remove('shake'), 600);
    }
    
    updateProgressIndicator();
    return isValid;
  }
  
  // Create validation message
  function createValidationMessage(wrapper, text, type) {
    if (!wrapper || !text) return;
    
    const message = document.createElement('div');
    message.className = `validation-message ${type}`;
    message.innerHTML = `<i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${text}`;
    
    wrapper.appendChild(message);
    
    // Animate in
    setTimeout(() => message.classList.add('show'), 100);
  }
  
  // Form animations
  function initFormAnimations() {
    const fields = form.querySelectorAll('.form-field');
    
    fields.forEach((field, index) => {
      // Stagger animation on load
      setTimeout(() => {
        field.classList.add('animate-in');
      }, index * 100);
      
      // Focus animations
      const input = field.querySelector('input, textarea');
      if (input) {
        input.addEventListener('focus', function() {
          field.classList.add('focused');
          
          // Create focus ring effect
          createFocusRing(this);
        });
        
        input.addEventListener('blur', function() {
          field.classList.remove('focused');
        });
      }
    });
  }
  
  // Create focus ring effect
  function createFocusRing(element) {
    const ring = document.createElement('div');
    ring.className = 'focus-ring';
    element.parentNode.appendChild(ring);
    
    // Remove after animation
    setTimeout(() => {
      if (ring.parentNode) {
        ring.parentNode.removeChild(ring);
      }
    }, 1000);
  }
  
  // Progress indicator
  function initProgressIndicator() {
    // Create progress bar if it doesn't exist
    let progressBar = document.querySelector('.form-progress');
    if (!progressBar) {
      progressBar = document.createElement('div');
      progressBar.className = 'form-progress';
      progressBar.innerHTML = `
        <div class="progress-header">
          <span class="progress-label">
            <i class="fas fa-clipboard-check"></i>
            Tiến độ hoàn thành
          </span>
          <span class="progress-percentage">0%</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
      `;
      
      form.insertBefore(progressBar, form.firstChild);
    }
    
    updateProgressIndicator();
  }
  
  // Update progress indicator
  function updateProgressIndicator() {
    const requiredFields = form.querySelectorAll('[required]');
    const completedFields = form.querySelectorAll('[required].success');
    const percentage = Math.round((completedFields.length / requiredFields.length) * 100);
    
    const progressFill = document.querySelector('.progress-fill');
    const progressPercentage = document.querySelector('.progress-percentage');
    
    if (progressFill && progressPercentage) {
      progressFill.style.width = `${percentage}%`;
      progressPercentage.textContent = `${percentage}%`;
      
      // Color changes based on progress
      if (percentage === 100) {
        progressFill.style.background = 'linear-gradient(90deg, #10b981, #059669)';
      } else if (percentage > 50) {
        progressFill.style.background = 'linear-gradient(90deg, #3b82f6, #1d4ed8)';
      } else {
        progressFill.style.background = 'linear-gradient(90deg, #f59e0b, #d97706)';
      }
    }
  }
  
  // Ripple effects
  function initRippleEffects() {
    const buttons = form.querySelectorAll('button, .submit-button');
    
    buttons.forEach(button => {
      button.addEventListener('click', function(e) {
        createRipple(e, this);
      });
    });
  }
  
  // Create ripple effect
  function createRipple(event, element) {
    const circle = document.createElement('span');
    const diameter = Math.max(element.clientWidth, element.clientHeight);
    const radius = diameter / 2;
    
    const rect = element.getBoundingClientRect();
    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${event.clientX - rect.left - radius}px`;
    circle.style.top = `${event.clientY - rect.top - radius}px`;
    circle.classList.add('ripple');
    
    const ripple = element.getElementsByClassName('ripple')[0];
    if (ripple) {
      ripple.remove();
    }
    
    element.appendChild(circle);
  }
  
  // Scroll animations
  function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    const elementsToAnimate = form.querySelectorAll('.field-group, .form-field');
    elementsToAnimate.forEach(el => observer.observe(el));
  }
  
  // Keyboard navigation
  function initKeyboardNavigation() {
    const formFields = form.querySelectorAll('input, textarea, button');
    
    form.addEventListener('keydown', function(e) {
      // Tab navigation enhancement
      if (e.key === 'Tab') {
        const currentIndex = Array.from(formFields).indexOf(document.activeElement);
        
        if (e.shiftKey && currentIndex === 0) {
          e.preventDefault();
          formFields[formFields.length - 1].focus();
        } else if (!e.shiftKey && currentIndex === formFields.length - 1) {
          e.preventDefault();
          formFields[0].focus();
        }
      }
      
      // Enter to submit
      if (e.key === 'Enter' && e.ctrlKey) {
        e.preventDefault();
        submitBtn.click();
      }
      
      // Escape to clear current field
      if (e.key === 'Escape') {
        if (document.activeElement && document.activeElement.tagName !== 'BUTTON') {
          document.activeElement.value = '';
          document.activeElement.blur();
        }
      }
    });
  }
  
  // Touch gestures for mobile
  function initTouchGestures() {
    if (!('ontouchstart' in window)) return;
    
    let startY = 0;
    let startX = 0;
    
    form.addEventListener('touchstart', function(e) {
      startY = e.touches[0].clientY;
      startX = e.touches[0].clientX;
    });
    
    form.addEventListener('touchend', function(e) {
      const endY = e.changedTouches[0].clientY;
      const endX = e.changedTouches[0].clientX;
      const diffY = startY - endY;
      const diffX = startX - endX;
      
      // Swipe gestures
      if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
        if (diffX > 0) {
          // Swipe left - next field
          const currentField = document.activeElement;
          const nextField = getNextField(currentField);
          if (nextField) {
            nextField.focus();
            nextField.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        } else {
          // Swipe right - previous field
          const currentField = document.activeElement;
          const prevField = getPreviousField(currentField);
          if (prevField) {
            prevField.focus();
            prevField.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }
      }
    });
  }
  
  // Form persistence (save draft)
  function initFormPersistence() {
    const storageKey = 'bookbee_contact_draft';
    
    // Load saved draft
    try {
      const savedData = localStorage.getItem(storageKey);
      if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
          const field = form.querySelector(`[name="${key}"]`);
          if (field) {
            field.value = data[key];
            validateField(field);
          }
        });
      }
    } catch (e) {
      console.log('Could not load saved draft');
    }
    
    // Save draft on input
    form.addEventListener('input', debounce(function() {
      const formData = new FormData(form);
      const data = {};
      for (let [key, value] of formData.entries()) {
        if (value.trim()) {
          data[key] = value;
        }
      }
      
      try {
        localStorage.setItem(storageKey, JSON.stringify(data));
      } catch (e) {
        console.log('Could not save draft');
      }
    }, 1000));
    
    // Clear draft on successful submit
    form.addEventListener('submit', function() {
      try {
        localStorage.removeItem(storageKey);
      } catch (e) {
        console.log('Could not clear draft');
      }
    });
  }
  
  // Loading states
  function initLoadingStates() {
    // Add loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
      <div class="loading-spinner">
        <div class="spinner-ring"></div>
        <div class="loading-text">Đang gửi tin nhắn...</div>
      </div>
    `;
    form.appendChild(loadingOverlay);
  }
  
  // Back to top functionality
  function initBackToTop() {
    // Create back to top button
    const backToTop = document.createElement('button');
    backToTop.className = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTop.setAttribute('aria-label', 'Về đầu trang');
    backToTop.setAttribute('title', 'Về đầu trang');
    document.body.appendChild(backToTop);
    
    // Show/hide on scroll
    let ticking = false;
    function updateBackToTop() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > 300) {
        backToTop.classList.add('show');
      } else {
        backToTop.classList.remove('show');
      }
      ticking = false;
    }
    
    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(updateBackToTop);
        ticking = true;
      }
    });
    
    // Click handler
    backToTop.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
      
      // Focus management
      setTimeout(() => {
        const firstInput = form.querySelector('input, textarea');
        if (firstInput) {
          firstInput.focus();
        }
      }, 500);
    });
  }
  
  // Page load animations
  function initPageAnimations() {
    // Animate hero section
    const hero = document.querySelector('.hero-section');
    if (hero) {
      setTimeout(() => {
        hero.classList.add('animate-in');
      }, 100);
    }
    
    // Animate contact info cards
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach((item, index) => {
      setTimeout(() => {
        item.classList.add('animate-in');
      }, 200 + (index * 100));
    });
    
    // Animate form container
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
      setTimeout(() => {
        formContainer.classList.add('animate-in');
      }, 400);
    }
  }
  
  // Auto-save notification
  function initAutoSaveNotification() {
    let saveNotification = null;
    
    function showSaveNotification() {
      // Remove existing notification
      if (saveNotification) {
        saveNotification.remove();
      }
      
      // Create new notification
      saveNotification = document.createElement('div');
      saveNotification.className = 'save-notification';
      saveNotification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>Bản nháp đã được lưu</span>
      `;
      
      document.body.appendChild(saveNotification);
      
      // Show notification
      setTimeout(() => {
        saveNotification.classList.add('show');
      }, 100);
      
      // Hide after 3 seconds
      setTimeout(() => {
        if (saveNotification) {
          saveNotification.classList.remove('show');
          setTimeout(() => {
            if (saveNotification && saveNotification.parentNode) {
              saveNotification.parentNode.removeChild(saveNotification);
            }
          }, 300);
        }
      }, 3000);
    }
    
    // Show notification when draft is saved
    form.addEventListener('input', debounce(function() {
      const formData = new FormData(form);
      let hasData = false;
      for (let [key, value] of formData.entries()) {
        if (value.trim()) {
          hasData = true;
          break;
        }
      }
      
      if (hasData) {
        showSaveNotification();
      }
    }, 2000));
  }
  
  // Submit form
  function submitForm() {
    const loadingOverlay = form.querySelector('.loading-overlay');
    
    // Show loading
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    loadingOverlay?.classList.add('show');
    
    // Change button text
    const buttonText = submitBtn.querySelector('.button-text');
    if (buttonText) {
      buttonText.textContent = 'Đang gửi...';
    }
    
    // Actually submit the form to the server
    const formData = new FormData(form);
    
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })
    .then(response => response.text())
    .then(data => {
      // Hide loading
      submitBtn.classList.remove('loading');
      submitBtn.classList.add('success');
      loadingOverlay?.classList.remove('show');
      
      // Show success message
      showSuccessMessage();
      
      // Reset form after delay
      setTimeout(() => {
        resetForm();
      }, 3000);
    })
    .catch(error => {
      console.error('Error:', error);
      // Hide loading
      submitBtn.classList.remove('loading');
      loadingOverlay?.classList.remove('show');
      
      // Reset button text
      if (buttonText) {
        buttonText.textContent = 'Gửi tin nhắn';
      }
      submitBtn.disabled = false;
      
      alert('Có lỗi xảy ra khi gửi form. Vui lòng thử lại.');
    });
  }
  
  // Show success message
  function showSuccessMessage() {
    // Create success overlay
    const successOverlay = document.createElement('div');
    successOverlay.className = 'success-overlay';
    successOverlay.innerHTML = `
      <div class="success-content">
        <div class="success-icon">
          <i class="fas fa-check"></i>
        </div>
        <h3>Gửi thành công!</h3>
        <p>Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.</p>
      </div>
    `;
    
    form.appendChild(successOverlay);
    
    // Show success overlay
    setTimeout(() => {
      successOverlay.classList.add('show');
    }, 100);
    
    // Create confetti effect
    createConfetti();
    
    // Remove overlay after delay
    setTimeout(() => {
      successOverlay.classList.remove('show');
      setTimeout(() => {
        if (successOverlay.parentNode) {
          successOverlay.parentNode.removeChild(successOverlay);
        }
      }, 500);
    }, 3000);
  }
  
  // Create confetti effect
  function createConfetti() {
    const confettiContainer = document.createElement('div');
    confettiContainer.className = 'confetti-container';
    document.body.appendChild(confettiContainer);
    
    // Create confetti pieces
    for (let i = 0; i < 50; i++) {
      const confetti = document.createElement('div');
      confetti.className = 'confetti';
      confetti.style.left = Math.random() * 100 + '%';
      confetti.style.animationDelay = Math.random() * 3 + 's';
      confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
      confettiContainer.appendChild(confetti);
    }
    
    // Remove confetti after animation
    setTimeout(() => {
      if (confettiContainer.parentNode) {
        confettiContainer.parentNode.removeChild(confettiContainer);
      }
    }, 6000);
  }
  
  // Reset form
  function resetForm() {
    form.reset();
    
    // Remove all validation states
    const fields = form.querySelectorAll('.form-field');
    fields.forEach(field => {
      field.classList.remove('error', 'success', 'focused');
      const input = field.querySelector('input, textarea');
      if (input) {
        input.classList.remove('error', 'success');
      }
    });
    
    // Clear validation messages
    const messages = form.querySelectorAll('.validation-message');
    messages.forEach(msg => msg.remove());
    
    // Reset button
    submitBtn.classList.remove('loading', 'success');
    submitBtn.disabled = false;
    const buttonText = submitBtn.querySelector('.button-text');
    if (buttonText) {
      buttonText.textContent = 'Gửi tin nhắn';
    }
    
    // Reset progress
    updateProgressIndicator();
  }
  
  // Phone number formatting
  function formatPhoneNumber(e) {
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
  }
  
  // Helper functions
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
  
  function getNextField(currentField) {
    const fields = Array.from(form.querySelectorAll('input, textarea'));
    const currentIndex = fields.indexOf(currentField);
    return fields[currentIndex + 1] || null;
  }
  
  function getPreviousField(currentField) {
    const fields = Array.from(form.querySelectorAll('input, textarea'));
    const currentIndex = fields.indexOf(currentField);
    return fields[currentIndex - 1] || null;
  }
});
