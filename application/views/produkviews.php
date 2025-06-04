<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Helper function (bisa juga diletakkan di custom helper CodeIgniter Anda)
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

if (!function_exists('get_first_image')) {
    function get_first_image($gambar_string, $default_image = 'no-image.png') {
        if (!empty($gambar_string)) {
            $img_array = explode(';', $gambar_string);
            $first_image = trim($img_array[0]);
            if (!empty($first_image)) {
                return $first_image;
            }
        }
        return $default_image;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'Toko Online Saya'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand img { max-height: 40px; }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        .product-card {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image-container {
            width: 100%;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            position: relative;
            overflow: hidden;
        }
        .product-image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures image covers the container */
        }
        .product-info {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            /* Batasi jumlah baris untuk judul */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.4em; /* Perkiraan tinggi untuk 2 baris */
        }
        .product-price {
            font-size: 1.25rem;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-stock {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .product-actions .btn {
            margin-right: 5px;
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
        }
        .product-actions .btn:last-child {
            margin-right: 0;
        }
        .page-title {
            color: #343a40;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 300;
        }
        .header-section {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .header-section h1 {
            font-size: 2rem;
            font-weight: bold;
        }
        .header-section .btn-primary {
            background-color: #fff;
            color: #007bff;
            border-color: #fff;
            font-weight: bold;
        }
        .header-section .btn-primary:hover {
            background-color: #e6f2ff;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px 0;
            margin-top: 40px;
            text-align: center;
        }
        .alert-fixed {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php if (isset($site_logo) && !empty($site_logo)): ?>
                    <img src="<?php echo base_url('asset/logo/' . htmlspecialchars($site_logo)); ?>" alt="Logo">
                <?php else: ?>
                    <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'Toko Saya'; ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo site_url('produk'); // Atau 'home' jika Anda menggunakan controller Home untuk produk ?>">Produk <span class="sr-only">(current)</span></a>
                    </li>
                    </ul>
            </div>
        </div>
    </nav>

    <header class="header-section">
        <div class="container text-center">
            <h1><i class="fas fa-store"></i> Katalog Produk Kami</h1>
            <p class="lead">Jelajahi berbagai produk berkualitas dengan harga terbaik.</p>
            <a href="<?php echo site_url((isset($controller_name) ? $controller_name : 'produk') . '/tambah_produk'); ?>" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-plus-circle"></i> Tambah Produk Baru
            </a>
        </div>
    </header>

    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show alert-fixed" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error_upload')): ?>
            <div class="alert alert-danger alert-dismissible fade show alert-fixed" role="alert">
                <strong>Error Upload!</strong> <?php echo $this->session->flashdata('error_upload'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>


        <?php if (empty($produk)): ?>
            <div class="alert alert-info text-center" role="alert">
                <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Belum Ada Produk</h4>
                <p>Saat ini belum ada produk yang tersedia. Silakan tambahkan produk baru.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($produk as $item): // $item sekarang adalah objek
                    // Mendapatkan gambar pertama dari string gambar (jika ada multiple)
                    // PERBAIKAN: Gunakan sintaks objek ->
                    $gambar_utama = get_first_image(isset($item->gambar) ? $item->gambar : '');
                    $gambar_url = base_url('asset/foto_produk/' . htmlspecialchars($gambar_utama));

                    // URL detail produk bisa menggunakan produk_seo jika ada, atau id_produk
                    // PERBAIKAN: Gunakan sintaks objek ->
                    $detail_url = !empty($item->produk_seo) ?
                                  site_url((isset($controller_name) ? $controller_name : 'produk') . '/detail/' . $item->produk_seo) :
                                  site_url((isset($controller_name) ? $controller_name : 'produk') . '/detail_by_id/' . $item->id_produk);
                ?>
                    <div class="product-card">
                        <a href="<?php echo $detail_url; ?>" style="text-decoration: none; color: inherit;">
                            <div class="product-image-container">
                                <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars(isset($item->nama_produk) ? $item->nama_produk : 'Produk'); ?>">
                            </div>
                        </a>
                        <div class="product-info">
                            <div>
                                <a href="<?php echo $detail_url; ?>" style="text-decoration: none;">
                                    <h5 class="product-title"><?php echo htmlspecialchars(isset($item->nama_produk) ? $item->nama_produk : 'Nama Produk Tidak Tersedia'); ?></h5>
                                </a>
                                <p class="product-price">
                                    <?php echo format_rupiah(isset($item->harga_konsumen) ? $item->harga_konsumen : 0); // PERBAIKAN ?>
                                </p>
                                <p class="product-stock">
                                    Stok: <?php echo htmlspecialchars(isset($item->stok) ? $item->stok : 0); ?> <?php echo htmlspecialchars(isset($item->satuan) ? $item->satuan : ''); // PERBAIKAN ?>
                                    <?php if (isset($item->berat) && $item->berat > 0): // PERBAIKAN ?>
                                        | Berat: <?php echo htmlspecialchars($item->berat); ?> gram
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="product-actions">
                                <a href="<?php echo site_url((isset($controller_name) ? $controller_name : 'produk') . '/edit_produk/' . (isset($item->id_produk) ? $item->id_produk : '')); // PERBAIKAN ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="<?php echo site_url((isset($controller_name) ? $controller_name : 'produk') . '/delete_produk/' . (isset($item->id_produk) ? $item->id_produk : '')); // PERBAIKAN ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </a>
                                <a href="<?php echo $detail_url; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'Toko Online Saya'; ?>. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script untuk auto-hide alert
        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert-fixed").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 4000); // Alert akan hilang setelah 4 detik
        });
    </script>
</body>
</html>