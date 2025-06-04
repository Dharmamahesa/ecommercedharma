<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Fungsi untuk menghapus tag HTML dan karakter berbahaya
if (!function_exists('clean_string')) {
    function clean_string($string) {
        return htmlspecialchars(strip_tags($string));
    }
}

// Fungsi untuk membuat slug dari teks
if (!function_exists('create_slug')) {
    function create_slug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}

// Fungsi untuk waktu Indonesia
if (!function_exists('waktu_sekarang')) {
    function waktu_sekarang() {
        date_default_timezone_set('Asia/Jakarta');
        return date('Y-m-d H:i:s');
    }
}
