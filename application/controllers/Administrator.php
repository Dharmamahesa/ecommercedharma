<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Model_app $Model_app
 * @property Model_utama $Model_utama
 * @property Model_menu $Model_menu
 * @property Model_reseller $Model_reseller
 * @property Produk_model $Produk_model
 * @property Kategori_produk_model $Kategori_produk_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload
 * @property CI_Email $email
 * @property CI_Pagination $pagination
 * @property CI_Config $config
 */
class Administrator extends CI_Controller {

    protected $default_data;

    public function __construct() {
        parent::__construct();
        // Load helper dan library dasar
        $this->load->helper(array('url', 'form', 'phpmu', 'download', 'html', 'engine', 'cookie', 'string', 'text')); // Helper Captcha bisa dihapus jika tidak dipakai
        $this->load->library(array('session', 'form_validation', 'pagination', 'template', 'user_agent', 'email'));

        // Load model-model utama
        $this->load->model('Model_app');
        $this->load->model('Produk_model'); // Diasumsikan sudah dibuat
        $this->load->model('Kategori_produk_model'); // Diasumsikan sudah dibuat
        // $this->load->model('Model_utama'); // Load jika sering dipakai
        // $this->load->model('Model_menu');

        // Data default untuk view admin
        $identitas_query = $this->Model_app->edit('tb_identitas', ['id_identitas' => 1]);
        $identitas = $identitas_query ? $identitas_query->row_array() : null;

        $this->default_data = array(
            'website_name'    => isset($identitas['nama_website']) ? $identitas['nama_website'] : 'Admin Panel E-commerce',
            'controller_name' => 'administrator', // Untuk path URL dinamis di view
            'iden'            => $identitas
        );

        // Pengecekan Session untuk halaman yang memerlukan login
        $current_method = $this->uri->segment(2, 'index');
        $is_logged_in = ($this->session->userdata('id_session') && ($this->session->userdata('level') == 'admin' || $this->session->userdata('level') == 'user')); // Sesuaikan 'user' jika ada role lain
        $allowed_methods_without_login = ['index', 'login', 'logout']; // Method yang boleh diakses tanpa login

        if (!$is_logged_in && !in_array($current_method, $allowed_methods_without_login)) {
             $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Anda harus login untuk mengakses halaman ini.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
             redirect('administrator');
             exit;
        } elseif ($is_logged_in && in_array($current_method, $allowed_methods_without_login) && $current_method != 'logout') {
            redirect('administrator/home');
            exit;
        }
    }

    /**
     * Halaman login administrator.
     */
    public function index() {
        // Logika pengecekan jika sudah login sudah dihandle di constructor
        // Jika sampai sini, berarti pengguna belum login.

        $this->form_validation->set_rules('a', 'Username', 'required|trim|strip_tags');
        $this->form_validation->set_rules('b', 'Password', 'required|trim');

        if ($this->form_validation->run() === TRUE) {
            $username = $this->input->post('a');
            $password_input = $this->input->post('b');
            $password_hashed_for_check = hash("sha512", md5($password_input)); // Sesuai metode hashing di PDF

            // Menggunakan tabel rb_reseller untuk login admin, kolom 'password' menyimpan hash
            $cek = $this->Model_app->cek_login($username, $password_hashed_for_check, 'rb_reseller');
            $row = $cek->row_array();
            $total = $cek->num_rows();

            if ($total > 0) {
                // Login berhasil, set session data
                $this->session->set_userdata('upload_image_file_manager', true); // Sesuai PDF
                $this->session->set_userdata(array(
                    'username'     => $row['username'],
                    'nama_lengkap' => $row['nama_reseller'], // Menggunakan nama_reseller
                    'level'        => 'admin', // Asumsikan semua di rb_reseller yang bisa login adalah admin, atau tambahkan kolom 'level'
                    'id_session'   => $row['id_reseller'] // Menggunakan id_reseller sebagai id_session
                ));
                redirect('administrator/home');
            } else {
                // Login gagal
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Username atau Password Salah!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                redirect('administrator'); // Kembali ke form login
            }
        } else {
            // Validasi gagal atau permintaan GET pertama kali (form belum disubmit)
            $data = $this->default_data;
            $data['title'] = 'Administrator Login Panel';
            // Tidak ada Captcha sesuai permintaan
            $this->load->view('administrator/view_login', $data);
        }
    }

