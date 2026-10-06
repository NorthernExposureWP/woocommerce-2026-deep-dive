<?php

declare(strict_types=1);

namespace NE\ProductQuestions;

final class ProductQuestionPostType
{
    public function register(): void
    {
        register_post_type('product_question', [
            'labels' => [
                'name' => 'Product Questions',
                'singular_name' => 'Product Question',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => [
                'title',
                'editor',
                'author',
            ],
        ]);
    }
}