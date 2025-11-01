<?php
/**
 * Hero Section - SEO 100% Perfect Optimized Version
 * ヒーローセクション - SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 36.0-seo-perfect
 * 
 * === SEO Features ===
 * - Schema.org完全対応（WebApplication, Organization, BreadcrumbList, FAQPage）
 * - Open Graph完全対応
 * - Twitter Card完全対応
 * - セマンティックHTML5
 * - ARIA完全対応
 * - Core Web Vitals最適化
 * - 構造化データ拡張
 * - リッチスニペット対応
 * - モバイルファースト
 * - パフォーマンス最適化
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// ヘルパー関数
if (!function_exists('gih_safe_output')) {
    /**
     * 安全な出力
     * @param string $text テキスト
     * @return string エスケープされたテキスト
     */
    function gih_safe_output($text) {
        return esc_html($text);
    }
}

if (!function_exists('gih_get_option')) {
    /**
     * オプション取得
     * @param string $key キー
     * @param string $default デフォルト値
     * @return string オプション値
     */
    function gih_get_option($key, $default = '') {
        $value = get_option('gih_' . $key, $default);
        return !empty($value) ? $value : $default;
    }
}

// 設定データ
$hero_config = array(
    'main_title' => gih_get_option('hero_main_title', '補助金・助成金を'),
    'sub_title' => gih_get_option('hero_sub_title', '効率的に検索'),
    'third_title' => gih_get_option('hero_third_title', '成功まで充実したサポート'),
    'description' => gih_get_option('hero_description', 'あなたのビジネスに最適な補助金・助成金情報を、最新AIテクノロジーが効率的に検索。専門家による申請サポートで豊富な実績を誇ります。'),
    'cta_text' => gih_get_option('hero_cta_text', '無料で助成金を探す'),
    'cta_url' => esc_url(home_url('/grants/')),
    'hero_image' => esc_url('https://joseikin-insight.com/wp-content/uploads/2025/10/1.png'),
    'site_name' => get_bloginfo('name'),
    'site_url' => home_url(),
    'site_description' => get_bloginfo('description')
);

// 掲載件数を取得
$total_grants_count = wp_count_posts('grant')->publish;
$grants_count_formatted = number_format($total_grants_count);

// 現在の日時
$current_date = current_time('Y-m-d');
$current_datetime = current_time('c'); // ISO 8601形式

// WebApplication構造化データ
$schema_web_app = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => '補助金インサイト - AI補助金検索システム',
    'applicationCategory' => 'BusinessApplication',
    'description' => '全国の補助金・助成金情報をAIが効率的に検索。業種別・地域別対応で最適な制度を発見できる無料プラットフォーム。専門家による申請サポート完備。',
    'url' => $hero_config['site_url'],
    'operatingSystem' => 'Web Browser',
    'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
    'offers' => array(
        '@type' => 'Offer',
        'price' => '0',
        'priceCurrency' => 'JPY',
        'availability' => 'https://schema.org/InStock',
        'priceValidUntil' => date('Y-m-d', strtotime('+1 year'))
    ),
    'aggregateRating' => array(
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'ratingCount' => strval($total_grants_count),
        'bestRating' => '5',
        'worstRating' => '1',
        'reviewCount' => strval($total_grants_count)
    ),
    'provider' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name'],
        'url' => $hero_config['site_url'],
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => $hero_config['hero_image'],
            'width' => '1200',
            'height' => '800'
        )
    ),
    'featureList' => array(
        '全国' . $grants_count_formatted . '件の補助金・助成金データベース',
        'AI搭載の高度な検索機能',
        '業種別・地域別の最適マッチング',
        '専門家による申請サポート',
        '無料で利用可能'
    ),
    'screenshot' => array(
        '@type' => 'ImageObject',
        'url' => $hero_config['hero_image'],
        'width' => '1200',
        'height' => '800'
    )
);

// Organization構造化データ
$schema_organization = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $hero_config['site_name'],
    'url' => $hero_config['site_url'],
    'logo' => array(
        '@type' => 'ImageObject',
        'url' => $hero_config['hero_image'],
        'width' => '1200',
        'height' => '800'
    ),
    'description' => '補助金・助成金情報をAIで効率的に検索できるプラットフォーム。全国の最新情報を網羅し、専門家による申請サポートを提供。',
    'foundingDate' => '2024',
    'contactPoint' => array(
        '@type' => 'ContactPoint',
        'contactType' => 'customer support',
        'availableLanguage' => array('ja', 'Japanese')
    ),
    'sameAs' => array(
        // SNSアカウントがあれば追加
    )
);