    /**
     * Halaman dashboard admin setelah login.
     */
    public function home() {
        // Pengecekan session sudah dilakukan di constructor
        $data = $this->default_data;
        $data['title'] = 'Dashboard Administrator';

        if ($this->session->level == 'admin') {
            // Ambil data statistik untuk dashboard
            $data['total_produk'] = $this->Model_app->view('rb_produk')->num_rows();
            $data['total_konsumen'] = $this->Model_app->view('rb_konsumen')->num_rows();
            $data['total_reseller'] = $this->Model_app->view_where('rb_reseller', array('username !=' => 'admin'))->num_rows(); // Reseller selain admin
            $data['total_pesanan_baru'] = $this->Model_app->view_where('rb_penjualan', array('proses' => '0', 'status_penjual' => 'reseller'))->num_rows(); // Pesanan dari konsumen ke reseller yang belum diproses

            $this->template->load('administrator/template', 'administrator/view_home_admin', $data);
        } elseif ($this->session->level == 'user') { // Jika ada level user lain untuk admin panel
            $user_info_query = $this->Model_app->view_where('rb_reseller', array('username' => $this->session->username));
            $data['users'] = $user_info_query ? $user_info_query->row_array() : null;
            $this->template->load('administrator/template', 'administrator/view_home_users', $data);
        } else {
            // Ini seharusnya tidak terjadi jika __construct bekerja benar
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Sesi tidak valid atau level tidak dikenali.</div>');
            redirect('administrator');
        }
    }

