<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Model_reseller $Model_reseller // Model utama untuk data reseller dan konsumen
 * @property Model_app $Model_app       // Model umum untuk operasi database
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Form_validation $form_validation
 * @property CI_Email $email           // Untuk mengirim email
 * // Tambahkan model lain jika dibutuhkan, misalnya Produk_model
 */
class Members extends CI_Controller {

    protected $default_data;

    public function __construct() {
        parent::__construct();
        // Pastikan helper dan library dasar sudah di-load (bisa juga di autoload)
        $this->load->helper(array('url', 'form', 'phpmu', 'download', 'html', 'engine', 'captcha', 'cookie', 'string'));
        $this->load->library(array('session', 'form_validation', 'pagination', 'template', 'user_agent', 'email'));

        // Load model yang sering digunakan
        $this->load->model('Model_reseller');
        $this->load->model('Model_app');
        // $this->load->model('Produk_model'); // Jika dibutuhkan untuk detail produk di keranjang, dll.

        // Cek apakah member sudah login, kecuali untuk halaman tertentu seperti logout
        if (!in_array($this->uri->segment(2), array('logout'))) {
            cek_session_members(); // Asumsi fungsi ini ada di helper untuk mengecek session member
        }

        // Data default untuk view (bisa diambil dari database identitas)
        $identitas = $this->Model_app->edit('tb_identitas', ['id_identitas' => 1])->row_array();
        $this->default_data = array(
            'website_name'    => isset($identitas['nama_website']) ? $identitas['nama_website'] : 'Toko Online Saya',
            'site_logo'       => isset($identitas['logo']) ? $identitas['logo'] : 'logo.png', // Sesuaikan kolom logo jika ada di tb_identitas atau tabel logo
            'controller_name' => 'members', // Nama controller saat ini
            'iden'            => $identitas   // Menyimpan semua data identitas
        );
    }

    /**
     * Halaman dashboard atau profil utama member.
     */
    public function index() {
        $this->profile(); // Alihkan ke halaman profil
    }

    /**
     * Menampilkan halaman profil konsumen/member.
     */
    public function profile() {
        $data = $this->default_data;
        $data['title'] = 'Profil Anda';
        // Mengambil data profil konsumen dari Model_reseller [cite: 853]
        $data['row'] = $this->Model_reseller->profile_konsumen($this->session->id_konsumen)->row_array();
        // Path view sesuai dengan struktur di PDF [cite: 853]
        $this->template->load(template().'/template', template().'/reseller/view_profile', $data);
    }

