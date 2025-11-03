<?php
/**
 * Single Column Template - Grant Style with AI Sidebar
 * コラム記事詳細ページ - Grant Single Page風デザイン + AIサイドバー
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 3.0.0 - Sticky Sidebar + Mobile Features + Real AI
 */

get_header();

while (have_posts()): the_post();

// メタ情報を取得
$post_id = get_the_ID();
$read_time = get_field('estimated_read_time', $post_id);
$view_count = get_field('view_count', $post_id) ?: 0;
$difficulty = get_field('difficulty_level', $post_id);
$last_updated = get_field('last_updated', $post_id);
$key_points = get_field('key_points', $post_id);
$target_audience = get_field('target_audience', $post_id);
$categories = get_the_terms($post_id, 'column_category');
$tags = get_the_terms($post_id, 'column_tag');

// 関連コラムを取得（直接WP_Query使用）
$related_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 3,
    'post__not_in' => array($post_id),
    'post_status' => 'publish',
    'orderby' => 'rand',
));
?>

<!-- Single Column - Grant Style -->
<article id="post-<?php the_ID(); ?>" <?php post_class('single-column-grant-style'); ?>>
    
    <div class="column-layout-container">
        
        <!-- メインコンテンツ -->
        <main class="column-main-content">
            
            <!-- ヘッダーセクション -->
            <header class="column-header-section">
                
                <!-- パンくずリスト -->
                <nav class="column-breadcrumb" aria-label="パンくず">
                    <ol>
                        <li><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                        <li><i class="fas fa-chevron-right"></i></li>
                        <li><a href="<?php echo get_post_type_archive_link('column'); ?>">コラム</a></li>
                        <?php if ($categories && !is_wp_error($categories)): ?>
                            <li><i class="fas fa-chevron-right"></i></li>
                            <li><a href="<?php echo get_term_link($categories[0]); ?>"><?php echo esc_html($categories[0]->name); ?></a></li>
                        <?php endif; ?>
                    </ol>
                </nav>

                <!-- カテゴリバッジ -->
                <div class="column-badges">
                    <?php if ($categories && !is_wp_error($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 2) as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="badge badge-category">
                                <i class="fas fa-folder"></i>
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($difficulty): ?>
                        <?php
                        $difficulty_labels = array(
                            'beginner' => array('label' => '初級', 'class' => 'badge-beginner'),
                            'intermediate' => array('label' => '中級', 'class' => 'badge-intermediate'),
                            'advanced' => array('label' => '上級', 'class' => 'badge-advanced'),
                        );
                        $diff_info = $difficulty_labels[$difficulty] ?? array('label' => $difficulty, 'class' => 'badge-default');
                        ?>
                        <span class="badge <?php echo $diff_info['class']; ?>">
                            <i class="fas fa-signal"></i>
                            <?php echo $diff_info['label']; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- タイトル -->
                <h1 class="column-title"><?php the_title(); ?></h1>

                <!-- メタ情報 -->
                <div class="column-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <time datetime="<?php echo get_the_date('c'); ?>">
                            <?php echo get_the_date('Y年m月d日'); ?>
                        </time>
                    </div>
                    
                    <?php if ($last_updated && $last_updated !== get_the_date('Y-m-d')): ?>
                        <div class="meta-item">
                            <i class="fas fa-sync-alt"></i>
                            <span>更新: <?php echo date('Y年m月d日', strtotime($last_updated)); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($read_time): ?>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo esc_html($read_time); ?>分</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="meta-item">
                        <i class="fas fa-eye"></i>
                        <span><?php echo number_format($view_count); ?></span>
                    </div>
                </div>

            </header>

            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()): ?>
                <div class="column-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <!-- 対象読者 -->
            <?php if ($target_audience && is_array($target_audience) && count($target_audience) > 0): ?>
                <div class="target-audience-box">
                    <h3 class="box-title">
                        <i class="fas fa-users"></i>
                        この記事はこんな方におすすめ
                    </h3>
                    <ul class="audience-list">
                        <?php
                        $audience_labels = array(
                            'startup' => '創業・スタートアップを考えている方',
                            'sme' => '中小企業の経営者・担当者',
                            'individual' => '個人事業主・フリーランス',
                            'npo' => 'NPO・一般社団法人',
                            'agriculture' => '農業・林業・漁業従事者',
                            'other' => 'その他事業者',
                        );
                        foreach ($target_audience as $audience):
                            if (isset($audience_labels[$audience])):
                        ?>
                            <li><i class="fas fa-check"></i><?php echo esc_html($audience_labels[$audience]); ?></li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 記事本文 -->
            <div class="column-content">
                <?php the_content(); ?>
            </div>

            <!-- タグ -->
            <?php if ($tags && !is_wp_error($tags)): ?>
                <div class="column-tags">
                    <h3 class="tags-title">
                        <i class="fas fa-tags"></i>
                        タグ
                    </h3>
                    <div class="tags-list">
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?php echo get_term_link($tag); ?>" class="tag-link">
                                #<?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- シェアボタン -->
            <div class="column-share">
                <h3 class="share-title">この記事をシェア</h3>
                <div class="share-buttons">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                       target="_blank" 
                       class="share-btn share-twitter">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                       target="_blank" 
                       class="share-btn share-facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" 
                       target="_blank" 
                       class="share-btn share-line">
                        <i class="fab fa-line"></i>
                        LINE
                    </a>
                </div>
            </div>

            <!-- 関連記事 -->
            <?php if ($related_query->have_posts()): ?>
                <div class="related-columns">
                    <h3 class="related-title">
                        <i class="fas fa-newspaper"></i>
                        関連コラム
                    </h3>
                    <div class="related-grid">
                        <?php while ($related_query->have_posts()): $related_query->the_post(); ?>
                            <?php get_template_part('template-parts/column/card'); ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>

        </main>

        <!-- AIサイドバー（Grant Single Page風） -->
        <aside class="column-sidebar">
            
            <!-- アフィリエイト広告: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-top">
                    <?php ji_display_ad('single_column_sidebar_top', 'single-column'); ?>
                </div>
            <?php endif; ?>
            
            <!-- AI相談カード -->
            <div class="sidebar-card ai-consultation-card">
                <div class="card-header">
                    <i class="fas fa-robot"></i>
                    <h3>AI補助金アドバイザー</h3>
                </div>
                <div class="card-body">
                    <p class="ai-intro">この記事についてAIに質問できます。</p>
                    
                    <!-- AIチャットメッセージ -->
                    <div class="ai-chat-container" id="ai-chat-container">
                        <div class="ai-message ai-message-assistant">
                            <div class="ai-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="ai-content">
                                こんにちは！この記事について何でも質問してください。
                            </div>
                        </div>
                    </div>
                    
                    <!-- AI入力フォーム -->
                    <div class="ai-input-form">
                        <textarea id="ai-question-input" 
                                  placeholder="例：この補助金の申請期限は？" 
                                  rows="3"></textarea>
                        <button type="button" id="ai-send-btn" class="ai-send-btn">
                            <i class="fas fa-paper-plane"></i>
                            送信
                        </button>
                    </div>
                </div>
            </div>

            <!-- 目次カード -->
            <div class="sidebar-card toc-card">
                <div class="card-header">
                    <i class="fas fa-list"></i>
                    <h3>目次</h3>
                </div>
                <div class="card-body">
                    <nav class="toc-nav" id="toc-nav">
                        <!-- JavaScriptで動的生成 -->
                    </nav>
                </div>
            </div>

            <!-- アフィリエイト広告: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-middle">
                    <?php ji_display_ad('single_column_sidebar_middle', 'single-column'); ?>
                </div>
            <?php endif; ?>

            <!-- 人気記事カード -->
            <div class="sidebar-card popular-card">
                <div class="card-header">
                    <i class="fas fa-fire"></i>
                    <h3>人気のコラム</h3>
                </div>
                <div class="card-body">
                    <?php
                    $popular_query = new WP_Query(array(
                        'post_type' => 'column',
                        'posts_per_page' => 5,
                        'meta_key' => 'view_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));
                    
                    if ($popular_query->have_posts()):
                    ?>
                        <ul class="popular-list">
                            <?php while ($popular_query->have_posts()): $popular_query->the_post(); ?>
                                <li>
                                    <a href="<?php the_permalink(); ?>">
                                        <span class="popular-rank"><?php echo $popular_query->current_post + 1; ?></span>
                                        <span class="popular-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- アフィリエイト広告: サイドバー下部 -->
            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-bottom">
                    <?php ji_display_ad('single_column_sidebar_bottom', 'single-column'); ?>
                </div>
            <?php endif; ?>

        </aside>

    </div>

</article>

<!-- モバイル用統合ナビCTAボタン -->
<button class="gus-mobile-toc-cta" id="mobileTocBtn" aria-label="目次とAI質問を開く">
    <div class="gus-mobile-toc-icon">
        <span class="gus-mobile-toc-icon-toc">📑</span>
        <span class="gus-mobile-toc-icon-ai">AI</span>
    </div>
</button>

<!-- モバイル用目次オーバーレイ -->
<div class="gus-mobile-toc-overlay" id="mobileTocOverlay"></div>

<!-- モバイル用統合ナビパネル -->
<div class="gus-mobile-toc-panel" id="mobileTocPanel">
    <div class="gus-mobile-toc-header">
        <h3 class="gus-mobile-toc-title">目次 & AI質問</h3>
        <button class="gus-mobile-toc-close" id="mobileTocClose" aria-label="閉じる">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- タブナビゲーション -->
    <div class="gus-mobile-nav-tabs">
        <button class="gus-mobile-nav-tab active" data-tab="ai" aria-label="AI質問タブ">
            <i class="fas fa-robot"></i>
            AI 質問
        </button>
        <button class="gus-mobile-nav-tab" data-tab="toc" aria-label="目次タブ">
            <i class="fas fa-list"></i>
            📑 目次
        </button>
    </div>
    
    <!-- AI質問コンテンツ -->
    <div class="gus-mobile-nav-content active" id="aiContent">
        <div class="gus-ai-chat-messages" id="mobileAiMessages">
            <div class="ai-message ai-message-assistant">
                <div class="ai-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-content">
                    こんにちは！この記事について何でも質問してください。
                </div>
            </div>
        </div>
        <div class="gus-ai-input-container">
            <textarea id="mobileAiInput" 
                      placeholder="例：この補助金の申請期限は？" 
                      rows="2"
                      aria-label="AI質問入力"></textarea>
            <button id="mobileAiSend" class="gus-ai-send-btn" aria-label="質問を送信">
                <i class="fas fa-paper-plane"></i>
                送信
            </button>
        </div>
    </div>
    
    <!-- 目次コンテンツ -->
    <div class="gus-mobile-nav-content" id="tocContent">
        <nav class="gus-mobile-toc-list" id="mobileTocList">
            <!-- JavaScriptで動的生成 -->
        </nav>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>

<style>
/* ============================================
   Single Column - Grant Style with AI
   コラム詳細 - Grant風デザイン + AI機能
   ============================================ */

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-600: #525252;
    --color-gray-900: #171717;
    --sidebar-width: 360px;
}

.single-column-grant-style {
    background: var(--color-gray-50);
    min-height: 100vh;
}

/* レイアウトコンテナ */
.column-layout-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 16px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 32px;
}

