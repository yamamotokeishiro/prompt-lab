<?php
/**
 * コンテンツカードのテンプレート
 *
 * @package PENGIN_AI
 */

// 親レッスン情報の取得
$parent_lesson_id = get_field('parent_lesson');
if (is_array($parent_lesson_id)) {
    $parent_lesson_id = isset($parent_lesson_id[0]) ? $parent_lesson_id[0] : null;
}
$parent_lesson = $parent_lesson_id ? get_post($parent_lesson_id) : null;

// タグの取得
$tags = get_the_terms(get_the_ID(), 'content_tag');
$professions = get_the_terms(get_the_ID(), 'profession');
?>

<div class="col-md-6 col-lg-4 mb-4">
    <div class="content-card">
        <div class="card-image">
            <?php if (has_post_thumbnail()): ?>
                <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
            <?php else: ?>
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/default-content.jpg" alt="<?php the_title(); ?>">
            <?php endif; ?>
            <div class="card-image-overlay"></div>
        </div>

        <div class="card-body">
            <h3 class="card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <?php if ($parent_lesson): ?>
                <div class="lesson-info">
                    <i class="fas fa-book"></i>
                    <span>レッスン: <a href="<?php echo get_permalink($parent_lesson->ID); ?>"><?php echo get_the_title($parent_lesson->ID); ?></a></span>
                </div>
            <?php endif; ?>

            <div class="card-excerpt">
                <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
            </div>

            <?php if (!empty($tags) && !is_wp_error($tags)): ?>
                <div class="content-tags">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?php echo esc_url(add_query_arg('content_tag', $tag->slug, get_permalink(get_page_by_path('search')))); ?>" class="tag">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm mt-2">
                詳細を見る <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
