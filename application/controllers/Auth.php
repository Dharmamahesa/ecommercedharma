<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_reseller');
    }

    public function index()
    {
        $this->load->view('administrator/login');
    }

    public function login()
    {
        $username = $this->input->post('username');
        $password = hash('sha512', md5($this->input->post('password'))); // disesuaikan dengan struktur Anda

        $reseller = $this->db->get_where('rb_reseller', [
            'username' => $username,
            'password' => $password
        ])->row();

        if ($reseller) {
            $this->session->set_userdata([
                'id_reseller'   => $reseller->id_reseller,
                'username'      => $reseller->username,
                'nama_reseller' => $reseller->nama_reseller,
                'login'         => TRUE
            ]);
            redirect('dashboard'); // atau halaman tujuan Anda
        } else {
            $this->session->set_flashdata('error', 'Login gagal, username atau password salah');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