    /**
     * Logout administrator.
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('administrator');
    }

    // -------------------- MODUL: KATEGORI PRODUK (INDUK) --------------------
    public function kategori_produk() {
        $data = $this->default_data;
        $data['title'] = 'Manajemen Kategori Produk';
        $query_result = $this->Kategori_produk_model->get_all_kategori('nama_kategori', 'ASC');
        $data['record'] = $query_result ? $query_result->result_array() : [];
        $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk', $data);
    }

    public function tambah_kategori_produk() {
        $data = $this->default_data;
        $data['title'] = 'Tambah Kategori Produk Baru';

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim|is_unique[rb_kategori_produk.nama_kategori]');
        // 'kategori_seo' akan dibuat otomatis di model Kategori_produk_model

        if ($this->form_validation->run() === FALSE) {
            $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk_tambah', $data);
        } else {
            $data_insert = array('nama_kategori' => $this->input->post('nama_kategori', TRUE));
            // Untuk nama field 'a' sesuai view terakhir:
            // $data_insert = array('nama_kategori' => $this->input->post('a', TRUE));
            $this->Kategori_produk_model->insert_kategori($data_insert);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Kategori produk berhasil ditambahkan.</div>');
            redirect('administrator/kategori_produk');
        }
    }

    public function edit_kategori_produk($id = NULL) {
        if ($id === NULL || !is_numeric($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">ID Kategori produk tidak valid.</div>');
            redirect('administrator/kategori_produk'); return;
        }
        $data = $this->default_data;
        $data['title'] = 'Edit Kategori Produk';
        $kategori_query = $this->Kategori_produk_model->get_kategori_by_id($id);
        $data['rows'] = $kategori_query ? $kategori_query->row_array() : null;

        if (!$data['rows']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Kategori produk tidak ditemukan.</div>');
            redirect('administrator/kategori_produk'); return;
        }

        // Sesuaikan nama field validasi dengan nama input di form view_kategori_produk_edit
        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim');
        $this->form_validation->set_rules('id_kategori_produk', 'ID Kategori', 'required|integer');


        if ($this->form_validation->run() === FALSE) {
            $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk_edit', $data);
        } else {
            $data_update = array('nama_kategori' => $this->input->post('nama_kategori', TRUE));
            // Untuk nama field 'a' sesuai view terakhir:
            // $data_update = array('nama_kategori' => $this->input->post('a', TRUE));
            $this->Kategori_produk_model->update_kategori($this->input->post('id_kategori_produk', TRUE), $data_update);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Kategori produk berhasil diperbarui.</div>');
            redirect('administrator/kategori_produk');
        }
    }

    public function delete_kategori_produk($id = NULL) {
        if ($id === NULL || !is_numeric($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">ID Kategori produk tidak valid.</div>');
            redirect('administrator/kategori_produk'); return;
        }
        // PERHATIAN: Tambahkan pengecekan apakah ada produk atau sub-kategori yang menggunakan kategori ini sebelum dihapus
        $this->Kategori_produk_model->delete_kategori($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success">Kategori produk berhasil dihapus.</div>');
        redirect('administrator/kategori_produk');
    }

    // -------------------- MODUL: SUB KATEGORI PRODUK --------------------
    public function kategori_produk_sub() {
        $data = $this->default_data;
        $data['title'] = 'Manajemen Sub Kategori Produk';
        $query_result = $this->Kategori_produk_model->get_all_sub_kategori_with_main_kategori('b.nama_kategori ASC, a.nama_kategori_sub', 'ASC');
        $data['record'] = $query_result ? $query_result->result_array() : [];
        $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk_sub', $data);
    }

    public function tambah_kategori_produk_sub() {
        $data = $this->default_data;
        $data['title'] = 'Tambah Sub Kategori Produk';
        $kategori_utama_q = $this->Kategori_produk_model->get_all_kategori('nama_kategori', 'ASC');
        $data['kategori_utama'] = $kategori_utama_q ? $kategori_utama_q->result_array() : [];

        $this->form_validation->set_rules('id_kategori_produk', 'Kategori Utama', 'required|integer');
        $this->form_validation->set_rules('nama_kategori_sub', 'Nama Sub Kategori', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk_tambah_sub', $data);
        } else {
            $data_insert = array(
                'id_kategori_produk' => $this->input->post('id_kategori_produk', TRUE),
                'nama_kategori_sub'  => $this->input->post('nama_kategori_sub', TRUE)
            );
            $this->Kategori_produk_model->insert_sub_kategori($data_insert);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Sub kategori produk berhasil ditambahkan.</div>');
            redirect('administrator/kategori_produk_sub');
        }
    }

    public function edit_kategori_produk_sub($id = NULL) {
        if ($id === NULL || !is_numeric($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">ID Sub Kategori tidak valid.</div>');
            redirect('administrator/kategori_produk_sub'); return;
        }
        $data = $this->default_data;
        $data['title'] = 'Edit Sub Kategori Produk';
        $sub_kategori_q = $this->Kategori_produk_model->get_sub_kategori_by_id($id);
        $data['rows'] = $sub_kategori_q ? $sub_kategori_q->row_array() : null;
        $kategori_utama_q = $this->Kategori_produk_model->get_all_kategori('nama_kategori', 'ASC');
        $data['kategori_utama'] = $kategori_utama_q ? $kategori_utama_q->result_array() : [];

        if (!$data['rows']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Sub kategori produk tidak ditemukan.</div>');
            redirect('administrator/kategori_produk_sub'); return;
        }

        $this->form_validation->set_rules('id_kategori_produk', 'Kategori Utama', 'required|integer');
        $this->form_validation->set_rules('nama_kategori_sub', 'Nama Sub Kategori', 'required|trim');
        $this->form_validation->set_rules('id_kategori_produk_sub', 'ID', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            $this->template->load('administrator/template', 'administrator/additional/mod_kategori_produk/view_kategori_produk_edit_sub', $data);
        } else {
            $data_update = array(
                'id_kategori_produk' => $this->input->post('id_kategori_produk', TRUE),
                'nama_kategori_sub'  => $this->input->post('nama_kategori_sub', TRUE)
            );
            $this->Kategori_produk_model->update_sub_kategori($this->input->post('id_kategori_produk_sub', TRUE), $data_update);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Sub kategori produk berhasil diperbarui.</div>');
            redirect('administrator/kategori_produk_sub');
        }
    }

     public function delete_kategori_produk_sub($id = NULL) {
        if ($id === NULL || !is_numeric($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">ID Sub Kategori tidak valid.</div>');
            redirect('administrator/kategori_produk_sub'); return;
        }
        // PERHATIAN: Tambahkan pengecekan apakah ada produk yang menggunakan sub kategori ini
        $this->Kategori_produk_model->delete_sub_kategori($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success">Sub kategori produk berhasil dihapus.</div>');
        redirect('administrator/kategori_produk_sub');
    }


    // --- PRODUK (ADMIN) ---
    // public function produk() { /* Load Produk_model, tampilkan view_produk admin */ }
    // public function tambah_produk() { /* Form tambah produk oleh admin, load kategori, dll */ }
    // public function edit_produk($id_produk) { /* Form edit produk oleh admin */ }
    // public function delete_produk($id_produk) { /* Hapus produk */ }

    // --- KONSUMEN (ADMIN) ---
    // public function konsumen() { /* Load Model_app atau Model_reseller, tampilkan view_konsumen admin */ }
    // ... (CRUD konsumen)

    // --- RESELLER (ADMIN) ---
    // public function reseller() { /* Load Model_app atau Model_reseller, tampilkan view_reseller admin */ }
    // ... (CRUD reseller)

    // --- PENJUALAN (ADMIN ke RESELLER) ---
    // public function penjualan() { /* Manajemen pesanan dari reseller ke admin */ }
    // ... (Lihat detail, proses, dll.)

    // --- BERITA (ADMIN) ---
    // public function listberita() { /* Manajemen berita, load Model_utama atau Model_app */ }
    // public function tambah_listberita() { /* ... */ }
    // public function edit_listberita($id) { /* ... */ }
    // public function delete_listberita($id) { /* ... */ }
    // public function kategoriberita() { /* Pindahkan logika dari contoh sebelumnya ke sini jika belum */ }

    // Dan seterusnya untuk semua modul dari PDF...
}