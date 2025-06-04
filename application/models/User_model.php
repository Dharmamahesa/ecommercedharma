<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    // Tabel utama untuk pengguna (sesuaikan jika Anda membuat tabel users terpisah)
    // Untuk contoh ini, kita bisa membuat fungsi yang lebih generik
    // atau fokus pada salah satu tabel yang ada (misal rb_konsumen atau rb_reseller)
    // Jika ini untuk admin panel users (yang login dari rb_reseller):
    protected $table_admin_users = 'rb_reseller';
    // Jika ini untuk konsumen/member:
    protected $table_konsumen = 'rb_konsumen';


    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Mengambil semua pengguna admin (dari tabel rb_reseller).
     * @return object Query result object
     */
    public function get_all_admin_users() {
        $this->db->order_by('username', 'ASC');
        $query = $this->db->get($this->table_admin_users);
        return $query; // Di controller ->result_array() atau ->result()
    }

    /**
     * Mengambil satu pengguna admin berdasarkan ID (id_reseller).
     * @param int $id_user (merujuk ke id_reseller)
     * @return object Query result object
     */
    public function get_admin_user_by_id($id_user) {
        $this->db->where('id_reseller', $id_user);
        $query = $this->db->get($this->table_admin_users);
        return $query; // Di controller ->row_array() atau ->row()
    }

    /**
     * Mengambil satu pengguna admin berdasarkan username.
     * @param string $username
     * @return object Query result object
     */
    public function get_admin_user_by_username($username) {
        $this->db->where('username', $username);
        $query = $this->db->get($this->table_admin_users);
        return $query; // Di controller ->row_array() atau ->row()
    }

    /**
     * Menambah pengguna admin baru ke tabel rb_reseller.
     * Pastikan password sudah di-hash sebelum dikirim ke method ini.
     * @param array $data Data pengguna
     * @return int|bool ID pengguna baru atau false jika gagal
     */
    public function insert_admin_user($data) {
        // Anda mungkin ingin menambahkan kolom 'level' atau status lain di sini
        // jika tabel rb_reseller digunakan untuk lebih dari sekadar admin.
        // Contoh: $data['level'] = 'admin';
        // Contoh: $data['tanggal_daftar'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_admin_users, $data);
        return $this->db->insert_id();
    }

    /**
     * Memperbarui data pengguna admin berdasarkan ID (id_reseller).
     * @param int $id_user (merujuk ke id_reseller)
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update_admin_user($id_user, $data) {
        $this->db->where('id_reseller', $id_user);
        return $this->db->update($this->table_admin_users, $data);
    }

    /**
     * Menghapus pengguna admin berdasarkan ID (id_reseller).
     * @param int $id_user (merujuk ke id_reseller)
     * @return bool
     */
    public function delete_admin_user($id_user) {
        // PERHATIAN: Pertimbangkan konsekuensi menghapus admin.
        // Mungkin lebih baik menonaktifkan daripada menghapus.
        $this->db->where('id_reseller', $id_user);
        return $this->db->delete($this->table_admin_users);
    }

    /**
     * Validasi login untuk pengguna admin (menggunakan tabel rb_reseller).
     * Password yang diterima di sini sudah di-hash dari controller.
     * @param string $username
     * @param string $password_hashed
     * @return object Query result object (untuk diambil row_array() dan num_rows() di controller)
     */
    public function validate_admin_login($username, $password_hashed) {
        $this->db->where('username', $username);
        $this->db->where('password', $password_hashed);
        // Jika ada kolom 'blokir' atau 'aktif' di tabel rb_reseller, tambahkan kondisinya
        // $this->db->where('status_akun', 'aktif'); // contoh
        $query = $this->db->get($this->table_admin_users);
        return $query;
    }


    // --- Contoh Fungsi untuk Pengguna Konsumen (Tabel rb_konsumen) ---

    /**
     * Mengambil semua pengguna konsumen.
     * @return object Query result object
     */
    public function get_all_konsumen() {
        $this->db->order_by('nama_lengkap', 'ASC');
        $query = $this->db->get($this->table_konsumen);
        return $query;
    }

    /**
     * Mengambil satu pengguna konsumen berdasarkan ID.
     * @param int $id_konsumen
     * @return object Query result object
     */
    public function get_konsumen_by_id($id_konsumen) {
        $this->db->where('id_konsumen', $id_konsumen);
        $query = $this->db->get($this->table_konsumen);
        return $query;
    }

    // Anda bisa menambahkan fungsi insert, update, delete untuk konsumen jika diperlukan.
    // Model_reseller.php dari PDF sudah memiliki fungsi untuk update profil konsumen.
}