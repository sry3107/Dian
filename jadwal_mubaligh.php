<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Query untuk mendapatkan id_mubaligh berdasarkan username, dengan menghubungkan tabel akun dan mubaligh
$queryMubaligh = $conn->prepare("SELECT m.id_mubaligh FROM akun a
    INNER JOIN mubaligh m ON a.id_akun = m.id_akun
    WHERE a.username = ?");
$queryMubaligh->bind_param("s", $username);
$queryMubaligh->execute();
$resultMubaligh = $queryMubaligh->get_result();
if ($resultMubaligh->num_rows > 0) {
    $dataMubaligh = $resultMubaligh->fetch_assoc();
    $id_mubaligh = $dataMubaligh['id_mubaligh']; // id_mubaligh untuk sesi login
} else {
    echo "<script>alert('Mubaligh tidak ditemukan! Silahkan lengkapi detail profil!'); window.location.href='edit_profil_mubaligh.php';</script>";
    exit;
}

// Jika query pencarian tidak kosong, tampilkan data berdasarkan pencarian
if (!empty($query)) {
    $stmt = $conn->prepare("SELECT j.*, k.tanggal_kegiatan, m.nama_masjid, p.nama_pengurus, mu.nama_mubaligh, k.nama_kegiatan FROM jadwal j
        INNER JOIN masjid m ON j.id_masjid = m.id_masjid
        INNER JOIN pengurus p ON j.id_pengurus = p.id_pengurus
        INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
        INNER JOIN kegiatan k ON j.id_kegiatan = k.id_kegiatan
        WHERE mu.nama_mubaligh LIKE ? AND j.id_mubaligh = ?
        ORDER BY k.tanggal_kegiatan ASC
    ");
    $search = "%$query%";
    $stmt->bind_param("si", $search, $id_mubaligh);
} else {
    // Jika query kosong, tampilkan semua jadwal milik mubaligh yang sedang login
    $stmt = $conn->prepare("SELECT j.*, k.tanggal_kegiatan, m.nama_masjid, p.nama_pengurus, mu.nama_mubaligh, k.nama_kegiatan 
        FROM jadwal j
        INNER JOIN masjid m ON j.id_masjid = m.id_masjid
        INNER JOIN pengurus p ON j.id_pengurus = p.id_pengurus
        INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
        INNER JOIN kegiatan k ON j.id_kegiatan = k.id_kegiatan
        WHERE j.id_mubaligh = ?
        ORDER BY k.tanggal_kegiatan ASC
    ");
    $stmt->bind_param("i", $id_mubaligh);
}

// Eksekusi query
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data jadwal ada
$data_jadwal = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

$stmt->close();

// Hapus data jadwal
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_jadwal = $_GET['id'];
    $deleteQuery = $conn->prepare("DELETE FROM jadwal WHERE id_jadwal = ?");
    $deleteQuery->bind_param("i", $id_jadwal);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Jadwal berhasil dihapus!'); window.location.href='jadwal_mubaligh.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jadwal!'); window.location.href='jadwal_mubaligh.php';</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - Kelola Jadwal</title>
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

/* Navbar styles */
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
    color: #4a5568; /* Tailwind gray-600 */
    padding: 0.5rem 1rem; /* Padding */
    border-radius: 0.5rem; /* Rounded corners */
}

nav a:hover {
    background-color: rgba(31, 41, 55, 0.1); /* Tailwind gray-200 */
}

/* Footer styles */
footer {
    background-color: #FFFFEC;
    border-top: 1px solid #e2e8f0; /* Tailwind gray-300 */
}

/* Custom styles for the swiper */
.swiper-container {
    width: 100%;
    height: 400px; /* Atur tinggi tetap untuk konsistensi */
    position: relative;
    overflow: hidden;
    border-radius: 10px; /* Rounded corners for the slider */
}

.swiper-wrapper {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

/* Menambahkan gaya agar gambar di dalam slide rata tengah */
.swiper-slide {
    display: flex;
    justify-content: center; /* Memastikan konten slide berada di tengah */
    align-items: center;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    position: absolute; /* Mengatur posisi absolut agar bertumpuk */
    width: 100%; /* Pastikan slide mengambil lebar penuh */
    height: 100%; /* Pastikan slide mengambil tinggi penuh */
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.swiper-slide-active {
    opacity: 1; /* Hanya slide aktif yang terlihat */
}

.swiper-slide img {
    max-width: 100%;
    height: auto; /* Tinggi otomatis agar proporsional */
    object-fit: cover; /* Menjaga gambar tetap dalam ukuran kontainer */
    border-radius: 8px;
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
                            <a href="home.php">
                                <i class="fas fa-home mr-2"></i>Home    
                            </a>   
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Profil" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-user-circle mr-2"></i>Profil
                        </div>
                        <div id="SubMenuProfil" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="detail_profil_mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-user-cog mr-2"></i>Detail Profil
                            </a>
                            <a href="masjid.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-mosque mr-2"></i>Masjid
                            </a>
                            <a href="pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-address-card mr-2"></i>Pengurus
                            </a>
                            <a href="mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Mubaligh
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Activity" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-clipboard-list mr-2"></i>Kegiatan 
                        </div>
                        <div id="SubMenuActivity" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="kegiatan_mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-clipboard mr-2"></i>Kegiatan Masjid
                            </a>
                            <a href="kegiatan_mingguan.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar mr-2"></i>Mingguan
                            </a>
                            <a href="kegiatan_bulanan.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-day mr-2"></i>Bulanan
                            </a>
                            <a href="kegiatan_tahunan.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-week mr-2"></i>Tahunan
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <div id="Schedule" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-calendar-alt mr-2"></i>Jadwal
                        </div>
                        <div id="SubMenuSchedule" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                            <a href="jadwal_mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-plus mr-2"></i>Jadwal Masjid
                            </a>
                            <a href="khutbah_jumat_mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                <i class="fas fa-calendar-check mr-2"></i>Khutbah Jum'at
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="flex items-center gap-4">
                    <a href="logout.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Log Out</a>
                </div>
            </div>
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

    <!-- Jadwal Mubaligh -->
    <section class="card py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Daftar Jadwal Mubaligh</h2>
                    <div class="flex items-center gap-4">
                    <!-- Tombol Print Daftar Jadwal -->
                    <a href="cetak_jadwal_mubaligh.php" class="text-white bg-green-500 hover:bg-green-600 flex items-center gap-2 rounded-full py-2 px-4 shadow-lg" title="Cetak Daftar Jadwal Mubaligh">
                        <i class="fas fa-print"></i>
                        <span>Daftar</span>
                    </a>
                    <!-- Tombol Print Laporan Jadwal Tahunan -->
                    <a href="laporan_jadwal_mubaligh.php" class="text-white bg-green-500 hover:bg-green-600 flex items-center gap-2 rounded-full py-2 px-4 shadow-lg" title="Cetak Laporan Jadwal Tahunan Mubaligh">
                        <i class="fas fa-print"></i>
                        <span>Laporan</span>
                    </a>
                    <!-- Tombol Tambah Data Jadwal -->
                    <a href="tambah_jadwal_mubaligh.php" class="text-white bg-green-500 hover:bg-green-600 rounded-full p-3 shadow-lg" title="Tambah Jadwal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Tabel Jadwal -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-4">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">No.</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Masjid</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Pengurus</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Mubaligh</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Kegiatan</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Tanggal</th>
                            <th class="py-3 px-6 bg-gray-100 font-semibold text-sm text-gray-900 border-b border-gray-200 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data_jadwal)): ?>
                            <?php foreach ($data_jadwal as $index => $jadwal): ?>
                                <tr class="hover:bg-gray-100">
                                    <td class="py-4 px-6 border-b border-gray-200"><?= $index + 1; ?>.</td>
                                    <td class="py-4 px-6 border-b border-gray-200"><?php echo htmlspecialchars($jadwal['nama_masjid'] ?? ''); ?></td>
                                    <td class="py-4 px-6 border-b border-gray-200"><?php echo htmlspecialchars($jadwal['nama_pengurus'] ?? ''); ?></td>
                                    <td class="py-4 px-6 border-b border-gray-200"><?php echo htmlspecialchars($jadwal['nama_mubaligh'] ?? ''); ?></td>
                                    <td class="py-4 px-6 border-b border-gray-200"><?php echo htmlspecialchars($jadwal['nama_kegiatan'] ?? ''); ?></td>
                                    <td class="py-4 px-6 border-b border-gray-200"><?php echo htmlspecialchars($jadwal['tanggal_kegiatan'] ?? ''); ?></td>
                                    <td class="py-4 px-6 border-b border-gray-200 text-left">
                                        <a href="edit_jadwal_mubaligh.php?id=<?php echo $jadwal['id_jadwal']; ?>" class="text-blue-600 hover:text-blue-800">Edit</a>
                                        <span class="mx-1">|</span>
                                        <a href="jadwal_mubaligh.php?id=<?php echo $jadwal['id_jadwal']; ?>&action=delete" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus jadwal ini?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500">
                                    <?php echo !empty($query) ? "(Tidak ada hasil untuk pencarian \"" . htmlspecialchars($query) . "\")" : "(Data jadwal tidak ditemukan!)"; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                fetch(`cari_jadwal.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            searchResults.classList.remove("hidden");
                            data.forEach(item => {
                                const resultDiv = document.createElement("div");
                                resultDiv.classList.add("p-4", "hover:bg-gray-100", "cursor-pointer");
                                resultDiv.innerHTML = `
                                    <h3 class="text-sm font-medium text-gray-700">${item.nama_mubaligh}</h3>
                                `;

                                // Klik akan mengarahkan ke jadwal_mubaligh.php
                                resultDiv.addEventListener("click", function () {
                                    window.location.href = `jadwal_mubaligh.php?query=${encodeURIComponent(query)}`;
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