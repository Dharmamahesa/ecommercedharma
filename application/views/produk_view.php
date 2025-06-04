<?php $this->load->view('administrator/main_header'); ?>

<?php
// Предполагается, что данные передаются из контроллера:
// $site_logo (из таблицы logo)
// $website_name (из таблицы tb_identitas)
// $menu_items (из таблицы menu, обработано Model_menu)
// $banners (из таблицы tb_banner)
// $featured_products (из таблицы rb_produk, обработано соответствующей моделью)
// $latest_news (из таблицы tb_berita, обработано соответствующей моделью)

// Вспомогательная функция для вывода цены (может быть в хелпере CodeIgniter)
if (!function_exists('format_price')) {
    function format_price($price) {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($website_name) ? htmlspecialchars($website_name) : 'E-commerce'; ?> - Beranda</title>
    <link rel="stylesheet" href="https
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
            background-color: #ffffff; /* Белый фон для навигационной панели */
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .navbar-brand img {
            max-height: 40px;
        }
        .hero-section {
            background-color: #e9ecef; /* Светлый фон для секции приветствия */
            padding: 3rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: .3rem;
        }
        .product-card, .news-card {
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform .2s; /* Анимация при наведении */
            border: none; /* Убираем стандартную рамку */
        }
        .product-card:hover, .news-card:hover {
            transform: scale(1.03); /* Увеличиваем карточку при наведении */
        }
        .product-card img, .news-card img {
            max-height: 200px;
            object-fit: cover; /* Чтобы изображение покрывало область, не искажаясь */
            border-top-left-radius: calc(.25rem - 1px);
            border-top-right-radius: calc(.25rem - 1px);
        }
        .card-body {
            padding: 1.25rem;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }
        .product-price {
            color: #007bff; /* Фирменный синий цвет для цены */
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-details {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        .btn-details:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
            font-size: 2.5rem;
            color: #333;
        }
        .footer {
            background-color: #343a40; /* Темный фон для футера */
            color: white;
            padding: 40px 0;
            margin-top: 40px;
        }
        .footer a {
            color: #adb5bd;
        }
        .footer a:hover {
            color: #f8f9fa;
            text-decoration: none;
        }
        /* Стили для баннера/карусели */
        #mainBanner .carousel-item img {
            max-height: 450px; /* Максимальная высота для изображений баннера */
            object-fit: cover; /* Масштабирование изображения для покрытия области */
            width: 100%;
        }
        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5); /* Полупрозрачный фон для текста */
            border-radius: .3rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php if (isset($site_logo) && !empty($site_logo)): ?>
                    <img src="<?php echo base_url('asset/logo/' . htmlspecialchars($site_logo)); ?>" alt="Logo">
                <?php else: ?>
                    <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'E-commerce'; ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda <span class="sr-only">(current)</span></a>
                    </li>
                    <?php
                    // Пример вывода пунктов меню из $menu_items (предполагаемая структура)
                    // Данные должны быть подготовлены в контроллере с использованием model_menu.php [cite: 365]
                    if (isset($menu_items) && is_array($menu_items)) {
                        foreach ($menu_items as $item) {
                            // Проверяем, что позиция меню 'top' [cite: 298, 365] и оно активно [cite: 298]
                            if (isset($item['position']) && $item['position'] == 'top' && isset($item['aktif']) && $item['aktif'] == 'Y') {
                                echo '<li class="nav-item">';
                                // base_url() используется для формирования URL, как в примерах PDF [cite: 210]
                                echo '<a class="nav-link" href="' . base_url(htmlspecialchars($item['link'])) . '">' . htmlspecialchars($item['nama_menu']) . '</a>';
                                echo '</li>';
                            }
                        }
                    } else {
                        // Запасные пункты меню, если $menu_items не предоставлены
                        echo '<li class="nav-item"><a class="nav-link" href="' . base_url('produk') . '">Produk</a></li>'; // [cite: 946]
                        echo '<li class="nav-item"><a class="nav-link" href="' . base_url('berita') . '">Berita</a></li>'; // [cite: 1108]
                        echo '<li class="nav-item"><a class="nav-link" href="' . base_url('konfirmasi') . '">Konfirmasi</a></li>'; // [cite: 1053]
                        echo '<li class="nav-item"><a class="nav-link" href="' . site_url('administrator') . '">Login Admin</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">

        <?php if (isset($banners) && !empty($banners) && is_array($banners)): ?>
        <div id="mainBanner" class="carousel slide mb-4" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php foreach ($banners as $index => $banner): ?>
                <li data-target="#mainBanner" data-slide-to="<?php echo $index; ?>" class="<?php echo $index == 0 ? 'active' : ''; ?>"></li>
                <?php endforeach; ?>
            </ol>
            <div class="carousel-inner">
                <?php foreach ($banners as $index => $banner): // Таблица tb_banner[cite: 266]?>
                <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                    <img src="<?php echo base_url('asset/banner/' . htmlspecialchars($banner['gambar'])); // Предполагаемый путь к изображениям баннеров ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($banner['judul']); ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h5><?php echo htmlspecialchars($banner['judul']); ?></h5>
                        <?php /* <p>Дополнительное описание, если есть в таблице</p> */ ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <a class="carousel-control-prev" href="#mainBanner" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#mainBanner" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <?php else: ?>
        <div class="hero-section text-center">
            <h1 class="display-4">Selamat Datang di <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'Toko Kami'; ?>!</h1>
            <p class="lead">Temukan produk terbaik dengan penawaran menarik.</p>
        </div>
        <?php endif; ?>


        <section class="featured-products mt-5">
            <h2 class="section-title">🛍️ Produk Unggulan</h2>
            <div class="row">
                <?php
                // Пример вывода товаров из $featured_products
                // Данные из таблицы rb_produk [cite: 341] или produk
                if (isset($featured_products) && is_array($featured_products) && !empty($featured_products)) {
                    foreach ($featured_products as $product) {
                        $product_image = 'no-image.png'; // Изображение по умолчанию
                        // В PDF для продуктов используется поле 'gambar' [cite: 341, 742]
                        // Также используется разделитель ';' для нескольких изображений [cite: 741]
                        if (!empty($product['gambar'])) {
                            $img_array = explode(';', $product['gambar']);
                            $product_image = trim($img_array[0]);
                        }
                        // Путь к изображению продукта, как в PDF [cite: 1008]
                        $image_path = base_url('asset/foto_produk/' . htmlspecialchars($product_image));
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card product-card">
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                            <p class="card-text product-price">
                                <?php
                                // Отображение цены. В таблице rb_produk есть harga_konsumen, harga_reseller, harga_beli [cite: 341]
                                // В таблице produk есть harga [SQL file]
                                // Предполагаем, что отображается цена для потребителя
                                echo format_price(isset($product['harga_konsumen']) ? $product['harga_konsumen'] : (isset($product['harga']) ? $product['harga'] : 0));
                                ?>
                            </p>
                            <?php
                                // URL для деталей продукта, используя produk_seo из rb_produk [cite: 341] или id_produk
                                $detail_url = isset($product['produk_seo']) ? base_url('produk/detail/' . htmlspecialchars($product['produk_seo'])) : base_url('produk/detail/' . htmlspecialchars($product['id_produk']));
                            ?>
                            <a href="<?php echo $detail_url; ?>" class="btn btn-details btn-block">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    // Сообщение, если нет рекомендуемых товаров
                    echo '<div class="col-12"><p class="text-center">Produk unggulan akan segera hadir!</p></div>';
                }
                ?>
            </div>
        </section>

        <section class="latest-news mt-5">
            <h2 class="section-title">📰 Berita Terbaru</h2>
            <div class="row">
                <?php
                // Пример вывода новостей из $latest_news
                // Данные из таблицы tb_berita [cite: 268]
                if (isset($latest_news) && is_array($latest_news) && !empty($latest_news)) {
                    foreach ($latest_news as $news_item) {
                        $news_image = 'no-news-image.png'; // Изображение по умолчанию
                        // В PDF для новостей используется поле 'gambar' [cite: 268, 1094]
                        if (!empty($news_item['gambar'])) {
                            $news_image = $news_item['gambar'];
                        }
                        // Путь к изображению новости, как в PDF [cite: 1094]
                        $image_path_news = base_url('asset/foto_berita/' . htmlspecialchars($news_image));
                ?>
                <div class="col-md-4">
                    <div class="card news-card">
                        <img src="<?php echo $image_path_news; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news_item['judul']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($news_item['judul']); ?></h5>
                            <p class="card-text"><small class="text-muted"><?php echo isset($news_item['tanggal']) ? date('d M Y', strtotime($news_item['tanggal'])) : ''; ?></small></p>
                            <p class="card-text">
                                <?php
                                // Отображение краткого содержания или sub_judul [cite: 268]
                                $summary = isset($news_item['sub_judul']) ? $news_item['sub_judul'] : (isset($news_item['isiberita']) ? substr(strip_tags($news_item['isiberita']), 0, 100) . '...' : '');
                                echo htmlspecialchars($summary);
                                ?>
                            </p>
                            <?php
                                // URL для деталей новости, используя judul_seo из tb_berita [cite: 268, 1114]
                                $news_detail_url = isset($news_item['judul_seo']) ? base_url('berita/detail/' . htmlspecialchars($news_item['judul_seo'])) : base_url('berita/detail/' . htmlspecialchars($news_item['id_berita']));
                            ?>
                            <a href="<?php echo $news_detail_url; ?>" class="btn btn-outline-secondary btn-sm">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    // Сообщение, если нет новостей
                    echo '<div class="col-12"><p class="text-center">Belum ada berita terbaru.</p></div>';
                }
                ?>
            </div>
        </section>

    </div> <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo isset($website_name) ? htmlspecialchars($website_name) : 'Nama Toko Anda'; ?>. Semua Hak Dilindungi.</p>
            <?php
            // Можно добавить ссылки из меню с position='bottom' [cite: 298, 368]
            if (isset($menu_items) && is_array($menu_items)) {
                echo "<ul class='list-inline'>";
                foreach ($menu_items as $item) {
                    if (isset($item['position']) && $item['position'] == 'bottom' && isset($item['aktif']) && $item['aktif'] == 'Y') {
                        echo "<li class='list-inline-item'><a href='" . base_url(htmlspecialchars($item['link'])) . "'>" . htmlspecialchars($item['nama_menu']) . "</a></li>";
                    }
                }
                echo "</ul>";
            }
            // Информация из tb_identitas [SQL file]
            if(isset($identitas) && !empty($identitas)){
                 echo "<p><small>Email: ".htmlspecialchars($identitas['email'])." | Telp: ".htmlspecialchars($identitas['no_telp'])."</small></p>";
                 if(!empty($identitas['facebook'])) echo "<p><small><a href='".htmlspecialchars($identitas['facebook'])."' target='_blank'>Facebook</a></small></p>";
            }
            ?>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>