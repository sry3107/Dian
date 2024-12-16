<?php
require __DIR__ . '/fpdf/fpdf.php'; // Sesuaikan path ke lokasi FPDF
session_start();
include 'koneksi.php'; // File koneksi ke database
date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Query untuk mengambil data mubaligh dan masjid
$query = $conn->prepare("SELECT 
        m.nama_masjid, 
        m.alamat_masjid,
        mb.nama_mubaligh,
        mb.alamat_mubaligh 
        FROM mubaligh mb 
        INNER JOIN masjid m ON mb.id_masjid = m.id_masjid 
        ORDER BY mb.nama_mubaligh ASC");
$query->execute();
$result = $query->get_result();
$data_mubaligh = $result->fetch_all(MYSQLI_ASSOC);

// Extend FPDF untuk menambahkan header dan footer
class PDF extends FPDF
{
    public $nama_masjid;
    public $alamat_masjid;

    function Header()
    {
        $this->Image('D:/laragon/www/sisfo-masjid/fpdf/logo_masjid.jpg', 10, 5, 30); // Sesuaikan path logo
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'DAFTAR MUBALIGH MASJID', 0, 1, 'C');
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
        $this->Cell(18, 10, 'No', 1, 0, 'C');
        $this->Cell(86, 10, 'Nama Mubaligh', 1, 0, 'C');
        $this->Cell(86, 10, 'Alamat', 1, 1, 'C');
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

// Ambil data masjid dari data pertama (semua mubaligh terkait dengan satu masjid)
if (!empty($data_mubaligh)) {
    $pdf->nama_masjid = $data_mubaligh[0]['nama_masjid'];
    $pdf->alamat_masjid = $data_mubaligh[0]['alamat_masjid'];
} else {
    $pdf->nama_masjid = 'Masjid Tidak Ditemukan';
    $pdf->alamat_masjid = 'Alamat Tidak Ditemukan';
}

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Tabel Data Mubaligh
if (!empty($data_mubaligh)) {
    $no = 1; // Nomor urut
    foreach ($data_mubaligh as $mubaligh) {
        $pdf->Cell(18, 10, $no++.'.', 1, 0, 'C');
        $pdf->Cell(86, 10, $mubaligh['nama_mubaligh'], 1, 0, 'L');
        $pdf->Cell(86, 10, $mubaligh['alamat_mubaligh'], 1, 1, 'L');
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data mubaligh', 1, 1, 'C');
}

// Output PDF
$pdf->Output();
?>
