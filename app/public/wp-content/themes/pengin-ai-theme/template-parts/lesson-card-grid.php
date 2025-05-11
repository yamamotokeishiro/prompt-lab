<?php
/**
 * グリッド表示用レッスンカードのテンプレート
 *
 * @package PENGIN_AI
 */

// 親コース情報の取得
$parent_course_id = get_field('parent_course');
if (is_array($parent_course_id)) {
    $parent_course_id = isset($parent_course_id[0]) ? $parent_course_id[0] : null;
}
$parent_course = $parent_course_id ? get_post($parent_course_id) : null;

// タグと職種の取得
$tags = get_the_terms(get_the_ID(), 'lesson_tag');
$professions = get_the_terms(get_the_ID(), 'profession');
?>

<div class="col-md-6 col-lg-4 mb-4">
    <div class="content-card">
        <a href="<?php the_permalink(); ?>" class="card-link">
            <div class="card-image">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('medium', array('class' => 'card-img')); ?>
                <?php else: ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/default-lesson.jpg" alt="<?php the_title(); ?>" class="card-img">
                <?php endif; ?>

                <?php if (!empty($professions) && !is_wp_error($professions)): ?>
                    <div class="profession-badge">
                        <?php echo esc_html($professions[0]->name); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <h3 class="card-title"><?php the_title(); ?></h3>

                <div class="card-excerpt">
                    <?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?>
                </div>

                <div class="card-meta">
                    <?php if (!empty($tags) && !is_wp_error($tags)): ?>
                        <div class="meta-tags">
                            <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                <span class="meta-tag"><?php echo esc_html($tag->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="meta-date">
                        <i class="far fa-calendar-alt"></i> <?php echo get_the_date('Y.m.d'); ?>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
