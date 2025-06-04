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
