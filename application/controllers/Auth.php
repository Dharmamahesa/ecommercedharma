<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    protected $default_data;

    public function __construct(){
        parent::__construct();
        $this->load->model('Model_app'); // Model_app digunakan untuk cek_login
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper(array('url', 'form', 'phpmu')); // Asumsi phpmu_helper ada

        // Data default untuk view
        $identitas_query = $this->Model_app->edit('tb_identitas', ['id_identitas' => 1]);
        $identitas = $identitas_query ? $identitas_query->row_array() : null;
        $this->default_data = array(
            'website_name'    => isset($identitas['nama_website']) ? $identitas['nama_website'] : 'Toko Online Saya',
            'controller_name' => 'auth',
            'iden'            => $identitas
        );
    }

    public function index(){
        // Bisa diarahkan ke login member atau tampilkan pilihan
        $this->login_member();
    }

    /**
     * Menampilkan form login member dan memproses login.
     */
    public function login_member() {
        // Jika sudah login sebagai member (konsumen atau reseller), arahkan ke dashboard masing-masing
        if ($this->session->userdata('id_konsumen')) {
            redirect('members/profile'); // Dashboard/profil konsumen
        }
        if ($this->session->userdata('id_reseller') && strtolower($this->session->userdata('username_reseller')) !== 'admin') { // Pastikan bukan admin
            redirect('reseller/dashboard'); // Dashboard reseller (Anda perlu buat controller Reseller.php)
        }

        $data = $this->default_data;
        $data['title'] = 'Login Member / Reseller';

        $this->form_validation->set_rules('username', 'Username', 'required|trim|strip_tags');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            // Tampilkan form login member
            // Menggunakan path view seperti di PDF untuk registrasi/login member
            // yaitu template().'/view_login_member' atau serupa
            // Untuk contoh ini, kita buat view di 'auth/view_login_member.php'
            $this->template->load(template().'/template', template().'/view_login_member', $data);
        } else {
            $username = $this->input->post('username');
            $password_input = $this->input->post('password');
            $password_hashed_for_check = hash("sha512", md5($password_input)); // Metode hashing sesuai PDF

            // 1. Coba cek sebagai Konsumen (tabel rb_konsumen)
            $cek_konsumen = $this->Model_app->cek_login($username, $password_hashed_for_check, 'rb_konsumen');
            if ($cek_konsumen && $cek_konsumen->num_rows() > 0) {
                $row_konsumen = $cek_konsumen->row_array();
                // Login berhasil sebagai konsumen
                $this->session->set_userdata(array(
                    'id_konsumen'         => $row_konsumen['id_konsumen'],
                    'username_konsumen'   => $row_konsumen['username'],
                    'nama_lengkap_konsumen' => $row_konsumen['nama_lengkap'],
                    'level_user'          => 'konsumen' // Set level user
                ));
                redirect('members/profile'); // Arahkan ke dashboard/profil konsumen
                return;
            }

            // 2. Jika tidak ditemukan sebagai konsumen, coba cek sebagai Reseller (tabel rb_reseller)
            // Pastikan user 'admin' tidak login melalui form ini
            if (strtolower($username) !== 'admin') {
                $cek_reseller = $this->Model_app->cek_login($username, $password_hashed_for_check, 'rb_reseller');
                if ($cek_reseller && $cek_reseller->num_rows() > 0) {
                    $row_reseller = $cek_reseller->row_array();
                    // Login berhasil sebagai reseller
                    $this->session->set_userdata(array(
                        'id_reseller'        => $row_reseller['id_reseller'],
                        'username_reseller'  => $row_reseller['username'],
                        'nama_lengkap_reseller' => $row_reseller['nama_reseller'],
                        'level_user'         => 'reseller' // Set level user
                        // Anda mungkin perlu session 'id_session' jika ada logika lain yang menggunakannya
                        // 'id_session' => $row_reseller['id_reseller']
                    ));
                    redirect('reseller/dashboard'); // Arahkan ke dashboard reseller (buat controller Reseller.php jika belum)
                    return;
                }
            }

            // Jika tidak cocok di keduanya
            $this->session->set_flashdata('message_login_member', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Username atau Password salah.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            redirect('auth/login_member');
        }
    }

    // ... (method register(), lupapassword(), reset_password() Anda yang sudah ada) ...
    // Pastikan method login() yang ada di Auth.php Anda saat ini (yang untuk admin)
    // diganti namanya atau dibedakan pathnya, misalnya menjadi auth/admin_login
    // atau jika tidak, pastikan logika di dalamnya tidak bentrok.
    // Untuk sementara, method login admin bisa tetap di controller Administrator.php
}