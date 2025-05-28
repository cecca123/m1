/**
 * Dashboard JavaScript file
 * Handles dashboard-related functionality including charts and statistics
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeStatCards();
    initializeCharts();
    initializeUpcomingBookings();
    initializeNotifications();
});

/**
 * Initialize stat cards with animations
 */
function initializeStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length === 0) return;
    
    // Animate the stat card values
    statCards.forEach(card => {
        const valueElement = card.querySelector('.stat-card-value');
        if (!valueElement) return;
        
        const value = parseFloat(valueElement.dataset.value || valueElement.textContent);
        const prefix = valueElement.dataset.prefix || '';
        const suffix = valueElement.dataset.suffix || '';
        const decimals = valueElement.dataset.decimals || 0;
        
        animateValue(valueElement, 0, value, 1500, prefix, suffix, decimals);
    });
    
    // Animate value from start to end
    function animateValue(element, start, end, duration, prefix, suffix, decimals) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = progress * (end - start) + start;
            
            element.textContent = `${prefix}${currentValue.toFixed(decimals)}${suffix}`;
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        
        window.requestAnimationFrame(step);
    }
}

/**
 * Initialize charts
 * Note: This is a placeholder function. In a real app, you'd use a library like Chart.js.
 */
function initializeCharts() {
    // This is where you would initialize your charts
    // For demonstration purposes, we'll just create placeholder chart displays
    
    // Energy consumption chart
    const energyChartContainer = document.getElementById('energy-consumption-chart');
    if (energyChartContainer) {
        createPlaceholderChart(energyChartContainer, 'Energy Consumption');
    }
    
    // Cost chart
    const costChartContainer = document.getElementById('charging-cost-chart');
    if (costChartContainer) {
        createPlaceholderChart(costChartContainer, 'Charging Costs');
    }
    
    // Sessions chart
    const sessionsChartContainer = document.getElementById('charging-sessions-chart');
    if (sessionsChartContainer) {
        createPlaceholderChart(sessionsChartContainer, 'Charging Sessions');
    }
    
    // Set up chart filter buttons
    const chartFilters = document.querySelectorAll('.chart-filter');
    chartFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            const filterGroup = this.closest('.chart-filters');
            if (!filterGroup) return;
            
            // Remove active class from all filters in this group
            filterGroup.querySelectorAll('.chart-filter').forEach(f => {
                f.classList.remove('active');
            });
            
            // Add active class to clicked filter
            this.classList.add('active');
            
            // Get the chart container
            const chartContainer = this.closest('.chart-container');
            if (!chartContainer) return;
            
            const chartBody = chartContainer.querySelector('.chart-body');
            if (!chartBody) return;
            
            // Update chart based on selected filter
            const period = this.dataset.period;
            
            // Show loading state
            chartBody.innerHTML = `
                <div class="loading-indicator text-center p-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Loading ${period} data...</p>
                </div>
            `;
            
            // In a real app, you would fetch new data and update the chart
            // For demonstration, we'll just show a placeholder after a delay
            setTimeout(() => {
                createPlaceholderChart(chartBody, chartBody.dataset.chartType, period);
            }, 500);
        });
    });
    
    /**
     * Create a placeholder chart visualization
     * In a real application, you would replace this with actual chart rendering
     */
    function createPlaceholderChart(container, chartType, period = 'monthly') {
        container.dataset.chartType = chartType;
        
        const colorMap = {
            'Energy Consumption': 'var(--primary)',
            'Charging Costs': 'var(--accent)',
            'Charging Sessions': 'var(--secondary)'
        };
        
        const color = colorMap[chartType] || 'var(--primary)';
        
        // Generate random data
        const data = [];
        const labels = [];
        
        if (period === 'weekly') {
            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            for (let i = 0; i < 7; i++) {
                labels.push(days[i]);
                data.push(Math.floor(Math.random() * 100));
            }
        } else if (period === 'monthly') {
            for (let i = 1; i <= 30; i++) {
                labels.push(i);
                data.push(Math.floor(Math.random() * 100));
            }
        } else if (period === 'yearly') {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for (let i = 0; i < 12; i++) {
                labels.push(months[i]);
                data.push(Math.floor(Math.random() * 100));
            }
        }
        
        // Create a chart visualization using HTML/CSS
        container.innerHTML = `
            <div class="placeholder-chart">
                <div class="chart-bars">
                    ${data.map((value, index) => `
                        <div class="chart-bar-container" title="${labels[index]}: ${value}">
                            <div class="chart-bar" style="height: ${value}%; background-color: ${color};"></div>
                            <div class="chart-label">${labels[index]}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="chart-axes">
                    <div class="y-axis">
                        <div class="axis-label">100</div>
                        <div class="axis-label">75</div>
                        <div class="axis-label">50</div>
                        <div class="axis-label">25</div>
                        <div class="axis-label">0</div>
                    </div>
                </div>
            </div>
        `;
        
        // Add CSS for the placeholder chart if needed
        if (!document.getElementById('placeholder-chart-styles')) {
            const style = document.createElement('style');
            style.id = 'placeholder-chart-styles';
            style.textContent = `
                .placeholder-chart {
                    position: relative;
                    height: 100%;
                    padding-bottom: 30px;
                    padding-left: 40px;
                }
                
                .chart-bars {
                    display: flex;
                    align-items: flex-end;
                    height: 100%;
                    gap: 2px;
                }
                
                .chart-bar-container {
                    flex: 1;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: flex-end;
                }
                
                .chart-bar {
                    width: 100%;
                    transition: height 0.5s ease-in-out;
                }
                
                .chart-label {
                    font-size: 0.7rem;
                    color: var(--gray-600);
                    margin-top: 5px;
                    transform: rotate(-45deg);
                    transform-origin: top left;
                    white-space: nowrap;
                }
                
                .chart-axes {
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: calc(100% - 30px);
                }
                
                .y-axis {
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                
                .axis-label {
                    font-size: 0.7rem;
                    color: var(--gray-500);
                }
            `;
            
            document.head.appendChild(style);
        }
    }
}

/**
 * Initialize upcoming bookings section
 */
function initializeUpcomingBookings() {
    const upcomingBookingsContainer = document.getElementById('upcoming-bookings');
    if (!upcomingBookingsContainer) return;
    
    // Set up cancel booking buttons
    const cancelButtons = upcomingBookingsContainer.querySelectorAll('.cancel-booking-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookingId = this.dataset.bookingId;
            const bookingDate = this.dataset.bookingDate;
            const bookingTime = this.dataset.bookingTime;
            
            confirmAction(
                `Are you sure you want to cancel your booking for ${bookingDate} at ${bookingTime}?`,
                () => {
                    // Show loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
                    this.disabled = true;
                    
                    // In a real implementation, this would be an AJAX call
                    setTimeout(() => {
                        // Remove the booking card from the UI
                        const bookingCard = this.closest('.booking-card');
                        if (bookingCard) {
                            bookingCard.style.height = bookingCard.offsetHeight + 'px';
                            bookingCard.style.overflow = 'hidden';
                            
                            setTimeout(() => {
                                bookingCard.style.height = '0';
                                bookingCard.style.padding = '0';
                                bookingCard.style.margin = '0';
                                
                                setTimeout(() => {
                                    bookingCard.remove();
                                    
                                    // If no more bookings, show a message
                                    const remainingBookings = upcomingBookingsContainer.querySelectorAll('.booking-card');
                                    if (remainingBookings.length === 0) {
                                        upcomingBookingsContainer.innerHTML = `
                                            <div class="alert alert-info">
                                                <p>You have no upcoming bookings.</p>
                                                <a href="bookings.php" class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-calendar-plus"></i> Make a Booking
                                                </a>
                                            </div>
                                        `;
                                    }
                                }, 300);
                            }, 10);
                        }
                        
                        showNotification('Booking has been successfully cancelled.', 'success');
                    }, 800);
                }
            );
        });
    });
}

/**
 * Initialize notifications panel
 */
function initializeNotifications() {
    const notificationsContainer = document.getElementById('notifications-panel');
    if (!notificationsContainer) return;
    
    // Set up mark as read buttons
    const markAsReadButtons = notificationsContainer.querySelectorAll('.mark-read-btn');
    markAsReadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const notificationId = this.dataset.notificationId;
            const notificationItem = this.closest('.notification-item');
            
            // In a real implementation, this would be an AJAX call
            setTimeout(() => {
                // Mark notification as read
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    this.remove(); // Remove the button
                    
                    // Update unread count
                    const unreadCountElement = document.querySelector('.notification-badge');
                    if (unreadCountElement) {
                        const currentCount = parseInt(unreadCountElement.textContent);
                        if (currentCount > 1) {
                            unreadCountElement.textContent = currentCount - 1;
                        } else {
                            unreadCountElement.remove();
                        }
                    }
                }
            }, 300);
        });
    });
    
    // Set up mark all as read button
    const markAllReadButton = notificationsContainer.querySelector('.mark-all-read-btn');
    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // In a real implementation, this would be an AJAX call
            setTimeout(() => {
                // Mark all notifications as read
                const unreadNotifications = notificationsContainer.querySelectorAll('.notification-item.unread');
                unreadNotifications.forEach(item => {
                    item.classList.remove('unread');
                    item.querySelector('.mark-read-btn')?.remove();
                });
                
                // Update unread count
                const unreadCountElements = document.querySelectorAll('.notification-badge');
                unreadCountElements.forEach(el => el.remove());
                
                showNotification('All notifications marked as read.', 'success');
            }, 300);
        });
    }
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