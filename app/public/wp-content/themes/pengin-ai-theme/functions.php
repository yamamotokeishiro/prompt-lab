<?php
// 子テーマ用functions.php

// スタイルシートとスクリプトの読み込み
function pengin_ai_enqueue_styles() {
    // Reset CSS (最初に読み込む)
    wp_enqueue_style('reset-style', get_stylesheet_directory_uri() . '/assets/css/reset.css');
  // 親テーマのスタイルシート
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    // Google Fonts - Noto Sans JP
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap');

    // Bootstrap (必要に応じて)
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');

        // カスタムCSS
        wp_enqueue_style('pengin-ai-custom', get_stylesheet_directory_uri() . '/assets/css/custom.css', array('parent-style'), '1.0');


    // カスタムJavaScript
    wp_enqueue_script('pengin-ai-custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0', true);

    // Bootstrap JS (必要に応じて)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'pengin_ai_enqueue_styles');

// テーマサポート
function pengin_ai_theme_setup() {
    // アイキャッチ画像サポート
    add_theme_support('post-thumbnails');

    // タイトルタグサポート
    add_theme_support('title-tag');

    // HTML5サポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // メニュー登録
    register_nav_menus(array(
        'header-menu' => 'ヘッダーメニュー',
        'footer-menu' => 'フッターメニュー',
    ));
}
add_action('after_setup_theme', 'pengin_ai_theme_setup');


// コース用カスタム投稿タイプの登録
function create_course_post_type() {
  register_post_type('course',
      array(
          'labels' => array(
              'name' => __('コース'),
              'singular_name' => __('コース')
          ),
          'public' => true,
          'has_archive' => true,
          'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
          'menu_icon' => 'dashicons-welcome-learn-more',
          'rewrite' => array('slug' => 'courses')
      )
  );
}
add_action('init', 'create_course_post_type');

// コースカテゴリー用タクソノミーの登録
function create_course_taxonomy() {
  register_taxonomy(
      'course_category',
      'course',
      array(
          'label' => __('コースカテゴリー'),
          'hierarchical' => true,
          'rewrite' => array('slug' => 'course-category')
      )
  );
}
add_action('init', 'create_course_taxonomy');

// プロンプト用カスタム投稿タイプ
function create_lesson_post_type() {
  register_post_type('lesson',
      array(
          'labels' => array(
              'name' => __('プロンプト'),
              'singular_name' => __('プロンプト'),
              'add_new' => __('新規追加'),
              'add_new_item' => __('新規プロンプトを追加'),
              'edit_item' => __('プロンプトを編集'),
              'new_item' => __('新規プロンプト'),
              'view_item' => __('プロンプトを表示'),
              'search_items' => __('プロンプトを検索'),
              'not_found' => __('プロンプトが見つかりませんでした'),
              'not_found_in_trash' => __('ゴミ箱にプロンプトはありません'),
              'all_items' => __('全てのプロンプト'),
              'archives' => __('プロンプトアーカイブ'),
              'menu_name' => __('プロンプト')
          ),
          'public' => true,
          'has_archive' => true,
          'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
          'menu_icon' => 'dashicons-text-page', // アイコンもプロンプトらしいものに変更
          'rewrite' => array('slug' => 'prompts') // URLも変更
      )
  );
}
add_action('init', 'create_lesson_post_type');




// Font Awesome の読み込み
function pengin_ai_enqueue_font_awesome() {
  wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'pengin_ai_enqueue_font_awesome');


// レッスンにタクソノミーを追加
function pengin_ai_modify_lesson_taxonomies() {
  // 既存のコンテンツ用タクソノミーがあれば、レッスンにも適用
  if (taxonomy_exists('profession')) {
      register_taxonomy_for_object_type('profession', 'lesson');
  } else {
      // 職種タクソノミー
      $profession_labels = array(
          'name'              => '職種',
          'singular_name'     => '職種',
          'search_items'      => '職種を検索',
          'all_items'         => 'すべての職種',
          'parent_item'       => '親職種',
          'parent_item_colon' => '親職種:',
          'edit_item'         => '職種を編集',
          'update_item'       => '職種を更新',
          'add_new_item'      => '新しい職種を追加',
          'new_item_name'     => '新しい職種名',
          'menu_name'         => '職種',
      );

      register_taxonomy('profession', array('lesson'), array(
          'hierarchical'      => true,
          'labels'            => $profession_labels,
          'show_ui'           => true,
          'show_admin_column' => true,
          'query_var'         => true,
          'rewrite'           => array('slug' => 'profession'),
          'show_in_rest'      => true,
      ));
  }

  // コンテンツタグの処理も同様
  if (taxonomy_exists('content_tag')) {
    register_taxonomy_for_object_type('content_tag', 'lesson');
  } else {
    // タグタクソノミー
    $content_tag_labels = array(
        'name'              => 'プロンプトタグ',
        'singular_name'     => 'プロンプトタグ',
        'search_items'      => 'タグを検索',
        'all_items'         => 'すべてのタグ',
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => 'タグを編集',
        'update_item'       => 'タグを更新',
        'add_new_item'      => '新しいタグを追加',
        'new_item_name'     => '新しいタグ名',
        'menu_name'         => 'タグ',
    );

    register_taxonomy('lesson_tag', array('lesson'), array(
        'hierarchical'      => false,
        'labels'            => $content_tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'prompt-tag'), // URLも変更
        'show_in_rest'      => true,
    ));
  }

}
add_action('init', 'pengin_ai_modify_lesson_taxonomies');

