// Updated to use correct image paths
const IMAGE_BASE_PATH = '/assets/images/'; 

// Fetch posts from the backend
async function fetchPosts() {
    try {
        const response = await fetch('/includes/post_operations/fetch_posts.php');
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const posts = await response.json();
        return posts.map(post => {
            // Ensure consistent image URLs
            return {
                ...post,
                // Use image_url if available, otherwise construct path
                image: post.image_url || `${IMAGE_BASE_PATH}${encodeURIComponent(post.image)}`
            };
        });
    } catch (error) {
        console.error("Error fetching posts:", error);
        return [];
    }
}

// Generate slides from the fetched posts
function generateSlides(posts) {
    const carouselInner = document.getElementById('carousel-inner');
    if (!carouselInner) return;
    
    carouselInner.innerHTML = ''; 

    posts.forEach((post, index) => {
        const slide = document.createElement('div');
        slide.className = index === 0 ? 'carousel-item active' : 'carousel-item';

        const card = document.createElement('div');
        card.className = 'card';

        const image = document.createElement('img');
        // Use the properly formatted image path
        image.src = post.image.startsWith('http') ? post.image : 
                   post.image.startsWith('/') ? post.image : 
                   `${IMAGE_BASE_PATH}${encodeURIComponent(post.image)}`;
        image.alt = post.title;
        image.className = 'd-block w-100';
        image.onerror = function() {
            this.src = `${IMAGE_BASE_PATH}default.jpg`;
        };

        const title = document.createElement('h3');
        title.textContent = post.title;

        const seeMoreButton = document.createElement('button');
        seeMoreButton.className = 'see-more-button';
        seeMoreButton.textContent = 'See More';
        
        seeMoreButton.addEventListener('click', () => {
            if (confirm('Are you sure you want to continue to the home page?')) {
                window.location.href = 'register.php';
            }
        });

        card.appendChild(image);
        card.appendChild(title);
        card.appendChild(seeMoreButton);
        slide.appendChild(card);
        carouselInner.appendChild(slide);
    });
}

// Initialize Carousel after slides are generated
async function initializeCarousel() {
    const posts = await fetchPosts();
    if (posts.length > 0) {
        generateSlides(posts);
        
        new bootstrap.Carousel('#carouselExampleSlidesOnly', {
            interval: 2000,
            wrap: true
        });
    } else {
        console.warn("No posts found for carousel");
    }
}

// Initialize when DOM is ready
if (document.readyState !== 'loading') {
    initializeCarousel();
} else {
    document.addEventListener('DOMContentLoaded', initializeCarousel);
}