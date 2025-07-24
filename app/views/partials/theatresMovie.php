<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-white text-uppercase fw-bold">🎬 Phim mới cập nhật</h5>
    <a href="#" class="btn btn-sm btn-secondary">Xem tất cả</a>
  </div>

  <div class="row g-3">
    {% for movie in singleMovies %}
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <div class="card bg-dark text-white border-0 rounded overflow-hidden position-relative">
        <a href="/{{ movie.slug_movie | e }}" class="text-decoration-none">
          <!-- Poster -->
          <img src="/img/posters/{{ movie.poster_url | e }}" alt="{{ movie.name_vn | e }}"
               class="card-img-top img-fluid" style="height:270px; object-fit:cover;">

          <!-- Label chất lượng -->
          <span class="badge bg-danger position-absolute top-0 start-0 rounded-0 px-2 py-1 small">
            {{ movie.quality | default('Full | Vietsub | FHD') }}
          </span>

          <!-- Tên phim -->
          <div class="card-img-overlay d-flex align-items-end p-2"
               style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
            <h6 class="card-title text-white mb-0 small fw-bold">
              {{ movie.name_vn | e }}
            </h6>
          </div>
        </a>
      </div>
    </div>
    {% endfor %}
  </div>
</div>
