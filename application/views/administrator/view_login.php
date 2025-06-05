<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Ambil data default jika ada (misalnya nama website dari controller)
$website_name_login = isset($website_name) ? $website_name : (isset($iden['nama_website']) ? $iden['nama_website'] : 'Admin Panel');
$title_page_login = isset($title) ? $title : 'Administrator Login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($title_page_login); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --background-color: #f4f7f6;
            --card-background: #ffffff;
            --input-border-color: #ced4da;
            --input-focus-border-color: #80bdff;
            --text-color: #495057;
            --heading-color: #343a40;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
        }

        .login-container {
            width: 100%;
            max-width: 400px; /* Sedikit lebih ramping */
            padding: 20px;
        }

        .login-card {
            background-color: var(--card-background);
            padding: 30px 35px; /* Padding disesuaikan */
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border: none;
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header img.logo {
            max-width: 90px;
            margin-bottom: 12px;
        }

        .login-header h2 {
            font-weight: 600;
            color: var(--heading-color);
            font-size: 1.7rem; /* Sedikit lebih kecil */
        }
        .login-header p {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--input-border-color);
            padding: 0.80rem 1rem; /* Padding input */
            height: auto;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--input-focus-border-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .input-group-text {
            background-color: #e9ecef;
            border-radius: 8px 0 0 8px;
            border: 1px solid var(--input-border-color);
            border-right:0;
        }
        .input-group-prepend + .form-control {
            border-left: 0;
            border-radius: 0 8px 8px 0;
        }


        .btn-login {
            background-color: var(--primary-color);
            border: none;
            padding: 0.80rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }
        .btn-login:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.5);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
        }
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <?php
                // Anda bisa menambahkan logo di sini jika ada
                // Contoh: <img src="<?php echo base_url('asset/logo_admin.png'); 
                ?>
                <h2>Selamat Datang!</h2>
                <p>Login ke <?php echo htmlspecialchars($website_name_login); ?></p>
            </div>

            <?php
            // Menampilkan pesan error/sukses dari flashdata
            if ($this->session->flashdata('message')) {
                echo $this->session->flashdata('message');
            }
            ?>

            <?php echo form_open('administrator', array('role' => 'form', 'id' => 'loginForm')); // Action mengarah ke administrator/index ?>
                <div class="form-group mb-3">
                    <label for="username" class="sr-only">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="a" id="username" placeholder="Username" value="<?php echo set_value('a'); ?>" required autofocus>
                    </div>
                    <?php echo form_error('a', '<small class="text-danger pt-1 d-block">', '</small>'); ?>
                </div>

                <div class="form-group mb-4"> <label for="password" class="sr-only">Password</label>
                    <div class="input-group">
                         <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="b" id="password" placeholder="Password" required>
                    </div>
                     <?php echo form_error('b', '<small class="text-danger pt-1 d-block">', '</small>'); ?>
                </div>

                <button type="submit" name="submit" class="btn btn-login btn-block">Masuk</button>
            <?php echo form_close(); ?>

            <div class="login-footer">
                <?php if (isset($iden['url']) && $iden['url']): // Asumsi $iden dikirim dari controller ?>
                <a href="<?php echo prep_url($iden['url']); ?>" target="_blank">
                    <i class="fas fa-globe mr-1"></i> Kunjungi Website
                </a>
                <?php else: ?>
                <a href="<?php echo base_url(); ?>">
                     <i class="fas fa-globe mr-1"></i> Kunjungi Website
                </a>
                <?php endif; ?>
                <?php
                // Link lupa password jika ada fiturnya
                // echo '<br><a href="'.site_url('administrator/lupapassword').'">Lupa password?</a>';
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fokus otomatis ke field username saat halaman dimuat
            $('#username').focus();

            // Script untuk auto-hide alert (opsional)
            window.setTimeout(function() {
                $(".alert:not(.alert-dismissible)").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 5000);
             // Untuk alert yang bisa ditutup manual
            $(".alert-dismissible").fadeTo(20000, 500).slideUp(500, function(){
                $(this).remove();
            });
        });
    </script>
</body>
</html>