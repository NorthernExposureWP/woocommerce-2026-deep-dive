<?php

declare(strict_types=1);

namespace NE\ProductQuestions;

final class ProductQuestionTab
{
    public function register(): void
    {
        add_filter(
            'woocommerce_product_tabs',
            $this->addTab(...)
        );
    }

    /**
     * @param array<string, array<string, mixed>> $tabs
     * @return array<string, array<string, mixed>>
     */
    public function addTab(array $tabs): array
    {
        $tabs['questions'] = [
            'title'    => 'Questions & Answers',
            'priority' => 50,
            'callback' => $this->render(...),
        ];

        return $tabs;
    }

    public function render(): void
    {
        global $product;

        if (!$product instanceof \WC_Product) {
            return;
        }

        if (isset($_GET['question_submitted'])) {
            echo '<p>Your question has been submitted successfully.</p>';
        }

        if (
            isset($_GET['question_error']) &&
            in_array(
                $_GET['question_error'],
                [
                    'invalid_product',
                    'invalid_email',
                    'empty_question',
                    'save_failed',
                ],
                true
            )
        ) {
            echo '<p>There was a problem submitting your question.</p>';
        }

        $questions = new \WP_Query([
            'post_type'      => 'product_question',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => ProductQuestionMetaBox::META_PRODUCT_ID,
            'meta_value'     => $product->get_id(),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);

        if (!$questions->have_posts()) {
            echo '<p>No questions yet.</p>';
        }

        while ($questions->have_posts()) {
            $questions->the_post();

            $answer = (string) get_post_meta(
                get_the_ID(),
                ProductQuestionMetaBox::META_ANSWER,
                true
            );

            ?>
            <div style="margin-bottom: 24px;">
                <p>
                    <small><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></small>
                </p>
                <p>
                    <strong>Question:</strong>
                    <?php echo esc_html(get_the_content()); ?>
                </p>

                <?php if ($answer !== '') : ?>
                    <p>
                        <strong>Answer:</strong>
                        <?php echo esc_html($answer); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php
        }
        wp_reset_postdata();
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
    }
}