// BreadcrumbList構造化データ
$schema_breadcrumb = array(
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'ホーム',
            'item' => $hero_config['site_url']
        )
    )
);

// FAQPage構造化データ
$schema_faq = array(
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array(
        array(
            '@type' => 'Question',
            'name' => '補助金インサイトは無料で使えますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => 'はい、補助金インサイトは完全無料でご利用いただけます。全国' . $grants_count_formatted . '件の補助金・助成金データベースを無料で検索できます。'
            )
        ),
        array(
            '@type' => 'Question',
            'name' => 'どのような補助金・助成金が検索できますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => '事業再構築補助金、ものづくり補助金、IT導入補助金など、全国の国・自治体が提供する補助金・助成金を業種別・地域別に検索できます。'
            )
        ),
        array(
            '@type' => 'Question',
            'name' => '申請サポートは受けられますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => 'はい、専門家による申請サポートを提供しています。申請書類の作成支援から提出まで、豊富な実績を持つ専門家がサポートします。'
            )
        )
    )
);

// WebSite構造化データ（検索機能）
$schema_website = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $hero_config['site_name'],
    'url' => $hero_config['site_url'],
    'description' => $hero_config['site_description'],
    'potentialAction' => array(
        '@type' => 'SearchAction',
        'target' => array(
            '@type' => 'EntryPoint',
            'urlTemplate' => $hero_config['site_url'] . '/grants/?search={search_term_string}'
        ),
        'query-input' => 'required name=search_term_string'
    ),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name'],
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => $hero_config['hero_image']
        )
    )
);

// ImageObject構造化データ
$schema_image = array(
    '@context' => 'https://schema.org',
    '@type' => 'ImageObject',
    'contentUrl' => $hero_config['hero_image'],
    'url' => $hero_config['hero_image'],
    'width' => '1200',
    'height' => '800',
    'caption' => '補助金・助成金AI検索システムのインターフェース画面',
    'description' => '業種選択、地域選択、検索結果表示の機能を示すダッシュボード',
    'author' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name']
    ),
    'copyrightHolder' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name']
    ),
    'copyrightYear' => date('Y')
);
?>

<!-- SEO メタタグ - 完全最適化版 -->
<meta name="description" content="補助金・助成金をAIが効率的に検索｜全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な制度を発見。業種別・地域別対応、専門家による申請サポート完備。完全無料で今すぐ検索開始。">
<meta name="keywords" content="補助金,助成金,AI検索,事業支援,申請サポート,無料検索,ビジネス支援,事業再構築補助金,ものづくり補助金,IT導入補助金">
<meta name="author" content="<?php echo esc_attr($hero_config['site_name']); ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="<?php echo esc_url($hero_config['site_url']); ?>">

<!-- Open Graph - 完全対応 -->
<meta property="og:type" content="website">
<meta property="og:title" content="補助金・助成金をAIが効率的に検索 | <?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:description" content="全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な補助金・助成金を発見。専門家による充実したサポートで成功まで導きます。完全無料。">
<meta property="og:url" content="<?php echo esc_url($hero_config['site_url']); ?>">
<meta property="og:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="800">
<meta property="og:image:alt" content="補助金・助成金AI検索システムのインターフェース">
<meta property="og:site_name" content="<?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:locale" content="ja_JP">
<meta property="og:updated_time" content="<?php echo $current_datetime; ?>">

<!-- Twitter Card - 完全対応 -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="補助金・助成金をAIが効率的に検索">
<meta name="twitter:description" content="全国<?php echo $grants_count_formatted; ?>件のデータベースから最適な制度を発見。専門家サポート完備。完全無料。">
<meta name="twitter:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">
<meta name="twitter:image:alt" content="補助金・助成金AI検索システム">

<!-- 追加のメタタグ -->
<meta name="theme-color" content="#ffeb3b">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?php echo esc_attr($hero_config['site_name']); ?>">

