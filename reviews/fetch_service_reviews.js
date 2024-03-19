// global variables
const max_review_length = 300; // characters

// fetch service reviews
function fetchReviews() {
    $.ajax({
        url: "fetch_service_reviews.php",
        type: "GET",
        success: function (data) {
            // display data to user
            displayReviews(data);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching reviews:", error);
            reviewsContainer.innerHTML = `
                <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb;">
                    Error: Something went wrong fetching your reviews.
                </p>
            `;
        },
    });
}

// Function to display reviews
function displayReviews(reviews) {
    reviewsContainer.innerHTML = ""; // Clear previous reviews
	
	
	reviews = [
				{
					'user_id' : '1234',
					'service_id' : '1234',
					'review_rating' : '4',
					'review_body' : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Vestibulum lectus mauris ultrices eros in cursus turpis massa tincidunt. Arcu ac tortor dignissim convallis aenean et tortor. Mauris in aliquam sem fringilla ut morbi tincidunt augue. In hendrerit gravida rutrum quisque non tellus orci ac auctor. Cursus euismod quis viverra nibh cras. Vel eros donec ac odio tempor orci dapibus ultrices in. Netus et malesuada fames ac turpis egestas integer eget aliquet. Eros in cursus turpis massa tincidunt. Sed euismod nisi porta lorem mollis aliquam. Tortor at risus viverra adipiscing. Eget sit amet tellus cras adipiscing enim. Felis eget velit aliquet sagittis id consectetur purus ut faucibus. Sed lectus vestibulum mattis ullamcorper. Aliquet risus feugiat in ante metus. Porttitor lacus luctus accumsan tortor posuere ac ut consequat. Nisl suscipit adipiscing bibendum est. Et ligula ullamcorper malesuada proin libero. Enim facilisis gravida neque convallis a.',
					'review_date' : '2/12/2024'
				}
			];
			
			
    reviews.forEach((review) => {
        const reviewElement = document.createElement("div"); 
        reviewElement.classList.add("review");

        // add read more to long reviews
        let reviewText = review.review_body;
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
                <p class="review-text">${review.review_date}</p>
            </div>
        `;
        reviewsContainer.appendChild(reviewElement);

        // update star rating
        const stars = reviewElement.querySelectorAll(".star");
        stars.forEach((star, i) => {
            if (i < review.review_rating) {
                star.style.backgroundColor = "black";
            }
        });

        // add event listener to read more
        const readMoreButton = reviewElement.querySelector(".read-more");
        if (readMoreButton) {
            readMoreButton.addEventListener("click", function () {
                $(reviewElement).find(".read-more").text("Close");

                const clonedReview = $(reviewElement).clone();
                clonedReview.find(".review-text").text(review.review_body).removeClass("review-text");

                $("#modal-review").html(clonedReview);
                $("#modal").css("display", "block");

                // close modal
                $(".read-more").click(() => {
                    const isModalVisible = $("#modal").toggle().is(":visible");
                    $(".read-more").text(isModalVisible ? "Close" : "Read more");
                });
            });
        }
    });
}

const reviewsContainer = document.getElementById("reviews-container");

// fetch reviews when the page loads
document.addEventListener("DOMContentLoaded", function () {
    displayReviews("asdfr");
});

// close modal when click off
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};