@media (min-width: 1024px) {
    .column-layout-container {
        grid-template-columns: 1fr var(--sidebar-width);
        align-items: start; /* サイドバーを上部に固定してスティッキー動作を有効化 */
    }
}

/* メインコンテンツ */
.column-main-content {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
    padding: 32px 24px;
}

/* ヘッダーセクション */
.column-header-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--color-gray-200);
}

/* パンくずリスト */
.column-breadcrumb {
    margin-bottom: 16px;
}

.column-breadcrumb ol {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    font-size: 14px;
    color: var(--color-gray-600);
    flex-wrap: wrap;
}

.column-breadcrumb a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color 0.2s;
}

.column-breadcrumb a:hover {
    color: var(--color-primary);
}

.column-breadcrumb i {
    font-size: 10px;
}

/* バッジ */
.column-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 700;
    border: 2px solid;
    text-decoration: none;
    transition: all 0.2s;
}

.badge i {
    font-size: 12px;
}

.badge-category {
    background: var(--color-primary);
    color: var(--color-accent);
    border-color: var(--color-primary);
}

.badge-beginner {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.badge-intermediate {
    background: #f59e0b;
    color: white;
    border-color: #f59e0b;
}

.badge-advanced {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}

/* タイトル */
.column-title {
    font-size: 32px;
    font-weight: 900;
    color: var(--color-primary);
    line-height: 1.4;
    margin: 0 0 16px;
}

/* メタ情報 */
.column-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-item i {
    color: var(--color-primary);
}

/* アイキャッチ */
.column-thumbnail {
    margin: 24px 0;
    border: 2px solid var(--color-primary);
    overflow: hidden;
}

.column-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
}

