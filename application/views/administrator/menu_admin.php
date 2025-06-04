<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $current_method, $controller_name_path are expected to be set by the main template or passed by the controller
$current_method = isset($current_method) ? $current_method : $this->uri->segment(2, 'home'); // Default to 'home' if segment 2 is empty
$controller_name_path = isset($controller_name) ? $controller_name : 'administrator';

/**
 * Helper function to check menu access.
 * This function assumes $this->Model_app and $this->session are available
 * because this view is loaded within the scope of a controller method
 * where Model_app has been loaded and session is active.
 */
if (!function_exists('cek_menu_akses_admin_view')) {
    function cek_menu_akses_admin_view($link_modul) {
        $CI =& get_instance();
        if (!isset($CI->Model_app) || !method_exists($CI->Model_app, 'umenu_akses') || !$CI->session->userdata('id_session')) {
            // Fallback or log error if Model_app or session is not available
            // For security, default to no access if components are missing
            if (isset($CI->session) && $CI->session->userdata('level') == 'admin') return true; // Admin sees all if umenu_akses fails
            return false;
        }
        return $CI->Model_app->umenu_akses($link_modul, $CI->session->id_session) || ($CI->session->userdata('level') == 'admin');
    }
}

/**
 * Helper function to determine if a treeview menu should be active and open.
 * $methods_in_group: array of method names belonging to this treeview group.
 */
if (!function_exists('is_treeview_active_admin')) {
    function is_treeview_active_admin($methods_in_group, $current_method_active) {
        if (is_array($methods_in_group) && in_array($current_method_active, $methods_in_group)) {
            return 'active menu-open';
        }
        return '';
    }
}

/**
 * Helper function to determine if a menu item should be active.
 */
if (!function_exists('is_menu_item_active_admin')) {
    function is_menu_item_active_admin($method_name, $current_method_active) {
        if ($method_name == $current_method_active) {
            return 'active';
        }
        return '';
    }
}

