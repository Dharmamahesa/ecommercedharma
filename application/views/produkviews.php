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

// Data dari controller
$website_name_view = isset($website_name) ? htmlspecialchars($website_name) : 'Toko Online Saya';
$site_logo_view = isset($site_logo) ? htmlspecialchars($site_logo) : '';
$controller_name_view = isset($controller_name) ? $controller_name : 'produk';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - <?php echo $website_name_view; ?></title>
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
            object-fit: cover;
        }
        .product-info {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Agar tombol selalu di bawah */
        }
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.4em;
        }
        .product-price {
            font-size: 1.25rem;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-stock { /* Dihilangkan karena lebih cocok di detail produk */
            /* font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; */
        }
        .product-actions .btn, .btn-add-to-cart-list { /* Menggunakan class baru untuk tombol di list */
            font-size: 0.9rem; /* Ukuran font tombol sedikit lebih besar */
            padding: 0.5rem 0.8rem; /* Padding tombol disesuaikan */
            border-radius: 6px;
            width: 100%; /* Tombol memenuhi lebar info */
            margin-top: 10px; /* Jarak dari harga/info lain */
        }
        .btn-add-to-cart-list {
            background-color: #28a745; /* Warna hijau untuk tambah keranjang */
            color: white;
            border: none;
        }
        .btn-add-to-cart-list:hover {
            background-color: #218838;
        }
        .btn-add-to-cart-list .fas {
            margin-right: 5px;
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
        .header-section h1 { font-size: 2rem; font-weight: bold; }
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
                <?php if (!empty($site_logo_view)): ?>
                    <img src="<?php echo base_url('asset/logo/' . $site_logo_view); ?>" alt="Logo">
                <?php else: ?>
                    <?php echo $website_name_view; ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavProduk" aria-controls="navbarNavProduk" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavProduk">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo site_url($controller_name_view); ?>">Produk <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url($controller_name_view . '/keranjang'); ?>">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="header-section">
        <div class="container text-center">
            <h1><i class="fas fa-store"></i> Katalog Produk Kami</h1>
            <p class="lead">Jelajahi berbagai produk berkualitas dengan harga terbaik.</p>
            <?php if($this->session->userdata('level') == 'admin' || $this->session->userdata('level') == 'reseller'): // Tombol tambah produk hanya untuk admin/reseller ?>
            <a href="<?php echo site_url($controller_name_view . '/tambah_produk'); ?>" class="btn btn-light btn-lg mt-3">
                <i class="fas fa-plus-circle"></i> Tambah Produk
            </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show alert-fixed" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error_upload')): ?>
            <div class="alert alert-danger alert-dismissible fade show alert-fixed" role="alert">
                <strong>Error Upload!</strong> <?php echo $this->session->flashdata('error_upload'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
         <?php if ($this->session->flashdata('message')): // Untuk pesan umum dari keranjang ?>
            <div class="alert alert-info alert-dismissible fade show alert-fixed" role="alert">
                <?php echo $this->session->flashdata('message'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>


        <?php if (empty($produk)): ?>
            <div class="alert alert-info text-center" role="alert">
                <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Belum Ada Produk</h4>
                <p>Saat ini belum ada produk yang tersedia.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($produk as $item_obj):
                    // Mengubah $item_obj menjadi array jika belum, atau pastikan controller mengirim array
                    $item = (array) $item_obj;

                    $gambar_utama = get_first_image($item['gambar']);
                    $gambar_url = base_url('asset/foto_produk/' . htmlspecialchars($gambar_utama));
                    $detail_url = !empty($item['produk_seo']) ?
                                  site_url($controller_name_view . '/detail/' . $item['produk_seo']) :
                                  site_url($controller_name_view . '/detail/' . $item['id_produk']); // Asumsi ada method detail(id)

                    // ID Penjual (id_reseller dari tabel rb_produk, atau '0' jika produk milik admin/toko utama)
                    $id_penjual_produk = isset($item['id_reseller']) && $item['id_reseller'] != '0' ? $item['id_reseller'] : '0';
                    $link_tambah_keranjang = site_url($controller_name_view . '/keranjang/' . $id_penjual_produk . '/' . $item['id_produk']. '/from_list'); // Tambahkan parameter 'from_list'

                ?>
                    <div class="product-card">
                        <a href="<?php echo $detail_url; ?>" style="text-decoration: none; color: inherit;">
                            <div class="product-image-container">
                                <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>">
                            </div>
                        </a>
                        <div class="product-info">
                            <div>
                                <a href="<?php echo $detail_url; ?>" style="text-decoration: none;">
                                    <h5 class="product-title"><?php echo htmlspecialchars($item['nama_produk']); ?></h5>
                                </a>
                                <p class="product-price">
                                    <?php echo format_rupiah($item['harga_konsumen']); ?>
                                </p>
                                <?php if (isset($item['nama_reseller']) && !empty($item['nama_reseller']) && $item['id_reseller'] != '0'): ?>
                                    <small class="text-muted d-block mb-2">Dijual oleh: <?php echo htmlspecialchars($item['nama_reseller']); ?></small>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($item['stok']) && $item['stok'] > 0): ?>
                                <a href="<?php echo $link_tambah_keranjang; ?>" class="btn btn-add-to-cart-list">
                                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-add-to-cart-list" disabled>
                                    <i class="fas fa-ban"></i> Stok Habis
                                </button>
                            <?php endif; ?>

                            <?php
                            $is_admin = $this->session->userdata('level') == 'admin';
                            $is_owner = isset($item['username']) && $this->session->userdata('username') == $item['username']; // Jika ada kolom username pembuat di tabel produk
                            // Atau jika ini adalah panel reseller: $is_owner = $item['id_reseller'] == $this->session->userdata('id_reseller');

                            if ($is_admin || $is_owner):
                            ?>
                            <div class="product-actions mt-2">
                                <a href="<?php echo site_url($controller_name_view . '/edit_produk/' . $item['id_produk']); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo site_url($controller_name_view . '/delete_produk/' . $item['id_produk']); ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo $website_name_view; ?>. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert-fixed").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 4000);
        });
    </script>
</body>
</html>