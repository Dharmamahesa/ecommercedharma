<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ambil data statistik dari controller (contoh, sesuaikan dengan data yang Anda kirim)
$total_produk = isset($total_produk) ? $total_produk : 0;
$total_konsumen = isset($total_konsumen) ? $total_konsumen : 0;
$total_reseller = isset($total_reseller) ? $total_reseller : 0;
$total_pesanan_baru = isset($total_pesanan_baru) ? $total_pesanan_baru : 0;

// Nama pengguna dari session
$nama_lengkap_admin = $this->session->userdata('nama_lengkap') ? htmlspecialchars($this->session->userdata('nama_lengkap')) : 'Administrator';
$controller_name_path = isset($controller_name) ? $controller_name : 'administrator';
?>

<style>
    /* Font Kustom (Opsional, jika belum ada di template utama) */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { /* Mengaplikasikan font ke body jika template utama belum */
        font-family: 'Poppins', sans-serif;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 30px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .welcome-banner h3 {
        font-weight: 600;
        margin-top: 0;
    }
    .welcome-banner p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .info-box-custom {
        display: flex;
        align-items: center;
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .info-box-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .info-box-custom-icon {
        font-size: 2.5rem; /* Ukuran ikon lebih besar */
        width: 60px;
        height: 60px;
        line-height: 60px;
        text-align: center;
        border-radius: 50%;
        color: #fff;
        margin-right: 20px;
    }
    .bg-custom-produk { background-color: #17a2b8; } /* Info */
    .bg-custom-konsumen { background-color: #28a745; } /* Success */
    .bg-custom-reseller { background-color: #ffc107; } /* Warning */
    .bg-custom-pesanan { background-color: #dc3545; } /* Danger */

    .info-box-custom-content {
        flex-grow: 1;
    }
    .info-box-custom-text {
        display: block;
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 2px;
    }
    .info-box-custom-number {
        display: block;
        font-weight: 600;
        font-size: 1.75rem;
        color: #343a40;
    }
    .info-box-custom a {
        text-decoration: none;
        color: inherit;
    }

    .quick-links-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .quick-links-card .header-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
        font-size: 1.3rem;
    }
    .quick-link-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: background-color 0.2s ease, transform 0.2s ease;
        color: #495057;
        text-decoration: none;
    }
    .quick-link-item:hover {
        background-color: #e9ecef;
        transform: translateX(3px);
        color: #007bff;
    }
    .quick-link-item i {
        margin-right: 15px;
        font-size: 1.2rem;
        width: 25px; /* Agar ikon sejajar */
        text-align: center;
        color: #007bff;
    }
    .quick-link-item span {
        font-weight: 500;
    }
    .section-divider {
        margin-top: 30px;
        margin-bottom: 30px;
        border-top: 1px solid #e9ecef;
    }

</style>

<div class="welcome-banner">
    <h3>Selamat Datang Kembali, <?php echo $nama_lengkap_admin; ?>!</h3>
    <p>Ini adalah pusat kendali untuk mengelola seluruh aspek website e-commerce Anda.</p>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <a href="<?php echo site_url($controller_name_path . '/produk'); ?>" class="info-box-custom-link">
            <div class="info-box-custom">
                <span class="info-box-custom-icon bg-custom-produk"><i class="fas fa-box-open"></i></span>
                <div class="info-box-custom-content">
                    <span class="info-box-custom-text">Total Produk</span>
                    <span class="info-box-custom-number"><?php echo $total_produk; ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
         <a href="<?php echo site_url($controller_name_path . '/konsumen'); ?>" class="info-box-custom-link">
            <div class="info-box-custom">
                <span class="info-box-custom-icon bg-custom-konsumen"><i class="fas fa-users"></i></span>
                <div class="info-box-custom-content">
                    <span class="info-box-custom-text">Total Konsumen</span>
                    <span class="info-box-custom-number"><?php echo $total_konsumen; ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <a href="<?php echo site_url($controller_name_path . '/reseller'); ?>" class="info-box-custom-link">
            <div class="info-box-custom">
                <span class="info-box-custom-icon bg-custom-reseller"><i class="fas fa-store-alt"></i></span>
                <div class="info-box-custom-content">
                    <span class="info-box-custom-text">Total Reseller</span>
                    <span class="info-box-custom-number"><?php echo $total_reseller; ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <a href="<?php echo site_url($controller_name_path . '/pesanan'); // Ganti 'pesanan' dengan URL manajemen pesanan Anda ?>" class="info-box-custom-link">
            <div class="info-box-custom">
                <span class="info-box-custom-icon bg-custom-pesanan"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-custom-content">
                    <span class="info-box-custom-text">Pesanan Baru</span>
                    <span class="info-box-custom-number"><?php echo $total_pesanan_baru; ?></span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="section-divider"></div>

<div class="row">
    <div class="col-md-6">
        <div class="quick-links-card">
            <h5 class="header-title"><i class="fas fa-cogs mr-2"></i>Pengaturan Utama</h5>
            <a href="<?php echo site_url($controller_name_path . '/identitaswebsite'); ?>" class="quick-link-item">
                <i class="fas fa-id-card"></i> <span>Identitas Website</span>
            </a>
            <a href="<?php echo site_url($controller_name_path . '/menuwebsite'); ?>" class="quick-link-item">
                <i class="fas fa-bars"></i> <span>Manajemen Menu</span>
            </a>
            <a href="<?php echo site_url($controller_name_path . '/logowebsite'); ?>" class="quick-link-item">
                <i class="far fa-image"></i> <span>Logo Website</span>
            </a>
            <a href="<?php echo site_url($controller_name_path . '/rekening'); ?>" class="quick-link-item">
                <i class="fas fa-credit-card"></i> <span>Rekening Perusahaan</span>
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="quick-links-card">
             <h5 class="header-title"><i class="fas fa-bullhorn mr-2"></i>Konten & Marketing</h5>
            <a href="<?php echo site_url($controller_name_path . '/listberita'); ?>" class="quick-link-item">
                <i class="fas fa-newspaper"></i> <span>Manajemen Berita</span>
            </a>
             <a href="<?php echo site_url($controller_name_path . '/halamanbaru'); ?>" class="quick-link-item">
                <i class="fas fa-file-alt"></i> <span>Halaman Statis</span>
            </a>
            <a href="<?php echo site_url($controller_name_path . '/banner'); ?>" class="quick-link-item">
                <i class="fas fa-images"></i> <span>Manajemen Banner/Iklan</span>
            </a>
            <a href="<?php echo site_url($controller_name_path . '/pesanmasuk'); ?>" class="quick-link-item">
                <i class="fas fa-envelope-open-text"></i> <span>Pesan Masuk</span>
            </a>
        </div>
    </div>
</div>

<script>
// Script tambahan jika diperlukan, misalnya untuk inisialisasi grafik
// Pastikan jQuery sudah dimuat oleh template utama
$(document).ready(function() {
    console.log("Dashboard Admin Siap!");
    // Tambahkan script spesifik dashboard di sini
});
</script>