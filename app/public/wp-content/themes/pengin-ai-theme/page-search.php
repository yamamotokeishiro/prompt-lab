<?php
/**
 * Template Name: 検索ページ
 *
 * @package PENGIN_AI
 */

get_header();

// 検索クエリのパラメータを取得
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$profession = isset($_GET['profession']) ? sanitize_text_field($_GET['profession']) : '';
$lesson_tag = isset($_GET['lesson_tag']) ? sanitize_text_field($_GET['lesson_tag']) : '';

// すべての職種カテゴリーを取得
$all_professions = get_terms(array(
    'taxonomy' => 'profession',
    'hide_empty' => false,
));

// すべてのタグを取得
$all_tags = get_terms(array(
    'taxonomy' => 'lesson_tag', // lesson_tagに変更
    'hide_empty' => false,
));
?>

<div class="search-page">
    <div class="container">
        <!-- 検索ヘッダー -->
        <div class="search-header">
            <h1 class="search-title">プロンプト検索</h1>

            <form action="<?php echo esc_url(get_permalink()); ?>" method="get" class="main-search-form">
                <div class="search-input-container">
                    <input type="text" name="s" placeholder="キーワードを入力" value="<?php echo esc_attr($search_query); ?>" class="main-search-input">
                    <button type="submit" class="main-search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="search-content">
            <div class="row">
                <!-- サイドバー -->
                <div class="col-lg-3">
                    <div class="search-sidebar">
                        <!-- 職種カテゴリーリスト -->
                        <div class="category-section">
                            <h3 class="sidebar-title">職種から探す</h3>
                            <ul class="category-list">
                                <li class="category-item <?php echo empty($profession) ? 'active' : ''; ?>">
                                    <a href="<?php echo esc_url(remove_query_arg('profession', add_query_arg('s', $search_query))); ?>">
                                        全ての職種
                                    </a>
                                </li>
                                <?php if (!empty($all_professions) && !is_wp_error($all_professions)): ?>
                                    <?php foreach ($all_professions as $prof): ?>
                                        <li class="category-item <?php echo ($profession === $prof->slug) ? 'active' : ''; ?>">
                                            <a href="<?php echo esc_url(add_query_arg(array('profession' => $prof->slug, 's' => $search_query))); ?>">
                                                <?php echo esc_html($prof->name); ?>
                                                <span class="count">(<?php echo $prof->count; ?>)</span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- タグクラウド -->
                        <div class="tag-section">
                            <h3 class="sidebar-title">タグから探す</h3>
                            <div class="tag-cloud">
                                <?php if (!empty($all_tags) && !is_wp_error($all_tags)): ?>
                                    <?php foreach ($all_tags as $tag): ?>
                                        <a href="<?php echo esc_url(add_query_arg(array('lesson_tag' => $tag->slug, 's' => $search_query))); ?>"
                                           class="tag-item <?php echo ($lesson_tag === $tag->slug) ? 'active' : ''; ?>">
                                            <?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>タグはまだありません</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- メインコンテンツ -->
                <div class="col-lg-9">
                    <div class="search-results-container">
                        <?php
                        // 検索クエリの構築
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                        $args = array(
                            'post_type' => 'lesson', // プロンプトに変更
                            'posts_per_page' => 12,
                            'paged' => $paged,
                        );

                        // 検索キーワードがある場合
                        if (!empty($search_query)) {
                            $args['s'] = $search_query;
                        }

                        // タクソノミークエリの追加
                        $tax_query = array();

                        // 職種フィルター
                        if (!empty($profession)) {
                            $tax_query[] = array(
                                'taxonomy' => 'profession',
                                'field' => 'slug',
                                'terms' => $profession,
                            );
                        }

                        // タグフィルター
                        if (!empty($lesson_tag)) {
                            $tax_query[] = array(
                                'taxonomy' => 'lesson_tag',
                                'field' => 'slug',
                                'terms' => $lesson_tag,
                            );
                        }

                        // タクソノミークエリの設定
                        if (!empty($tax_query)) {
                            $args['tax_query'] = array(
                                'relation' => 'AND',
                                $tax_query,
                            );
                        }

                        // クエリの実行
                        $search_results = new WP_Query($args);
                        $total_results = $search_results->found_posts;

                        // 検索結果の表示
                        if (!empty($search_query) || !empty($profession) || !empty($lesson_tag)) {
                            // 検索条件がある場合は結果数を表示
                            $filter_terms = array();
                            if (!empty($profession)) {
                                $prof_term = get_term_by('slug', $profession, 'profession');
                                if ($prof_term) {
                                    $filter_terms[] = $prof_term->name;
                                }
                            }
                            if (!empty($lesson_tag)) {
                                $tag_term = get_term_by('slug', $lesson_tag, 'lesson_tag');
                                if ($tag_term) {
                                    $filter_terms[] = $tag_term->name;
                                }
                            }

                            echo '<div class="search-results-header">';
                            if (!empty($filter_terms)) {
                                echo '<h2 class="results-title">' . implode(' × ', $filter_terms) . ' の検索結果</h2>';
                            } else {
                                echo '<h2 class="results-title">検索結果</h2>';
                            }
                            echo '<p class="results-count">' . $total_results . '件のプロンプトが見つかりました</p>';
                            echo '</div>';
                        } else {
                            // 検索条件がない場合は見出しのみ
                            echo '<div class="search-results-header">';
                            echo '<h2 class="results-title">最新のプロンプト</h2>';
                            echo '</div>';
                        }
                        ?>

                        <?php if ($search_results->have_posts()): ?>
                            <div class="content-grid">
                                <div class="row">
                                    <?php while ($search_results->have_posts()): $search_results->the_post(); ?>
                                        <?php include(get_stylesheet_directory() . '/template-parts/lesson-card-grid.php'); ?>
                                    <?php endwhile; ?>
                                </div>

                                <?php
                                // ページネーション
                                $big = 999999999;
                                echo '<div class="pagination-container">';
                                echo paginate_links(array(
                                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                    'format' => '?paged=%#%',
                                    'current' => max(1, $paged),
                                    'total' => $search_results->max_num_pages,
                                    'prev_text' => '<i class="fas fa-chevron-left"></i> 前へ',
                                    'next_text' => '次へ <i class="fas fa-chevron-right"></i>',
                                ));
                                echo '</div>';
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <div class="no-results-inner">
                                    <i class="fas fa-search fa-3x"></i>
                                    <h3>検索結果がありません</h3>
                                    <p>検索条件を変更して、もう一度お試しください。</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
