<?php
/**
 * Archive Template for Grant Post Type - SEO Perfect Version v18.0
 * 助成金・補助金アーカイブページ - SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 18.0.0 - SEO Perfect
 * 
 * === SEO Features ===
 * - Schema.org完全対応（CollectionPage, ItemList, BreadcrumbList, SearchAction）
 * - Open Graph完全対応
 * - Twitter Card完全対応
 * - セマンティックHTML5
 * - ARIA完全対応
 * - Core Web Vitals最適化
 * - 構造化データ拡張
 * - メタタグ最適化
 * - 内部リンク最適化
 */

get_header();

// 各種データ取得
$current_category = get_queried_object();
$is_category_archive = is_tax('grant_category');
$is_prefecture_archive = is_tax('grant_prefecture');
$is_municipality_archive = is_tax('grant_municipality');
$is_tag_archive = is_tax('grant_tag');

// タイトル・説明文の生成
if ($is_category_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->description ?: $current_category->name . 'に関する助成金・補助金の情報を網羅。申請方法から採択のコツまで、専門家監修の最新情報を提供しています。';
} elseif ($is_prefecture_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'で利用できる助成金・補助金の最新情報。地域別・業種別に検索可能。専門家による申請サポート完備。';
} elseif ($is_municipality_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'の地域密着型助成金・補助金情報。市町村独自の支援制度から国の制度まで幅広く掲載。';
} elseif ($is_tag_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'に関連する助成金・補助金の一覧。最新の募集情報を毎日更新。';
} else {
    $archive_title = '助成金・補助金総合検索';
    $archive_description = '全国の助成金・補助金情報を網羅的に検索。都道府県・市町村・業種・金額で詳細に絞り込み可能。専門家による申請サポート完備。毎日更新。';
}

// カテゴリデータの取得
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// SEO対策データ
$current_year = date('Y');
$current_month = date('n');
$popular_categories = array_slice($all_categories, 0, 6);
$current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
$canonical_url = $current_url;

// 都道府県データ
$prefectures = gi_get_all_prefectures();

$region_groups = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

if ($is_category_archive || $is_prefecture_archive || $is_municipality_archive || $is_tag_archive) {
    $breadcrumbs[] = ['name' => $archive_title, 'url' => ''];
} else {
    $breadcrumbs[] = ['name' => '検索結果', 'url' => ''];
}

// 構造化データ: CollectionPage
$schema_collection = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $archive_title,
    'description' => $archive_description,
    'url' => $canonical_url,
    'inLanguage' => 'ja-JP',
    'dateModified' => current_time('c'),
    'provider' => [
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => get_site_icon_url(512) ?: home_url('/wp-content/uploads/2025/10/1.png')
        ]
    ],
    'mainEntity' => [
        '@type' => 'ItemList',
        'name' => $archive_title,
        'description' => $archive_description,
        'numberOfItems' => $total_grants,
        'itemListElement' => []
    ]
];

// 構造化データ: BreadcrumbList
$breadcrumb_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => []
];

foreach ($breadcrumbs as $index => $breadcrumb) {
    $breadcrumb_schema['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $breadcrumb['name'],
        'item' => !empty($breadcrumb['url']) ? $breadcrumb['url'] : $canonical_url
    ];
}

// 構造化データ: SearchAction
$search_action_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'url' => home_url(),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => home_url('/?s={search_term_string}&post_type=grant')
        ],
        'query-input' => 'required name=search_term_string'
    ]
];

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', '検索', '申請', '支援制度'];
if ($is_category_archive) {
    $keywords[] = $current_category->name;
}
if ($is_prefecture_archive) {
    $keywords[] = $current_category->name;
}
$keywords_string = implode(',', $keywords);

/**
 * SEO メタタグ出力
 * 
 * NOTE: 以下のメタタグはwp_head()経由でSEOプラグインまたはテーマのSEO機能から
 * 一元的に出力されるため、ここでは重複を避けるためコメントアウトしています。
 * 
 * - meta description
 * - meta keywords
 * - meta robots
 * - canonical link
 * - Open Graph tags (og:*)
 * - Twitter Card tags (twitter:*)
 * 
 * これらのタグはheader.phpのwp_head()によって適切に出力されます。
 */
?>

