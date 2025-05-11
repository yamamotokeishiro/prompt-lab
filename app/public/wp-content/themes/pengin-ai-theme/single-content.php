<?php
/**
 * コンテンツ詳細表示用テンプレート
 *
 * @package PENGIN_AI
 */

get_header();

// 親レッスン情報の取得 - 配列対応版
$parent_lesson_id = get_field('parent_lesson');

// 配列の場合は最初の要素を使用
if (is_array($parent_lesson_id)) {
    $parent_lesson_id = isset($parent_lesson_id[0]) ? $parent_lesson_id[0] : null;
}

$parent_lesson = $parent_lesson_id ? get_post($parent_lesson_id) : null;

// 親コース情報の取得
$parent_course_id = $parent_lesson ? get_field('parent_course', $parent_lesson_id) : null;

// 親コースも配列の場合は最初の要素を使用
if (is_array($parent_course_id)) {
    $parent_course_id = isset($parent_course_id[0]) ? $parent_course_id[0] : null;
}

$parent_course = $parent_course_id ? get_post($parent_course_id) : null;


// 現在のコンテンツID
$current_content_id = get_the_ID();
?>


<div class="content-single-page">
    <div class="container">
        <!-- パンくずリスト -->
        <div class="breadcrumbs">
            <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> &gt;
            <?php if ($parent_course): ?>
                <a href="<?php echo esc_url(get_permalink($parent_course->ID)); ?>"><?php echo esc_html($parent_course->post_title); ?></a> &gt;
            <?php endif; ?>
            <?php if ($parent_lesson): ?>
                <a href="<?php echo esc_url(get_permalink($parent_lesson->ID)); ?>"><?php echo esc_html($parent_lesson->post_title); ?></a> &gt;
            <?php endif; ?>
            <span><?php the_title(); ?></span>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- コンテンツ本文 -->
                <article id="post-<?php the_ID(); ?>" <?php post_class('content-main'); ?>>
                    <!-- コンテンツヘッダー -->
                    <header class="content-header">
                        <h1 class="content-title"><?php the_title(); ?></h1>

                        <?php if (has_post_thumbnail()): ?>
                            <div class="content-featured-image">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <!-- コンテンツ本文 -->
                    <div class="content-body">
                        <?php
                        // 本文の表示
                        the_content();

                        // ページネーション（コンテンツが複数ページに分かれている場合）
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('ページ:', 'pengin-ai'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                </article>
            </div>

            <div class="col-lg-4">
                <!-- サイドバー -->
                <div class="content-sidebar">
                    <!-- コンテンツナビゲーション -->
                    <div class="widget content-navigation">
                        <h3 class="widget-title">コンテンツナビゲーション</h3>

                        <?php if ($parent_lesson_id): ?>
                            <?php
                            // 同じレッスン内のコンテンツを取得
                            $args = array(
                                'post_type' => 'content',
                                'posts_per_page' => -1,
                                'meta_key' => 'content_order',
                                'orderby' => 'meta_value_num',
                                'order' => 'ASC',
                                'meta_query' => array(
                                    array(
                                        'key' => 'parent_lesson',
                                        'value' => $parent_lesson_id,
                                        'compare' => '='
                                    )
                                )
                            );
                            $content_query = new WP_Query($args);

                            if ($content_query->have_posts()) :
                                echo '<ul class="content-nav">';
                                $found_current = false;
                                $prev_content = null;
                                $next_content = null;

                                // 前後のコンテンツを特定
                                while ($content_query->have_posts()) : $content_query->the_post();
                                    if ($found_current && !$next_content) {
                                        $next_content = array(
                                            'id' => get_the_ID(),
                                            'title' => get_the_title(),
                                            'permalink' => get_permalink()
                                        );
                                    }

                                    if (get_the_ID() == $current_content_id) {
                                        $found_current = true;
                                    } else if (!$found_current) {
                                        $prev_content = array(
                                            'id' => get_the_ID(),
                                            'title' => get_the_title(),
                                            'permalink' => get_permalink()
                                        );
                                    }
                                endwhile;

                                // ナビゲーションを表示
                                wp_reset_postdata();
                                $content_query->rewind_posts();

                                while ($content_query->have_posts()) : $content_query->the_post();
                                    $is_current = get_the_ID() == $current_content_id;
                                    echo '<li class="' . ($is_current ? 'current' : '') . '">';
                                    echo '<a href="' . get_permalink() . '">';
                                    echo get_the_title();
                                    echo '</a>';
                                    echo '</li>';
                                endwhile;
                                echo '</ul>';
                                wp_reset_postdata();

                                // 前後のコンテンツへのリンク
                                if ($prev_content || $next_content): ?>
                                    <div class="prev-next-navigation">
                                        <?php if ($prev_content): ?>
                                            <a href="<?php echo esc_url($prev_content['permalink']); ?>" class="prev-content">
                                                <i class="fas fa-arrow-left"></i> 前のコンテンツ
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($next_content): ?>
                                            <a href="<?php echo esc_url($next_content['permalink']); ?>" class="next-content">
                                                次のコンテンツ <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif;
                            endif;
                            ?>
                        <?php endif; ?>

                        <div class="back-navigation">
                            <?php if ($parent_lesson): ?>
                                <a href="<?php echo esc_url(get_permalink($parent_lesson->ID)); ?>" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> レッスンに戻る
                                </a>
                            <?php endif; ?>

                            <?php if ($parent_course): ?>
                                <a href="<?php echo esc_url(get_permalink($parent_course->ID)); ?>" class="btn btn-outline-secondary btn-block mt-3">
                                    <i class="fas fa-book"></i> コースに戻る
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
