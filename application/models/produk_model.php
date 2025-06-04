<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {

    protected $table = 'rb_produk'; // Tabel utama untuk produk
    protected $table_produk_diskon = 'rb_produk_diskon';
    protected $table_reseller = 'rb_reseller';
    protected $table_kategori = 'rb_kategori_produk'; // Asumsi ini tabel kategori utama

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Memastikan database library sudah termuat
    }

    /**
     * Mengambil semua produk, bisa dengan join ke tabel reseller atau kategori jika perlu.
     * @return array of objects
     */
    public function get_all() {
        $this->db->select("{$this->table}.*, {$this->table_reseller}.nama_reseller, {$this->table_kategori}.nama_kategori");
        $this->db->from($this->table);
        $this->db->join($this->table_reseller, "{$this->table}.id_reseller = {$this->table_reseller}.id_reseller", 'left');
        $this->db->join($this->table_kategori, "{$this->table}.id_kategori_produk = {$this->table_kategori}.id_kategori_produk", 'left');
        // Tambahkan filter jika perlu, misalnya hanya produk aktif atau yang stoknya ada
        // $this->db->where("{$this->table}.stok >", 0);
        $this->db->order_by("{$this->table}.id_produk", "DESC");
        $query = $this->db->get();
        return $query->result(); // Mengembalikan array of objects
    }

    /**
     * Mengambil satu produk berdasarkan ID.
     * @param int $id ID Produk
     * @return object|null
     */
    public function get_by_id($id) {
        $this->db->select("{$this->table}.*, {$this->table_reseller}.nama_reseller, {$this->table_kategori}.nama_kategori");
        $this->db->from($this->table);
        $this->db->join($this->table_reseller, "{$this->table}.id_reseller = {$this->table_reseller}.id_reseller", 'left');
        $this->db->join($this->table_kategori, "{$this->table}.id_kategori_produk = {$this->table_kategori}.id_kategori_produk", 'left');
        $this->db->where("{$this->table}.id_produk", $id);
        $query = $this->db->get();
        return $query->row(); // Mengembalikan satu baris sebagai objek
    }

    /**
     * Mengambil satu produk berdasarkan URL SEO.
     * @param string $seo_url URL SEO Produk
     * @return object|null
     */
    public function get_by_seo($seo_url) {
        $this->db->select("{$this->table}.*, {$this->table_reseller}.nama_reseller, {$this->table_kategori}.nama_kategori");
        $this->db->from($this->table);
        $this->db->join($this->table_reseller, "{$this->table}.id_reseller = {$this->table_reseller}.id_reseller", 'left');
        $this->db->join($this->table_kategori, "{$this->table}.id_kategori_produk = {$this->table_kategori}.id_kategori_produk", 'left');
        $this->db->where("{$this->table}.produk_seo", $seo_url);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Menyimpan data produk baru.
     * @param array $data Data produk
     * @return int|bool ID produk yang baru dimasukkan atau false jika gagal
     */
    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id(); // Mengembalikan ID dari baris yang baru dimasukkan
    }

    /**
     * Memperbarui data produk berdasarkan ID.
     * @param int $id ID Produk
     * @param array $data Data produk yang akan diperbarui
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($id, $data) {
        $this->db->where('id_produk', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Menghapus produk berdasarkan ID.
     * @param int $id ID Produk
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($id) {
        $this->db->where('id_produk', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Mengambil data diskon untuk produk tertentu oleh reseller tertentu.
     * @param int $id_produk
     * @param int $id_reseller
     * @return object|null
     */
    public function get_diskon_produk($id_produk, $id_reseller) {
        $this->db->where('id_produk', $id_produk);
        $this->db->where('id_reseller', $id_reseller);
        $query = $this->db->get($this->table_produk_diskon);
        return $query->row();
    }

    /**
     * Memperbarui atau menambah data diskon.
     * (Ini contoh, Anda mungkin perlu logika yang lebih kompleks jika diskon tidak selalu ada)
     * @param int $id_produk
     * @param int $id_reseller
     * @param array $data (misalnya ['diskon' => nilai_diskon])
     * @return bool
     */
    public function update_diskon_produk($id_produk, $id_reseller, $data) {
        $this->db->where('id_produk', $id_produk);
        $this->db->where('id_reseller', $id_reseller);
        $query = $this->db->get($this->table_produk_diskon);

        if ($query->num_rows() > 0) {
            // Jika data diskon sudah ada, update
            $this->db->where('id_produk', $id_produk);
            $this->db->where('id_reseller', $id_reseller);
            return $this->db->update($this->table_produk_diskon, $data);
        } else {
            // Jika belum ada, insert data diskon baru
            $data_insert = array_merge(['id_produk' => $id_produk, 'id_reseller' => $id_reseller], $data);
            return $this->db->insert($this->table_produk_diskon, $data_insert);
        }
    }

    // Anda bisa menambahkan method lain di sini sesuai kebutuhan,
    // misalnya untuk mengambil produk berdasarkan kategori, pencarian, dll.
}