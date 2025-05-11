<?php
/**
 * 固定ページのテンプレート
 *
 * @package PENGIN_AI
 */

get_header();
?>

<div class="page-content-wrapper">
    <div class="container">
        <main id="main" class="page-main-content">
            <?php
            while ( have_posts() ) :
                the_post();
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-article'); ?>>
                    <header class="page-header">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="page-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </main>
    </div>
</div>

<?php
get_footer();
