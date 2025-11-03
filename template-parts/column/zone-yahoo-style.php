<?php
/**
 * Template Part: Column Zone - Yahoo! Style
 * Yahoo! JAPAN風コラムゾーン（タブ式フルワイド）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 2.0.0 - Yahoo! Style Implementation
 * 
 * デザイン: 現在のテーマスタイル（黒/白）
 * 機能: Yahoo! JAPAN風タブ切り替え
 * タブ: 全て・カテゴリ別・人気・最新
 * サイドバー: 削除（フルワイド表示）
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// カテゴリ一覧を取得
$categories = get_terms(array(
    'taxonomy' => 'column_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10, // TOP 10カテゴリ
));
if (is_wp_error($categories)) {
    $categories = array();
}

// 全てのコラムを取得（9件）
$all_columns = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 9,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
));

// 人気のコラムを取得（閲覧数順、9件）
$popular_columns = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 9,
    'post_status' => 'publish',
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
));

// 最新のコラムを取得（9件）
$recent_columns = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 9,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<!-- Yahoo!風コラムゾーン -->
<section class="column-zone-yahoo" id="column-zone">
    <div class="column-zone-wrapper">
        
        <!-- セクションヘッダー -->
        <header class="section-header">
            <div class="header-left">
                <h2 class="section-title">
                    <i class="fas fa-newspaper" aria-hidden="true"></i>
                    補助金コラム
                </h2>
                <p class="section-desc">
                    補助金活用のヒントやノウハウ、最新情報をお届けします
                </p>
            </div>
            <a href="<?php echo get_post_type_archive_link('column'); ?>" 
               class="view-all"
               aria-label="コラム一覧を見る">
                すべて見る <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
        </header>

        <!-- Yahoo!風タブナビゲーション -->
        <nav class="column-tabs-nav" role="tablist" aria-label="コラムタブ">
            <button class="column-tab-btn active" 
                    id="tab-col-all" 
                    role="tab" 
                    aria-selected="true" 
                    aria-controls="panel-col-all"
                    data-tab="all">
                <i class="fas fa-list" aria-hidden="true"></i>
                <span>全て</span>
                <span class="tab-count"><?php echo $all_columns->found_posts; ?></span>
            </button>
            
            <?php if (!empty($categories)) : ?>
                <?php foreach (array_slice($categories, 0, 3) as $category) : ?>
                <button class="column-tab-btn" 
                        id="tab-col-<?php echo esc_attr($category->slug); ?>" 
                        role="tab" 
                        aria-selected="false" 
                        aria-controls="panel-col-<?php echo esc_attr($category->slug); ?>"
                        data-tab="<?php echo esc_attr($category->slug); ?>"
                        data-category-id="<?php echo $category->term_id; ?>">
                    <i class="fas fa-folder" aria-hidden="true"></i>
                    <span><?php echo esc_html($category->name); ?></span>
                    <span class="tab-count"><?php echo $category->count; ?></span>
                </button>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <button class="column-tab-btn" 
                    id="tab-col-popular" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-col-popular"
                    data-tab="popular">
                <i class="fas fa-fire" aria-hidden="true"></i>
                <span>人気</span>
                <span class="tab-count"><?php echo $popular_columns->found_posts; ?></span>
            </button>
            
            <button class="column-tab-btn" 
                    id="tab-col-recent" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-col-recent"
                    data-tab="recent">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <span>最新</span>
                <span class="tab-count"><?php echo $recent_columns->found_posts; ?></span>
            </button>
        </nav>

        <!-- タブコンテンツエリア -->
        <div class="column-tabs-content">
            
            <!-- タブ1: 全て -->
            <div class="column-tab-panel active" 
                 id="panel-col-all" 
                 role="tabpanel" 
                 aria-labelledby="tab-col-all">
                <div class="columns-grid">
                    <?php 
                    if ($all_columns->have_posts()) :
                        while ($all_columns->have_posts()) : $all_columns->the_post();
                            get_template_part('template-parts/column/card');
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <p class="no-data">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            現在、コラムはありません。
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- タブ2+: カテゴリ別 (動的に読み込み) -->
            <?php if (!empty($categories)) : ?>
                <?php foreach (array_slice($categories, 0, 3) as $category) : ?>
                <div class="column-tab-panel" 
                     id="panel-col-<?php echo esc_attr($category->slug); ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-col-<?php echo esc_attr($category->slug); ?>"
                     data-category-slug="<?php echo esc_attr($category->slug); ?>"
                     hidden>
                    <div class="columns-grid">
                        <div class="loading-message">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            <p>読み込み中...</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- タブ: 人気 -->
            <div class="column-tab-panel" 
                 id="panel-col-popular" 
                 role="tabpanel" 
                 aria-labelledby="tab-col-popular" 
                 hidden>
                <div class="columns-grid">
                    <?php 
                    if ($popular_columns->have_posts()) :
                        while ($popular_columns->have_posts()) : $popular_columns->the_post();
                            get_template_part('template-parts/column/card');
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <p class="no-data">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            現在、人気のコラムはありません。
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- タブ: 最新 -->
            <div class="column-tab-panel" 
                 id="panel-col-recent" 
                 role="tabpanel" 
                 aria-labelledby="tab-col-recent" 
                 hidden>
                <div class="columns-grid">
                    <?php 
                    if ($recent_columns->have_posts()) :
                        while ($recent_columns->have_posts()) : $recent_columns->the_post();
                            get_template_part('template-parts/column/card');
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <p class="no-data">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            現在、最新のコラムはありません。
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
/* ============================================
   Column Zone - Yahoo! Style
   コラムゾーン - Yahoo!風デザイン
   ============================================ */