    /**
     * Menampilkan form dan memproses edit profil konsumen/member.
     */
    public function edit_profile() {
        $data = $this->default_data;
        $data['title'] = 'Edit Profil Anda';

        // Aturan validasi form
        $this->form_validation->set_rules('aa', 'Username', 'required|trim'); // Username lama, biasanya tidak diedit
        $this->form_validation->set_rules('b', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('c', 'Email', 'required|trim|valid_email');
        // Tambahkan validasi lain sesuai kebutuhan (jenis kelamin, tgl lahir, alamat, dll.)
        // Untuk password, hanya validasi jika diisi
        if ($this->input->post('a')) {
            $this->form_validation->set_rules('a', 'Password Baru', 'trim|min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            // Mengambil data profil untuk ditampilkan di form [cite: 855]
            $data['row'] = $this->Model_reseller->profile_konsumen($this->session->id_konsumen)->row_array();
            // Mengambil data provinsi untuk dropdown [cite: 855]
            $data['provinsi'] = $this->Model_app->view_ordering('rb_provinsi', 'provinsi_id', 'ASC')->result_array();
            // Mengambil provinsi_id dari kota konsumen untuk dropdown kota [cite: 855]
            $data['rowse'] = $this->db->query("SELECT provinsi_id FROM rb_kota where kota_id='" . (isset($data['row']['kota_id']) ? $data['row']['kota_id'] : '') . "'")->row_array();

            $this->template->load(template().'/template', template().'/reseller/view_profile_edit', $data);
        } else {
            // Proses update profil menggunakan method dari Model_reseller [cite: 854]
            $this->Model_reseller->profile_update($this->session->id_konsumen);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Profil berhasil diperbarui!</div>');
            redirect('members/profile');
        }
    }

    /**
     * Mengupdate foto profil member.
     */
    public function foto() {
        // Proses update foto menggunakan method dari Model_reseller [cite: 852]
        if ($this->input->post('submit')) {
            $this->Model_reseller->modupdatefoto(); // Method ini menangani upload dan update DB
            $this->session->set_flashdata('message', '<div class="alert alert-success">Foto profil berhasil diperbarui!</div>');
        }
        redirect('members/profile');
    }


    /**
     * Menampilkan keranjang belanja member.
     * Logika ini disesuaikan dari controller Produk dan Members di PDF.
     * PDF memiliki logika keranjang di controller Produk dan controller Members.
     * Untuk konsistensi, diletakkan di Members.
     */
    public function keranjang($id_reseller_produk = NULL, $id_produk = NULL, $dari_produk_detail = NULL) {
        $data = $this->default_data;
        $data['title'] = 'Keranjang Belanja Anda';

        // Logika penambahan produk ke keranjang, diadaptasi dari PDF [cite: 868, 871]
        if ($id_produk !== NULL && $id_reseller_produk !== NULL) {
            // Cek stok produk dari reseller tersebut
            $jual = $this->Model_reseller->jual_reseller($id_reseller_produk, $id_produk)->row_array();
            $beli = $this->Model_reseller->beli_reseller($id_reseller_produk, $id_produk)->row_array(); // Ini mungkin representasi stok awal atau pembelian reseller
            $stok_produk_reseller = (isset($beli['beli']) ? $beli['beli'] : 0) - (isset($jual['jual']) ? $jual['jual'] : 0);

            $qty_diminta = (int) ($this->input->post('qty') ? $this->input->post('qty') : 1);

            if ($stok_produk_reseller < $qty_diminta) {
                $produk_info = $this->Model_app->edit('rb_produk', array('id_produk' => $id_produk))->row_array();
                $nama_produk_cek = isset($produk_info['nama_produk']) ? filter($produk_info['nama_produk']) : 'Produk';
                $this->session->set_flashdata('message', "<div class='alert alert-danger'>Maaf, Stok untuk Produk $nama_produk_cek pada Penjual ini tidak mencukupi! (Sisa: $stok_produk_reseller)</div>");
                if($dari_produk_detail){
                     redirect('produk/detail/' . (isset($produk_info['produk_seo']) ? $produk_info['produk_seo'] : $id_produk) );
                } else {
                     redirect('members/produk_reseller/' . $id_reseller_produk); // Arahkan ke halaman produk reseller tsb
                }
                return;
            }

            // Logika session keranjang atau tabel temporary rb_penjualan_temp dari PDF
            // PDF menggunakan id_penjualan yang disimpan di session ('idp')
            if ($this->session->idp == '') { // Jika belum ada transaksi aktif
                $kode_transaksi = 'TRX-' . date('YmdHis'); // Sesuai format di PDF [cite: 871]
                // Data untuk tabel rb_penjualan
                $penjualan_data = array(
                    'kode_transaksi' => $kode_transaksi,
                    'id_pembeli' => $this->session->id_konsumen,
                    'id_penjual' => $id_reseller_produk, // ID Reseller dari produk yang dibeli
                    'status_pembeli' => 'konsumen',
                    'status_penjual' => 'reseller',
                    'waktu_transaksi' => date('Y-m-d H:i:s'),
                    'proses' => '0' // 0: Keranjang/Pending, 1: Diproses, 2: Dikonfirmasi bayar, dst.
                );
                $this->Model_app->insert('rb_penjualan', $penjualan_data);
                $id_penjualan_baru = $this->db->insert_id();
                $this->session->set_userdata(array('idp' => $id_penjualan_baru));
            }

            // Cek apakah transaksi saat ini dari reseller yang sama [cite: 873]
            $transaksi_aktif = $this->Model_app->edit('rb_penjualan', array('id_penjualan' => $this->session->idp))->row_array();
            if ($transaksi_aktif && $transaksi_aktif['id_penjual'] != $id_reseller_produk) {
                $data['error_reseller'] = "<div class='alert alert-danger'>Maaf, Dalam 1 Transaksi hanya boleh order dari 1 Penjual saja. Silakan selesaikan atau batalkan transaksi sebelumnya.</div>";
            } else {
                 // Data untuk tabel rb_penjualan_detail
                $produk_harga_info = $this->Model_app->edit('rb_produk', array('id_produk' => $id_produk))->row_array();
                $diskon_info = $this->Model_app->edit('rb_produk_diskon', array('id_produk' => $id_produk, 'id_reseller' => $id_reseller_produk))->row_array();
                $harga_jual_final = isset($produk_harga_info['harga_konsumen']) ? $produk_harga_info['harga_konsumen'] : 0;
                $diskon_nominal = isset($diskon_info['diskon']) ? $diskon_info['diskon'] : 0;
                // $harga_jual_final -= $diskon_nominal; // Harga setelah diskon

                $detail_data = array(
                    'id_penjualan' => $this->session->idp,
                    'id_produk' => $id_produk,
                    'jumlah' => $qty_diminta,
                    'harga_jual' => $harga_jual_final, // Harga satuan sebelum diskon produk
                    'diskon' => $diskon_nominal, // Diskon per item jika ada
                    'satuan' => isset($produk_harga_info['satuan']) ? $produk_harga_info['satuan'] : ''
                );

                // Cek apakah produk sudah ada di keranjang untuk transaksi ini
                $item_keranjang = $this->Model_app->view_where('rb_penjualan_detail', array('id_penjualan' => $this->session->idp, 'id_produk' => $id_produk))->row_array();
                if ($item_keranjang) {
                    // Update jumlah jika produk sudah ada
                    $this->db->query("UPDATE rb_penjualan_detail SET jumlah = jumlah + " . (int)$qty_diminta . " WHERE id_penjualan_detail = '" . $item_keranjang['id_penjualan_detail'] . "'");
                } else {
                    $this->Model_app->insert('rb_penjualan_detail', $detail_data);
                }
                if($dari_produk_detail){ // Jika datang dari halaman detail produk
                    redirect('members/keranjang');
                }
            }
        }

        // Menampilkan isi keranjang
        if ($this->session->idp != '') {
            // Mengambil data transaksi (penjualan) yang aktif [cite: 875]
            $data['rows'] = $this->Model_reseller->penjualan_konsumen_detail($this->session->idp)->row_array();
            // Mengambil data alamat konsumen untuk pengiriman [cite: 877]
            $data['rowsk'] = $this->Model_reseller->view_join_where_one('rb_konsumen', 'rb_kota', 'kota_id', array('id_konsumen' => $this->session->id_konsumen))->row_array();
            // Mengambil item-item produk dalam keranjang [cite: 875]
            $data['record'] = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $this->session->idp), 'id_penjualan_detail', 'ASC')->result_array();
        }

        $this->template->load(template().'/template', template().'/reseller/members/view_keranjang', $data);
    }


