<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Data dari controller (jika ada, seperti nama website, dll.)
$website_name_home = isset($website_name) ? htmlspecialchars($website_name) : (isset($iden['nama_website']) ? htmlspecialchars($iden['nama_website']) : 'Selamat Datang');
$site_logo_home = isset($site_logo) ? htmlspecialchars($site_logo) : ''; // Ambil dari $data['site_logo'] di controller Home

// Jika Anda memiliki sistem menu dinamis yang dikirim ke home_view
$menu_items_home = isset($menu_items) ? $menu_items : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website_name_home; ?> - E-commerce Terpercaya</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f9; /* Latar belakang sedikit abu-abu */
        }
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .navbar-custom .navbar-brand img {
            max-height: 45px;
        }
        .navbar-custom .nav-link {
            color: #555;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #007bff;
        }
        .btn-login-group .btn {
            margin-left: 10px;
            border-radius: 20px; /* Tombol lebih membulat */
            padding-left: 20px;
            padding-right: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-login-member {
            background-color: #28a745; /* Hijau */
            border-color: #28a745;
            color: white;
        }
        .btn-login-member:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: white;
        }
        .btn-login-admin {
            background-color: #007bff; /* Biru */
            border-color: #007bff;
            color: white;
        }
        .btn-login-admin:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            color: white;
        }

        .hero-section {
            background: #E0E7FF; /* Warna biru muda pastel */
            /* background: linear-gradient(135deg, #6DD5FA 0%, #FFFFFF 100%); */
            padding: 80px 0;
            text-align: center;
            color: #333; /* Warna teks lebih gelap untuk kontras */
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            color: #555;
        }
        .hero-section .btn-explore {
            font-size: 1.1rem;
            padding: 12px 30px;
            border-radius: 25px;
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }
        .hero-section .btn-explore:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .section-title {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 40px;
            font-weight: 600;
            font-size: 2.2rem;
            color: #343a40;
        }

        /* Placeholder untuk produk atau konten lain */
        .product-placeholder-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .product-placeholder-card i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 15px;
        }

        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 40px 0;
            margin-top: 50px;
            text-align: center;
            font-size: 0.9rem;
        }
        .footer a { color: #00aeff; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php if (!empty($site_logo_home)): ?>
                    <img src="<?php echo base_url('asset/logo/' . $site_logo_home); ?>" alt="Logo <?php echo $website_name_home; ?>">
                <?php else: ?>
                    <?php echo $website_name_home; ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavHome" aria-controls="navbarNavHome" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavHome">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('produk'); ?>">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('konfirmasi'); ?>">Konfirmasi Bayar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('konfirmasi/tracking'); ?>">Lacak Pesanan</a>
                    </li>
                    <?php
                    // Contoh jika Anda ingin menampilkan menu dinamis dari database
                    // if (!empty($menu_items_home)) {
                    //     foreach ($menu_items_home as $item) {
                    //         if (isset($item['position']) && $item['position'] == 'top' && isset($item['aktif']) && $item['aktif'] == 'Y') {
                    //             echo '<li class="nav-item">';
                    //             echo '<a class="nav-link" href="' . base_url(htmlspecialchars($item['link'])) . '">' . htmlspecialchars($item['nama_menu']) . '</a>';
                    //             echo '</li>';
                    //         }
                    //     }
                    // }
                    ?>
                </ul>
                <div class="btn-login-group">
                    <?php if ($this->session->userdata('id_konsumen') || $this->session->userdata('id_reseller')): ?>
                        <a href="<?php echo site_url('members/profile'); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-circle mr-1"></i> Profil Saya
                        </a>
                         <a href="<?php echo site_url('members/logout'); ?>" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php elseif ($this->session->userdata('level') == 'admin'): ?>
                         <a href="<?php echo site_url('administrator/home'); ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard Admin
                        </a>
                         <a href="<?php echo site_url('administrator/logout'); ?>" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="<?php echo site_url('auth/login_member'); ?>" class="btn btn-login-member btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Daftar/Login Member
                        </a>
                        <a href="<?php echo site_url('administrator'); ?>" class="btn btn-login-admin btn-sm">
                            <i class="fas fa-user-shield mr-1"></i> Login Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>Temukan Produk Impian Anda</h1>
            <p>Kualitas terbaik dengan harga paling bersaing. Belanja mudah, aman, dan nyaman.</p>
            <a href="<?php echo site_url('produk'); ?>" class="btn btn-primary btn-lg btn-explore">
                <i class="fas fa-search mr-2"></i> Jelajahi Produk Sekarang
            </a>
        </div>
    </div>

    <div class="container">
        <h2 class="section-title">Mengapa Memilih Kami?</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="product-placeholder-card">
                    <i class="fas fa-award"></i>
                    <h5>Kualitas Terjamin</h5>
                    <p class="text-muted">Produk original dengan standar kualitas tertinggi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-placeholder-card">
                    <i class="fas fa-shipping-fast"></i>
                    <h5>Pengiriman Cepat</h5>
                    <p class="text-muted">Pesanan Anda kami proses dan kirim dengan segera.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-placeholder-card">
                    <i class="fas fa-headset"></i>
                    <h5>Layanan Terbaik</h5>
                    <p class="text-muted">Tim kami siap membantu Anda setiap saat.</p>
                </div>
            </div>
        </div>

        </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo $website_name_home; ?>. Dikelola dengan <i class="fas fa-heart text-danger"></i>.</p>
        <?php if (isset($iden['email']) && isset($iden['no_telp'])): ?>
            <p><small>Email: <?php echo htmlspecialchars($iden['email']); ?> | Telp: <?php echo htmlspecialchars($iden['no_telp']); ?></small></p>
        <?php endif; ?>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>