<?php
/**
 * Grant Insight Perfect - Front Page Template
 * テンプレートパーツを活用したシンプル構成
 *
 * @package Grant_Insight_Perfect
 * @version 7.1-optimized
 */

get_header(); ?>

<style>
/* フロントページ専用スタイル */
.site-main {
    padding: 0;
    background: #ffffff;
}

/* セクション間のスペーシング調整 */
.front-page-section {
    position: relative;
}

.front-page-section + .front-page-section {
    margin-top: -1px; /* セクション間の隙間を削除 */
}

/* スムーススクロール */
html {
    scroll-behavior: smooth;
}

/* セクションアニメーション - モバイル対応改善 */
.section-animate {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

/* デスクトップのみアニメーション適用 */
@media (min-width: 1024px) {
    .section-animate {
        opacity: 0;
        transform: translateY(30px);
    }
    
    .section-animate.visible {
        opacity: 1;
        transform: translateY(0);
    }
}

/* モバイル最適化 - スクロール問題完全解決 */
@media (max-width: 1023px) {
    html {
        height: 100%;
        overflow-y: auto !important;
        -webkit-text-size-adjust: 100%;
    }
    
    body {
        height: auto;
        min-height: 100vh;
        overflow-y: auto !important;
        overflow-x: hidden;
    }
    
    .site-main {
        display: block !important;
        width: 100% !important;
        overflow: visible !important;
        height: auto !important;
        min-height: auto !important;
    }
    
    /* フロントページ専用のスクロール修正 */
    .front-page-section {
        display: block !important;
        width: 100% !important;
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        position: relative !important;
        opacity: 1 !important;
        transform: none !important;
    }
    
    /* すべてのセクション要素を強制表示 */
    section {
        display: block !important;
        width: 100% !important;
        overflow: visible !important;
        opacity: 1 !important;
    }
}

/* スクロールプログレスバー */
.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    /* ===== 変更点1: プログレスバーの色を白黒（グレー）に変更 ===== */
    background: #333333;
    z-index: 9999;
    transition: width 0.1s ease;
    width: 0%;
}
</style>

