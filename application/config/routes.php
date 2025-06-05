<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['login'] = 'auth/index';
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// Mengarahkan produk/keranjang ke members/keranjang
$route['produk/keranjang'] = 'members/keranjang';
// Jika method keranjang di Members.php menerima parameter, tambahkan juga route untuk itu:
$route['produk/keranjang/(:any)'] = 'members/keranjang/$1';
$route['produk/keranjang/(:any)/(:any)'] = 'members/keranjang/$1/$2';
$route['produk/keranjang/(:any)/(:any)/(:any)'] = 'members/keranjang/$1/$2/$3'; // Tambahkan jika ada parameter ketiga seperti 'from_list'

// Pastikan juga route untuk aksi lain di keranjang (jika ada di Produk.php sebelumnya) diarahkan ke Members.php
$route['produk/keranjang_delete/(:any)'] = 'members/keranjang_delete/$1';
$route['produk/selesai_belanja'] = 'members/selesai_belanja';
$route['produk/batalkan_transaksi'] = 'members/batalkan_transaksi';
// Tambahkan juga untuk method AJAX ongkir jika Anda memindahkannya atau ingin diakses via /produk
$route['produk/cek_ongkir'] = 'members/cek_ongkir'; // atau nama method AJAX ongkir Anda di Members.php
