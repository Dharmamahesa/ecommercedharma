<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mylibrary {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    // Mendapatkan waktu sekarang
    public function waktu_sekarang() {
        date_default_timezone_set('Asia/Jakarta');
        return date('Y-m-d H:i:s');
    }

    // Menampilkan flashdata alert
    public function alert($message, $type = 'success') {
        return '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">'
                . $message .
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }

    // Mengambil konfigurasi identitas website dari tabel tb_identitas
    public function identitas_web() {
        return $this->CI->db->get('tb_identitas')->row_array();
    }

    // Fungsi untuk mendapatkan ip address
    public function ip_address() {
        return $this->CI->input->ip_address();
    }

    // Fungsi untuk mengecek browser
    public function browser_user() {
        return $this->CI->input->user_agent();
    }
}