// ACFフィールドの調整（既存のフィールドがある場合）
function pengin_ai_adjust_acf_fields() {
  if (function_exists('acf_add_local_field_group')) {
      // 既存のコンテンツ情報フィールドグループを確認・調整
      $field_groups = acf_get_field_groups();
      $found_content_group = false;

      foreach ($field_groups as $group) {
          if (strpos($group['title'], 'コンテンツ情報') !== false) {
              $found_content_group = true;
              break;
          }
      }

      // コンテンツ情報フィールドグループが見つからない場合のみ新規作成
      if (!$found_content_group) {
          // プロンプト用のフィールドグループを作成（必要に応じて）
          acf_add_local_field_group(array(
              'key' => 'group_lesson_info',
              'title' => 'プロンプト情報',
              'fields' => array(
                  array(
                      'key' => 'field_parent_course',
                      'label' => '親コース',
                      'name' => 'parent_course',
                      'type' => 'post_object',
                      'instructions' => 'このプロンプトが属するコースを選択してください',
                      'required' => 1,
                      'post_type' => array('course'),
                      'return_format' => 'id',
                  ),
                  array(
                      'key' => 'field_lesson_order',
                      'label' => '表示順序',
                      'name' => 'lesson_order',
                      'type' => 'number',
                      'instructions' => 'プロンプトの表示順序（小さい数字が先に表示されます）',
                      'default_value' => 0,
                      'min' => 0,
                      'max' => 999,
                  ),
                  array(
                      'key' => 'field_lesson_duration',
                      'label' => '所要時間',
                      'name' => 'lesson_duration',
                      'type' => 'number',
                      'instructions' => '実行の所要時間（分）',
                      'default_value' => 0,
                      'min' => 0,
                      'max' => 999,
                  ),
              ),
              'location' => array(
                  array(
                      array(
                          'param' => 'post_type',
                          'operator' => '==',
                          'value' => 'lesson',
                      ),
                  ),
              ),
          ));
      }
  }
}

add_action('acf/init', 'pengin_ai_adjust_acf_fields');