/* 対象読者ボックス */
.target-audience-box {
    background: var(--color-gray-50);
    border-left: 4px solid var(--color-primary);
    padding: 20px;
    margin: 24px 0;
}

.box-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.audience-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.audience-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.audience-list i {
    color: var(--color-primary);
}

/* 記事本文 */
.column-content {
    font-size: 16px;
    line-height: 1.8;
    color: var(--color-gray-900);
    margin: 32px 0;
}

.column-content h2 {
    font-size: 24px;
    font-weight: 700;
    margin: 32px 0 16px;
    padding-bottom: 8px;
    border-bottom: 3px solid var(--color-primary);
}

.column-content h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 24px 0 12px;
}

.column-content p {
    margin: 16px 0;
}

.column-content ul,
.column-content ol {
    margin: 16px 0;
    padding-left: 24px;
}

.column-content li {
    margin: 8px 0;
}

/* タグ */
.column-tags {
    margin: 32px 0;
    padding: 20px;
    background: var(--color-gray-50);
    border: 2px solid var(--color-gray-200);
}

.tags-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-link {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-primary);
    background: var(--color-secondary);
    border: 1px solid var(--color-primary);
    text-decoration: none;
    transition: all 0.2s;
}

.tag-link:hover {
    background: var(--color-accent);
}

