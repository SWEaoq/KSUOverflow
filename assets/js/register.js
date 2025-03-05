document.getElementById("register-form").addEventListener("submit", function(event) {
    event.preventDefault(); 

    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm-password").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    alert("Registration successful!");
    // Here you can add AJAX to send data to the backend later.
});
