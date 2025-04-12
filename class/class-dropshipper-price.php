<?php
class Dropshipper_Price {

    // نمونه واحد از کلاس (Singleton)
    private static $instance;

    // جلوگیری از ایجاد نمونه جدید (Singleton Pattern)
    private function __construct() {
        // افزودن اکشن‌ها
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_product_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_fields'));
        add_action('woocommerce_single_product_summary', array($this, 'display_dropship_profit'), 20);
        add_shortcode('dropship_prices', array($this, 'dropship_price_shortcode'));
    }

    // دسترسی به نمونه واحد افزونه
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // افزودن فیلدهای سفارشی
    public function add_custom_product_fields() {
        woocommerce_wp_text_input( array(
            'id' => '_dropship_price',
            'label' => __('Dropship Price', 'simple-plugin'),
            'description' => __('Enter the dropship price for the product.', 'simple-plugin'),
            'desc_tip' => true,
            'type' => 'number',
            'custom_attributes' => array( 'step' => 'any' ),
        ));

        woocommerce_wp_text_input( array(
            'id' => '_regular_price',
            'label' => __('Regular Price', 'simple-plugin'),
            'description' => __('Enter the regular price of the product.', 'simple-plugin'),
            'desc_tip' => true,
            'type' => 'number',
            'custom_attributes' => array( 'step' => 'any' ),
        ));
    }

    // ذخیره‌سازی فیلدهای سفارشی و بررسی اعتبار داده‌ها
    public function save_custom_product_fields($post_id) {
        $dropship_price = isset($_POST['_dropship_price']) ? sanitize_text_field($_POST['_dropship_price']) : '';
        $regular_price = isset($_POST['_regular_price']) ? sanitize_text_field($_POST['_regular_price']) : '';

        // بررسی اعتبار قیمت‌ها
        if (!empty($dropship_price) && !empty($regular_price) && $regular_price <= $dropship_price) {
            wc_add_notice(__('Regular price must be greater than dropship price.', 'simple-plugin'), 'error');
            return;
        }

        // ذخیره‌سازی قیمت‌ها
        update_post_meta($post_id, '_dropship_price', $dropship_price);
        update_post_meta($post_id, '_regular_price', $regular_price);

        // محاسبه و ذخیره قیمت نهایی (قیمت اصلی - قیمت دراپ‌شیپر)
        if (!empty($regular_price) && !empty($dropship_price)) {
            $final_price = $regular_price - $dropship_price;
            update_post_meta($post_id, '_final_price', $final_price);
        }
    }

    // نمایش سود دراپ‌شیپر در صفحه محصول
    public function display_dropship_profit() {
        global $product;

        // دریافت قیمت نهایی از متا دیتا
        $final_price = get_post_meta($product->get_id(), '_final_price', true);

        if (!empty($final_price)) {
            $dropship_profit = $final_price; // سود دراپ‌شیپر
            echo '<p>' . __('Dropship Profit: ', 'simple-plugin') . wc_price($dropship_profit) . '</p>';
        }
    }

    // شورتکد برای نمایش قیمت‌ها
    public function dropship_price_shortcode() {
        global $product;

        $dropship_price = get_post_meta($product->get_id(), '_dropship_price', true);
        $regular_price = get_post_meta($product->get_id(), '_regular_price', true);

        if (!empty($dropship_price) && !empty($regular_price)) {
            return '<p>' . __('Dropship Price: ', 'simple-plugin') . wc_price($dropship_price) . '</p>'
                   . '<p>' . __('Regular Price: ', 'simple-plugin') . wc_price($regular_price) . '</p>';
        }
    }
}
?>
