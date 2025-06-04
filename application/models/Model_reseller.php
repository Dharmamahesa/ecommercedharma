<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_reseller extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Method top_menu() ada di PDF, tapi fungsinya mirip dengan yang ada di Model_menu.
    // Jika ini untuk menu spesifik reseller, Anda bisa implementasikan di sini.
    // Jika sama, sebaiknya gunakan Model_menu.
    /*
    public function top_menu(){
        // Implementasi jika berbeda atau spesifik untuk reseller
        // return $this->db->query("SELECT * FROM menu where position='Top' AND khusus_reseller='Y' ORDER BY urutan ASC");
    }
    */

    // --- TESTIMONI ---
    public function testimoni() {
        // Mengambil testimoni dengan join ke tabel konsumen
        $this->db->select('testimoni.*, rb_konsumen.nama_lengkap, rb_konsumen.id_konsumen');
        $this->db->from('testimoni');
        $this->db->join('rb_konsumen', 'testimoni.id_konsumen = rb_konsumen.id_konsumen', 'left');
        $this->db->order_by('testimoni.id_testimoni', 'DESC');
        $query = $this->db->get();
        return $query; // Di controller bisa ->result_array() atau ->result()
    }

    public function testimoni_edit($id) {
        // Mengambil detail testimoni untuk diedit
        $this->db->select('testimoni.*, rb_konsumen.nama_lengkap, rb_konsumen.id_konsumen');
        $this->db->from('testimoni');
        $this->db->join('rb_konsumen', 'testimoni.id_konsumen = rb_konsumen.id_konsumen', 'left');
        $this->db->where('testimoni.id_testimoni', $id);
        $query = $this->db->get();
        return $query; // Di controller bisa ->row_array() atau ->row()
    }

    public function testimoni_update() { // Data diambil dari $this->input->post() di PDF
        $datadb = array(
            'isi_testimoni' => $this->input->post('b', TRUE), // Sesuaikan nama input
            'aktif'         => $this->input->post('f', TRUE)  // Sesuaikan nama input
        );
        $this->db->where('id_testimoni', $this->input->post('id', TRUE));
        return $this->db->update('testimoni', $datadb);
    }

    public function testimoni_delete($id) {
        $this->db->where('id_testimoni', $id);
        return $this->db->delete('testimoni');
    }

    public function public_testimoni($sampai, $dari) {
        $this->db->select('testimoni.*, rb_konsumen.nama_lengkap, rb_konsumen.foto, rb_konsumen.id_konsumen, rb_konsumen.jenis_kelamin');
        $this->db->from('testimoni');
        $this->db->join('rb_konsumen', 'testimoni.id_konsumen = rb_konsumen.id_konsumen', 'left');
        $this->db->where('testimoni.aktif', 'Y');
        $this->db->order_by('testimoni.id_testimoni', 'DESC');
        $this->db->limit($sampai, $dari);
        $query = $this->db->get();
        return $query;
    }

    public function hitung_testimoni() {
        $this->db->where('aktif', 'Y');
        $query = $this->db->get('testimoni');
        return $query; // Di controller bisa ->num_rows()
    }

    public function insert_testimoni() { // Data diambil dari $this->input->post() dan session di PDF
        $datadb = array(
            'id_konsumen'     => $this->session->id_konsumen, // Pastikan session id_konsumen ada
            'isi_testimoni'   => $this->input->post('testimoni', TRUE),
            'aktif'           => 'N', // Default tidak aktif, perlu moderasi
            'waktu_testimoni' => date('Y-m-d H:i:s')
        );
        return $this->db->insert('testimoni', $datadb);
    }


    // --- PENCARIAN RESELLER ---
    public function cari_reseller($kata) {
        // Logika pencarian reseller dari PDF
        $pisah_kata = explode(" ", $kata);
        $jml_katakan = (integer)count($pisah_kata);
        $jml_kata = $jml_katakan - 1;

        $this->db->select('rb_reseller.*, rb_kota.nama_kota');
        $this->db->from('rb_reseller');
        $this->db->join('rb_kota', 'rb_reseller.kota_id = rb_kota.kota_id', 'left');

        $this->db->group_start(); // Memulai grup kondisi OR
        for ($i = 0; $i <= $jml_kata; $i++) {
            if ($i == 0) {
                $this->db->like('rb_reseller.nama_reseller', $pisah_kata[$i]);
                $this->db->or_like('rb_kota.nama_kota', $pisah_kata[$i]);
            } else {
                $this->db->or_like('rb_reseller.nama_reseller', $pisah_kata[$i]);
                $this->db->or_like('rb_kota.nama_kota', $pisah_kata[$i]);
            }
        }
        $this->db->group_end(); // Menutup grup kondisi OR

        $this->db->order_by('rb_reseller.id_reseller', 'DESC');
        $this->db->limit(36); // Sesuai PDF
        $query = $this->db->get();
        return $query;
    }


    // --- PROFIL KONSUMEN (MEMBER) ---
    public function profile_konsumen($id_konsumen) {
        $this->db->select('a.*, b.nama_kota as kota, c.nama_provinsi as propinsi, b.provinsi_id');
        $this->db->from('rb_konsumen a');
        $this->db->join('rb_kota b', 'a.kota_id = b.kota_id', 'left');
        $this->db->join('rb_provinsi c', 'b.provinsi_id = c.provinsi_id', 'left');
        $this->db->where('a.id_konsumen', $id_konsumen);
        $query = $this->db->get();
        return $query; // Di controller ->row_array()
    }

    public function profile_update($id_konsumen) { // Data diambil dari $this->input->post() di PDF
        $data_update = array(
            'username'       => $this->db->escape_str(strip_tags($this->input->post('aa'))),
            'nama_lengkap'   => $this->db->escape_str(strip_tags($this->input->post('b'))),
            'email'          => $this->db->escape_str(strip_tags($this->input->post('c'))),
            'jenis_kelamin'  => $this->db->escape_str($this->input->post('d')),
            'tanggal_lahir'  => $this->db->escape_str($this->input->post('e')),
            'tempat_lahir'   => $this->db->escape_str(strip_tags($this->input->post('f'))),
            'alamat_lengkap' => $this->db->escape_str(strip_tags($this->input->post('g'))),
            'kecamatan'      => $this->db->escape_str(strip_tags($this->input->post('k'))), // 'k' dari PDF, mungkin seharusnya 'ia'
            'kota_id'        => $this->db->escape_str(strip_tags($this->input->post('ga'))),
            'no_telp'        => $this->db->escape_str(strip_tags($this->input->post('l'))) // 'no_hp' di PDF, 'l' dari PDF
        );

        // Update password jika diisi
        if (trim($this->input->post('a')) != '') {
            $data_update['password'] = hash("sha512", md5($this->input->post('a')));
        }

        $this->db->where('id_konsumen', $id_konsumen);
        return $this->db->update('rb_konsumen', $data_update);
    }

    public function modupdatefoto() { // Untuk konsumen, sesuai PDF
        // Logika upload gambar dari PDF (menggunakan library Upload CI)
        // Ini lebih baik dihandle di controller, model sebaiknya hanya menerima nama file
        $config['upload_path']   = './asset/foto_user/'; // Pastikan path benar dan writable
        $config['allowed_types'] = 'gif|jpg|png|JPG|JPEG|jpeg';
        $config['max_size']      = '1000'; // KB
        $config['encrypt_name']  = TRUE; // Atau false jika ingin nama asli + penanganan duplikat

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('foto_user_input_name')) { // Ganti 'foto_user_input_name' dengan name input file Anda
            $hasil = $this->upload->data();
            // Logika image_lib (crop/resize) dari PDF jika diperlukan
            // ...

            $datadb = array('foto' => $hasil['file_name']);
            $this->db->where('id_konsumen', $this->session->id_konsumen);
            return $this->db->update('rb_konsumen', $datadb);
        } else {
            // $this->upload->display_errors(); // Bisa di-log atau dikirim ke controller
            return false;
        }
    }
     public function modupdatefotoreseller() { // Untuk reseller, sesuai PDF
        $config['upload_path']   = './asset/foto_user/';
        $config['allowed_types'] = 'gif|jpg|png|JPG|JPEG|jpeg';
        $config['max_size']      = '1000'; // KB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('foto_reseller_input_name')) { // Ganti 'foto_reseller_input_name'
            $hasil = $this->upload->data();
            $datadb = array('foto' => $hasil['file_name']);
            $this->db->where('id_reseller', $this->session->id_reseller); // Pastikan session id_reseller ada
            return $this->db->update('rb_reseller', $datadb);
        } else {
            return false;
        }
    }


    // --- LAPORAN PESANAN DAN TRANSAKSI (dari sudut pandang Konsumen atau Reseller) ---
    public function orders_report($id_pembeli, $status_penjual) {
        // $id_pembeli bisa id_konsumen atau id_reseller (jika reseller beli dari admin)
        // $status_penjual bisa 'reseller' (konsumen beli dari reseller) atau 'admin' (reseller beli dari admin)
        $this->db->select('rb_penjualan.*, penjual.nama_reseller as nama_penjual_reseller, pembeli_kons.nama_lengkap as nama_pembeli_konsumen, pembeli_res.nama_reseller as nama_pembeli_reseller');
        $this->db->from('rb_penjualan');
        // Join untuk nama penjual (jika penjualnya reseller)
        $this->db->join('rb_reseller as penjual', 'rb_penjualan.id_penjual = penjual.id_reseller AND rb_penjualan.status_penjual="reseller"', 'left');
        // Join untuk nama pembeli (jika pembelinya konsumen)
        $this->db->join('rb_konsumen as pembeli_kons', 'rb_penjualan.id_pembeli = pembeli_kons.id_konsumen AND rb_penjualan.status_pembeli="konsumen"', 'left');
        // Join untuk nama pembeli (jika pembelinya reseller)
        $this->db->join('rb_reseller as pembeli_res', 'rb_penjualan.id_pembeli = pembeli_res.id_reseller AND rb_penjualan.status_pembeli="reseller"', 'left');

        $this->db->where('rb_penjualan.id_pembeli', $id_pembeli);
        $this->db->where('rb_penjualan.status_penjual', $status_penjual);
        $this->db->order_by('rb_penjualan.id_penjualan', 'DESC');
        $query = $this->db->get();
        return $query;
    }

    public function penjualan_konsumen_detail($id_penjualan) {
        // Detail transaksi yang pembelinya konsumen dan penjualnya reseller
        $this->db->select('a.*, b.nama_reseller, b.alamat_lengkap as alamat_penjual, b.no_telpon as telp_penjual, b.email as email_penjual, b.keterangan as keterangan_penjual, c.nama_kota as kota_penjual');
        $this->db->from('rb_penjualan a');
        $this->db->join('rb_reseller b', 'a.id_penjual = b.id_reseller', 'left');
        $this->db->join('rb_kota c', 'b.kota_id = c.kota_id', 'left');
        $this->db->where('a.id_penjualan', $id_penjualan);
        // Tambahan: Pastikan ini untuk transaksi yang benar (pembeli konsumen, penjual reseller)
        $this->db->where('a.status_pembeli', 'konsumen');
        $this->db->where('a.status_penjual', 'reseller');
        $query = $this->db->get();
        return $query; // Di controller ->row_array()
    }

    // --- FUNGSI PERHITUNGAN STOK (Sesuai PDF, ini untuk kalkulasi di sisi Reseller Panel) ---
    // Fungsi jual() dan beli() di PDF sepertinya untuk produk global (admin).
    // Fungsi jual_reseller() dan beli_reseller() lebih spesifik untuk transaksi antar reseller/konsumen.

    /**
     * Total produk terjual oleh reseller tertentu ke konsumen.
     * @param int $id_penjual (id_reseller)
     * @param int $id_produk
     * @return object Query result object
     */
    public function jual_reseller($id_penjual, $id_produk) {
        $this->db->select_sum('b.jumlah', 'total_jual');
        $this->db->from('rb_penjualan a');
        $this->db->join('rb_penjualan_detail b', 'a.id_penjualan = b.id_penjualan');
        $this->db->where('a.status_pembeli', 'konsumen');
        $this->db->where('a.status_penjual', 'reseller');
        $this->db->where('a.id_penjual', $id_penjual);
        $this->db->where('b.id_produk', $id_produk);
        $this->db->where('a.proses', '1'); // Hanya yang sudah diproses/terjual
        $query = $this->db->get();
        return $query; // Di controller ->row_array()['total_jual']
    }

    /**
     * Total produk dibeli oleh reseller dari admin (atau stok awal).
     * @param int $id_pembeli (id_reseller)
     * @param int $id_produk
     * @return object Query result object
     */
    public function beli_reseller($id_pembeli, $id_produk) {
        $this->db->select_sum('b.jumlah', 'total_beli');
        $this->db->from('rb_penjualan a');
        $this->db->join('rb_penjualan_detail b', 'a.id_penjualan = b.id_penjualan');
        $this->db->where('a.status_pembeli', 'reseller'); // Reseller sebagai pembeli
        $this->db->where('a.status_penjual', 'admin');    // Admin sebagai penjual
        $this->db->where('a.id_pembeli', $id_pembeli);
        $this->db->where('b.id_produk', $id_produk);
        $this->db->where('a.proses', '1'); // Hanya yang sudah diproses/diterima
        $query = $this->db->get();
        return $query; // Di controller ->row_array()['total_beli']
    }


    // --- AGENDA (Ada di Model_reseller PDF, mungkin lebih cocok di Model_utama atau Model_agenda) ---
    public function agenda_terbaru($limit) {
        $this->db->order_by('id_agenda', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('tb_agenda');
        return $query;
    }


    // --- Method lain dari PDF yang bisa ditambahkan atau disesuaikan ---
    // penjualan_list_konsumen($id_penjual, $level)
    // reseller_pembelian($id_reseller_pembeli, $level_penjual)
    // penjualan_detail($id_penjualan) // Jika ini untuk admin melihat detail penjualan ke reseller
    // penjualan_list($id_penjual, $level)
    // pembelian($id_reseller) // Total pembelian reseller dari admin
    // penjualan_perusahaan($id_reseller) // Total penjualan produk perusahaan oleh reseller
    // penjualan($id_reseller) // Total penjualan produk pribadi reseller
    // modal_perusahaan($id_reseller)
    // modal_pribadi($id_reseller)
    // produk_perkategori($id_reseller, $id_produk_perusahaan, $id_kategori_produk, $limit)
    // view_join_rows(...) dan view_join_where_one(...) adalah method generik,
    // mungkin lebih cocok di Model_app atau Model_utama jika belum ada yang serupa.

}