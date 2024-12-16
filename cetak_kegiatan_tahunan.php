<?php
require __DIR__ . '/fpdf/fpdf.php'; // Sesuaikan path ke lokasi FPDF
session_start();
include 'koneksi.php'; // File koneksi ke database
date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Query untuk mengambil data kegiatan dan detail masjid
$query = $conn->prepare("SELECT 
        k.tanggal_kegiatan, 
        k.waktu_kegiatan, 
        k.nama_kegiatan,
        k.tempat_kegiatan,
        m.nama_masjid, 
        m.alamat_masjid,
        k.created_at,
        p1.nama_pengurus AS ketua,
        p2.nama_pengurus AS sekretaris
    FROM kegiatan k
    INNER JOIN masjid m ON k.id_masjid = m.id_masjid
    LEFT JOIN pengurus p1 ON p1.jabatan_pengurus = 'Ketua'
    LEFT JOIN pengurus p2 ON p2.jabatan_pengurus = 'Sekretaris'
    WHERE k.kode_kegiatan = 'KTH'
    ORDER BY k.tanggal_kegiatan ASC
");
$query->execute();
$result = $query->get_result();
$data_kegiatan = $result->fetch_all(MYSQLI_ASSOC);

// Validasi data
if (!empty($data_kegiatan)) {
    $nama_masjid = $data_kegiatan[0]['nama_masjid'];
    $alamat_masjid = $data_kegiatan[0]['alamat_masjid'];
    $created_at = $data_kegiatan[0]['created_at'];
    $ketua = $data_kegiatan[0]['ketua'];
    $sekretaris = $data_kegiatan[0]['sekretaris'];
} else {
    $nama_masjid = 'N/A';
    $alamat_masjid = 'N/A';
    $created_at = date('Y-m-d');
    $ketua = 'Tidak diketahui';
    $sekretaris = 'Tidak diketahui';
}

// Extend FPDF untuk menambahkan header dan footer
class PDF extends FPDF
{
    public $nama_masjid;
    public $alamat_masjid;

    function Header()
    {
        $this->Image('D:/laragon/www/sisfo-masjid/fpdf/logo_masjid.jpg', 10, 5, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'DAFTAR KEGIATAN TAHUNAN', 0, 1, 'C');
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
        $this->Cell(15, 10, 'No.', 1, 0, 'C');
        $this->Cell(28, 10, 'Tanggal', 1, 0, 'C');
        $this->Cell(22, 10, 'Waktu', 1, 0, 'C');
        $this->Cell(70, 10, 'Kegiatan', 1, 0, 'C');
        $this->Cell(55, 10, 'Tempat', 1, 1, 'C');
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

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Tabel Data
if (!empty($data_kegiatan)) {
    $no = 1; // Nomor urut
    foreach ($data_kegiatan as $kegiatan) {
        $pdf->Cell(15, 10, $no++.'.', 1, 0, 'C');
        $pdf->Cell(28, 10, $kegiatan['tanggal_kegiatan'], 1, 0, 'C');
        $pdf->Cell(22, 10, $kegiatan['waktu_kegiatan'], 1, 0, 'C');
        $pdf->Cell(70, 10, $kegiatan['nama_kegiatan'], 1, 0, 'L');
        $pdf->Cell(55, 10, $kegiatan['tempat_kegiatan'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data kegiatan', 1, 1, 'C');
}

// Informasi pengurus
$pdf->Ln(10);
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
