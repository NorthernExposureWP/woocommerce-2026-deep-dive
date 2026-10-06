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

defined('ABSPATH') || exit;

require_once __DIR__ . '/src/ProductQuestionPostType.php';
require_once __DIR__ . '/src/ProductQuestionMetaBox.php';
require_once __DIR__ . '/src/ProductQuestionColumns.php';
require_once __DIR__ . '/src/ProductQuestionSubmission.php';

$postType = new ProductQuestionPostType();
$metaBox = new ProductQuestionMetaBox();
$columns = new ProductQuestionColumns();
$submission = new ProductQuestionSubmission();

add_action('init', $postType->register(...));
add_action('admin_init', $columns->register(...));
add_action('add_meta_boxes', $metaBox->register(...));
add_action('save_post_product_question', $metaBox->save(...), 10, 1);
add_action('template_redirect', $submission->handle(...));

add_filter('woocommerce_product_tabs', function (array $tabs): array {
    $tabs['questions'] = [
        'title'    => 'Questions & Answers',
        'priority' => 50,
        'callback' => function (): void {
            global $product;

            if (! $product instanceof \WC_Product) {
                return;
            }

            ?>
            <h3>Ask your Question</h3>

            <form method="post">
                <p>
                    <label for="product-question-email">
                        Email *
                    </label>
                    <br>
                    <input
                        type="email"
                        id="product-question-email"
                        name="product_question_email"
                        required
                    >
                </p>

                <p>
                    <label for="product-question-phone">
                        Phone
                    </label>
                    <br>
                    <input
                        type="text"
                        id="product-question-phone"
                        name="product_question_phone"
                    >
                </p>

                <p>
                    <label for="product-question-text">
                        Question *
                    </label>
                    <br>
                    <textarea
                        id="product-question-text"
                        name="product_question_text"
                        rows="5"
                        required
                    ></textarea>
                </p>

                <?php wp_nonce_field(
                    'submit_product_question',
                    'product_question_nonce'
                ); ?>

                <input
                    type="hidden"
                    name="product_question_product_id"
                    value="<?php echo esc_attr((string) $product->get_id()); ?>"
                >

                <button type="submit">
                    Ask Question
                </button>
            </form>
            <?php
        },
    ];

    return $tabs;
});