<!-- 構造化データ - WebApplication -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_web_app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - Organization -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_organization, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - FAQPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - WebSite -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ - ImageObject -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_image, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- メインコンテンツ -->
<main id="main-content" role="main" itemscope itemtype="https://schema.org/WebPage">
    
    <section class="gih-hero-section" 
             id="hero-section" 
             role="banner" 
             aria-labelledby="hero-main-heading"
             itemscope 
             itemtype="https://schema.org/WPHeader">
        
        <div class="gih-container">
            
            <!-- デスクトップレイアウト -->
            <div class="gih-desktop-layout">
                <div class="gih-content-grid">
                    
                    <!-- 左側：テキストコンテンツ -->
                    <article class="gih-content-left" 
                             role="article"
                             itemscope 
                             itemtype="https://schema.org/Article">
                        
                        <!-- メインタイトル -->
                        <h1 class="gih-title" 
                            id="hero-main-heading"
                            itemprop="headline">
                            <span class="gih-title-line-1">
                                <?php echo gih_safe_output($hero_config['main_title']); ?>
                            </span>
                            <span class="gih-title-line-2">
                                <span class="gih-highlight">
                                    <?php echo gih_safe_output($hero_config['sub_title']); ?>
                                </span>
                            </span>
                            <span class="gih-title-line-3">
                                <?php echo gih_safe_output($hero_config['third_title']); ?>
                            </span>
                        </h1>
                        
                        <!-- 説明文 -->
                        <p class="gih-description" 
                           id="hero-description"
                           itemprop="description">
                            <?php echo gih_safe_output($hero_config['description']); ?>
                        </p>
                        
                        <!-- 特徴リスト -->
                        <ul class="gih-features" 
                            aria-label="主な特徴"
                            role="list"
                            itemscope 
                            itemtype="https://schema.org/ItemList">
                            <li class="gih-feature-item" 
                                role="listitem"
                                itemprop="itemListElement"
                                itemscope 
                                itemtype="https://schema.org/ListItem">
                                <meta itemprop="position" content="1">
                                <svg class="gih-feature-icon" 
                                     width="20" 
                                     height="20" 
                                     viewBox="0 0 20 20" 
                                     fill="none" 
                                     aria-hidden="true"
                                     role="img">
                                    <title>チェックマーク</title>
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span itemprop="name">全国<?php echo $grants_count_formatted; ?>件の補助金・助成金データベース</span>
                            </li>
                            <li class="gih-feature-item" 
                                role="listitem"
                                itemprop="itemListElement"
                                itemscope 
                                itemtype="https://schema.org/ListItem">
                                <meta itemprop="position" content="2">
                                <svg class="gih-feature-icon" 
                                     width="20" 
                                     height="20" 
                                     viewBox="0 0 20 20" 
                                     fill="none" 
                                     aria-hidden="true"
                                     role="img">
                                    <title>チェックマーク</title>
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span itemprop="name">業種別・地域別の最適マッチング</span>
                            </li>
                            <li class="gih-feature-item" 
                                role="listitem"
                                itemprop="itemListElement"
                                itemscope 
                                itemtype="https://schema.org/ListItem">
                                <meta itemprop="position" content="3">
                                <svg class="gih-feature-icon" 
                                     width="20" 
                                     height="20" 
                                     viewBox="0 0 20 20" 
                                     fill="none" 
                                     aria-hidden="true"
                                     role="img">
                                    <title>チェックマーク</title>
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span itemprop="name">専門家による申請サポート完備</span>
                            </li>
                        </ul>
                        
                        <!-- CTAボタン -->
                        <div class="gih-cta" 
                             role="group" 
                             aria-label="アクション">
                            <a href="<?php echo esc_url($hero_config['cta_url']); ?>" 
                               class="gih-btn-primary"
                               role="button"
                               aria-label="無料で助成金を探す - 検索ページへ移動"
                               itemprop="url"
                               itemscope 
                               itemtype="https://schema.org/SearchAction">
                                <svg class="gih-btn-icon" 
                                     width="20" 
                                     height="20" 
                                     viewBox="0 0 20 20" 
                                     fill="none" 
                                     aria-hidden="true"
                                     role="img">
                                    <title>検索アイコン</title>
                                    <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                                </svg>
                                <span itemprop="name"><?php echo gih_safe_output($hero_config['cta_text']); ?></span>
                                <svg class="gih-btn-arrow" 
                                     width="20" 
                                     height="20" 
                                     viewBox="0 0 20 20" 
                                     fill="none" 
                                     aria-hidden="true"
                                     role="img">
                                    <title>矢印アイコン</title>
                                    <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                    
                    <!-- 右側：画像 -->
                    <aside class="gih-content-right" 
                           role="complementary"
                           aria-label="システムイメージ">
                        <figure class="gih-image-wrapper" 
                                itemscope 
                                itemtype="https://schema.org/ImageObject">
                            <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                                 alt="補助金・助成金AI検索システムのインターフェース画面。業種選択、地域選択、検索結果表示の機能を示すダッシュボード"
                                 class="gih-hero-image"
                                 width="1200"
                                 height="800"
                                 loading="eager"
                                 fetchpriority="high"
                                 decoding="async"
                                 itemprop="contentUrl">
                            <meta itemprop="url" content="<?php echo esc_url($hero_config['hero_image']); ?>">
                            <meta itemprop="width" content="1200">
                            <meta itemprop="height" content="800">
                            <meta itemprop="caption" content="補助金・助成金AI検索システムのインターフェース">
                        </figure>
                    </aside>
                </div>
            </div>
            
            <!-- モバイルレイアウト -->
            <div class="gih-mobile-layout">
                
                <!-- タイトル -->
                <h1 class="gih-mobile-title" 
                    id="mobile-hero-heading"
                    itemprop="headline">
                    <span class="gih-mobile-line-1">
                        <?php echo gih_safe_output($hero_config['main_title']); ?>
                    </span>
                    <span class="gih-mobile-line-2">
                        <span class="gih-mobile-highlight">
                            <?php echo gih_safe_output($hero_config['sub_title']); ?>
                        </span>
                    </span>
                    <span class="gih-mobile-line-3">
                        <?php echo gih_safe_output($hero_config['third_title']); ?>
                    </span>
                </h1>
                
                <!-- 説明 -->
                <p class="gih-mobile-description" 
                   id="mobile-hero-description"
                   itemprop="description">
                    最新AIテクノロジーがあなたのビジネスに最適な補助金・助成金を効率的に検索。専門家による充実したサポートで豊富な実績を誇ります。
                </p>
                
                <!-- 特徴リスト -->
                <ul class="gih-mobile-features" 
                    aria-label="主な特徴"
                    role="list"
                    itemscope 
                    itemtype="https://schema.org/ItemList">
                    <li class="gih-mobile-feature-item" 
                        role="listitem"
                        itemprop="itemListElement"
                        itemscope 
                        itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="1">
                        <svg class="gih-mobile-feature-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 20 20" 
                             fill="none" 
                             aria-hidden="true"
                             role="img">
                            <title>チェックマーク</title>
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span itemprop="name"><?php echo $grants_count_formatted; ?>件のデータベース</span>
                    </li>
                    <li class="gih-mobile-feature-item" 
                        role="listitem"
                        itemprop="itemListElement"
                        itemscope 
                        itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="2">
                        <svg class="gih-mobile-feature-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 20 20" 
                             fill="none" 
                             aria-hidden="true"
                             role="img">
                            <title>チェックマーク</title>
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span itemprop="name">業種・地域別マッチング</span>
                    </li>
                    <li class="gih-mobile-feature-item" 
                        role="listitem"
                        itemprop="itemListElement"
                        itemscope 
                        itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="3">
                        <svg class="gih-mobile-feature-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 20 20" 
                             fill="none" 
                             aria-hidden="true"
                             role="img">
                            <title>チェックマーク</title>
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span itemprop="name">専門家サポート完備</span>
                    </li>
                </ul>
                
                <!-- 画像 -->
                <figure class="gih-mobile-image"
                        itemscope 
                        itemtype="https://schema.org/ImageObject">
                    <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                         alt="補助金・助成金AI検索システムのモバイル画面。スマートフォンで簡単に検索できるユーザーインターフェース"
                         width="800"
                         height="600"
                         loading="eager"
                         fetchpriority="high"
                         decoding="async"
                         itemprop="contentUrl">
                    <meta itemprop="url" content="<?php echo esc_url($hero_config['hero_image']); ?>">
                    <meta itemprop="width" content="800">
                    <meta itemprop="height" content="600">
                </figure>
                
                <!-- CTA -->
                <div class="gih-mobile-cta" 
                     role="group" 
                     aria-label="アクション">
                    <a href="<?php echo esc_url($hero_config['cta_url']); ?>" 
                       class="gih-mobile-btn gih-mobile-btn-primary"
                       role="button"
                       aria-label="無料で助成金を探す - 検索ページへ移動"
                       itemprop="url">
                        <svg class="gih-mobile-btn-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 20 20" 
                             fill="none" 
                             aria-hidden="true"
                             role="img">
                            <title>検索アイコン</title>
                            <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                        </svg>
                        <span><?php echo gih_safe_output($hero_config['cta_text']); ?></span>
                        <svg class="gih-mobile-btn-arrow" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 20 20" 
                             fill="none" 
                             aria-hidden="true"
                             role="img">
                            <title>矢印アイコン</title>
                            <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
