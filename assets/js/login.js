document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault(); 

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if (email.trim() === "" || password.trim() === "") {
        alert("Please fill in all fields.");
        return;
    }

    alert("Login successful!");
    // Later, you can replace this with an AJAX request to verify credentials with PHP.
});
