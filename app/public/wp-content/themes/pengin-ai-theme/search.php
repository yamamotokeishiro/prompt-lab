<?php
/**
 * 検索結果ページのテンプレート
 *
 * @package PENGIN_AI
 */

get_header();

// 検索クエリを取得
$search_query = get_search_query();
?>

<div class="search-results-page">
    <div class="container">
        <!-- 検索ヘッダー -->
        <div class="search-header">
            <h1 class="search-title">
                <?php if (!empty($search_query)) : ?>
                    「<?php echo esc_html($search_query); ?>」の検索結果
                <?php else : ?>
                    検索結果
                <?php endif; ?>
            </h1>

            <!-- 検索フォーム -->
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="main-search-form">
                <div class="search-input-container">
                    <input type="text" name="s" placeholder="キーワードを入力" value="<?php echo esc_attr($search_query); ?>" class="main-search-input">
                    <button type="submit" class="main-search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <?php
        // コースの検索結果
        $course_args = array(
            'post_type' => 'course',
            's' => $search_query,
            'posts_per_page' => 6,
        );
        $course_query = new WP_Query($course_args);

        // プロンプトの検索結果
        $prompt_args = array(
            'post_type' => 'lesson', // レッスン（プロンプト）
            's' => $search_query,
            'posts_per_page' => 9,
        );
        $prompt_query = new WP_Query($prompt_args);

        // 検索結果の総数
        $total_results = $course_query->found_posts + $prompt_query->found_posts;
        ?>

        <!-- 検索結果メタ情報 -->
        <div class="search-meta">
            <?php if ($total_results > 0) : ?>
                <p class="results-count"><?php echo number_format_i18n($total_results); ?>件の検索結果が見つかりました</p>
            <?php endif; ?>
        </div>

        <!-- メインコンテンツ -->
        <div class="search-content">
            <?php if ($course_query->have_posts() || $prompt_query->have_posts()) : ?>

                <!-- コース検索結果 -->
                <?php if ($course_query->have_posts()) : ?>
                <section class="search-section course-results">
                    <div class="section-header">
                        <h2 class="section-title">コース</h2>
                        <?php if ($course_query->found_posts > 6) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('s' => $search_query, 'post_type' => 'course'), home_url('/'))); ?>" class="view-all-link">すべて見る (<?php echo number_format_i18n($course_query->found_posts); ?>)</a>
                        <?php endif; ?>
                    </div>

                    <div class="course-cards">
                        <?php while ($course_query->have_posts()) : $course_query->the_post(); ?>
                            <div class="course-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="course-card-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="course-card-content">
                                    <div class="course-card-meta">
                                        <?php
                                        // コースカテゴリーを取得
                                        $course_categories = get_the_terms(get_the_ID(), 'course_category');
                                        if ($course_categories && !is_wp_error($course_categories)) :
                                            $course_category = reset($course_categories);
                                        ?>
                                            <span class="course-category"><?php echo esc_html($course_category->name); ?></span>
                                        <?php endif; ?>

                                        <?php
                                        // 職種を取得
                                        $professions = get_the_terms(get_the_ID(), 'profession');
                                        if ($professions && !is_wp_error($professions)) :
                                            $profession = reset($professions);
                                        ?>
                                            <span class="course-profession"><?php echo esc_html($profession->name); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="course-card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <div class="course-card-excerpt">
                                        <?php
                                        // 60文字以内に制限
                                        $excerpt = get_the_excerpt();
                                        echo mb_substr($excerpt, 0, 60) . (mb_strlen($excerpt) > 60 ? '...' : '');
                                        ?>
                                    </div>

                                    <!-- コースの詳細情報（レッスン数など） -->
                                    <?php
                                    // このコースに属するレッスン（プロンプト）の数を取得
                                    $lessons_args = array(
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
                                    $lessons_query = new WP_Query($lessons_args);
                                    $lessons_count = $lessons_query->found_posts;
                                    wp_reset_postdata();
                                    ?>

                                    <div class="course-card-footer">
                                        <div class="course-stats">
                                            <span class="course-lessons-count">
                                                <i class="far fa-file-alt"></i> <?php echo number_format_i18n($lessons_count); ?>個のプロンプト
                                            </span>
                                            <span class="course-date">
                                                <i class="far fa-calendar-alt"></i> <?php echo get_the_date('Y.m.d'); ?>
                                            </span>
                                        </div>

                                        <div class="course-actions">
                                            <a href="<?php the_permalink(); ?>" class="course-link">詳細を見る</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- プロンプト検索結果 -->
                <?php if ($prompt_query->have_posts()) : ?>
                <section class="search-section prompt-results">
                    <div class="section-header">
                        <h2 class="section-title">プロンプト</h2>
                        <?php if ($prompt_query->found_posts > 9) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('s' => $search_query, 'post_type' => 'lesson'), home_url('/'))); ?>" class="view-all-link">すべて見る (<?php echo number_format_i18n($prompt_query->found_posts); ?>)</a>
                        <?php endif; ?>
                    </div>

                    <div class="prompt-grid">
                        <?php while ($prompt_query->have_posts()) : $prompt_query->the_post(); ?>
                            <div class="prompt-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="prompt-card-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="prompt-card-content">
                                    <div class="prompt-card-meta">
                                        <?php
                                        // 親コースの情報を取得
                                        $parent_course_id = get_field('parent_course');
                                        if ($parent_course_id) {
                                            if (is_array($parent_course_id)) {
                                                $parent_course_id = isset($parent_course_id[0]) ? $parent_course_id[0] : null;
                                            }
                                            $parent_course = $parent_course_id ? get_post($parent_course_id) : null;

                                            if ($parent_course) {
                                                $terms = get_the_terms($parent_course->ID, 'course_category');
                                                if ($terms && !is_wp_error($terms)) {
                                                    echo '<span class="prompt-category">' . esc_html($terms[0]->name) . '</span>';
                                                }
                                            }
                                        }
                                        ?>
                                        <span class="prompt-date"><?php echo get_the_date('Y/m/d'); ?></span>
                                    </div>

                                    <h3 class="prompt-card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <div class="prompt-card-excerpt">
                                        <?php
                                        // 50文字以内に制限
                                        $excerpt = get_the_excerpt();
                                        echo mb_substr($excerpt, 0, 50) . (mb_strlen($excerpt) > 50 ? '...' : '');
                                        ?>
                                    </div>

                                    <div class="prompt-card-footer">
                                        <div class="author-info">
                                            <?php
                                            // 監修者情報の取得
                                            $prompt_author = get_field('prompt_author');
                                            if (!$prompt_author && function_exists('get_post_meta')) {
                                                $author_id = get_post_meta(get_the_ID(), 'prompt_author', true);
                                                if ($author_id) {
                                                    $user = get_userdata($author_id);
                                                    if ($user) {
                                                        $prompt_author = array(
                                                            'ID' => $user->ID,
                                                            'display_name' => $user->display_name,
                                                        );
                                                    }
                                                }
                                            }

                                            if ($prompt_author) {
                                                echo get_avatar($prompt_author['ID'], 24, '', $prompt_author['display_name'], array('class' => 'author-avatar'));
                                                echo '<span class="author-name">' . esc_html($prompt_author['display_name']) . '</span>';
                                            } else {
                                                echo get_avatar(get_the_author_meta('ID'), 24);
                                                echo '<span class="author-name">' . get_the_author() . '</span>';
                                            }
                                            ?>
                                        </div>

                                        <div class="prompt-actions">
                                            <a href="<?php the_permalink(); ?>" class="read-more-link">詳細を見る</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

            <?php else : ?>
                <!-- 検索結果なし -->
                <div class="no-results">
                    <div class="no-results-inner">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>検索結果がありません</h3>
                        <p>別のキーワードで検索するか、以下のカテゴリーから探してみてください。</p>

                        <!-- 職種カテゴリー一覧 -->
                        <div class="browse-categories">
                            <h4>職種から探す</h4>
                            <div class="category-links">
                                <?php
                                $professions = get_terms(array(
                                    'taxonomy' => 'profession',
                                    'hide_empty' => true,
                                ));

                                if (!empty($professions) && !is_wp_error($professions)) :
                                    foreach ($professions as $profession) :
                                ?>
                                    <a href="<?php echo esc_url(get_term_link($profession)); ?>" class="category-link">
                                        <?php echo esc_html($profession->name); ?>
                                        <span class="count">(<?php echo esc_html($profession->count); ?>)</span>
                                    </a>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
