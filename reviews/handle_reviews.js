document.addEventListener('DOMContentLoaded', function() {
    
    // fetch and display item reviews
    fetch(`fetch_reviews.php?service_id=${service_id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error retreiving database data.');
            }
            return response.json();
        })
        .then(data => {
        	// display data to user
            displayReviews(data);
        })
        .catch(error => {
            console.error('Error fetching reviews:', error);
        });
    }

    // Function to display reviews
    function displayReviews(reviews) {
        const reviewsContainer = document.getElementById('itemReviews');
        reviewsContainer.innerHTML = ''; // Clear previous reviews

        reviews.forEach(review => {
            const reviewElement = document.createElement('div');
            // reviewElement.classList.add('review');
            reviewElement.innerHTML = `
                <p><strong>Rating:</strong> ${review.rating_value}</p>
                <p><strong>Review:</strong> ${review.review_body}</p>
            `;
            reviewsContainer.appendChild(reviewElement);
        });
    }

    // Function to handle form submission
    function submitReview(event) {
        // do something
    }

    // fetch reviews when the page loads
    const service_id = 123; // replace with actual item id
    fetchReviews(service_id);

    // call submitReview() on form submission
    document.getElementById('reviewForm').addEventListener('submit', submitReview);
});

