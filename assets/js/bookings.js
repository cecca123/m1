/**
 * Bookings JavaScript file
 * Handles booking-related functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeBookingForm();
    setupBookingCancellation();
});

/**
 * Initialize the booking form
 */
function initializeBookingForm() {
    const bookingForm = document.getElementById('booking-form');
    if (!bookingForm) return;

    const stationSelect = document.getElementById('station-id');
    const chargingPointSelect = document.getElementById('charging-point-id');
    const dateInput = document.getElementById('booking-date');
    const startTimeInput = document.getElementById('start-time');
    const endTimeInput = document.getElementById('end-time');
    const submitButton = bookingForm.querySelector('button[type="submit"]');

    // Disable submit button initially
    if (submitButton) {
        submitButton.disabled = true;
    }

    if (stationSelect) {
        stationSelect.addEventListener('change', async function() {
            const stationId = this.value;
            if (!stationId) {
                if (chargingPointSelect) {
                    chargingPointSelect.innerHTML = '<option value="">Select a charging point</option>';
                    chargingPointSelect.disabled = true;
                }
                return;
            }

            // Clear and disable charging point select
            if (chargingPointSelect) {
                chargingPointSelect.innerHTML = '<option value="">Select a charging point</option>';
                chargingPointSelect.disabled = true;
            }

            // Clear time inputs
            if (startTimeInput) startTimeInput.value = '';
            if (endTimeInput) endTimeInput.value = '';

            try {
                // Show loading state
                const loadingElement = document.createElement('div');
                loadingElement.className = 'loading-indicator';
                loadingElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading charging points...';

                // Remove any existing loading indicator
                const existingLoadingIndicator = bookingForm.querySelector('.loading-indicator');
                if (existingLoadingIndicator) {
                    existingLoadingIndicator.remove();
                }

                bookingForm.insertBefore(loadingElement, chargingPointSelect.parentNode.nextSibling);

                // Simulate API call (replace with actual API call in production)
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Mock charging points data
                const mockChargingPoints = [
                    { charging_point_id: 1, slots_num: 2 },
                    { charging_point_id: 2, slots_num: 2 },
                    { charging_point_id: 3, slots_num: 2 },
                    { charging_point_id: 4, slots_num: 2 },
                    { charging_point_id: 5, slots_num: 2 }
                ];

                // Remove loading indicator
                loadingElement.remove();

                // Populate charging points dropdown
                if (chargingPointSelect) {
                    mockChargingPoints.forEach(point => {
                        const option = document.createElement('option');
                        option.value = point.charging_point_id;
                        option.textContent = `Point #${point.charging_point_id} (${point.slots_num} slots)`;
                        chargingPointSelect.appendChild(option);
                    });

                    chargingPointSelect.disabled = false;
                }

                validateForm();
            } catch (error) {
                console.error('Error fetching charging points:', error);
                const loadingElement = bookingForm.querySelector('.loading-indicator');
                if (loadingElement) {
                    loadingElement.remove();
                }
                showNotification('Failed to load charging points. Please try again.', 'error');
            }
        });
    }

    // Add change event listeners to all form inputs
    const formInputs = bookingForm.querySelectorAll('select, input');
    formInputs.forEach(input => {
        input.addEventListener('change', validateForm);
    });

    // Form validation function
    function validateForm() {
        if (!submitButton) return;

        const isValid = stationSelect.value &&
            chargingPointSelect.value &&
            dateInput.value &&
            startTimeInput.value &&
            endTimeInput.value;

        submitButton.disabled = !isValid;
    }

    // Handle form submission
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate time range
            if (startTimeInput.value && endTimeInput.value) {
                const startTime = new Date(`2000-01-01 ${startTimeInput.value}`);
                const endTime = new Date(`2000-01-01 ${endTimeInput.value}`);

                if (endTime <= startTime) {
                    showNotification('End time must be after start time', 'error');
                    return;
                }
            }

            // If all valid, submit the form
            this.submit();
        });
    }
}

/**
 * Set up booking cancellation
 */
function setupBookingCancellation() {
    const cancelButtons = document.querySelectorAll('.cancel-booking-btn');
    if (cancelButtons.length === 0) return;

    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const bookingId = this.dataset.bookingId;
            const bookingDate = this.dataset.bookingDate;
            const bookingTime = this.dataset.bookingTime;

            if (confirm(`Are you sure you want to cancel your booking for ${bookingDate} at ${bookingTime}?`)) {
                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
                this.disabled = true;

                // Simulate API call (replace with actual API call in production)
                setTimeout(() => {
                    const bookingElement = this.closest('.booking-item') || this.closest('tr');
                    if (bookingElement) {
                        bookingElement.style.opacity = '0.5';
                        bookingElement.style.textDecoration = 'line-through';
                        this.innerHTML = 'Cancelled';
                    }
                    showNotification('Booking has been successfully cancelled', 'success');
                }, 1000);
            }
        });
    });
}

/**
 * Display a notification
 *
 * @param {string} message Notification message
 * @param {string} type Notification type (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;

    // Add notification to the page
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(notification, container.firstChild);

        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}