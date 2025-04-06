export function initializeSpinner() {
    const spinner = document.getElementById('spinner');

    if (!spinner) {
        console.error("Spinner element not found!");
        return;
    }

    // Intercept all link clicks
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior

            // Show the spinner
            spinner.style.display = 'block';

            // Get the target URL
            const targetUrl = link.getAttribute('href');

            // Delay the page transition 
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 900); // 0.9 second
        });
    });
}

