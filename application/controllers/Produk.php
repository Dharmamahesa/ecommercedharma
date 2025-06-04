<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Produk_model $Produk_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Form_validation $form_validation
 */
class Produk extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Produk_model'); // Untuk produk
        $this->load->model('Kategori_produk_model'); // PASTIKAN BARIS INI ADA DAN NAMA MODELNYA BENAR

        // Load library dan helper lain yang dibutuhkan
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Data default Anda
        $this->default_data = array(
            'website_name' => 'Toko Online Saya',
            'site_logo'    => 'logo.png',
            'controller_name' => 'produk'
        );
    }
     public function index() { // Method ini akan dijalankan untuk URL /produk
        $data = $this->default_data;
        $data['produk'] = $this->Produk_model->get_all();
        $this->load->view('produkviews', $data); // Pastikan view produkviews.php ada
    }


    public function tambah_produk() {
        $data = $this->default_data;
        $data['error_upload'] = ''; // Inisialisasi variabel error upload

        // Aturan validasi
        $this->form_validation->set_rules('nama_produk', 'Nama Produk', 'required|trim');
        $this->form_validation->set_rules('harga_konsumen', 'Harga Konsumen', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('stok', 'Stok', 'required|integer');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'numeric');
        $this->form_validation->set_rules('satuan', 'Satuan', 'trim');
        $this->form_validation->set_rules('berat', 'Berat', 'numeric');

        if ($this->form_validation->run() === FALSE) {
            // Jika validasi gagal, tampilkan kembali form tambah produk
            $this->load->view('tambah_produk_view', $data);
        } else {
            // Jika validasi berhasil, proses data
            $produk_data = [
                'nama_produk'    => $this->input->post('nama_produk'),
                'produk_seo'     => url_title($this->input->post('nama_produk'), 'dash', TRUE),
                'satuan'         => $this->input->post('satuan'),
                'berat'          => $this->input->post('berat'),
                'harga_beli'     => $this->input->post('harga_beli'),
                'harga_reseller' => NULL, // Atau $this->input->post('harga_reseller') jika ada fieldnya
                'harga_konsumen' => $this->input->post('harga_konsumen'),
                'stok'           => $this->input->post('stok'),
                'username'       => $this->session->userdata('username'), // Pastikan session 'username' sudah ada saat login
                'waktu_input'    => date('Y-m-d H:i:s')
                // 'id_kategori_produk' => $this->input->post('id_kategori_produk'), // Jika ada pemilihan kategori
                // 'id_kategori_produk_sub' => $this->input->post('id_kategori_produk_sub'), // Jika ada pemilihan sub kategori
            ];

            // Handle Upload Gambar
            $upload_gambar = TRUE;
            if (!empty($_FILES['userfile']['name'])) {
                $config['upload_path']   = './asset/foto_produk/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = 2048; // 2MB
                $config['encrypt_name']  = TRUE; // Mengenkripsi nama file untuk keamanan

                // Membuat direktori jika belum ada
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);
                }

                $this->load->library('upload', $config);
                $this->upload->initialize($config); // Inisialisasi ulang jika config diubah

                if ($this->upload->do_upload('userfile')) {
                    $upload_data = $this->upload->data();
                    $produk_data['gambar'] = $upload_data['file_name'];
                } else {
                    $upload_gambar = FALSE;
                    $data['error_upload'] = $this->upload->display_errors('<p class="text-danger">', '</p>');
                }
            } else {
                $produk_data['gambar'] = ''; // Atau set gambar default jika tidak ada upload
            }

            if ($upload_gambar) {
                $this->Produk_model->insert($produk_data);
                $this->session->set_flashdata('success', 'Produk berhasil ditambahkan!');
                redirect('produk');
            } else {
                // Jika upload gagal, tampilkan kembali form dengan pesan error
                $this->load->view('tambah_produk_view', $data);
            }
        }
    }

    // application/controllers/Produk.php

