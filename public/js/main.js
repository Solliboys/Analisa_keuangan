// Toggle Mobile Menu
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.getElementById('navMenu');
if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('show');
    });
}

// Close mobile menu on link click
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (navMenu) navMenu.classList.remove('show');
    });
});

// Modal Functionality
const demoModal = document.getElementById('demoModal');
function openModal(e) {
    if (e) e.preventDefault();
    if (demoModal) demoModal.classList.add('active');
}

function closeModal() {
    if (demoModal) demoModal.classList.remove('active');
}

if (demoModal) {
    demoModal.addEventListener('click', (e) => {
        if (e.target === demoModal) {
            closeModal();
        }
    });
}

function handleDemoSubmit(e) {
    e.preventDefault();
    alert('Terima kasih! Permintaan demo Anda telah diterima. Tim OTW Kredit akan segera menghubungi Anda.');
    closeModal();
}

// Star Rating Selection
function setRating(val) {
    const ratingInput = document.getElementById('selectedRating');
    if (ratingInput) ratingInput.value = val;

    const starButtons = document.querySelectorAll('.star-btn');
    starButtons.forEach(btn => {
        const btnVal = parseInt(btn.getAttribute('data-val'), 10);
        if (btnVal <= val) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Review Form Submission Handler
function handleReviewSubmit(e) {
    e.preventDefault();

    const nameInput = document.getElementById('reviewerName');
    const commentInput = document.getElementById('reviewerComment');
    const ratingInput = document.getElementById('selectedRating');
    const testimonialsGrid = document.getElementById('testimonialsGrid');
    const successAlert = document.getElementById('reviewSuccessAlert');

    const name = nameInput.value.trim();
    const comment = commentInput.value.trim();
    const rating = parseInt(ratingInput.value, 10) || 5;

    if (!name || !comment) {
        alert('Silakan isi Nama Lengkap dan Ulasan Anda terlebih dahulu.');
        return;
    }

    // Create Star String
    let starsStr = '';
    for (let i = 1; i <= 5; i++) {
        starsStr += (i <= rating) ? '★' : '☆';
    }

    // Generate Initials
    const words = name.split(' ').filter(w => w.length > 0);
    let initials = 'U';
    if (words.length >= 2) {
        initials = (words[0][0] + words[1][0]).toUpperCase();
    } else if (words.length === 1) {
        initials = words[0].substring(0, 2).toUpperCase();
    }

    // Create new Testimonial Card Element
    const newCard = document.createElement('div');
    newCard.className = 'testimonial-card';
    newCard.style.animation = 'fadeIn 0.5s ease-out';
    newCard.style.borderColor = 'var(--primary-border)';
    newCard.style.boxShadow = '0 10px 25px -5px rgba(37, 99, 235, 0.12)';

    // Sanitize text
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    newCard.innerHTML = `
        <div>
            <div class="stars-row">${starsStr}</div>
            <p class="testimonial-text">"${escapeHTML(comment)}"</p>
        </div>
        <div class="testimonial-author">
            <div class="author-avatar" style="background: linear-gradient(135deg, #BFDBFE, #60A5FA); color: #1E3A8A;">${initials}</div>
            <div class="author-info">
                <span class="author-name">${escapeHTML(name)}</span>
                <span class="author-role">Pengguna Terverifikasi</span>
            </div>
        </div>
    `;

    // Append to testimonials grid
    if (testimonialsGrid) {
        testimonialsGrid.appendChild(newCard);
    }

    // Show success alert
    if (successAlert) {
        successAlert.style.display = 'flex';
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 6000);
    }

    // Reset form inputs
    nameInput.value = '';
    commentInput.value = '';
    setRating(5);

    // Smooth scroll to the new card
    newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
