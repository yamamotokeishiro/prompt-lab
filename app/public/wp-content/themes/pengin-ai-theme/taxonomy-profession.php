<?php
/**
 * 職種カテゴリーアーカイブテンプレート
 *
 * @package PENGIN_AI
 */

get_header();

// 現在の職種ターム情報を取得
$term = get_queried_object();
?>

<div class="profession-archive-page">
    <div class="container">
        <!-- ヘッダーセクション -->
        <div class="profession-header">
            <div class="profession-header-content">
                <h1 class="profession-title"><?php echo esc_html($term->name); ?></h1>

                <?php if (!empty($term->description)) : ?>
                <div class="profession-description">
                    <?php echo wp_kses_post($term->description); ?>
                </div>
                <?php endif; ?>

                <div class="profession-meta">
                    <?php
                    $post_count = $wp_query->found_posts;
                    printf(_n('%s個のプロンプト', '%s個のプロンプト', $post_count, 'pengin-ai'), number_format_i18n($post_count));
                    ?>
                </div>
            </div>
        </div>

        <!-- メインコンテンツ -->
        <div class="profession-content">
            <!-- 検索フォーム -->
            <!-- <div class="profession-search">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
                    <input type="text" name="s" placeholder="<?php echo esc_attr($term->name); ?>のプロンプトを検索" class="search-input">
                    <input type="hidden" name="post_type" value="lesson">
                    <input type="hidden" name="profession" value="<?php echo esc_attr($term->slug); ?>">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div> -->

            <?php if (have_posts()) : ?>
                <!-- プロンプトカードグリッド -->
                <div class="prompt-grid">
                    <?php while (have_posts()) : the_post(); ?>
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
                                    // 40文字以内に制限
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
                    <?php endwhile; ?>
                </div>

                <!-- ページネーション -->
                <div class="pagination-container">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '<i class="fas fa-chevron-left"></i> 前へ',
                        'next_text' => '次へ <i class="fas fa-chevron-right"></i>',
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="no-results">
                    <div class="no-results-inner">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>プロンプトが見つかりません</h3>
                        <p>この職種に関連するプロンプトはまだありません。</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 関連職種セクション -->
        <?php
        // 他の職種を取得
        $other_professions = get_terms(array(
            'taxonomy' => 'profession',
            'exclude' => array($term->term_id),
            'hide_empty' => true,
            'number' => 6,
        ));

        if (!empty($other_professions) && !is_wp_error($other_professions)) :
        ?>
        <div class="related-professions">
            <h2 class="section-title">その他の職種</h2>
            <div class="profession-links">
                <?php foreach ($other_professions as $profession) : ?>
                    <a href="<?php echo esc_url(get_term_link($profession)); ?>" class="profession-link">
                        <?php echo esc_html($profession->name); ?>
                        <span class="count">(<?php echo esc_html($profession->count); ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
