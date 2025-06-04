<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_app extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Memastikan database library sudah termuat
    }

    /**
     * Mengambil semua data dari sebuah tabel.
     * @param string $table Nama tabel
     * @return object Query result object
     */
    public function view($table) {
        return $this->db->get($table);
    }

    /**
     * Menyimpan data baru ke sebuah tabel.
     * @param string $table Nama tabel
     * @param array $data Data yang akan disimpan
     * @return bool True jika berhasil, false jika gagal
     */
    public function insert($table, $data) {
        return $this->db->insert($table, $data);
    }

    /**
     * Mengambil satu baris data dari tabel berdasarkan kondisi.
     * Biasanya digunakan untuk form edit.
     * @param string $table Nama tabel
     * @param array $data Kondisi (where clause)
     * @return object Query result object
     */
    public function edit($table, $data) {
        return $this->db->get_where($table, $data);
    }

    /**
     * Memperbarui data di tabel berdasarkan kondisi.
     * @param string $table Nama tabel
     * @param array $data Data yang akan diperbarui
     * @param array $where Kondisi (where clause)
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($table, $data, $where) {
        return $this->db->update($table, $data, $where);
    }

    /**
     * Menghapus data dari tabel berdasarkan kondisi.
     * @param string $table Nama tabel
     * @param array $where Kondisi (where clause)
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($table, $where) {
        return $this->db->delete($table, $where);
    }

    /**
     * Mengambil data dari tabel dengan kondisi tertentu.
     * @param string $table Nama tabel
     * @param array $data Kondisi (where clause)
     * @return object Query result object
     */
    public function view_where($table, $data) {
        $this->db->where($data);
        return $this->db->get($table);
    }

    /**
     * Mengambil data dari tabel dengan pengurutan dan batasan (limit & offset).
     * @param string $table Nama tabel
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan (ASC/DESC)
     * @param int $baris Jumlah baris (limit)
     * @param int $dari Mulai dari baris ke berapa (offset)
     * @return object Query result object
     */
    public function view_ordering_limit($table, $order, $ordering, $baris, $dari) {
        $this->db->select('*');
        $this->db->from($table); // Ditambahkan from() untuk kejelasan
        $this->db->order_by($order, $ordering);
        $this->db->limit($baris, $dari);
        return $this->db->get();
    }

    /**
     * Mengambil data dari tabel dengan kondisi, pengurutan, dan batasan.
     * @param string $table Nama tabel
     * @param array $data Kondisi (where clause)
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan (ASC/DESC)
     * @param int $baris Jumlah baris (limit)
     * @param int $dari Mulai dari baris ke berapa (offset)
     * @return object Query result object
     */
    public function view_where_ordering_limit($table, $data, $order, $ordering, $baris, $dari) {
        $this->db->select('*');
        $this->db->from($table); // Ditambahkan from()
        $this->db->where($data);
        $this->db->order_by($order, $ordering);
        $this->db->limit($baris, $dari);
        return $this->db->get();
    }

    /**
     * Mengambil data dari tabel dengan pengurutan.
     * Mengembalikan array of arrays.
     * @param string $table Nama tabel
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan (ASC/DESC)
     * @return array
     */
    public function view_ordering($table, $order, $ordering) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->order_by($order, $ordering);
        return $this->db->get(); // Di controller perlu ->result_array() atau ->result()
    }

    /**
     * Mengambil data dari tabel dengan kondisi dan pengurutan.
     * Mengembalikan array of arrays.
     * @param string $table Nama tabel
     * @param array $data Kondisi (where clause)
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan (ASC/DESC)
     * @return array
     */
    public function view_where_ordering($table, $data, $order, $ordering) {
        $this->db->from($table); // Ditambahkan from()
        $this->db->where($data);
        $this->db->order_by($order, $ordering);
        $query = $this->db->get();
        return $query; // Di controller perlu ->result_array() atau ->result()
    }

    /**
     * Mengambil data dengan join satu tabel.
     * Mengembalikan array of arrays.
     * @param string $table1 Nama tabel pertama (kiri)
     * @param string $table2 Nama tabel kedua (kanan)
     * @param string $field Kolom untuk join
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan
     * @return array
     */
    public function view_join_one($table1, $table2, $field, $order, $ordering) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, $table1 . '.' . $field . '=' . $table2 . '.' . $field); // Join standar
        $this->db->order_by($order, $ordering);
        return $this->db->get(); // Di controller perlu ->result_array() atau ->result()
    }

    /**
     * Mengambil data dengan join satu tabel dan kondisi.
     * Mengembalikan array of arrays.
     * @param string $table1 Nama tabel pertama
     * @param string $table2 Nama tabel kedua
     * @param string $field Kolom untuk join
     * @param array $where Kondisi (where clause)
     * @param string $order Kolom untuk pengurutan
     * @param string $ordering Arah pengurutan
     * @return array
     */
    public function view_join_where($table1, $table2, $field, $where, $order, $ordering) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, $table1 . '.' . $field . '=' . $table2 . '.' . $field); // Join standar
        $this->db->where($where);
        $this->db->order_by($order, $ordering);
        return $this->db->get(); // Di controller perlu ->result_array() atau ->result()
    }

    /**
     * Cek akses menu/modul untuk pengguna.
     * @param string $link Link modul
     * @param string $id_session ID session pengguna
     * @return int Jumlah baris (0 atau 1)
     */
    // application/models/Model_app.php
