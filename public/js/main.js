window.addEventListener("scroll", function () {
    const navbar = document.getElementById("mainNav");
    if (window.scrollY > 50) {
        navbar.classList.add("navbar-scrolled");
    } else {
        navbar.classList.remove("navbar-scrolled");
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-input');
    const resultBox = document.querySelector('.autocomplete-results');

    input.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length < 2) {
            resultBox.innerHTML = '';
            return;
        }

        fetch(`/autocomplete?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resultBox.innerHTML = '';
                data.forEach(movie => {
                    const item = document.createElement('li');
                    item.className = 'p-2 text-white search-item';
                    item.textContent = movie.name_vn;
                    item.style.cursor = 'pointer';
                    item.addEventListener('click', () => {
                        window.location.href = `/${movie.slug_movie}`;
                    });
                    resultBox.appendChild(item);
                });
            });
    });

    // Ẩn gợi ý khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!document.getElementById('search-form').contains(e.target)) {
            resultBox.innerHTML = '';
        }
    });
});
