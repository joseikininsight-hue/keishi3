<?php
/**
 * Archive Column Template - Yahoo! News Style Complete with AJAX Tab Switching
 * コラム記事一覧ページ - Yahoo!ニュース完全版（AJAXタブ切り替え対応）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 6.0.3 - AJAX Tab Switching (Fix card layout consistency)
 * 
 * Features:
 * - Yahoo! JAPAN style category tab switching (no page reload)
 * - Real-time topics feed update via REST API
 * - Smooth fade animations
 * - Instant article list updates with consistent card layout
 * - Browser history management
 * 
 * REST API Configuration:
 * - Post Type: 'column' with rest_base 'columns'
 * - Taxonomy: 'column_category' with rest_base 'column-categories'
 * - Endpoints: /wp-json/wp/v2/columns?column-categories={term_id}
 * - IMPORTANT: Must use term ID (integer), not slug (string)
 * 
 * Card Layout:
 * - Uses column-card-compact class (matches template-parts/column/card.php)
 * - Consistent styling between server-rendered and AJAX-loaded cards
 */

get_header();

// カテゴリ一覧を取得
$categories = get_terms(array(
    'taxonomy' => 'column_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
));
if (is_wp_error($categories)) {
    $categories = array();
}

$current_category = get_queried_object();
$is_category = is_tax('column_category');
$is_tag = is_tax('column_tag');

// 総件数取得
$total_count = wp_count_posts('column')->publish;

// ソート取得
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// 注目記事を取得（閲覧数トップ3）
$featured_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 3,
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_status' => 'publish',
));

// アクセスランキング（トップ10）
$ranking_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 10,
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_status' => 'publish',
));

// トレンドキーワード（タグから取得）
$trending_tags = get_terms(array(
    'taxonomy' => 'column_tag',
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10,
    'hide_empty' => true,
));

// 新着記事（最新20件）
$latest_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 20,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
));
?>

