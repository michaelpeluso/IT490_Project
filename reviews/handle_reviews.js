// global variables
const max_review_length = 300; // characters

// Sample data (replace with actual data fetched from server)
// Sample data (replace with actual data fetched from server)
const reviews = [
    { user_id: "user1", service_id: "service1", rating_text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.", rating_value: 4 },
    {
        user_id: "user2",
        service_id: "service2",
        rating_text:
            "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.",
        rating_value: 5,
    },
    { user_id: "user3", service_id: "service3", rating_text: "Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.", rating_value: 3 },
    { user_id: "user4", service_id: "service4", rating_text: "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.", rating_value: 4 },
    { user_id: "user5", service_id: "service5", rating_text: "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", rating_value: 2 },
    { user_id: "user6", service_id: "service6", rating_text: "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.", rating_value: 5 },
    { user_id: "user7", service_id: "service7", rating_text: "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", rating_value: 3 },
    { user_id: "user8", service_id: "service8", rating_text: "Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.", rating_value: 4 },
    { user_id: "user9", service_id: "service9", rating_text: "Nulla quis sem at nibh elementum imperdiet.", rating_value: 2 },
    { user_id: "user10", service_id: "service10", rating_text: "Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.", rating_value: 5 },
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

        // add read more to long reviews
        let reviewText = review.rating_text;
        let readMoreLink = "<br/>";

        if (reviewText.length > max_review_length) {
            reviewText = reviewText.substring(0, max_review_length) + "...";
            readMoreLink = '<a href="#" class="read-more">Read more</a>';
        }

        // review html
        reviewElement.innerHTML = `
            <div class="review-cell">
                <div style="display: flex; justify-content: space-between">
                    <h3 class="review-userid"> ${review.user_id}</h3>
                    <h3 class="review-serviceid"><a href="${review.service_id}.html">${review.service_id}</a></h3>
                </div>
                <div class="stars" style="display: flex; justify-content: center">
                    <span class="star" data-value="1"></span>
                    <span class="star" data-value="2"></span>
                    <span class="star" data-value="3"></span>
                    <span class="star" data-value="4"></span>
                    <span class="star" data-value="5"></span>
                </div>
                <p class="review-text">${reviewText}</p>
                ${readMoreLink}
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
