<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Default values jika variabel tidak terdefinisi (sebaiknya data selalu ada dari controller)
$produk_detail = isset($produk_detail) ? (object)$produk_detail : (object)[
    'id_produk' => 'N/A',
    'nama_produk' => 'Produk Tidak Ditemukan',
    'produk_seo' => '',
    'keterangan' => 'Deskripsi tidak tersedia.',
    'gambar' => '',
    'harga_konsumen' => 0,
    'stok' => 0,
    'satuan' => 'unit',
    'berat' => 0,
    'nama_kategori' => 'Tidak Berkategori', // Diasumsikan join dari model
    'nama_reseller' => 'Penjual Tidak Diketahui' // Diasumsikan join dari model
];

// Helper function (bisa juga diletakkan di custom helper CodeIgniter Anda)
if (!function_exists('format_rupiah_detail')) {
    function format_rupiah_detail($number) {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

$gambar_array = [];
if (!empty($produk_detail->gambar)) {
    $gambar_array = explode(';', $produk_detail->gambar);
    $gambar_array = array_map('trim', $gambar_array);
    $gambar_array = array_filter($gambar_array); // Menghapus entri kosong jika ada
}
$gambar_utama = !empty($gambar_array) ? $gambar_array[0] : 'no-image.png';

$controller_name_path = isset($controller_name) ? $controller_name : 'produk'; // Dari controller
$website_name = isset($website_name) ? $website_name : 'Toko Online Saya';
$site_logo = isset($site_logo) ? $site_logo : '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produk_detail->nama_produk); ?> - <?php echo htmlspecialchars($website_name); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        .product-detail-container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .product-card-detail {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .product-gallery {
            padding: 20px;
        }
        .main-product-image {
            width: 100%;
            max-height: 450px;
            object-fit: contain; /* Agar gambar tidak terpotong, bisa ganti 'cover' jika ingin memenuhi area */
            border-radius: 8px;
            border: 1px solid #eee;
            cursor: pointer;
        }
        .thumbnail-images {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            overflow-x: auto; /* Jika banyak thumbnail */
        }
        .thumbnail-images img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s ease;
        }
        .thumbnail-images img.active,
        .thumbnail-images img:hover {
            border-color: #007bff;
        }
        .product-info-section {
            padding: 25px 30px;
        }
        .product-title-detail {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        .product-category-seller {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .product-category-seller a { color: #007bff; }
        .product-price-detail {
            font-size: 2.25rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 1.5rem;
        }
        .product-meta-info {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        .product-meta-info .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.7em;
        }
        .product-description {
            margin-top: 1.5rem;
            line-height: 1.7;
            color: #495057;
        }
        .product-description h5 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #343a40;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .btn-add-to-cart {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: background-color .2s ease;
        }
        .btn-add-to-cart:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .breadcrumb-custom {
            background-color: transparent;
            padding-left: 0;
            font-size: 0.9rem;
        }
        /* Navbar sederhana */
        .navbar-brand img { max-height: 40px; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.05); }

        .footer { background-color: #343a40; color: white; padding: 30px 0; margin-top: 40px; text-align: center;}

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php if (!empty($site_logo)): ?>
                    <img src="<?php echo base_url('asset/logo/' . htmlspecialchars($site_logo)); ?>" alt="Logo">
                <?php else: ?>
                    <?php echo htmlspecialchars($website_name); ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDetail" aria-controls="navbarNavDetail" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDetail">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url($controller_name_path); ?>">Produk</a>
                    </li>
                    </ul>
            </div>
        </div>
    </nav>

    <div class="container product-detail-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?php echo site_url($controller_name_path); ?>">Produk</a></li>
                <?php if (!empty($produk_detail->nama_kategori)): ?>
                <li class="breadcrumb-item"><a href="<?php echo site_url('kategori/produk/' . url_title($produk_detail->nama_kategori, 'dash', TRUE)); ?>"><?php echo htmlspecialchars($produk_detail->nama_kategori); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($produk_detail->nama_produk); ?></li>
            </ol>
        </nav>

        <div class="card product-card-detail">
            <div class="row no-gutters">
                <div class="col-md-6">
                    <div class="product-gallery">
                        <a href="<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gambar_utama)); ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($produk_detail->nama_produk); ?>">
                            <img src="<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gambar_utama)); ?>" alt="<?php echo htmlspecialchars($produk_detail->nama_produk); ?>" class="main-product-image" id="mainImage">
                        </a>
                        <?php if (count($gambar_array) > 1): ?>
                        <div class="thumbnail-images">
                            <?php foreach ($gambar_array as $index => $gbr): ?>
                                <a href="<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gbr)); ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($produk_detail->nama_produk); ?> (Gambar <?php echo $index + 1; ?>)">
                                    <img src="<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gbr)); ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="<?php echo ($index == 0) ? 'active' : ''; ?>" onclick="changeMainImage('<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gbr)); ?>', this)">
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-info-section">
                        <h1 class="product-title-detail"><?php echo htmlspecialchars($produk_detail->nama_produk); ?></h1>
                        <p class="product-category-seller">
                            Kategori: <a href="<?php echo site_url('kategori/produk/' . (isset($produk_detail->kategori_seo) ? $produk_detail->kategori_seo : url_title($produk_detail->nama_kategori, 'dash', TRUE) ) ); ?>"><?php echo htmlspecialchars($produk_detail->nama_kategori); ?></a>
                            <?php if (isset($produk_detail->nama_reseller) && !empty($produk_detail->nama_reseller)): ?>
                                | Dijual oleh: <a href="<?php echo site_url('reseller/detail/' . (isset($produk_detail->id_reseller) ? $produk_detail->id_reseller : '')); ?>"><?php echo htmlspecialchars($produk_detail->nama_reseller); ?></a>
                            <?php endif; ?>
                        </p>

                        <div class="product-price-detail">
                            <?php echo format_rupiah_detail($produk_detail->harga_konsumen); ?>
                        </div>

                        <div class="product-meta-info">
                            <p class="mb-1">
                                <i class="fas fa-box-open mr-2 text-muted"></i>Stok:
                                <?php if ($produk_detail->stok > 0): ?>
                                    <span class="badge badge-success">Tersedia (<?php echo $produk_detail->stok; ?> <?php echo htmlspecialchars($produk_detail->satuan); ?>)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Stok Habis</span>
                                <?php endif; ?>
                            </p>
                            <?php if (isset($produk_detail->berat) && $produk_detail->berat > 0): ?>
                            <p class="mb-1"><i class="fas fa-weight-hanging mr-2 text-muted"></i>Berat: <?php echo htmlspecialchars($produk_detail->berat); ?> gram</p>
                            <?php endif; ?>
                            <p class="mb-1"><i class="fas fa-tag mr-2 text-muted"></i>Satuan: <?php echo htmlspecialchars($produk_detail->satuan); ?></p>
                        </div>

                        <?php
                        // Form untuk menambah ke keranjang, disesuaikan dengan logika di PDF
                        // PDF menggunakan path members/keranjang/{id_reseller}/{id_produk}
                        // atau produk/keranjang/{id_reseller}/{id_produk}
                        $id_penjual_produk = isset($produk_detail->id_reseller) ? $produk_detail->id_reseller : '0'; // '0' jika produk admin/toko utama
                        $action_url = site_url('produk/keranjang/' . $id_penjual_produk . '/' . $produk_detail->id_produk);
                        echo form_open($action_url, ['class' => 'form-inline mt-3']);
                        ?>
                            <div class="form-group mr-2 mb-2">
                                <label for="qty" class="sr-only">Jumlah</label>
                                <input type="number" name="qty" id="qty" class="form-control quantity-input" value="1" min="1" <?php echo ($produk_detail->stok <= 0) ? 'disabled' : ''; ?>>
                            </div>
                            <button type="submit" class="btn btn-add-to-cart mb-2" <?php echo ($produk_detail->stok <= 0) ? 'disabled' : ''; ?>>
                                <i class="fas fa-shopping-cart mr-2"></i> Tambah ke Keranjang
                            </button>
                        <?php echo form_close(); ?>

                        <div class="product-description">
                            <h5>Deskripsi Produk</h5>
                            <?php echo (!empty($produk_detail->keterangan)) ? nl2br(htmlspecialchars($produk_detail->keterangan)) : 'Tidak ada deskripsi detail untuk produk ini.'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($website_name); ?>. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'disableScrolling': true
        });

        function changeMainImage(newImageSrc, clickedThumbnail) {
            document.getElementById('mainImage').src = newImageSrc;
            // Update active class for thumbnails
            var thumbnails = document.querySelectorAll('.thumbnail-images img');
            thumbnails.forEach(function(thumb) {
                thumb.classList.remove('active');
            });
            clickedThumbnail.classList.add('active');
        }
    </script>
</body>
</html>