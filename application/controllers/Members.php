<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Model_reseller $Model_reseller // Model utama untuk data reseller dan konsumen
 * @property Model_app $Model_app       // Model umum untuk operasi database
 * @property Produk_model $Produk_model   // Untuk detail produk di keranjang, dll.
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload         // Untuk upload foto profil
 * @property CI_Email $email           // Untuk mengirim email
 * @property CI_Template $template     // Asumsi library template sudah di-load
 * @property CI_Config $config
 */
class Members extends CI_Controller {

    protected $default_data;

    public function __construct() {
        parent::__construct();
        // Load helper dan library dasar
        $this->load->helper(array('url', 'form', 'phpmu', 'download', 'html', 'engine', 'captcha', 'cookie', 'string', 'text'));
        $this->load->library(array('session', 'form_validation', 'pagination', 'template', 'user_agent', 'email', 'upload'));


        // Load model yang sering digunakan
        $this->load->model('Model_reseller');
        $this->load->model('Model_app');
        $this->load->model('Produk_model'); // Untuk mengambil detail produk

        // Data default untuk view (bisa diambil dari database identitas)
        $identitas_query = $this->Model_app->edit('tb_identitas', ['id_identitas' => 1]);
        $identitas = $identitas_query ? $identitas_query->row_array() : null;

        $this->default_data = array(
            'website_name'    => isset($identitas['nama_website']) ? $identitas['nama_website'] : 'Toko Online Saya',
            'controller_name' => 'members', // Nama controller saat ini
            'iden'            => $identitas   // Menyimpan semua data identitas
        );
        
        // Cek apakah member sudah login untuk semua method kecuali 'logout' atau method publik lainnya
        // Fungsi cek_session_members() dari helper phpmu.php
        // Jika helper phpmu tidak di-autoload, load manual: $this->load->helper('phpmu');
        $allowed_public_methods = ['logout']; // Tambahkan method lain yang boleh diakses tanpa login
        if (!in_array($this->uri->segment(2), $allowed_public_methods)) {
            cek_session_members();
        }
    }

    /**
     * Halaman dashboard atau profil utama member.
     */
    public function index() {
        $this->profile(); // Alihkan ke halaman profil sebagai default
    }

    /**
     * Menampilkan halaman profil konsumen/member.
     * Mengacu pada PDF halaman 241
     */
    public function profile() {
        $data = $this->default_data;
        $data['title'] = 'Profil Anda';
        $profile_query = $this->Model_reseller->profile_konsumen($this->session->id_konsumen);
        $data['row'] = $profile_query ? $profile_query->row_array() : null;

        // Jika profil tidak ditemukan (seharusnya tidak terjadi jika session valid)
        if (!$data['row']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Data profil tidak ditemukan. Silakan login kembali.</div>');
            redirect('auth/login'); // Arahkan ke login member
            return;
        }
        $this->template->load(template().'/template', template().'/reseller/view_profile', $data);
    }

