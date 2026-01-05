// File: slider.js

document.addEventListener('DOMContentLoaded', function () {
    // 1. Lấy các phần tử cần thiết
    const sliderTrack = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const dotsContainer = document.querySelector('.dots-container');
    const slideCount = slides.length;
    let currentIndex = 0;

    // ĐÃ SỬA: Điều chỉnh tốc độ chuyển slide xuống 3 giây (3000ms)
    const slideInterval = 3000;
    let autoSlideTimer;

    // Kiểm tra nếu không tìm thấy các phần tử, dừng script để tránh lỗi
    if (!sliderTrack || slideCount === 0) {
        console.error("Slider elements not found. Check HTML classes.");
        return;
    }

    // --- 2. Hàm Cập nhật Slide ---
    function updateSlide() {
        // Tính toán vị trí dịch chuyển (dùng transform X)
        const offset = -currentIndex * 100;
        sliderTrack.style.transform = `translateX(${offset}%)`;

        // Cập nhật dấu chấm
        updateDots();
    }

    // --- 3. Hàm chuyển đến Slide tiếp theo ---
    function nextSlide() {
        currentIndex = (currentIndex + 1) % slideCount;
        updateSlide();
    }

    // --- 4. Thiết lập chế độ tự động chạy ---
    function startAutoSlide() {
        // Xóa timer cũ nếu có
        clearInterval(autoSlideTimer);
        // Thiết lập timer mới
        autoSlideTimer = setInterval(nextSlide, slideInterval);
    }

    // --- 5. Xử lý nút bấm (Chỉ khi nút tồn tại) ---
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + slideCount) % slideCount;
            updateSlide();
            startAutoSlide(); // Khởi động lại timer sau khi nhấn nút
        });

        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoSlide(); // Khởi động lại timer sau khi nhấn nút
        });
    }

    // --- 6. Xử lý Dấu chấm (Chỉ khi dotsContainer tồn tại) ---
    if (dotsContainer) {
        // Tạo các dấu chấm (dot)
        for (let i = 0; i < slideCount; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            dot.dataset.index = i;
            dot.addEventListener('click', (e) => {
                currentIndex = parseInt(e.target.dataset.index);
                updateSlide();
                startAutoSlide(); // Khởi động lại timer sau khi nhấn
            });
            dotsContainer.appendChild(dot);
        }
    }


    // Hàm cập nhật dấu chấm
    function updateDots() {
        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            dot.classList.remove('active');
            if (index === currentIndex) {
                dot.classList.add('active');
            }
        });
    }

    // 7. Bắt đầu slider khi tải trang
    startAutoSlide();
    updateSlide(); // Hiển thị slide đầu tiên

    // 8. Xử lý tương tác chuột (Dừng/Tiếp tục tự động chạy)
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlideTimer);
        });

        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
});