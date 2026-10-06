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

defined('ABSPATH') || exit;

require_once __DIR__ . '/src/ProductQuestionPostType.php';
require_once __DIR__ . '/src/ProductQuestionMetaBox.php';
require_once __DIR__ . '/src/ProductQuestionColumns.php';

$postType = new ProductQuestionPostType();
$metaBox = new ProductQuestionMetaBox();
$columns = new ProductQuestionColumns();


add_action('init', $postType->register(...));
add_action('add_meta_boxes', $metaBox->register(...));
add_action('admin_init', $columns->register(...));

add_filter('woocommerce_product_tabs', function (array $tabs): array {
    $tabs['questions'] = [
        'title'    => 'Questions & Answers',
        'priority' => 50,
        'callback' => function (): void {
            echo '<p>Sample question?</p>';
            echo '<p><strong>Sample answer.</strong></p>';
        },
    ];

    return $tabs;
});