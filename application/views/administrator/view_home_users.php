<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ambil data pengguna dari controller
$username = isset($users['username']) ? htmlspecialchars($users['username']) : 'Pengguna';
$nama_lengkap = isset($users['nama_lengkap']) ? htmlspecialchars($users['nama_lengkap']) : (isset($users['nama_reseller']) ? htmlspecialchars($users['nama_reseller']) : 'Pengguna');
$email = isset($users['email']) ? htmlspecialchars($users['email']) : 'Tidak ada data';
$no_telp = isset($users['no_telpon']) ? htmlspecialchars($users['no_telpon']) : (isset($users['no_hp']) ? htmlspecialchars($users['no_hp']) : 'Tidak ada data');
$level_user = isset($users['level']) ? ucfirst(htmlspecialchars($users['level'])) : 'User';

// Untuk hak akses modul (sesuai contoh di PDF halaman 127)
// Anda perlu mengirimkan $hakakses dari controller
$hakakses_list = isset($hakakses) && $hakakses->num_rows() > 0 ? $hakakses->result_array() : [];

$controller_name_path = isset($controller_name) ? $controller_name : 'administrator';

?>

<style>
    /* Penyesuaian gaya agar lebih elegan dan minimalis */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
    body { /* Jika belum di-set oleh template utama */
        font-family: 'Poppins', sans-serif;
    }

    .user-dashboard-welcome {
        background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%); /* Warna ungu yang elegan */
        color: white;
        padding: 25px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .user-dashboard-welcome h3 {
        font-weight: 600;
        margin-top: 0;
    }
    .user-dashboard-welcome p {
        font-size: 1.05rem;
        opacity: 0.9;
    }

    .profile-info-card, .quick-actions-card, .module-access-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .card-title-custom {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
        font-size: 1.3rem;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
    }
    .profile-info dl dt {
        font-weight: 500;
        color: #495057;
        width: 150px; /* Lebar tetap untuk label */
    }
    .profile-info dl dd {
        margin-bottom: 0.8rem;
        color: #333;
    }

    .quick-action-btn {
        display: block;
        width: 100%;
        text-align: left;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: background-color 0.2s ease, transform 0.2s ease;
        color: #007bff;
        font-weight: 500;
    }
    .quick-action-btn:hover {
        background-color: #e2e6ea;
        transform: translateX(3px);
        text-decoration: none;
    }
    .quick-action-btn i {
        margin-right: 10px;
        width: 20px; /* Konsistensi ikon */
        text-align: center;
    }
    .module-list span {
        display: inline-block;
        background-color: #e9ecef;
        color: #495057;
        padding: 5px 10px;
        margin-right: 5px;
        margin-bottom: 5px;
        border-radius: 5px;
        font-size: 0.9em;
    }
</style>

<div class="user-dashboard-welcome">
    <h3>Halo, <?php echo $nama_lengkap; ?>!</h3>
    <p>Selamat datang di panel Anda. Berikut adalah ringkasan akun dan akses cepat ke fitur Anda.</p>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="profile-info-card">
            <h4 class="card-title-custom"><i class="fas fa-user-circle mr-2"></i>Informasi Akun Anda</h4>
            <dl class="dl-horizontal profile-info">
                <dt>Username</dt>
                <dd><?php echo $username; ?></dd>

                <dt>Nama Lengkap</dt>
                <dd><?php echo $nama_lengkap; ?></dd>

                <dt>Alamat Email</dt>
                <dd><?php echo $email; ?></dd>

                <dt>No. Telepon</dt>
                <dd><?php echo $no_telp; ?></dd>

                <dt>Level</dt>
                <dd><span class="badge badge-info" style="font-size: 0.9em; padding: 0.4em 0.7em;"><?php echo $level_user; ?></span></dd>
            </dl>
            <a href="<?php echo site_url($controller_name_path . '/edit_manajemenuser/' . $this->session->userdata('username')); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Profil Saya
            </a>
        </div>

        <?php if (!empty($hakakses_list)): ?>
        <div class="module-access-card">
            <h4 class="card-title-custom"><i class="fas fa-key mr-2"></i>Hak Akses Modul Anda</h4>
            <div class="module-list">
                <?php foreach ($hakakses_list as $mod): ?>
                    <?php if (isset($mod['nama_modul']) && isset($mod['link'])): ?>
                        <a href="<?php echo site_url($controller_name_path . '/' . $mod['link']); ?>" title="<?php echo htmlspecialchars($mod['nama_modul']); ?>">
                            <span><?php echo htmlspecialchars($mod['nama_modul']); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <small class="text-muted mt-2 d-block">Ini adalah modul yang dapat Anda akses berdasarkan hak yang diberikan.</small>
        </div>
        <?php endif; ?>

    </div>

    <div class="col-md-5">
        <div class="quick-actions-card">
            <h4 class="card-title-custom"><i class="fas fa-bolt mr-2"></i>Aksi Cepat</h4>
            <?php
            // Contoh Aksi Cepat, sesuaikan dengan modul yang dimiliki user ini
            // Anda bisa membuat array $quick_actions di controller berdasarkan hak akses
            $quick_actions = [];
            if (isset($hakakses_list) && !empty($hakakses_list)) {
                foreach ($hakakses_list as $mod_aksi) {
                    if (isset($mod_aksi['nama_modul']) && isset($mod_aksi['link'])) {
                        // Tentukan ikon berdasarkan nama modul atau link (ini contoh sederhana)
                        $icon = 'fas fa-puzzle-piece'; // Default icon
                        if (strpos($mod_aksi['link'], 'berita') !== false) $icon = 'fas fa-newspaper';
                        if (strpos($mod_aksi['link'], 'produk') !== false) $icon = 'fas fa-box-open';
                        // Tambahkan kondisi lain untuk ikon yang berbeda

                        $quick_actions[] = ['title' => $mod_aksi['nama_modul'], 'link' => $mod_aksi['link'], 'icon' => $icon];
                    }
                }
            } else {
                // Aksi default jika tidak ada hak akses modul spesifik yang bisa dijadikan quick link
                 $quick_actions[] = ['title' => 'Lihat Profil', 'link' => 'edit_manajemenuser/' . $this->session->userdata('username'), 'icon' => 'fas fa-id-badge'];
            }

            // Batasi jumlah quick links yang ditampilkan
            $quick_actions_display = array_slice($quick_actions, 0, 4);

            foreach ($quick_actions_display as $action):
            ?>
            <a href="<?php echo site_url($controller_name_path . '/' . $action['link']); ?>" class="quick-action-btn">
                <i class="<?php echo $action['icon']; ?>"></i> <span><?php echo htmlspecialchars($action['title']); ?></span>
            </a>
            <?php endforeach; ?>

            <?php if (empty($quick_actions_display)): ?>
                <p class="text-muted">Tidak ada aksi cepat yang tersedia untuk Anda saat ini.</p>
            <?php endif; ?>
        </div>

        <div class="alert alert-light mt-4" role="alert" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h5 class="alert-heading" style="font-weight:500; color: #555;"><i class="fas fa-info-circle"></i> Info Penting!</h5>
            <p style="font-size:0.95em; color: #666;">Pastikan informasi akun Anda selalu sesuai dengan data identitas yang valid. Untuk bantuan, silakan hubungi administrator.</p>
        </div>
    </div>
</div>