<!-- Yahoo!風アーカイブページ Enhanced -->
<div class="archive-column-yahoo-v3">
    
    <!-- ヘッダーセクション -->
    <header class="archive-header">
        <div class="archive-header-wrapper">
            
            <!-- パンくずリスト -->
            <nav class="breadcrumb" aria-label="パンくず">
                <ol class="breadcrumb-list">
                    <li><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                    <li><i class="fas fa-chevron-right"></i></li>
                    <li><a href="<?php echo get_post_type_archive_link('column'); ?>">コラム</a></li>
                    <?php if ($is_category || $is_tag): ?>
                        <li><i class="fas fa-chevron-right"></i></li>
                        <li aria-current="page"><?php single_term_title(); ?></li>
                    <?php endif; ?>
                </ol>
            </nav>

            <!-- タイトルエリア -->
            <div class="header-title-area">
                <div class="title-content">
                    <div class="icon-title">
                        <i class="fas fa-newspaper"></i>
                        <h1>
                            <?php
                            if ($is_category || $is_tag) {
                                single_term_title();
                            } else {
                                echo '補助金コラム';
                            }
                            ?>
                        </h1>
                    </div>
                    
                    <p class="header-description">
                        <?php 
                        if ($is_category && $current_category->description) {
                            echo esc_html($current_category->description);
                        } elseif (!$is_category && !$is_tag) {
                            echo '補助金活用のヒントやノウハウ、最新情報をお届けします。';
                        }
                        ?>
                    </p>
                </div>
                
                <!-- 総件数表示 -->
                <div class="total-count">
                    <span class="count-number"><?php echo number_format($total_count); ?></span>
                    <span class="count-label">件</span>
                </div>
            </div>

        </div>
    </header>

    <!-- メインコンテンツ（2カラムレイアウト） -->
    <div class="archive-main">
        <div class="archive-wrapper-two-column">
            
            <!-- 左カラム：メインコンテンツ -->
            <div class="main-content-column">
            
            <!-- 検索・ソート バー -->
            <div class="control-bar">
                <!-- 検索フォーム -->
                <form class="search-form" method="get" action="<?php echo esc_url(get_post_type_archive_link('column')); ?>">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               name="s" 
                               placeholder="キーワードで検索..." 
                               value="<?php echo esc_attr($search_query); ?>"
                               class="search-input">
                        <?php if ($is_category): ?>
                        <input type="hidden" name="column_category" value="<?php echo esc_attr($current_category->slug); ?>">
                        <?php endif; ?>
                        <button type="submit" class="search-btn">
                            検索
                        </button>
                    </div>
                </form>
                
                <!-- ソートドロップダウン -->
                <div class="sort-dropdown">
                    <label for="sort-select">
                        <i class="fas fa-sort"></i>
                        並び順:
                    </label>
                    <select id="sort-select" class="sort-select">
                        <option value="date" <?php selected($orderby, 'date'); ?>>新着順</option>
                        <option value="popular" <?php selected($orderby, 'popular'); ?>>人気順</option>
                        <option value="title" <?php selected($orderby, 'title'); ?>>タイトル順</option>
                    </select>
                </div>
            </div>

            <!-- Yahoo!風タブフィルター -->
            <?php if (!empty($categories) && !$is_tag): ?>
                <nav class="category-tabs" role="navigation" aria-label="カテゴリフィルター">
                    <div class="tabs-scroll-container">
                        <button type="button" 
                                data-category="" 
                                class="tab-btn <?php echo (!$is_category) ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i>
                            <span>すべて</span>
                            <span class="tab-count"><?php echo number_format($total_count); ?></span>
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button type="button"
                                    data-category-id="<?php echo esc_attr($category->term_id); ?>" 
                                    data-category-slug="<?php echo esc_attr($category->slug); ?>"
                                    class="tab-btn <?php echo ($is_category && $current_category->term_id === $category->term_id) ? 'active' : ''; ?>">
                                <i class="fas fa-folder"></i>
                                <span><?php echo esc_html($category->name); ?></span>
                                <span class="tab-count"><?php echo number_format($category->count); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- 新着トピック欄（Yahoo!風） -->
            <div class="topics-section">
                <h2 class="topics-title">
                    <i class="fas fa-bolt"></i>
                    新着トピック
                </h2>
                <div id="topics-list-container">
                    <?php if ($latest_query->have_posts()): ?>
                    <ul class="topics-list">
                        <?php 
                        while ($latest_query->have_posts()): 
                            $latest_query->the_post();
                            $time_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                        ?>
                            <li class="topic-item">
                                <a href="<?php the_permalink(); ?>" class="topic-link">
                                    <span class="topic-time"><?php echo $time_ago; ?>前</span>
                                    <span class="topic-title"><?php the_title(); ?></span>
                                    <?php
                                    $cats = get_the_terms(get_the_ID(), 'column_category');
                                    if ($cats && !is_wp_error($cats)):
                                    ?>
                                    <span class="topic-category"><?php echo esc_html($cats[0]->name); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                    <?php else: ?>
                    <p class="no-topics">このカテゴリの記事はまだありません。</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 記事リスト -->
            <div class="articles-container" id="articles-container">
                <!-- ローディング表示 -->
                <div id="loading-indicator" class="loading-indicator" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p>読み込み中...</p>
                </div>
                
                <div id="articles-list-wrapper">
                    <?php if (have_posts()): ?>
                        <div class="articles-list">
                            <?php while (have_posts()): the_post(); ?>
                                <?php get_template_part('template-parts/column/card'); ?>
                            <?php endwhile; ?>
                        </div>

                        <!-- ページネーション -->
                        <nav class="pagination-nav" aria-label="ページネーション">
                            <?php
                            $pagination = paginate_links(array(
                                'prev_text' => '<i class="fas fa-chevron-left"></i> 前へ',
                                'next_text' => '次へ <i class="fas fa-chevron-right"></i>',
                                'type' => 'array',
                                'mid_size' => 2,
                                'end_size' => 1,
                            ));
                            
                            if ($pagination) {
                                echo '<ul class="pagination-list">';
                                foreach ($pagination as $page) {
                                    echo '<li>' . $page . '</li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </nav>

                    <?php else: ?>
                        <!-- 記事なしメッセージ -->
                        <div class="no-posts">
                            <i class="fas fa-search"></i>
                            <h2>記事が見つかりませんでした</h2>
                            <p>条件に一致する記事がありません。</p>
                            <a href="<?php echo get_post_type_archive_link('column'); ?>" class="back-btn">
                                <i class="fas fa-arrow-left"></i>
                                すべての記事を見る
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            </div>
            <!-- /main-content-column -->
            
            <!-- 右カラム：サイドバー -->
            <aside class="sidebar-column">
                
                <?php
                // 広告: サイドバー上部
                if (function_exists('ji_display_ad')): ?>
                    <div class="sidebar-ad-space sidebar-ad-top">
                        <?php ji_display_ad('archive_column_sidebar_top', 'archive-column'); ?>
                    </div>
                <?php endif; ?>
                
                <!-- 注目記事 -->
                <?php if ($featured_query->have_posts()): ?>
                <div class="sidebar-widget featured-widget">
                    <h2 class="widget-title">
                        <i class="fas fa-fire"></i>
                        注目記事
                    </h2>
                    <div class="widget-content">
                        <div class="featured-sidebar-list">
                            <?php while ($featured_query->have_posts()): $featured_query->the_post(); ?>
                                <article class="featured-sidebar-item">
                                    <a href="<?php the_permalink(); ?>" class="featured-sidebar-link">
                                        <?php if (has_post_thumbnail()): ?>
                                        <div class="featured-sidebar-thumb">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="featured-sidebar-content">
                                            <h3 class="featured-sidebar-title"><?php the_title(); ?></h3>
                                            <div class="featured-sidebar-meta">
                                                <span><?php echo get_the_date('n/j'); ?></span>
                                                <?php
                                                $views = get_field('view_count', get_the_ID());
                                                if ($views && $views > 0):
                                                ?>
                                                <span><i class="fas fa-eye"></i> <?php echo number_format($views); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- PR欄（アフィリエイト広告用） -->
                <div class="sidebar-widget pr-widget">
                    <h2 class="widget-title">
                        <i class="fas fa-ad"></i>
                        PR
                    </h2>
                    <div class="widget-content">
                        <div class="pr-content">
                            <?php if (function_exists('ji_display_ad')): ?>
                                <?php ji_display_ad('archive_column_sidebar_pr', 'archive-column'); ?>
                            <?php else: ?>
                                <p class="pr-placeholder">広告スペース</p>
                                <p class="pr-note">※ここにアフィリエイト広告が入ります</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- アクセスランキング -->
                <div class="sidebar-widget ranking-widget">
                    <h2 class="widget-title">
                        <i class="fas fa-chart-line"></i>
                        アクセスランキング
                    </h2>
                    <div class="widget-content">
                        <?php if ($ranking_query->have_posts()): ?>
                            <ol class="ranking-list">
                                <?php 
                                $rank = 1;
                                while ($ranking_query->have_posts()): 
                                    $ranking_query->the_post();
                                    $rank_class = ($rank <= 3) ? 'rank-top' : '';
                                ?>
                                    <li class="ranking-item <?php echo $rank_class; ?>">
                                        <a href="<?php the_permalink(); ?>">
                                            <span class="rank-number"><?php echo $rank; ?></span>
                                            <div class="rank-content">
                                                <?php if (has_post_thumbnail() && $rank <= 3): ?>
                                                <div class="rank-thumb">
                                                    <?php the_post_thumbnail('thumbnail'); ?>
                                                </div>
                                                <?php endif; ?>
                                                <div class="rank-text">
                                                    <h3 class="rank-title"><?php the_title(); ?></h3>
                                                    <div class="rank-meta">
                                                        <span class="rank-date"><?php echo get_the_date('n/j'); ?></span>
                                                        <?php
                                                        $views = get_field('view_count', get_the_ID());
                                                        if ($views && $views > 0):
                                                        ?>
                                                        <span class="rank-views">
                                                            <i class="fas fa-eye"></i>
                                                            <?php echo number_format($views); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php 
                                    $rank++;
                                endwhile; 
                                wp_reset_postdata();
                                ?>
                            </ol>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- トレンドキーワード -->
                <?php if (!empty($trending_tags) && !is_wp_error($trending_tags)): ?>
                <div class="sidebar-widget trend-widget">
                    <h2 class="widget-title">
                        <i class="fas fa-fire"></i>
                        トレンドキーワード
                    </h2>
                    <div class="widget-content">
                        <div class="trend-tags">
                            <?php foreach ($trending_tags as $index => $tag): ?>
                                <a href="<?php echo get_term_link($tag); ?>" class="trend-tag">
                                    <span class="trend-rank"><?php echo ($index + 1); ?></span>
                                    <span class="trend-name"><?php echo esc_html($tag->name); ?></span>
                                    <span class="trend-count"><?php echo number_format($tag->count); ?>件</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- カテゴリ一覧 -->
                <?php if (!empty($categories)): ?>
                <div class="sidebar-widget category-widget">
                    <h2 class="widget-title">
                        <i class="fas fa-folder-open"></i>
                        カテゴリ
                    </h2>
                    <div class="widget-content">
                        <ul class="category-list">
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="<?php echo get_term_link($cat); ?>">
                                        <span class="cat-name"><?php echo esc_html($cat->name); ?></span>
                                        <span class="cat-count"><?php echo number_format($cat->count); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php
                // 広告: サイドバー下部
                if (function_exists('ji_display_ad')): ?>
                    <div class="sidebar-ad-space sidebar-ad-bottom">
                        <?php ji_display_ad('archive_column_sidebar_bottom', 'archive-column'); ?>
                    </div>
                <?php endif; ?>
                
            </aside>
            <!-- /sidebar-column -->

        </div>
    </div>

    <!-- トップに戻るボタン -->
    <button class="back-to-top" id="backToTop" aria-label="トップに戻る">
        <i class="fas fa-chevron-up"></i>
    </button>

</div>

<?php get_footer(); ?>

<style>
/* ============================================
   Archive Column - Yahoo! News Style v3.0
   白黒スタイリッシュデザイン
   ============================================ */

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-900: #171717;
    --color-blue: #0066cc;
}

.archive-column-yahoo-v3 {
    background: var(--color-gray-50);
    min-height: 100vh;
}

/* ============================================
   ヘッダーセクション
   ============================================ */
.archive-header {
    background: var(--color-secondary);
    border-bottom: 4px solid var(--color-primary);
    padding: 32px 0 24px;
}

.archive-header-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

/* パンくずリスト */
.breadcrumb {
    margin-bottom: 24px;
}

.breadcrumb-list {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    font-size: 14px;
    color: var(--color-gray-600);
    flex-wrap: wrap;
}

.breadcrumb-list a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-list a:hover {
    color: var(--color-primary);
}

.breadcrumb-list i {
    font-size: 10px;
    color: var(--color-gray-300);
}

.breadcrumb-list li:last-child {
    color: var(--color-gray-900);
    font-weight: 600;
}

/* タイトルエリア */
.header-title-area {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.title-content {
    flex: 1;
}

.icon-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.icon-title i {
    font-size: 36px;
    color: var(--color-primary);
}

.icon-title h1 {
    font-size: 32px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0;
    letter-spacing: -0.5px;
}

.header-description {
    font-size: 15px;
    color: var(--color-gray-600);
    line-height: 1.6;
    margin: 0;
}

/* 総件数表示 */
.total-count {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 24px;
    background: var(--color-primary);
    color: var(--color-accent);
    border-radius: 8px;
}

.count-number {
    font-size: 32px;
    font-weight: 900;
    line-height: 1;
}

.count-label {
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
}

/* ============================================
   新着トピック欄（Yahoo!風）
   ============================================ */
.topics-section {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
    margin-bottom: 32px;
}

.topics-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    padding: 16px 20px;
    font-size: 18px;
    font-weight: 900;
    color: var(--color-secondary);
    background: var(--color-primary);
    border-bottom: 3px solid var(--color-accent);
}

.topics-title i {
    color: var(--color-accent);
    font-size: 20px;
}

.topics-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.topic-item {
    border-bottom: 1px solid var(--color-gray-200);
}

.topic-item:last-child {
    border-bottom: none;
}

.topic-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s;
}