    /**
     * Menampilkan form dan memproses edit profil konsumen/member.
     * Mengacu pada PDF halaman 241-242
     */
    public function edit_profile() {
        $data = $this->default_data;
        $data['title'] = 'Edit Profil Anda';

        // Aturan validasi form (sesuaikan dengan field di view_profile_edit.php)
        // Nama input di PDF menggunakan 'aa' untuk username, 'a' untuk password, 'b' untuk nama, dst.
        $this->form_validation->set_rules('b', 'Nama Lengkap', 'required|trim|strip_tags');
        $this->form_validation->set_rules('c', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('d', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('e', 'Tanggal Lahir', 'required|trim'); // Format YYYY-MM-DD
        $this->form_validation->set_rules('f', 'Tempat Lahir', 'required|trim|strip_tags');
        $this->form_validation->set_rules('g', 'Alamat Lengkap', 'required|trim|strip_tags');
        $this->form_validation->set_rules('ga', 'Kota/Kabupaten', 'required|integer'); // kota_id
        $this->form_validation->set_rules('k', 'Kecamatan', 'required|trim|strip_tags'); // 'k' dari PDF, mungkin harusnya 'ia'
        $this->form_validation->set_rules('l', 'No. Telepon/HP', 'required|trim|numeric'); // 'l' (no_hp) dari PDF

        if ($this->input->post('a')) { // Jika password baru diisi
            $this->form_validation->set_rules('a', 'Password Baru', 'trim|min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            $profile_query = $this->Model_reseller->profile_konsumen($this->session->id_konsumen);
            $data['row'] = $profile_query ? $profile_query->row_array() : null;
            if (!$data['row']) { redirect('members/profile'); return; } // Pengaman

            $provinsi_query = $this->Model_app->view_ordering('rb_provinsi', 'provinsi_id', 'ASC');
            $data['provinsi'] = $provinsi_query ? $provinsi_query->result_array() : [];

            // Ambil provinsi_id dari kota konsumen untuk pre-select dropdown provinsi dan kota
            if(isset($data['row']['kota_id']) && $data['row']['kota_id']){
                $kota_query = $this->Model_app->edit('rb_kota', ['kota_id' => $data['row']['kota_id']]);
                $kota_detail = $kota_query ? $kota_query->row_array() : null;
                $data['rowse'] = $kota_detail; // Berisi detail kota termasuk provinsi_id
            } else {
                $data['rowse'] = null;
            }

            $this->template->load(template().'/template', template().'/reseller/view_profile_edit', $data);
        } else {
            // Proses update profil menggunakan method dari Model_reseller
            // Method profile_update di Model_reseller Anda mengambil data langsung dari input post
            if ($this->Model_reseller->profile_update($this->session->id_konsumen)) {
                 $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">Profil berhasil diperbarui!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            } else {
                 $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Gagal memperbarui profil. Silakan coba lagi.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
            redirect('members/profile');
        }
    }

    /**
     * Mengupdate foto profil member.
     * Mengacu pada PDF halaman 241
     */
    public function foto() {
        if ($this->input->post('submit')) {
            // Method modupdatefoto di Model_reseller Anda menangani upload dan update DB
            // Pastikan nama input file di form adalah 'userfile' atau sesuai yang diharapkan model
            if ($this->Model_reseller->modupdatefoto()) { // modupdatefoto menggunakan nama 'userfile' di PDFnya (halaman 84)
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">Foto profil berhasil diperbarui!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Gagal mengupload foto: '.$this->upload->display_errors('','').'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
        }
        redirect('members/profile');
    }


    /**
     * Menampilkan dan memproses keranjang belanja member.
     * Logika ini diadaptasi dari controller Members di PDF halaman 245-248
     */
    public function keranjang($id_reseller_url = NULL, $id_produk_url = NULL) {
        $data = $this->default_data;
        $data['title'] = 'Keranjang Belanja Anda';
        $data['error_reseller'] = ''; // Inisialisasi pesan error

        if ($id_produk_url !== NULL && $id_reseller_url !== NULL) {
            // --- LOGIKA PENAMBAHAN PRODUK KE KERANJANG ---
            $qty_diminta = (int)($this->input->post('qty') ? $this->input->post('qty') : 1);
            if ($qty_diminta <= 0) $qty_diminta = 1;

            // Cek stok produk dari reseller tersebut
            // jual_reseller dan beli_reseller di Model_reseller PDF digunakan untuk kalkulasi stok
            $jual_query = $this->Model_reseller->jual_reseller($id_reseller_url, $id_produk_url);
            $jual = $jual_query ? $jual_query->row_array() : ['total_jual' => 0];

            $beli_query = $this->Model_reseller->beli_reseller($id_reseller_url, $id_produk_url);
            $beli = $beli_query ? $beli_query->row_array() : ['total_beli' => 0];
            
            $stok_produk_reseller = (isset($beli['total_beli']) ? $beli['total_beli'] : 0) - (isset($jual['total_jual']) ? $jual['total_jual'] : 0);

            if ($stok_produk_reseller < $qty_diminta) {
                $produk_info_query = $this->Produk_model->get_by_id($id_produk_url); // Gunakan Produk_model
                $produk_info = $produk_info_query ? $produk_info_query->row_array() : null;
                $nama_produk_cek = $produk_info ? filter($produk_info['nama_produk']) : 'Produk';
                $this->session->set_flashdata('message', "<div class='alert alert-danger'>Maaf, Stok untuk Produk $nama_produk_cek pada Penjual ini tidak mencukupi! (Sisa: $stok_produk_reseller)</div>");
                // Arahkan kembali ke halaman produk reseller atau detail produk
                $source_page = $this->uri->segment(5); // Jika ada parameter ke-5 seperti 'from_detail'
                if($source_page == 'from_detail' && $produk_info){
                     redirect('produk/detail/' . ($produk_info['produk_seo'] ? $produk_info['produk_seo'] : $id_produk_url) );
                } else {
                     redirect('produk/produk_reseller/' . $id_reseller_url);
                }
                return;
            }

            // Logika session keranjang 'idp' (id_penjualan) dari PDF
            if ($this->session->idp == '') {
                $kode_transaksi = 'TRX-' . date('YmdHis');
                $penjualan_data = array(
                    'kode_transaksi' => $kode_transaksi,
                    'id_pembeli' => $this->session->id_konsumen,
                    'id_penjual' => $id_reseller_url,
                    'status_pembeli' => 'konsumen',
                    'status_penjual' => 'reseller',
                    'waktu_transaksi' => date('Y-m-d H:i:s'),
                    'proses' => '0' // Keranjang
                );
                $this->Model_app->insert('rb_penjualan', $penjualan_data);
                $this->session->set_userdata(array('idp' => $this->db->insert_id()));
            }

            // Cek apakah transaksi saat ini dari reseller yang sama
            $transaksi_aktif_query = $this->Model_app->edit('rb_penjualan', array('id_penjualan' => $this->session->idp));
            $transaksi_aktif = $transaksi_aktif_query ? $transaksi_aktif_query->row_array() : null;

            if ($transaksi_aktif && $transaksi_aktif['id_penjual'] != $id_reseller_url) {
                $data['error_reseller'] = "<div class='alert alert-danger'>Maaf, Dalam 1 Transaksi hanya boleh order dari 1 Penjual saja. Silakan selesaikan atau batalkan transaksi sebelumnya.</div>";
            } else {
                $produk_harga_info_query = $this->Produk_model->get_by_id($id_produk_url); // Gunakan Produk_model
                $produk_harga_info = $produk_harga_info_query ? $produk_harga_info_query->row_array() : null;

                if ($produk_harga_info) {
                    $diskon_info_query = $this->Produk_model->get_diskon_produk($id_produk_url, $id_reseller_url); // Dari Produk_model
                    $diskon_info = $diskon_info_query ? $diskon_info_query->row_array() : null;
                    
                    $harga_jual_final = isset($produk_harga_info['harga_konsumen']) ? $produk_harga_info['harga_konsumen'] : 0;
                    $diskon_nominal = isset($diskon_info['diskon']) ? $diskon_info['diskon'] : 0;

                    $detail_data = array(
                        'id_penjualan' => $this->session->idp,
                        'id_produk' => $id_produk_url,
                        'jumlah' => $qty_diminta,
                        'harga_jual' => $harga_jual_final,
                        'diskon' => $diskon_nominal,
                        'satuan' => $produk_harga_info['satuan']
                    );

                    $item_keranjang_query = $this->Model_app->view_where('rb_penjualan_detail', array('id_penjualan' => $this->session->idp, 'id_produk' => $id_produk_url));
                    $item_keranjang = $item_keranjang_query ? $item_keranjang_query->row_array() : null;

                    if ($item_keranjang) {
                        $this->db->query("UPDATE rb_penjualan_detail SET jumlah = jumlah + " . (int)$qty_diminta . " WHERE id_penjualan_detail = '" . $item_keranjang['id_penjualan_detail'] . "'");
                    } else {
                        $this->Model_app->insert('rb_penjualan_detail', $detail_data);
                    }
                    $this->session->set_flashdata('message', '<div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>');
                } else {
                     $this->session->set_flashdata('message', '<div class="alert alert-danger">Gagal mengambil detail produk.</div>');
                }
                // Arahkan ke halaman keranjang untuk melihat hasilnya
                redirect('members/keranjang');
                return;
            }
        }

        // --- MENAMPILKAN ISI KERANJANG ---
        if ($this->session->idp != '') {
            $data['rows'] = $this->Model_reseller->penjualan_konsumen_detail($this->session->idp)->row_array();
            $konsumen_query = $this->Model_reseller->view_join_where_one('rb_konsumen', 'rb_kota', 'kota_id', array('id_konsumen' => $this->session->id_konsumen));
            $data['rowsk'] = $konsumen_query ? $konsumen_query->row_array() : null;

            $items_query = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $this->session->idp), 'id_penjualan_detail', 'ASC');
            $data['record'] = $items_query ? $items_query->result_array() : [];

            $total_query = $this->db->query("SELECT sum((a.harga_jual*a.jumlah)-a.diskon) as total_harga_produk, sum(b.berat*a.jumlah) as total_berat FROM `rb_penjualan_detail` a JOIN rb_produk b ON a.id_produk=b.id_produk where a.id_penjualan='" . $this->session->idp . "'");
            $data['total'] = $total_query ? $total_query->row_array() : ['total_harga_produk' => 0, 'total_berat' => 0];
        } else {
            $data['record'] = [];
            $data['rows'] = null;
            $data['rowsk'] = null;
            $data['total'] = ['total_harga_produk' => 0, 'total_berat' => 0];
        }
        $this->template->load(template().'/template', template().'/reseller/members/view_keranjang', $data);
    }

    /**
     * Menghapus item dari keranjang belanja.
     * PDF halaman 249
     */
    public function keranjang_delete($id_penjualan_detail) {
        if ($this->session->idp && $id_penjualan_detail) {
            $this->Model_app->delete('rb_penjualan_detail', array('id_penjualan_detail' => $id_penjualan_detail, 'id_penjualan' => $this->session->idp));
            $isi_keranjang_query = $this->db->query("SELECT SUM(jumlah) as jumlah FROM rb_penjualan_detail WHERE id_penjualan='" . $this->session->idp . "'");
            $isi_keranjang = $isi_keranjang_query ? $isi_keranjang_query->row_array() : null;
            if (!$isi_keranjang || $isi_keranjang['jumlah'] <= 0) {
                $this->Model_app->delete('rb_penjualan', array('id_penjualan' => $this->session->idp));
                $this->session->unset_userdata('idp');
            }
        }
        redirect('members/keranjang');
    }

    /**
     * Menampilkan detail pesanan yang sudah ada (bukan keranjang aktif).
     * PDF halaman 249
     */
    public function keranjang_detail($id_penjualan) {
        $data = $this->default_data;
        $data['title'] = 'Detail Pesanan Anda';

        $penjualan_query = $this->Model_reseller->penjualan_konsumen_detail($id_penjualan);
        $data['rows'] = $penjualan_query ? $penjualan_query->row_array() : null;

        $items_query = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $id_penjualan), 'id_penjualan_detail', 'ASC');
        $data['record'] = $items_query ? $items_query->result_array() : [];

        if (!$data['rows'] || (isset($data['rows']['id_pembeli']) && $data['rows']['id_pembeli'] != $this->session->id_konsumen)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Pesanan tidak ditemukan atau bukan milik Anda.</div>');
            redirect('members/orders_report');
            return;
        }
        $this->template->load(template().'/template', template().'/reseller/members/view_keranjang_detail', $data);
    }

    /**
     * Proses checkout dan finalisasi belanja.
     * PDF halaman 250
     */
    public function selesai_belanja() {
        if ($this->input->post('submit') && $this->session->idp) {
            $id_penjualan_aktif = $this->session->idp;

            $transaksi_info_q = $this->Model_app->view_where('rb_penjualan', array('id_penjualan' => $id_penjualan_aktif));
            $transaksi_info = $transaksi_info_q ? $transaksi_info_q->row_array() : null;

            if(!$transaksi_info){
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Data transaksi tidak ditemukan.</div>');
                redirect('members/keranjang');
                return;
            }

            // Update data pengiriman di tabel rb_penjualan
            $ongkir_raw = $this->input->post('ongkir', TRUE);
            $pengiriman_data = array(
                'kurir'   => $this->input->post('kurir', TRUE),
                'service' => $this->input->post('service', TRUE),
                'ongkir'  => preg_replace("/[^0-9]/", "", $ongkir_raw),
                'proses'  => '1' // Status berubah menjadi "Sedang Diproses" setelah checkout
            );
            $this->Model_app->update('rb_penjualan', $pengiriman_data, array('id_penjualan' => $id_penjualan_aktif));

            // Kirim email konfirmasi (mengambil data dari default_data dan model)
            $konsumen_info_q = $this->Model_reseller->profile_konsumen($this->session->id_konsumen);
            $konsumen_info = $konsumen_info_q ? $konsumen_info_q->row_array() : null;

            $reseller_info_q = $this->Model_app->view_where('rb_reseller', array('id_reseller' => $transaksi_info['id_penjual']));
            $reseller_info = $reseller_info_q ? $reseller_info_q->row_array() : null;
            
            $items_keranjang_q = $this->Model_app->view_join_where('rb_penjualan_detail', 'rb_produk', 'id_produk', array('id_penjualan' => $id_penjualan_aktif), 'id_penjualan_detail', 'ASC');
            $items_keranjang = $items_keranjang_q ? $items_keranjang_q->result_array() : [];

            $total_belanja_q = $this->db->query("SELECT sum((a.harga_jual*a.jumlah)-a.diskon) as total_harga_produk, sum(b.berat*a.jumlah) as total_berat FROM `rb_penjualan_detail` a JOIN rb_produk b ON a.id_produk=b.id_produk where a.id_penjualan='" . $id_penjualan_aktif . "'");
            $total_belanja = $total_belanja_q ? $total_belanja_q->row_array() : null;
            
            $rekening_reseller_list_q = $this->Model_app->view_where('rb_rekening_reseller', array('id_reseller' => $transaksi_info['id_penjual']));
            $rekening_reseller_list = $rekening_reseller_list_q ? $rekening_reseller_list_q->result_array() : [];

            $email_data = array(
                'konsumen'           => $konsumen_info,
                'reseller'           => $reseller_info,
                'transaksi'          => array_merge($transaksi_info, $pengiriman_data), // Gabungkan dengan data ongkir
                'items'              => $items_keranjang,
                'total_belanja'      => $total_belanja,
                // 'ongkir' sudah ada di $pengiriman_data, dan akan ada di $transaksi dalam $email_data
                'rekening_reseller'  => $rekening_reseller_list,
                'identitas_toko'     => $this->default_data['iden']
            );
            $message_html = $this->load->view('emails/order_confirmation_member', $email_data, TRUE);

            $this->email->from($this->default_data['iden']['email'], $this->default_data['iden']['nama_website']);
            $this->email->to($konsumen_info['email']);
            $this->email->subject($this->default_data['iden']['nama_website'] . " - Detail Pesanan Anda #" . $transaksi_info['kode_transaksi']);
            $this->email->message($message_html);
            $this->email->set_mailtype("html");
            @$this->email->send();

            // Hapus session keranjang
            $this->session->unset_userdata('idp');
            // Pesan sukses dipindahkan ke orders_report
            redirect('members/orders_report/orders');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Keranjang belanja Anda kosong atau terjadi kesalahan saat checkout.</div>');
            redirect('members/keranjang');
        }
    }

    /**
     * Membatalkan transaksi yang masih ada di keranjang.
     * PDF halaman 255
     */
    public function batalkan_transaksi() {
        if ($this->session->idp) {
            $this->Model_app->delete('rb_penjualan_detail', array('id_penjualan' => $this->session->idp));
            $this->Model_app->delete('rb_penjualan', array('id_penjualan' => $this->session->idp));
            $this->session->unset_userdata('idp');
            $this->session->set_flashdata('message', '<div class="alert alert-info">Transaksi telah dibatalkan dan keranjang belanja dikosongkan.</div>');
        }
        redirect('members/profile');
    }

    /**
     * Menampilkan laporan pesanan member.
     * PDF halaman 254
     */
    public function orders_report($status_info = NULL) {
        $data = $this->default_data;
        $data['title'] = 'Laporan Pesanan Anda';
        $orders_query = $this->Model_reseller->orders_report($this->session->id_konsumen, 'reseller');
        $data['record'] = $orders_query ? $orders_query->result_array() : [];
        $data['info_message'] = '';

        if($status_info == 'orders'){ // Notifikasi setelah checkout berhasil
             $data['info_message'] = "<div class='alert alert-success alert-dismissible fade show' role='alert'><b>Transaksi Berhasil!</b> Pesanan Anda telah kami terima. Silakan cek email Anda untuk detail dan instruksi pembayaran.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        } elseif ($status_info == 'success') { // Notifikasi setelah konfirmasi pembayaran
             $data['info_message'] = "<div class='alert alert-success alert-dismissible fade show' role='alert'><b>Konfirmasi Sukses!</b> Terima kasih telah melakukan konfirmasi pembayaran. Pesanan Anda akan segera kami proses.<button type'button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        }

        $this->template->load(template().'/template', template().'/reseller/members/view_orders_report', $data);
    }

    /**
     * Proses logout member.
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('main');
    }

    /**
     * AJAX untuk mengambil kota berdasarkan provinsi (digunakan di form profil/checkout).
     * Sesuai dengan yang ada di Members.php PDF halaman 256 (tanpa username_check dan email_check)
     */
    public function kota(){
        if($this->input->is_ajax_request()){
            $provinsi_id = $this->input->post('prov_id', TRUE);
            if ($provinsi_id) {
                $kota_kabupaten_query = $this->Model_app->view_where_ordering('rb_kota',array('provinsi_id' => $provinsi_id),'nama_kota','ASC'); // Urutkan nama kota
                $kota_kabupaten = $kota_kabupaten_query ? $kota_kabupaten_query->result_array() : [];
                
                $csrf_name = $this->security->get_csrf_token_name();
                $csrf_hash = $this->security->get_csrf_hash();

                $options = "<option value=''>- Pilih Kota / Kabupaten -</option>";
                foreach ($kota_kabupaten as $kota){
                    $options .= "<option value='".$kota['kota_id']."'>".$kota['nama_kota']."</option>";
                }
                echo json_encode(['options' => $options, $csrf_name => $csrf_hash]);
            } else {
                echo json_encode(['options' => "<option value=''>- Pilih Provinsi Dahulu -</option>"]);
            }
        } else {
            show_404();
        }
    }

    /**
     * AJAX untuk mengambil data ongkir (memanggil API RajaOngkir atau sejenisnya).
     * Ini adalah method baru yang dibutuhkan oleh view_keranjang.php.
     */
    public function cek_ongkir(){
        if($this->input->is_ajax_request()){
            $kurir       = $this->input->post('kurir', TRUE);
            $berat       = $this->input->post('berat', TRUE); // dalam gram
            $kota_asal   = $this->input->post('kota_asal', TRUE); // ID kota asal
            $kota_tujuan = $this->input->post('kota_tujuan', TRUE); // ID kota tujuan

            // Di sini Anda akan menambahkan logika untuk request ke API RajaOngkir
            // Contoh sederhana:
            $api_key = 'API_KEY_RAJAONGKIR_ANDA'; // Ganti dengan API Key Anda
            $this->load->library('rajaongkir'); // Asumsi Anda punya library RajaOngkir atau buat fungsi sendiri

            $cost = null;
            if (class_exists('Rajaongkir')) { // Jika library RajaOngkir ada
                $this->rajaongkir->initialize(array('api_key' => $api_key, 'account_type' => 'starter')); // atau pro
                $cost = $this->rajaongkir->cost($kota_asal, $kota_tujuan, $berat, $kurir);
            } else {
                // Fallback jika library tidak ada atau error
                log_message('error', 'Library RajaOngkir tidak ditemukan atau gagal diinisialisasi.');
            }

            $response_data = array();
            if($cost && isset($cost['rajaongkir']['status']['code']) && $cost['rajaongkir']['status']['code'] == 200 && !empty($cost['rajaongkir']['results'])){
                $response_data['success'] = true;
                $response_data['rajaongkir'] = $cost['rajaongkir'];
            } else {
                $response_data['success'] = false;
                $response_data['message'] = 'Gagal mengambil data ongkir atau layanan tidak tersedia.';
                $response_data['rajaongkir'] = $cost; // Kirim respons asli untuk debugging jika perlu
            }

            // Kirim CSRF token baru
            $response_data[$this->security->get_csrf_token_name()] = $this->security->get_csrf_hash();

            header('Content-Type: application/json');
            echo json_encode($response_data);

        } else {
            show_404();
        }
    }
}