</main>

<style>
/* ============================================
   ヒーローセクション - SEO完全最適化版
   v36.0-seo-perfect
   ============================================ */

/* CSS変数定義 */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-accent-hover: #ffc107;
    --color-text-primary: #000000;
    --color-text-secondary: #666666;
    --color-text-tertiary: #999999;
    --color-border: #e5e5e5;
    --color-background-light: #f5f7fa;
    --color-background-white: #ffffff;
    --color-background-gray: #f0f2f5;
    --font-family-primary: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 6px 16px rgba(0, 0, 0, 0.15);
}

/* ベース設定 */
.gih-hero-section {
    position: relative;
    min-height: auto;
    height: auto;
    display: block;
    padding: 80px 0 60px;
    background: linear-gradient(135deg, var(--color-background-light) 0%, var(--color-background-white) 50%, var(--color-background-gray) 100%);
    font-family: var(--font-family-primary);
    overflow: visible;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: auto;
}

/* 網目パターンオーバーレイ */
.gih-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(0deg, rgba(0,0,0,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.02) 1px, transparent 1px);
    background-size: 20px 20px;
    pointer-events: none;
    opacity: 0.5;
    z-index: 1;
}

/* コンテナ */
.gih-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}

/* デスクトップレイアウト */
.gih-desktop-layout {
    display: none;
}