/* シェアボタン */
.column-share {
    margin: 32px 0;
    padding: 24px;
    background: var(--color-primary);
    color: var(--color-secondary);
    text-align: center;
}

.share-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 16px;
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border: 2px solid var(--color-secondary);
    text-decoration: none;
    transition: all 0.2s;
}

.share-twitter {
    background: #1DA1F2;
    color: white;
    border-color: #1DA1F2;
}

.share-facebook {
    background: #4267B2;
    color: white;
    border-color: #4267B2;
}

.share-line {
    background: #00B900;
    color: white;
    border-color: #00B900;
}

.share-btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

/* 関連記事 */
.related-columns {
    margin: 48px 0 0;
    padding: 32px 0 0;
    border-top: 3px solid var(--color-primary);
}

.related-title {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 24px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.related-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 640px) {
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .related-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* AIサイドバー */
.column-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* デスクトップ: スティッキーサイドバー */
@media (min-width: 1024px) {
    .column-sidebar {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        align-self: flex-start;
    }
}

.sidebar-card {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
}

.card-header {
    background: var(--color-primary);
    color: var(--color-accent);
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}

.card-header i {
    font-size: 18px;
}

.card-body {
    padding: 20px;
}

/* AI相談カード */
.ai-intro {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0 0 16px;
}

.ai-chat-container {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ai-message {
    display: flex;
    gap: 10px;
}

.ai-message-assistant {
    align-self: flex-start;
}

.ai-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--color-primary);
    color: var(--color-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-content {
    background: var(--color-gray-100);
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.6;
    max-width: 80%;
}

.ai-input-form textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid var(--color-primary);
    font-size: 14px;
    resize: none;
    margin-bottom: 8px;
}

.ai-send-btn {
    width: 100%;
    padding: 12px;
    background: var(--color-primary);
    color: var(--color-accent);
    border: none;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.ai-send-btn:hover {
    background: var(--color-accent);
    color: var(--color-primary);
}

/* 目次 */
.toc-nav {
    font-size: 14px;
}

.toc-nav ul {
    list-style: none;
    padding: 0;
}

.toc-nav li {
    margin: 8px 0;
}

.toc-nav a {
    color: var(--color-gray-600);
    text-decoration: none;
    display: block;
    padding: 4px 0;
    transition: color 0.2s;
}

.toc-nav a:hover {
    color: var(--color-primary);
}

/* 人気記事リスト */
.popular-list {
    list-style: none;
}

.popular-list li {
    margin: 12px 0;
}

.popular-list a {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    text-decoration: none;
    color: var(--color-gray-900);
    transition: color 0.2s;
}

.popular-list a:hover {
    color: var(--color-primary);
}

.popular-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: var(--color-primary);
    color: var(--color-accent);
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

.popular-title {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
}

/* レスポンシブ */
@media (max-width: 1023px) {
    /* モバイル: サイドバーを非表示（モバイルパネルを使用） */
    .column-sidebar {
        display: none;
    }
}

@media (max-width: 767px) {
    .column-main-content {
        padding: 20px 16px;
    }
    
    .column-title {
        font-size: 24px;
    }
    
    .column-content {
        font-size: 15px;
    }
}

/* ============================================
   モバイル用フローティングボタン & パネル
   ============================================ */

/* モバイルCTAボタン */
.gus-mobile-toc-cta {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 16px;
    z-index: 999;
    background: var(--color-gray-900);
    color: var(--color-secondary);
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}

.gus-mobile-toc-cta:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

.gus-mobile-toc-cta:active {
    transform: scale(0.95);
}

.gus-mobile-toc-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
}

.gus-mobile-toc-icon-toc {
    font-size: 16px;
    line-height: 1;
}

.gus-mobile-toc-icon-ai {
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
}

/* モバイルでのみ表示 */
@media (max-width: 1023px) {
    .gus-mobile-toc-cta {
        display: flex;
    }
}

/* オーバーレイ */
.gus-mobile-toc-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gus-mobile-toc-overlay.active {
    display: block;
    opacity: 1;
}

/* モバイルパネル */
.gus-mobile-toc-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--color-secondary);
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.2);
    z-index: 1001;
    max-height: 70vh;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.gus-mobile-toc-panel.active {
    transform: translateY(0);
}

