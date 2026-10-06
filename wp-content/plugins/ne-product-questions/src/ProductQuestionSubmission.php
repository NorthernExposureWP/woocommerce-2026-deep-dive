<?php

declare(strict_types=1);

namespace NE\ProductQuestions;

final class ProductQuestionSubmission
{
    public function register(): void
    {
        add_action('template_redirect', $this->handle(...));
    }

    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (! isset($_POST['product_question_nonce'])) {
            return;
        }

        if (! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['product_question_nonce'])
            ),
            'submit_product_question'
        )) {
            return;
        }

        $productId = isset($_POST['product_question_product_id'])
            ? absint($_POST['product_question_product_id'])
            : 0;

        $email = isset($_POST['product_question_email'])
            ? sanitize_email(
                wp_unslash($_POST['product_question_email'])
            )
            : '';

        $phone = isset($_POST['product_question_phone'])
            ? sanitize_text_field(
                wp_unslash($_POST['product_question_phone'])
            )
            : '';

        $question = isset($_POST['product_question_text'])
            ? sanitize_textarea_field(
                wp_unslash($_POST['product_question_text'])
            )
            : '';

        if ($productId === 0) {
            return;
        }

        if (! wc_get_product($productId)) {
            return;
        }

        if (! is_email($email)) {
            return;
        }

        if ($question === '') {
            return;
        }

        $postId = wp_insert_post([
            'post_type'    => 'product_question',
            'post_status'  => 'draft',
            'post_content' => $question,
        ], true);

        if (is_wp_error($postId)) {
            return;
        }

        update_post_meta(
            $postId,
            ProductQuestionMetaBox::META_PRODUCT_ID,
            $productId
        );

        update_post_meta(
            $postId,
            ProductQuestionMetaBox::META_EMAIL,
            $email
        );

        update_post_meta(
            $postId,
            ProductQuestionMetaBox::META_PHONE,
            $phone
        );

        wp_safe_redirect(
            wp_get_referer() ?: get_permalink($productId)
        );

        exit;
    }
}