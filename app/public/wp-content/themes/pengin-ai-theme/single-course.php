<?php
/**
 * コース詳細表示用テンプレート
 *
 * @package PENGIN_AI
 */

get_header();

// コースに関連するレッスン（プロンプト）を取得
$args = array(
    'post_type' => 'lesson',
    'posts_per_page' => -1,
    'meta_key' => 'lesson_order',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'parent_course',
            'value' => get_the_ID(),
            'compare' => '='
        )
    )
);
$lessons = new WP_Query($args);

// コースの情報を取得
$course_categories = get_the_terms(get_the_ID(), 'course_category');
$professions = get_the_terms(get_the_ID(), 'profession');
?>

<div class="course-single-page">
    <div class="container">
        <!-- パンくずリスト -->
        <div class="breadcrumbs">
            <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> &gt;
            <a href="<?php echo esc_url(home_url('/courses/')); ?>">コース一覧</a> &gt;
            <span><?php the_title(); ?></span>
        </div>

        <div class="course-content-wrapper">
            <div class="course-main-content">
                <!-- コースヘッダー -->
                <div class="course-header">

                    <h1 class="course-title"><?php the_title(); ?></h1>

                    <?php if (!empty($professions) && !is_wp_error($professions)) : ?>
                        <div class="course-professions">
                            <?php foreach ($professions as $profession) : ?>
                                <a href="<?php echo esc_url(get_term_link($profession)); ?>" class="course-profession">
                                    <?php echo esc_html($profession->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="course-featured-image">
                            <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- コース説明 -->
                <div class="course-description">
                    <h2 class="section-title">コース概要</h2>
                    <div class="course-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <!-- プロンプト一覧 -->
                <?php if ($lessons->have_posts()) : ?>
                    <div class="course-lessons">
                        <h2 class="section-title">プロンプト一覧</h2>
                        <div class="lesson-list">
                            <?php
                            $count = 1;
                            while ($lessons->have_posts()) : $lessons->the_post();
                            ?>
                                <div class="lesson-item">
                                    <div class="lesson-number"><?php echo esc_html($count); ?></div>
                                    <div class="lesson-info">
                                        <h3 class="lesson-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                    </div>
                                    <div class="lesson-action">
                                        <a href="<?php the_permalink(); ?>" class="btn-lesson">確認する</a>
                                    </div>
                                </div>
                            <?php
                                $count++;
                                endwhile;
                                wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="no-lessons">
                        <p>このコースにはまだプロンプトがありません。</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- サイドバー -->
            <div class="course-sidebar">
                <!-- コース情報 -->
                <div class="sidebar-widget course-info">
                    <h3 class="widget-title">コース情報</h3>
                    <div class="course-info-content">
                        <!-- プロンプト数 -->
                        <div class="info-item">
                            <div class="info-label">プロンプト数</div>
                            <div class="info-value"><?php echo $lessons->found_posts; ?>個</div>
                        </div>

                        <!-- 更新日 -->
                        <div class="info-item">
                            <div class="info-label">最終更新日</div>
                            <div class="info-value"><?php echo get_the_modified_date('Y年n月j日'); ?></div>
                        </div>

                        <!-- カテゴリー -->
                        <?php if (!empty($course_categories) && !is_wp_error($course_categories)) : ?>
                            <div class="info-item">
                                <div class="info-label">カテゴリー</div>
                                <div class="info-value">
                                    <?php
                                    $category_names = array();
                                    foreach ($course_categories as $category) {
                                        $category_names[] = $category->name;
                                    }
                                    echo implode(', ', $category_names);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 職種 -->
                        <?php if (!empty($professions) && !is_wp_error($professions)) : ?>
                            <div class="info-item">
                                <div class="info-label">対象職種</div>
                                <div class="info-value profession-tags">
                                    <?php foreach ($professions as $profession) : ?>
                                        <a href="<?php echo esc_url(get_term_link($profession)); ?>" class="profession-tag">
                                            <?php echo esc_html($profession->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 完成の参考 -->
                <?php
                // カスタムフィールドから完成例の画像を取得（ACFを使用している場合）
                $completion_example = get_field('completion_example');
                // 完成例の画像がなければ、サムネイルを使用
                if (!$completion_example) {
                    $completion_example = get_the_post_thumbnail_url(get_the_ID(), 'large');
                }

                if ($completion_example) :
                ?>
                <div class="sidebar-widget completion-example">
                    <h3 class="widget-title">完成の参考</h3>
                    <div class="completion-example-content">
                        <div class="completion-image">
                            <img src="<?php echo esc_url($completion_example); ?>" alt="完成例" class="img-fluid">
                        </div>
                        <p class="completion-caption">このコースを完了すると、上記のような成果物を作成できるようになります。</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 関連コース -->
                <?php
                // 同じカテゴリーまたは職種の関連コースを取得
                $related_args = array(
                    'post_type' => 'course',
                    'posts_per_page' => 3,
                    'post__not_in' => array(get_the_ID()),
                );

                // タクソノミー条件を設定
                $tax_query = array('relation' => 'OR');

                if (!empty($course_categories) && !is_wp_error($course_categories)) {
                    $category_ids = wp_list_pluck($course_categories, 'term_id');
                    $tax_query[] = array(
                        'taxonomy' => 'course_category',
                        'field' => 'term_id',
                        'terms' => $category_ids,
                    );
                }

                if (!empty($professions) && !is_wp_error($professions)) {
                    $profession_ids = wp_list_pluck($professions, 'term_id');
                    $tax_query[] = array(
                        'taxonomy' => 'profession',
                        'field' => 'term_id',
                        'terms' => $profession_ids,
                    );
                }

                $related_args['tax_query'] = $tax_query;
                $related_courses = new WP_Query($related_args);

                if ($related_courses->have_posts()) :
                ?>
                <div class="sidebar-widget related-courses">
                    <h3 class="widget-title">関連コース</h3>
                    <div class="related-courses-list">
                        <?php while ($related_courses->have_posts()) : $related_courses->the_post(); ?>
                            <div class="related-course-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="related-course-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="related-course-info">
                                    <h4 class="related-course-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>

                                    <?php
                                    // このコースのレッスン数を取得
                                    $lesson_count_args = array(
                                        'post_type' => 'lesson',
                                        'posts_per_page' => -1,
                                        'meta_query' => array(
                                            array(
                                                'key' => 'parent_course',
                                                'value' => get_the_ID(),
                                                'compare' => '='
                                            )
                                        )
                                    );
                                    $lesson_count_query = new WP_Query($lesson_count_args);
                                    $lesson_count = $lesson_count_query->found_posts;
                                    wp_reset_postdata();
                                    ?>

                                    <div class="related-course-meta">
                                        <span class="prompt-count">
                                            <i class="far fa-file-alt"></i> <?php echo $lesson_count; ?>個のプロンプト
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
