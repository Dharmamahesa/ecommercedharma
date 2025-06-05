<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Data default dan helper
$website_name_cart = isset($website_name) ? $website_name : (isset($iden['nama_website']) ? $iden['nama_website'] : 'Toko Online Saya');
$controller_name_cart = isset($controller_name) ? $controller_name : 'produk'; // atau 'members' sesuai controller
$current_transaction_id = isset($rows['id_penjualan']) ? $rows['id_penjualan'] : $this->session->idp; // idp dari PDF

// Helper function
if (!function_exists('format_rupiah_cart')) {
    function format_rupiah_cart($number) {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}
if (!function_exists('get_first_image_cart')) {
    function get_first_image_cart($gambar_string, $default = 'no-image.png') {
        if (!empty($gambar_string)) {
            $img = explode(';', $gambar_string);
            return trim($img[0]);
        }
        return $default;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - <?php echo htmlspecialchars($website_name_cart); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .cart-container { margin-top: 30px; margin-bottom: 30px; }
        .cart-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 25px 30px;
        }
        .cart-header {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 25px;
            font-size: 1.75rem;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 15px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }
        .cart-item-details { flex-grow: 1; }
        .cart-item-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: #007bff;
            text-decoration: none;
        }
        .cart-item-title:hover { text-decoration: underline; }
        .cart-item-meta { font-size: 0.85rem; color: #6c757d; }
        .cart-item-price, .cart-item-subtotal { font-weight: 500; font-size: 1rem; }
        .cart-item-subtotal { color: #28a745; }
        .cart-item-actions .btn-remove { color: #dc3545; font-size: 1.1rem; }
        .cart-summary { margin-top: 25px; padding-top: 20px; border-top: 1px solid #e9ecef; }
        .summary-label { font-weight: 500; }
        .summary-value { font-weight: 600; }
        .total-bayar-value { font-size: 1.5rem; color: #dc3545; font-weight: 700; }
        .btn-custom-checkout {
            background-color: #28a745; border-color: #28a745; color: white;
            padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 8px;
        }
        .btn-custom-checkout:hover { background-color: #218838; border-color: #1e7e34; }
        .btn-custom-light { border-radius: 8px; padding: 0.6rem 1.2rem; }
        .info-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .info-box h6 { font-weight: 600; color: #007bff; margin-bottom: 10px; }
        .form-control-sm { border-radius: 6px; }
        .alert-cart { border-radius: 8px; }
        /* Navbar sederhana */
        .navbar-brand img { max-height: 40px; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.05); margin-bottom:0;}
        .footer { background-color: #343a40; color: white; padding: 30px 0; margin-top: 40px; text-align: center;}
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php // Ambil logo dari $this->default_data['site_logo'] atau $this->default_data['iden']['logo'] jika ada
                // Untuk contoh, kita gunakan nama website
                echo htmlspecialchars($website_name_cart);
                ?>
            </a>
            <div class="ml-auto">
                <a href="<?php echo site_url(); // Arahkan ke halaman utama produk ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-shopping-bag mr-1"></i> Lanjut Belanja
                </a>
            </div>
        </div>
    </nav>

    <div class="container cart-container">
        <div class="cart-card">
            <h3 class="cart-header"><i class="fas fa-shopping-cart mr-2"></i>Keranjang Belanja Anda</h3>

            <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-info alert-dismissible fade show alert-cart" role="alert">
                    <?php echo $this->session->flashdata('message'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if (isset($error_reseller) && !empty($error_reseller)): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-cart" role="alert">
                    <?php echo $error_reseller; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (empty($record)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="lead text-muted">Keranjang belanja Anda masih kosong.</p>
                    <a href="<?php echo site_url(); // Ganti dengan halaman produk utama Anda ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-store mr-1"></i> Mulai Belanja
                    </a>
                </div>
            <?php else: ?>
                <?php
                // Form untuk proses checkout, termasuk pemilihan kurir
                // Nama controller bisa 'produk' atau 'members' tergantung di mana logika checkout ditempatkan
                $checkout_controller_path = isset($rows['status_penjual']) && $rows['status_penjual'] == 'reseller' ? 'members' : 'produk';
                echo form_open($checkout_controller_path . '/selesai_belanja', array('id' => 'formCheckout'));
                // atau jika checkout ada di controller Produk
                // echo form_open('produk/proses_checkout', array('id' => 'formCheckout'));
                ?>

                <div class="mb-3">
                    <?php foreach ($record as $item):
                        $gambar_item = get_first_image_cart(isset($item['gambar']) ? $item['gambar'] : '');
                        $link_produk = site_url('produk/detail/' . (isset($item['produk_seo']) ? $item['produk_seo'] : $item['id_produk']));
                        $sub_total_item = (isset($item['harga_jual']) ? $item['harga_jual'] : 0) * (isset($item['jumlah']) ? $item['jumlah'] : 0) - (isset($item['diskon']) ? $item['diskon'] : 0);
                    ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <a href="<?php echo $link_produk; ?>">
                                <img src="<?php echo base_url('asset/foto_produk/' . htmlspecialchars($gambar_item)); ?>" alt="<?php echo htmlspecialchars(isset($item['nama_produk']) ? $item['nama_produk'] : ''); ?>">
                            </a>
                        </div>
                        <div class="cart-item-details">
                            <a href="<?php echo $link_produk; ?>" class="cart-item-title"><?php echo htmlspecialchars(isset($item['nama_produk']) ? $item['nama_produk'] : 'Nama Produk'); ?></a>
                            <div class="cart-item-meta">
                                Harga: <?php echo format_rupiah_cart((isset($item['harga_jual']) ? $item['harga_jual'] : 0) - (isset($item['diskon']) ? $item['diskon'] : 0)); ?>
                                x <?php echo isset($item['jumlah']) ? $item['jumlah'] : 0; ?> <?php echo htmlspecialchars(isset($item['satuan']) ? $item['satuan'] : ''); ?>
                            </div>
                            <div class="cart-item-subtotal">Subtotal: <?php echo format_rupiah_cart($sub_total_item); ?></div>
                        </div>
                        <div class="cart-item-actions">
                            <a href="<?php echo site_url($checkout_controller_path . '/keranjang_delete/' . (isset($item['id_penjualan_detail']) ? $item['id_penjualan_detail'] : '')); ?>" class="btn btn-link btn-remove" title="Hapus item" onclick="return confirm('Anda yakin ingin menghapus item ini dari keranjang?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <?php if (isset($rows['nama_reseller'])): // Info Penjual/Reseller ?>
                        <div class="info-box">
                            <h6><i class="fas fa-store mr-1"></i> Informasi Penjual</h6>
                            <strong><?php echo htmlspecialchars($rows['nama_reseller']); ?></strong><br>
                            <?php if(isset($rows['kota_penjual'])): echo htmlspecialchars($rows['kota_penjual']); endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($rowsk['nama_lengkap'])): // Info Alamat Pengiriman Konsumen ?>
                        <div class="info-box">
                            <h6><i class="fas fa-map-marker-alt mr-1"></i> Alamat Pengiriman</h6>
                            <strong><?php echo htmlspecialchars($rowsk['nama_lengkap']); ?></strong><br>
                            <?php echo htmlspecialchars($rowsk['alamat_lengkap']); ?><br>
                            Kec. <?php echo htmlspecialchars($rowsk['kecamatan']); ?>, <?php echo htmlspecialchars(isset($rowsk['kota']) ? $rowsk['kota'] : ''); ?><br>
                            <?php echo htmlspecialchars(isset($rowsk['propinsi']) ? $rowsk['propinsi'] : ''); ?><br>
                            Telp: <?php echo htmlspecialchars($rowsk['no_telp']); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <div class="cart-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="summary-label">Subtotal Produk:</span>
                                <span class="summary-value"><?php echo format_rupiah_cart(isset($total['total_harga_produk']) ? $total['total_harga_produk'] : 0); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="summary-label">Total Berat:</span>
                                <span class="summary-value"><?php echo isset($total['total_berat']) ? number_format($total['total_berat'], 0, ',', '.') : 0; ?> gram</span>
                            </div>

                            <hr>
                            <h5 class="mb-3 font-weight-normal">Opsi Pengiriman</h5>
                            <div class="form-group">
                                <label for="kurir" class="control-label sr-only">Pilih Kurir</label>
                                <select name="kurir" id="kurir" class="form-control form-control-sm">
                                    <option value="">-- Pilih Kurir --</option>
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI</option>
                                    </select>
                            </div>
                            <div class="form-group" id="kurirServiceContainer" style="display:none;">
                                <label for="service" class="control-label sr-only">Pilih Layanan</label>
                                <select name="service" id="service" class="form-control form-control-sm">
                                    <option value="">-- Pilih Layanan --</option>
                                </select>
                            </div>
                            <input type="hidden" name="ongkir" id="ongkirVal" value="0">
                            <input type="hidden" name="total_berat_gram" id="totalBeratGram" value="<?php echo isset($total['total_berat']) ? $total['total_berat'] : 0; ?>">
                            <input type="hidden" name="kota_asal_id" id="kotaAsalId" value="<?php echo isset($rows['kota_id']) ? $rows['kota_id'] : ''; // ID Kota Penjual/Asal ?>">
                            <input type="hidden" name="kota_tujuan_id" id="kotaTujuanId" value="<?php echo isset($rowsk['kota_id']) ? $rowsk['kota_id'] : ''; // ID Kota Konsumen/Tujuan ?>">


                            <div class="d-flex justify-content-between mt-3 mb-2">
                                <span class="summary-label">Biaya Pengiriman:</span>
                                <span class="summary-value" id="ongkirDisplay"><?php echo format_rupiah_cart(0); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <h5 class="summary-label mb-0">Total Pembayaran:</h5>
                                <span class="total-bayar-value" id="totalBayarDisplay"><?php echo format_rupiah_cart(isset($total['total_harga_produk']) ? $total['total_harga_produk'] : 0); ?></span>
                            </div>

                            <button type="submit" name="submit" class="btn btn-custom-checkout btn-block mt-4" id="btnToCheckout" disabled>
                                <i class="fas fa-shield-alt mr-1"></i> Lanjut ke Pembayaran
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?php echo site_url(); // Ganti dengan halaman produk utama Anda ?>" class="btn btn-outline-secondary btn-custom-light">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali Belanja
                    </a>
                    <a href="<?php echo site_url($checkout_controller_path . '/batalkan_transaksi'); ?>" class="btn btn-outline-danger btn-custom-light" onclick="return confirm('Anda yakin ingin membatalkan seluruh transaksi ini dan mengosongkan keranjang?')">
                        <i class="fas fa-times-circle mr-1"></i> Batalkan Transaksi
                    </a>
                </div>
                <?php echo form_close(); ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($website_name_cart); ?>. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

        function updateTotalBayar() {
            var subtotalProduk = parseFloat(<?php echo isset($total['total_harga_produk']) ? $total['total_harga_produk'] : 0; ?>) || 0;
            var ongkir = parseFloat($('#ongkirVal').val()) || 0;
            var totalBayar = subtotalProduk + ongkir;
            $('#totalBayarDisplay').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalBayar));

            if (ongkir > 0 || $('#kurir').val() === 'cod' || $('#kurir').val() === '') { // COD atau jika belum pilih kurir (anggap bisa tanpa ongkir jika COD/belum pilih)
                 $('#btnToCheckout').prop('disabled', false);
            } else {
                 $('#btnToCheckout').prop('disabled', true);
            }
        }

        $('#kurir').on('change', function() {
            var kurir = $(this).val();
            var berat = $('#totalBeratGram').val();
            var kota_asal = $('#kotaAsalId').val(); // ID Kota asal (penjual)
            var kota_tujuan = $('#kotaTujuanId').val(); // ID Kota tujuan (pembeli)
            var serviceDropdown = $('#service');
            var serviceContainer = $('#kurirServiceContainer');
            var ongkirDisplay = $('#ongkirDisplay');
            var ongkirVal = $('#ongkirVal');

            ongkirDisplay.text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(0));
            ongkirVal.val(0);
            serviceDropdown.html('<option value="">-- Memuat Layanan --</option>');
            updateTotalBayar(); // Reset total bayar

            if (kurir && berat > 0 && kota_asal && kota_tujuan) {
                serviceContainer.show();
                $.ajax({
                    url: "<?php echo site_url('produk/cek_ongkir'); ?>", // Anda perlu membuat method ini di controller Produk
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        kurir: kurir,
                        berat: berat,
                        kota_asal: kota_asal,
                        kota_tujuan: kota_tujuan,
                        [csrf_name]: csrf_hash
                    },
                    success: function(response) {
                        csrf_name = response.csrf_name || csrf_name; // Update CSRF token name if sent
                        csrf_hash = response.csrf_hash || csrf_hash; // Update CSRF token hash
                        serviceDropdown.html('<option value="">-- Pilih Layanan --</option>');
                        if (response.success && response.rajaongkir && response.rajaongkir.results && response.rajaongkir.results[0] && response.rajaongkir.results[0].costs) {
                            $.each(response.rajaongkir.results[0].costs, function(i, cost) {
                                serviceDropdown.append('<option value="' + cost.service + '|' + cost.cost[0].value + '">' + cost.service + ' (' + cost.description + ') - ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(cost.cost[0].value) + ' ('+cost.cost[0].etd+' hari)</option>');
                            });
                        } else {
                            serviceDropdown.append('<option value="">Layanan tidak tersedia</option>');
                             $('#btnToCheckout').prop('disabled', true);
                        }
                    },
                    error: function() {
                        serviceDropdown.html('<option value="">Gagal memuat layanan</option>');
                        serviceContainer.hide();
                        $('#btnToCheckout').prop('disabled', true);
                    }
                });
            } else {
                serviceContainer.hide();
                if(kurir === 'cod'){ // Jika COD, aktifkan tombol checkout
                     $('#btnToCheckout').prop('disabled', false);
                } else {
                     $('#btnToCheckout').prop('disabled', true);
                }
            }
        });

        $('#service').on('change', function() {
            var selectedService = $(this).val();
            var ongkir = 0;
            if (selectedService) {
                ongkir = parseFloat(selectedService.split('|')[1]) || 0;
            }
            $('#ongkirDisplay').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(ongkir));
            $('#ongkirVal').val(ongkir);
            updateTotalBayar();
        });

        // Panggil sekali untuk inisialisasi
        updateTotalBayar();
    });
    </script>
</body>
</html>