.column-zone-yahoo {
    background: var(--color-gray-50, #fafafa);
    padding: 48px 0;
    width: 100%;
    overflow: visible;
}

.column-zone-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

/* セクションヘッダー */
.column-zone-yahoo .section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.column-zone-yahoo .header-left {
    flex: 1;
    min-width: 200px;
}

.column-zone-yahoo .section-title {
    font-size: 28px;
    font-weight: 900;
    color: var(--color-primary, #000000);
    margin: 0 0 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.column-zone-yahoo .section-title i {
    font-size: 30px;
}

.column-zone-yahoo .section-desc {
    font-size: 15px;
    color: var(--color-gray-600, #525252);
    margin: 0;
    line-height: 1.6;
}

.column-zone-yahoo .view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 700;
    color: var(--color-primary, #000000);
    background: var(--color-secondary, #ffffff);
    border: 2px solid var(--color-primary, #000000);
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.column-zone-yahoo .view-all:hover {
    background: var(--color-accent, #ffeb3b);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Yahoo!風タブナビゲーション */
.column-tabs-nav {
    display: flex;
    gap: 6px;
    margin-bottom: 0;
    border-bottom: 4px solid var(--color-primary, #000000);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.column-tabs-nav::-webkit-scrollbar {
    height: 6px;
}

.column-tabs-nav::-webkit-scrollbar-track {
    background: var(--color-gray-100, #f5f5f5);
}

.column-tabs-nav::-webkit-scrollbar-thumb {
    background: var(--color-gray-400, #a3a3a3);
    border-radius: 3px;
}

.column-tab-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 700;
    color: var(--color-gray-700, #404040);
    background: var(--color-gray-100, #f5f5f5);
    border: none;
    border-bottom: 4px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
    position: relative;
    margin-bottom: -4px;
}

.column-tab-btn:hover {
    background: var(--color-gray-200, #e5e5e5);
    color: var(--color-primary, #000000);
}

.column-tab-btn.active {
    color: var(--color-primary, #000000);
    background: var(--color-secondary, #ffffff);
    border-bottom-color: var(--color-accent, #ffeb3b);
    font-weight: 900;
}

.column-tab-btn i {
    font-size: 18px;
}

.column-tab-btn .tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 22px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 700;
    color: var(--color-secondary, #ffffff);
    background: var(--color-gray-700, #404040);
    border-radius: 11px;
}

.column-tab-btn.active .tab-count {
    background: var(--color-primary, #000000);
    color: var(--color-accent, #ffeb3b);
}

/* タブコンテンツ */
.column-tabs-content {
    background: var(--color-secondary, #ffffff);
    border: 3px solid var(--color-primary, #000000);
    border-top: none;
    padding: 32px 24px;
    min-height: 400px;
}

.column-tab-panel {
    display: none;
}

.column-tab-panel.active {
    display: block;
    animation: fadeInUp 0.4s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.column-tab-panel[hidden] {
    display: none !important;
}

/* コラムグリッド */
.columns-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 640px) {
    .columns-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .columns-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ローディングメッセージ */
.loading-message {
    text-align: center;
    padding: 64px 24px;
    color: var(--color-gray-600, #525252);
}

.loading-message i {
    font-size: 40px;
    color: var(--color-primary, #000000);
    margin-bottom: 16px;
}

.loading-message p {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

/* データなしメッセージ */
.no-data {
    text-align: center;
    padding: 64px 24px;
    color: var(--color-gray-600, #525252);
    font-size: 16px;
    font-weight: 600;
    background: var(--color-gray-50, #fafafa);
    border: 3px dashed var(--color-gray-300, #d4d4d4);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.no-data i {
    font-size: 24px;
    color: var(--color-gray-500, #737373);
}

/* レスポンシブ調整 */
@media (max-width: 767px) {
    .column-zone-yahoo {
        padding: 32px 0;
    }
    
    .column-zone-wrapper {
        padding: 0 12px;
    }
    
    .column-zone-yahoo .section-title {
        font-size: 24px;
    }
    
    .column-tab-btn {
        padding: 14px 18px;
        font-size: 14px;
    }
    
    .column-tab-btn span:not(.tab-count) {
        display: none;
    }
    
    .column-tab-btn i {
        margin-right: 0;
    }
    
    .column-tabs-content {
        padding: 20px 16px;
    }
}

@media (min-width: 1280px) {
    .column-zone-wrapper {
        max-width: 1400px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // コラムタブ切り替え機能
    function initColumnTabs() {
        const tabButtons = document.querySelectorAll('.column-tab-btn');
        const tabPanels = document.querySelectorAll('.column-tab-panel');
        
        if (!tabButtons.length || !tabPanels.length) {
            return;
        }
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                const categoryId = this.dataset.categoryId;
                
                // すべてのタブを非アクティブ化
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // すべてのパネルを非表示
                tabPanels.forEach(panel => {
                    panel.classList.remove('active');
                    panel.setAttribute('hidden', '');
                });
                
                // クリックされたタブをアクティブ化
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // 対応するパネルを表示
                const targetPanel = document.getElementById('panel-col-' + targetTab);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.removeAttribute('hidden');
                    
                    // カテゴリタブでコンテンツが未読み込みの場合はAjaxで取得
                    if (categoryId && !targetPanel.dataset.loaded) {
                        loadCategoryColumns(targetPanel, targetTab);
                    }
                }
                
                console.log('[Column Tabs] Switched to:', targetTab);
            });
        });
    }
    
    // カテゴリ別コラムをAjaxで読み込み
    function loadCategoryColumns(panel, categorySlug) {
        const grid = panel.querySelector('.columns-grid');
        if (!grid) return;
        
        // ローディング表示
        grid.innerHTML = '<div class="loading-message"><i class="fas fa-spinner fa-spin"></i><p>読み込み中...</p></div>';
        
        // WordPressのREST APIを使用してコラムを取得
        const restUrl = window.location.origin + '/wp-json/wp/v2/columns?column_category=' + categorySlug + '&per_page=9&orderby=date&order=desc';
        
        fetch(restUrl)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    // コラムカードをレンダリング
                    grid.innerHTML = data.map(column => renderColumnCard(column)).join('');
                } else {
                    grid.innerHTML = '<p class="no-data"><i class="fas fa-info-circle"></i>このカテゴリのコラムはまだありません。</p>';
                }
                panel.dataset.loaded = 'true';
            })
            .catch(error => {
                console.error('[Column Tabs] Load error:', error);
                grid.innerHTML = '<p class="no-data"><i class="fas fa-exclamation-circle"></i>読み込みに失敗しました。</p>';
            });
    }
    
    // コラムカードをレンダリング (簡易版)
    function renderColumnCard(column) {
        // このレンダリングは実際にはget_template_part()で生成されたカードを使用することが望ましい
        // ここでは最小限の実装
        return `
            <article class="column-card">
                <a href="${column.link}" class="column-card-link">
                    <h3 class="column-card-title">${column.title.rendered}</h3>
                </a>
            </article>
        `;
    }
    
    // 初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initColumnTabs);
    } else {
        initColumnTabs();
    }
    
    console.log('[OK] Column Tabs (Yahoo Style) initialized');
    
})();
</script>
