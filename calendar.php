<?php
header("Content-Type: application/javascript; charset=utf-8");
?>
// calendar.php
// Interactive AJAX Calendar Engine for Kismec Booking System

document.addEventListener('DOMContentLoaded', function() {
    // Current date tracking state
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth(); // 0-indexed
    let currentYear = currentDate.getFullYear();
    
    // Cached event list to prevent redundant loads
    let loadedBookings = [];

    // DOM Elements Cache
    const calendarMonthYear = document.getElementById('calendar-month-year');
    const calendarGridCells = document.getElementById('calendar-grid-cells');
    const btnPrevMonth = document.getElementById('btn-prev-month');
    const btnNextMonth = document.getElementById('btn-next-month');
    const filterType = document.getElementById('filter-type');
    const filterResource = document.getElementById('filter-resource');
    
    // Sidebar & Navigation Elements
    const btnMenuToggle = document.getElementById('btn-menu-toggle');
    const sidebar = document.getElementById('dashboard-sidebar');
    
    // Modal Elements
    const bookingModalOverlay = document.getElementById('booking-modal-overlay');
    const bookingModalTitle = document.getElementById('booking-modal-title');
    const btnCloseBookingModal = document.getElementById('btn-close-booking-modal');
    const bookingForm = document.getElementById('booking-form');
    const modalAlert = document.getElementById('modal-alert');
    
    // Form Input Elements
    const fieldBookingId = document.getElementById('booking-id-field');
    const fieldBookingDate = document.getElementById('booking-date');
    const fieldBookingEndDate = document.getElementById('booking-end-date');
    const endGroup = document.getElementById('end-date-group');
    const datesContainer = document.getElementById('booking-dates-container');
    const fieldResource = document.getElementById('booking-resource');
    const fieldStart = document.getElementById('booking-start');
    const fieldEnd = document.getElementById('booking-end');
    const fieldPurpose = document.getElementById('booking-purpose');
    const ownerDisplay = document.getElementById('booking-owner-display');
    const ownerName = document.getElementById('booking-owner-name');
    
    // Modal Button Controls
    const btnCancelBooking = document.getElementById('btn-cancel-booking');
    const btnDeleteBooking = document.getElementById('btn-delete-booking');
    const btnSubmitBooking = document.getElementById('btn-submit-booking');
    const btnQuickBook = document.getElementById('btn-quick-book');

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // ==========================================================================
    // Sidebar Mobile Controls
    // ==========================================================================
    if (btnMenuToggle && sidebar) {
        btnMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            btnMenuToggle.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && e.target !== btnMenuToggle) {
                sidebar.classList.remove('active');
                btnMenuToggle.classList.remove('active');
            }
        });
    }

    // ==========================================================================
    // Calendar Core Rendering Engine
    // ==========================================================================
    
    function initCalendar() {
        renderCalendarGrid();
        attachEventListeners();
    }

    function renderCalendarGrid() {
        // Update header month title
        calendarMonthYear.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        // Clear old calendar cells
        calendarGridCells.innerHTML = '';

        // First day of current month (0 is Sunday, 6 is Saturday)
        const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
        
        // Total days in current month
        const totalDaysCurrent = new Date(currentYear, currentMonth + 1, 0).getDate();
        
        // Total days in previous month
        const totalDaysPrev = new Date(currentYear, currentMonth, 0).getDate();

        // Calculate leading pad days from previous month
        for (let i = firstDayIndex; i > 0; i--) {
            const prevDay = totalDaysPrev - i + 1;
            const cell = createCalendarCell(prevDay, 'other-month', true);
            calendarGridCells.appendChild(cell);
        }

        // Current month cells populator
        const today = new Date();
        for (let day = 1; day <= totalDaysCurrent; day++) {
            const isToday = (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
            const cellClass = isToday ? 'today' : '';
            
            const cell = createCalendarCell(day, cellClass, false);
            calendarGridCells.appendChild(cell);
        }

        // Trailing pad days to complete the calendar grid row (6-day week completion)
        const totalCellsFilled = firstDayIndex + totalDaysCurrent;
        const remainingGridCells = 42 - totalCellsFilled; // standard 6x7 grid size
        for (let j = 1; j <= remainingGridCells; j++) {
            const cell = createCalendarCell(j, 'other-month', true);
            calendarGridCells.appendChild(cell);
        }

        // Load Bookings for this newly loaded month view
        fetchBookings();
    }

    function createCalendarCell(dayNumber, additionalClass = '', isOtherMonth = false) {
        const cell = document.createElement('div');
        cell.className = `calendar-cell ${additionalClass}`;
        
        // Add date number element
        const dateNum = document.createElement('span');
        dateNum.className = 'calendar-date-number';
        dateNum.textContent = dayNumber;
        cell.appendChild(dateNum);

        if (!isOtherMonth) {
            // Build absolute date format string for backend queries (YYYY-MM-DD)
            const formattedDay = String(dayNumber).padStart(2, '0');
            const formattedMonth = String(currentMonth + 1).padStart(2, '0');
            const dateStr = `${currentYear}-${formattedMonth}-${formattedDay}`;
            cell.dataset.date = dateStr;

            // Soft interactive creation button on hovering slot
            const addBtn = document.createElement('button');
            addBtn.className = 'calendar-cell-add-btn';
            addBtn.innerHTML = '+';
            addBtn.title = 'Add booking';
            addBtn.type = 'button';
            addBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                openNewBookingModal(dateStr);
            });
            cell.appendChild(addBtn);

            // Container for event pills
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'calendar-cell-events';
            cell.appendChild(eventsContainer);

            // Let clicking anywhere inside an empty day open the creation modal
            cell.addEventListener('click', function() {
                openNewBookingModal(dateStr);
            });
        }

        return cell;
    }

    // ==========================================================================
    // AJAX Server Requests
    // ==========================================================================
    
    function fetchBookings() {
        const type = filterType.value;
        const resourceId = filterResource.value;
        const monthParam = currentMonth + 1; // php expects 1-12 range
        
        // Show loading state if needed
        const url = `api_bookings.php?month=${monthParam}&year=${currentYear}&type=${type}&resource_id=${resourceId}`;
        
        fetch(url)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    loadedBookings = res.data;
                    populateEventsOnCalendar(res.data);
                } else {
                    console.error('Failed loading bookings: ', res.message);
                }
            })
            .catch(err => {
                console.error('Network error fetching bookings: ', err);
            });
    }

    function populateEventsOnCalendar(bookings) {
        // Clear out any old events rendered inside current month cells
        const currentMonthCells = calendarGridCells.querySelectorAll('.calendar-cell:not(.other-month)');
        currentMonthCells.forEach(cell => {
            const container = cell.querySelector('.calendar-cell-events');
            if (container) container.innerHTML = '';
        });

        // Insert booking pills
        bookings.forEach(booking => {
            const cell = calendarGridCells.querySelector(`.calendar-cell[data-date="${booking.booking_date}"]`);
            if (cell) {
                const container = cell.querySelector('.calendar-cell-events');
                if (container) {
                    const pill = createBookingPill(booking);
                    container.appendChild(pill);
                }
            }
        });
    }

    function createBookingPill(booking) {
        const pill = document.createElement('div');
        const isLab = booking.resource_type === 'lab';
        
        pill.className = `calendar-event ${isLab ? 'event-lab' : 'event-classroom'}`;
        
        // Format time string for clean display e.g. "09:00 - 11:30"
        const cleanStart = booking.start_time.substring(0, 5);
        const cleanEnd = booking.end_time.substring(0, 5);
        
        pill.textContent = `${cleanStart} ${booking.resource_name}`;
        pill.title = `${booking.resource_name}\nTime: ${cleanStart} - ${cleanEnd}\nBooked by: ${booking.booked_by}\nPurpose: ${booking.purpose}`;
        
        // Stop bubbling so cell click doesn't trigger blank creation modal
        pill.addEventListener('click', function(e) {
            e.stopPropagation();
            openEditBookingModal(booking);
        });

        return pill;
    }

    // ==========================================================================
    // Modal Controller & Actions
    // ==========================================================================
    
    function showModal() {
        hideModalAlert();
        bookingModalOverlay.classList.add('active');
    }

    function closeModal() {
        bookingModalOverlay.classList.remove('active');
        bookingForm.reset();
    }

    function showModalAlert(message, type = 'error') {
        modalAlert.textContent = message;
        modalAlert.className = `alert alert-${type}`;
        modalAlert.style.display = 'block';
    }

    function hideModalAlert() {
        modalAlert.style.display = 'none';
        modalAlert.textContent = '';
    }

    function setFormInputsDisabled(state) {
        fieldBookingDate.disabled = state;
        fieldBookingEndDate.disabled = state;
        fieldResource.disabled = state;
        fieldStart.disabled = state;
        fieldEnd.disabled = state;
        fieldPurpose.disabled = state;
    }

    function openNewBookingModal(dateStr) {
        bookingModalTitle.textContent = 'Book a Resource';
        setFormInputsDisabled(false);
        
        // Show end date selector and make layout dual-column
        if (endGroup) endGroup.style.display = 'block';
        if (datesContainer) datesContainer.style.gridTemplateColumns = '1fr 1fr';
        
        // Set values
        fieldBookingId.value = '';
        fieldBookingDate.value = dateStr;
        if (fieldBookingEndDate) fieldBookingEndDate.value = dateStr;
        fieldResource.value = '';
        fieldStart.value = '';
        fieldEnd.value = '';
        fieldPurpose.value = '';
        
        // Adjust button layouts
        btnDeleteBooking.style.display = 'none';
        btnSubmitBooking.style.display = 'inline-flex';
        btnSubmitBooking.textContent = 'Save Reservation';
        ownerDisplay.style.display = 'none';
        
        showModal();
    }

    function openEditBookingModal(booking) {
        bookingModalTitle.textContent = 'Booking Details';
        
        // Hide end date selector and make layout single-column (view/edit is single day specific)
        if (endGroup) endGroup.style.display = 'none';
        if (datesContainer) datesContainer.style.gridTemplateColumns = '1fr';
        
        // Set values
        fieldBookingId.value = booking.id;
        fieldBookingDate.value = booking.booking_date;
        if (fieldBookingEndDate) fieldBookingEndDate.value = booking.booking_date;
        fieldResource.value = booking.resource_id;
        
        // Trim standard MySQL HH:MM:SS to match select dropdown values (HH:MM)
        fieldStart.value = booking.start_time.substring(0, 5);
        fieldEnd.value = booking.end_time.substring(0, 5);
        fieldPurpose.value = booking.purpose;

        // Display ownership metadata
        ownerName.textContent = booking.booked_by;
        ownerDisplay.style.display = 'block';

        // Check if current user is allowed to edit this booking
        const sessionUser = window.KISMEC_SESSION;
        const canModify = (booking.user_id === sessionUser.userId || sessionUser.role === 'admin');

        if (canModify) {
            setFormInputsDisabled(false);
            btnDeleteBooking.style.display = 'inline-flex';
            btnSubmitBooking.style.display = 'inline-flex';
            btnSubmitBooking.textContent = 'Update Booking';
        } else {
            // View only for items owned by other users
            setFormInputsDisabled(true);
            btnDeleteBooking.style.display = 'none';
            btnSubmitBooking.style.display = 'none';
            bookingModalTitle.textContent = 'View Booking';
        }

        showModal();
    }

    // Handle form submissions (Create or Edit)
    function submitBookingForm() {
        const bookingId = fieldBookingId.value;
        const isEdit = bookingId !== '';
        
        const formData = new FormData(bookingForm);
        formData.append('action', isEdit ? 'edit' : 'create');

        btnSubmitBooking.disabled = true;
        btnSubmitBooking.textContent = 'Processing...';

        fetch('api_bookings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            btnSubmitBooking.disabled = false;
            btnSubmitBooking.textContent = isEdit ? 'Update Booking' : 'Save Reservation';

            if (res.status === 'success') {
                closeModal();
                fetchBookings(); // Refresh grid layout
            } else {
                showModalAlert(res.message, 'error');
            }
        })
        .catch(err => {
            btnSubmitBooking.disabled = false;
            btnSubmitBooking.textContent = isEdit ? 'Update Booking' : 'Save Reservation';
            showModalAlert('A server communication error occurred. Please try again.', 'error');
            console.error('Submit booking error:', err);
        });
    }

    // Handle delete operation
    function deleteBooking() {
        const bookingId = fieldBookingId.value;
        if (!bookingId) return;

        if (!confirm('Are you absolutely sure you want to cancel and delete this resource booking?')) {
            return;
        }

        btnDeleteBooking.disabled = true;
        btnDeleteBooking.textContent = 'Deleting...';

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('booking_id', bookingId);

        fetch('api_bookings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            btnDeleteBooking.disabled = false;
            btnDeleteBooking.textContent = 'Delete Booking';

            if (res.status === 'success') {
                closeModal();
                fetchBookings(); // Refresh
            } else {
                showModalAlert(res.message, 'error');
            }
        })
        .catch(err => {
            btnDeleteBooking.disabled = false;
            btnDeleteBooking.textContent = 'Delete Booking';
            showModalAlert('A server communication error occurred during deletion.', 'error');
            console.error('Delete booking error:', err);
        });
    }

    // ==========================================================================
    // Event Listeners Mapping
    // ==========================================================================
    
    function attachEventListeners() {
        // Navigation clicks
        btnPrevMonth.addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendarGrid();
        });

        btnNextMonth.addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendarGrid();
        });

        // Filter triggers
        filterType.addEventListener('change', function() {
            // Filter resource dropdown values based on type selection dynamically
            const selectedType = filterType.value;
            const resourceOptions = filterResource.querySelectorAll('option:not([value="all"])');
            
            resourceOptions.forEach(opt => {
                const optType = opt.getAttribute('data-type');
                if (selectedType === 'all' || optType === selectedType) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            // Reset specific resource selection if filtered out
            const activeOpt = filterResource.options[filterResource.selectedIndex];
            if (activeOpt.value !== 'all' && activeOpt.style.display === 'none') {
                filterResource.value = 'all';
            }

            fetchBookings();
        });

        filterResource.addEventListener('change', fetchBookings);

        // Modal triggers
        btnCloseBookingModal.addEventListener('click', closeModal);
        btnCancelBooking.addEventListener('click', closeModal);
        
        // Modal clicking outside content closes
        bookingModalOverlay.addEventListener('click', function(e) {
            if (e.target === bookingModalOverlay) {
                closeModal();
            }
        });

        // Add Booking Form submissions
        btnSubmitBooking.addEventListener('click', submitBookingForm);
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBookingForm();
        });

        // Delete Booking
        btnDeleteBooking.addEventListener('click', deleteBooking);

        // Quick booking trigger
        if (btnQuickBook) {
            btnQuickBook.addEventListener('click', function() {
                const todayFormatted = new Date().toISOString().split('T')[0];
                openNewBookingModal(todayFormatted);
            });
        }
    }

    // Initialize layout rendering
    initCalendar();
});
