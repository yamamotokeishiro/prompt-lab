<?php
/**
 * フロントページテンプレート
 *
 * @package PENGIN_AI
 */
get_header();
?>

<div class="ai-chat-layout">
    <!-- 左側サイドバー（職種カテゴリー） -->
    <div class="sidebar-menu">
        <div class="sidebar-header">
            <h2>職種から探す</h2>
        </div>
        <div class="sidebar-search">
            <input type="text" placeholder="職種を検索" id="sidebar-search-input">
        </div>
        <div class="sidebar-categories">
            <ul>
                <?php
                // 職種タクソノミーを取得
                $professions = get_terms(array(
                    'taxonomy' => 'profession',
                    'hide_empty' => false,
                ));

                if (!empty($professions) && !is_wp_error($professions)) :
                    foreach ($professions as $profession) :
                        // この職種に属するコース数を取得
                        $args = array(
                            'post_type' => 'course',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'profession',
                                    'field' => 'term_id',
                                    'terms' => $profession->term_id,
                                ),
                            ),
                            'posts_per_page' => -1,
                        );
                        $query = new WP_Query($args);
                        $count = $query->found_posts;
                        wp_reset_postdata();
                ?>
                    <li class="sidebar-category">
                        <a href="<?php echo get_term_link($profession); ?>">
                            <?php echo $profession->name; ?>
                            <span class="category-count">(<?php echo $count; ?>)</span>
                        </a>
                    </li>
                <?php
                    endforeach;
                endif;

                // 職種がない場合の処理
                if (empty($professions) || is_wp_error($professions)) :
                ?>
                    <li class="sidebar-category">
                        <p class="no-categories">
                            <?php if (is_wp_error($professions)) : ?>
                                エラー: <?php echo $professions->get_error_message(); ?>
                            <?php else : ?>
                                職種がまだ登録されていません
                            <?php endif; ?>
                        </p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <!-- メインコンテンツエリア -->
    <div class="main-content">
        <!-- ヒーローセクション（全画面） -->
        <div class="hero-section">
            <div class="hero-content">
                <h1>さあ、仕事を楽しもう</h1>
                <div class="search-container">
                    <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="search-form">
                        <input type="text" name="s" placeholder="検索してみましょう" class="search-input">
                        <input type="hidden" name="post_type" value="lesson">
                        <div class="search-buttons">
                            <button type="submit" class="search-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                                <span>検索</span>
                            </button>
                            <button type="button" class="deep-research-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stars" viewBox="0 0 16 16">
                                    <path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.734 1.734 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.734 1.734 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.734 1.734 0 0 0 3.407 2.31l.387-1.162zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L10.863.1z"/>
                                </svg>
                                <span>希望するプロンプト</span>
                            </button>
                        </div>
                    </form>
                </div>
                <!-- プラスとマイクのボタンを削除 -->
            </div>
        </div>

        <!-- 最近追加されたプロンプトセクション -->
        <div class="recent-prompts-section">
            <div class="section-header">
                <h2>最近追加されたプロンプト</h2>
                <a href="<?php echo get_post_type_archive_link('lesson'); ?>" class="view-all-link">すべて見る</a>
            </div>
            <div class="prompt-cards">
                <?php
                // 最近追加されたレッスン（プロンプト）を取得
                $recent_prompts = new WP_Query(array(
                    'post_type' => 'lesson',
                    'posts_per_page' => 6,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($recent_prompts->have_posts()) :
                    while ($recent_prompts->have_posts()) : $recent_prompts->the_post();
                        // 親コースの情報を取得
                        $parent_course_id = get_field('parent_course');
                        $parent_course = $parent_course_id ? get_post($parent_course_id) : null;
                ?>
                <div class="prompt-card">
                    <?php if (has_post_thumbnail()) : ?>
                    <div class="prompt-card-image">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                    <?php endif; ?>
                    <div class="prompt-card-content">
                        <div class="prompt-card-meta">
                            <?php
                            if ($parent_course_id) {
                                $terms = get_the_terms($parent_course_id, 'course_category');
                                if ($terms && !is_wp_error($terms)) {
                                    echo '<span class="prompt-category">' . $terms[0]->name . '</span>';
                                }
                            }
                            ?>
                            <span class="prompt-date"><?php echo get_the_date('Y/m/d'); ?></span>
                        </div>
                        <h3 class="prompt-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="prompt-card-excerpt">
                            <?php
                              // 40文字以内に制限
                              $excerpt = get_the_excerpt();
                              echo mb_substr($excerpt, 0, 40) . (mb_strlen($excerpt) > 40 ? '...' : '');
                            ?>
                        </div>
                        <div class="prompt-card-footer">
                            <div class="author-info">
                                <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                <span class="author-name"><?php the_author(); ?></span>
                            </div>
                            <div class="prompt-actions">
                                <a href="<?php the_permalink(); ?>" class="read-more-link">詳細を見る</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                <div class="no-prompts">
                    <p>最近追加されたプロンプトはありません。</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 人気のプロンプトセクション -->
        <div class="popular-prompts-section">
            <div class="section-header">
                <h2>人気のプロンプト</h2>
                <a href="<?php echo home_url('/popular-prompts/'); ?>" class="view-all-link">すべて見る</a>
            </div>
            <div class="prompt-cards">
                <?php
                // 人気のレッスン（プロンプト）を取得（例：コメント数や閲覧数でソート）
                $popular_prompts = new WP_Query(array(
                    'post_type' => 'lesson',
                    'posts_per_page' => 6,
                    'meta_key' => 'post_views_count', // 閲覧数のカスタムフィールドを想定
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                ));

                // 閲覧数がない場合はコメント数でソート
                if ($popular_prompts->post_count == 0) {
                    $popular_prompts = new WP_Query(array(
                        'post_type' => 'lesson',
                        'posts_per_page' => 6,
                        'orderby' => 'comment_count',
                        'order' => 'DESC',
                    ));
                }

                if ($popular_prompts->have_posts()) :
                    while ($popular_prompts->have_posts()) : $popular_prompts->the_post();
                        // 親コースの情報を取得
                        $parent_course_id = get_field('parent_course');
                        $parent_course = $parent_course_id ? get_post($parent_course_id) : null;
                ?>
                <div class="prompt-card">
                    <?php if (has_post_thumbnail()) : ?>
                    <div class="prompt-card-image">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                    <?php endif; ?>
                    <div class="prompt-card-content">
                        <div class="prompt-card-meta">
                            <?php
                            if ($parent_course_id) {
                                $terms = get_the_terms($parent_course_id, 'course_category');
                                if ($terms && !is_wp_error($terms)) {
                                    echo '<span class="prompt-category">' . $terms[0]->name . '</span>';
                                }
                            }
                            ?>
                            <span class="prompt-views">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                                <?php echo get_post_meta(get_the_ID(), 'post_views_count', true) ? get_post_meta(get_the_ID(), 'post_views_count', true) : '0'; ?>
                            </span>
                        </div>
                        <h3 class="prompt-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="prompt-card-excerpt">
                            <?php
                              // 40文字以内に制限
                              $excerpt = get_the_excerpt();
                              echo mb_substr($excerpt, 0, 40) . (mb_strlen($excerpt) > 40 ? '...' : '');
                            ?>
                        </div>
                        <div class="prompt-card-footer">
                            <div class="author-info">
                                <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                <span class="author-name"><?php the_author(); ?></span>
                            </div>
                            <div class="prompt-actions">
                                <a href="<?php the_permalink(); ?>" class="read-more-link">詳細を見る</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                <div class="no-prompts">
                    <p>人気のプロンプトはまだありません。</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- 職種カテゴリーセクション -->
        <div class="profession-categories-section">
            <div class="container">
                <div class="section-header">
                    <h2>職種から探す</h2>
                    <a href="<?php echo get_term_link('profession'); ?>" class="view-all-link">すべて見る</a>
                </div>
                <div class="profession-categories">
                    <?php
                    // 職種タクソノミーを取得
                    $professions = get_terms(array(
                        'taxonomy' => 'profession',
                        'hide_empty' => false,
                    ));

                    if (!empty($professions) && !is_wp_error($professions)) :
                        foreach ($professions as $profession) :
                            // このカテゴリーに属するコース数を取得
                            $args = array(
                                'post_type' => 'course',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'profession',
                                        'field' => 'term_id',
                                        'terms' => $profession->term_id,
                                    ),
                                ),
                                'posts_per_page' => -1,
                            );
                            $query = new WP_Query($args);
                            $count = $query->found_posts;
                            wp_reset_postdata();

                            // アイコンクラスを設定（Font Awesomeを使用）
                            $icon_class = 'fa-briefcase'; // デフォルトアイコン
                            switch ($profession->slug) {
                                case 'engineer':
                                case 'エンジニア':
                                    $icon_class = 'fa-code';
                                    break;
                                case 'designer':
                                case 'デザイナー':
                                    $icon_class = 'fa-pencil-ruler';
                                    break;
                                case 'marketer':
                                case 'マーケター':
                                    $icon_class = 'fa-chart-line';
                                    break;
                                case 'writer':
                                case 'ライター':
                                    $icon_class = 'fa-pen';
                                    break;
                                case 'business-analyst':
                                case 'ビジネスアナリスト':
                                    $icon_class = 'fa-chart-pie';
                                    break;
                                case 'product-manager':
                                case 'プロダクトマネージャー':
                                    $icon_class = 'fa-tasks';
                                    break;
                                case 'sales':
                                case '営業':
                                    $icon_class = 'fa-handshake';
                                    break;
                                case 'customer-support':
                                case 'カスタマーサポート':
                                    $icon_class = 'fa-headset';
                                    break;
                                case 'educator':
                                case '教育者':
                                    $icon_class = 'fa-chalkboard-teacher';
                                    break;
                                case 'researcher':
                                case '研究者':
                                    $icon_class = 'fa-microscope';
                                    break;
                            }
                    ?>
                    <div class="profession-category-card">
                        <div class="profession-icon">
                            <i class="fas <?php echo $icon_class; ?>"></i>
                        </div>
                        <h3><?php echo $profession->name; ?></h3>
                        <p><?php echo $profession->description; ?></p>
                        <div class="profession-course-count"><?php echo $count; ?> コース</div>
                        <a href="<?php echo get_term_link($profession); ?>" class="profession-link">詳細を見る</a>
                    </div>
                    <?php
                        endforeach;
                    endif;

                    if (empty($professions) || is_wp_error($professions)) :
                    ?>
                    <div class="no-professions">
                        <p>職種カテゴリーがまだ登録されていません。</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>
