<?php

/**
 * Plugin Name: NE Categories Lab
 * Description: Educational plugin for WooCommerce Categories CRUD API.
 * Version: 1.0.0
 * Requires PHP: 8.4
 * Requires Plugins: woocommerce
 * Text Domain: ne-categoreies-lab
 */
declare(strict_types=1);

use NE\ProductQuestions\ProductQuestionColumns;
use NE\ProductQuestions\ProductQuestionPostType;
use NE\ProductQuestions\ProductQuestionMetaBox;
use NE\ProductQuestions\ProductQuestionSubmission;
use NE\ProductQuestions\ProductQuestionTab;

defined('ABSPATH') || exit;

require_once __DIR__ . '/src/ProductQuestionPostType.php';
require_once __DIR__ . '/src/ProductQuestionMetaBox.php';
require_once __DIR__ . '/src/ProductQuestionColumns.php';
require_once __DIR__ . '/src/ProductQuestionSubmission.php';
require_once __DIR__ . '/src/ProductQuestionTab.php';


$postType = new ProductQuestionPostType();
$metaBox = new ProductQuestionMetaBox();
$columns = new ProductQuestionColumns();
$submission = new ProductQuestionSubmission();
$tab = new ProductQuestionTab();

add_action('init', $postType->register(...));
add_action('admin_init', $columns->register(...));
add_action('add_meta_boxes', $metaBox->register(...));
add_action('save_post_product_question', $metaBox->save(...), 10, 1);
add_action('template_redirect', $submission->handle(...));
add_action('init', $tab->register(...));

add_action('wp_enqueue_scripts', function (): void {
    if (!is_product()) {
        return;
    }

    wp_enqueue_script(
        'ne-product-questions-tabs',
        plugins_url(
            'assets/questions-tabs.js',
            __FILE__
        ),
        [],
        '1.0.0',
        [
            'in_footer' => true,
        ]
    );
});