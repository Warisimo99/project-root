// Base path to the images folder
const IMAGE_BASE_PATH = 'public/assets/images/';

// Fetch posts from the backend
async function fetchPosts() {
    try {
        const response = await fetch('includes/post_operations/fetch_posts.php');
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const posts = await response.json();
        return posts;
    } catch (error) {
        console.error("Error fetching posts:", error);
        return [];
    }
}

// Generate slides from the fetched posts
function generateSlides(posts) {
    const carouselInner = document.getElementById('carousel-inner');
    carouselInner.innerHTML = ''; 

    posts.forEach((post, index) => {
        const slide = document.createElement('div');
        slide.className = index === 0 ? 'carousel-item active' : 'carousel-item'; 

        const card = document.createElement('div');
        card.className = 'card';

        const image = document.createElement('img');
        image.src = post.image; 
        image.alt = post.title;
        image.className = 'd-block w-100'; 

        const title = document.createElement('h3');
        title.textContent = post.title;

        const seeMoreButton = document.createElement('button');
        seeMoreButton.className = 'see-more-button';
        seeMoreButton.textContent = 'See More';

        
        seeMoreButton.addEventListener('click', () => {
            // Show a confirmation dialog
            const isConfirmed = confirm('Are you sure you want to continue to the home page?');
            if (isConfirmed) {
                // Redirect to the home page if the user confirms
                window.location.href = 'register.php'; 
            } else {
                // Do nothing if the user cancels
                console.log('User canceled the action.');
            }
        });

        card.appendChild(image);
        card.appendChild(title);
        slide.appendChild(card);
        card.appendChild(seeMoreButton);
        carouselInner.appendChild(slide);
    });
}

// Initialize Carousel after slides are generated
async function initializeCarousel() {
    const posts = await fetchPosts();
    generateSlides(posts);

    // Initialize Bootstrap Carousel 
    const carousel = new bootstrap.Carousel('#carouselExampleSlidesOnly', {
        interval: 2000, // 2 seconds
        wrap: true, // Loop the carousel
    });
}

// Initialize the carousel when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
    initializeCarousel();
});