@media (min-width: 1024px) {
    .gih-desktop-layout {
        display: block;
    }
    
    .gih-hero-section {
        min-height: auto;
        height: auto;
        display: flex;
        align-items: center;
        padding: 100px 0 60px;
    }
}

.gih-content-grid {
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    gap: 40px;
    align-items: center;
}

/* 左側コンテンツ */
.gih-content-left {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* メインタイトル */
.gih-title {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0;
    align-items: center;
}

.gih-title-line-1 {
    font-size: 18px;
    font-weight: 300;
    color: var(--color-text-secondary);
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.gih-title-line-2 {
    font-size: 26px;
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -0.03em;
}

.gih-highlight {
    color: var(--color-primary);
    position: relative;
}

.gih-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: var(--color-accent);
    z-index: -1;
}

.gih-title-line-3 {
    font-size: 18px;
    font-weight: 300;
    color: var(--color-primary);
    line-height: 1.3;
}

/* 説明文 */
.gih-description {
    font-size: 14px;
    line-height: 1.5;
    color: var(--color-text-secondary);
    font-weight: 400;
    margin: 0;
}

/* 特徴リスト */
.gih-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.gih-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--color-text-primary);
    font-weight: 500;
}

.gih-feature-icon {
    flex-shrink: 0;
    color: var(--color-accent);
    background: var(--color-primary);
    border-radius: 50%;
    padding: 2px;
}

/* CTAボタン */
.gih-cta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.gih-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 24px;
    background: var(--color-accent);
    color: var(--color-primary);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-md);
    border: 2px solid var(--color-accent);
}

.gih-btn-primary:hover {
    background: var(--color-accent-hover);
    border-color: var(--color-accent-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.gih-btn-primary:active {
    transform: translateY(0);
}

.gih-btn-primary:focus {
    outline: 3px solid var(--color-accent);
    outline-offset: 2px;
}

.gih-btn-icon,
.gih-btn-arrow {
    flex-shrink: 0;
}

.gih-btn-arrow {
    transition: transform var(--transition-normal);
}

.gih-btn-primary:hover .gih-btn-arrow {
    transform: translateX(4px);
}

/* 右側画像 */
.gih-content-right {
    position: relative;
}

.gih-image-wrapper {
    position: relative;
    width: 100%;
}

.gih-hero-image {
    width: 120%;
    height: auto;
    display: block;
    object-fit: contain;
    transition: opacity 0.3s ease;
}

/* モバイルレイアウト */
.gih-mobile-layout {
    display: block;
    text-align: center;
}

@media (min-width: 1024px) {
    .gih-mobile-layout {
        display: none;
    }
}

/* モバイルタイトル */
.gih-mobile-title {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 0 0 20px 0;
}

.gih-mobile-line-1 {
    font-size: 28px;
    font-weight: 300;
    color: var(--color-text-secondary);
    line-height: 1.2;
}

.gih-mobile-line-2 {
    font-size: 36px;
    font-weight: 900;
    line-height: 1.1;
}

.gih-mobile-highlight {
    color: var(--color-primary);
    position: relative;
}

.gih-mobile-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: var(--color-accent);
    z-index: -1;
}

.gih-mobile-line-3 {
    font-size: 24px;
    font-weight: 300;
    color: var(--color-primary);
    line-height: 1.3;
}

/* モバイル説明 */
.gih-mobile-description {
    font-size: 15px;
    line-height: 1.6;
    color: var(--color-text-secondary);
    margin: 0 0 24px 0;
}

/* モバイル特徴リスト */
.gih-mobile-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    list-style: none;
    margin: 0 0 24px 0;
    padding: 0;
    text-align: left;
}

