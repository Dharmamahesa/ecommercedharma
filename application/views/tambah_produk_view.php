<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Data default atau yang dikirim dari controller
$record = isset($record) ? $record : []; // Daftar kategori utama
$sub_kategori_produk_all = isset($sub_kategori_produk_all) ? $sub_kategori_produk_all : []; // Untuk JS jika diperlukan
$controller_name_path = isset($controller_name) ? $controller_name : 'reseller'; // Dari controller
$website_name = isset($website_name) ? $website_name : 'Toko Online Saya';
$site_logo = isset($site_logo) ? $site_logo : '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - <?php echo htmlspecialchars($website_name); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        .card-add-produk {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-top: 30px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .card-header-custom {
            background-color: #007bff;
            color: white;
            padding: 20px 25px;
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
        }
        .card-body-custom {
            padding: 25px 30px;
        }
        .form-group {
            margin-bottom: 1.75rem;
        }
        .form-control, .custom-file-input {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus, .custom-file-input:focus ~ .custom-file-label {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .custom-file-label {
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .custom-file-label::after {
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 1rem;
        }
        .control-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: .5rem;
        }
        .btn-submit-custom {
            background-color: #28a745;
            border-color: #28a745;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color .2s ease;
        }
        .btn-submit-custom:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-cancel-custom {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            color: #6c757d;
        }
        .image-preview-container {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #ced4da;
            min-height: 100px; /* Agar kontainer tetap terlihat meski belum ada preview */
        }
        .image-preview-container img {
            max-width: 100px; /* Ukuran thumbnail preview */
            max-height: 100px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .upload-instructions {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
        }
        .form-row > .col, .form-row > [class*="col-"] {
            padding-right: 10px;
            padding-left: 10px;
        }
        .form-row {
            margin-right: -10px;
            margin-left: -10px;
        }
         /* Navbar sederhana */
        .navbar-brand img { max-height: 40px; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.05); }
        .footer { background-color: #343a40; color: white; padding: 30px 0; margin-top: 40px; text-align: center;}
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
             <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <?php if (!empty($site_logo)): ?>
                    <img src="<?php echo base_url('asset/logo/' . htmlspecialchars($site_logo)); ?>" alt="Logo">
                <?php else: ?>
                    <?php echo htmlspecialchars($website_name); ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAdd" aria-controls="navbarNavAdd" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAdd">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url($controller_name_path); ?>">Daftar Produk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-add-produk">
                <div class="card-header-custom">
                    <h4><i class="fas fa-plus-circle mr-2"></i>Formulir Tambah Produk Baru</h4>
                </div>
                <div class="card-body-custom">
                    <?php
                    // Menampilkan pesan error upload jika ada
                    if (isset($error_upload) && !empty($error_upload)) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $error_upload . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    }
                    // Menampilkan pesan sukses atau error dari session (jika ada)
                    if ($this->session->flashdata('message')) {
                        echo $this->session->flashdata('message');
                    }

                    $attributes = array('name' => 'form_tambah_produk');
                    echo form_open_multipart($controller_name_path . '/tambah_produk', $attributes);
                    ?>

                    <div class="form-row">
                        <div class="form-group col-md-6 <?php if(form_error('nama_produk')) echo 'has-danger';?>">
                            <label for="nama_produk" class="control-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php if(form_error('nama_produk')) echo 'is-invalid';?>" name="nama_produk" value="<?php echo set_value('nama_produk'); ?>" placeholder="Contoh: Kemeja Batik Pria Lengan Panjang" required>
                            <?php echo form_error('nama_produk', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-6 <?php if(form_error('id_kategori_produk')) echo 'has-danger';?>">
                            <label for="kategori" class="control-label">Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori_produk" class="form-control <?php if(form_error('id_kategori_produk')) echo 'is-invalid';?>" id="kategori_produk" onchange="getSubKategoriProdukTambah()" required>
                                <option value="">Pilih Kategori</option>
                                <?php
                                foreach ($record as $kat) { // $record adalah daftar kategori utama
                                    echo "<option value='" . htmlspecialchars($kat['id_kategori_produk']) . "' " . set_select('id_kategori_produk', $kat['id_kategori_produk']) . ">" . htmlspecialchars($kat['nama_kategori']) . "</option>";
                                }
                                ?>
                            </select>
                            <?php echo form_error('id_kategori_produk', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                     <div class="form-group <?php if(form_error('id_kategori_produk_sub')) echo 'has-danger';?>">
                        <label for="sub_kategori" class="control-label">Sub Kategori</label>
                        <select name="id_kategori_produk_sub" class="form-control <?php if(form_error('id_kategori_produk_sub')) echo 'is-invalid';?>" id="sub_kategori_produk">
                            <option value="">Pilih Sub Kategori (Jika Ada)</option>
                            </select>
                        <?php echo form_error('id_kategori_produk_sub', '<small class="form-text text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group <?php if(form_error('keterangan')) echo 'has-danger';?>">
                        <label for="keterangan" class="control-label">Deskripsi Produk</label>
                        <textarea class="form-control <?php if(form_error('keterangan')) echo 'is-invalid';?>" name="keterangan" rows="4" placeholder="Jelaskan detail produk Anda: bahan, ukuran, fitur unggulan, dll."><?php echo set_value('keterangan'); ?></textarea>
                        <?php echo form_error('keterangan', '<small class="form-text text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4 <?php if(form_error('harga_beli')) echo 'has-danger';?>">
                            <label for="harga_beli" class="control-label">Harga Modal (Beli)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('harga_beli')) echo 'is-invalid';?>" name="harga_beli" value="<?php echo set_value('harga_beli'); ?>" placeholder="0">
                            </div>
                            <?php echo form_error('harga_beli', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('harga_konsumen')) echo 'has-danger';?>">
                            <label for="harga_konsumen" class="control-label">Harga Jual <span class="text-danger">*</span></label>
                             <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('harga_konsumen')) echo 'is-invalid';?>" name="harga_konsumen" value="<?php echo set_value('harga_konsumen'); ?>" placeholder="0" required>
                            </div>
                            <?php echo form_error('harga_konsumen', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('diskon')) echo 'has-danger';?>">
                            <label for="diskon" class="control-label">Diskon (Nominal)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('diskon')) echo 'is-invalid';?>" name="diskon" value="<?php echo set_value('diskon', 0); ?>" placeholder="0">
                            </div>
                             <?php echo form_error('diskon', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                     <div class="form-row">
                        <div class="form-group col-md-4 <?php if(form_error('stok')) echo 'has-danger';?>">
                            <label for="stok" class="control-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control <?php if(form_error('stok')) echo 'is-invalid';?>" name="stok" value="<?php echo set_value('stok', 0); ?>" placeholder="0" required>
                            <?php echo form_error('stok', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('berat')) echo 'has-danger';?>">
                            <label for="berat" class="control-label">Berat (gram)</label>
                            <input type="number" class="form-control <?php if(form_error('berat')) echo 'is-invalid';?>" name="berat" value="<?php echo set_value('berat'); ?>" placeholder="0">
                            <?php echo form_error('berat', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                         <div class="form-group col-md-4 <?php if(form_error('satuan')) echo 'has-danger';?>">
                            <label for="satuan" class="control-label">Satuan</label>
                            <input type="text" class="form-control <?php if(form_error('satuan')) echo 'is-invalid';?>" name="satuan" value="<?php echo set_value('satuan'); ?>" placeholder="Pcs, Kg, Box">
                             <?php echo form_error('satuan', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gambar_produk" class="control-label">Gambar Produk</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fileupload" name="userfile[]" multiple onchange="updateFileLabelTambah(this)">
                            <label class="custom-file-label" for="fileupload" data-browse="Pilih File">Pilih satu atau beberapa gambar...</label>
                        </div>
                        <small class="form-text upload-instructions">Anda bisa unggah beberapa gambar (gif, jpg, png, jpeg). Gambar pertama akan menjadi utama.</small>
                        <div class="image-preview-container">
                            <div id="dvPreviewTambah" class="d-flex flex-wrap">
                                <small class="text-muted align-self-center">Preview gambar akan muncul di sini</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-group text-right mb-0">
                        <a href="<?php echo site_url($controller_name_path); // Arahkan ke daftar produk reseller ?>" class="btn btn-cancel-custom mr-2"><i class="fas fa-times mr-1"></i> Batal</a>
                        <button type="submit" name="submit" class="btn btn-submit-custom"><i class="fas fa-plus-circle mr-1"></i> Tambahkan Produk</button>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script type="text/javascript">
    function updateFileLabelTambah(input) {
        var fileNames = [];
        for (var i = 0; i < input.files.length; ++i) {
            fileNames.push(input.files[i].name);
        }
        var label = input.files.length > 1 ? input.files.length + ' file dipilih' : fileNames.join(', ');
        $(input).next('.custom-file-label').html(label || 'Pilih satu atau beberapa gambar...');
    }

    $(function() {
        var dvPreview = $("#dvPreviewTambah");
        $("#fileupload").change(function() {
            dvPreview.html(""); // Clear old previews
            if (this.files && this.files.length > 0) {
                 if (typeof(FileReader) != "undefined") {
                    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png)$/i; // Case-insensitive regex
                    $(this.files).each(function(index, file) {
                        if (regex.test(file.name.toLowerCase())) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                var img = $("<img>");
                                img.attr("style", "max-height:100px; margin:5px; border:1px solid #ddd; padding:3px; border-radius:4px;");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file);
                        } else {
                             dvPreview.append('<small class="text-danger d-block p-2">File: ' + file.name + ' tidak valid.</small>');
                        }
                    });
                } else {
                    alert("Browser Anda tidak mendukung FileReader untuk preview gambar.");
                }
            } else {
                 dvPreview.html('<small class="text-muted align-self-center">Preview gambar akan muncul di sini</small>');
            }
        });
    });

    function getSubKategoriProdukTambah() {
        var id_kategori = $("#kategori_produk").val();
        var subKategoriDropdown = $("#sub_kategori_produk");
        subKategoriDropdown.html('<option value="">- Memuat...</option>');

        if (id_kategori) {
            $.ajax({
                url: "<?php echo site_url($controller_name_path . '/get_sub_kategori_by_kategori_id'); ?>", // URL yang sama dengan form edit
                type: "POST",
                data: {
                    id_kategori_produk: id_kategori,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: "json",
                success: function(data) {
                    subKategoriDropdown.html('<option value="">Pilih Sub Kategori (Jika Ada)</option>');
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            subKategoriDropdown.append('<option value="' + value.id_kategori_produk_sub + '">' + value.nama_kategori_sub + '</option>');
                        });
                    } else {
                         subKategoriDropdown.append('<option value="">Tidak ada sub kategori</option>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: " + textStatus, errorThrown);
                    subKategoriDropdown.html('<option value="">Gagal memuat</option>');
                }
            });
        } else {
            subKategoriDropdown.html('<option value="">Pilih Kategori dulu</option>');
        }
    }
</script>

</body>
</html>