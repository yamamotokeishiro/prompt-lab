<?php
/**
 * プロンプト詳細表示用テンプレート
 *
 * @package PENGIN_AI
 */

get_header();

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

<div class="prompt-single-page">
    <div class="container">
        <!-- パンくずリスト -->
        <div class="breadcrumbs">
            <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a> &gt;
            <?php if ($parent_course): ?>
                <a href="<?php echo esc_url(get_permalink($parent_course->ID)); ?>"><?php echo esc_html($parent_course->post_title); ?></a> &gt;
            <?php endif; ?>
            <span><?php the_title(); ?></span>
        </div>

        <!-- プロンプト本文 -->
        <article id="post-<?php the_ID(); ?>" <?php post_class('prompt-main'); ?>>
            <!-- プロンプトヘッダー -->
            <header class="prompt-header">
                <h1 class="prompt-title"><?php the_title(); ?></h1>

                <?php if (!empty($professions) && !is_wp_error($professions)): ?>
                <div class="prompt-profession">
                    <?php foreach ($professions as $profession): ?>
                        <span class="profession-badge"><?php echo esc_html($profession->name); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($tags) && !is_wp_error($tags)): ?>
                <div class="prompt-tags">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?php echo esc_url(add_query_arg('lesson_tag', $tag->slug, get_permalink(get_page_by_path('search')))); ?>" class="tag-badge">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="prompt-meta">
                    <?php
                    $lesson_duration = get_field('lesson_duration');
                    if ($lesson_duration):
                    ?>
                    <div class="meta-item duration">
                        <i class="far fa-clock"></i> 実行時間: 約<?php echo $lesson_duration; ?>分
                    </div>
                    <?php endif; ?>

                    <div class="meta-item date">
                        <i class="far fa-calendar-alt"></i> 更新日: <?php echo get_the_modified_date('Y年n月j日'); ?>
                    </div>

                    <!-- 推奨するAIツール -->
                    <?php
                    $recommended_tools = get_field('recommended_ai_tools');
                    if (!$recommended_tools && function_exists('get_post_meta')) {
                        $recommended_tools = get_post_meta(get_the_ID(), 'recommended_ai_tools', true);
                    }

                    if ($recommended_tools && is_array($recommended_tools) && !empty($recommended_tools)) :
                        $ai_tool_labels = array(
                            'chatgpt' => 'ChatGPT',
                            'claude' => 'Claude',
                            'gemini' => 'Google Gemini',
                            'copilot' => 'Microsoft Copilot',
                            'midjourney' => 'Midjourney',
                            'dall-e' => 'DALL-E',
                            'stable-diffusion' => 'Stable Diffusion',
                            'perplexity' => 'Perplexity',
                            'other' => 'その他',
                        );
                    ?>
                    <div class="meta-item ai-tools">
                        <h4><i class="fas fa-robot"></i> 推奨するAIツール:</h4>
                        <ul class="ai-tools-list">
                            <?php foreach ($recommended_tools as $tool) :
                                $label = isset($ai_tool_labels[$tool]) ? $ai_tool_labels[$tool] : $tool;
                            ?>
                            <li class="ai-tool-item"><?php echo esc_html($label); ?></li>
                            <?php endforeach; ?>

                            <?php
                            // その他のツールがある場合は表示
                            $other_tools = get_post_meta(get_the_ID(), 'other_ai_tool', true);
                            if (!empty($other_tools)) :
                                $other_tools_array = array_map('trim', explode(',', $other_tools));
                                foreach ($other_tools_array as $other_tool) :
                            ?>
                            <li class="ai-tool-item"><?php echo esc_html($other_tool); ?></li>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (has_post_thumbnail()): ?>
                <div class="prompt-featured-image">
                    <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                </div>
                <?php endif; ?>
            </header>

            <!-- プロンプト本文 -->
            <div class="prompt-content-container">
                <?php
                // 本文の取得
                $content = get_the_content();

                // プロンプトセクションの特定のパターン（例：```prompt や [PROMPT] など）を探す
                $pattern = '/(\[PROMPT\]|\`\`\`prompt)(.*?)(\[\/PROMPT\]|\`\`\`)/s';
                $has_prompt_section = preg_match($pattern, $content, $matches);

                if ($has_prompt_section) {
                    // プロンプト部分とそれ以外を分離
                    $prompt_text = trim($matches[2]);
                    $explanation_text = str_replace($matches[0], '', $content);
                ?>
                    <!-- 説明エリア -->
                    <div class="prompt-explanation">
                        <h2 class="section-title">説明</h2>
                        <?php echo apply_filters('the_content', $explanation_text); ?>
                    </div>

                    <?php
                    // 監修者情報を取得
                    $prompt_author = get_field('prompt_author');
                    if (!$prompt_author && function_exists('get_post_meta')) {
                        $author_id = get_post_meta(get_the_ID(), 'prompt_author', true);
                        if ($author_id) {
                            $user = get_userdata($author_id);
                            if ($user) {
                                $prompt_author = array(
                                    'ID' => $user->ID,
                                    'display_name' => $user->display_name,
                                    'user_email' => $user->user_email,
                                );
                            }
                        }
                    }

                    // 監修者が設定されている場合のみ表示
                    if ($prompt_author) :
                        // 追加情報の取得
                        $author_title = get_field('prompt_author_title');
                        if (!$author_title) {
                            $author_title = get_post_meta(get_the_ID(), 'prompt_author_title', true);
                        }

                        $author_description = get_field('prompt_author_description');
                        if (!$author_description) {
                            $author_description = get_post_meta(get_the_ID(), 'prompt_author_description', true);
                        }
                    ?>
                    <div class="prompt-author-container">
                        <h2 class="prompt-author-heading">プロンプトの監修者</h2>
                        <div class="prompt-author-content">
                            <div class="prompt-author-image">
                                <?php echo get_avatar($prompt_author['ID'], 64, '', $prompt_author['display_name'], array('class' => 'prompt-author-avatar')); ?>
                            </div>
                            <div class="prompt-author-details">
                                <?php if (!empty($author_title)) : ?>
                                <p class="prompt-author-position"><?php echo esc_html($author_title); ?></p>
                                <?php endif; ?>

                                <p class="prompt-author-name"><?php echo esc_html($prompt_author['display_name']); ?></p>

                                <?php if (!empty($author_description)) : ?>
                                <div class="prompt-author-bio"><?php echo wp_kses_post($author_description); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>


                    <!-- プロンプトエリア -->
                    <div class="prompt-code-area">
                        <h2 class="section-title">プロンプト
                            <button class="copy-prompt-btn" data-prompt="<?php echo esc_attr($prompt_text); ?>">
                                <i class="far fa-copy"></i> コピーする
                            </button>
                        </h2>
                        <div class="prompt-code">
                            <pre id="prompt-text-content"><?php echo esc_html($prompt_text); ?></pre>
                        </div>
                        <div class="prompt-tips">
                            <p><i class="fas fa-lightbulb"></i> ヒント: 「コピーする」ボタンをクリックすると、プロンプト全体をクリップボードにコピーできます。</p>
                        </div>
                    </div>
                <?php
                } else {
                    // プロンプトセクションが見つからない場合は通常の本文を表示
                ?>
                    <div class="prompt-explanation">
                        <h2 class="section-title">説明</h2>
                        <?php
                        // 本文を解析して自動的にプロンプト部分を見つける試み
                        $paragraphs = explode("\n\n", $content);
                        $found_prompt = false;

                        if (count($paragraphs) > 1) {
                            // 最後の段落または長めの段落をプロンプトとして扱う
                            $last_paragraph = trim(end($paragraphs));
                            $second_last = count($paragraphs) > 1 ? trim($paragraphs[count($paragraphs) - 2]) : '';

                            // 条件：200文字以上の段落、または最後の段落（説明が短い場合）
                            if (mb_strlen($last_paragraph) > 200 ||
                                (mb_strlen($last_paragraph) > 50 && mb_strlen($second_last) < 100)) {
                                $prompt_text = $last_paragraph;
                                array_pop($paragraphs);
                                $explanation_text = implode("\n\n", $paragraphs);
                                $found_prompt = true;

                                // 説明部分の表示
                                echo apply_filters('the_content', $explanation_text);
                            }
                        }

                        if (!$found_prompt) {
                            // プロンプト部分が見つからない場合は全文を表示
                            the_content();
                        }
                        ?>
                    </div>

                    <?php
                    // 監修者情報を取得
                    $prompt_author = get_field('prompt_author');
                    if (!$prompt_author && function_exists('get_post_meta')) {
                        $author_id = get_post_meta(get_the_ID(), 'prompt_author', true);
                        if ($author_id) {
                            $user = get_userdata($author_id);
                            if ($user) {
                                $prompt_author = array(
                                    'ID' => $user->ID,
                                    'display_name' => $user->display_name,
                                    'user_email' => $user->user_email,
                                );
                            }
                        }
                    }

                    // 監修者が設定されている場合のみ表示
                    if ($prompt_author) :
                        // 追加情報の取得
                        $author_title = get_field('prompt_author_title');
                        if (!$author_title) {
                            $author_title = get_post_meta(get_the_ID(), 'prompt_author_title', true);
                        }

                        $author_description = get_field('prompt_author_description');
                        if (!$author_description) {
                            $author_description = get_post_meta(get_the_ID(), 'prompt_author_description', true);
                        }

                        // 著者のURLを取得
                        $author_url = get_the_author_meta('user_url', $prompt_author['ID']);
                    ?>
                    <div class="prompt-author-section">
                        <h2 class="section-title">監修者情報</h2>
                        <div class="prompt-author-card">
                            <!-- 監修者のアバター画像 -->
                            <div class="prompt-author-avatar">
                                <?php echo get_avatar($prompt_author['ID'], 96, '', $prompt_author['display_name'], array('class' => 'prompt-author-img')); ?>
                            </div>

                            <!-- 監修者情報 -->
                            <div class="prompt-author-info">
                                <h3 class="prompt-author-name"><?php echo esc_html($prompt_author['display_name']); ?></h3>

                                <?php if (!empty($author_title)) : ?>
                                <p class="prompt-author-title"><?php echo esc_html($author_title); ?></p>
                                <?php endif; ?>

                                <?php if (!empty($author_description)) : ?>
                                <div class="prompt-author-bio"><?php echo wp_kses_post($author_description); ?></div>
                                <?php endif; ?>

                                <?php if (!empty($author_url)) : ?>
                                <div class="prompt-author-links">
                                    <a href="<?php echo esc_url($author_url); ?>" target="_blank" rel="nofollow">
                                        <i class="fas fa-link"></i> プロフィールサイト
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($found_prompt) : ?>
                    <!-- 自動検出したプロンプト部分の表示 -->
                    <div class="prompt-code-area">
                        <h2 class="section-title">プロンプト
                            <button class="copy-prompt-btn" data-prompt="<?php echo esc_attr($prompt_text); ?>">
                                <i class="far fa-copy"></i> コピーする
                            </button>
                        </h2>
                        <div class="prompt-code">
                            <pre id="prompt-text-content"><?php echo esc_html($prompt_text); ?></pre>
                        </div>
                        <div class="prompt-tips">
                            <p><i class="fas fa-lightbulb"></i> ヒント: 「コピーする」ボタンをクリックすると、プロンプト全体をクリップボードにコピーできます。</p>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php
                }

                // ページネーション（プロンプトが複数ページに分かれている場合）
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('ページ:', 'pengin-ai'),
                    'after'  => '</div>',
                ));
                ?>
            </div>

            <!-- コピー成功メッセージ -->
            <div id="copy-success-message" class="copy-success-message">
                <i class="fas fa-check-circle"></i> プロンプトをコピーしました！
            </div>
        </article>
    </div>
