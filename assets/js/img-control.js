
let slideIndex = 1;
showSlides();

// Autoplay settings
const autoplayInterval = 4000; 
let slideInterval = setInterval(function() {
    plusSlides(1); 
}, autoplayInterval);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    
    // Hide all slides
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    // Show the current slide
    slides[slideIndex-1].style.display = "block";
}

