/**
 * Main JavaScript file for EV Charging Station Management
 * Contains global utility functions and event handlers
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeDropdowns();
    initializeAlerts();
    handleFormValidation();
    initializeAnimations();
    
    // Initialize specific components if they exist
    if (document.querySelector('.booking-calendar')) {
        initializeBookingCalendar();
    }
    
    if (document.querySelector('.station-grid')) {
        initializeStationCards();
    }
    
    if (document.querySelector('.chart-container')) {
        initializeCharts();
    }
});

/**
 * Initialize dropdown menus
 */
function initializeDropdowns() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            } else {
                // Close all other dropdowns
                document.querySelectorAll('.dropdown.show').forEach(openDropdown => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove('show');
                    }
                });
                
                dropdown.classList.add('show');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-toggle') && !e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
}

/**
 * Initialize alert dismissal
 */
function initializeAlerts() {
    const alertCloseButtons = document.querySelectorAll('.alert .close');
    
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            
            // Add fade out animation
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            
            // Remove alert after animation completes
            setTimeout(() => {
                alert.remove();
            }, 300);
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert:not(.alert-persistent)').forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }, 5000);
    });
}

/**
 * Handle form validation
 */
function handleFormValidation() {
    const forms = document.querySelectorAll('form.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
            
            // Custom validation
            const passwordFields = form.querySelectorAll('input[type="password"]');
            if (passwordFields.length > 1) {
                const password = passwordFields[0].value;
                const confirmPassword = passwordFields[1].value;
                
                if (password !== confirmPassword) {
                    passwordFields[1].setCustomValidity('Passwords do not match');
                    e.preventDefault();
                } else {
                    passwordFields[1].setCustomValidity('');
                }
            }
        });
        
        // Clear custom validation on input
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.setCustomValidity('');
            });
        });
    });
}

/**
 * Initialize animations
 */
function initializeAnimations() {
    // Animate elements when they come into view
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    function checkAnimatedElements() {
        animatedElements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 50) {
                element.classList.add('animated');
            }
        });
    }
    
    // Check on load and scroll
    checkAnimatedElements();
    window.addEventListener('scroll', checkAnimatedElements);
}

/**
 * Initialize booking calendar
 */
function initializeBookingCalendar() {
    const calendar = document.querySelector('.booking-calendar');
    if (!calendar) return;
    
    const calendarDays = calendar.querySelectorAll('.calendar-day:not(.disabled)');
    const timeSlots = calendar.querySelectorAll('.time-slot:not(.disabled)');
    const dateInput = document.querySelector('#booking-date');
    const timeInput = document.querySelector('#booking-time');
    const selectedDateDisplay = document.querySelector('#selected-date-display');
    
    // Set up day selection
    calendarDays.forEach(day => {
        day.addEventListener('click', function() {
            // Remove selected class from all days
            calendarDays.forEach(d => d.classList.remove('selected'));
            
            // Add selected class to clicked day
            this.classList.add('selected');
            
            // Update form input and display
            const date = this.dataset.date;
            if (dateInput) dateInput.value = date;
            if (selectedDateDisplay) selectedDateDisplay.textContent = formatDate(date);
            
            // Update available time slots (simulate API call)
            updateAvailableTimeSlots(date);
        });
    });
    
    // Set up time slot selection
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            // Remove selected class from all time slots
            timeSlots.forEach(s => s.classList.remove('selected'));
            
            // Add selected class to clicked time slot
            this.classList.add('selected');
            
            // Update form input
            if (timeInput) timeInput.value = this.dataset.time;
        });
    });
    
    // Previous/Next month navigation
    const prevMonthBtn = calendar.querySelector('.prev-month');
    const nextMonthBtn = calendar.querySelector('.next-month');
    
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
            // In a real implementation, this would load the previous month
            console.log('Load previous month');
        });
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
            // In a real implementation, this would load the next month
            console.log('Load next month');
        });
    }
    
    // Simulate updating time slots based on date
    function updateAvailableTimeSlots(date) {
        // This would typically be an AJAX call to the server
        console.log(`Fetching available time slots for date: ${date}`);
        
        // Show loading state
        document.querySelector('.time-slots').classList.add('loading');
        
        // Simulate API delay
        setTimeout(() => {
            // Remove loading state
            document.querySelector('.time-slots').classList.remove('loading');
            
            // Reset time slots
            timeSlots.forEach(slot => {
                slot.classList.remove('disabled', 'selected');
                
                // Randomly disable some slots for demo purposes
                if (Math.random() > 0.7) {
                    slot.classList.add('disabled');
                }
            });
            
            // Clear time input
            if (timeInput) timeInput.value = '';
        }, 500);
    }
    
    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString(undefined, options);
    }
}

