<?php 
session_start();
include 'koneksi.php'; // File koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

$username = $_SESSION['username']; // Mendapatkan username dari session

// Ambil id_akun berdasarkan username
$queryAkun = $conn->prepare("SELECT id_akun FROM akun WHERE username = ?");
$queryAkun->bind_param("s", $username);
$queryAkun->execute();
$resultAkun = $queryAkun->get_result();

if ($resultAkun->num_rows > 0) {
    $akunData = $resultAkun->fetch_assoc();
    $id_akun = $akunData['id_akun']; // Mendapatkan id_akun

    // Ambil data pengurus dari tabel berdasarkan id_akun
    $queryPengurus = $conn->prepare("SELECT id_pengurus, id_akun, id_masjid, COALESCE(nama_pengurus, '') AS nama_pengurus, COALESCE(jabatan_pengurus, '') AS jabatan_pengurus, COALESCE(alamat_pengurus, '') AS alamat_pengurus, COALESCE(no_telepon_pengurus, '') AS no_telepon_pengurus, COALESCE(created_at, '') AS created_at, COALESCE(updated_at, '') AS updated_at FROM pengurus WHERE id_akun = ?");
    $queryPengurus->bind_param("i", $id_akun); // Mengikat parameter id_akun
    $queryPengurus->execute();
    $resultPengurus = $queryPengurus->get_result();

    // Jika ada data, ambil data pengurus
    if ($resultPengurus->num_rows > 0) {
        $data = $resultPengurus->fetch_assoc();
    } 
} else {
    echo "Akun tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Detail Profil</title>
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
                                <a href="detail_profil_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-user-cog mr-2"></i>Detail Profil
                                </a>
                                <a href="manage_masjid.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-mosque mr-2"></i>Masjid
                                </a>
                                <a href="manage_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-address-card mr-2"></i>Pengurus
                                </a>
                                <a href="manage_mubaligh.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i>Mubaligh
                                </a>
                                <a href="manage_akun.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-users mr-2"></i>Akun Pengguna
                                </a>
                            </div>
                    </div>
                    <div class="relative">
                        <div id="Activity" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-clipboard-list mr-2"></i>Kegiatan
                        </div>
                            <div id="SubMenuActivity" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                                <a href="manage_kegiatan.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-clipboard mr-2"></i>Kegiatan Masjid
                                </a>
                                <a href="kegiatan_mingguan_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-calendar mr-2"></i>Mingguan
                                </a>
                                <a href="kegiatan_bulanan_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-calendar-day mr-2"></i>Bulanan
                                </a>
                                <a href="kegiatan_tahunan_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-calendar-week mr-2"></i>Tahunan
                                </a>
                            </div>
                    </div>
                    <div class="relative">
                        <div id="Schedule" class="flex items-center text-gray-600 hover:text-green-600 px-2 py-1 cursor-pointer">
                            <i class="fas fa-calendar-alt mr-2"></i>Jadwal
                        </div>
                            <div id="SubMenuSchedule" class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                                <a href="manage_jadwal.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
                                    <i class="fas fa-calendar-plus mr-2"></i>Jadwal Masjid
                                </a>
                                <a href="khutbah_jumat_pengurus.php" class="block px-4 py-2 text-gray-800 hover:bg-green-100">
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

    <!-- Detail Profil Pengurus -->
    <section class="card py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Detail Profil Pengurus</h2>
                <div class="flex items-center space-x-4">
                    <!-- Icon Settings -->
                    <a href="pengaturan_akun_pengurus.php" class="text-white bg-gray-500 hover:text-gray-300 rounded-full p-3 shadow-lg" title="Pengaturan Akun">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M11.25 2.25c.414 0 .75.336.75.75v1.518a8.977 8.977 0 013.347.69l1.089-.63a.75.75 0 01.75 1.299l-1.089.63c.774.696 1.418 1.554 1.889 2.527l1.212-.336a.75.75 0 01.386 1.445l-1.212.336c.184.902.184 1.854 0 2.756l1.212.336a.75.75 0 11-.386 1.445l-1.212-.336a8.978 8.978 0 01-1.889 2.527l1.089.63a.75.75 0 11-.75 1.299l-1.089-.63a8.977 8.977 0 01-3.347.69v1.518a.75.75 0 11-1.5 0v-1.518a8.977 8.977 0 01-3.347-.69l-1.089.63a.75.75 0 01-.75-1.299l1.089-.63a8.978 8.978 0 01-1.889-2.527l-1.212.336a.75.75 0 11-.386-1.445l1.212-.336a8.977 8.977 0 010-2.756l-1.212-.336a.75.75 0 11.386-1.445l1.212.336a8.978 8.978 0 011.889-2.527l-1.089-.63a.75.75 0 11.75-1.299l1.089.63a8.977 8.977 0 013.347-.69V3c0-.414.336-.75.75-.75zM12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                        </svg>
                    </a>

                    <?php
                    // Cek apakah data pengurus dengan id_akun ini sudah ada
                    $queryCheck = "SELECT COUNT(*) AS total FROM pengurus WHERE id_akun = ?";
                    $stmt = $conn->prepare($queryCheck);
                    $stmt->bind_param("i", $id_akun);
                    $stmt->execute();
                    $resultCheck = $stmt->get_result();
                    $dataCheck = $resultCheck->fetch_assoc();

                    // Tampilkan tombol "Input Data" hanya jika data pengurus belum ada
                    if ($dataCheck['total'] == 0): ?>
                        <!-- Icon Input Data -->
                        <a href="edit_profil_pengurus.php" class="text-white bg-green-500 hover:bg-green-600 rounded-full p-3 shadow-lg" title="Input Data">
                            <i class="fas fa-edit mr-1 ml-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            // Ambil data pengurus untuk ditampilkan
            $query = "SELECT * FROM pengurus WHERE id_akun = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_akun);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0):
                while ($data = $result->fetch_assoc()):
            ?>
            <!-- Card Profil Pengurus -->
            <div class="card-hover bg-white rounded-xl overflow-hidden mb-4 shadow-md">
                <a href="edit_profil_pengurus.php?id=<?php echo $data['id_pengurus']; ?>" class="block w-full h-full">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold mb-4">Profil:</h2>
                        <div class="detail">
                            <table class="table-auto w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <th class="text-left py-2 pr-4 font-medium">
                                            <i class="fas fa-user-edit"></i> Nama
                                        </th>
                                        <td class="py-2">: <?php echo htmlspecialchars($data['nama_pengurus']); ?></td>
                                    </tr>
                                    <tr class="border-b">
                                        <th class="text-left py-2 pr-4 font-medium">
                                            <i class="fas fa-briefcase"></i> Jabatan
                                        </th>
                                        <td class="py-2">: <?php echo htmlspecialchars($data['jabatan_pengurus']); ?></td>
                                    </tr>
                                    <tr class="border-b">
                                        <th class="text-left py-2 pr-4 font-medium">
                                            <i class="fas fa-map-marker-alt"></i> Alamat
                                        </th>
                                        <td class="py-2">: <?php echo htmlspecialchars($data['alamat_pengurus']); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left py-2 pr-4 font-medium">
                                            <i class="fas fa-phone-alt"></i> Nomor Telepon
                                        </th>
                                        <td class="py-2">: <?php echo htmlspecialchars($data['no_telepon_pengurus']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </a>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <div class="text-center">
                <p class="text-gray-500">Tidak ada data pengurus tersedia.</p>
            </div>
            <?php endif; ?>
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