    /**
     * Menghapus item dari keranjang belanja.
     */
    public function keranjang_delete($id_penjualan_detail) {
        // Hapus item dari tabel rb_penjualan_detail [cite: 879]
        $this->Model_app->delete('rb_penjualan_detail', array('id_penjualan_detail' => $id_penjualan_detail, 'id_penjualan' => $this->session->idp)); // Tambahkan cek id_penjualan untuk keamanan

        // Cek apakah keranjang menjadi kosong [cite: 880]
        $isi_keranjang = $this->db->query("SELECT SUM(jumlah) as jumlah FROM rb_penjualan_detail WHERE id_penjualan='" . $this->session->idp . "'")->row_array();
        if (empty($isi_keranjang) || $isi_keranjang['jumlah'] <= 0) {
            // Hapus data transaksi dari rb_penjualan jika keranjang kosong
            $this->Model_app->delete('rb_penjualan', array('id_penjualan' => $this->session->idp));
            $this->session->unset_userdata('idp');
        }
        redirect('members/keranjang');
    }

    /**
     * Menampilkan detail pesanan yang sudah ada (bukan keranjang aktif).
     */
    public function keranjang_detail($id_penjualan) {
        $data = $this->default_data;
        $data['title'] = 'Detail Pesanan Anda';
        // Mengambil data penjualan/transaksi [cite: 878]
        $data['rows'] = $this->Model_reseller->penjualan_konsumen_detail($id_penjualan)->row_array();
        // Mengambil item-item produk dalam penjualan tersebut [cite: 878]
        $data['record'] = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $id_penjualan), 'id_penjualan_detail', 'ASC')->result_array();

        // Pastikan pesanan ini milik user yang login
        if (!$data['rows'] || $data['rows']['id_pembeli'] != $this->session->id_konsumen) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Pesanan tidak ditemukan atau bukan milik Anda.</div>');
            redirect('members/orders_report');
            return;
        }

        $this->template->load(template().'/template', template().'/reseller/members/view_keranjang_detail', $data);
    }

    /**
     * Proses checkout dan finalisasi belanja.
     */
    public function selesai_belanja() {
        if ($this->input->post('submit') && $this->session->idp) {
            // Ambil data identitas toko dan konsumen [cite: 881, 882]
            $identitas_toko = $this->Model_app->view_where('tb_identitas', array('id_identitas' => '1'))->row_array();
            $konsumen_info = $this->Model_reseller->profile_konsumen($this->session->id_konsumen)->row_array();
            $transaksi_info = $this->Model_app->view_where('rb_penjualan', array('id_penjualan' => $this->session->idp))->row_array();
            $reseller_info = $this->Model_app->view_where('rb_reseller', array('id_reseller' => $transaksi_info['id_penjual']))->row_array();

            // Update data pengiriman di tabel rb_penjualan [cite: 882]
            $pengiriman_data = array(
                'kurir' => $this->input->post('kurir'),
                'service' => $this->input->post('service'),
                'ongkir' => preg_replace("/[^0-9]/", "", $this->input->post('ongkir')) // Hanya angka
            );
            $this->Model_app->update('rb_penjualan', $pengiriman_data, array('id_penjualan' => $this->session->idp));

            // Kirim email konfirmasi ke konsumen
            // (Logika pengiriman email diadaptasi dari PDF)
            $email_tujuan = $konsumen_info['email'];
            $subject = $identitas_toko['nama_website'] . " - Detail Orderan Anda #" . $transaksi_info['kode_transaksi'];
            // Buat isi email (message) seperti di PDF, mengambil data dari $konsumen_info, $reseller_info, item keranjang, dll.
            // ... (kode untuk membangun $message email seperti di PDF halaman 251-254) ...
            $items_keranjang = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $this->session->idp), 'id_penjualan_detail', 'ASC')->result_array();
            $total_belanja = $this->db->query("SELECT sum((a.harga_jual*a.jumlah)-a.diskon) as total, sum(b.berat*a.jumlah) as total_berat FROM `rb_penjualan_detail` a JOIN rb_produk b ON a.id_produk=b.id_produk where a.id_penjualan='" . $this->session->idp . "'")->row_array();
            $rekening_reseller_list = $this->Model_app->view_where('rb_rekening_reseller', array('id_reseller' => $transaksi_info['id_penjual']))->result_array();


            // Load view email sebagai string
            $email_data = array(
                'konsumen' => $konsumen_info,
                'reseller' => $reseller_info,
                'transaksi' => $transaksi_info,
                'items' => $items_keranjang,
                'total_belanja' => $total_belanja,
                'ongkir' => $pengiriman_data['ongkir'],
                'rekening_reseller' => $rekening_reseller_list,
                'identitas_toko' => $identitas_toko
            );
            $message_html = $this->load->view('emails/order_confirmation_member', $email_data, TRUE); // Buat view email ini

            $this->email->from($identitas_toko['email'], $identitas_toko['nama_website']);
            $this->email->to($email_tujuan);
            $this->email->subject($subject);
            $this->email->message($message_html);
            $this->email->set_mailtype("html");
            @$this->email->send(); // Gunakan @ untuk menekan error jika email gagal terkirim di localhost

            // Hapus session keranjang [cite: 893]
            $this->session->unset_userdata('idp');
            $this->session->set_flashdata('message', '<div class="alert alert-success">Transaksi berhasil! Silakan cek email Anda untuk detail pesanan dan instruksi pembayaran.</div>');
            redirect('members/orders_report/orders'); // Arahkan ke halaman laporan pesanan
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Keranjang belanja Anda kosong atau terjadi kesalahan.</div>');
            redirect('members/keranjang');
        }
    }

    /**
     * Membatalkan transaksi yang masih ada di keranjang.
     */
    public function batalkan_transaksi() {
        if ($this->session->idp) {
            // Hapus data dari rb_penjualan_detail dan rb_penjualan [cite: 894, 895]
            $this->Model_app->delete('rb_penjualan_detail', array('id_penjualan' => $this->session->idp));
            $this->Model_app->delete('rb_penjualan', array('id_penjualan' => $this->session->idp));
            $this->session->unset_userdata('idp');
            $this->session->set_flashdata('message', '<div class="alert alert-info">Transaksi telah dibatalkan.</div>');
        }
        redirect('members/profile'); // Atau ke halaman lain
    }


    /**
     * Menampilkan laporan pesanan member.
     */
    public function orders_report($status_info = NULL) {
        $data = $this->default_data;
        $data['title'] = 'Laporan Pesanan Anda';
        // Mengambil laporan pesanan dari Model_reseller [cite: 863]
        $data['record'] = $this->Model_reseller->orders_report($this->session->id_konsumen, 'reseller')->result_array(); // 'reseller' adalah status_penjual di tabel rb_penjualan

        if($status_info == 'orders'){
             $data['info_message'] = "<div class='alert alert-success'><b>SUCCESS </b> - Orderan anda sukses terkirim, silakan melakukan pembayaran ke rekening penjual dan selanjutnya lakukan konfirmasi pembayaran!</div>";
        }

        $this->template->load(template().'/template', template().'/reseller/members/view_orders_report', $data);
    }

    /**
     * Proses logout member.
     */
    public function logout() {
        // Hancurkan session member [cite: 901]
        $this->session->sess_destroy(); // Atau unset_userdata spesifik untuk member
        redirect('main'); // Arahkan ke halaman utama publik
    }


    // ---- FUNGSI AJAX (jika diperlukan, contoh dari PDF Members controller) ----
    /**
     * AJAX untuk mengambil kota berdasarkan provinsi (digunakan di form profil/checkout).
     * Anda perlu membuat view atau langsung output JSON.
     */
    public function kota(){
        if($this->input->is_ajax_request()){
            $provinsi_id = $this->input->post('prov_id');
            $kota_kabupaten = $this->Model_app->view_where_ordering('rb_kota',array('provinsi_id' => $provinsi_id),'kota_id','ASC')->result_array();
            echo "<option value=''>- Pilih Kota / Kabupaten -</option>";
            foreach ($kota_kabupaten as $kota){
                echo "<option value='".$kota['kota_id']."'>".$kota['nama_kota']."</option>";
            }
        } else {
            show_404();
        }
    }
}