<!-- 構造化データ: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: SearchAction -->
<script type="application/ld+json">
<?php echo wp_json_encode($search_action_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="grant-archive-optimized" 
      id="grant-archive" 
      role="main"
      itemscope 
      itemtype="https://schema.org/CollectionPage">

    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" 
         aria-label="パンくずリスト" 
         itemscope 
         itemtype="https://schema.org/BreadcrumbList">
        <div class="container">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <li class="breadcrumb-item" 
                    itemprop="itemListElement" 
                    itemscope 
                    itemtype="https://schema.org/ListItem">
                    <?php if (!empty($breadcrumb['url'])): ?>
                        <a href="<?php echo esc_url($breadcrumb['url']); ?>" 
                           itemprop="item"
                           title="<?php echo esc_attr($breadcrumb['name']); ?>へ移動">
                            <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                        </a>
                    <?php else: ?>
                        <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo $index + 1; ?>">
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- ヒーローセクション -->
    <header class="category-hero-section" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="container">
            <div class="hero-content-wrapper">
                
                <!-- カテゴリーバッジ -->
                <div class="category-badge" role="status">
                    <svg class="badge-icon" 
                         width="20" 
                         height="20" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true"
                         role="img">
                        <title>検索アイコン</title>
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>助成金・補助金検索</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="category-main-title" itemprop="headline">
                    <span class="category-name-highlight">助成金・補助金</span>
                    <span class="title-text">総合検索</span>
                    <span class="year-badge">
                        <time datetime="<?php echo $current_year; ?>"><?php echo $current_year; ?>年度版</time>
                    </span>
                </h1>

                <!-- リード文 -->
                <div class="category-lead-section" itemprop="description">
                    <p class="category-lead-text">
                        <?php echo esc_html($archive_description); ?>
                    </p>
                    <p class="category-lead-sub">
                        <?php echo $current_year; ?>年度の最新募集情報を毎日更新。
                        都道府県・市町村・カテゴリで詳細に絞り込んで、あなたに最適な助成金を見つけられます。
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="category-meta-info" role="group" aria-label="統計情報">
                    <div class="meta-item" itemscope itemtype="https://schema.org/QuantitativeValue">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>データベースアイコン</title>
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong itemprop="value"><?php echo $total_grants_formatted; ?></strong>
                        <span itemprop="unitText">件の助成金</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>時計アイコン</title>
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo $current_year; ?>" itemprop="dateModified">
                            <?php echo $current_year; ?>年度最新情報
                        </time>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>スターアイコン</title>
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>毎日更新中</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>位置情報アイコン</title>
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>市町村別対応</span>
                    </div>
                </div>

                <!-- 特徴カード -->
                <div class="feature-cards-grid" role="list">
                    <article class="feature-card" 
                             role="listitem"
                             itemscope 
                             itemtype="https://schema.org/Service">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" 
                                 height="24" 
                                 viewBox="0 0 24 24" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 stroke-width="2"
                                 role="img">
                                <title>リアルタイム更新</title>
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3 itemprop="name">リアルタイム更新</h3>
                            <p itemprop="description">最新の募集情報・締切情報を毎日チェック。見逃しを防ぎます。</p>
                        </div>
                    </article>

                    <article class="feature-card" 
                             role="listitem"
                             itemscope 
                             itemtype="https://schema.org/Service">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" 
                                 height="24" 
                                 viewBox="0 0 24 24" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 stroke-width="2"
                                 role="img">
                                <title>地域別検索</title>
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3 itemprop="name">地域別検索対応</h3>
                            <p itemprop="description">都道府県・市町村で絞り込み。地域密着型の助成金も見つかります。</p>
                        </div>
                    </article>

                    <article class="feature-card" 
                             role="listitem"
                             itemscope 
                             itemtype="https://schema.org/Service">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" 
                                 height="24" 
                                 viewBox="0 0 24 24" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 stroke-width="2"
                                 role="img">
                                <title>申請ガイド</title>
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3 itemprop="name">詳細な申請ガイド</h3>
                            <p itemprop="description">申請方法から採択のコツまで、専門家監修の情報を提供。</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </header>

    <?php
    // 広告: コンテンツ上部
    if (function_exists('ji_display_ad')): ?>
        <div class="archive-ad-space archive-ad-top" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('archive_grant_content_top', 'archive-grant'); ?>
        </div>
    <?php endif; ?>

    <!-- プルダウン式フィルターセクション -->
    <!-- モバイル用フローティングフィルターボタン -->
    <button class="mobile-filter-toggle" 
            id="mobile-filter-toggle"
            aria-label="フィルターを開く"
            aria-expanded="false"
            type="button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <span class="filter-count-badge" id="mobile-filter-count" style="display: none;">0</span>
    </button>

    <!-- フィルターパネル背景オーバーレイ -->
    <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

    <section class="dropdown-filter-section" id="filter-panel" 
             role="search" 
             aria-label="助成金検索フィルター"
             itemscope 
             itemtype="https://schema.org/WebPageElement">
        <div class="container">
            
            <!-- フィルターヘッダー -->
            <div class="filter-header">
                <h2 class="filter-title" itemprop="name">
                    <svg class="title-icon" 
                         width="20" 
                         height="20" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true"
                         role="img">
                        <title>フィルターアイコン</title>
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    絞り込み検索
                </h2>
                <button class="mobile-filter-close" 
                        id="mobile-filter-close"
                        aria-label="フィルターを閉じる"
                        type="button">×</button>
                <button class="filter-reset-all" 
                        id="reset-all-filters-btn" 
                        style="display: none;" 
                        aria-label="すべてのフィルターをリセット"
                        type="button">
                    <svg width="16" 
                         height="16" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true"
                         role="img">
                        <title>リセットアイコン</title>
                        <polyline points="1 4 1 10 7 10"/>
                        <polyline points="23 20 23 14 17 14"/>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                    </svg>
                    すべてリセット
                </button>
            </div>

            <!-- 検索バー -->
            <div class="search-bar-wrapper">
                <label for="keyword-search" class="visually-hidden">キーワード検索</label>
                <div class="search-input-container">
                    <svg class="search-icon" 
                         width="20" 
                         height="20" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true"
                         role="img">
                        <title>検索アイコン</title>
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="search" 
                           id="keyword-search" 
                           class="search-input" 
                           placeholder="助成金名、実施機関、対象事業、キーワードで検索..."
                           aria-label="助成金を検索"
                           autocomplete="off">
                    <button class="search-clear-btn" 
                            id="search-clear-btn" 
                            style="display: none;" 
                            aria-label="検索をクリア"
                            type="button">
                        <svg width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="currentColor" 
                             aria-hidden="true"
                             role="img">
                            <title>クリアアイコン</title>
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                    <button class="search-execute-btn" 
                            id="search-btn" 
                            aria-label="検索を実行"
                            type="button">
                        <svg width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>検索実行</title>
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        検索
                    </button>
                </div>
            </div>

            <!-- プルダウンフィルターグリッド -->
            <div class="dropdown-filters-grid">
                
                <!-- カテゴリ選択（複数選択対応） -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="category-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>カテゴリアイコン</title>
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                        カテゴリ
                        <span class="multi-select-badge" 
                              id="category-count-badge" 
                              style="display: none;" 
                              aria-live="polite">0</span>
                    </label>
                    <div class="custom-select multi-select" 
                         id="category-select" 
                         role="combobox" 
                         aria-labelledby="category-label" 
                         aria-expanded="false" 
                         aria-multiselectable="true">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">選択してください</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown multi-select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-search-wrapper">
                                <label for="category-search" class="visually-hidden">カテゴリを検索</label>
                                <input type="search" 
                                       class="select-search-input" 
                                       placeholder="カテゴリを検索..."
                                       id="category-search"
                                       aria-label="カテゴリを検索"
                                       autocomplete="off">
                            </div>
                            <div class="select-options-wrapper" id="category-options">
                                <div class="select-option all-option" 
                                     data-value="" 
                                     role="option">
                                    <input type="checkbox" 
                                           id="cat-all" 
                                           class="option-checkbox" 
                                           aria-label="すべてのカテゴリ">
                                    <label for="cat-all">すべて</label>
                                </div>
                                <?php foreach ($all_categories as $index => $category): ?>
                                    <div class="select-option" 
                                         data-value="<?php echo esc_attr($category->slug); ?>"
                                         data-name="<?php echo esc_attr($category->name); ?>"
                                         role="option">
                                        <input type="checkbox" 
                                               id="cat-<?php echo $index; ?>" 
                                               class="option-checkbox" 
                                               value="<?php echo esc_attr($category->slug); ?>"
                                               aria-label="<?php echo esc_attr($category->name); ?>">
                                        <label for="cat-<?php echo $index; ?>">
                                            <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>件)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="select-actions">
                                <button class="select-action-btn clear-btn" 
                                        id="clear-category-btn" 
                                        type="button">クリア</button>
                                <button class="select-action-btn apply-btn" 
                                        id="apply-category-btn" 
                                        type="button">適用</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 地域選択 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="region-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>地域アイコン</title>
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        地域
                    </label>
                    <div class="custom-select" 
                         id="region-select" 
                         role="combobox" 
                         aria-labelledby="region-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">全国</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-option active" 
                                 data-value="" 
                                 role="option" 
                                 aria-selected="true">全国</div>
                            <?php foreach ($region_groups as $region_slug => $region_name): ?>
                                <div class="select-option" 
                                     data-value="<?php echo esc_attr($region_slug); ?>" 
                                     role="option" 
                                     aria-selected="false">
                                    <?php echo esc_html($region_name); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 都道府県選択（複数選択対応） -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="prefecture-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>都道府県アイコン</title>
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                        都道府県
                        <span class="multi-select-badge" 
                              id="prefecture-count-badge" 
                              style="display: none;" 
                              aria-live="polite">0</span>
                    </label>
                    <div class="custom-select multi-select" 
                         id="prefecture-select" 
                         role="combobox" 
                         aria-labelledby="prefecture-label" 
                         aria-expanded="false" 
                         aria-multiselectable="true">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">選択してください</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown multi-select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-search-wrapper">
                                <label for="prefecture-search" class="visually-hidden">都道府県を検索</label>
                                <input type="search" 
                                       class="select-search-input" 
                                       placeholder="都道府県を検索..."
                                       id="prefecture-search"
                                       aria-label="都道府県を検索"
                                       autocomplete="off">
                            </div>
                            <div class="select-options-wrapper" id="prefecture-options">
                                <div class="select-option all-option" 
                                     data-value="" 
                                     role="option">
                                    <input type="checkbox" 
                                           id="pref-all" 
                                           class="option-checkbox" 
                                           aria-label="すべての都道府県">
                                    <label for="pref-all">すべて</label>
                                </div>
                                <?php foreach ($prefectures as $index => $pref): ?>
                                    <div class="select-option" 
                                         data-value="<?php echo esc_attr($pref['slug']); ?>"
                                         data-region="<?php echo esc_attr($pref['region']); ?>"
                                         data-name="<?php echo esc_attr($pref['name']); ?>"
                                         role="option">
                                        <input type="checkbox" 
                                               id="pref-<?php echo $index; ?>" 
                                               class="option-checkbox" 
                                               value="<?php echo esc_attr($pref['slug']); ?>"
                                               aria-label="<?php echo esc_attr($pref['name']); ?>">
                                        <label for="pref-<?php echo $index; ?>">
                                            <?php echo esc_html($pref['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="select-actions">
                                <button class="select-action-btn clear-btn" 
                                        id="clear-prefecture-btn" 
                                        type="button">クリア</button>
                                <button class="select-action-btn apply-btn" 
                                        id="apply-prefecture-btn" 
                                        type="button">適用</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 市町村選択（都道府県選択後に表示） -->
                <div class="filter-dropdown-wrapper" 
                     id="municipality-wrapper" 
                     style="display: none;">
                    <label class="filter-label" id="municipality-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>市町村アイコン</title>
                            <path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/>
                            <path d="M9 22V12h6v10M2 10.6L12 2l10 8.6"/>
                        </svg>
                        市町村
                        <span class="selected-prefecture-name" 
                              id="selected-prefecture-name"></span>
                    </label>
                    <div class="custom-select" 
                         id="municipality-select" 
                         role="combobox" 
                         aria-labelledby="municipality-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-search-wrapper">
                                <label for="municipality-search" class="visually-hidden">市町村を検索</label>
                                <input type="search" 
                                       class="select-search-input" 
                                       placeholder="市町村を検索..."
                                       id="municipality-search"
                                       aria-label="市町村を検索"
                                       autocomplete="off">
                            </div>
                            <div class="select-options-wrapper" id="municipality-options">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option" 
                                     aria-selected="true">すべて</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 助成金額 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="amount-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>金額アイコン</title>
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        助成金額
                    </label>
                    <div class="custom-select" 
                         id="amount-select" 
                         role="combobox" 
                         aria-labelledby="amount-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-option active" 
                                 data-value="" 
                                 role="option" 
                                 aria-selected="true">指定なし</div>
                            <div class="select-option" 
                                 data-value="0-100" 
                                 role="option" 
                                 aria-selected="false">〜100万円</div>
                            <div class="select-option" 
                                 data-value="100-500" 
                                 role="option" 
                                 aria-selected="false">100万円〜500万円</div>
                            <div class="select-option" 
                                 data-value="500-1000" 
                                 role="option" 
                                 aria-selected="false">500万円〜1000万円</div>
                            <div class="select-option" 
                                 data-value="1000-3000" 
                                 role="option" 
                                 aria-selected="false">1000万円〜3000万円</div>
                            <div class="select-option" 
                                 data-value="3000+" 
                                 role="option" 
                                 aria-selected="false">3000万円以上</div>
                        </div>
                    </div>
                </div>

                <!-- 募集状況 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="status-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>カレンダーアイコン</title>
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        募集状況
                    </label>
                    <div class="custom-select" 
                         id="status-select" 
                         role="combobox" 
                         aria-labelledby="status-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-option active" 
                                 data-value="" 
                                 role="option" 
                                 aria-selected="true">すべて</div>
                            <div class="select-option" 
                                 data-value="active" 
                                 role="option" 
                                 aria-selected="false">
                                <span class="status-dot status-active" aria-hidden="true"></span>
                                募集中
                            </div>
                            <div class="select-option" 
                                 data-value="upcoming" 
                                 role="option" 
                                 aria-selected="false">
                                <span class="status-dot status-upcoming" aria-hidden="true"></span>
                                募集予定
                            </div>
                            <div class="select-option" 
                                 data-value="closed" 
                                 role="option" 
                                 aria-selected="false">
                                <span class="status-dot status-closed" aria-hidden="true"></span>
                                募集終了
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 難易度 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="difficulty-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>難易度アイコン</title>
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        申請難易度
                    </label>
                    <div class="custom-select" 
                         id="difficulty-select" 
                         role="combobox" 
                         aria-labelledby="difficulty-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-option active" 
                                 data-value="" 
                                 role="option" 
                                 aria-selected="true">指定なし</div>
                            <div class="select-option" 
                                 data-value="easy" 
                                 role="option" 
                                 aria-selected="false">易しい</div>
                            <div class="select-option" 
                                 data-value="normal" 
                                 role="option" 
                                 aria-selected="false">普通</div>
                            <div class="select-option" 
                                 data-value="hard" 
                                 role="option" 
                                 aria-selected="false">難しい</div>
                        </div>
                    </div>
                </div>

                <!-- 並び順 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="sort-label">
                        <svg class="label-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true"
                             role="img">
                            <title>並び順アイコン</title>
                            <line x1="4" y1="6" x2="16" y2="6"/>
                            <line x1="4" y1="12" x2="14" y2="12"/>
                            <line x1="4" y1="18" x2="12" y2="18"/>
                        </svg>
                        並び順
                    </label>
                    <div class="custom-select" 
                         id="sort-select" 
                         role="combobox" 
                         aria-labelledby="sort-label" 
                         aria-expanded="false">
                        <button class="select-trigger" 
                                type="button" 
                                aria-haspopup="listbox">
                            <span class="select-value">新着順</span>
                            <svg class="select-arrow" 
                                 width="16" 
                                 height="16" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true"
                                 role="img">
                                <title>ドロップダウン</title>
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" 
                             role="listbox" 
                             style="display: none;">
                            <div class="select-option active" 
                                 data-value="date_desc" 
                                 role="option" 
                                 aria-selected="true">新着順</div>
                            <div class="select-option" 
                                 data-value="amount_desc" 
                                 role="option" 
                                 aria-selected="false">金額が高い順</div>
                            <div class="select-option" 
                                 data-value="deadline_asc" 
                                 role="option" 
                                 aria-selected="false">締切が近い順</div>
                            <div class="select-option" 
                                 data-value="featured_first" 
                                 role="option" 
                                 aria-selected="false">注目順</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 選択中のフィルター表示 -->
            <div class="active-filters-display" 
                 id="active-filters" 
                 style="display: none;" 
                 role="status" 
                 aria-live="polite">
                <div class="active-filters-label">
                    <svg width="16" 
                         height="16" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true"
                         role="img">
                        <title>フィルターアイコン</title>
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    適用中のフィルター:
                </div>
                <div class="active-filter-tags" id="active-filter-tags"></div>
            </div>
        </div>
    </section>

    <!-- 検索結果セクション -->
    <section class="results-section-optimized" 
             itemscope 
             itemtype="https://schema.org/ItemList">
        <div class="container">
            
            <!-- 結果ヘッダー -->
            <div class="results-header">
                <div class="results-info">
                    <h2 class="results-title" itemprop="name">検索結果</h2>
                    <div class="results-meta" 
                         role="status" 
                         aria-live="polite"
                         itemprop="description">
                        <span class="total-count">
                            <strong id="current-count" itemprop="numberOfItems">0</strong>件
                        </span>
                        <span class="showing-range">
                            （<span id="showing-from">1</span>〜<span id="showing-to">12</span>件を表示）
                        </span>
                    </div>
                </div>

                <div class="view-controls" 
                     role="group" 
                     aria-label="表示形式切り替え">
                    <button class="view-btn active" 
                            data-view="single" 
                            title="単体表示" 
                            aria-pressed="true"
                            type="button">
                        <svg width="20" 
                             height="20" 
                             viewBox="0 0 24 24" 
                             fill="currentColor" 
                             aria-hidden="true"
                             role="img">
                            <title>単体表示</title>
                            <rect x="2" y="2" width="20" height="20"/>
                        </svg>
                        <span class="visually-hidden">単体表示</span>
                    </button>
                    <button class="view-btn" 
                            data-view="grid" 
                            title="カード表示" 
                            aria-pressed="false"
                            type="button">
                        <svg width="20" 
                             height="20" 
                             viewBox="0 0 24 24" 
                             fill="currentColor" 
                             aria-hidden="true"
                             role="img">
                            <title>カード表示</title>
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                        </svg>
                        <span class="visually-hidden">カード表示</span>
                    </button>
                </div>
            </div>

            <!-- ローディング -->
            <div class="loading-overlay" 
                 id="loading-overlay" 
                 style="display: none;" 
                 role="status" 
                 aria-live="polite">
                <div class="loading-spinner">
                    <div class="spinner" aria-hidden="true"></div>
                    <p class="loading-text">検索中...</p>
                </div>
            </div>

            <!-- 助成金表示エリア -->
            <div class="grants-container-optimized" 
                 id="grants-container" 
                 data-view="single">
                <?php
                $initial_grants_query = new WP_Query([
                    'post_type' => 'grant',
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
                if ($initial_grants_query->have_posts()) :
                    while ($initial_grants_query->have_posts()) : 
                        $initial_grants_query->the_post();
                        get_template_part('template-parts/grant-card-unified');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <!-- 結果なし -->
            <div class="no-results" 
                 id="no-results" 
                 style="display: none;" 
                 role="status" 
                 aria-live="polite">
                <svg class="no-results-icon" 
                     width="64" 
                     height="64" 
                     viewBox="0 0 24 24" 
                     fill="none" 
                     stroke="currentColor" 
                     stroke-width="2" 
                     aria-hidden="true"
                     role="img">
                    <title>結果なし</title>
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                <p class="no-results-message">
                    検索条件を変更して再度お試しください。
                </p>
            </div>

            <!-- ページネーション（SEO対応：クロール可能なリンク形式） -->
            <div class="pagination-wrapper" 
                 id="pagination-wrapper">
                <?php
                // WordPress標準のページネーション（クロール可能な<a>タグを生成）
                $big = 999999999; // 大きな数値が必要
                
                echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var('paged') ),
                    'total' => $initial_grants_query->max_num_pages,
                    'type' => 'plain',
                    'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg> 前へ',
                    'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>',
                    'mid_size' => 2,
                    'end_size' => 1,
                    'aria_current' => 'page',
                    'before_page_number' => '<span class="screen-reader-text">ページ </span>',
                ) );
                ?>
            </div>
        </div>
    </section>

    <?php
    // 広告: コンテンツ下部
    if (function_exists('ji_display_ad')): ?>
        <div class="archive-ad-space archive-ad-bottom" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
            <?php ji_display_ad('archive_grant_content_bottom', 'archive-grant'); ?>
        </div>
    <?php endif; ?>

</main>

<!-- アクセシビリティ用スタイル -->
<style>
/* ===================================
   Archive Grant - Mobile Filter Improvements
   モバイルフィルター改善CSS
   =================================== */

/* モバイルフローティングボタン */
.mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: 24px;
    left: 24px;
    width: 64px;
    height: 64px;
    background: var(--color-primary, #000000);
    color: var(--color-secondary, #ffffff);
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}

.mobile-filter-toggle:active {
    transform: scale(0.95);
}

.mobile-filter-toggle .filter-count-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: #ef4444;
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-filter-close {
    display: none;
    background: none;
    border: none;
    font-size: 28px;
    line-height: 1;
    color: var(--color-gray-600, #525252);
    cursor: pointer;
    padding: 8px;
    margin-left: auto;
}

/* フィルターパネル背景オーバーレイ */
.filter-panel-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 997;
    transition: opacity 0.3s ease;
    opacity: 0;
}

.filter-panel-overlay.active {
    display: block !important;
    opacity: 1;
}

@media (max-width: 768px) {
    .mobile-filter-toggle {
        display: flex !important;
    }
    
    .mobile-filter-close {
        display: block !important;
    }
    
    .dropdown-filter-section {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--color-secondary, #ffffff);
        z-index: 998;
        padding: 60px 20px 20px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        transform: translateX(100%);
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        max-height: 100vh;
    }
    
    .dropdown-filter-section.active {
        transform: translateX(0) !important;
    }
    
    .filter-header {
        position: sticky;
        top: 0;
        background: var(--color-secondary, #ffffff);
        z-index: 10;
        padding: 16px 0 !important;
        margin-bottom: 20px !important;
        border-bottom: 1px solid var(--color-gray-200, #e5e5e5);
    }
    
    .filter-header::before {
        content: '';
        position: absolute;
        left: -20px;
        right: -20px;
        top: 0;
        bottom: 0;
        background: var(--color-secondary, #ffffff);
        z-index: -1;
    }
}

/* スペース最適化 */
@media (max-width: 768px) {
    .container {
        padding: 0 12px !important;
    }
    
    .category-hero-section {
        padding: 24px 0 20px !important;
    }
    
    .category-main-title {
        font-size: 28px !important;
        margin: 0 0 12px 0 !important;
        gap: 8px !important;
    }
    
    .year-badge {
        font-size: 13px !important;
        padding: 4px 10px !important;
    }
    
    .category-lead-section {
        margin: 16px 0 !important;
    }
    
    .category-lead-text {
        font-size: 15px !important;
    }
    
    .category-lead-sub {
        font-size: 14px !important;
    }
    
    .category-meta-info {
        gap: 16px !important;
        margin: 16px 0 20px 0 !important;
    }
    
    .feature-cards-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
        margin-top: 20px !important;
    }
    
    .feature-card {
        padding: 14px !important;
        gap: 12px !important;
    }
    
    .dropdown-filters-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
    
    .search-bar-wrapper {
        margin-bottom: 16px !important;
    }
    
    .results-section-optimized {
        padding: 24px 0 !important;
    }
    
    .results-header {
        margin-bottom: 20px !important;
        gap: 12px !important;
    }
    
    .grants-container-optimized {
        gap: 12px !important;
        margin-bottom: 30px !important;
        min-height: 300px !important;
    }
    
    .grants-container-optimized[data-view="grid"] {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
    
    .pagination-wrapper {
        margin-top: 30px !important;
        padding: 12px 0 !important;
    }
    
    .pagination-wrapper .page-numbers {
        min-width: 36px !important;
        height: 36px !important;
        font-size: 13px !important;
        margin: 0 2px !important;
        padding: 0 8px !important;
    }
}

/* デスクトップでもスペース最適化 */
.category-hero-section {
    padding: 40px 0 30px !important;
}

.container {
    max-width: 1200px !important;
}

.feature-card {
    padding: 18px !important;
    gap: 14px !important;
}

.dropdown-filter-section {
    padding: 30px 0 !important;
}

.results-section-optimized {
    padding: 40px 0 !important;
}

.category-main-title {
    font-size: 42px !important;
    margin: 0 0 16px 0 !important;
    gap: 10px !important;
}

.category-lead-section {
    margin: 20px 0 !important;
}

.category-meta-info {
    gap: 24px !important;
    margin: 20px 0 30px 0 !important;
}

.feature-cards-grid {
    gap: 16px !important;
    margin-top: 30px !important;
}

.filter-header {
    margin-bottom: 24px !important;
    gap: 12px !important;
}

.filter-title {
    font-size: 22px !important;
}

.search-bar-wrapper {
    margin-bottom: 20px !important;
}

.dropdown-filters-grid {
    gap: 16px !important;
    margin-bottom: 20px !important;
}

.results-header {
    margin-bottom: 30px !important;
    gap: 16px !important;
}

.grants-container-optimized {
    gap: 16px !important;
    margin-bottom: 40px !important;
}

.grants-container-optimized[data-view="grid"] {
    gap: 20px !important;
}

.pagination-wrapper {
    margin-top: 40px !important;
    padding: 16px 0 !important;
}
.visually-hidden {
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

/* フォーカス表示 */
*:focus {
    outline: 3px solid rgba(255, 235, 59, 0.5);
    outline-offset: 2px;
}

/* キーボードナビゲーション */
.keyboard-nav *:focus {
    outline: 3px solid #ffeb3b;
    outline-offset: 3px;
}
</style>

<!-- プルダウン式フィルター専用CSS -->
<style>
/* ===================================
   Archive Grant - Mobile Filter Improvements
   モバイルフィルター改善CSS
   =================================== */

/* モバイルフローティングボタン */
.mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: 24px;
    left: 24px;
    width: 64px;
    height: 64px;
    background: var(--color-primary, #000000);
    color: var(--color-secondary, #ffffff);
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}

.mobile-filter-toggle:active {
    transform: scale(0.95);
}

.mobile-filter-toggle .filter-count-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: #ef4444;
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-filter-close {
    display: none;
    background: none;
    border: none;
    font-size: 28px;
    line-height: 1;
    color: var(--color-gray-600, #525252);
    cursor: pointer;
    padding: 8px;
    margin-left: auto;
}

/* フィルターパネル背景オーバーレイ */
.filter-panel-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 997;
    transition: opacity 0.3s ease;
    opacity: 0;
}

.filter-panel-overlay.active {
    display: block !important;
    opacity: 1;
}

@media (max-width: 768px) {
    .mobile-filter-toggle {
        display: flex !important;
    }
    
    .mobile-filter-close {
        display: block !important;
    }
    
    .dropdown-filter-section {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--color-secondary, #ffffff);
        z-index: 998;
        padding: 60px 20px 20px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        transform: translateX(100%);
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        max-height: 100vh;
    }
    
    .dropdown-filter-section.active {
        transform: translateX(0) !important;
    }
    
    .filter-header {
        position: sticky;
        top: 0;
        background: var(--color-secondary, #ffffff);
        z-index: 10;
        padding: 16px 0 !important;
        margin-bottom: 20px !important;
        border-bottom: 1px solid var(--color-gray-200, #e5e5e5);
    }
    
    .filter-header::before {
        content: '';
        position: absolute;
        left: -20px;
        right: -20px;
        top: 0;
        bottom: 0;
        background: var(--color-secondary, #ffffff);
        z-index: -1;
    }
}

/* スペース最適化 */
@media (max-width: 768px) {
    .container {
        padding: 0 12px !important;
    }
    
    .category-hero-section {
        padding: 24px 0 20px !important;
    }
    
    .category-main-title {
        font-size: 28px !important;
        margin: 0 0 12px 0 !important;
        gap: 8px !important;
    }
    
    .year-badge {
        font-size: 13px !important;
        padding: 4px 10px !important;
    }
    
    .category-lead-section {
        margin: 16px 0 !important;
    }
    
    .category-lead-text {
        font-size: 15px !important;
    }
    
    .category-lead-sub {
        font-size: 14px !important;
    }
    
    .category-meta-info {
        gap: 16px !important;
        margin: 16px 0 20px 0 !important;
    }
    
    .feature-cards-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
        margin-top: 20px !important;
    }
    
    .feature-card {
        padding: 14px !important;
        gap: 12px !important;
    }
    
    .dropdown-filters-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
    
    .search-bar-wrapper {
        margin-bottom: 16px !important;
    }
    
    .results-section-optimized {
        padding: 24px 0 !important;
    }
    
    .results-header {
        margin-bottom: 20px !important;
        gap: 12px !important;
    }
    
    .grants-container-optimized {
        gap: 12px !important;
        margin-bottom: 30px !important;
        min-height: 300px !important;
    }
    
    .grants-container-optimized[data-view="grid"] {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
    
    .pagination-wrapper {
        margin-top: 30px !important;
        padding: 12px 0 !important;
    }
    
    .pagination-wrapper .page-numbers {
        min-width: 36px !important;
        height: 36px !important;
        font-size: 13px !important;
        margin: 0 2px !important;
        padding: 0 8px !important;
    }
}

/* デスクトップでもスペース最適化 */
.category-hero-section {
    padding: 40px 0 30px !important;
}

.container {
    max-width: 1200px !important;
}

.feature-card {
    padding: 18px !important;
    gap: 14px !important;
}

.dropdown-filter-section {
    padding: 30px 0 !important;
}

.results-section-optimized {
    padding: 40px 0 !important;
}

.category-main-title {
    font-size: 42px !important;
    margin: 0 0 16px 0 !important;
    gap: 10px !important;
}

.category-lead-section {
    margin: 20px 0 !important;
}

.category-meta-info {
    gap: 24px !important;
    margin: 20px 0 30px 0 !important;
}

.feature-cards-grid {
    gap: 16px !important;
    margin-top: 30px !important;
}

.filter-header {
    margin-bottom: 24px !important;
    gap: 12px !important;
}

.filter-title {
    font-size: 22px !important;
}

.search-bar-wrapper {
    margin-bottom: 20px !important;
}

.dropdown-filters-grid {
    gap: 16px !important;
    margin-bottom: 20px !important;
}

.results-header {
    margin-bottom: 30px !important;
    gap: 16px !important;
}

.grants-container-optimized {
    gap: 16px !important;
    margin-bottom: 40px !important;
}

.grants-container-optimized[data-view="grid"] {
    gap: 20px !important;
}

.pagination-wrapper {
    margin-top: 40px !important;
    padding: 16px 0 !important;
}
/* ===== CSS Variables ===== */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-400: #a3a3a3;
    --color-gray-500: #737373;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-800: #262626;
    --color-gray-900: #171717;
    --color-success: #22c55e;
    --color-warning: #f59e0b;
    --color-error: #ef4444;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --transition-fast: 0.15s ease;
    --transition-normal: 0.3s ease;
    --border-radius: 8px;
    --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
}

/* ===== Base Styles ===== */
.grant-archive-optimized {
    font-family: var(--font-family);
    color: var(--color-primary);
    background: var(--color-secondary);
    line-height: 1.6;
}

.container {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ===== Breadcrumb ===== */
.breadcrumb-nav {
    padding: 16px 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 14px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 8px;
    color: var(--color-gray-400);
    font-size: 16px;
}

.breadcrumb-item a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.breadcrumb-item a:hover {
    color: var(--color-primary);
    text-decoration: underline;
}

.breadcrumb-item span {
    color: var(--color-primary);
    font-weight: 600;
}

/* ===== Hero Section ===== */
.category-hero-section {
    padding: 60px 0;
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-secondary) 100%);
    border-bottom: 2px solid var(--color-gray-200);
}

.hero-content-wrapper {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}

.badge-icon {
    color: var(--color-secondary);
}

.category-main-title {
    font-size: 48px;
    font-weight: 800;
    color: var(--color-primary);
    margin: 0 0 20px 0;
    line-height: 1.2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.category-name-highlight {
    background: linear-gradient(180deg, transparent 60%, rgba(255, 235, 59, 0.4) 60%);
    padding: 0 8px;
}

.title-text {
    color: var(--color-gray-700);
}

.year-badge {
    display: inline-block;
    padding: 6px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 20px;
    font-size: 16px;
    font-weight: 700;
}

.category-lead-section {
    margin: 30px 0;
}

.category-lead-text {
    font-size: 18px;
    color: var(--color-gray-700);
    margin: 0 0 16px 0;
    line-height: 1.7;
}

.category-lead-sub {
    font-size: 16px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.8;
}

.category-meta-info {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    margin: 30px 0 40px 0;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: var(--color-gray-700);
}

.meta-icon {
    color: var(--color-gray-500);
}

.meta-item strong {
    color: var(--color-primary);
    font-weight: 700;
}

.feature-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 40px;
}

.feature-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-200);
    border-radius: var(--border-radius);
    transition: all var(--transition-normal);
    text-align: left;
}

.feature-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.feature-card-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: var(--color-primary);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-secondary);
}

.feature-card-content {
    flex: 1;
}

.feature-card-content h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.feature-card-content p {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.5;
}

/* ===== Dropdown Filter Section ===== */
.dropdown-filter-section {
    padding: 50px 0;
    background: var(--color-secondary);
    border-bottom: 1px solid var(--color-gray-200);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 16px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0;
}

.title-icon {
    color: var(--color-gray-600);
}

.filter-reset-all {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    color: var(--color-gray-700);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-reset-all:hover {
    background: var(--color-gray-200);
    border-color: var(--color-gray-400);
}

/* ===== Search Bar ===== */
.search-bar-wrapper {
    margin-bottom: 30px;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    max-width: 100%;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: border-color var(--transition-fast);
}

.search-input-container:focus-within {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.search-icon {
    position: absolute;
    left: 16px;
    color: var(--color-gray-400);
    pointer-events: none;
}

.search-input {
    flex: 1;
    padding: 14px 16px 14px 48px;
    border: none;
    outline: none;
    font-size: 15px;
    background: transparent;
}

.search-clear-btn {
    background: none;
    border: none;
    color: var(--color-gray-400);
    padding: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color var(--transition-fast);
}

.search-clear-btn:hover {
    color: var(--color-gray-700);
}

.search-execute-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 24px;
    background: var(--color-primary);
    border: none;
    color: var(--color-secondary);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.search-execute-btn:hover {
    background: var(--color-gray-800);
}

/* ===== Dropdown Filters Grid ===== */
.dropdown-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.filter-dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-700);
}

.label-icon {
    color: var(--color-gray-500);
}

.multi-select-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
}

.selected-prefecture-name {
    font-size: 12px;
    color: var(--color-gray-500);
    font-weight: 400;
}

/* ===== Custom Select ===== */
.custom-select {
    position: relative;
    width: 100%;
}

.select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    color: var(--color-gray-700);
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-fast);
    text-align: left;
}

.select-trigger:hover {
    border-color: var(--color-gray-400);
}

.custom-select.active .select-trigger {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.select-value {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.select-arrow {
    flex-shrink: 0;
    color: var(--color-gray-500);
    transition: transform var(--transition-fast);
}

.custom-select.active .select-arrow {
    transform: rotate(180deg);
}

.select-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
}

.select-option {
    padding: 12px 16px;
    cursor: pointer;
    transition: background var(--transition-fast);
    font-size: 14px;
    color: var(--color-gray-700);
    display: flex;
    align-items: center;
    gap: 8px;
}

.select-option:hover {
    background: var(--color-gray-100);
}

.select-option.active {
    background: var(--color-gray-100);
    color: var(--color-primary);
    font-weight: 600;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot.status-active {
    background: var(--color-success);
}

.status-dot.status-upcoming {
    background: var(--color-warning);
}

.status-dot.status-closed {
    background: var(--color-gray-400);
}

/* ===== Multi Select ===== */
.multi-select-dropdown {
    max-height: 400px;
}

.select-search-wrapper {
    padding: 12px;
    border-bottom: 1px solid var(--color-gray-200);
    position: sticky;
    top: 0;
    background: var(--color-secondary);
    z-index: 10;
}

.select-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
    outline: none;
}

.select-search-input:focus {
    border-color: var(--color-primary);
}

.select-options-wrapper {
    max-height: 250px;
    overflow-y: auto;
}

.select-option .option-checkbox {
    margin-right: 8px;
    cursor: pointer;
}

.select-option label {
    flex: 1;
    cursor: pointer;
}

.select-actions {
    display: flex;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--color-gray-200);
    position: sticky;
    bottom: 0;
    background: var(--color-secondary);
}

.select-action-btn {
    flex: 1;
    padding: 8px 16px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.select-action-btn.clear-btn {
    background: var(--color-secondary);
    color: var(--color-gray-700);
}

.select-action-btn.clear-btn:hover {
    background: var(--color-gray-100);
}

.select-action-btn.apply-btn {
    background: var(--color-primary);
    color: var(--color-secondary);
    border-color: var(--color-primary);
}

.select-action-btn.apply-btn:hover {
    background: var(--color-gray-800);
}

/* ===== Active Filters ===== */
.active-filters-display {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--border-radius);
    flex-wrap: wrap;
}

.active-filters-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-700);
    white-space: nowrap;
}