public function umenu_akses($link, $id_session) {
    $this->db->select('COUNT(*) as numrows'); // Lebih efisien menggunakan COUNT(*)
    $this->db->from('modul m');
    $this->db->join('users_modul um', 'm.id_modul = um.id_modul');
    $this->db->where('um.id_session', $id_session);
    $this->db->where('m.link', $link);
    $this->db->where('m.aktif', 'Y'); // Tambahkan pengecekan apakah modulnya aktif
    $this->db->where('m.publish', 'Y'); // Dan apakah modulnya di-publish

    $query = $this->db->get();
    $result = $query->row();
    return ($result && $result->numrows > 0) ? (int)$result->numrows : 0;
}

    /**
     * Cek login pengguna.
     * @param string $username
     * @param string $password (sudah di-hash dari controller)
     * @param string $table Nama tabel pengguna (misalnya 'rb_reseller')
     * @return object Query result object
     */
    public function cek_login($username, $password, $table) {
        // Menyesuaikan dengan SQL Anda, kolom 'blokir' tidak ada di 'rb_reseller'
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        // Jika Anda menambahkan kolom 'blokir' di tabel $table, uncomment baris berikut:
        // $this->db->where('blokir', 'N');
        return $this->db->get($table);
    }

    /**
     * Mengambil data untuk grafik kunjungan.
     * @return object Query result object
     */
    public function grafik_kunjungan() {
        // Tabel statistik tidak ada di SQL Anda.
        // Jika Anda membuat tabel statistik, query ini bisa digunakan.
        // Untuk sementara, kita akan mengembalikan query kosong atau data dummy jika tabel tidak ada.
        if ($this->db->table_exists('statistik')) {
            $this->db->select('count(*) as jumlah, tanggal');
            $this->db->from('statistik');
            $this->db->group_by('tanggal');
            $this->db->order_by('tanggal', 'DESC');
            $this->db->limit(10);
            return $this->db->get();
        } else {
            return $this->db->query('SELECT 0 as jumlah, CURDATE() as tanggal'); // Data dummy
        }
    }

    /**
     * Mengambil kategori populer (berita) berdasarkan jumlah dibaca.
     * @param int $limit Batas jumlah kategori yang diambil
     * @return object Query result object
     */
    public function kategori_populer($limit) {
        // Query ini kompleks dan bergantung pada struktur tabel 'tb_kategori' dan 'tb_berita'
        // dan bagaimana 'dibaca' dan 'jum_dibaca' dihitung.
        // Berdasarkan SQL Anda, tabel 'tb_kategori' dan 'tb_berita' ada.
        // PDF memiliki query yang cukup spesifik di sini.
        // Untuk penyederhanaan, Anda bisa membuat query yang lebih langsung jika strukturnya memungkinkan.

        // Query dari PDF (dengan sedikit penyesuaian untuk kejelasan jika memungkinkan)
        // Pastikan tabel dan kolom sesuai dengan skema Anda.
        $sql = "SELECT c.*, b.jum_dibaca FROM 
                (SELECT a.* FROM tb_kategori a WHERE a.aktif='Y') as c 
                LEFT JOIN 
                (SELECT id_kategori, sum(dibaca) as jum_dibaca FROM tb_berita GROUP BY id_kategori) as b 
                ON c.id_kategori=b.id_kategori 
                ORDER BY b.jum_dibaca DESC LIMIT " . (int)$limit;
        return $this->db->query($sql);
    }
}