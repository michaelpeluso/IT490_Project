// global variables
const max_review_length = 250; // characters

// Sample data (replace with actual data fetched from server)
const reviews = [
    { user_id: "user1", service_id: "service1", rating_text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.", rating_value: 4 },
    {
        user_id: "user2",
        service_id: "service2",
        rating_text:
            "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.",
        rating_value: 5,
    },
    { user_id: "user3", service_id: "service3", rating_text: "Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.", rating_value: 3 },
];

// fetch and display item reviews
function fetchReviews() {
    $.ajax({
        url: "sample_reviews.json", // testing only
        type: "GET",
        dataType: "json",
        success: function (data) {
            // display data to user
            displayReviews(data);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching reviews:", error);
        },
    });
}

// Function to display reviews
function displayReviews() {
    reviewsContainer.innerHTML = ""; // Clear previous reviews

    reviews.forEach((review) => {
        const reviewElement = document.createElement("div");
        reviewElement.classList.add("review");

        let reviewText = review.rating_text;
        let readMoreLink = "";

        // Check if review text length exceeds 75 characters
        if (reviewText.length > 75) {
            reviewText = reviewText.substring(0, max_review_length) + "..."; // Truncate review text
            readMoreLink = '<a href="#" class="read-more">Read more</a>'; // Add read more link
        }

        //

        // review html
        reviewElement.innerHTML = `
            <div class="review-cell">
                <div>
                    <h2 class="review-userid"> ${review.user_id}</h2>
                    <h3 class="review-serviceid"><a href="${review.service_id}.html">${review.service_id}</a></h3>
                </div>
                <div class="stars">
                    <span class="star" data-value="1"></span>
                    <span class="star" data-value="2"></span>
                    <span class="star" data-value="3"></span>
                    <span class="star" data-value="4"></span>
                    <span class="star" data-value="5"></span>
                </div>
                <p><strong>Rating:</strong> ${review.rating_value}</p>
                <p>${reviewText}</p>
                <p>${readMoreLink}</p>
            </div>
        `;

        reviewsContainer.appendChild(reviewElement);

        // update star rating
        const stars = reviewElement.querySelectorAll(".star");
        stars.forEach((star, i) => {
            if (i < review.rating_value) {
                star.style.backgroundColor = "black";
            }
        });
    });
}

// Function to handle form submission
function submitReview(event) {
    // do something
}

const reviewsContainer = document.getElementById("reviews-container");

// fetch reviews when the page loads
const service_id = 123; // replace with actual item id

document.addEventListener("DOMContentLoaded", function () {
    fetchReviews(service_id);
});
