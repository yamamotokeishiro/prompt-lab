<?php
/**
 * コース詳細ページのテンプレート
 */
get_header();

// コース進捗計算関数
function calculate_course_progress() {
    if(!is_user_logged_in()) {
        return 0;
    }

    $user_id = get_current_user_id();
    $course_id = get_the_ID();

    // このコースに属するレッスンを取得
    $args = array(
        'post_type' => 'lesson',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'parent_course',
                'value' => $course_id,
                'compare' => '='
            )
        )
    );

    $lessons = new WP_Query($args);

    if($lessons->post_count === 0) {
        return 0;
    }

    $completed_count = 0;

    while($lessons->have_posts()) {
        $lessons->the_post();
        $lesson_id = get_the_ID();
        $lesson_progress = get_user_meta($user_id, 'lesson_' . $lesson_id . '_progress', true);

        if($lesson_progress === 'completed') {
            $completed_count++;
        }
    }

    wp_reset_postdata();

    return ($completed_count / $lessons->post_count) * 100;
}

// ユーザーがコースを開始しているか確認
function has_user_started_course() {
    if(!is_user_logged_in()) {
        return false;
    }

    $user_id = get_current_user_id();
    $course_id = get_the_ID();

    return get_user_meta($user_id, 'course_' . $course_id . '_started', true) === 'yes';
}

// 次のレッスンのURLを取得
function get_next_lesson_url() {
    if(!is_user_logged_in()) {
        return '#';
    }

    $user_id = get_current_user_id();
    $course_id = get_the_ID();

    // このコースに属するレッスンを取得
    $args = array(
        'post_type' => 'lesson',
        'posts_per_page' => -1,
        'meta_key' => 'lesson_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'parent_course',
                'value' => $course_id,
                'compare' => '='
            )
        )
    );

    $lessons = new WP_Query($args);

    if(!$lessons->have_posts()) {
        return '#';
    }

    // 最初の未完了レッスンを探す
    while($lessons->have_posts()) {
        $lessons->the_post();
        $lesson_id = get_the_ID();
        $lesson_progress = get_user_meta($user_id, 'lesson_' . $lesson_id . '_progress', true);

        if($lesson_progress !== 'completed') {
            $url = get_permalink();
            wp_reset_postdata();
            return $url;
        }
    }

    // すべて完了している場合は最初のレッスン
    $lessons->rewind_posts();
    $lessons->the_post();
    $url = get_permalink();
    wp_reset_postdata();

    return $url;
}

while(have_posts()): the_post();
?>

<div class="course-header">
    <div class="container">
        <h1 class="course-title"><?php the_title(); ?></h1>

        <div class="course-meta">
            <div class="difficulty">
                <i class="fas fa-signal"></i>
                <span>難易度: <?php echo get_post_meta(get_the_ID(), 'difficulty', true) ?: '初級'; ?></span>
            </div>
            <div class="duration">
                <i class="far fa-clock"></i>
                <span>所要時間: <?php echo get_post_meta(get_the_ID(), 'duration', true) ?: '0'; ?>時間</span>
            </div>

            <!-- ソーシャルシェアボタン -->
            <div class="social-share">
                <a href="https://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" class="twitter-share">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" class="facebook-share">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="course-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- コース説明 -->
                <div class="course-description">
                    <?php the_content(); ?>
                </div>

                <!-- レッスン一覧 -->
                <div class="lessons-list">
                    <h2>レッスン一覧</h2>

                    <?php
                    // このコースに属するレッスンを取得
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
                    $lesson_count = 1;

                    if($lessons->have_posts()) :
                    ?>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo calculate_course_progress(); ?>%" data-width="<?php echo calculate_course_progress(); ?>"></div>
                        </div>

                        <?php
                        while($lessons->have_posts())