// Define menu groups and their items for better organization
// The 'access_check' should match the 'link' column in your 'modul' table for umenu_akses()
$menu_groups = [
    'toko_marketplace' => [
        'title' => 'Toko / Marketplace',
        'icon' => 'fa-shopping-cart',
        'access_check_any' => ["konsumen", "reseller", "supplier", "kategori_produk", "produk", "rekening", "pembelian", "penjualan", "pembayaran_reseller", "keuangan", "kurir", "sopir", "konfirmasi_pembayaran_konsumen"],
        'methods_in_group' => ['konsumen', 'reseller', 'supplier', 'kategori_produk', 'kategori_produk_sub', 'produk', 'rekening', 'pembelian', 'penjualan', 'pembayaran_reseller', 'keuangan', 'kurir', 'sopir', 'konfirmasi_pembayaran_konsumen', 'laporan_penjualan'],
        'sub_groups' => [
            'master_data' => [
                'title' => 'Master Data',
                'icon' => 'fa-archive',
                'access_check_any' => ["konsumen", "reseller", "supplier", "kategori_produk", "produk", "rekening", "kurir", "sopir"],
                'methods_in_group' => ['konsumen', 'reseller', 'supplier', 'kategori_produk', 'kategori_produk_sub', 'produk', 'rekening', 'kurir', 'sopir'],
                'items' => [
                    ['title' => 'Data Konsumen', 'link' => 'konsumen', 'icon' => 'fa-user-circle-o', 'access_check' => 'konsumen'],
                    ['title' => 'Data Reseller', 'link' => 'reseller', 'icon' => 'fa-users', 'access_check' => 'reseller'],
                    ['title' => 'Data Supplier', 'link' => 'supplier', 'icon' => 'fa-truck', 'access_check' => 'supplier'],
                    ['title' => 'Kategori Produk', 'link' => 'kategori_produk', 'icon' => 'fa-tags', 'access_check' => 'kategori_produk'],
                    ['title' => 'Sub-Kategori Produk', 'link' => 'kategori_produk_sub', 'icon' => 'fa-tag', 'access_check' => 'kategori_produk_sub'],
                    ['title' => 'Data Produk', 'link' => 'produk', 'icon' => 'fa-cubes', 'access_check' => 'produk'],
                    ['title' => 'Rekening Perusahaan', 'link' => 'rekening', 'icon' => 'fa-credit-card', 'access_check' => 'rekening'],
                    // ['title' => 'Manajemen Kurir Internal', 'link' => 'kurir', 'icon' => 'fa-motorcycle', 'access_check' => 'kurir'],
                    // ['title' => 'Manajemen Sopir', 'link' => 'sopir', 'icon' => 'fa-id-badge', 'access_check' => 'sopir'],
                ]
            ],
            'transaksi' => [
                'title' => 'Transaksi',
                'icon' => 'fa-exchange',
                'access_check_any' => ["pembelian", "penjualan", "pembayaran_reseller", "konfirmasi_pembayaran_konsumen"],
                'methods_in_group' => ['pembelian', 'penjualan', 'pembayaran_reseller', 'konfirmasi_pembayaran_konsumen'],
                'items' => [
                    ['title' => 'Pembelian (ke Supplier)', 'link' => 'pembelian', 'icon' => 'fa-shopping-basket', 'access_check' => 'pembelian'],
                    ['title' => 'Penjualan (ke Reseller)', 'link' => 'penjualan', 'icon' => 'fa-dollar', 'access_check' => 'penjualan'],
                    ['title' => 'Pembayaran Reseller', 'link' => 'pembayaran_reseller', 'icon' => 'fa-money', 'access_check' => 'pembayaran_reseller'],
                    ['title' => 'Konfirmasi Konsumen', 'link' => 'konfirmasi_pembayaran_konsumen', 'icon' => 'fa-check-square-o', 'access_check' => 'konfirmasi_pembayaran_konsumen'],
                ]
            ],
            'report' => [
                'title' => 'Report',
                'icon' => 'fa-bar-chart',
                'access_check_any' => ["keuangan", "laporan_penjualan"],
                'methods_in_group' => ['keuangan', 'laporan_penjualan'],
                'items' => [
                    ['title' => 'Keuangan Reseller', 'link' => 'keuangan', 'icon' => 'fa-book', 'access_check' => 'keuangan'],
                    // ['title' => 'Laporan Penjualan', 'link' => 'laporan_penjualan', 'icon' => 'fa-file-text-o', 'access_check' => 'laporan_penjualan'],
                ]
            ],
        ]
    ],
    'menu_utama_web' => [
        'title' => 'Menu Utama Web',
        'icon' => 'fa-laptop',
        'access_check_any' => ["menuwebsite", "halamanbaru"],
        'methods_in_group' => ['menuwebsite', 'halamanbaru'],
        'items' => [
            ['title' => 'Manajemen Menu', 'link' => 'menuwebsite', 'icon' => 'fa-list-alt', 'access_check' => 'menuwebsite'],
            ['title' => 'Halaman Statis', 'link' => 'halamanbaru', 'icon' => 'fa-file-o', 'access_check' => 'halamanbaru'],
        ]
    ],
    'modul_berita' => [
        'title' => 'Modul Berita',
        'icon' => 'fa-pencil-square-o',
        'access_check_any' => ["listberita", "kategoriberita", "tagberita", "komentarberita", "sensorkomentar"],
        'methods_in_group' => ['listberita', 'kategoriberita', 'tagberita', 'komentarberita', 'sensorkomentar'],
        'items' => [
            ['title' => 'Semua Berita', 'link' => 'listberita', 'icon' => 'fa-newspaper-o', 'access_check' => 'listberita'],
            ['title' => 'Kategori Berita', 'link' => 'kategoriberita', 'icon' => 'fa-tags', 'access_check' => 'kategoriberita'],
            ['title' => 'Tag Berita', 'link' => 'tagberita', 'icon' => 'fa-hashtag', 'access_check' => 'tagberita'],
            ['title' => 'Komentar Berita', 'link' => 'komentarberita', 'icon' => 'fa-comments-o', 'access_check' => 'komentarberita'],
            ['title' => 'Sensor Komentar', 'link' => 'sensorkomentar', 'icon' => 'fa-ban', 'access_check' => 'sensorkomentar'],
        ]
    ],
    'modul_iklan' => [
        'title' => 'Modul Iklan',
        'icon' => 'fa-picture-o',
        'access_check_any' => ["iklanatas", "iklanhome", "iklansidebar", "banner"],
        'methods_in_group' => ['iklanatas', 'iklanhome', 'iklansidebar', 'banner'],
        'items' => [
            ['title' => 'Iklan Atas', 'link' => 'iklanatas', 'icon' => 'fa-object-group', 'access_check' => 'iklanatas'],
            ['title' => 'Iklan Tengah (Home)', 'link' => 'iklanhome', 'icon' => 'fa-object-ungroup', 'access_check' => 'iklanhome'],
            ['title' => 'Iklan Sidebar', 'link' => 'iklansidebar', 'icon' => 'fa-puzzle-piece', 'access_check' => 'iklansidebar'],
            ['title' => 'Iklan Link (Banner)', 'link' => 'banner', 'icon' => 'fa-link', 'access_check' => 'banner'],
        ]
    ],
    'modul_web_lainnya' => [
        'title' => 'Modul Web Lainnya',
        'icon' => 'fa-globe',
        'access_check_any' => ["agenda", "sekilasinfo", "download", "alamat", "pesanmasuk"],
        'methods_in_group' => ['agenda', 'sekilasinfo', 'download', 'alamat', 'pesanmasuk'], // 'ym' dihilangkan karena usang
        'items' => [
            ['title' => 'Agenda', 'link' => 'agenda', 'icon' => 'fa-calendar-plus-o', 'access_check' => 'agenda'],
            ['title' => 'Sekilas Info', 'link' => 'sekilasinfo', 'icon' => 'fa-info-circle', 'access_check' => 'sekilasinfo'],
            ['title' => 'Download Area', 'link' => 'download', 'icon' => 'fa-download', 'access_check' => 'download'],
            ['title' => 'Alamat Kontak', 'link' => 'alamat', 'icon' => 'fa-map-marker', 'access_check' => 'alamat'],
            ['title' => 'Pesan Masuk', 'link' => 'pesanmasuk', 'icon' => 'fa-envelope', 'access_check' => 'pesanmasuk'],
        ]
    ],
    'pengaturan_users' => [
        'title' => 'Pengaturan & Users',
        'icon' => 'fa-cogs',
        'access_check_any' => ["manajemenuser", "manajemenmodul", "identitaswebsite", "logowebsite", "backgroundwebsite"],
        'methods_in_group' => ['manajemenuser', 'manajemenmodul', 'identitaswebsite', 'logowebsite', 'backgroundwebsite', 'edit_manajemenuser'], // edit_manajemenuser ditambahkan agar treeview aktif saat edit profil
        'items' => [
            ['title' => 'Manajemen User', 'link' => 'manajemenuser', 'icon' => 'fa-user-secret', 'access_check' => 'manajemenuser'],
            ['title' => 'Manajemen Modul', 'link' => 'manajemenmodul', 'icon' => 'fa-check-square', 'access_check' => 'manajemenmodul'],
            ['title' => 'Identitas Website', 'link' => 'identitaswebsite', 'icon' => 'fa-info', 'access_check' => 'identitaswebsite'],
            ['title' => 'Logo Website', 'link' => 'logowebsite', 'icon' => 'fa-image', 'access_check' => 'logowebsite'],
            ['title' => 'Background Website', 'link' => 'backgroundwebsite', 'icon' => 'fa-photo', 'access_check' => 'backgroundwebsite'],
        ]
    ],
];
?>