</div>

<!-- プロンプトコピー用のJavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-prompt-btn');
    const successMessage = document.getElementById('copy-success-message');

    copyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const promptText = this.getAttribute('data-prompt');
            const originalButtonText = this.innerHTML;

            // テキストエリアを作成してコピー
            const textarea = document.createElement('textarea');
            textarea.value = promptText;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                // execCommandを使用
                const successful = document.execCommand('copy');
                if (successful) {
                    // コピー成功時のボタン表示変更
                    this.innerHTML = '<i class="fas fa-check"></i> コピーしました';
                    this.style.backgroundColor = '#28a745';

                    // 成功メッセージを表示
                    successMessage.classList.add('show');

                    // 3秒後に元に戻す
                    setTimeout(() => {
                        this.innerHTML = originalButtonText;
                        this.style.backgroundColor = '';
                        successMessage.classList.remove('show');
                    }, 3000);
                } else {
                    // コピー失敗
                    this.innerHTML = '<i class="fas fa-times"></i> 失敗しました';
                    this.style.backgroundColor = '#dc3545';

                    setTimeout(() => {
                        this.innerHTML = originalButtonText;
                        this.style.backgroundColor = '';
                    }, 3000);

                    console.error('コピーに失敗しました');
                }
            } catch (err) {
                // エラー発生時
                this.innerHTML = '<i class="fas fa-times"></i> エラー';
                this.style.backgroundColor = '#dc3545';

                setTimeout(() => {
                    this.innerHTML = originalButtonText;
                    this.style.backgroundColor = '';
                }, 3000);

                console.error('コピーエラー:', err);
            }

            // クリーンアップ
            document.body.removeChild(textarea);

            // モダンなClipboard APIもバックアップとして試す
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(promptText).catch(function(err) {
                    console.error('Clipboard API エラー:', err);
                });
            }
        });
    });

    // プロンプトテキストにマウスオーバーしたときにツールチップを表示
    const promptTextContent = document.getElementById('prompt-text-content');
    if (promptTextContent) {
        promptTextContent.title = "クリックするとテキストを選択できます";

        promptTextContent.addEventListener('click', function() {
            // テキスト選択
            const range = document.createRange();
            range.selectNodeContents(this);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        });
    }
});
</script>

<?php get_footer(); ?>