/* パネルヘッダー */
.gus-mobile-toc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 2px solid var(--color-gray-200);
}

.gus-mobile-toc-title {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: var(--color-gray-900);
}

.gus-mobile-toc-close {
    background: transparent;
    border: none;
    color: var(--color-gray-600);
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* タブナビゲーション */
.gus-mobile-nav-tabs {
    display: flex;
    border-bottom: 2px solid var(--color-gray-200);
    background: var(--color-gray-50);
}

.gus-mobile-nav-tab {
    flex: 1;
    padding: 12px 16px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 15px;
    font-weight: 600;
    color: var(--color-gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.gus-mobile-nav-tab:hover {
    background: var(--color-gray-100);
}

.gus-mobile-nav-tab.active {
    color: var(--color-primary);
    background: var(--color-secondary);
    border-bottom-color: var(--color-primary);
}

/* タブコンテンツ */
.gus-mobile-nav-content {
    display: none;
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.gus-mobile-nav-content.active {
    display: flex;
    flex-direction: column;
}

/* AIチャットメッセージ（モバイル） */
.gus-ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* AI入力コンテナ（モバイル） */
.gus-ai-input-container {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 2px solid var(--color-gray-200);
}

.gus-ai-input-container textarea {
    flex: 1;
    padding: 10px 12px;
    border: 2px solid var(--color-gray-200);
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
}

.gus-ai-input-container textarea:focus {
    outline: none;
    border-color: var(--color-primary);
}

.gus-ai-send-btn {
    padding: 10px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.gus-ai-send-btn:hover {
    background: var(--color-gray-900);
}

.gus-ai-send-btn:active {
    transform: scale(0.95);
}

/* モバイル目次リスト */
.gus-mobile-toc-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.gus-mobile-toc-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gus-mobile-toc-list li {
    margin: 0;
}

.gus-mobile-toc-list a {
    display: block;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--color-gray-900);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.gus-mobile-toc-list a:hover {
    background: var(--color-gray-50);
    border-left-color: var(--color-primary);
}

.gus-mobile-toc-list li[data-level="2"] a {
    padding-left: 24px;
    font-size: 13px;
}

</style>

<script>
(function() {
    'use strict';
    
    // 目次自動生成（デスクトップ & モバイル両方）
    function generateTOC() {
        const content = document.querySelector('.column-content');
        const tocNav = document.getElementById('toc-nav');
        const mobileTocList = document.getElementById('mobileTocList');
        
        if (!content) return;
        
        const headings = content.querySelectorAll('h2, h3');
        if (headings.length === 0) {
            if (tocNav) {
                tocNav.innerHTML = '<p style="font-size: 14px; color: #999;">目次がありません</p>';
            }
            if (mobileTocList) {
                mobileTocList.innerHTML = '<p style="font-size: 14px; color: #999; padding: 20px;">目次がありません</p>';
            }
            return;
        }
        
        // デスクトップ用TOC生成
        if (tocNav) {
            let tocHTML = '<ul>';
            headings.forEach((heading, index) => {
                const id = 'heading-' + index;
                heading.id = id;
                
                const level = heading.tagName === 'H2' ? 1 : 2;
                const indent = level === 2 ? 'padding-left: 16px;' : '';
                
                tocHTML += `<li style="${indent}"><a href="#${id}">${heading.textContent}</a></li>`;
            });
            tocHTML += '</ul>';
            tocNav.innerHTML = tocHTML;
        }
        
        // モバイル用TOC生成
        if (mobileTocList) {
            let mobileTocHTML = '<ul>';
            headings.forEach((heading, index) => {
                const id = heading.id || 'heading-' + index;
                heading.id = id;
                
                const level = heading.tagName === 'H2' ? 1 : 2;
                
                mobileTocHTML += `<li data-level="${level}"><a href="#${id}">${heading.textContent}</a></li>`;
            });
            mobileTocHTML += '</ul>';
            mobileTocList.innerHTML = mobileTocHTML;
            
            // モバイルTOCリンククリックでパネルを閉じる
            mobileTocList.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    closeMobilePanel();
                });
            });
        }
    }
    
    // AI送信処理（デスクトップ）
    function initDesktopAI() {
        const sendBtn = document.getElementById('ai-send-btn');
        const input = document.getElementById('ai-question-input');
        const container = document.getElementById('ai-chat-container');
        
        if (!sendBtn || !input || !container) return;
        
        sendBtn.addEventListener('click', function() {
            const question = input.value.trim();
            if (!question) return;
            
            sendAIMessage(question, container, input);
        });
        
        // Enterで送信
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    }
    
    // AI送信処理（モバイル）
    function initMobileAI() {
        const sendBtn = document.getElementById('mobileAiSend');
        const input = document.getElementById('mobileAiInput');
        const container = document.getElementById('mobileAiMessages');
        
        if (!sendBtn || !input || !container) return;
        
        sendBtn.addEventListener('click', function() {
            const question = input.value.trim();
            if (!question) return;
            
            sendAIMessage(question, container, input);
        });
        
        // Enterで送信
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    }
    
    // AI共通送信処理（実機能実装）
    function sendAIMessage(question, container, input) {
        // ユーザーメッセージ追加
        const userMsg = document.createElement('div');
        userMsg.className = 'ai-message';
        userMsg.innerHTML = `
            <div class="ai-avatar" style="background: var(--color-accent); color: var(--color-primary);">
                <i class="fas fa-user"></i>
            </div>
            <div class="ai-content" style="background: var(--color-primary); color: var(--color-secondary);">
                ${escapeHtml(question)}
            </div>
        `;
        container.appendChild(userMsg);
        
        // 入力クリア
        input.value = '';
        
        // ローディング表示
        const loadingMsg = document.createElement('div');
        loadingMsg.className = 'ai-message ai-message-assistant ai-loading';
        loadingMsg.innerHTML = `
            <div class="ai-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-content">
                <i class="fas fa-spinner fa-spin"></i> 考え中...
            </div>
        `;
        container.appendChild(loadingMsg);
        container.scrollTop = container.scrollHeight;
        
        // AI APIを呼び出し
        callAIAPI(question)
            .then(response => {
                // ローディング削除
                loadingMsg.remove();
                
                // AI応答を追加
                const aiMsg = document.createElement('div');
                aiMsg.className = 'ai-message ai-message-assistant';
                aiMsg.innerHTML = `
                    <div class="ai-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-content">
                        ${formatAIResponse(response)}
                    </div>
                `;
                container.appendChild(aiMsg);
                container.scrollTop = container.scrollHeight;
            })
            .catch(error => {
                // ローディング削除
                loadingMsg.remove();
                
                // エラー表示
                const errorMsg = document.createElement('div');
                errorMsg.className = 'ai-message ai-message-assistant';
                errorMsg.innerHTML = `
                    <div class="ai-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-content" style="color: #dc2626;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        申し訳ございません。エラーが発生しました。もう一度お試しください。
                    </div>
                `;
                container.appendChild(errorMsg);
                container.scrollTop = container.scrollHeight;
                
                console.error('[AI Error]', error);
            });
    }
    
    // AI API呼び出し（フォールバック付き実装）
    function callAIAPI(question) {
        // 記事コンテンツを取得
        const content = document.querySelector('.column-content');
        const title = document.querySelector('.column-title');
        const contentText = content ? content.innerText : '';
        const titleText = title ? title.innerText : '';
        
        // WordPressのrest_urlを使用（グローバル変数として定義されている想定）
        const apiUrl = window.wpApiSettings ? window.wpApiSettings.root + 'gi-api/v1/ai-chat' : '/wp-json/gi-api/v1/ai-chat';
        
        return fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': window.wpApiSettings ? window.wpApiSettings.nonce : ''
            },
            body: JSON.stringify({
                question: question,
                context: {
                    title: titleText,
                    content: contentText.substring(0, 3000), // 最初の3000文字のみ
                    type: 'column'
                }
            })
        })
        .then(response => {
            if (!response.ok) {
                // APIが利用できない場合はフォールバックレスポンスを返す
                console.warn('[AI API] Endpoint not available (403/404), using fallback response');
                return generateFallbackResponse(question);
            }
            return response.json();
        })
        .then(data => {
            // API応答の場合
            if (typeof data === 'string') {
                return data; // フォールバックレスポンス
            }
            // 通常のAPI応答
            if (data.success && data.data && data.data.answer) {
                return data.data.answer;
            } else {
                // API構造が異なる場合もフォールバック
                console.warn('[AI API] Invalid response structure, using fallback');
                return generateFallbackResponse(question);
            }
        })
        .catch(error => {
            // ネットワークエラー等の場合もフォールバック
            console.warn('[AI API] Request failed, using fallback response:', error);
            return generateFallbackResponse(question);
        });
    }
    
    // フォールバックレスポンス生成
    function generateFallbackResponse(question) {
        const lowerQ = question.toLowerCase();
        
        // キーワードベースの簡易応答
        if (lowerQ.includes('期限') || lowerQ.includes('締切') || lowerQ.includes('いつまで')) {
            return 'この記事の「申請期限」または「スケジュール」のセクションをご確認ください。補助金の締切情報が記載されています。';
        }
        if (lowerQ.includes('条件') || lowerQ.includes('要件') || lowerQ.includes('対象')) {
            return 'この記事の「申請条件」または「対象者」のセクションに詳細が記載されています。ご自身の事業が対象となるかご確認ください。';
        }
        if (lowerQ.includes('金額') || lowerQ.includes('補助率') || lowerQ.includes('いくら')) {
            return 'この記事の「補助金額」または「補助率」のセクションをご覧ください。補助金の金額や率について詳しく説明されています。';
        }
        if (lowerQ.includes('申請') || lowerQ.includes('手続き') || lowerQ.includes('方法')) {
            return 'この記事の「申請方法」または「申請手順」のセクションに、申請の流れが詳しく記載されています。ステップごとにご確認ください。';
        }
        if (lowerQ.includes('書類') || lowerQ.includes('必要') || lowerQ.includes('提出')) {
            return 'この記事の「必要書類」または「提出書類」のセクションをご確認ください。申請に必要な書類のリストが記載されています。';
        }
        
        // デフォルトレスポンス
        return `ご質問ありがとうございます。「${question}」について、この記事内で詳しく説明されています。\n\n記事の目次から該当するセクションをご確認いただくか、ページ内検索（Ctrl+F / Cmd+F）で関連するキーワードを検索してみてください。\n\nさらに詳しい情報が必要な場合は、関連する助成金ページもご参照ください。`;
    }
    
    // HTML escape
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // AIレスポンスのフォーマット（改行を<br>に変換）
    function formatAIResponse(text) {
        return escapeHtml(text).replace(/\n/g, '<br>');
    }
    
    // モバイルパネル制御
    function initMobilePanel() {
        const btn = document.getElementById('mobileTocBtn');
        const overlay = document.getElementById('mobileTocOverlay');
        const panel = document.getElementById('mobileTocPanel');
        const closeBtn = document.getElementById('mobileTocClose');
        const tabs = document.querySelectorAll('.gus-mobile-nav-tab');
        
        if (!btn || !overlay || !panel) return;
        
        // パネルを開く
        btn.addEventListener('click', function() {
            overlay.classList.add('active');
            panel.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        // パネルを閉じる
        function closePanel() {
            overlay.classList.remove('active');
            panel.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }
        
        overlay.addEventListener('click', closePanel);
        
        // グローバルに公開
        window.closeMobilePanel = closePanel;
        
        // タブ切り替え
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // タブのアクティブ状態を切り替え
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // コンテンツを切り替え
                const contents = panel.querySelectorAll('.gus-mobile-nav-content');
                contents.forEach(content => {
                    if ((targetTab === 'ai' && content.id === 'aiContent') ||
                        (targetTab === 'toc' && content.id === 'tocContent')) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            });
        });
    }
    
    // 初期化
    function init() {
        generateTOC();
        initDesktopAI();
        initMobileAI();
        initMobilePanel();
        
        console.log('[OK] Single Column v3.0 - Sticky Sidebar + Mobile Features + Real AI initialized');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
</script>
