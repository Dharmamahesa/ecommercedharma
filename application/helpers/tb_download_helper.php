<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Fungsi untuk memaksa download file dari folder tertentu
if (!function_exists('force_download_file')) {
    function force_download_file($filename) {
        $filepath = FCPATH . 'assets/files/' . $filename;

        if (file_exists($filepath)) {
            $CI =& get_instance();
            $CI->load->helper('download');
            force_download($filepath, NULL);
        } else {
            show_404(); // tampilkan error jika file tidak ditemukan
        }
    }
}