// OpenAI風スタイルの読み込み
function pengin_ai_openai_styles() {
  wp_enqueue_style('pengin-ai-openai-style', get_stylesheet_directory_uri() . '/assets/css/openai-style.css', array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'pengin_ai_openai_styles');

/**
 * ナビゲーションメニューの登録
 */
function pengin_ai_register_menus() {
  register_nav_menus(array(
      'header-menu' => 'ヘッダーメニュー',
      'footer-menu' => 'フッターメニュー'
  ));
}
add_action('init', 'pengin_ai_register_menus');

/**
 * 職種タクソノミーの登録
 */
function pengin_ai_register_profession_taxonomy() {
  $labels = array(
      'name'              => '職種',
      'singular_name'     => '職種',
      'search_items'      => '職種を検索',
      'all_items'         => '全ての職種',
      'parent_item'       => '親職種',
      'parent_item_colon' => '親職種:',
      'edit_item'         => '職種を編集',
      'update_item'       => '職種を更新',
      'add_new_item'      => '新しい職種を追加',
      'new_item_name'     => '新しい職種名',
      'menu_name'         => '職種',
  );

  $args = array(
      'hierarchical'      => true,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'profession'),
  );

  register_taxonomy('profession', 'course', $args);
}
add_action('init', 'pengin_ai_register_profession_taxonomy');

// 推奨するAIツールのフィールドを追加
function add_recommended_ai_tools_field() {
  if (function_exists('acf_add_local_field_group')) {
      // 既存のレッスン情報フィールドグループを確認
      $field_groups = acf_get_field_groups();
      $lesson_info_group = null;

      // レッスン情報のフィールドグループを探す
      foreach ($field_groups as $group) {
          if ($group['title'] === 'レッスン情報' || $group['title'] === 'プロンプト情報') {
              $lesson_info_group = $group;
              break;
          }
      }

      // フィールドグループが見つかった場合は新しいフィールドを追加
      if ($lesson_info_group) {
          // 既存のフィールドを取得
          $fields = acf_get_fields($lesson_info_group['key']);

          // 新しいフィールドを追加
          acf_add_local_field(array(
              'key' => 'field_recommended_ai_tools',
              'label' => '推奨するAIツール',
              'name' => 'recommended_ai_tools',
              'type' => 'checkbox',
              'instructions' => 'このプロンプトで推奨するAIツールを選択してください（複数選択可）',
              'required' => 0,
              'choices' => array(
                  'chatgpt' => 'ChatGPT',
                  'claude' => 'Claude',
                  'gemini' => 'Google Gemini',
                  'copilot' => 'Microsoft Copilot',
                  'midjourney' => 'Midjourney',
                  'dall-e' => 'DALL-E',
                  'stable-diffusion' => 'Stable Diffusion',
                  'perplexity' => 'Perplexity',
                  'other' => 'その他',
              ),
              'allow_custom' => true,
              'save_custom' => true,
              'layout' => 'vertical',
              'toggle' => 0,
              'return_format' => 'value',
              'parent' => $lesson_info_group['key'],
          ));
      } else {
          // フィールドグループが見つからない場合は新規作成
          acf_add_local_field_group(array(
              'key' => 'group_lesson_ai_tools',
              'title' => 'プロンプト情報',
              'fields' => array(
                  array(
                      'key' => 'field_recommended_ai_tools',
                      'label' => '推奨するAIツール',
                      'name' => 'recommended_ai_tools',
                      'type' => 'checkbox',
                      'instructions' => 'このプロンプトで推奨するAIツールを選択してください（複数選択可）',
                      'required' => 0,
                      'choices' => array(
                          'chatgpt' => 'ChatGPT',
                          'claude' => 'Claude',
                          'gemini' => 'Google Gemini',
                          'copilot' => 'Microsoft Copilot',
                          'midjourney' => 'Midjourney',
                          'dall-e' => 'DALL-E',
                          'stable-diffusion' => 'Stable Diffusion',
                          'perplexity' => 'Perplexity',
                          'other' => 'その他',
                      ),
                      'allow_custom' => true,
                      'save_custom' => true,
                      'layout' => 'vertical',
                      'toggle' => 0,
                      'return_format' => 'value',
                  ),
              ),
              'location' => array(
                  array(
                      array(
                          'param' => 'post_type',
                          'operator' => '==',
                          'value' => 'lesson',
                      ),
                  ),
              ),
              'menu_order' => 0,
              'position' => 'normal',
              'style' => 'default',
              'label_placement' => 'top',
              'instruction_placement' => 'label',
              'hide_on_screen' => '',
              'active' => true,
              'description' => '',
              'show_in_rest' => 0,
          ));
      }
  } else {
      // ACFが有効でない場合は、通常のカスタムフィールドとして追加
      add_action('add_meta_boxes', 'add_recommended_ai_tools_meta_box');
      add_action('save_post', 'save_recommended_ai_tools_meta_data');
  }
}