.active-filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 1;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 16px;
    font-size: 13px;
    font-weight: 500;
}

.filter-tag-remove {
    background: none;
    border: none;
    color: var(--color-secondary);
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    line-height: 1;
    opacity: 0.8;
    transition: opacity var(--transition-fast);
}

.filter-tag-remove:hover {
    opacity: 1;
}

/* ===== Results Section ===== */
.results-section-optimized {
    padding: 60px 0;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.results-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.results-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.total-count strong {
    font-size: 20px;
    color: var(--color-primary);
}

.view-controls {
    display: flex;
    gap: 4px;
    background: var(--color-gray-100);
    border-radius: var(--border-radius);
    padding: 4px;
}

.view-btn {
    background: transparent;
    border: none;
    padding: 10px 12px;
    border-radius: 6px;
    cursor: pointer;
    color: var(--color-gray-600);
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
}

.view-btn:hover {
    color: var(--color-primary);
}

.view-btn.active {
    background: var(--color-primary);
    color: var(--color-secondary);
}

/* ===== Loading ===== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
}

/* ===== Grants Container (Single View Base) ===== */
.grants-container-optimized {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 60px;
    min-height: 400px;
}

.grants-container-optimized[data-view="single"] {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grants-container-optimized[data-view="single"] .grant-card-unified {
    max-width: 100%;
    width: 100%;
}

.grants-container-optimized[data-view="grid"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.grants-container-optimized[data-view="grid"] .grant-card-unified {
    max-width: 100%;
}

/* ===== No Results ===== */
.no-results {
    text-align: center;
    padding: 80px 20px;
    color: var(--color-gray-600);
}

.no-results-icon {
    color: var(--color-gray-300);
    margin-bottom: 20px;
}

.no-results-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--color-primary);
    margin: 0 0 12px 0;
}

.no-results-message {
    font-size: 16px;
    margin: 0;
}

/* ===== Pagination（SEO対応：クロール可能なリンク形式） ===== */
.pagination-wrapper {
    margin-top: 60px;
    display: flex;
    justify-content: center;
    padding: 20px 0;
}

.pagination-wrapper .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 12px;
    margin: 0 4px;
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    background: var(--color-secondary);
    color: var(--color-gray-700);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-fast);
}

