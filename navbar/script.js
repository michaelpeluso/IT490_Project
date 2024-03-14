function navigateToSection() {
    const hash = window.location.hash;
    const section = document.querySelector(hash);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

// Listen for hashchange event and navigate to section
window.addEventListener('hashchange', navigateToSection);

// Navigate to initial section if hash is present
if (window.location.hash) {
    navigateToSection();
}