.topic-link:hover {
    background: var(--color-gray-50);
}

.topic-time {
    display: inline-block;
    min-width: 80px;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-blue);
}

.topic-title {
    flex: 1;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-gray-900);
}

.topic-link:hover .topic-title {
    color: var(--color-blue);
}

.topic-category {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: var(--color-secondary);
    background: var(--color-gray-600);
    border-radius: 3px;
}

/* ============================================
   メインコンテンツ
   ============================================ */
.archive-main {
    padding: 32px 0 64px;
}

.archive-wrapper-two-column {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 1024px) {
    .archive-wrapper-two-column {
        grid-template-columns: 1fr 360px;
        gap: 32px;
    }
}

.main-content-column {
    min-width: 0;
}

/* 検索・ソートバー */
.control-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    align-items: center;
}

.search-form {
    flex: 1;
    min-width: 280px;
}

.search-input-group {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    padding: 8px 12px;
    border-radius: 4px;
}

.search-input-group i {
    color: var(--color-gray-600);
    font-size: 16px;
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 15px;
    padding: 6px 8px;
    background: transparent;
}

.search-btn {
    padding: 8px 20px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: none;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border-radius: 3px;
    transition: all 0.2s;
}

.search-btn:hover {
    background: var(--color-gray-900);
    transform: translateY(-1px);
}

