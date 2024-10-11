document.getElementById("appointmentForm").addEventListener("submit", function(event) {
    var name = document.getElementById("name").value;
    var phone = document.getElementById("phone").value;

    // Basic phone number validation (example)
    var phonePattern = /^[0-9]{10}$/;
    if (!phone.match(phonePattern)) {
        alert("Please enter a valid 10-digit phone number.");
        event.preventDefault(); // Prevent the form from submitting
    }
});
