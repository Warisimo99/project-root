document.addEventListener('DOMContentLoaded', function () {
    const signUpButton = document.getElementById('signUpButton');
    const signInButton = document.getElementById('signInButton');
    const signInForm = document.getElementById('signin');
    const signUpForm = document.getElementById('signup');

    if (signUpButton && signInButton && signInForm && signUpForm) {
        // Toggle between forms
        signUpButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default button behavior
            signInForm.style.display = "none";
            signUpForm.style.display = "block";
        });

        signInButton.addEventListener('click', function (event) {
            event.preventDefault(); 
            signInForm.style.display = "block";
            signUpForm.style.display = "none";
        });

        // Client-side validation for Sign Up form
        const signUpFormElement = document.querySelector('#signup');
        if (signUpFormElement) {
            signUpFormElement.addEventListener('submit', function (event) {
                const email = document.querySelector('#signup input[name="email"]').value;
                const password = document.querySelector('#signup input[name="password"]').value;
                const confirmPassword = document.querySelector('#signup input[name="confirm_password"]').value;


                if (!validateEmail(email)) {
                    event.preventDefault(); 
                    alert("Please enter a valid email address.");
                }

                if (password.length < 8) {
                    event.preventDefault(); 
                    alert("Password must be at least 8 characters long.");
                }

                if (password !== confirmPassword) {
                    event.preventDefault(); 
                    alert("Passwords do not match.");
                }
            });
        }

        // Ensure Sign In form submits correctly
        const signInFormElement = document.querySelector('#signin');
        if (signInFormElement) {
            signInFormElement.addEventListener('submit', function (event) {
                const email = document.querySelector('#signin input[name="email"]').value;
                const password = document.querySelector('#signin input[name="password"]').value;

                if (!email || !password) {
                    event.preventDefault(); // Prevent form submission
                    alert("Email and password are required.");
                }
            });
        }
    } else {
        console.error("One or more elements are missing in the DOM.");
    }
});

// function to validate email
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}
