<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Format tanggal ke format Indonesia
if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal){
        $bulan = array(
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        );
        $pecahkan = explode('-', $tanggal);
        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
}

// Format ke rupiah
if (!function_exists('rupiah')) {
    function rupiah($angka){
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

// Membuat slug SEO dari string
if (!function_exists('seo_title')) {
    function seo_title($s) {
        $c = array(' ');
        $d = array('-', '/','\\',',','.','#',':',';','\'','"','[',']','{','}',')','(','|','`','~','!','@','%','$','^','&','*','=','?','+');
        $s = str_replace($d, '', $s); // hilangkan karakter aneh
        $s = str_replace($c, '-', $s); // ganti spasi dengan -
        return strtolower($s);
    }
}

// Batasi jumlah kata dari teks
if (!function_exists('limit_text')) {
    function limit_text($text, $limit) {
        $words = explode(' ', $text);
        if (count($words) > $limit) {
            return implode(' ', array_slice($words, 0, $limit)) . '...';
        } else {
            return $text;
        }
    }
}

// Cek login admin
if (!function_exists('cek_session_admin')) {
    function cek_session_admin(){
        $ci =& get_instance();
        if (!$ci->session->userdata('level')){
            redirect('main');
        }
    }
}

// Cek login user (reseller)
if (!function_exists('cek_session_user')) {
    function cek_session_user(){
        $ci =& get_instance();
        if (!$ci->session->userdata('id_reseller')){
            redirect('reseller/login');
        }
    }
}
if (!function_exists('template')) {
    /**
     * Mengembalikan nama direktori template aktif.
     * Anda bisa mengambil nilai ini dari database (tabel identitas atau pengaturan)
     * atau dari file konfigurasi CodeIgniter.
     * Untuk contoh ini, kita akan menggunakan nilai default atau mengambil dari config.
     */
    function template() {
        $CI =& get_instance();

        // Opsi 1: Ambil dari item konfigurasi (lebih fleksibel)
        // Anda bisa menambahkan $config['theme_directory'] = 'nama_folder_template_anda'; di application/config/config.php
        $active_template_dir = $CI->config->item('theme_directory');
        if ($active_template_dir) {
            return $active_template_dir;
        }

        // Opsi 2: Ambil dari database (misalnya tabel tb_identitas jika ada kolom untuk template)
        // Contoh jika Anda menambahkan kolom 'folder_template' di tb_identitas:
        /*
        $identitas = $CI->db->get_where('tb_identitas', array('id_identitas' => 1))->row_array();
        if (isset($identitas['folder_template']) && !empty($identitas['folder_template'])) {
            return $identitas['folder_template'];
        }
        */

        // Opsi 3: Nilai default jika tidak ada konfigurasi atau data di database
        // Sesuaikan 'phpmu-one' dengan nama folder template default Anda di application/views/
        // Berdasarkan PDF, sering disebut 'phpmu-one' atau path dinamis lainnya.
        // Jika view login member Anda ada di application/views/template_frontend/view_login_member.php
        // dan template utamanya di application/views/template_frontend/template.php,
        // maka fungsi ini bisa mengembalikan 'template_frontend'.
        return 'phpmu-one'; // GANTI DENGAN NAMA DIREKTORI TEMPLATE DEFAULT ANDA
    }
}
