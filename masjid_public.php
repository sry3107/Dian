<?php
session_start();
include 'koneksi.php';

// Ambil data masjid dari tabel
$query = $conn->prepare("SELECT id_masjid, COALESCE(nama_masjid, '') AS nama_masjid, COALESCE(alamat_masjid, '') AS alamat_masjid, COALESCE(tahun_berdiri, '') AS tahun_berdiri, COALESCE(created_at, '') AS created_at, COALESCE(updated_at, '') AS updated_at FROM masjid");
$query->execute();
$result = $query->get_result();

// Jika ada data, ambil data masjid pertama
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Masjid</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Montserrat', sans-serif;
    background-color: #fff3ee;
}

.hero-gradient {
    background: linear-gradient(135deg, #16a34a 10%, #065f46 100%);
}

.card {
    background: #fff3ee;
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: scale(1.05);
}

nav {
    background: #FFFFEC;
    position: sticky;
    top: 0;
    z-index: 50;
}

nav .bg-white {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

nav a {
    color: #4a5568; 
    padding: 0.5rem 1rem; 
    border-radius: 0.5rem; 
}

nav a:hover {
    background-color: rgba(31, 41, 55, 0.1); 
}

footer {
    background-color: #FFFFEC;
    border-top: 1px solid #e2e8f0; 
}

.swiper-container {
    width: 100%;
    height: 400px; 
    position: relative;
    overflow: hidden;
    border-radius: 10px; 
}

.swiper-wrapper {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.swiper-slide {
    display: flex;
    justify-content: center; 
    align-items: center;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    position: absolute; 
    width: 100%; 
    height: 100%; 
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.swiper-slide-active {
    opacity: 1; 
}

.swiper-slide img {
    max-width: 100%;
    height: auto; 
    object-fit: cover; 
    border-radius: 8px;
}

.table-auto {
    border-collapse: collapse; 
    width: 100%;
}

.table-auto th {
    vertical-align: top;
    text-align: left;
}

.table-auto td {
    text-align: left;
}

.table-auto th,
.table-auto td {
    padding: 10px; 
    position: relative;
}

.table-auto .border-b {
    border-bottom: 1px solid #e2e8f0; 
    width: 100%; 
}
</style>

<body>
    <!-- Navbar -->
    <nav class="background shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-moon text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-xl text-gray-800">SINARMU</h1>
                        <p class="text-xs text-green-600">Terangi Imanmu</p>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-20">
                    <div class="relative">
                        <div id="Home" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <a href="home_public.php">
                                <i class="fas fa-home mr-2"></i>Home    
                            </a>   
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Profil" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-user-circle mr-2"></i>Profil
                        </div>
                        <div id="SubMenuProfil" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="masjid_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-mosque mr-2"></i>Masjid
                            </a>
                            <a href="pengurus_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-address-card mr-2"></i>Pengurus
                            </a>
                            <a href="mubaligh_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Mubaligh
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Activity" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-clipboard-list mr-2"></i>Kegiatan
                        </div>
                        <div id="SubMenuActivity" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="kegiatan_mingguan_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar mr-2"></i>Mingguan
                            </a>
                            <a href="kegiatan_bulanan_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-day mr-2"></i>Bulanan
                            </a>
                            <a href="kegiatan_tahunan_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-week mr-2"></i>Tahunan
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Schedule" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-calendar-alt mr-2"></i>Jadwal
                        </div>
                        <div id="SubMenuSchedule" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="khutbah_jumat_public.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-check mr-2"></i>Khutbah Jum'at
                            </a>
                        </div>
                    </div>
                </div>

        <!-- Login -->
        <div class="flex items-center gap-4">
            <a href="login.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Log In</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient py-16 text-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold mb-5" style="color: #fff3ee">MASJID</h1>
                    <h1 class="text-5xl font-bold mb-8">RAODHATUL MUFLIHIN</h1>
                    <p class="text-green-100 mb-20">Menyediakan informasi lengkap tentang jadwal ibadah, kajian, dan kegiatan masjid untuk memudahkan jamaah dalam beribadah.</p>
                    <h3 class="text-green-50" style="color: #FFFFEC">~Terangi imanmu dengan SINARMU~</h3>
                </div>

                <!-- Swiper Container -->
                <div class="relative w-full h-94 overflow-hidden">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="/sisfo-masjid/image/masjid1.png" alt="Gambar 1" class="rounded-lg shadow-lg"></div>
                            <div class="swiper-slide"><img src="/sisfo-masjid/image/masjid2.png" alt="Gambar 2" class="rounded-lg shadow-lg"></div>
                            <div class="swiper-slide"><img src="/sisfo-masjid/image/masjid3.png" alt="Gambar 3" class="rounded-lg shadow-lg"></div>
                            <div class="swiper-slide"><img src="/sisfo-masjid/image/masjid4.png" alt="Gambar 4" class="rounded-lg shadow-lg"></div>
                            <div class="swiper-slide"><img src="/sisfo-masjid/image/masjid5.jpg" alt="Gambar 5" class="rounded-lg shadow-lg"></div>
                        </div> <!-- End swiper-wrapper -->
                    </div> <!-- End swiper-container -->
                </div> <!-- End relative div -->
            </div> <!-- End grid div -->
        </div> <!-- End max-w div -->
    </section>

    <!-- Profil Masjid -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Profil Masjid</h2>
            </div>
            <div class="bg-white rounded-xl p-6">
                <h2 class="text-lg font-semibold mb-4">Profil:</h2>
                <div class="detail">
                    <table class="table-auto w-full">
                        <tbody>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4 font-medium">
                                    <i class="fas fa-mosque icon"></i> Nama Masjid
                                    <th>:</th>
                                </th>
                                <td class="py-2"><?php echo htmlspecialchars($data['nama_masjid'] ?? ''); ?></td>
                            </tr>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4 font-medium">
                                    <i class="fas fa-map-marker-alt mr-1.5"></i> Alamat Masjid
                                    <th>:</th>
                                </th>
                                <td class="py-2"><?php echo htmlspecialchars($data['alamat_masjid'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th class="text-left py-2 pr-4 font-medium">
                                    <i class="fas fa-calendar-alt mr-1"></i> Tahun Berdiri
                                    <th>:</th>
                                </th>
                                <td class="py-2"><?php echo htmlspecialchars($data['tahun_berdiri'] ?? ''); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="background border-t">
            <div class="max-w-7xl mx-auto px-6 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-moon text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">SINARMU</h4>
                                <p class="text-xs text-green-600">Terangi Imanmu</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Sistem Informasi Masjid Raodhatul Muflihin</p>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-4">Kontak</h5>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i>
                                    <a href="https://www.google.co.id/maps/place/Masjid+Raodhatul+Muflihin/@-4.8570717,119.5683597,17z/data=!3m1!4b1!4m6!3m5!1s0x2dbe50384d5f9bdf:0x94d60c3d24cec5d!8m2!3d-4.8570717!4d119.5709346!16s%2Fg%2F11cjh_1107?entry=ttu&g_ep=EgoyMDI0MTAyNy4wIKXMDSoASAFQAw%3D%3D" class="hover:text-green-600">
                                        Jl. Sultan Hasanuddin, Sanrangan, Baru-baru Towa
                                    </a>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-phone"></i>
                                    <a href="https://wa.me/6285299330534" class="hover:text-green-600">
                                        +62 852-9933-0534
                                    </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-5">Media Sosial</h5>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <i class="fab fa-facebook"></i>
                                    <a href="https://web.facebook.com/pages/Mesjid-Raodhatul-Muflihin-Sanrangan-Pangkep/2469976183028030" class="hover:text-green-600">
                                        Mesjid Raodhatul Muflihin Sanrangan Pangkep      
                                    </a> 
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fab fa-instagram"></i>
                                    <a href="https://www.instagram.com/masjidraodhatulmuflihin_/" class="hover:text-green-600">
                                        @masjidraodhatulmuflihin_     
                                    </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
    </footer>

    <!-- Swiper initialization -->
    <script> 
        let currentIndex = 0; 
        const slides = document.querySelectorAll('.swiper-slide'); 
        const totalSlides = slides.length; 

        function showSlide(index) { 
            slides.forEach((slide, i) => { 
                slide.classList.remove('swiper-slide-active'); 
                if (i === index) { 
                    slide.classList.add('swiper-slide-active'); 
                } 
            }); 
        } 

        function nextSlide() { 
            currentIndex = (currentIndex + 1) % totalSlides; 
            showSlide(currentIndex); 
        } 

        // Auto switch slide every 5 seconds
        setInterval(nextSlide, 5000); 

        // Show the first slide on load
        showSlide(currentIndex); 

        // Script untuk menampilkan dropdown saat diklik
        document.querySelectorAll('.cursor-pointer').forEach(item => {
            item.addEventListener('click', event => {
                // Menyembunyikan semua submenu terlebih dahulu
                document.querySelectorAll('.absolute').forEach(submenu => {
                    if (submenu !== item.nextElementSibling) {
                        submenu.classList.add('hidden');
                    }
                });
                
                // Toggle submenu yang sesuai
                const submenu = item.nextElementSibling;
                submenu.classList.toggle('hidden');
            });
        });

        // Menyembunyikan submenu saat klik di luar menu
        document.addEventListener('click', function(event) {
            const isClickInside = event.target.closest('.relative');
            if (!isClickInside) {
                document.querySelectorAll('.absolute').forEach(submenu => {
                    submenu.classList.add('hidden');
                });
            }
        });

        // Tampilan pencarian
        const searchInput = document.getElementById("search-input");
        const searchResults = document.getElementById("search-results");

        searchInput.addEventListener("input", function () {
            const query = this.value.trim();
            searchResults.innerHTML = ""; // Kosongkan hasil sebelumnya
            searchResults.classList.add("hidden");

            if (query.length > 0) {
                // Mengambil data dari backend menggunakan Fetch API
                fetch(`cari_jadwal_public.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            searchResults.classList.remove("hidden");
                            data.forEach(item => {
                                const resultDiv = document.createElement("div");
                                resultDiv.classList.add("p-4", "hover:bg-gray-100", "cursor-pointer");
                                resultDiv.innerHTML = `
                                    <h3 class="text-sm font-medium text-gray-700">${item.nama_kegiatan}</h3>
                                `;
                                
                                // Menambahkan event listener untuk mengarahkan ke halaman yang sesuai
                                resultDiv.addEventListener("click", function () {
                                    let url;

                                    // Menentukan URL berdasarkan kode_jadwal
                                    switch (item.kode_jadwal) {
                                        case 'JKJ':
                                            url = 'khutbah_jumat_public.php';
                                            break;
                                        case 'JPR':
                                            url = 'pengajian_public.php'; 
                                            break;
                                        case 'JKR':
                                            url = 'kajian_public.php'; 
                                            break;
                                        case 'JPB':
                                            url = 'peringatan_hari_besar_public.php'; 
                                            break;
                                        default:
                                            url = 'jadwal_public.php'; 
                                    }

                                    // Mengarahkan ke halaman yang sesuai dengan query
                                    window.location.href = `${url}?query=${encodeURIComponent(item.nama_kegiatan)}`;
                                });

                                searchResults.appendChild(resultDiv);
                            });
                        } else {
                            searchResults.innerHTML = `<div class="p-4 text-sm text-gray-500">Tidak ada hasil yang ditemukan.</div>`;
                            searchResults.classList.remove("hidden");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching search results:", error);
                        searchResults.innerHTML = `<div class="p-4 text-sm text-red-500">Terjadi kesalahan. Silakan coba lagi.</div>`;
                        searchResults.classList.remove("hidden");
                    });
            }
        });
    </script> 
</body> 
</html>