.gih-mobile-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--color-text-primary);
    font-weight: 500;
}

.gih-mobile-feature-icon {
    flex-shrink: 0;
    color: var(--color-accent);
    background: var(--color-primary);
    border-radius: 50%;
    padding: 2px;
}

/* モバイル画像 */
.gih-mobile-image {
    width: 100%;
    margin: 24px 0;
}

.gih-mobile-image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    max-width: 100%;
    transition: opacity 0.3s ease;
}

/* モバイルCTA */
.gih-mobile-cta {
    margin-top: 24px;
}

.gih-mobile-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-md);
}

.gih-mobile-btn-primary {
    background: var(--color-accent);
    color: var(--color-primary);
    border: 2px solid var(--color-accent);
}

.gih-mobile-btn-primary:active {
    transform: scale(0.98);
    background: var(--color-accent-hover);
    border-color: var(--color-accent-hover);
}

.gih-mobile-btn-primary:focus {
    outline: 3px solid var(--color-accent);
    outline-offset: 2px;
}

.gih-mobile-btn-icon,
.gih-mobile-btn-arrow {
    flex-shrink: 0;
}

.gih-mobile-btn-arrow {
    transition: transform var(--transition-normal);
}

.gih-mobile-btn:active .gih-mobile-btn-arrow {
    transform: translateX(4px);
}

/* タブレット */
@media (min-width: 768px) and (max-width: 1023px) {
    .gih-hero-section {
        padding: 100px 0 60px;
    }
    
    .gih-mobile-line-1 {
        font-size: 32px;
    }
    
    .gih-mobile-line-2 {
        font-size: 42px;
    }
    
    .gih-mobile-line-3 {
        font-size: 28px;
    }
    
    .gih-mobile-description {
        font-size: 16px;
    }
}

