<?php
require __DIR__ . '/fpdf/fpdf.php'; // Sesuaikan path ke lokasi FPDF
session_start();
include 'koneksi.php'; // File koneksi ke database
date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Query untuk mengambil data jadwal dan detail masjid
$query = $conn->prepare("SELECT 
        j.id_jadwal,
        j.kode_jadwal,
        k.tanggal_kegiatan, 
        k.waktu_kegiatan, 
        mu.nama_mubaligh, 
        mu.no_telepon_mubaligh,
        m.nama_masjid, 
        m.alamat_masjid,
        j.created_at,
        p1.nama_pengurus AS ketua,
        p2.nama_pengurus AS sekretaris
    FROM jadwal j
    INNER JOIN kegiatan k ON j.id_kegiatan = k.id_kegiatan
    INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
    INNER JOIN masjid m ON j.id_masjid = m.id_masjid
    LEFT JOIN pengurus p1 ON p1.jabatan_pengurus = 'Ketua'
    LEFT JOIN pengurus p2 ON p2.jabatan_pengurus = 'Sekretaris'
    WHERE j.kode_jadwal = 'JKJ'
    ORDER BY k.tanggal_kegiatan ASC
");

$query->execute();
$result = $query->get_result();
$data_jadwal = $result->fetch_all(MYSQLI_ASSOC);

// Ambil data masjid, tanggal jadwal dibuat, dan pengurus
$nama_masjid = $data_jadwal[0]['nama_masjid'] ?? 'N/A';
$alamat_masjid = $data_jadwal[0]['alamat_masjid'] ?? 'N/A';
$created_at = $data_jadwal[0]['created_at'] ?? date('Y-m-d');
$ketua = $data_jadwal[0]['ketua'] ?? 'Tidak diketahui';
$sekretaris = $data_jadwal[0]['sekretaris'] ?? 'Tidak diketahui';

// Extend FPDF untuk menambahkan header dan footer
class PDF extends FPDF
{
    public $nama_masjid;
    public $alamat_masjid;
    public $sekretaris;
    public $created_at;

    function Header()
    {
        $this->Image('D:/laragon/www/sisfo-masjid/fpdf/logo_masjid.jpg', 10, 5, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'DAFTAR JADWAL KHUTBAH JUM\'AT', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 5, $this->nama_masjid, 0, 1, 'C');
        $this->SetFont('Arial', 'IU', 11);
        $this->Cell(0, 5, $this->alamat_masjid, 0, 1, 'C');
        $this->Ln(8);

        // Garis pemisah
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);

        // Header tabel
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(17, 10, 'No.', 1, 0, 'C');
        $this->Cell(30, 10, 'Tanggal', 1, 0, 'C');
        $this->Cell(25, 10, 'Waktu', 1, 0, 'C');
        $this->Cell(83, 10, 'Nama Mubaligh', 1, 0, 'C');
        $this->Cell(35, 10, 'No. Telepon', 1, 1, 'C');
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 10);
        $this->Line(10, $this->GetY() + 10, 200, $this->GetY() + 10);
        $this->Cell(0, 10, 'Tanggal & Waktu: ' . date('Y-m-d H:i:s'), 0, 0, 'L');
        $this->Cell(0, 10, 'Halaman: ' . $this->PageNo() . '/{nb}', 0, 1, 'R');
    }
}

// Buat objek PDF
$pdf = new PDF();
$pdf->AliasNbPages(); // Tambahkan ini untuk menggantikan {nb}
$pdf->nama_masjid = $nama_masjid;
$pdf->alamat_masjid = $alamat_masjid;
$pdf->sekretaris = $sekretaris;
$pdf->created_at = $created_at;

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Tabel Data
if (!empty($data_jadwal)) {
    $no = 1; // Nomor urut
    foreach ($data_jadwal as $jadwal) {
        $pdf->Cell(17, 10, $no++.'.', 1, 0, 'C');
        $pdf->Cell(30, 10, $jadwal['tanggal_kegiatan'], 1, 0, 'C');
        $pdf->Cell(25, 10, $jadwal['waktu_kegiatan'], 1, 0, 'C');
        $pdf->Cell(83, 10, $jadwal['nama_mubaligh'], 1, 0, 'L');
        $pdf->Cell(35, 10, $jadwal['no_telepon_mubaligh'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data jadwal', 1, 1, 'C');
}

// Informasi pengurus
$pdf->Ln(8);
$pdf->Cell(0, 8, 'Mengetahui,', 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 8, 'Ketua', 0, 0, 'L');
$pdf->Cell(0, 8, 'Sekretaris', 0, 1, 'R');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'BU', 12);
$pdf->Cell(100, 8, $ketua, 0, 0, 'L');
$pdf->Cell(0, 8, $sekretaris, 0, 1, 'R');

// Output PDF
$pdf->Output();
?>