.pagination-wrapper .page-numbers:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
    background: var(--color-gray-50);
}

.pagination-wrapper .page-numbers.current {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: var(--color-secondary);
    cursor: default;
}

.pagination-wrapper .page-numbers.dots {
    border: none;
    background: transparent;
    cursor: default;
}

.pagination-wrapper .prev,
.pagination-wrapper .next {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.pagination-wrapper .prev svg,
.pagination-wrapper .next svg {
    width: 16px;
    height: 16px;
}

/* レスポンシブ調整 */
@media (max-width: 768px) {
    .pagination-wrapper .page-numbers {
        min-width: 40px;
        height: 40px;
        font-size: 13px;
    }
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .category-main-title {
        font-size: 32px;
    }

    .year-badge {
        font-size: 14px;
        padding: 5px 12px;
    }

    .category-lead-text {
        font-size: 16px;
    }

    .category-lead-sub {
        font-size: 15px;
    }

    .category-meta-info {
        flex-direction: column;
        gap: 16px;
    }

    .feature-cards-grid {
        grid-template-columns: 1fr;
    }

    .dropdown-filters-grid {
        grid-template-columns: 1fr;
    }

    .results-header {
        flex-direction: column;
        align-items: stretch;
    }

    .grants-container-optimized[data-view="grid"] {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 16px;
    }

    .category-main-title {
        font-size: 28px;
    }

    .search-input-container {
        flex-direction: column;
    }

    .search-execute-btn {
        width: 100%;
        justify-content: center;
    }

    .filter-header {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
}

/* ===== Print Styles ===== */
@media print {
    .filter-header,
    .search-bar-wrapper,
    .dropdown-filters-grid,
    .active-filters-display,
    .view-controls,
    .pagination-wrapper,
    .loading-overlay {
        display: none !important;
    }
    
    .grants-container-optimized {
        display: block !important;
    }
    
    .grant-card-unified {
        page-break-inside: avoid;
    }
}
</style>

<!-- JavaScript（プルダウン式・完全統合版） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    
    const state = {
        currentPage: 1,
        perPage: 12,
        view: 'single',
        filters: {
            search: '',
            category: [],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc',
            tag: ''
        },
        isLoading: false,
        tempCategories: [],
        tempPrefectures: [],
        currentMunicipalities: []
    };
    
    // 要素はinit()内で初期化（DOMロード後に取得）
    const elements = {};
    
    function init() {
        console.log('🚀 Archive SEO Perfect v18.0 Initialized');
        console.log('📊 Configuration:', {
            ajaxUrl: AJAX_URL,
            noncePresent: !!NONCE,
            defaultView: state.view
        });
        
        // DOMが完全にロードされてから要素を取得
        initializeElements();
        initializeFromUrlParams();
        setupCustomSelects();
        setupEventListeners();
        setupAccessibility();
        loadGrants();
    }
    
    function initializeElements() {
        // すべての要素をDOMロード後に取得
        elements.grantsContainer = document.getElementById('grants-container');
        elements.loadingOverlay = document.getElementById('loading-overlay');
        elements.noResults = document.getElementById('no-results');
        elements.resultsCount = document.getElementById('current-count');
        elements.showingFrom = document.getElementById('showing-from');
        elements.showingTo = document.getElementById('showing-to');
        elements.pagination = document.getElementById('pagination');
        elements.paginationWrapper = document.getElementById('pagination-wrapper');
        elements.activeFilters = document.getElementById('active-filters');
        elements.activeFilterTags = document.getElementById('active-filter-tags');
        
        elements.keywordSearch = document.getElementById('keyword-search');
        elements.searchBtn = document.getElementById('search-btn');
        elements.searchClearBtn = document.getElementById('search-clear-btn');
        
        elements.categorySelect = document.getElementById('category-select');
        elements.categorySearch = document.getElementById('category-search');
        elements.categoryOptions = document.getElementById('category-options');
        elements.clearCategoryBtn = document.getElementById('clear-category-btn');
        elements.applyCategoryBtn = document.getElementById('apply-category-btn');
        elements.categoryCountBadge = document.getElementById('category-count-badge');
        
        elements.regionSelect = document.getElementById('region-select');
        
        elements.prefectureSelect = document.getElementById('prefecture-select');
        elements.prefectureSearch = document.getElementById('prefecture-search');
        elements.prefectureOptions = document.getElementById('prefecture-options');
        elements.clearPrefectureBtn = document.getElementById('clear-prefecture-btn');
        elements.applyPrefectureBtn = document.getElementById('apply-prefecture-btn');
        elements.prefectureCountBadge = document.getElementById('prefecture-count-badge');
        
        elements.municipalitySelect = document.getElementById('municipality-select');
        elements.municipalityWrapper = document.getElementById('municipality-wrapper');
        elements.municipalitySearch = document.getElementById('municipality-search');
        elements.municipalityOptions = document.getElementById('municipality-options');
        elements.selectedPrefectureName = document.getElementById('selected-prefecture-name');
        
        elements.amountSelect = document.getElementById('amount-select');
        elements.statusSelect = document.getElementById('status-select');
        elements.difficultySelect = document.getElementById('difficulty-select');
        elements.sortSelect = document.getElementById('sort-select');
        
        elements.viewBtns = document.querySelectorAll('.view-btn');
        elements.resetAllFiltersBtn = document.getElementById('reset-all-filters-btn');
        
        // モバイルフィルター要素
        elements.mobileFilterToggle = document.getElementById('mobile-filter-toggle');
        elements.mobileFilterClose = document.getElementById('mobile-filter-close');
        elements.filterPanel = document.getElementById('filter-panel');
        elements.mobileFilterCount = document.getElementById('mobile-filter-count');
        elements.filterPanelOverlay = document.getElementById('filter-panel-overlay');
        
        // 要素が正しく取得できたかログ出力
        console.log('📱 Mobile filter elements:', {
            toggle: !!elements.mobileFilterToggle,
            close: !!elements.mobileFilterClose,
            panel: !!elements.filterPanel,
            overlay: !!elements.filterPanelOverlay
        });
    }
    
    function initializeFromUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        
        const searchParam = urlParams.get('search');
        if (searchParam) {
            state.filters.search = searchParam;
            if (elements.keywordSearch) {
                elements.keywordSearch.value = searchParam;
                elements.searchClearBtn.style.display = 'flex';
            }
            console.log('🔍 Search keyword from URL:', searchParam);
        }
        
        const categoryParam = urlParams.get('category');
        if (categoryParam) {
            state.filters.category = [categoryParam];
            console.log('📁 Category from URL:', categoryParam);
        }
        
        const prefectureParam = urlParams.get('prefecture');
        if (prefectureParam) {
            state.filters.prefecture = [prefectureParam];
            console.log('📍 Prefecture from URL:', prefectureParam);
        }
        
        const municipalityParam = urlParams.get('municipality');
        if (municipalityParam) {
            state.filters.municipality = municipalityParam;
            console.log('🏘️ Municipality from URL:', municipalityParam);
        }
        
        const tagParam = urlParams.get('grant_tag');
        if (tagParam) {
            state.filters.tag = tagParam;
            console.log('🏷️ Tag from URL:', tagParam);
        }
    }
    
    function setupAccessibility() {
        // キーボードナビゲーション検出
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-nav');
            }
        });
        
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-nav');
        });
        
        console.log('[Accessibility] Features enabled');
    }
    
    function setupCustomSelects() {
        setupMultiSelectCategory();
        setupSingleSelect(elements.regionSelect, (value) => {
            state.filters.region = value;
            filterPrefecturesByRegion(value);
            state.currentPage = 1;
            loadGrants();
        });
        setupMultiSelectPrefecture();
        setupMunicipalitySelect();
        setupSingleSelect(elements.amountSelect, (value) => {
            state.filters.amount = value;
            state.currentPage = 1;
            loadGrants();
        });
        setupSingleSelect(elements.statusSelect, (value) => {
            state.filters.status = value;
            state.currentPage = 1;
            loadGrants();
        });
        setupSingleSelect(elements.difficultySelect, (value) => {
            state.filters.difficulty = value;
            state.currentPage = 1;
            loadGrants();
        });
        setupSingleSelect(elements.sortSelect, (value) => {
            state.filters.sort = value;
            state.currentPage = 1;
            loadGrants();
        });
    }
    
    function setupSingleSelect(selectElement, onChange) {
        if (!selectElement) return;
        
        const trigger = selectElement.querySelector('.select-trigger');
        const dropdown = selectElement.querySelector('.select-dropdown');
        const options = selectElement.querySelectorAll('.select-option');
        const valueSpan = selectElement.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = selectElement.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                selectElement.classList.add('active');
                selectElement.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const text = option.textContent.trim();
                
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                option.classList.add('active');
                option.setAttribute('aria-selected', 'true');
                
                valueSpan.textContent = text;
                
                selectElement.classList.remove('active');
                selectElement.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                onChange(value);
            });
        });
    }
    
    function setupMultiSelectCategory() {
        if (!elements.categorySelect) return;
        
        const trigger = elements.categorySelect.querySelector('.select-trigger');
        const dropdown = elements.categorySelect.querySelector('.select-dropdown');
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.categorySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.categorySelect.classList.add('active');
                elements.categorySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempCategories = [...state.filters.category];
                updateCategoryCheckboxes();
            }
        });
        
        if (elements.categorySearch) {
            elements.categorySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.categoryOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempCategories = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempCategories.includes(value)) {
                            state.tempCategories.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempCategories.indexOf(value);
                        if (index > -1) {
                            state.tempCategories.splice(index, 1);
                        }
                        if (state.tempCategories.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearCategoryBtn) {
            elements.clearCategoryBtn.addEventListener('click', () => {
                state.tempCategories = [];
                updateCategoryCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyCategoryBtn) {
            elements.applyCategoryBtn.addEventListener('click', () => {
                state.filters.category = [...state.tempCategories];
                updateCategoryDisplay();
                elements.categorySelect.classList.remove('active');
                elements.categorySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                state.currentPage = 1;
                loadGrants();
            });
        }
    }
    
    function setupMultiSelectPrefecture() {
        if (!elements.prefectureSelect) return;
        
        const trigger = elements.prefectureSelect.querySelector('.select-trigger');
        const dropdown = elements.prefectureSelect.querySelector('.select-dropdown');
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.prefectureSelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.prefectureSelect.classList.add('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureCheckboxes();
            }
        });
        
        if (elements.prefectureSearch) {
            elements.prefectureSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempPrefectures = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempPrefectures.includes(value)) {
                            state.tempPrefectures.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempPrefectures.indexOf(value);
                        if (index > -1) {
                            state.tempPrefectures.splice(index, 1);
                        }
                        if (state.tempPrefectures.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearPrefectureBtn) {
            elements.clearPrefectureBtn.addEventListener('click', () => {
                state.tempPrefectures = [];
                updatePrefectureCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyPrefectureBtn) {
            elements.applyPrefectureBtn.addEventListener('click', () => {
                state.filters.prefecture = [...state.tempPrefectures];
                updatePrefectureDisplay();
                elements.prefectureSelect.classList.remove('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                if (state.filters.prefecture.length === 1) {
                    const prefectureSlug = state.filters.prefecture[0];
                    const prefectureOption = document.querySelector(`.select-option[data-value="${prefectureSlug}"]`);
                    const prefectureName = prefectureOption ? prefectureOption.dataset.name : '';
                    loadMunicipalities(prefectureSlug, prefectureName);
                } else {
                    hideMunicipalityFilter();
                    state.filters.municipality = '';
                }
                
                state.currentPage = 1;
                loadGrants();
            });
        }
    }
    
    function setupMunicipalitySelect() {
        if (!elements.municipalitySelect) return;
        
        const trigger = elements.municipalitySelect.querySelector('.select-trigger');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.municipalitySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.municipalitySelect.classList.add('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        if (elements.municipalitySearch) {
            elements.municipalitySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                
                options.forEach(option => {
                    const name = option.textContent.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
    }
    
    function updateCategoryCheckboxes() {
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempCategories.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempCategories.length === 0;
    }
    
    function updateCategoryDisplay() {
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const count = state.filters.category.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択してください';
            elements.categoryCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択中`;
            elements.categoryCountBadge.textContent = count;
            elements.categoryCountBadge.style.display = 'inline-flex';
        }
    }
    
    function updatePrefectureCheckboxes() {
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempPrefectures.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempPrefectures.length === 0;
    }
    
    function updatePrefectureDisplay() {
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const count = state.filters.prefecture.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択してください';
            elements.prefectureCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択中`;
            elements.prefectureCountBadge.textContent = count;
            elements.prefectureCountBadge.style.display = 'inline-flex';
        }
    }
    
    function filterPrefecturesByRegion(region) {
        if (!elements.prefectureOptions) return;
        
        const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
        
        options.forEach(option => {
            const optionRegion = option.dataset.region;
            if (!region || optionRegion === region) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    }
    
    function closeAllSelects() {
        document.querySelectorAll('.custom-select').forEach(select => {
            select.classList.remove('active');
            select.setAttribute('aria-expanded', 'false');
            const dropdown = select.querySelector('.select-dropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select')) {
            closeAllSelects();
        }
    });
    
    function loadMunicipalities(prefectureSlug, prefectureName) {
        console.log('🏘️ Loading municipalities:', prefectureSlug, prefectureName);
        
        if (!prefectureSlug) {
            hideMunicipalityFilter();
            return;
        }
        
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'block';
        }
        
        if (elements.selectedPrefectureName) {
            elements.selectedPrefectureName.textContent = `（${prefectureName}）`;
        }
        
        const formData = new FormData();
        formData.append('action', 'gi_get_municipalities_for_prefecture');
        formData.append('prefecture_slug', prefectureSlug);
        formData.append('nonce', NONCE);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('📦 Municipality API Response:', data);
            
            let municipalities = [];
            
            if (data.success) {
                if (data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                    municipalities = data.data.data.municipalities;
                } else if (data.data && Array.isArray(data.data.municipalities)) {
                    municipalities = data.data.municipalities;
                } else if (Array.isArray(data.municipalities)) {
                    municipalities = data.municipalities;
                } else if (Array.isArray(data.data)) {
                    municipalities = data.data;
                }
            }
            
            console.log('🎯 Municipalities count:', municipalities.length);
            
            if (municipalities.length > 0) {
                state.currentMunicipalities = municipalities;
                renderMunicipalityOptions(municipalities);
            } else {
                renderMunicipalityOptions([]);
            }
        })
        .catch(error => {
            console.error('🚨 Municipality fetch error:', error);
            renderMunicipalityOptions([]);
        });
    }
    
    function renderMunicipalityOptions(municipalities) {
        if (!elements.municipalityOptions) return;
        
        let html = '<div class="select-option active" data-value="" role="option" aria-selected="true">すべて</div>';
        
        municipalities.forEach(municipality => {
            html += `<div class="select-option" data-value="${municipality.slug}" role="option" aria-selected="false">${municipality.name}</div>`;
        });
        
        elements.municipalityOptions.innerHTML = html;
        
        const options = elements.municipalityOptions.querySelectorAll('.select-option');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const text = option.textContent.trim();
                
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                option.classList.add('active');
                option.setAttribute('aria-selected', 'true');
                
                valueSpan.textContent = text;
                
                elements.municipalitySelect.classList.remove('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                state.filters.municipality = value;
                state.currentPage = 1;
                
                console.log('✅ Municipality selected:', value || '(all)');
                
                loadGrants();
            });
        });
    }
    
    function hideMunicipalityFilter() {
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'none';
        }
        
        state.filters.municipality = '';
        if (elements.municipalitySelect) {
            const valueSpan = elements.municipalitySelect.querySelector('.select-value');
            if (valueSpan) {
                valueSpan.textContent = 'すべて';
            }
        }
    }
    
    function setupEventListeners() {
        if (elements.keywordSearch) {
            elements.keywordSearch.addEventListener('input', debounce(handleSearchInput, 300));
            elements.keywordSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }
        
        if (elements.searchBtn) {
            elements.searchBtn.addEventListener('click', handleSearch);
        }
        
        if (elements.searchClearBtn) {
            elements.searchClearBtn.addEventListener('click', clearSearch);
        }
        
        elements.viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                elements.viewBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-pressed', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                state.view = this.dataset.view;
                elements.grantsContainer.setAttribute('data-view', state.view);
            });
        });
        
        if (elements.resetAllFiltersBtn) {
            elements.resetAllFiltersBtn.addEventListener('click', resetAllFilters);
        }
        
        // モバイルフィルターイベント（トグル機能付き）
        if (elements.mobileFilterToggle) {
            console.log('✅ Binding click to mobile-filter-toggle');
            elements.mobileFilterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔵 Toggle button clicked!');
                
                // フィルターパネルが開いているかチェック
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    console.log('  → Closing filter (toggle)');
                    closeMobileFilter();
                } else {
                    console.log('  → Opening filter');
                    openMobileFilter();
                }
            }, false);
        } else {
            console.error('❌ mobile-filter-toggle element not found!');
        }
        
        if (elements.mobileFilterClose) {
            console.log('✅ Binding click to mobile-filter-close');
            elements.mobileFilterClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔴 Close button clicked!');
                closeMobileFilter();
            }, false);
        } else {
            console.error('❌ mobile-filter-close element not found!');
        }
        
        // オーバーレイクリックで閉じる
        if (elements.filterPanelOverlay) {
            console.log('✅ Binding click to filter-panel-overlay');
            elements.filterPanelOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('⚫ Overlay clicked!');
                closeMobileFilter();
            }, false);
        } else {
            console.error('❌ filter-panel-overlay element not found!');
        }
        
        // フィルターパネル内のクリックは伝播を止める＋スクロール制御
        if (elements.filterPanel) {
            elements.filterPanel.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            // タッチイベントでスクロール制御
            let startY = 0;
            
            elements.filterPanel.addEventListener('touchstart', function(e) {
                startY = e.touches[0].pageY;
            }, { passive: true });
            
            elements.filterPanel.addEventListener('touchmove', function(e) {
                const scrollTop = elements.filterPanel.scrollTop;
                const scrollHeight = elements.filterPanel.scrollHeight;
                const height = elements.filterPanel.clientHeight;
                const currentY = e.touches[0].pageY;
                const delta = currentY - startY;
                
                // 上端で上スクロール、または下端で下スクロールの場合のみ背景スクロールを防止
                if ((scrollTop === 0 && delta > 0) || 
                    (scrollTop + height >= scrollHeight && delta < 0)) {
                    e.preventDefault();
                }
            }, { passive: false });
        } else {
            console.error('❌ filter-panel element not found!');
        }
        
        // ESCキーでフィルターを閉じる
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth <= 768) {
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    console.log('⌨️ ESC key pressed - closing filter');
                    closeMobileFilter();
                }
            }
        });
    }
    
    function openMobileFilter() {
        console.log('📱 openMobileFilter() called');
        if (elements.filterPanel) {
            elements.filterPanel.classList.add('active');
            // 背景スクロールを完全にブロック
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.touchAction = 'none';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.add('active');
                console.log('  ✅ Overlay activated');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'true');
            }
            console.log('  ✅ Filter panel opened successfully');
        } else {
            console.error('  ❌ Filter panel element not found!');
        }
    }
    
    function closeMobileFilter() {
        console.log('📱 closeMobileFilter() called');
        if (elements.filterPanel) {
            elements.filterPanel.classList.remove('active');
            // 背景スクロール制御を解除
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.touchAction = '';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.remove('active');
                console.log('  ✅ Overlay deactivated');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'false');
            }
            console.log('  ✅ Filter panel closed successfully');
        } else {
            console.error('  ❌ Filter panel element not found!');
        }
    }
    
    function handleSearchInput() {
        const query = elements.keywordSearch.value.trim();
        if (query.length > 0) {
            elements.searchClearBtn.style.display = 'flex';
        } else {
            elements.searchClearBtn.style.display = 'none';
        }
    }
    
    function handleSearch() {
        const query = elements.keywordSearch.value.trim();
        state.filters.search = query;
        state.currentPage = 1;
        loadGrants();
    }
    
    function clearSearch() {
        elements.keywordSearch.value = '';
        state.filters.search = '';
        elements.searchClearBtn.style.display = 'none';
        state.currentPage = 1;
        loadGrants();
    }
    
    function resetAllFilters() {
        console.log('🔄 Resetting all filters');
        
        state.filters = {
            search: '',
            category: [],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc',
            tag: ''
        };
        state.tempCategories = [];
        state.tempPrefectures = [];
        state.currentPage = 1;
        
        elements.keywordSearch.value = '';
        elements.searchClearBtn.style.display = 'none';
        
        resetCustomSelect(elements.regionSelect, '全国');
        resetCustomSelect(elements.amountSelect, '指定なし');
        resetCustomSelect(elements.statusSelect, 'すべて');
        resetCustomSelect(elements.difficultySelect, '指定なし');
        resetCustomSelect(elements.sortSelect, '新着順');
        
        updateCategoryDisplay();
        updateCategoryCheckboxes();
        updatePrefectureDisplay();
        updatePrefectureCheckboxes();
        
        filterPrefecturesByRegion('');
        hideMunicipalityFilter();
        
        console.log('✅ All filters reset');
        
        loadGrants();
    }
    
    function resetCustomSelect(selectElement, defaultText) {
        if (!selectElement) return;
        
        const valueSpan = selectElement.querySelector('.select-value');
        const options = selectElement.querySelectorAll('.select-option');
        
        valueSpan.textContent = defaultText;
        options.forEach(opt => {
            opt.classList.remove('active');
            opt.setAttribute('aria-selected', 'false');
        });
        options[0].classList.add('active');
        options[0].setAttribute('aria-selected', 'true');
    }
    
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        
        // パフォーマンス測定開始
        if (window.performance && window.performance.mark) {
            performance.mark('grants-load-start');
        }
        
        showLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'gi_ajax_load_grants');
        formData.append('nonce', NONCE);
        formData.append('page', state.currentPage);
        formData.append('posts_per_page', state.perPage);
        formData.append('view', state.view);
        
        if (state.filters.search) {
            formData.append('search', state.filters.search);
        }
        
        if (state.filters.category.length > 0) {
            formData.append('categories', JSON.stringify(state.filters.category));
        }
        
        if (state.filters.prefecture.length > 0) {
            formData.append('prefectures', JSON.stringify(state.filters.prefecture));
        }
        
        if (state.filters.municipality && state.filters.municipality !== '') {
            formData.append('municipalities', JSON.stringify([state.filters.municipality]));
        }
        
        if (state.filters.region) {
            formData.append('region', state.filters.region);
        }
        
        if (state.filters.amount) {
            formData.append('amount', state.filters.amount);
        }
        
        if (state.filters.status) {
            formData.append('status', JSON.stringify([state.filters.status]));
        }
        
        if (state.filters.difficulty) {
            formData.append('difficulty', JSON.stringify([state.filters.difficulty]));
        }
        
        if (state.filters.tag) {
            formData.append('tag', state.filters.tag);
        }
        
        formData.append('sort', state.filters.sort);
        
        console.log('📡 Loading grants:', state.filters);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayGrants(data.data.grants);
                updateStats(data.data.stats);
                updatePagination(data.data.pagination);
                updateActiveFiltersDisplay();
            } else {
                showError('データの読み込みに失敗しました。');
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            showError('通信エラーが発生しました。');
        })
        .finally(() => {
            state.isLoading = false;
            showLoading(false);
            
            // パフォーマンス測定終了
            if (window.performance && window.performance.mark) {
                performance.mark('grants-load-end');
                try {
                    performance.measure('grants-load-duration', 'grants-load-start', 'grants-load-end');
                    const measure = performance.getEntriesByName('grants-load-duration')[0];
                    console.log(`⚡ Grants loaded in ${Math.round(measure.duration)}ms`);
                } catch(e) {
                    // Ignore performance measurement errors
                }
            }
        });
    }
    
    function displayGrants(grants) {
        if (!elements.grantsContainer) return;
        
        if (!grants || grants.length === 0) {
            elements.grantsContainer.innerHTML = '';
            elements.grantsContainer.style.display = 'none';
            if (elements.noResults) {
                elements.noResults.style.display = 'block';
            }
            return;
        }
        
        elements.grantsContainer.style.display = state.view === 'single' ? 'flex' : 'grid';
        if (elements.noResults) {
            elements.noResults.style.display = 'none';
        }
        
        // 仮想スクロール対応（DocumentFragment使用でDOM操作最適化）
        const fragment = document.createDocumentFragment();
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = grants.map(grant => grant.html).join('');
        
        while (tempDiv.firstChild) {
            fragment.appendChild(tempDiv.firstChild);
        }
        
        // 一括DOM更新（リフローを1回に削減）
        elements.grantsContainer.innerHTML = '';
        elements.grantsContainer.appendChild(fragment);
        
        // モバイルでフィルター適用後は自動的にパネルを閉じる
        if (window.innerWidth <= 768) {
            closeMobileFilter();
        }
        
        // AI button listeners use event delegation (set up once in unified-frontend.js)
        // No need to re-initialize after AJAX updates
    }
    
    function updateStats(stats) {
        if (elements.resultsCount) {
            elements.resultsCount.textContent = (stats.total_found || 0).toLocaleString();
        }
        if (elements.showingFrom) {
            elements.showingFrom.textContent = (stats.showing_from || 0).toLocaleString();
        }
        if (elements.showingTo) {
            elements.showingTo.textContent = (stats.showing_to || 0).toLocaleString();
        }
    }
    
    function updatePagination(pagination) {
        if (!elements.pagination || !elements.paginationWrapper) return;
        
        if (!pagination || pagination.total_pages <= 1) {
            elements.paginationWrapper.style.display = 'none';
            return;
        }
        
        elements.paginationWrapper.style.display = 'block';
        
        let html = '';
        const current = pagination.current_page;
        const total = pagination.total_pages;
        
        html += `<button class="pagination-btn" ${current === 1 ? 'disabled' : ''} data-page="${current - 1}" aria-label="前のページへ">前へ</button>`;
        
        const maxPages = 7;
        let startPage = Math.max(1, current - Math.floor(maxPages / 2));
        let endPage = Math.min(total, startPage + maxPages - 1);
        
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        if (startPage > 1) {
            html += `<button class="pagination-btn" data-page="1" aria-label="1ページ目へ">1</button>`;
            if (startPage > 2) {
                html += `<span class="pagination-ellipsis" aria-hidden="true">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="pagination-btn ${i === current ? 'active' : ''}" data-page="${i}" aria-label="${i}ページ目へ" ${i === current ? 'aria-current="page"' : ''}>${i}</button>`;
        }
        
        if (endPage < total) {
            if (endPage < total - 1) {
                html += `<span class="pagination-ellipsis" aria-hidden="true">...</span>`;
            }
            html += `<button class="pagination-btn" data-page="${total}" aria-label="${total}ページ目へ">${total}</button>`;
        }
        
        html += `<button class="pagination-btn" ${current === total ? 'disabled' : ''} data-page="${current + 1}" aria-label="次のページへ">次へ</button>`;
        
        elements.pagination.innerHTML = html;
        
        elements.pagination.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    state.currentPage = parseInt(this.dataset.page);
                    loadGrants();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
	
	    function updateActiveFiltersDisplay() {
        if (!elements.activeFilters || !elements.activeFilterTags) return;
        
        const tags = [];
        
        if (state.filters.search) {
            tags.push({
                type: 'search',
                label: `検索: "${state.filters.search}"`,
                value: state.filters.search
            });
        }
        
        if (state.filters.category.length > 0) {
            state.filters.category.forEach(catSlug => {
                const option = document.querySelector(`.select-option[data-value="${catSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'category',
                        label: option.dataset.name || option.textContent.trim(),
                        value: catSlug
                    });
                }
            });
        }
        
        if (state.filters.prefecture.length > 0) {
            state.filters.prefecture.forEach(prefSlug => {
                const option = document.querySelector(`.select-option[data-value="${prefSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'prefecture',
                        label: option.dataset.name || option.textContent.trim(),
                        value: prefSlug
                    });
                }
            });
        }
        
        if (state.filters.municipality) {
            const municipalityOption = Array.from(elements.municipalityOptions.querySelectorAll('.select-option')).find(opt => opt.dataset.value === state.filters.municipality);
            if (municipalityOption) {
                tags.push({
                    type: 'municipality',
                    label: `市町村: ${municipalityOption.textContent.trim()}`,
                    value: state.filters.municipality
                });
            }
        }
        
        if (state.filters.amount) {
            const labels = {
                '0-100': '〜100万円',
                '100-500': '100万円〜500万円',
                '500-1000': '500万円〜1000万円',
                '1000-3000': '1000万円〜3000万円',
                '3000+': '3000万円以上'
            };
            tags.push({
                type: 'amount',
                label: `金額: ${labels[state.filters.amount]}`,
                value: state.filters.amount
            });
        }
        
        if (state.filters.status) {
            const labels = {
                'active': '募集中',
                'upcoming': '募集予定',
                'closed': '募集終了'
            };
            tags.push({
                type: 'status',
                label: `状況: ${labels[state.filters.status]}`,
                value: state.filters.status
            });
        }
        
        if (state.filters.difficulty) {
            const labels = {
                'easy': '易しい',
                'normal': '普通',
                'hard': '難しい'
            };
            tags.push({
                type: 'difficulty',
                label: `難易度: ${labels[state.filters.difficulty]}`,
                value: state.filters.difficulty
            });
        }
        
        if (state.filters.tag) {
            tags.push({
                type: 'tag',
                label: `#${state.filters.tag}`,
                value: state.filters.tag
            });
        }
        
        if (tags.length === 0) {
            elements.activeFilters.style.display = 'none';
            elements.resetAllFiltersBtn.style.display = 'none';
            if (elements.mobileFilterCount) {
                elements.mobileFilterCount.style.display = 'none';
            }
            return;
        }
        
        elements.activeFilters.style.display = 'flex';
        elements.resetAllFiltersBtn.style.display = 'flex';
        
        // モバイルフィルターバッジ更新
        if (elements.mobileFilterCount) {
            elements.mobileFilterCount.textContent = tags.length;
            elements.mobileFilterCount.style.display = 'flex';
        }
        
        elements.activeFilterTags.innerHTML = tags.map(tag => `
            <div class="filter-tag">
                <span>${escapeHtml(tag.label)}</span>
                <button class="filter-tag-remove" 
                        data-type="${tag.type}" 
                        data-value="${escapeHtml(tag.value)}"
                        aria-label="${escapeHtml(tag.label)}を削除"
                        type="button">×</button>
            </div>
        `).join('');
        
        elements.activeFilterTags.querySelectorAll('.filter-tag-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                removeFilter(this.dataset.type, this.dataset.value);
            });
        });
    }
    
    function removeFilter(type, value) {
        console.log('🗑️ Removing filter:', type, value);
        
        switch(type) {
            case 'search':
                clearSearch();
                break;
            case 'category':
                const catIndex = state.filters.category.indexOf(value);
                if (catIndex > -1) {
                    state.filters.category.splice(catIndex, 1);
                }
                state.tempCategories = [...state.filters.category];
                updateCategoryDisplay();
                updateCategoryCheckboxes();
                break;
            case 'prefecture':
                const prefIndex = state.filters.prefecture.indexOf(value);
                if (prefIndex > -1) {
                    state.filters.prefecture.splice(prefIndex, 1);
                }
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureDisplay();
                updatePrefectureCheckboxes();
                
                if (state.filters.prefecture.length !== 1) {
                    hideMunicipalityFilter();
                }
                break;
            case 'municipality':
                state.filters.municipality = '';
                const valueSpan = elements.municipalitySelect.querySelector('.select-value');
                if (valueSpan) {
                    valueSpan.textContent = 'すべて';
                }
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                options[0].classList.add('active');
                options[0].setAttribute('aria-selected', 'true');
                break;
            case 'amount':
                state.filters.amount = '';
                resetCustomSelect(elements.amountSelect, '指定なし');
                break;
            case 'status':
                state.filters.status = '';
                resetCustomSelect(elements.statusSelect, 'すべて');
                break;
            case 'difficulty':
                state.filters.difficulty = '';
                resetCustomSelect(elements.difficultySelect, '指定なし');
                break;
            case 'tag':
                state.filters.tag = '';
                break;
        }
        
        state.currentPage = 1;
        loadGrants();
    }
    
    function showLoading(show) {
        if (elements.loadingOverlay) {
            elements.loadingOverlay.style.display = show ? 'flex' : 'none';
        }
        if (elements.grantsContainer) {
            elements.grantsContainer.style.opacity = show ? '0.5' : '1';
            elements.grantsContainer.setAttribute('aria-busy', show ? 'true' : 'false');
        }
    }
    
    function showError(message) {
        console.error('❌ Error:', message);
        alert(message);
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    // パフォーマンス監視
    if ('PerformanceObserver' in window) {
        try {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.entryType === 'measure') {
                        console.log(`[Performance] ${entry.name}: ${Math.round(entry.duration)}ms`);
                    }
                }
            });
            observer.observe({ entryTypes: ['measure'] });
        } catch (e) {
            console.warn('[Performance] Observer not supported');
        }
    }
    
    // Google Analytics tracking
    function trackEvent(category, action, label, value) {
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': category,
                'event_label': label,
                'value': value
            });
        }
        console.log('[Analytics]', category, action, label, value);
    }
    
    // 検索イベントの追跡
    const originalLoadGrants = loadGrants;
    loadGrants = function() {
        trackEvent('Search', 'filter_applied', JSON.stringify(state.filters), 1);
        originalLoadGrants();
    };
    
    // 初期化実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    console.log('✅ Archive SEO Perfect v18.0 - Fully Loaded');
    console.log('📊 Initial State:', state);
    
})();
</script>

<?php get_footer(); ?>