function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('collapsed');
}

// Gắn toSlug vào window để dùng trong HTML onclick
window.toSlug = function(str) {
    return str
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
};

// Tự động tạo slug khi nhập tên phim
document.addEventListener('DOMContentLoaded', function () {
    const nameVNInput = document.getElementById('name_vn');
    const slugInput = document.getElementById('slug_movie');

    if (nameVNInput && slugInput) {
        nameVNInput.addEventListener('input', function () {
            slugInput.value = window.toSlug(this.value);
        });
    }
});
// Tự động tạo slug khi nhập tên thể loại
document.addEventListener('DOMContentLoaded', function () {
    const nameCatInput = document.getElementById('name_cat');
    const slugCatInput = document.getElementById('slug_cat');

    if (nameCatInput && slugCatInput) {
        nameCatInput.addEventListener('input', function () {
            slugCatInput.value = window.toSlug(this.value);
        });
    }
});
// Tự động tạo slug khi nhập tên quốc gia
document.addEventListener('DOMContentLoaded', function () {
    const nameCountryInput = document.getElementById('name_country');
    const slugCountryInput = document.getElementById('slug_country');

    if (nameCountryInput && slugCountryInput) {
        nameCountryInput.addEventListener('input', function () {
            slugCountryInput.value = window.toSlug(this.value);
        });
    }
});
// Tự động tạo slug khi nhập tên nhãn phim
document.addEventListener('DOMContentLoaded', function () {
    const nameTagInput = document.getElementById('name_tag');
    const slugtagInput = document.getElementById('slug_tag');

    if (nameTagInput && slugtagInput) {
        nameTagInput.addEventListener('input', function () {
            slugtagInput.value = window.toSlug(this.value);
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const nameInputs = document.querySelectorAll('[id^="name_cat_edit_"]');
    
    nameInputs.forEach(function (input) {
        const idSuffix = input.id.replace('name_cat_edit_', '');
        const slugInput = document.getElementById('slug_cat_edit_' + idSuffix);

        if (slugInput) {
            input.addEventListener('input', function () {
                slugInput.value = window.toSlug(this.value);
            });
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const nameInputs = document.querySelectorAll('[id^="name_tag_edit_"]');
    
    nameInputs.forEach(function (input) {
        const idSuffix = input.id.replace('name_tag_edit_', '');
        const slugInput = document.getElementById('slug_tag_edit_' + idSuffix);

        if (slugInput) {
            input.addEventListener('input', function () {
                slugInput.value = window.toSlug(this.value);
            });
        }
    });
});
function nextTab(id) {
    const currentTab = document.querySelector('.tab-pane.active');
    if (currentTab) {
        const visibleFields = currentTab.querySelectorAll('input, select, textarea');
        for (const field of visibleFields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return; // Không hợp lệ thì dừng lại
            }
        }
    }

    const trigger = document.querySelector(`[data-bs-target="#${id}"]`);
    if (trigger) {
        bootstrap.Tab.getOrCreateInstance(trigger).show();
    }
}

let episodeIndex = 0;

document.addEventListener('DOMContentLoaded', function () {
    const episodeList = document.getElementById('episode-list');
    if (episodeList && episodeList.dataset.episodeCount) {
        episodeIndex = parseInt(episodeList.dataset.episodeCount);
    }
});
let deletedEpisodeIds = [];

function markEpisodeDeleted(button) {
    const episodeDiv = button.closest('.episode-item');
    const episodeId = episodeDiv.dataset.episodeId;

    if (episodeId) {
        deletedEpisodeIds.push(episodeId);
        document.getElementById('deleted_episodes').value = deletedEpisodeIds.join(',');
    }

    episodeDiv.remove();
}
document.addEventListener('DOMContentLoaded', function () {
    const tabStep3 = document.querySelector('[data-bs-target="#step3"]'); // nút chuyển tới step3
    if (tabStep3) {
        tabStep3.addEventListener('shown.bs.tab', function () {
            if (!document.querySelector('#categories').tomselect) {
                new TomSelect('#categories', {
                    plugins: ['remove_button'],
                    maxItems: null,
                    create: false,
                    placeholder: 'Chọn thể loại...'
                });
            }

            if (!document.querySelector('#countries').tomselect) {
                new TomSelect('#countries', {
                    plugins: ['remove_button'],
                    maxItems: null,
                    create: false,
                    placeholder: 'Chọn quốc gia...'
                });
            }
        });
    }
});


function addEpisode() {
    const container = document.getElementById('episode-list');
    const div = document.createElement('div');
    div.classList.add('episode-item', 'mb-3', 'border', 'p-3', 'rounded', 'position-relative');

    div.innerHTML = `
        <button type="button" class="btn-close position-absolute top-0 end-0" aria-label="Xóa" onclick="this.parentElement.remove()"></button>

        <label class="form-label">Tên tập</label>
        <input type="text" name="episodes[${episodeIndex}][title]" placeholder="Tên tập" class="form-control mb-2" required>

        <label class="form-label">Link video</label>
        <input type="text" name="episodes[${episodeIndex}][url]" placeholder="Link video" class="form-control" required>
    `;
    container.appendChild(div);
    episodeIndex++;
}
function previewImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('change', function () {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    previewImage('poster_input', 'poster_preview');
    previewImage('thum_input', 'thum_preview');
    previewImage('banner_input', 'banner_preview');
});

document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-movie-form');

    deleteForms.forEach(form => {
        const button = form.querySelector('button');
        const movieName = button.dataset.movieName || 'phim này';

        button.addEventListener('click', function (e) {
            Swal.fire({
                title: `Xóa "${movieName}"?`,
                text: "Thao tác này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-episode-form');

    deleteForms.forEach(form => {
        const button = form.querySelector('button');
        const episodeName = button.dataset.episodeName || 'tập phim này';

        button.addEventListener('click', function (e) {
            Swal.fire({
                title: `Xóa tập "${episodeName}"?`,
                text: "Thao tác này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-category-form');

    deleteForms.forEach(form => {
        const button = form.querySelector('button');
        const categoryName = button.dataset.categoryName || 'thể loại phim này';

        button.addEventListener('click', function (e) {
            Swal.fire({
                title: `Xóa thể loại "${categoryName}"?`,
                text: "Thao tác này không thể hoàn tác",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-country-form');

    deleteForms.forEach(form => {
        const button = form.querySelector('button');
        const conutryName = button.dataset.countryName || 'quốc gia phim này';

        button.addEventListener('click', function (e) {
            Swal.fire({
                title: `Xóa quốc gia "${conutryName}"?`,
                text: "Thao tác này không thể hoàn tác",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-tag-form');

    deleteForms.forEach(form => {
        const button = form.querySelector('button');
        const tagName = button.dataset.tagName || 'nhãn phim này';

        button.addEventListener('click', function (e) {
            Swal.fire({
                title: `Xóa nhãn "${tagName}"?`,
                text: "Thao tác này không thể hoàn tác",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});