.sort-dropdown {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    border-radius: 4px;
}

.sort-dropdown label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-900);
}

.sort-select {
    border: 1px solid var(--color-gray-300);
    padding: 6px 10px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 3px;
    cursor: pointer;
    background: var(--color-secondary);
}

.sort-select:focus {
    outline: none;
    border-color: var(--color-primary);
}

/* Yahoo!風タブフィルター */
.category-tabs {
    margin-bottom: 32px;
    border-bottom: 4px solid var(--color-primary);
    overflow: hidden;
}

.tabs-scroll-container {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    margin-bottom: -4px;
}

.tabs-scroll-container::-webkit-scrollbar {
    height: 6px;
}

.tabs-scroll-container::-webkit-scrollbar-track {
    background: var(--color-gray-100);
}

.tabs-scroll-container::-webkit-scrollbar-thumb {
    background: var(--color-gray-600);
    border-radius: 3px;
}

.tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 700;
    color: var(--color-gray-700);
    background: var(--color-gray-100);
    border: none;
    border-bottom: 4px solid transparent;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.25s;
    cursor: pointer;
}

.tab-btn:hover {
    background: var(--color-gray-200);
    color: var(--color-primary);
}

.tab-btn.active {
    color: var(--color-primary);
    background: var(--color-secondary);
    border-bottom-color: var(--color-accent);
    font-weight: 900;
}

/* ローディング表示 */
.loading-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: var(--color-white);
    border-radius: 8px;
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-indicator p {
    color: var(--color-gray-600);
    font-size: 14px;
    font-weight: 600;
}

/* フェードアニメーション */
.fade-in {
    animation: fadeIn 0.4s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.no-topics {
    padding: 40px 20px;
    text-align: center;
    color: var(--color-gray-600);
    font-size: 14px;
}

.tab-btn i {
    font-size: 18px;
}

.tab-btn .tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 22px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 700;
    color: var(--color-secondary);
    background: var(--color-gray-700);
    border-radius: 11px;
}

.tab-btn.active .tab-count {
    background: var(--color-primary);
    color: var(--color-accent);
}

/* 記事リスト */
.articles-container {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
    min-height: 400px;
}

.articles-list {
    /* カードの区切り線はcard.php内で管理 */
}

/* ページネーション */
.pagination-nav {
    margin-top: 0;
    padding: 32px 24px;
    border-top: 3px solid var(--color-primary);
}

.pagination-list {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    list-style: none;
    flex-wrap: wrap;
}

.pagination-list a,
.pagination-list span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 16px;
    font-size: 15px;
    font-weight: 600;
    border: 2px solid var(--color-primary);
    background: var(--color-secondary);
    color: var(--color-primary);
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-list a:hover {
    background: var(--color-accent);
    transform: translateY(-2px);
}

.pagination-list .current {
    background: var(--color-primary);
    color: var(--color-accent);
    font-weight: 900;
}