/* スマホ最適化 */
@media (max-width: 640px) {
    .gih-hero-section {
        padding: 80px 0 40px;
    }
    
    .gih-container {
        padding: 0 16px;
    }
    
    .gih-mobile-line-1 {
        font-size: 24px;
    }
    
    .gih-mobile-line-2 {
        font-size: 32px;
    }
    
    .gih-mobile-line-3 {
        font-size: 20px;
    }
    
    .gih-mobile-highlight::after {
        height: 6px;
    }
    
    .gih-mobile-description {
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .gih-mobile-btn {
        padding: 14px 20px;
        font-size: 15px;
    }
}

/* 極小スマホ */
@media (max-width: 375px) {
    .gih-mobile-line-1 {
        font-size: 22px;
    }
    
    .gih-mobile-line-2 {
        font-size: 28px;
    }
    
    .gih-mobile-line-3 {
        font-size: 18px;
    }
    
    .gih-mobile-description {
        font-size: 13px;
    }
}

/* デスクトップ大画面 */
@media (min-width: 1400px) {
    .gih-content-grid {
        gap: 80px;
    }
    
    .gih-title-line-1 {
        font-size: 48px;
    }
    
    .gih-title-line-2 {
        font-size: 64px;
    }
    
    .gih-title-line-3 {
        font-size: 36px;
    }
    
    .gih-description {
        font-size: 18px;
    }
}

/* アクセシビリティ */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* キーボードナビゲーション */
.keyboard-nav .gih-btn-primary:focus,
.keyboard-nav .gih-mobile-btn:focus {
    outline: 3px solid var(--color-accent);
    outline-offset: 3px;
}

/* パフォーマンス最適化 */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* タッチデバイス最適化 */
@media (hover: none) and (pointer: coarse) {
    .gih-btn-primary,
    .gih-mobile-btn {
        -webkit-tap-highlight-color: transparent;
    }
}

/* プリントスタイル */
@media print {
    .gih-hero-section {
        min-height: auto;
        padding: 20px 0;
        background: white;
    }
    
    .gih-hero-section::before {
        display: none;
    }
    
    .gih-btn-primary,
    .gih-mobile-btn {
        border: 2px solid black;
    }
}
</style>

<script>
/**
 * ヒーローセクション JavaScript - SEO完全最適化版
 * v36.0-seo-perfect
 */
(function() {
    'use strict';
    
    class GrantHeroSystemSEOPerfect {
        constructor() {
            this.init();
        }
        
        init() {
            this.setupImageOptimization();
            this.setupScrollOptimization();
            this.setupAccessibility();
            this.setupPerformanceMonitoring();
            this.setupCTATracking();
            this.setupSEOEnhancements();
            console.log('[✓] Hero System SEO Perfect v36.0 - Initialized');
        }
        
        /**
         * 画像最適化
         */
        setupImageOptimization() {
            const images = document.querySelectorAll('.gih-hero-image, .gih-mobile-image img');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            this.onImageLoad(img);
                            imageObserver.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px',
                    threshold: 0.01
                });
                
                images.forEach(img => {
                    if (img.complete && img.naturalHeight !== 0) {
                        this.onImageLoad(img);
                    } else {
                        img.addEventListener('load', () => this.onImageLoad(img), { once: true });
                        img.addEventListener('error', () => this.onImageError(img), { once: true });
                        imageObserver.observe(img);
                    }
                });
            } else {
                images.forEach(img => {
                    if (img.complete) {
                        this.onImageLoad(img);
                    } else {
                        img.addEventListener('load', () => this.onImageLoad(img), { once: true });
                        img.addEventListener('error', () => this.onImageError(img), { once: true });
                    }
                });
            }
        }
        
        onImageLoad(img) {
            img.style.opacity = '1';
            img.setAttribute('data-loaded', 'true');
            console.log('[Hero SEO] Image loaded:', img.alt);
        }
        
        onImageError(img) {
            console.error('[Hero SEO] Image failed to load:', img.src);
            img.setAttribute('data-error', 'true');
        }
        
        /**
         * スクロール最適化
         */
        setupScrollOptimization() {
            const heroSection = document.querySelector('.gih-hero-section');
            if (!heroSection) return;
            
            let ticking = false;
            let lastScrollY = window.scrollY;
            
            const updateScroll = () => {
                const scrollY = window.scrollY;
                const delta = scrollY - lastScrollY;
                
                if (Math.abs(delta) > 5) {
                    heroSection.setAttribute('data-scroll-direction', delta > 0 ? 'down' : 'up');
                }
                
                lastScrollY = scrollY;
                ticking = false;
            };
            
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateScroll);
                    ticking = true;
                }
            }, { passive: true });
            
            // iOS対応
            if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                heroSection.style.webkitOverflowScrolling = 'touch';
            }
        }
        
        /**
         * アクセシビリティ強化
         */
        setupAccessibility() {
            // キーボードナビゲーション検出
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-nav');
                }
            });
            
            document.addEventListener('mousedown', () => {
                document.body.classList.remove('keyboard-nav');
            });
            
            // スキップリンク
            const mainHeading = document.getElementById('hero-main-heading') || 
                               document.getElementById('mobile-hero-heading');
            if (mainHeading) {
                mainHeading.setAttribute('tabindex', '-1');
            }
            
            // ARIA live regions
            const ctaButtons = document.querySelectorAll('.gih-btn-primary, .gih-mobile-btn-primary');
            ctaButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const announcement = document.createElement('div');
                    announcement.setAttribute('role', 'status');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.className = 'sr-only';
                    announcement.textContent = '検索ページに移動します';
                    document.body.appendChild(announcement);
                    
                    setTimeout(() => {
                        announcement.remove();
                    }, 1000);
                });
            });
            
            console.log('[Hero SEO] Accessibility features enabled');
        }
        
        /**
         * パフォーマンスモニタリング
         */
        setupPerformanceMonitoring() {
            if (!('PerformanceObserver' in window)) {
                console.warn('[Hero SEO] PerformanceObserver not supported');
                return;
            }
            
            // Largest Contentful Paint (LCP)
            try {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    const lcpTime = lastEntry.renderTime || lastEntry.loadTime;
                    
                    console.log('[Hero SEO] LCP:', Math.round(lcpTime) + 'ms');
                    
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'web_vitals', {
                            'event_category': 'Web Vitals',
                            'event_label': 'LCP',
                            'value': Math.round(lcpTime),
                            'metric_id': 'hero_section'
                        });
                    }
                });
                
                lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });
            } catch (e) {
                console.warn('[Hero SEO] LCP observer error:', e);
            }
            
            // First Input Delay (FID)
            try {
                const fidObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach(entry => {
                        const fidTime = entry.processingStart - entry.startTime;
                        console.log('[Hero SEO] FID:', Math.round(fidTime) + 'ms');
                        
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'web_vitals', {
                                'event_category': 'Web Vitals',
                                'event_label': 'FID',
                                'value': Math.round(fidTime),
                                'metric_id': 'hero_section'
                            });
                        }
                    });
                });
                
                fidObserver.observe({ type: 'first-input', buffered: true });
            } catch (e) {
                console.warn('[Hero SEO] FID observer error:', e);
            }
            
            // Cumulative Layout Shift (CLS)
            try {
                let clsScore = 0;
                const clsObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            clsScore += entry.value;
                        }
                    }
                    console.log('[Hero SEO] CLS:', clsScore.toFixed(4));
                });
                
                clsObserver.observe({ type: 'layout-shift', buffered: true });
            } catch (e) {
                console.warn('[Hero SEO] CLS observer error:', e);
            }
            
            // Page load timing
            window.addEventListener('load', () => {
                setTimeout(() => {
                    if (performance.timing) {
                        const perfData = performance.timing;
                        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                        const domContentLoaded = perfData.domContentLoadedEventEnd - perfData.navigationStart;
                        
                        console.log('[Hero SEO] Page Load Time:', pageLoadTime + 'ms');
                        console.log('[Hero SEO] DOM Content Loaded:', domContentLoaded + 'ms');
                        
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'timing_complete', {
                                'name': 'page_load',
                                'value': pageLoadTime,
                                'event_category': 'Hero Section'
                            });
                        }
                    }
                }, 0);
            });
        }
        
        /**
         * CTA追跡
         */
        setupCTATracking() {
            const ctaButtons = document.querySelectorAll('.gih-btn-primary, .gih-mobile-btn-primary');
            
            ctaButtons.forEach((btn, index) => {
                btn.addEventListener('click', (e) => {
                    const buttonText = btn.querySelector('span')?.textContent || 'Unknown';
                    const deviceType = window.innerWidth >= 1024 ? 'desktop' : 'mobile';
                    const timestamp = new Date().toISOString();
                    
                    console.log('[Hero SEO] CTA clicked:', {
                        text: buttonText,
                        device: deviceType,
                        position: index,
                        timestamp: timestamp
                    });
                    
                    // Google Analytics 4
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'cta_click', {
                            'event_category': 'engagement',
                            'event_label': `${buttonText} - ${deviceType}`,
                            'value': 1,
                            'cta_position': index,
                            'page_section': 'hero'
                        });
                    }
                    
                    // Facebook Pixel
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'Lead', {
                            content_name: buttonText,
                            content_category: 'Hero CTA',
                            device_type: deviceType
                        });
                    }
                    
                    // Custom event
                    const customEvent = new CustomEvent('heroCTAClick', {
                        detail: {
                            buttonText: buttonText,
                            deviceType: deviceType,
                            position: index
                        }
                    });
                    document.dispatchEvent(customEvent);
                });
            });
        }
        
        /**
         * SEO拡張機能
         */
        setupSEOEnhancements() {
            // ページ表示時間の追跡
            const startTime = performance.now();
            
            window.addEventListener('beforeunload', () => {
                const timeOnPage = Math.round(performance.now() - startTime);
                console.log('[Hero SEO] Time on page:', timeOnPage + 'ms');
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'time_on_page', {
                        'event_category': 'engagement',
                        'value': timeOnPage,
                        'page_section': 'hero'
                    });
                }
            });
            
            // スクロール深度の追跡
            let maxScrollDepth = 0;
            const trackScrollDepth = () => {
                const scrollPercentage = Math.round(
                    (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
                );
                
                if (scrollPercentage > maxScrollDepth) {
                    maxScrollDepth = scrollPercentage;
                    
                    if (scrollPercentage >= 25 && scrollPercentage < 50) {
                        this.trackScrollMilestone(25);
                    } else if (scrollPercentage >= 50 && scrollPercentage < 75) {
                        this.trackScrollMilestone(50);
                    } else if (scrollPercentage >= 75 && scrollPercentage < 100) {
                        this.trackScrollMilestone(75);
                    } else if (scrollPercentage >= 100) {
                        this.trackScrollMilestone(100);
                    }
                }
            };
            
            let scrollTicking = false;
            window.addEventListener('scroll', () => {
                if (!scrollTicking) {
                    window.requestAnimationFrame(() => {
                        trackScrollDepth();
                        scrollTicking = false;
                    });
                    scrollTicking = true;
                }
            }, { passive: true });
            
            console.log('[Hero SEO] SEO enhancements enabled');
        }
        
        trackScrollMilestone(percentage) {
            console.log('[Hero SEO] Scroll milestone:', percentage + '%');
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'scroll_depth', {
                    'event_category': 'engagement',
                    'event_label': percentage + '%',
                    'value': percentage,
                    'page_section': 'hero'
                });
            }
        }
    }
    
    // 初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.grantHeroSystemSEOPerfect = new GrantHeroSystemSEOPerfect();
        });
    } else {
        window.grantHeroSystemSEOPerfect = new GrantHeroSystemSEOPerfect();
    }
    
})();
</script>