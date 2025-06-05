<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$website_name_login_member = isset($website_name) ? $website_name : (isset($iden['nama_website']) ? htmlspecialchars($iden['nama_website']) : 'Portal Member');
$title_page_login_member = isset($title) ? htmlspecialchars($title) : 'Login Member ';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($title_page_login_member); ?> - <?php echo htmlspecialchars($website_name_login_member); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --member-primary-color: #28a745;
            --member-secondary-color: #6c757d;
            --member-background-color: #f0fdf4;
            --member-card-background: #ffffff;
            --member-input-border-color: #ced4da;
            --member-input-focus-border-color: #5cb85c;
            --member-text-color: #495057;
            --member-heading-color: #343a40;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0; /* Tambahkan padding: 0 untuk mengatasi default browser */
        }

        body {
            display: flex;
            flex-direction: column; /* Memungkinkan footer tetap di bawah jika konten pendek */
            align-items: center;   /* Pusatkan secara horizontal */
            justify-content: center; /* Pusatkan secara vertikal */
            min-height: 100vh;     /* Pastikan body setidaknya setinggi viewport */
            background-color: var(--member-background-color);
            font-family: 'Poppins', sans-serif;
            color: var(--member-text-color);
            padding: 15px; /* Beri sedikit padding pada body untuk layar kecil */
            box-sizing: border-box; /* Agar padding tidak menambah ukuran total */
        }

        .login-container-member {
            width: 100%;
            max-width: 400px; /* Sedikit diperkecil agar lebih fokus */
            /* Hapus padding di sini jika sudah ada di body atau biarkan jika diperlukan */
            /* padding: 20px; */
            margin: auto; /* Tambahan untuk memastikan centering, meski flex seharusnya sudah cukup */
        }

        .login-card-member {
            background-color: var(--member-card-background);
            padding: 30px 35px; /* Padding internal kartu */
            border-radius: 12px; /* Sedikit lebih lembut */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Bayangan lebih halus */
            border: none;
            width: 100%; /* Pastikan kartu mengisi container */
        }

        .login-header-member {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header-member img.logo {
            max-width: 80px;
            margin-bottom: 15px;
        }

        .login-header-member h2 {
            font-weight: 600;
            color: var(--member-heading-color);
            font-size: 1.7rem; /* Sedikit disesuaikan */
        }
        .login-header-member p {
            color: var(--member-secondary-color);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--member-input-border-color);
            padding: 0.80rem 1rem;
            height: auto;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--member-input-focus-border-color);
            box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25);
        }
        .input-group-text {
            background-color: #f0f1f3;
            border-radius: 8px 0 0 8px;
            border: 1px solid var(--member-input-border-color);
            border-right:0;
            color: var(--member-secondary-color);
        }
        .input-group-prepend + .form-control {
            border-left: 0;
            border-radius: 0 8px 8px 0;
        }

        .btn-login-member-submit {
            background-color: var(--member-primary-color);
            border: none;
            padding: 0.80rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            width: 100%;
            color: white;
            box-shadow: 0 3px 8px rgba(40,167,69,.25);
        }

        .btn-login-member-submit:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(40,167,69,.3);
            color: white;
        }
         .btn-login-member-submit:focus {
            box-shadow: 0 0 0 0.2rem rgba(40,167,69,.5);
        }

        .login-footer-member {
            text-align: center;
            margin-top: 20px; /* Sedikit dikurangi */
            font-size: 0.875rem; /* Sedikit diperkecil */
        }
        .login-footer-member a {
            color: var(--member-primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .login-footer-member a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.875rem;
        }
         .back-to-home-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.85em;
            color: var(--member-secondary-color);
        }
        .back-to-home-link:hover {
            color: var(--member-primary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-container-member"> <div class="login-card-member">
            <div class="login-header-member">
                <h2>Login Akun Anda</h2>
                <p>Selamat datang kembali!</p>
            </div>

            <?php
            if ($this->session->flashdata('message_login_member')) {
                echo $this->session->flashdata('message_login_member');
            } elseif ($this->session->flashdata('message')) {
                 echo $this->session->flashdata('message');
            }
            if (validation_errors()) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . strip_tags(validation_errors()) . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
            ?>

            <?php echo form_open('auth/login_member', array('role' => 'form', 'id' => 'loginMemberForm')); ?>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo set_value('username'); ?>" required autofocus>
                    </div>
                </div>

                <div class="form-group mb-3"> <div class="input-group">
                         <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    </div>
                </div>
                
                <div class="form-group text-right mb-3" style="font-size: 0.85rem;">
                     <a href="<?php echo site_url('auth/lupapassword'); ?>">Lupa Password?</a>
                </div>

                <button type="submit" name="submit" class="btn btn-login-member-submit btn-block">LOGIN</button>
            <?php echo form_close(); ?>

            <div class="login-footer-member">
                Belum punya akun? <a href="<?php echo site_url('auth/register'); ?>">Daftar Sekarang</a>
            </div>

        </div>
        <a href="<?php echo base_url(); ?>" class="back-to-home-link"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#username').focus();
            // Script auto-hide alert bisa tetap di sini jika diinginkan
        });
    </script>
</body>
</html>