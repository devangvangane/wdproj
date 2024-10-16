function validateAppointmentDate() {
    // Get today's date
    let today = new Date();
    today.setHours(0, 0, 0, 0); // Set time to midnight to avoid issues with time comparison
    
    // Get the selected appointment date
    let appointmentDate = new Date(document.getElementById('date').value);
    
    // Compare the selected date with today's date
    if (appointmentDate < today) {
        // If the selected date is in the past, show an alert
        alert("You cannot select a date before today's date. Please choose a valid appointment date.");
        return false; // Prevent form submission
    }
    
    // Allow form submission if the date is valid
    return true;
}

// Attach the function to the form's submit event
document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.querySelector('form');
    
    // Attach the validation function to the form's submit event
    form.onsubmit = function() {
        return validateAppointmentDate();
    };
});


function updateAppointmentStatus() {
    // Get today's date without the time
    let today = new Date();
    today.setHours(0, 0, 0, 0); // Set to midnight to avoid time-related issues

    // Get all rows from the table
    let table = document.getElementById('appointmentsTable');
    let rows = table.getElementsByTagName('tr');

    // Loop through the rows, skipping the header row
    for (let i = 1; i < rows.length; i++) {
        // Get the date cell and the status cell
        let dateCell = rows[i].getElementsByTagName('td')[0]; // First cell is the date
        let statusCell = rows[i].getElementsByClassName('status')[0]; // Status cell

        // Parse the date from the cell
        let appointmentDate = new Date(dateCell.innerText);

        // Compare the appointment date with today's date
        if (appointmentDate < today) {
            statusCell.innerText = 'Completed'; // Set to Completed if before today
            statusCell.style.color = 'green';   // Optional: change text color to green for completed
        } else {
            statusCell.innerText = 'Pending';   // Set to Pending if after today
            statusCell.style.color = 'orange';  // Optional: change text color to orange for pending
        }
    }
}

// Call the function once the document has loaded
document.addEventListener('DOMContentLoaded', function() {
    updateAppointmentStatus(); // Update statuses on page load
});
