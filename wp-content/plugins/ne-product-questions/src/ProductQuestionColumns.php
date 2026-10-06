<?php

declare(strict_types=1);

namespace NE\ProductQuestions;

final class ProductQuestionColumns
{
    public function register(): void
    {
        add_filter(
            'manage_product_question_posts_columns',
            $this->addColumns(...)
        );

        add_action(
            'manage_product_question_posts_custom_column',
            $this->renderColumn(...),
            10,
            2
        );
    }

    /**
     * @param array<string, string> $columns
     * @return array<string, string>
     */
    public function addColumns(array $columns): array
    {
        $columns['product'] = 'Product';
        $columns['question_email'] = 'Email';

        return $columns;
    }

    public function renderColumn(string $column, int $postId): void
    {
        switch ($column) {
            case 'product':
                $productId = (int) get_post_meta(
                    $postId,
                    ProductQuestionMetaBox::META_PRODUCT_ID,
                    true
                );

                if ($productId === 0) {
                    echo '—';
                    return;
                }

                $product = wc_get_product($productId);

                if (! $product) {
                    echo '—';
                    return;
                }

                printf(
                    '<a href="%s">%s</a>',
                    esc_url(get_edit_post_link($productId)),
                    esc_html($product->get_name())
                );

                break;

            case 'question_email':
                $email = (string) get_post_meta(
                    $postId,
                    ProductQuestionMetaBox::META_EMAIL,
                    true
                );

                echo esc_html($email ?: '—');

                break;
        }
    }
}