.pagination-list .dots {
    border: none;
    background: none;
    color: var(--color-gray-600);
}

/* 記事なしメッセージ */
.no-posts {
    text-align: center;
    padding: 80px 24px;
}

.no-posts i {
    font-size: 64px;
    color: var(--color-gray-300);
    margin-bottom: 24px;
}

.no-posts h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-gray-900);
    margin: 0 0 12px;
}

.no-posts p {
    font-size: 16px;
    color: var(--color-gray-600);
    margin: 0 0 32px;
}

.no-posts .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    font-size: 16px;
    font-weight: 700;
    color: var(--color-secondary);
    background: var(--color-primary);
    border: 2px solid var(--color-primary);
    text-decoration: none;
    transition: all 0.3s;
}

.no-posts .back-btn:hover {
    background: var(--color-secondary);
    color: var(--color-primary);
}

/* ============================================
   サイドバー
   ============================================ */
.sidebar-column {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

@media (min-width: 1024px) {
    .sidebar-column {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        align-self: flex-start;
    }
}

/* ウィジェット共通 */
.sidebar-widget {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
    overflow: hidden;
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    padding: 16px 20px;
    font-size: 18px;
    font-weight: 900;
    color: var(--color-secondary);
    background: var(--color-primary);
    border-bottom: 3px solid var(--color-accent);
}

.widget-title i {
    font-size: 20px;
    color: var(--color-accent);
}

.widget-content {
    padding: 0;
}

/* アクセスランキング */
.ranking-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ranking-item {
    border-bottom: 1px solid var(--color-gray-200);
}

.ranking-item:last-child {
    border-bottom: none;
}

.ranking-item a {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s;
}

.ranking-item a:hover {
    background: var(--color-gray-50);
}

.rank-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    font-size: 16px;
    font-weight: 900;
    color: var(--color-gray-600);
    background: var(--color-gray-100);
    border-radius: 4px;
    flex-shrink: 0;
}

.rank-top .rank-number {
    background: var(--color-primary);
    color: var(--color-accent);
    font-size: 18px;
}

.rank-content {
    flex: 1;
    min-width: 0;
    display: flex;
    gap: 10px;
}

.rank-thumb {
    width: 60px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 3px;
    overflow: hidden;
    background: var(--color-gray-100);
}

.rank-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rank-text {
    flex: 1;
    min-width: 0;
}

.rank-title {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    color: var(--color-gray-900);
    margin: 0 0 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ranking-item:hover .rank-title {
    color: var(--color-blue);
}

.rank-meta {
    display: flex;
    gap: 10px;
    font-size: 11px;
    color: var(--color-gray-600);
}

.rank-meta span {
    display: flex;
    align-items: center;
    gap: 3px;
}

.rank-views i {
    color: var(--color-blue);
}

/* トレンドキーワード */
.trend-tags {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.trend-tag {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.trend-tag:hover {
    background: var(--color-gray-100);
    border-color: var(--color-primary);
    transform: translateX(4px);
}

.trend-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    font-size: 13px;
    font-weight: 900;
    color: var(--color-secondary);
    background: var(--color-gray-600);
    border-radius: 12px;
}

.trend-tag:nth-child(1) .trend-rank,
.trend-tag:nth-child(2) .trend-rank,
.trend-tag:nth-child(3) .trend-rank {
    background: #ff5722;
}

.trend-name {
    flex: 1;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-gray-900);
}

.trend-count {
    font-size: 12px;
    color: var(--color-gray-600);
}

/* カテゴリリスト */
.category-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.category-list li {
    border-bottom: 1px solid var(--color-gray-200);
}

.category-list li:last-child {
    border-bottom: none;
}

.category-list a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s;
}

.category-list a:hover {
    background: var(--color-gray-50);
}

.cat-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-900);
}

.category-list a:hover .cat-name {
    color: var(--color-blue);
}

.cat-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 24px;
    padding: 0 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-secondary);
    background: var(--color-gray-600);
    border-radius: 12px;
}

