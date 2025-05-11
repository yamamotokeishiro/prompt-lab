<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="20" cy="22" rx="16" ry="18" fill="#1294BE"/>
                        <ellipse cx="20" cy="25" rx="12" ry="13" fill="#FFFFFF"/>
                        <ellipse cx="20" cy="20" rx="6" ry="7" fill="#13488D"/>
                        <circle cx="18" cy="18" r="1.5" fill="white"/>
                        <circle cx="22" cy="18" r="1.5" fill="white"/>
                        <path d="M20 7 L20 3 C20 2 23 2 23 3 L23 7" stroke="#13488D" stroke-width="2"/>
                        <path d="M16 11 L16 8 C12 3 8 8 12 11" fill="#FFAA55"/>
                        <path d="M24 11 L24 8 C28 3 32 8 28 11" fill="#FFAA55"/>
                    </svg>
                </a>
                <div class="logo-text">PENGIN AI</div>
                <p>AIについての学習プラットフォーム。最新の技術を分かりやすく学ぶことができます。</p>
            </div>

            <div class="footer-navigation">
                <h4>サイトマップ</h4>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-menu',
                    'container' => false,
                    'menu_class' => 'footer-menu',
                    'fallback_cb' => function() {
                        echo '<ul class="footer-menu">';
                        echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
                        echo '<li><a href="#">コース一覧</a></li>';
                        echo '<li><a href="#">PENGIN AIとは</a></li>';
                        echo '<li><a href="#">学習の流れ</a></li>';
                        echo '<li><a href="#">お問い合わせ</a></li>';
                        echo '</ul>';
                    }
                ));
                ?>
            </div>

            <div class="footer-contact">
                <h4>お問い合わせ</h4>
                <p>AIについて学びたい方は<br>お気軽にご連絡ください</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="footer-contact-btn">お問い合わせフォーム</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PENGIN AI. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
