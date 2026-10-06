<?php

declare(strict_types=1);

namespace NE\ProductQuestions;

final class ProductQuestionMetaBox
{
    public const META_PRODUCT_ID = '_product_id';
    public const META_EMAIL = '_question_email';
    public const META_PHONE = '_question_phone';
    public const META_ANSWER = '_question_answer';

    public const NONCE_ACTION = 'save_product_question';
    public const NONCE_NAME = 'product_question_nonce';

    public function register(): void
    {
        add_meta_box(
            'product_question_details',
            'Question Details',
            $this->render(...),
            'product_question',
            'normal',
            'high',
        );

        add_action('save_post_product_question', $this->save(...));
    }

    public function render(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $productId = (int) get_post_meta(
            $post->ID,
            self::META_PRODUCT_ID,
            true
        );

        $email = (string) get_post_meta(
            $post->ID,
            self::META_EMAIL,
            true
        );

        $phone = (string) get_post_meta(
            $post->ID,
            self::META_PHONE,
            true
        );

        $answer = (string) get_post_meta(
            $post->ID,
            self::META_ANSWER,
            true
        );

        $products = wc_get_products([
            'status' => 'publish',
            'limit'  => -1,
            'orderby' => 'name',
            'order'   => 'ASC',
        ]);
        ?>

        <p>
            <label for="product_question_product_id">
                <strong>Product</strong>
            </label>
        </p>

        <select
            id="product_question_product_id"
            name="product_question_product_id"
            style="width: 100%;"
        >
            <option value="">— Select product —</option>

            <?php foreach ($products as $product) : ?>
                <option
                    value="<?php echo esc_attr((string) $product->get_id()); ?>"
                    <?php selected($productId, $product->get_id()); ?>
                >
                    <?php echo esc_html($product->get_name()); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>
            <label for="product_question_email">
                <strong>Email</strong>
            </label>
        </p>

        <input
            type="email"
            id="product_question_email"
            name="product_question_email"
            value="<?php echo esc_attr($email); ?>"
            style="width: 100%;"
        >

        <p>
            <label for="product_question_phone">
                <strong>Phone</strong>
            </label>
        </p>

        <input
            type="text"
            id="product_question_phone"
            name="product_question_phone"
            value="<?php echo esc_attr($phone); ?>"
            style="width: 100%;"
        >

        <p>
            <label for="product_question_answer">
                <strong>Answer</strong>
            </label>
        </p>

        <textarea
            id="product_question_answer"
            name="product_question_answer"
            rows="6"
            style="width: 100%;"
        ><?php echo esc_textarea($answer); ?></textarea>

        <?php
    }

    public function save(int $postId): void
    {
        if (
            ! isset($_POST[self::NONCE_NAME]) ||
            ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])),
                self::NONCE_ACTION
            )
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $postId)) {
            return;
        }

        $productId = isset($_POST['product_question_product_id'])
            ? absint($_POST['product_question_product_id'])
            : 0;

        $email = isset($_POST['product_question_email'])
            ? sanitize_email(wp_unslash($_POST['product_question_email']))
            : '';

        $phone = isset($_POST['product_question_phone'])
            ? sanitize_text_field(wp_unslash($_POST['product_question_phone']))
            : '';

        $answer = isset($_POST['product_question_answer'])
            ? sanitize_textarea_field(wp_unslash($_POST['product_question_answer']))
            : '';

        update_post_meta($postId, self::META_PRODUCT_ID, $productId);
        update_post_meta($postId, self::META_EMAIL, $email);
        update_post_meta($postId, self::META_PHONE, $phone);
        update_post_meta($postId, self::META_ANSWER, $answer);
    }
}