/* サイドバー注目記事 */
.featured-sidebar-list {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.featured-sidebar-item {
    border-bottom: 1px solid var(--color-gray-200);
    padding-bottom: 16px;
}

.featured-sidebar-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.featured-sidebar-link {
    display: flex;
    gap: 12px;
    text-decoration: none;
    color: inherit;
}

.featured-sidebar-thumb {
    width: 100px;
    height: 75px;
    flex-shrink: 0;
    border-radius: 4px;
    overflow: hidden;
    background: var(--color-gray-100);
}

.featured-sidebar-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.featured-sidebar-link:hover .featured-sidebar-thumb img {
    transform: scale(1.1);
}

.featured-sidebar-content {
    flex: 1;
    min-width: 0;
}

.featured-sidebar-title {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    color: var(--color-gray-900);
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.featured-sidebar-link:hover .featured-sidebar-title {
    color: var(--color-blue);
}

.featured-sidebar-meta {
    display: flex;
    gap: 10px;
    font-size: 11px;
    color: var(--color-gray-600);
}

.featured-sidebar-meta span {
    display: flex;
    align-items: center;
    gap: 3px;
}

/* PR欄 */
.pr-content {
    padding: 20px;
    text-align: center;
    background: var(--color-gray-50);
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.pr-placeholder {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-gray-600);
    margin: 0 0 12px;
}

.pr-note {
    font-size: 12px;
    color: var(--color-gray-600);
    margin: 0;
}

/* トップに戻るボタン */
.back-to-top {
    position: fixed;
    bottom: 32px;
    right: 32px;
    width: 56px;
    height: 56px;
    background: var(--color-primary);
    color: var(--color-accent);
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

/* レスポンシブ調整 */
@media (max-width: 767px) {
    .archive-header {
        padding: 24px 0 16px;
    }
    
    .icon-title {
        gap: 8px;
    }
    
    .icon-title i {
        font-size: 28px;
    }
    
    .icon-title h1 {
        font-size: 24px;
    }
    
    .header-title-area {
        flex-direction: column;
    }
    
    .total-count {
        align-self: flex-start;
        padding: 12px 20px;
    }
    
    .count-number {
        font-size: 24px;
    }
    
    .control-bar {
        flex-direction: column;
        gap: 12px;
    }
    
    .search-form,
    .sort-dropdown {
        width: 100%;
    }
    
    .tab-btn {
        padding: 14px 18px;
        font-size: 14px;
    }
    
    .tab-btn span:not(.tab-count) {
        display: none;
    }
    
    .articles-container {
        border-width: 2px;
    }
    
    .pagination-nav {
        padding: 24px 16px;
    }
    
    .back-to-top {
        width: 48px;
        height: 48px;
        bottom: 24px;
        right: 24px;
        font-size: 18px;
    }
    
    .featured-grid {
        grid-template-columns: 1fr;
    }
}

/* モバイルでサイドバーを上部に移動 */
@media (max-width: 1023px) {
    .sidebar-column {
        order: -1;
        margin-bottom: 24px;
    }
    
    .sidebar-widget {
        border-width: 2px;
    }
    
    .widget-title {
        padding: 14px 16px;
        font-size: 16px;
    }
    
    /* ランキングは5件のみ表示 */
    .ranking-item:nth-child(n+6) {
        display: none;
    }
    
    /* トピック欄：時間を非表示 */
    .topic-time {
        display: none;
    }
    
    .topic-link {
        gap: 8px;
        padding: 12px 16px;
    }
}

@media (min-width: 1280px) {
    .archive-header-wrapper,
    .archive-wrapper-two-column,
    .featured-wrapper {
        max-width: 1400px;
    }
    
    .archive-wrapper-two-column {
        grid-template-columns: 1fr 380px;
    }
}

/* ========================================
   Column Card Compact Style (for AJAX loaded articles)
   ======================================== */

.column-card-compact {
    background: #ffffff;
    border-bottom: 1px solid #e5e5e5;
    transition: background-color 0.2s ease;
}

.column-card-compact:hover {
    background-color: #f8f8f8;
}

.card-link-compact {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 16px;
}

.card-inner {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

/* 左側：サムネイル */
.card-thumb {
    position: relative;
    flex-shrink: 0;
    width: 120px;
    height: 80px;
    border-radius: 4px;
    overflow: hidden;
    background: #f5f5f5;
}

.card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.column-card-compact:hover .card-thumb img {
    transform: scale(1.1);
}

/* バッジ */
.card-thumb .badge {
    position: absolute;
    top: 4px;
    left: 4px;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 2px;
    line-height: 1;
}

.badge-new {
    background: #2196f3;
    color: #ffffff;
}

/* 右側：テキスト */
.card-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* メタ情報（上部） */
.card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 12px;
}

.meta-category {
    padding: 3px 8px;
    background: #000000;
    color: #ffeb3b;
    border-radius: 2px;
    font-weight: 600;
}

.meta-difficulty {
    padding: 3px 8px;
    background: #f5f5f5;
    color: #666666;
    border-radius: 2px;
    font-weight: 500;
}

/* タイトル */
.card-title-compact {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
    color: #000000;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.column-card-compact:hover .card-title-compact {
    color: #0066cc;
}

/* 抜粋 */
.card-excerpt-compact {
    font-size: 13px;
    line-height: 1.5;
    color: #666666;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 下部メタ情報 */
.card-footer-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: #999999;
}

.card-footer-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-footer-meta i {
    font-size: 11px;
}

.meta-date i {
    color: #2196f3;
}

.meta-time i {
    color: #ff9800;
}

.meta-views i {
    color: #9c27b0;
}

/* Column card compact responsive */
@media (max-width: 640px) {
    .card-inner {
        gap: 12px;
    }
    
    .card-thumb {
        width: 100px;
        height: 67px;
    }
    
    .card-title-compact {
        font-size: 15px;
    }
    
    .card-excerpt-compact {
        font-size: 12px;
    }
    
    .card-footer-meta {
        font-size: 11px;
        gap: 8px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // カテゴリタブのAJAX切り替え
    const categoryTabs = document.querySelectorAll('.tab-btn');
    const topicsContainer = document.getElementById('topics-list-container');
    const articlesWrapper = document.getElementById('articles-list-wrapper');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // アクティブタブの切り替え
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const categoryId = this.dataset.categoryId || '';
            const categorySlug = this.dataset.categorySlug || '';
            
            // 記事を読み込み（ページ1から）
            loadCategoryPosts(categoryId, categorySlug, 1);
        });
    });
    
    async function loadCategoryPosts(categoryId, categorySlug, page = 1) {
        // ローディング表示
        loadingIndicator.style.display = 'flex';
        articlesWrapper.style.opacity = '0.3';
        topicsContainer.style.opacity = '0.3';
        
        try {
            // REST APIで記事を取得（埋め込みデータも含む）
            // Note: rest_base is 'columns' and taxonomy rest_base is 'column-categories'
            // WordPress REST API requires term ID, not slug for taxonomy filtering
            const topicsUrl = categoryId 
                ? `/wp-json/wp/v2/columns?column-categories=${categoryId}&per_page=20&orderby=date&order=desc&_embed=1`
                : `/wp-json/wp/v2/columns?per_page=20&orderby=date&order=desc&_embed=1`;
            
            const articlesUrl = categoryId
                ? `/wp-json/wp/v2/columns?column-categories=${categoryId}&per_page=12&page=${page}&orderby=date&order=desc&_embed=1`
                : `/wp-json/wp/v2/columns?per_page=12&page=${page}&orderby=date&order=desc&_embed=1`;
            
            // トピックと記事を並列取得
            const [topicsResponse, articlesResponse] = await Promise.all([
                fetch(topicsUrl),
                fetch(articlesUrl)
            ]);
            
            // Check for errors
            if (!topicsResponse.ok) {
                console.error('Topics API error:', topicsResponse.status, topicsUrl);
                throw new Error(`Topics API returned ${topicsResponse.status}`);
            }
            if (!articlesResponse.ok) {
                console.error('Articles API error:', articlesResponse.status, articlesUrl);
                throw new Error(`Articles API returned ${articlesResponse.status}`);
            }
            
            const topicsPosts = await topicsResponse.json();
            const articlesPosts = await articlesResponse.json();
            
            // Get total pages from response headers
            const totalPages = parseInt(articlesResponse.headers.get('X-WP-TotalPages')) || 1;
            const totalPosts = parseInt(articlesResponse.headers.get('X-WP-Total')) || 0;
            
            console.log('Topics loaded:', topicsPosts.length, 'articles loaded:', articlesPosts.length, 'page:', page, '/', totalPages);
            
            // トピックリストを更新
            updateTopicsList(topicsPosts);
            
            // 記事リストを更新
            updateArticlesList(articlesPosts);
            
            // ページネーション更新
            updatePagination(page, totalPages, categoryId, categorySlug);
            
            // URL更新（履歴に追加せず）
            let newUrl;
            if (categorySlug) {
                newUrl = page > 1 ? `/column-category/${categorySlug}/page/${page}/` : `/column-category/${categorySlug}/`;
            } else {
                newUrl = page > 1 ? `/column/page/${page}/` : `/column/`;
            }
            window.history.replaceState({
                categoryId: categoryId, 
                categorySlug: categorySlug,
                page: page
            }, '', newUrl);
            
        } catch (error) {
            console.error('Error loading posts:', error);
            topicsContainer.innerHTML = '<p class="no-topics">記事の読み込みに失敗しました。</p>';
            articlesWrapper.innerHTML = '<div class="no-posts"><p>記事の読み込みに失敗しました。</p></div>';
        } finally {
            // ローディング非表示
            loadingIndicator.style.display = 'none';
            articlesWrapper.style.opacity = '1';
            topicsContainer.style.opacity = '1';
            articlesWrapper.classList.add('fade-in');
            topicsContainer.classList.add('fade-in');
        }
    }
    
    function updateTopicsList(posts) {
        if (!posts || posts.length === 0) {
            topicsContainer.innerHTML = '<p class="no-topics">このカテゴリの記事はまだありません。</p>';
            return;
        }
        
        let html = '<ul class="topics-list">';
        posts.forEach(post => {
            const timeAgo = getTimeAgo(post.date);
            // Get category from embedded data
            // _embedded['wp:term'] is array of term arrays, first array is categories
            let categoryName = '';
            if (post._embedded && post._embedded['wp:term'] && post._embedded['wp:term'][0]) {
                const categories = post._embedded['wp:term'][0];
                if (categories && categories.length > 0) {
                    categoryName = categories[0].name;
                }
            }
            
            html += `
                <li class="topic-item">
                    <a href="${post.link}" class="topic-link">
                        <span class="topic-time">${timeAgo}前</span>
                        <span class="topic-title">${post.title.rendered}</span>
                        ${categoryName ? `<span class="topic-category">${categoryName}</span>` : ''}
                    </a>
                </li>
            `;
        });
        html += '</ul>';
        
        topicsContainer.innerHTML = html;
    }
    
    function updatePagination(currentPage, totalPages, categoryId, categorySlug) {
        // Find pagination container
        const paginationNav = document.querySelector('.pagination-nav');
        if (!paginationNav) return;
        
        if (totalPages <= 1) {
            paginationNav.style.display = 'none';
            return;
        }
        
        paginationNav.style.display = 'block';
        
        let html = '<ul class="pagination-list">';
        
        // Previous button
        if (currentPage > 1) {
            html += `<li><a href="#" class="page-link" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i> 前へ</a></li>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            html += `<li><a href="#" class="page-link" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li><span class="page-dots">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<li><span class="page-numbers current">${i}</span></li>`;
            } else {
                html += `<li><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
            }
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li><span class="page-dots">...</span></li>`;
            }
            html += `<li><a href="#" class="page-link" data-page="${totalPages}">${totalPages}</a></li>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            html += `<li><a href="#" class="page-link" data-page="${currentPage + 1}">次へ <i class="fas fa-chevron-right"></i></a></li>`;
        }
        
        html += '</ul>';
        
        paginationNav.innerHTML = html;
        
        // Add click event listeners to pagination links
        const pageLinks = paginationNav.querySelectorAll('.page-link');
        pageLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                loadCategoryPosts(categoryId, categorySlug, page);
                
                // Scroll to top of articles
                const articlesContainer = document.getElementById('articles-container');
                if (articlesContainer) {
                    articlesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }
    
    function updateArticlesList(posts) {
        if (!posts || posts.length === 0) {
            articlesWrapper.innerHTML = `
                <div class="no-posts">
                    <i class="fas fa-search"></i>
                    <h2>記事が見つかりませんでした</h2>
                    <p>このカテゴリの記事はまだありません。</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="articles-list">';
        posts.forEach(post => {
            // Get thumbnail from embedded featured media
            let thumbnail = '';
            if (post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'][0]) {
                thumbnail = post._embedded['wp:featuredmedia'][0].source_url || '';
            }
            
            // Get category from embedded data
            let categoryName = '';
            if (post._embedded && post._embedded['wp:term'] && post._embedded['wp:term'][0]) {
                const categories = post._embedded['wp:term'][0];
                if (categories && categories.length > 0) {
                    categoryName = categories[0].name;
                }
            }
            
            const views = post.meta?.view_count || 0;
            const postDate = new Date(post.date);
            const formattedDate = `${postDate.getFullYear()}/${String(postDate.getMonth() + 1).padStart(2, '0')}/${String(postDate.getDate()).padStart(2, '0')}`;
            
            // Check if post is new (within 7 days)
            const isNew = (Date.now() - postDate.getTime()) < (7 * 24 * 60 * 60 * 1000);
            
            // Generate excerpt from content
            let excerpt = '';
            if (post.excerpt && post.excerpt.rendered) {
                const div = document.createElement('div');
                div.innerHTML = post.excerpt.rendered;
                excerpt = div.textContent.substring(0, 100) + '...';
            }
            
            // Match the template structure from template-parts/column/card.php
            html += `
                <article class="column-card-compact">
                    <a href="${post.link}" class="card-link-compact">
                        <div class="card-inner">
                            ${thumbnail ? `
                                <div class="card-thumb">
                                    <img src="${thumbnail}" 
                                         alt="${post.title.rendered}"
                                         loading="lazy">
                                    ${isNew ? '<span class="badge badge-new">NEW</span>' : ''}
                                </div>
                            ` : ''}
                            <div class="card-text">
                                <div class="card-meta">
                                    ${categoryName ? `<span class="meta-category">${categoryName}</span>` : ''}
                                </div>
                                <h3 class="card-title-compact">${post.title.rendered}</h3>
                                ${excerpt ? `<p class="card-excerpt-compact">${excerpt}</p>` : ''}
                                <div class="card-footer-meta">
                                    <span class="meta-date">
                                        <i class="fas fa-calendar"></i>
                                        ${formattedDate}
                                    </span>
                                    ${views > 0 ? `
                                        <span class="meta-views">
                                            <i class="fas fa-eye"></i>
                                            ${Number(views).toLocaleString()}
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            `;
        });
        html += '</div>';
        
        articlesWrapper.innerHTML = html;
    }
    
    function getTimeAgo(dateString) {
        const now = new Date();
        const postDate = new Date(dateString);
        const diffMs = now - postDate;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 60) {
            return `${diffMins}分`;
        } else if (diffHours < 24) {
            return `${diffHours}時間`;
        } else {
            return `${diffDays}日`;
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getMonth() + 1}/${date.getDate()}`;
    }
    
    // ソートドロップダウン
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const orderby = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('orderby', orderby);
            window.location.href = url.toString();
        });
    }
    
    // トップに戻るボタン
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // タブのスムーズスクロール
    const tabsContainer = document.querySelector('.tabs-scroll-container');
    const activeTab = document.querySelector('.tab-btn.active');
    if (tabsContainer && activeTab) {
        // アクティブなタブを中央に表示
        setTimeout(() => {
            const containerWidth = tabsContainer.offsetWidth;
            const tabOffset = activeTab.offsetLeft;
            const tabWidth = activeTab.offsetWidth;
            const scrollPosition = tabOffset - (containerWidth / 2) + (tabWidth / 2);
            
            tabsContainer.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
            });
        }, 100);
    }
    
    console.log('[OK] Archive Column v6.0.3 - Yahoo! AJAX Tab Switching initialized (card layout fixed)');
    
})();
</script>
