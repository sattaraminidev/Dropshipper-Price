<?php
/*
Plugin Name: DropShipper-Price-Calculator
Plugin URI: https://github.com/sattaraminidev
Description: این افزونه برای محاسبه و نمایش قیمت و سود دراپ شیپر در صفحه محصولات است .
Version: 1.0
Author: Sattar Amini
Author URI: https://github.com/sattaraminidev
*/

// جلوگیری از دسترسی مستقیم به فایل
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// بارگذاری کلاس‌ها
require_once plugin_dir_path( __FILE__ ) . 'includes/class-dropshipper-price.php';

// فعال‌سازی افزونه
function activate_dropshipper_price() {
    Dropshipper_Price::get_instance();
}
register_activation_hook( __FILE__, 'activate_dropshipper_price' );

// غیرفعال‌سازی افزونه
function deactivate_dropshipper_price() {
    // Cleanup actions if necessary
}
register_deactivation_hook( __FILE__, 'deactivate_dropshipper_price' );

// اجرای افزونه
Dropshipper_Price::get_instance();
?>
