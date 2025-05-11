<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="site-header-inner">
            <!-- サイトロゴ（左寄せ） -->
            <div class="site-branding">
                <?php if ( has_custom_logo() ) : ?>
                    <div class="site-logo"><?php the_custom_logo(); ?></div>
                <?php else : ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            プロンプトラボ <span class="by-pengin">by PENGIN</span>
                        </a>
                    </h1>
                <?php endif; ?>
            </div>

            <!-- ヘッダー右側のグループ -->
            <div class="header-right-group">
                <!-- ナビゲーションメニュー -->
                <nav class="main-navigation" id="site-navigation">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-icon"></span>
                        <span class="screen-reader-text">メニュー</span>
                    </button>
                    <?php
                    // WordPressメニューを表示
                    wp_nav_menu(array(
                        'theme_location' => 'header-menu',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'primary-menu',
                        'container'      => 'div',
                        'container_class' => 'menu-container',
                        'fallback_cb'    => false, // メニューがない場合は何も表示しない
                    ));
                    ?>
                </nav>

                <!-- 右側のCTAボタン -->
                <div class="header-cta-buttons">
                    <a href="#" class="header-cta-btn cta-contact">お問い合わせ</a>
                    <a href="#" class="header-cta-btn cta-request">プロンプト作成依頼</a>
                </div>
            </div>
        </div>
    </div>
</header>
