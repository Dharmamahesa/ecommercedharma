<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_produk_model extends CI_Model {

    protected $table_kategori = 'rb_kategori_produk';
    protected $table_sub_kategori = 'rb_kategori_produk_sub';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // == KATEGORI UTAMA PRODUK ==

    /**
     * Mengambil semua kategori utama produk.
     * @param string $order_by Kolom untuk pengurutan
     * @param string $order_direction Arah pengurutan (ASC/DESC)
     * @return array of objects
     */
    public function get_all_kategori($order_by = 'id_kategori_produk', $order_direction = 'DESC') {
        $this->db->order_by($order_by, $order_direction);
        $query = $this->db->get($this->table_kategori);
        return $query->result(); // Mengembalikan array of objects
    }

    /**
     * Mengambil satu kategori utama produk berdasarkan ID.
     * @param int $id_kategori_produk
     * @return object|null
     */
    public function get_kategori_by_id($id_kategori_produk) {
        $query = $this->db->get_where($this->table_kategori, array('id_kategori_produk' => $id_kategori_produk));
        return $query->row(); // Mengembalikan satu baris sebagai objek
    }

    /**
     * Menyimpan data kategori utama produk baru.
     * @param array $data
     * @return int|bool ID kategori yang baru dimasukkan atau false jika gagal
     */
    public function insert_kategori($data) {
        // Pastikan ada kolom 'kategori_seo' jika belum dibuat di controller
        if (!isset($data['kategori_seo']) && isset($data['nama_kategori'])) {
            $this->load->helper('url'); // Untuk url_title()
            $data['kategori_seo'] = url_title($data['nama_kategori'], 'dash', TRUE);
        }
        $this->db->insert($this->table_kategori, $data);
        return $this->db->insert_id();
    }

    /**
     * Memperbarui data kategori utama produk berdasarkan ID.
     * @param int $id_kategori_produk
     * @param array $data
     * @return bool True jika berhasil, false jika gagal
     */
    public function update_kategori($id_kategori_produk, $data) {
        if (!isset($data['kategori_seo']) && isset($data['nama_kategori'])) {
            $this->load->helper('url');
            $data['kategori_seo'] = url_title($data['nama_kategori'], 'dash', TRUE);
        }
        $this->db->where('id_kategori_produk', $id_kategori_produk);
        return $this->db->update($this->table_kategori, $data);
    }

    /**
     * Menghapus kategori utama produk berdasarkan ID.
     * @param int $id_kategori_produk
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete_kategori($id_kategori_produk) {
        // Tambahan: Pertimbangkan untuk menghapus atau menangani sub-kategori terkait
        // $this->delete_sub_kategori_by_main_id($id_kategori_produk);
        $this->db->where('id_kategori_produk', $id_kategori_produk);
        return $this->db->delete($this->table_kategori);
    }


    // == SUB KATEGORI PRODUK ==

    /**
     * Mengambil semua sub kategori produk, biasanya di-join dengan kategori utama.
     * @param string $order_by Kolom untuk pengurutan
     * @param string $order_direction Arah pengurutan (ASC/DESC)
     * @return array of objects
     */
    public function get_all_sub_kategori_with_main_kategori($order_by = 'a.id_kategori_produk_sub', $order_direction = 'DESC') {
        $this->db->select("a.*, b.nama_kategori as nama_kategori_utama");
        $this->db->from("{$this->table_sub_kategori} a");
        $this->db->join("{$this->table_kategori} b", "a.id_kategori_produk = b.id_kategori_produk", "left");
        $this->db->order_by($order_by, $order_direction);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Mengambil sub kategori produk berdasarkan ID kategori utama.
     * @param int $id_kategori_produk
     * @return array of objects
     */
    public function get_sub_kategori_by_kategori_id($id_kategori_produk) {
        $this->db->where('id_kategori_produk', $id_kategori_produk);
        $this->db->order_by('nama_kategori_sub', 'ASC');
        $query = $this->db->get($this->table_sub_kategori);
        return $query->result(); // Untuk dropdown di view edit produk
    }
    
    /**
     * Mengambil sub kategori produk berdasarkan ID kategori utama untuk JSON (AJAX).
     * @param int $id_kategori_produk
     * @return array of arrays
     */
    public function get_sub_kategori_by_kategori_id_for_json($id_kategori_produk) {
        $this->db->select('id_kategori_produk_sub, nama_kategori_sub'); // Hanya kolom yang dibutuhkan AJAX
        $this->db->where('id_kategori_produk', $id_kategori_produk);
        $this->db->order_by('nama_kategori_sub', 'ASC');
        $query = $this->db->get($this->table_sub_kategori);
        return $query->result_array(); // Mengembalikan array of arrays untuk JSON
    }


    /**
     * Mengambil satu sub kategori produk berdasarkan ID-nya.
     * @param int $id_kategori_produk_sub
     * @return object|null
     */
    public function get_sub_kategori_by_id($id_kategori_produk_sub) {
        $query = $this->db->get_where($this->table_sub_kategori, array('id_kategori_produk_sub' => $id_kategori_produk_sub));
        return $query->row();
    }

    /**
     * Menyimpan data sub kategori produk baru.
     * @param array $data
     * @return int|bool ID sub kategori yang baru dimasukkan atau false jika gagal
     */
    public function insert_sub_kategori($data) {
        // Pastikan ada kolom 'kategori_seo_sub' jika belum dibuat di controller
        if (!isset($data['kategori_seo_sub']) && isset($data['nama_kategori_sub'])) {
            $this->load->helper('url');
            $data['kategori_seo_sub'] = url_title($data['nama_kategori_sub'], 'dash', TRUE);
        }
        $this->db->insert($this->table_sub_kategori, $data);
        return $this->db->insert_id();
    }

    /**
     * Memperbarui data sub kategori produk berdasarkan ID.
     * @param int $id_kategori_produk_sub
     * @param array $data
     * @return bool True jika berhasil, false jika gagal
     */
    public function update_sub_kategori($id_kategori_produk_sub, $data) {
        if (!isset($data['kategori_seo_sub']) && isset($data['nama_kategori_sub'])) {
            $this->load->helper('url');
            $data['kategori_seo_sub'] = url_title($data['nama_kategori_sub'], 'dash', TRUE);
        }
        $this->db->where('id_kategori_produk_sub', $id_kategori_produk_sub);
        return $this->db->update($this->table_sub_kategori, $data);
    }

    /**
     * Menghapus sub kategori produk berdasarkan ID.
     * @param int $id_kategori_produk_sub
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete_sub_kategori($id_kategori_produk_sub) {
        $this->db->where('id_kategori_produk_sub', $id_kategori_produk_sub);
        return $this->db->delete($this->table_sub_kategori);
    }
    
    /**
     * Menghapus semua sub kategori berdasarkan ID kategori utama.
     * (Opsional, bisa dipanggil saat kategori utama dihapus)
     * @param int $id_kategori_produk
     * @return bool
     */
    public function delete_sub_kategori_by_main_id($id_kategori_produk) {
        $this->db->where('id_kategori_produk', $id_kategori_produk);
        return $this->db->delete($this->table_sub_kategori);
    }

}