public function edit_produk($id = NULL) {
    if ($id === NULL) {
        show_404(); // Jika ID tidak ada di URL
        return;
    }

    $data = $this->default_data; // Berisi data default seperti 'website_name', 'controller_name'

    // 1. Mengambil data produk yang akan diedit dari model
    $produk_item = $this->Produk_model->get_by_id($id); // Asumsi method ini mengembalikan objek atau array

    if (!$produk_item) {
        show_404(); // Jika produk dengan ID tersebut tidak ditemukan
        return;
    }
    // 2. Meneruskan data produk ke array $data untuk dikirim ke view
    //    Saya akan menggunakan 'rows' agar konsisten dengan view yang telah didesain ulang.
    //    Jika $produk_item adalah array, biarkan. Jika objek, tidak masalah untuk view yang menggunakan ->
    $data['rows'] = $produk_item;

    // Anda juga perlu mengirim data lain yang dibutuhkan view seperti daftar kategori, sub-kategori, data diskon, dll.
    $data['record'] = $this->Kategori_produk_model->get_all_kategori(); // Contoh ambil semua kategori utama

    // Ambil sub kategori yang relevan untuk kategori produk saat ini (untuk dropdown awal)
    // Pastikan $produk_item->id_kategori_produk ada
    if (isset($produk_item->id_kategori_produk)) {
        $data['sub_kategori_produk_terpilih'] = $this->Kategori_produk_model->get_sub_kategori_by_kategori_id($produk_item->id_kategori_produk);
    } else {
        $data['sub_kategori_produk_terpilih'] = [];
    }

    // Ambil data diskon
    $data['disk'] = $this->Produk_model->get_diskon_produk($id, $this->session->userdata('id_reseller')); // Sesuaikan
    if (!$data['disk']) { // Jika tidak ada diskon, buat objek kosong agar tidak error di view
        $data['disk'] = (object)['diskon' => 0];
    }


    // Hitung stok efektif saat ini (ini contoh, sesuaikan dengan logika Anda)
    // $jual = $this->model_reseller->jual_reseller($this->session->userdata('id_reseller'), $id)->row_array();
    // $beli = $this->model_reseller->beli_reseller($this->session->userdata('id_reseller'), $id)->row_array();
    // $data['stok_saat_ini'] = (isset($beli['beli']) ? $beli['beli'] : 0) - (isset($jual['jual']) ? $jual['jual'] : 0);
    // Untuk edit, mungkin lebih baik menampilkan stok yang tercatat di tabel produk saja:
    $data['stok_saat_ini'] = isset($produk_item->stok) ? $produk_item->stok : 0;


    // Aturan validasi
    $this->form_validation->set_rules('b', 'Nama Produk', 'required|trim'); // 'b' sesuai name di view
    $this->form_validation->set_rules('f', 'Harga Jual', 'required|numeric|greater_than[0]'); // 'f' sesuai name di view
    $this->form_validation->set_rules('stok', 'Tambah Stok', 'integer'); // 'stok' untuk penambahan stok
    // Tambahkan aturan validasi lainnya sesuai kebutuhan

    if ($this->form_validation->run() === FALSE) {
        // 3. Jika validasi GAGAL atau ini adalah permintaan GET pertama kali, tampilkan form dengan data yang ada
        $data['error_upload'] = $this->session->flashdata('error_upload_edit'); // Ambil pesan error upload jika ada
        $this->load->view('edit_produk_view', $data); // Pastikan path view benar
    } else {
        // Proses update data
        $produk_data_update = [
            'nama_produk'    => $this->input->post('b'),
            'produk_seo'     => url_title($this->input->post('b'), 'dash', TRUE),
            'id_kategori_produk' => $this->input->post('a'),
            'id_kategori_produk_sub' => $this->input->post('aa'),
            'satuan'         => $this->input->post('c'),
            'berat'          => $this->input->post('berat'),
            'harga_beli'     => $this->input->post('d'),
            'harga_konsumen' => $this->input->post('f'),
            // Logika update stok: tambahkan input 'stok' ke stok yang ada
            'stok'           => (isset($produk_item->stok) ? $produk_item->stok : 0) + (int)$this->input->post('stok'),
            'keterangan'     => $this->input->post('ff'),
        ];

        // Handle Upload Gambar (jika ada file baru yang diupload)
        // ... (logika upload gambar seperti di controller sebelumnya) ...
        // Jika gambar diupload, update $produk_data_update['gambar'] dan hapus gambar lama

        if (!empty($_FILES['userfile']['name'][0])) { // Cek apakah ada file yang dipilih (untuk multiple upload)
            // ... (logika upload file sama seperti sebelumnya) ...
            // Jika berhasil: $produk_data_update['gambar'] = $nama_file_tergabung;
            // Hapus gambar lama
        }

        $this->Produk_model->update($id, $produk_data_update);

        // Update diskon terpisah jika tabelnya berbeda
        $diskon_data = ['diskon' => $this->input->post('diskon')];
        // Anda perlu method untuk update atau insert diskon di model
        // $this->Produk_model->update_diskon_produk($id, $this->session->userdata('id_reseller'), $diskon_data);

        $this->session->set_flashdata('success', 'Produk berhasil diperbarui!');
        redirect('produk'); // Atau ke halaman produk reseller
    }
}
    public function delete_produk($id = NULL) {
        if ($id === NULL) {
            show_404();
            return;
        }

        $product = $this->Produk_model->get_by_id($id);
        if (!$product) {
            show_404();
            return;
        }

        // Hapus gambar terkait dari server
        if (!empty($product->gambar)) {
            $image_path = './asset/foto_produk/' . $product->gambar;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $this->Produk_model->delete($id);
        $this->session->set_flashdata('success', 'Produk berhasil dihapus!');
        redirect('produk');
    }

    /**
     * Method contoh untuk menampilkan detail produk berdasarkan SEO URL atau ID
     * Anda mungkin memerlukan method ini jika link detail di view produk mengarah ke sini.
     */
    public function detail($seo_or_id = NULL) {
        if ($seo_or_id === NULL) {
            show_404();
            return;
        }

        $data = $this->default_data;
        // Coba cari berdasarkan produk_seo dulu, lalu berdasarkan id_produk jika tidak ketemu
        $produk = $this->Produk_model->get_by_seo($seo_or_id);
        if (!$produk) {
            $produk = $this->Produk_model->get_by_id($seo_or_id);
        }

        if (!$produk) {
            show_404();
            return;
        }

        $data['produk_detail'] = $produk;
        $data['title'] = htmlspecialchars($produk->nama_produk); // Judul halaman detail
        // Anda perlu membuat view 'produk_detail_view.php'
        $this->load->view('produk_detail_view', $data);
    }
}