add_action('acf/init', 'add_recommended_ai_tools_field');

// ACFがない場合のフォールバック：メタボックスを追加
function add_recommended_ai_tools_meta_box() {
  add_meta_box(
      'recommended_ai_tools_meta_box',
      '推奨するAIツール',
      'render_recommended_ai_tools_meta_box',
      'lesson',
      'normal',
      'default'
  );
}

// ACFがない場合のフォールバック：メタボックスのレンダリング
function render_recommended_ai_tools_meta_box($post) {
  wp_nonce_field('recommended_ai_tools_nonce', 'recommended_ai_tools_nonce');

  $ai_tools = get_post_meta($post->ID, 'recommended_ai_tools', true);
  $ai_tools = !empty($ai_tools) ? $ai_tools : array();

  $ai_tool_options = array(
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

  echo '<p>このレッスンで推奨するAIツールを選択してください（複数選択可）</p>';
  echo '<div style="margin-bottom: 10px;">';

  foreach ($ai_tool_options as $value => $label) {
      $checked = in_array($value, $ai_tools) ? 'checked="checked"' : '';
      echo '<div style="margin-bottom: 5px;">';
      echo '<label>';
      echo '<input type="checkbox" name="recommended_ai_tools[]" value="' . esc_attr($value) . '" ' . $checked . '> ';
      echo esc_html($label);
      echo '</label>';
      echo '</div>';
  }

  // その他のツールを追加するためのテキストフィールド
  echo '<div style="margin-top: 10px;">';
  echo '<label for="other_ai_tool">その他のAIツール（カンマ区切りで入力）:</label><br>';
  echo '<input type="text" id="other_ai_tool" name="other_ai_tool" value="' . esc_attr(get_post_meta($post->ID, 'other_ai_tool', true)) . '" style="width: 100%;">';
  echo '</div>';

  echo '</div>';
}

// ACFがない場合のフォールバック：メタデータの保存
function save_recommended_ai_tools_meta_data($post_id) {
  if (!isset($_POST['recommended_ai_tools_nonce']) || !wp_verify_nonce($_POST['recommended_ai_tools_nonce'], 'recommended_ai_tools_nonce')) {
      return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
  }

  if (!current_user_can('edit_post', $post_id)) {
      return;
  }

  // 推奨AIツールの保存
  if (isset($_POST['recommended_ai_tools'])) {
      update_post_meta($post_id, 'recommended_ai_tools', $_POST['recommended_ai_tools']);
  } else {
      update_post_meta($post_id, 'recommended_ai_tools', array());
  }

  // その他のAIツールの保存
  if (isset($_POST['other_ai_tool'])) {
      update_post_meta($post_id, 'other_ai_tool', sanitize_text_field($_POST['other_ai_tool']));
  }
}

// 監修者選択用のカスタムフィールドを追加
function add_prompt_author_field() {
  if (function_exists('acf_add_local_field_group')) {
      acf_add_local_field_group(array(
          'key' => 'group_prompt_author',
          'title' => 'プロンプト監修情報',
          'fields' => array(
              array(
                  'key' => 'field_prompt_author',
                  'label' => '監修者',
                  'name' => 'prompt_author',
                  'type' => 'user',
                  'instructions' => 'このプロンプトの作成者または監修者を選択してください',
                  'required' => 0,
                  'role' => '',
                  'allow_null' => 1,
                  'multiple' => 0,
                  'return_format' => 'array',
              ),
              array(
                  'key' => 'field_prompt_author_title',
                  'label' => '監修者肩書き',
                  'name' => 'prompt_author_title',
                  'type' => 'text',
                  'instructions' => '監修者の肩書きや役職など（例：AIエンジニア、プロンプトエンジニアなど）',
                  'required' => 0,
                  'conditional_logic' => array(
                      array(
                          array(
                              'field' => 'field_prompt_author',
                              'operator' => '!=empty',
                          ),
                      ),
                  ),
              ),
              array(
                  'key' => 'field_prompt_author_description',
                  'label' => '監修者プロフィール',
                  'name' => 'prompt_author_description',
                  'type' => 'textarea',
                  'instructions' => '監修者の簡単なプロフィールや経歴（100文字程度）',
                  'required' => 0,
                  'rows' => 3,
                  'new_lines' => 'br',
                  'conditional_logic' => array(
                      array(
                          array(
                              'field' => 'field_prompt_author',
                              'operator' => '!=empty',
                          ),
                      ),
                  ),
              ),
          ),
          'location' => array(
              array(
                  array(
                      'param' => 'post_type',
                      'operator' => '==',
                      'value' => 'lesson', // レッスン（プロンプト）投稿タイプ
                  ),
              ),
          ),
          'menu_order' => 10,
          'position' => 'normal',
          'style' => 'default',
          'label_placement' => 'top',
          'instruction_placement' => 'label',
          'hide_on_screen' => '',
          'active' => true,
          'description' => '',
          'show_in_rest' => 0,
      ));
  } else {
      // ACFが有効でない場合は通常のメタボックスを追加
      add_action('add_meta_boxes', 'add_prompt_author_meta_box');
      add_action('save_post', 'save_prompt_author_meta_data');
  }
}
add_action('acf/init', 'add_prompt_author_field');

// ACFがない場合のフォールバック：メタボックスを追加
function add_prompt_author_meta_box() {
  add_meta_box(
      'prompt_author_meta_box',
      'プロンプト監修情報',
      'render_prompt_author_meta_box',
      'lesson', // レッスン（プロンプト）投稿タイプ
      'normal',
      'default'
  );
}

// ACFがない場合のフォールバック：メタボックスのレンダリング
function render_prompt_author_meta_box($post) {
  wp_nonce_field('prompt_author_nonce', 'prompt_author_nonce');

  // 既存のデータを取得
  $author_id = get_post_meta($post->ID, 'prompt_author', true);
  $author_title = get_post_meta($post->ID, 'prompt_author_title', true);
  $author_description = get_post_meta($post->ID, 'prompt_author_description', true);

  // ユーザー選択ドロップダウン
  $users = get_users(array('role__in' => array('administrator', 'editor', 'author', 'contributor')));
  ?>
  <p>
      <label for="prompt_author">監修者:</label><br>
      <select name="prompt_author" id="prompt_author">
          <option value="">-- 選択してください --</option>
          <?php foreach ($users as $user) : ?>
              <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($author_id, $user->ID); ?>>
                  <?php echo esc_html($user->display_name); ?> (<?php echo esc_html($user->user_login); ?>)
              </option>
          <?php endforeach; ?>
      </select>
  </p>
  <p>
      <label for="prompt_author_title">監修者肩書き:</label><br>
      <input type="text" id="prompt_author_title" name="prompt_author_title" value="<?php echo esc_attr($author_title); ?>" class="widefat">
      <span class="description">監修者の肩書きや役職など（例：AIエンジニア、プロンプトエンジニアなど）</span>
  </p>
  <p>
      <label for="prompt_author_description">監修者プロフィール:</label><br>
      <textarea id="prompt_author_description" name="prompt_author_description" class="widefat" rows="3"><?php echo esc_textarea($author_description); ?></textarea>
      <span class="description">監修者の簡単なプロフィールや経歴（100文字程度）</span>
  </p>
  <?php
}

// ACFがない場合のフォールバック：メタデータの保存
function save_prompt_author_meta_data($post_id) {
  if (!isset($_POST['prompt_author_nonce']) || !wp_verify_nonce($_POST['prompt_author_nonce'], 'prompt_author_nonce')) {
      return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
  }

  if (!current_user_can('edit_post', $post_id)) {
      return;
  }

  // 監修者IDの保存
  if (isset($_POST['prompt_author'])) {
      update_post_meta($post_id, 'prompt_author', sanitize_text_field($_POST['prompt_author']));
  } else {
      delete_post_meta($post_id, 'prompt_author');
  }

  // 監修者肩書きの保存
  if (isset($_POST['prompt_author_title'])) {
      update_post_meta($post_id, 'prompt_author_title', sanitize_text_field($_POST['prompt_author_title']));
  }

  // 監修者プロフィールの保存
  if (isset($_POST['prompt_author_description'])) {
      update_post_meta($post_id, 'prompt_author_description', sanitize_textarea_field($_POST['prompt_author_description']));
  }
}

// 監修者によるプロンプト一覧を表示するショートコード
function prompt_author_list_shortcode($atts) {
  $atts = shortcode_atts(array(
      'author_id' => 0,
      'limit' => 6,
      'columns' => 3,
  ), $atts, 'prompt_author_list');

  $author_id = intval($atts['author_id']);
  $limit = intval($atts['limit']);
  $columns = intval($atts['columns']);

  if ($author_id <= 0) {
      return '<p>有効なauthor_idを指定してください。</p>';
  }

  // 監修者情報の取得
  $author = get_userdata($author_id);
  if (!$author) {
      return '<p>指定されたIDの監修者が見つかりません。</p>';
  }

  // 該当する監修者のプロンプトを取得
  $args = array(
      'post_type' => 'lesson',
      'posts_per_page' => $limit,
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_query' => array(
          array(
              'key' => 'prompt_author',
              'value' => $author_id,
              'compare' => '=',
          ),
      ),
  );

  $prompt_query = new WP_Query($args);

  ob_start();

  if ($prompt_query->have_posts()) : ?>
      <div class="prompt-author-list-section">
          <h2 class="prompt-author-list-title"><?php echo esc_html($author->display_name); ?>のプロンプト</h2>
          <div class="prompt-grid columns-<?php echo esc_attr($columns); ?>">

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
                          <!-- タイトル -->
                          <h3 class="prompt-card-title">
                              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                          </h3>

                          <!-- 抜粋 -->
                          <div class="prompt-card-excerpt">
                              <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                          </div>

                          <!-- フッター -->
                          <div class="prompt-card-footer">
                              <span class="prompt-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                              <a href="<?php the_permalink(); ?>" class="prompt-read-more">詳細を見る</a>
                          </div>

                      </div><!-- .prompt-card-content -->
                  </div><!-- .prompt-card -->
              <?php endwhile; ?>

          </div><!-- .prompt-grid -->
      </div><!-- .prompt-author-list-section -->

      <?php wp_reset_postdata();
  else : ?>
      <p>この監修者によるプロンプトはまだありません。</p>
  <?php endif;

  return ob_get_clean();
}
add_shortcode('prompt_author_list', 'prompt_author_list_shortcode');


// 完成例のカスタムフィールドを追加
function add_completion_example_field() {
  if (function_exists('acf_add_local_field_group')) {
      acf_add_local_field_group(array(
          'key' => 'group_completion_example',
          'title' => 'コース完成例',
          'fields' => array(
              array(
                  'key' => 'field_completion_example',
                  'label' => '完成の参考画像',
                  'name' => 'completion_example',
                  'type' => 'image',
                  'instructions' => 'コース完了後の成果物の参考となる画像をアップロードしてください',
                  'required' => 0,
                  'return_format' => 'url',
                  'preview_size' => 'medium',
                  'library' => 'all',
              ),
          ),
          'location' => array(
              array(
                  array(
                      'param' => 'post_type',
                      'operator' => '==',
                      'value' => 'course',
                  ),
              ),
          ),
          'menu_order' => 0,
          'position' => 'side',
          'style' => 'default',
          'label_placement' => 'top',
          'instruction_placement' => 'label',
          'hide_on_screen' => '',
          'active' => true,
          'description' => '',
          'show_in_rest' => 0,
      ));
  }
}
add_action('acf/init', 'add_completion_example_field');
