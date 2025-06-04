<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Produk_model $Produk_model
 * @property Kategori_model $Kategori_model
 * @property User_model $User_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property Produk_model $Produk_model 
 */
class Home extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Produk_model'); // Memuat model Produk_model
    }

    public function index() {
        $data['produk'] = $this->Produk_model->get_all(); // Mengambil semua produk
        $this->load->view('produk_view', $data); // Memuat view dengan data produk
    }

    // Fungsi untuk menambah produk
    public function tambah_produk() {
    if ($this->input->post()) {
        $data = [
            'nama_produk' => $this->input->post('nama_produk'),
            'produk_seo' => url_title($this->input->post('nama_produk'), 'dash', TRUE),
            'satuan' => $this->input->post('satuan'),
            'berat' => $this->input->post('berat'),
            'harga_beli' => $this->input->post('harga_beli'),
            'harga_reseller' => null,
            'harga_konsumen' => $this->input->post('harga_konsumen'),
            'stok' => $this->input->post('stok'),
            'username' => $this->session->userdata('username'),
            'waktu_input' => date('Y-m-d H:i:s')
        ];

        // Upload Gambar
        if (!empty($_FILES['userfile']['name'])) {
            $config['upload_path']   = './asset/foto_produk/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048; // 2MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('userfile')) {
                $upload_data = $this->upload->data();
                $data['gambar'] = $upload_data['file_name'];
            } else {
                // Tampilkan pesan error upload
                echo '<div class="alert alert-danger">';
                echo $this->upload->display_errors();
                echo '</div>';
                return; // hentikan proses jika gagal upload
            }
        }

        $this->Produk_model->insert($data);
        redirect('home');
    }

    $this->load->view('tambah_produk_view');
}


    // Fungsi untuk mengedit produk
    public function edit_produk($id) {
        // Logika untuk mengedit produk
        if ($this->input->post()) {
            $data = [
                'nama_produk' => $this->input->post('nama_produk'),
                'harga_beli' => $this->input->post('harga_beli'),
                'harga_konsumen' => $this->input->post('harga_konsumen'),
                'stok' => $this->input->post('stok'),
                'satuan' => $this->input->post('satuan'),
                'berat' => $this->input->post('berat'),
                // Tambahkan field lain sesuai kebutuhan
            ];
            $this->Produk_model->update($id, $data); // Memperbarui data produk
            redirect('home'); // Redirect ke halaman utama setelah berhasil
        }
        $data['produk'] = $this->Produk_model->get_by_id($id); // Mengambil data produk untuk ditampilkan di form
        $this->load->view('edit_produk_view', $data); // Memuat view untuk mengedit produk
    }

    // Fungsi untuk menghapus produk
    public function delete_produk($id) {
        $this->Produk_model->delete($id); // Menghapus produk
        redirect('home'); // Redirect ke halaman utama setelah berhasil
    }
}