/**
 * Initialize station cards
 */
function initializeStationCards() {
    const stationCards = document.querySelectorAll('.station-card');
    
    stationCards.forEach(card => {
        // Set up availability bar
        const availabilityBar = card.querySelector('.availability-progress');
        if (availabilityBar) {
            const percentage = availabilityBar.dataset.percentage || 0;
            availabilityBar.style.width = `${percentage}%`;
            
            // Change color based on availability
            if (percentage < 30) {
                availabilityBar.style.backgroundColor = 'var(--error)';
            } else if (percentage < 70) {
                availabilityBar.style.backgroundColor = 'var(--warning)';
            }
        }
        
        // Set up view details button
        const viewDetailsBtn = card.querySelector('.view-details-btn');
        if (viewDetailsBtn) {
            viewDetailsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const stationId = this.dataset.stationId;
                window.location.href = `station-details.php?id=${stationId}`;
            });
        }
        
        // Set up book now button
        const bookNowBtn = card.querySelector('.book-now-btn');
        if (bookNowBtn) {
            bookNowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const stationId = this.dataset.stationId;
                window.location.href = `book.php?station_id=${stationId}`;
            });
        }
    });
}

/**
 * Initialize charts
 * Note: This is a placeholder. In a real implementation, you would use a charting library like Chart.js
 */
function initializeCharts() {
    const chartContainers = document.querySelectorAll('.chart-container');
    
    chartContainers.forEach(container => {
        const chartFilters = container.querySelectorAll('.chart-filter');
        
        chartFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active class from all filters
                chartFilters.forEach(f => f.classList.remove('active'));
                
                // Add active class to clicked filter
                this.classList.add('active');
                
                // Update chart data (in a real implementation)
                const filterValue = this.dataset.filter;
                console.log(`Updating chart with filter: ${filterValue}`);
                
                // Simulate chart update
                const chartBody = container.querySelector('.chart-body');
                if (chartBody) {
                    chartBody.innerHTML = `<div class="text-center p-6">Chart data for ${filterValue} would be displayed here.</div>`;
                }
            });
        });
    });
}

/**
 * Format a date string
 * 
 * @param {Date|string} date Date object or date string
 * @param {boolean} includeTime Whether to include time
 * @returns {string} Formatted date string
 */
function formatDate(date, includeTime = false) {
    const d = new Date(date);
    
    if (isNaN(d.getTime())) {
        return 'Invalid date';
    }
    
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    
    if (includeTime) {
        options.hour = '2-digit';
        options.minute = '2-digit';
    }
    
    return d.toLocaleDateString(undefined, options);
}

/**
 * Format a time string
 * 
 * @param {string} time Time string (HH:MM format)
 * @returns {string} Formatted time string
 */
function formatTime(time) {
    if (!time) return '';
    
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(parseInt(hours, 10));
    date.setMinutes(parseInt(minutes, 10));
    
    return date.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

/**
 * Format currency
 * 
 * @param {number} amount Amount to format
 * @param {string} currency Currency symbol
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount, currency = '€') {
    return `${currency}${parseFloat(amount).toFixed(2)}`;
}

/**
 * Show a confirmation dialog
 * 
 * @param {string} message Confirmation message
 * @param {Function} onConfirm Callback function on confirm
 */
function confirmAction(message, onConfirm) {
    if (confirm(message)) {
        onConfirm();
    }
}

/**
 * Handle AJAX form submission
 * 
 * @param {HTMLFormElement} form Form element
 * @param {Function} onSuccess Success callback
 * @param {Function} onError Error callback
 */
function submitFormAjax(form, onSuccess, onError) {
    const formData = new FormData(form);
    const url = form.action;
    const method = form.method || 'POST';
    
    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (onSuccess) onSuccess(data);
        } else {
            if (onError) onError(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        if (onError) onError('Network error. Please try again.');
        console.error('Form submission error:', error);
    });
}

/**
 * Display a notification
 * 
 * @param {string} message Notification message
 * @param {string} type Notification type (success, error, warning, info)
 * @param {number} duration Duration in milliseconds (0 for persistent)
 */
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} slide-up`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-message">${message}</div>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Add close button functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
    
    // Auto-dismiss after duration (if not persistent)
    if (duration > 0) {
        setTimeout(() => {
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, duration);
    }
}