<ul class="sidebar-menu" data-widget="tree">
    <li class="header">MENU UTAMA NAVIGASI</li>

    <li class="<?php echo is_menu_item_active_admin('home', $current_method); ?>">
        <a href="<?php echo site_url($controller_name_path . '/home'); ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
    </li>

    <?php foreach ($menu_groups as $group_key => $group): ?>
        <?php
            // Cek apakah user punya akses ke salah satu item di grup ini
            $has_access_to_group = false;
            if (isset($group['access_check_any']) && is_array($group['access_check_any'])) {
                foreach($group['access_check_any'] as $acc_check) {
                    if (cek_menu_akses_admin_view($acc_check)) {
                        $has_access_to_group = true;
                        break;
                    }
                }
            } elseif (isset($group['items']) && is_array($group['items'])) { // Fallback jika access_check_any tidak ada
                 foreach($group['items'] as $item_acc) {
                    if (cek_menu_akses_admin_view($item_acc['access_check'])) {
                        $has_access_to_group = true;
                        break;
                    }
                }
            }


            if ($has_access_to_group):
        ?>
        <li class="treeview <?php echo is_treeview_active_admin($group['methods_in_group'], $current_method); ?>">
            <a href="#">
                <i class="fa <?php echo $group['icon']; ?>"></i> <span><?php echo htmlspecialchars($group['title']); ?></span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <?php if (isset($group['items']) && is_array($group['items'])): ?>
                    <?php foreach ($group['items'] as $item): ?>
                        <?php if (cek_menu_akses_admin_view($item['access_check'])): ?>
                        <li class="<?php echo is_menu_item_active_admin($item['link'], $current_method); ?>">
                            <a href="<?php echo site_url($controller_name_path . '/' . $item['link']); ?>">
                                <i class="fa <?php echo $item['icon']; ?>"></i> <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($group['sub_groups']) && is_array($group['sub_groups'])): // Untuk treeview di dalam treeview ?>
                    <?php foreach ($group['sub_groups'] as $sub_group_key => $sub_group): ?>
                         <?php
                            $has_access_to_sub_group = false;
                            if (isset($sub_group['access_check_any']) && is_array($sub_group['access_check_any'])) {
                                foreach($sub_group['access_check_any'] as $acc_check_sub) {
                                    if (cek_menu_akses_admin_view($acc_check_sub)) {
                                        $has_access_to_sub_group = true;
                                        break;
                                    }
                                }
                            } elseif (isset($sub_group['items']) && is_array($sub_group['items'])) {
                                 foreach($sub_group['items'] as $item_acc_sub) {
                                    if (cek_menu_akses_admin_view($item_acc_sub['access_check'])) {
                                        $has_access_to_sub_group = true;
                                        break;
                                    }
                                }
                            }
                            if ($has_access_to_sub_group):
                        ?>
                        <li class="treeview <?php echo is_treeview_active_admin($sub_group['methods_in_group'], $current_method); ?>">
                            <a href="#"><i class="fa <?php echo $sub_group['icon']; ?>"></i> <?php echo htmlspecialchars($sub_group['title']); ?>
                                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                <?php foreach ($sub_group['items'] as $sub_item): ?>
                                    <?php if (cek_menu_akses_admin_view($sub_item['access_check'])): ?>
                                    <li class="<?php echo is_menu_item_active_admin($sub_item['link'], $current_method); ?>">
                                        <a href="<?php echo site_url($controller_name_path . '/' . $sub_item['link']); ?>">
                                            <i class="fa <?php echo $sub_item['icon']; ?>"></i> <?php echo htmlspecialchars($sub_item['title']); ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endif; // End sub_group access check ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; // End group access check ?>
    <?php endforeach; ?>

    <li class="<?php echo is_menu_item_active_admin('edit_manajemenuser/' . $this->session->userdata('username'), $current_method . '/' . $this->uri->segment(3)); // Penanganan khusus untuk edit profil sendiri ?>">
        <a href="<?php echo site_url($controller_name_path . '/edit_manajemenuser/' . $this->session->userdata('username')); ?>">
            <i class="fa fa-user-circle"></i> <span>Edit Profil Saya</span>
        </a>
    </li>
    <li>
        <a href="<?php echo site_url($controller_name_path . '/logout'); ?>">
            <i class="fa fa-sign-out text-red"></i> <span class="text-red">Logout</span>
        </a>
    </li>
</ul>