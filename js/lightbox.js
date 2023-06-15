function openLightbox(event, image) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');

    lightboxImage.src = image.src;
    lightbox.style.display = 'flex';
    event.stopPropagation(); // Prevent event bubbling to the lightbox element

    lightboxImage.classList.remove('zoomed'); // Reset zoom
    lightboxImage.addEventListener('click', toggleZoom);
}

function closeLightbox(event) {
    if (event.target.id === 'lightbox') {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');

        lightboxImage.src = '';
        lightbox.style.display = 'none';
        lightboxImage.removeEventListener('click', toggleZoom);
    }
}

function toggleZoom(event) {
    const lightboxImage = event.target;

    if (lightboxImage.classList.contains('zoomed')) {
        lightboxImage.classList.remove('zoomed');
    } else {
        lightboxImage.classList.add('zoomed');
    }
}