<main id="main" class="site-main" role="main">

    <?php
    /**
     * 1. Hero Section
     * メインビジュアルとキャッチコピー
     */
    ?>
    <section class="front-page-section section-animate" id="hero-section">
        <?php get_template_part('template-parts/front-page/section', 'hero'); ?>
    </section>

    <?php
    // 広告: ヒーロー下部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-hero-bottom" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_hero_bottom', 'front-page'); ?>
        </div>
    <?php endif; ?>

    <?php
    /**
     * 2. Column Zone Section
     * コラムゾーン（補助金活用のヒントやノウハウ）
     */
    // 広告: コラムゾーン上部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-column-top" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_column_zone_top', 'front-page'); ?>
        </div>
    <?php endif; ?>
    <section class="front-page-section section-animate" id="column-section">
        <?php 
        error_log('[Front Page] Loading column zone');
        get_template_part('template-parts/column/zone'); 
        error_log('[Front Page] Column zone loaded');
        ?>
    </section>

    <?php
    // 広告: コラムゾーン下部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-column-bottom" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_column_zone_bottom', 'front-page'); ?>
        </div>
    <?php endif; ?>

    <?php
    /**
     * 3. Grant News Section
     * 補助金ニュース（締切間近・おすすめ・新着）
     */
    // 広告: 補助金ニュース上部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-grant-news-top" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_grant_news_top', 'front-page'); ?>
        </div>
    <?php endif; ?>
    <section class="front-page-section section-animate" id="grant-news-section">
        <?php 
        error_log('[Front Page] Loading grant news');
        // section-searchから変数を設定
        // 締切間近の補助金を取得（30日以内、最大9件）
        $all_grants_for_deadline = get_posts(array(
            'post_type' => 'grant',
            'posts_per_page' => 100,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        $deadline_soon_grants = array();
        $today = strtotime(date('Y-m-d'));
        $deadline_soon_date = strtotime('+30 days');

        foreach ($all_grants_for_deadline as $grant) {
            $deadline_date = get_field('deadline_date', $grant->ID);
            if (empty($deadline_date)) {
                $deadline_date = get_post_meta($grant->ID, 'deadline_date', true);
            }
            if (empty($deadline_date)) {
                $deadline_date = get_post_meta($grant->ID, '_deadline_date', true);
            }
            
            if (!empty($deadline_date)) {
                $deadline_timestamp = strtotime($deadline_date);
                if ($deadline_timestamp >= $today && $deadline_timestamp <= $deadline_soon_date) {
                    $deadline_soon_grants[] = $grant;
                    if (count($deadline_soon_grants) >= 9) break;
                }
            }
        }
        $deadline_soon_grants = is_array($deadline_soon_grants) ? $deadline_soon_grants : array();

        // レコメンド補助金を取得（注目度の高い9件）
        $all_grants_for_featured = get_posts(array(
            'post_type' => 'grant',
            'posts_per_page' => 100,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        $recommended_grants = array();
        foreach ($all_grants_for_featured as $grant) {
            $is_featured = get_field('is_featured', $grant->ID);
            if (empty($is_featured)) {
                $is_featured = get_post_meta($grant->ID, 'is_featured', true);
            }
            if (empty($is_featured)) {
                $is_featured = get_post_meta($grant->ID, '_is_featured', true);
            }
            
            if ($is_featured == '1' || $is_featured === true || $is_featured === 1) {
                $recommended_grants[] = $grant;
                if (count($recommended_grants) >= 9) break;
            }
        }

        // フォールバック：注目がない場合は最新のものを取得
        if (empty($recommended_grants)) {
            $recommended_grants = array_slice($all_grants_for_featured, 0, 9);
        }
        $recommended_grants = is_array($recommended_grants) ? $recommended_grants : array();

        // 新着補助金を取得（最新9件）
        $new_grants = get_posts(array(
            'post_type' => 'grant',
            'posts_per_page' => 9,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'suppress_filters' => false
        ));
        if (!is_array($new_grants)) {
            $new_grants = array();
        }
        // 空の場合、suppressを外して再試行
        if (empty($new_grants)) {
            $new_grants = get_posts(array(
                'post_type' => 'grant',
                'posts_per_page' => 9,
                'orderby' => 'ID',
                'order' => 'DESC',
                'post_status' => 'publish'
            ));
        }
        
        // 変数を明示的に渡す
        set_query_var('deadline_soon_grants', $deadline_soon_grants);
        set_query_var('recommended_grants', $recommended_grants);
        set_query_var('new_grants', $new_grants);
        get_template_part('template-parts/front-page/grant-tabs-section'); 
        error_log('[Front Page] Grant news loaded');
        ?>
    </section>

    <?php
    // 広告: 補助金ニュース下部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-grant-news-bottom" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_grant_news_bottom', 'front-page'); ?>
        </div>
    <?php endif; ?>

    <?php
    /**
     * 4. Grant Zone Section (補助金検索)
     * 補助金検索 + カテゴリ + 都道府県
     */
    // 広告: 検索エリア上部
    if (function_exists('ji_display_ad')): ?>
        <div class="front-ad-space front-ad-search-top" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('front_search_top', 'front-page'); ?>
        </div>
    <?php endif; ?>
    <section class="front-page-section section-animate" id="grant-zone-section">
        <?php 
        error_log('[Front Page] Loading grant zone');
        get_template_part('template-parts/front-page/section', 'search'); 
        error_log('[Front Page] Grant zone loaded');
        ?>
    </section>

</main>

<div class="scroll-progress" id="scroll-progress"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // セクションアニメーション（デスクトップのみ）
    if (window.innerWidth >= 1024) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    sectionObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.section-animate').forEach(section => {
            sectionObserver.observe(section);
        });
    } else {
        // モバイルではすべてのセクションを即座に表示
        document.querySelectorAll('.section-animate').forEach(section => {
            section.classList.add('visible');
            section.style.opacity = '1';
            section.style.transform = 'none';
        });
    }

    /* ===== 変更点2: スクロール処理をrequestAnimationFrameで最適化 ===== */
    const progressBar = document.getElementById('scroll-progress');
    let ticking = false;

    function updateProgressBar() {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = window.scrollY;
        // スクロール量が0未満またはscrollHeightが0の場合の計算エラーを防ぐ
        const progress = scrollHeight > 0 ? (scrolled / scrollHeight) * 100 : 0;
        
        if (progressBar) {
            progressBar.style.width = Math.min(progress, 100) + '%';
        }
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                updateProgressBar();
            });
            ticking = true;
        }
    });
    
    // 初期表示時にもプログレスバーを更新
    window.requestAnimationFrame(updateProgressBar);
    
    // パフォーマンス監視
    if ('performance' in window) {
        window.addEventListener('load', function() {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData) {
                console.log('[パフォーマンス] ページ読み込み時間:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
            }
        });
    }
    
    // ページ内リンクのスムーススクロール
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#' && href !== '#0') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offset = 80; // ヘッダーの高さ分調整
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    console.log('[OK] Grant Insight Perfect - フロントページ初期化完了 (v7.1-optimized)');
    
    // セクションの読み込み確認
    console.log('[Debug] Hero section exists:', !!document.getElementById('hero-section'));
    console.log('[Debug] Search section exists:', !!document.getElementById('search-section'));
    console.log('[Debug] Categories section exists:', !!document.getElementById('categories-section'));
    console.log('[Debug] Browse section integrated exists:', !!document.querySelector('.browse-section-integrated'));
});
</script>

<?php get_footer(); ?>