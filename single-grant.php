<?php
/**
 * Grant Single Page - Complete SEO & UX Optimized v15.0 FINAL
 * 補助金詳細ページ - 完全版（SEO完璧対応・右サイドバー・モバイル最適化）
 * 
 * @package Grant_Insight_Perfect
 * @version 15.0.0-seo-perfect
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();
the_post();

$post_id = get_the_ID();
$seo_title = get_the_title();
$current_year = date('Y');

// ACFデータ取得
$grant_data = array(
    'organization' => function_exists('get_field') ? get_field('organization', $post_id) : '',
    'max_amount' => function_exists('get_field') ? get_field('max_amount', $post_id) : '',
    'max_amount_numeric' => function_exists('get_field') ? intval(get_field('max_amount_numeric', $post_id)) : 0,
    'subsidy_rate' => function_exists('get_field') ? get_field('subsidy_rate', $post_id) : '',
    'deadline' => function_exists('get_field') ? get_field('deadline', $post_id) : '',
    'deadline_date' => function_exists('get_field') ? get_field('deadline_date', $post_id) : '',
    'grant_target' => function_exists('get_field') ? get_field('grant_target', $post_id) : '',
    'contact_info' => function_exists('get_field') ? get_field('contact_info', $post_id) : '',
    'official_url' => function_exists('get_field') ? get_field('official_url', $post_id) : '',
    'application_status' => function_exists('get_field') ? get_field('application_status', $post_id) : 'open',
    'required_documents' => function_exists('get_field') ? get_field('required_documents', $post_id) : '',
    'adoption_rate' => function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : 0,
    'grant_difficulty' => function_exists('get_field') ? get_field('grant_difficulty', $post_id) : 'normal',
    'is_featured' => function_exists('get_field') ? get_field('is_featured', $post_id) : false,
    'views_count' => function_exists('get_field') ? intval(get_field('views_count', $post_id)) : 0,
    'ai_summary' => function_exists('get_field') ? get_field('ai_summary', $post_id) : '',
);

// タクソノミー取得
$taxonomies = array(
    'categories' => wp_get_post_terms($post_id, 'grant_category'),
    'prefectures' => wp_get_post_terms($post_id, 'grant_prefecture'),
    'municipalities' => wp_get_post_terms($post_id, 'grant_municipality'),
    'tags' => wp_get_post_tags($post_id),
);

foreach ($taxonomies as $key => $terms) {
    if (is_wp_error($terms) || empty($terms)) {
        $taxonomies[$key] = array();
    }
}

// 金額フォーマット
$formatted_amount = '';
$max_amount_yen = intval($grant_data['max_amount_numeric']);

if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif (!empty($grant_data['max_amount'])) {
    $formatted_amount = $grant_data['max_amount'];
}

// 締切日計算
$deadline_info = '';
$deadline_class = '';
$days_remaining = 0;

if (!empty($grant_data['deadline_date'])) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_info = date('Y年n月j日', $deadline_timestamp);
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / 86400);
        
        if ($days_remaining <= 0) {
            $deadline_class = 'closed';
            $deadline_info .= ' (終了)';
        } elseif ($days_remaining <= 7) {
            $deadline_class = 'urgent';
            $deadline_info .= ' (残' . $days_remaining . '日)';
        } elseif ($days_remaining <= 30) {
            $deadline_class = 'warning';
        }
    }
} elseif (!empty($grant_data['deadline'])) {
    $deadline_info = $grant_data['deadline'];
}

// 難易度設定
$difficulty_configs = array(
    'easy' => array('label' => '易', 'dots' => 1, 'description' => '初心者向け'),
    'normal' => array('label' => '中', 'dots' => 2, 'description' => '一般的'),
    'hard' => array('label' => '難', 'dots' => 3, 'description' => '専門的'),
);

$difficulty = !empty($grant_data['grant_difficulty']) ? $grant_data['grant_difficulty'] : 'normal';
$difficulty_data = isset($difficulty_configs[$difficulty]) ? $difficulty_configs[$difficulty] : $difficulty_configs['normal'];

// ステータス
$status_configs = array(
    'open' => array('label' => '募集中', 'class' => 'open'),
    'closed' => array('label' => '終了', 'class' => 'closed'),
    'upcoming' => array('label' => '募集予定', 'class' => 'upcoming'),
);

$application_status = !empty($grant_data['application_status']) ? $grant_data['application_status'] : 'open';
$status_data = isset($status_configs[$application_status]) ? $status_configs[$application_status] : $status_configs['open'];

// 閲覧数更新
$current_views = intval($grant_data['views_count']);
$new_views = $current_views + 1;
if (function_exists('update_post_meta')) {
    update_post_meta($post_id, 'views_count', $new_views);
    $grant_data['views_count'] = $new_views;
}

// SEO: OGP画像取得
$og_image = '';
if (has_post_thumbnail($post_id)) {
    $og_image = get_the_post_thumbnail_url($post_id, 'large');
} else {
    $og_image = get_site_icon_url(512);
    if (empty($og_image)) {
        $og_image = get_template_directory_uri() . '/assets/images/default-og-grant.jpg';
    }
}

// SEO: メタディスクリプション生成
$meta_description = '';
if (!empty($grant_data['ai_summary'])) {
    $meta_description = wp_trim_words($grant_data['ai_summary'], 30, '...');
} elseif (has_excerpt()) {
    $meta_description = wp_trim_words(get_the_excerpt(), 30, '...');
} else {
    $meta_description = wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '...');
}

// 読了時間計算
$content = get_the_content();
$word_count = mb_strlen(strip_tags($content), 'UTF-8');
$reading_time = max(1, ceil($word_count / 400));

// SEO: キーワード生成
$seo_keywords = array();
$seo_keywords[] = $seo_title;
$seo_keywords[] = '補助金';
$seo_keywords[] = '補助金';
$seo_keywords[] = $current_year . '年度';
if (!empty($grant_data['organization'])) {
    $seo_keywords[] = $grant_data['organization'];
}
foreach ($taxonomies['categories'] as $cat) {
    $seo_keywords[] = $cat->name;
}
foreach ($taxonomies['prefectures'] as $pref) {
    $seo_keywords[] = $pref->name;
}
$seo_keywords = array_unique($seo_keywords);

// 関連補助金取得
$related_args = array(
    'post_type' => 'grant',
    'posts_per_page' => 6,
    'post__not_in' => array($post_id),
    'post_status' => 'publish',
    'orderby' => 'rand',
);

if (!empty($taxonomies['categories'])) {
    $related_args['tax_query'] = array(
        array(
            'taxonomy' => 'grant_category',
            'field' => 'term_id',
            'terms' => $taxonomies['categories'][0]->term_id,
        ),
    );
}

$related_query = new WP_Query($related_args);

// 公開日・更新日
$published_date = get_the_date('c');
$modified_date = get_the_modified_date('c');
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo esc_html($seo_title . ' | ' . get_bloginfo('name')); ?></title>
    <meta name="description" content="<?php echo esc_attr($meta_description); ?>">
    <meta name="keywords" content="<?php echo esc_attr(implode(',', $seo_keywords)); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:locale" content="ja_JP">
    <meta property="article:published_time" content="<?php echo $published_date; ?>">
    <meta property="article:modified_time" content="<?php echo $modified_date; ?>">
    <meta property="article:author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php foreach ($taxonomies['categories'] as $cat): ?>
    <meta property="article:section" content="<?php echo esc_attr($cat->name); ?>">
    <?php endforeach; ?>
    <?php foreach ($taxonomies['tags'] as $tag): ?>
    <meta property="article:tag" content="<?php echo esc_attr($tag->name); ?>">
    <?php endforeach; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <meta name="twitter:site" content="@joseikin_insight">
    <meta name="twitter:creator" content="@joseikin_insight">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Alternate Links -->
    <link rel="alternate" type="application/rss+xml" title="<?php echo esc_attr(get_bloginfo('name')); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>">
    
    <?php wp_head(); ?>
    
    <style>
    /* モバイル検索入力ズーム防止 - viewport meta制御の代わりにフォントサイズで対応 */
    input[type="text"],
    input[type="search"],
    input[type="email"],
    input[type="tel"],
    textarea,
    select {
        font-size: 16px !important; /* 16px以上でiOSのズームを防止 */
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }
    
    @media (max-width: 768px) {
        input[type="text"],
        input[type="search"],
        input[type="email"],
        input[type="tel"],
        textarea,
        select {
            font-size: 16px !important;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
    }
    </style>
</head>

<body <?php body_class(); ?>>

<!-- 構造化データ（JSON-LD） - 完璧版 -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "MonetaryGrant",
      "@id": "<?php echo esc_js(get_permalink() . '#grant'); ?>",
      "name": <?php echo json_encode($seo_title); ?>,
      "description": <?php echo json_encode($meta_description); ?>,
      "url": "<?php echo esc_js(get_permalink()); ?>",
      "image": "<?php echo esc_js($og_image); ?>",
      "funder": {
        "@type": "Organization",
        "name": <?php echo json_encode($grant_data['organization'] ?: get_bloginfo('name')); ?>
      },
      <?php if ($max_amount_yen > 0): ?>
      "amount": {
        "@type": "MonetaryAmount",
        "currency": "JPY",
        "value": "<?php echo $max_amount_yen; ?>"
      },
      <?php endif; ?>
      <?php if (!empty($grant_data['deadline_date'])): ?>
      "applicationDeadline": "<?php echo esc_js($grant_data['deadline_date']); ?>",
      <?php endif; ?>
      "datePublished": "<?php echo $published_date; ?>",
      "dateModified": "<?php echo $modified_date; ?>",
      "inLanguage": "ja-JP",
      "isAccessibleForFree": true
    },
    {
      "@type": "Article",
      "@id": "<?php echo esc_js(get_permalink() . '#article'); ?>",
      "headline": <?php echo json_encode($seo_title); ?>,
      "description": <?php echo json_encode($meta_description); ?>,
      "image": "<?php echo esc_js($og_image); ?>",
      "datePublished": "<?php echo $published_date; ?>",
      "dateModified": "<?php echo $modified_date; ?>",
      "author": {
        "@type": "Organization",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "url": "<?php echo esc_js(home_url()); ?>"
      },
      "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "url": "<?php echo esc_js(home_url()); ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?php echo esc_js(get_site_icon_url(512, get_template_directory_uri() . '/assets/images/logo.png')); ?>"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo esc_js(get_permalink()); ?>"
      },
      "articleSection": <?php echo json_encode(!empty($taxonomies['categories']) ? $taxonomies['categories'][0]->name : '補助金情報'); ?>,
      "keywords": <?php echo json_encode(implode(',', $seo_keywords)); ?>,
      "wordCount": <?php echo intval($word_count); ?>,
      "timeRequired": "PT<?php echo $reading_time; ?>M",
      "inLanguage": "ja-JP"
    },
    {
      "@type": "FAQPage",
      "@id": "<?php echo esc_js(get_permalink() . '#faq'); ?>",
      "mainEntity": [
        <?php if ($grant_data['grant_target']): ?>
        {
          "@type": "Question",
          "name": "この補助金の対象者は誰ですか？",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": <?php echo json_encode(wp_strip_all_tags($grant_data['grant_target'])); ?>
          }
        }<?php if ($grant_data['required_documents']): ?>,<?php endif; ?>
        <?php endif; ?>
        <?php if ($grant_data['required_documents']): ?>
        {
          "@type": "Question",
          "name": "申請に必要な書類は何ですか？",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": <?php echo json_encode(wp_strip_all_tags($grant_data['required_documents'])); ?>
          }
        },
        <?php endif; ?>
        {
          "@type": "Question",
          "name": "申請から採択までどのくらいかかりますか？",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "通常、申請から採択決定まで1〜2ヶ月程度かかります。ただし、補助金の種類や申請時期によって異なる場合がありますので、詳しくは担当窓口にお問い合わせください。"
          }
        },
        {
          "@type": "Question",
          "name": "不採択になった場合、再申請は可能ですか？",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "多くの場合、次回の募集期間で再申請が可能です。不採択の理由を確認し、改善した上で再度申請することをお勧めします。"
          }
        }
      ]
    },
    {
      "@type": "BreadcrumbList",
      "@id": "<?php echo esc_js(get_permalink() . '#breadcrumb'); ?>",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "ホーム",
          "item": "<?php echo esc_js(home_url('/')); ?>"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "補助金一覧",
          "item": "<?php echo esc_js(home_url('/grant/')); ?>"
        }
        <?php if (!empty($taxonomies['categories'])): ?>
        ,{
          "@type": "ListItem",
          "position": 3,
          "name": "<?php echo esc_js($taxonomies['categories'][0]->name); ?>",
          "item": "<?php echo esc_js(get_term_link($taxonomies['categories'][0])); ?>"
        },
        {
          "@type": "ListItem",
          "position": 4,
          "name": <?php echo json_encode($seo_title); ?>,
          "item": "<?php echo esc_js(get_permalink()); ?>"
        }
        <?php else: ?>
        ,{
          "@type": "ListItem",
          "position": 3,
          "name": <?php echo json_encode($seo_title); ?>,
          "item": "<?php echo esc_js(get_permalink()); ?>"
        }
        <?php endif; ?>
      ]
    },
    {
      "@type": "HowTo",
      "@id": "<?php echo esc_js(get_permalink() . '#howto'); ?>",
      "name": "<?php echo esc_js($seo_title); ?>の申請方法",
      "description": "補助金の申請から採択までの具体的な手順を解説します。",
      "step": [
        {
          "@type": "HowToStep",
          "position": 1,
          "name": "必要書類の準備",
          "text": "事業計画書、見積書などを用意します。",
          "url": "<?php echo esc_js(get_permalink() . '#application-flow'); ?>"
        },
        {
          "@type": "HowToStep",
          "position": 2,
          "name": "申請書類の提出",
          "text": "オンラインまたは郵送で提出します。",
          "url": "<?php echo esc_js(get_permalink() . '#application-flow'); ?>"
        },
        {
          "@type": "HowToStep",
          "position": 3,
          "name": "審査",
          "text": "通常1〜2ヶ月程度かかります。",
          "url": "<?php echo esc_js(get_permalink() . '#application-flow'); ?>"
        },
        {
          "@type": "HowToStep",
          "position": 4,
          "name": "採択・交付決定",
          "text": "結果通知と交付手続きを行います。",
          "url": "<?php echo esc_js(get_permalink() . '#application-flow'); ?>"
        }
      ],
      "totalTime": "P2M"
    },
    {
      "@type": "WebPage",
      "@id": "<?php echo esc_js(get_permalink()); ?>",
      "url": "<?php echo esc_js(get_permalink()); ?>",
      "name": <?php echo json_encode($seo_title); ?>,
      "description": <?php echo json_encode($meta_description); ?>,
      "inLanguage": "ja-JP",
      "isPartOf": {
        "@type": "WebSite",
        "@id": "<?php echo esc_js(home_url() . '#website'); ?>",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "url": "<?php echo esc_js(home_url()); ?>"
      },
      "primaryImageOfPage": {
        "@type": "ImageObject",
        "url": "<?php echo esc_js($og_image); ?>"
      },
      "datePublished": "<?php echo $published_date; ?>",
      "dateModified": "<?php echo $modified_date; ?>"
    }
  ]
}
</script>

<style>
/* ===============================================
   COMPLETE DESIGN - RIGHT SIDEBAR LAYOUT
   =============================================== */

:root {
    /* カラー */
    --gus-white: #ffffff;
    --gus-black: #1a1a1a;
    --gus-gray-50: #fafafa;
    --gus-gray-100: #f5f5f5;
    --gus-gray-200: #eeeeee;
    --gus-gray-300: #e0e0e0;
    --gus-gray-400: #bdbdbd;
    --gus-gray-500: #757575;
    --gus-gray-600: #757575;
    --gus-gray-700: #616161;
    --gus-gray-800: #424242;
    --gus-gray-900: #212121;
    --gus-yellow: #ffeb3b;
    --gus-yellow-dark: #ffc107;
    
    /* タイポグラフィ - 2段階小さく調整 */
    --gus-text-xs: 0.6875rem;  /* 0.75rem から縮小 */
    --gus-text-sm: 0.8125rem;  /* 0.875rem から縮小 */
    --gus-text-base: 0.875rem; /* 1rem から縮小 */
    --gus-text-md: 0.875rem;   /* 0.9375rem から縮小 */
    --gus-text-lg: 0.9375rem;  /* 1rem から縮小 */
    --gus-text-xl: 1rem;       /* 1.125rem から縮小 */
    --gus-text-2xl: 1.25rem;   /* 1.5rem から縮小 */
    
    /* スペーシング */
    --gus-space-xs: 4px;
    --gus-space-sm: 8px;
    --gus-space-md: 12px;
    --gus-space-lg: 16px;
    --gus-space-xl: 24px;
    --gus-space-2xl: 32px;
    
    /* その他 */
    --gus-radius: 0px;
    --gus-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    --gus-shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gus-transition: 0.2s ease;
    
    /* レイアウト */
    --gus-sidebar-width: 360px;
    --gus-content-max-width: 1400px;
}

/* ベース */
.gus-single {
    max-width: var(--gus-content-max-width);
    margin: 0 auto;
    padding: var(--gus-space-xl) var(--gus-space-lg);
    background: var(--gus-white);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
    font-size: 0.875rem; /* 2段階小さく (1rem → 0.875rem) */
    color: var(--gus-gray-800);
    line-height: 1.7; /* 可読性のため少し増やす */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    user-select: text;
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
}

/* メインレイアウト - 右サイドバー */
.gus-layout {
    display: grid;
    grid-template-columns: 1fr var(--gus-sidebar-width);
    gap: var(--gus-space-2xl);
    align-items: start;
}

/* メインコンテンツ */
.gus-main {
    min-width: 0;
}

/* サイドバー - 固定配置 + スタイリッシュデザイン */
.gus-sidebar {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    padding-left: 0;
}

.gus-sidebar::-webkit-scrollbar {
    width: 6px;
}

.gus-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.gus-sidebar::-webkit-scrollbar-thumb {
    background: #E5E5E5;
    border-radius: 3px;
}

.gus-sidebar::-webkit-scrollbar-thumb:hover {
    background: #000000;
}

/* サイドバーカード - 白黒スタイリッシュデザイン */
.gus-sidebar-card {
    background: #FFFFFF;
    border: 1px solid #E5E5E5;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-sidebar-card:hover {
    border-color: #000000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.gus-sidebar-title {
    font-size: 13px;
    font-weight: 800;
    color: #000000;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* 目次 - コンパクト */
.gus-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gus-toc-item {
    margin-bottom: 4px;
}

.gus-toc-link {
    display: block;
    padding: 8px 10px;
    color: #666666;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    border-left: 2px solid transparent;
    border-radius: 4px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-toc-link:hover,
.gus-toc-link.active {
    color: #000000;
    background: #F5F5F5;
    border-left-color: #000000;
    padding-left: 14px;
    font-weight: 600;
}

/* モバイル目次CTA */
.gus-mobile-toc-cta {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 16px;
    z-index: 999;
    background: var(--gus-gray-900);
    color: var(--gus-white);
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: var(--gus-transition);
    align-items: center;
    justify-content: center;
    gap: 2px;
    padding: 0;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    user-select: none;
}

.gus-mobile-toc-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    font-size: 0.625rem;
    font-weight: 800;
    letter-spacing: -0.5px;
}

.gus-mobile-toc-icon-toc {
    font-size: 1.25rem;
    line-height: 1;
}

.gus-mobile-toc-icon-ai {
    font-size: 0.75rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--gus-yellow) 0%, var(--gus-white) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 0 8px rgba(255, 235, 59, 0.5);
}

.gus-mobile-toc-cta:hover,
.gus-mobile-toc-cta:active {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

.gus-mobile-toc-cta:active {
    transform: scale(0.95);
}

.gus-mobile-toc-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    pointer-events: none;
}

.gus-mobile-toc-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.gus-mobile-toc-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--gus-white);
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    padding: var(--gus-space-xl) var(--gus-space-lg);
    max-height: 70vh;
    overflow-y: auto;
    z-index: 1000;
    transform: translateY(100%);
    visibility: hidden;
    transition: transform 0.3s ease;
    box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.2);
}

.gus-mobile-toc-panel.active {
    transform: translateY(0);
    visibility: visible;
}

.gus-mobile-toc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--gus-space-lg);
    padding-bottom: var(--gus-space-md);
    border-bottom: 2px solid var(--gus-gray-300);
}

.gus-mobile-toc-title {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    color: var(--gus-black);
    margin: 0;
}

.gus-mobile-toc-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gus-gray-700);
    cursor: pointer;
    padding: var(--gus-space-xs);
    line-height: 1;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gus-mobile-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gus-mobile-toc-list .gus-toc-item {
    margin-bottom: var(--gus-space-xs);
}

.gus-mobile-toc-list .gus-toc-link {
    font-size: 0.75rem;
    padding: 8px var(--gus-space-sm);
    border-radius: 4px;
    min-height: 36px;
    display: flex;
    align-items: center;
}

/* モバイルナビタブ */
.gus-mobile-nav-tabs {
    display: flex;
    gap: var(--gus-space-xs);
    margin-bottom: var(--gus-space-lg);
    border-bottom: 2px solid var(--gus-gray-300);
}

.gus-mobile-nav-tab {
    flex: 1;
    padding: var(--gus-space-md);
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: var(--gus-text-sm);
    font-weight: 600;
    color: var(--gus-gray-600);
    cursor: pointer;
    transition: var(--gus-transition);
    margin-bottom: -2px;
}

.gus-mobile-nav-tab.active {
    color: var(--gus-black);
    border-bottom-color: var(--gus-gray-900);
}

.gus-mobile-nav-content {
    display: none;
}

.gus-mobile-nav-content.active {
    display: block;
}

/* ===============================================
   Mobile AI Panel - Archive Design Unified
   チャット形式 + アーカイブデザイン統一
   =============================================== */
.gus-ai-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: calc(100vh - 200px);
    color-scheme: light !important;
}

/* Chat Messages Container */
.gus-ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #FFFFFF !important;
    min-height: 300px;
    max-height: calc(100vh - 350px);
    -webkit-overflow-scrolling: touch;
}

/* Message Bubble - Mobile */
.gus-ai-message {
    display: flex;
    gap: 10px;
    max-width: 90%;
    animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-ai-message--assistant {
    align-self: flex-start;
}

.gus-ai-message--user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

/* Avatar - Mobile */
.gus-ai-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid;
}

.gus-ai-message--assistant .gus-ai-message-avatar {
    background: linear-gradient(135deg, #000000 0%, #333333 100%);
    color: #FFEB3B;
    border-color: #000000;
}

.gus-ai-message--user .gus-ai-message-avatar {
    background: linear-gradient(135deg, #FFEB3B 0%, #FFD54F 100%);
    color: #000000;
    border-color: #FFEB3B;
}

/* Message Content - Mobile */
.gus-ai-message-content {
    background: #F8F8F8 !important;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #E5E5E5;
    font-size: 14px;
    line-height: 1.7;
    color: #1A1A1A !important;
    word-wrap: break-word;
}

.gus-ai-message--user .gus-ai-message-content {
    background: #000000 !important;
    color: #FFFFFF !important;
    border-color: #000000;
}

/* Input Container - Mobile */
.gus-ai-input-container {
    padding: 12px;
    background: #FFFFFF !important;
    border-top: 2px solid #E5E5E5;
    flex-shrink: 0;
}

.gus-ai-input-wrapper {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    margin-bottom: 10px;
}

.gus-ai-input {
    flex: 1;
    padding: 12px 14px;
    border: 2px solid #E5E5E5 !important;
    border-radius: 12px;
    font-size: 16px !important;
    font-family: inherit;
    min-height: 44px;
    max-height: 120px;
    resize: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: #F8F8F8 !important;
    color: #1A1A1A !important;
    -webkit-text-size-adjust: 100%;
    text-size-adjust: 100%;
}

.gus-ai-input:focus {
    outline: none;
    border-color: #000000 !important;
    background: #FFFFFF !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.gus-ai-input::placeholder {
    color: #999999;
}

/* Send Button - Mobile */
.gus-ai-send {
    width: 44px;
    height: 44px;
    background: #FFEB3B;
    color: #000000;
    border: 2px solid #FFEB3B;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(255, 235, 59, 0.3);
}

.gus-ai-send:hover:not(:disabled) {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.gus-ai-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.gus-ai-send svg {
    width: 18px;
    height: 18px;
}

/* Suggestion Chips - Mobile */
.gus-ai-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gus-ai-suggestion {
    padding: 8px 14px;
    background: #F8F8F8;
    color: #1A1A1A;
    border: 1px solid #E5E5E5;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}

.gus-ai-suggestion:hover,
.gus-ai-suggestion:active {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

/* Typing Indicator - Mobile */
.gus-ai-typing {
    display: flex;
    gap: 10px;
    max-width: 90%;
    align-self: flex-start;
}

.gus-ai-typing .gus-ai-message-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #000000 0%, #333333 100%);
    color: #FFEB3B;
    border-color: #000000;
}

.gus-ai-typing-dots {
    background: #F8F8F8 !important;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #E5E5E5;
    display: flex;
    gap: 4px;
    align-items: center;
}

.gus-ai-typing-dot {
    width: 6px;
    height: 6px;
    background: #666666;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.gus-ai-typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.gus-ai-typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

/* Scrollbar - Mobile */
.gus-ai-chat-messages::-webkit-scrollbar {
    width: 4px;
}

.gus-ai-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.gus-ai-chat-messages::-webkit-scrollbar-thumb {
    background: #E5E5E5;
    border-radius: 4px;
}

.gus-ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: #CCCCCC;
}

/* ===============================================
   PC AI Modal - Archive Design Unified
   ライトモード固定 + 黒ベース
   =============================================== */
.gus-pc-ai-modal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color-scheme: light !important;
}

.gus-pc-ai-modal.active {
    opacity: 1;
    visibility: visible;
}

.gus-pc-ai-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    cursor: pointer;
}

.gus-pc-ai-container {
    position: relative;
    width: 90%;
    max-width: 600px;
    height: 80vh;
    max-height: 700px;
    background: #FFFFFF !important;
    border: 2px solid #E5E5E5;
    border-radius: 16px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.95) translateY(20px);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color-scheme: light !important;
}

.gus-pc-ai-modal.active .gus-pc-ai-container {
    transform: scale(1) translateY(0);
}

.gus-pc-ai-header {
    padding: 20px 24px;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #FFFFFF;
    border-bottom: 2px solid #E5E5E5;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.gus-pc-ai-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
    color: #FFFFFF;
}

.gus-pc-ai-title svg {
    flex-shrink: 0;
    filter: drop-shadow(0 2px 4px rgba(255, 235, 59, 0.3));
}

.gus-pc-ai-subtitle {
    font-size: 13px;
    opacity: 0.85;
    max-width: 85%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
    padding-left: 32px;
    color: #FFFFFF;
}

.gus-pc-ai-close {
    position: absolute;
    top: 18px;
    right: 20px;
    width: 36px;
    height: 36px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 22px;
    font-weight: 700;
    line-height: 1;
    backdrop-filter: blur(10px);
}

.gus-pc-ai-close:hover {
    background: #FFEB3B;
    color: #000000;
    border-color: #FFEB3B;
    transform: rotate(90deg) scale(1.05);
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.4);
}

.gus-pc-ai-close:active {
    transform: rotate(90deg) scale(0.95);
}

.gus-pc-ai-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #FAFAFA !important;
    color-scheme: light !important;
}

.gus-pc-ai-messages {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
    scroll-behavior: smooth;
    background: #FAFAFA !important;
    color-scheme: light !important;
}

.gus-pc-ai-messages::-webkit-scrollbar {
    width: 8px;
}

.gus-pc-ai-messages::-webkit-scrollbar-track {
    background: transparent;
}

.gus-pc-ai-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    transition: background 0.2s;
}

.gus-pc-ai-messages::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Message Bubble */
.gus-ai-message {
    display: flex;
    gap: 12px;
    max-width: 85%;
    animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gus-ai-message--assistant {
    align-self: flex-start;
}

.gus-ai-message--user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

/* Message Avatar */
.gus-ai-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.gus-ai-message:hover .gus-ai-message-avatar {
    transform: scale(1.05);
}

.gus-ai-message--assistant .gus-ai-message-avatar {
    background: linear-gradient(135deg, #000000 0%, #333333 100%);
    color: #FFEB3B;
    border-color: #000000;
}

.gus-ai-message--user .gus-ai-message-avatar {
    background: linear-gradient(135deg, #FFEB3B 0%, #FFD54F 100%);
    color: #000000;
    border-color: #FFEB3B;
}

/* Message Content */
.gus-ai-message-content {
    background: #F8F8F8 !important;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #E5E5E5;
    font-size: 14px;
    line-height: 1.7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    position: relative;
    transition: all 0.2s ease;
    color: #1A1A1A !important;
    color-scheme: light !important;
}

.gus-ai-message-content:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.gus-ai-message--user .gus-ai-message-content {
    background: #000000 !important;
    color: #FFFFFF !important;
    border-color: #000000;
}

/* Chat Input Container */
.gus-pc-ai-input-container {
    padding: 20px 24px;
    background: #FFFFFF !important;
    border-top: 2px solid #E5E5E5;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
    color-scheme: light !important;
}

/* Input Wrapper */
.gus-pc-ai-input-wrapper {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
}

/* Chat Input */
.gus-pc-ai-input {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid #E5E5E5;
    border-radius: 12px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.5;
    resize: none;
    transition: all 0.2s ease;
    min-height: 52px;
    max-height: 120px;
    background: #F8F8F8 !important;
    color: #1A1A1A !important;
    color-scheme: light !important;
}

.gus-pc-ai-input::placeholder {
    color: #666666;
}

.gus-pc-ai-input:focus {
    outline: none;
    border-color: #000000;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
}

/* Send Button */
.gus-pc-ai-send {
    width: 52px;
    height: 52px;
    background: #FFEB3B;
    color: #000000;
    border: 2px solid #FFEB3B;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.3);
}

.gus-pc-ai-send:hover:not(:disabled) {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.gus-pc-ai-send:active:not(:disabled) {
    transform: scale(0.95);
}

.gus-pc-ai-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Suggestions */
.gus-pc-ai-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gus-pc-ai-suggestion {
    padding: 9px 16px;
    background: #FFFFFF;
    border: 2px solid #E5E5E5;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #666666;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.gus-pc-ai-suggestion:hover {
    background: #FFEB3B;
    color: #000000;
    border-color: #FFEB3B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 235, 59, 0.3);
}

.gus-pc-ai-suggestion:active {
    transform: translateY(0);
}

/* Typing Indicator */
.gus-ai-typing {
    display: flex;
    gap: 5px;
    padding: 8px 0;
}

.gus-ai-typing span {
    width: 9px;
    height: 9px;
    background: #666666;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.gus-ai-typing span:nth-child(1) { animation-delay: 0s; }
.gus-ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.gus-ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 80%, 100% { 
        transform: scale(0.7); 
        opacity: 0.4; 
    }
    40% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

/* Spinning Animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Mobile Modal Optimizations */
@media (max-width: 768px) {
    .gus-pc-ai-container {
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
        border: none;
        transform: translateY(100%);
    }
    
    .gus-pc-ai-modal.active .gus-pc-ai-container {
        transform: translateY(0);
    }
    
    .gus-pc-ai-header {
        padding: 16px 20px;
    }
    
    .gus-pc-ai-title {
        font-size: 16px;
    }
    
    .gus-pc-ai-subtitle {
        font-size: 12px;
    }
    
    .gus-pc-ai-close {
        width: 32px;
        height: 32px;
        top: 14px;
        right: 16px;
    }
    
    .gus-pc-ai-messages {
        padding: 16px;
        gap: 14px;
    }
    
    .gus-ai-message {
        max-width: 90%;
    }
    
    .gus-ai-message-avatar {
        width: 36px;
        height: 36px;
    }
    
    .gus-ai-message-content {
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .gus-pc-ai-input-container {
        padding: 16px;
    }
    
    .gus-pc-ai-input {
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .gus-pc-ai-send {
        width: 48px;
        height: 48px;
    }
    
    .gus-pc-ai-suggestion {
        padding: 8px 14px;
        font-size: 11px;
    }
}

/* 関連補助金（サイドバー版） */
.gus-related-mini {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.gus-related-mini-item {
    padding: 12px;
    background: #FFFFFF;
    border: 1px solid #E5E5E5;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-related-mini-item:hover {
    border-color: #000000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.gus-related-mini-title {
    font-size: 11px;
    font-weight: 600;
    color: #000000;
    margin-bottom: 6px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gus-related-mini-meta {
    font-size: 10px;
    color: #666666;
    font-weight: 500;
}

/* 統計グリッド - スタイリッシュ */
.gus-stats-grid {
    display: grid;
    gap: 10px;
}

.gus-stat-item {
    text-align: center;
    padding: 12px;
    background: #F5F5F5;
    border: 1px solid #E5E5E5;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-stat-item:hover {
    background: #FFFFFF;
    border-color: #000000;
    transform: translateY(-1px);
}

.gus-stat-label {
    font-size: 10px;
    color: #666666;
    margin-bottom: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.gus-stat-value {
    font-size: 20px;
    font-weight: 800;
    color: #000000;
    line-height: 1;
}

/* ヘッダー */
.gus-header {
    margin-bottom: var(--gus-space-xl);
}

.gus-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--gus-space-md);
    flex-wrap: wrap;
    gap: var(--gus-space-sm);
}

.gus-status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--gus-space-xs);
    padding: 6px 12px;
    border-radius: var(--gus-radius);
    font-size: var(--gus-text-sm);
    font-weight: 700;
    text-transform: uppercase;
    min-height: 32px;
}

.gus-status-badge.open {
    background: var(--gus-gray-800);
    color: var(--gus-white);
}

.gus-status-badge.urgent {
    background: var(--gus-gray-900);
    color: var(--gus-yellow);
}

.gus-status-badge.closed {
    background: var(--gus-gray-500);
    color: var(--gus-white);
}

.gus-featured-badge {
    background: var(--gus-yellow);
    color: var(--gus-black);
    padding: 6px 12px;
    font-size: var(--gus-text-sm);
    font-weight: 700;
    text-transform: uppercase;
    min-height: 32px;
    display: inline-flex;
    align-items: center;
}

.gus-reading-time {
    display: inline-flex;
    align-items: center;
    gap: var(--gus-space-xs);
    color: var(--gus-gray-600);
    font-size: var(--gus-text-sm);
    margin-bottom: var(--gus-space-md);
}

.gus-title {
    font-size: var(--gus-text-2xl);
    font-weight: 900;
    color: var(--gus-black);
    line-height: 1.4;
    margin: 0 0 var(--gus-space-lg);
    letter-spacing: -0.02em;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* セクション */
.gus-section {
    background: var(--gus-gray-50);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-xl);
    margin-bottom: var(--gus-space-lg);
    border-left: 3px solid var(--gus-gray-800);
}

.gus-section-header {
    display: flex;
    align-items: center;
    gap: var(--gus-space-sm);
    margin-bottom: var(--gus-space-lg);
    padding-bottom: var(--gus-space-md);
    border-bottom: 1px solid var(--gus-gray-300);
}

.gus-section-icon {
    width: 20px;
    height: 20px;
    opacity: 0.7;
    flex-shrink: 0;
}

.gus-section-title {
    font-size: var(--gus-text-xl);
    font-weight: 700;
    color: var(--gus-black);
    margin: 0;
}

.gus-section-content {
    font-size: var(--gus-text-base);
    color: var(--gus-gray-700);
    line-height: 1.7;
}

.gus-section-content p {
    margin-bottom: var(--gus-space-lg);
}

.gus-section-content p:last-child {
    margin-bottom: 0;
}

.gus-section-content h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    color: var(--gus-black);
    margin: var(--gus-space-xl) 0 var(--gus-space-md);
}

.gus-section-content h4 {
    font-size: var(--gus-text-md);
    font-weight: 700;
    color: var(--gus-black);
    margin: var(--gus-space-lg) 0 var(--gus-space-sm);
}

.gus-section-content ul,
.gus-section-content ol {
    padding-left: 24px;
    margin-bottom: var(--gus-space-lg);
}

.gus-section-content li {
    margin-bottom: var(--gus-space-sm);
}

/* テーブル */
.gus-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.gus-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    overflow: hidden;
}

.gus-table th,
.gus-table td {
    padding: var(--gus-space-md) var(--gus-space-lg);
    text-align: left;
    border-bottom: 1px solid var(--gus-gray-300);
    font-size: var(--gus-text-sm);
    line-height: 1.6;
}

.gus-table th {
    background: var(--gus-gray-100);
    font-weight: 700;
    color: var(--gus-gray-700);
    width: 35%;
    vertical-align: top;
}

.gus-table td {
    font-weight: 500;
    color: var(--gus-gray-800);
    word-wrap: break-word;
}

.gus-table tr:last-child th,
.gus-table tr:last-child td {
    border-bottom: none;
}

/* 難易度 */
.gus-difficulty {
    display: flex;
    align-items: center;
    gap: var(--gus-space-sm);
}

.gus-difficulty-dots {
    display: flex;
    gap: 4px;
}

.gus-difficulty-dot {
    width: 6px;
    height: 6px;
    border-radius: var(--gus-radius);
    background: var(--gus-gray-300);
}

.gus-difficulty-dot.filled {
    background: var(--gus-gray-900);
}

/* 申請フロー */
.gus-application-flow {
    display: flex;
    flex-direction: column;
    gap: var(--gus-space-md);
}

.gus-flow-step {
    display: flex;
    align-items: center;
    gap: var(--gus-space-lg);
    background: var(--gus-white);
    border: 2px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-lg);
}

.gus-flow-number {
    width: 48px;
    height: 48px;
    background: var(--gus-gray-900);
    color: var(--gus-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--gus-text-xl);
    font-weight: 800;
    flex-shrink: 0;
}

.gus-flow-content h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    margin-bottom: var(--gus-space-xs);
}

.gus-flow-content p {
    font-size: var(--gus-text-sm);
    color: var(--gus-gray-600);
    margin: 0;
}

.gus-flow-arrow {
    text-align: center;
    font-size: var(--gus-text-2xl);
    color: var(--gus-gray-500);
}

/* FAQ */
.gus-faq {
    display: flex;
    flex-direction: column;
    gap: var(--gus-space-md);
}

.gus-faq-item {
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-lg);
}

.gus-faq-question {
    font-size: var(--gus-text-md);
    font-weight: 700;
    color: var(--gus-black);
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
}

.gus-faq-question::after {
    content: '+';
    font-size: var(--gus-text-2xl);
    font-weight: 700;
    transition: transform var(--gus-transition);
    flex-shrink: 0;
    margin-left: var(--gus-space-md);
}

.gus-faq-item[open] .gus-faq-question::after {
    transform: rotate(45deg);
}

.gus-faq-answer {
    margin-top: var(--gus-space-md);
    padding-top: var(--gus-space-md);
    border-top: 1px solid var(--gus-gray-200);
    color: var(--gus-gray-700);
    line-height: 1.7;
}

/* ボタン */
.gus-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    width: 100%;
    min-height: 40px;
    text-align: center;
    -webkit-tap-highlight-color: transparent;
    letter-spacing: 0.3px;
}

.gus-btn-primary {
    background: #000000;
    color: #FFFFFF;
    border: 2px solid #000000;
}

.gus-btn-primary:hover {
    background: #FFFFFF;
    color: #000000;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.gus-btn-secondary {
    background: #FFFFFF;
    color: #000000;
    border: 1px solid #E5E5E5;
}

.gus-btn-secondary:hover {
    border-color: #000000;
    background: #F5F5F5;
    transform: translateY(-1px);
}

.gus-btn-yellow {
    background: #FFEB3B;
    color: #000000;
    border: 2px solid #FFEB3B;
    font-weight: 700;
}

.gus-btn-yellow:hover {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.gus-btn-ai {
    background: #FFEB3B;
    color: #000000;
    font-weight: 700;
    border: 2px solid #FFEB3B;
    box-shadow: 0 2px 8px rgba(255, 235, 59, 0.3);
}

.gus-btn-ai:hover {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.gus-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ソーシャルシェア */
.gus-social-share {
    margin-top: var(--gus-space-2xl);
    padding: var(--gus-space-xl);
    background: var(--gus-gray-50);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
}

.gus-social-share h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    margin-bottom: var(--gus-space-md);
}

.gus-social-buttons {
    display: flex;
    gap: var(--gus-space-sm);
    flex-wrap: wrap;
}

.gus-social-buttons .gus-btn {
    width: auto;
    flex: 1;
    min-width: 120px;
}

/* タグ */
.gus-tags-section {
    margin-bottom: var(--gus-space-lg);
}

.gus-tags-section:last-child {
    margin-bottom: 0;
}

.gus-tags-label {
    font-size: var(--gus-text-xs);
    color: var(--gus-gray-600);
    font-weight: 600;
    margin-bottom: var(--gus-space-sm);
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.gus-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--gus-space-sm);
}

.gus-tag {
    display: inline-flex;
    align-items: center;
    gap: var(--gus-space-xs);
    padding: 8px 12px;
    background: var(--gus-white);
    color: var(--gus-gray-700);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    font-size: var(--gus-text-sm);
    text-decoration: none;
    transition: var(--gus-transition);
    font-weight: 600;
    min-height: 36px;
    -webkit-tap-highlight-color: transparent;
}

.gus-tag:hover,
.gus-tag:active {
    background: var(--gus-gray-900);
    color: var(--gus-white);
    border-color: var(--gus-gray-900);
}

/* パンくずナビ */
.gus-breadcrumb {
    margin-top: var(--gus-space-2xl);
    padding: var(--gus-space-lg);
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
}

.gus-breadcrumb ol {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: var(--gus-space-sm);
    font-size: var(--gus-text-sm);
}

.gus-breadcrumb li {
    display: flex;
    align-items: center;
}

.gus-breadcrumb a {
    color: var(--gus-gray-700);
    text-decoration: none;
}

.gus-breadcrumb a:hover {
    color: var(--gus-black);
    text-decoration: underline;
}

.gus-breadcrumb span {
    margin: 0 8px;
    color: var(--gus-gray-500);
}

/* 関連コンテンツグリッド - スタイリッシュ版 */
.gus-related-section {
    margin-top: var(--gus-space-2xl);
    padding: var(--gus-space-2xl) 0;
    border-top: 3px solid var(--gus-gray-900);
    background: linear-gradient(180deg, var(--gus-white) 0%, var(--gus-gray-50) 100%);
}

.gus-related-section-header {
    display: flex;
    align-items: center;
    gap: var(--gus-space-md);
    margin-bottom: var(--gus-space-2xl);
}

.gus-related-section-icon {
    width: 48px;
    height: 48px;
    background: var(--gus-gray-900);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.gus-related-section-title {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--gus-black);
    margin: 0;
    letter-spacing: -0.02em;
}

.gus-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--gus-space-xl);
}

.gus-related-card {
    background: var(--gus-white);
    border: 2px solid var(--gus-gray-300);
    border-radius: 12px;
    padding: var(--gus-space-xl);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.gus-related-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--gus-gray-900) 0%, var(--gus-yellow) 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.gus-related-card:hover::before {
    transform: scaleX(1);
}

.gus-related-card:hover {
    border-color: var(--gus-gray-900);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
}

.gus-related-card-number {
    position: absolute;
    top: var(--gus-space-lg);
    right: var(--gus-space-lg);
    width: 32px;
    height: 32px;
    background: var(--gus-gray-900);
    color: var(--gus-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--gus-text-xs);
    font-weight: 800;
}

.gus-related-card h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: var(--gus-space-md);
    line-height: 1.4;
    padding-right: 40px;
}

.gus-related-card h3 a {
    color: var(--gus-black);
    text-decoration: none;
    transition: color 0.2s ease;
}

.gus-related-card h3 a:hover {
    color: var(--gus-gray-700);
}

.gus-related-card-meta {
    display: flex;
    flex-direction: column;
    gap: var(--gus-space-sm);
    margin-bottom: var(--gus-space-lg);
    padding: var(--gus-space-md);
    background: var(--gus-gray-50);
    border-radius: 8px;
}

.gus-related-card-meta-item {
    display: flex;
    align-items: center;
    gap: var(--gus-space-xs);
    font-size: var(--gus-text-sm);
    color: var(--gus-gray-700);
}

.gus-related-card-meta-label {
    font-weight: 600;
    color: var(--gus-gray-600);
    min-width: 60px;
}

.gus-related-card-meta-value {
    font-weight: 700;
    color: var(--gus-black);
}

.gus-related-card .gus-btn {
    margin-top: auto;
    font-size: var(--gus-text-sm);
    font-weight: 700;
    padding: 12px var(--gus-space-lg);
    background: var(--gus-gray-900);
    color: var(--gus-white);
    border: 2px solid var(--gus-gray-900);
    transition: all 0.2s ease;
}

.gus-related-card .gus-btn:hover {
    background: var(--gus-white);
    color: var(--gus-gray-900);
    transform: translateX(4px);
}

/* アイコン */
.gus-icon {
    width: 18px;
    height: 18px;
    display: inline-block;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    flex-shrink: 0;
}

.gus-icon-money {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>');
}

.gus-icon-calendar {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>');
}

.gus-icon-building {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>');
}

.gus-icon-chart {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>');
}

.gus-icon-document {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>');
}

.gus-icon-link {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>');
}

.gus-icon-tag {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>');
}

.gus-icon-list {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23424242"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>');
}

/* スティッキーCTA（モバイル用） */
.gus-sticky-cta {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--gus-white);
    border-top: 2px solid var(--gus-gray-300);
    padding: var(--gus-space-md);
    z-index: 90;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
    display: none;
}

/* レスポンシブ */
@media (max-width: 1024px) {
    .gus-layout {
        display: flex;
        flex-direction: column;
    }
    
    .gus-sidebar {
        position: static;
        max-height: none;
        padding-left: 0;
        display: contents; /* サイドバーの子要素を直接gus-layoutに配置 */
    }
    
    /* スマホ: 目次をCTAボタンとして表示 */
    .gus-sidebar > nav[aria-label="目次"] {
        display: none !important;
    }
    
    .gus-mobile-toc-cta {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .gus-mobile-toc-overlay {
        display: block;
        visibility: hidden;
        pointer-events: none;
    }
    
    /* メインコンテンツを目次の後に配置 */
    .gus-main {
        order: 0;
    }
    
    /* 他のサイドバー要素を下部に配置 */
    .gus-sidebar > div {
        order: 2;
        margin-bottom: var(--gus-space-lg);
    }
    
    .gus-related-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--gus-space-lg);
    }
    
    .gus-related-section-header {
        flex-direction: row;
    }
    
    .gus-related-section-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .gus-related-section-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 768px) {
    .gus-single {
        padding: var(--gus-space-lg) var(--gus-space-md);
    }
    
    .gus-title {
        font-size: 1.25rem;
        line-height: 1.4;
    }
    
    /* スマホでレイアウトをflexboxに変更（既に1024pxで設定済み） */
    .gus-layout {
        display: flex;
        flex-direction: column;
    }
    
    .gus-sidebar {
        display: contents;
    }
    
    .gus-sidebar > nav[aria-label="目次"] {
        display: none !important;
    }
    
    .gus-main {
        order: 0;
    }
    
    .gus-sidebar > div {
        order: 2;
        margin-bottom: var(--gus-space-lg);
    }
    
    .gus-section {
        padding: var(--gus-space-lg);
    }
    
    .gus-section-content {
        font-size: var(--gus-text-base);
    }
    
    .gus-table-wrapper {
        margin: 0 calc(var(--gus-space-lg) * -1);
        padding: 0 var(--gus-space-lg);
    }
    
    .gus-table th,
    .gus-table td {
        padding: var(--gus-space-sm) var(--gus-space-md);
        font-size: var(--gus-text-sm);
    }
    
    .gus-flow-step {
        flex-direction: column;
        text-align: center;
    }
    
    .gus-related-grid {
        grid-template-columns: 1fr;
        gap: var(--gus-space-lg);
    }
    
    .gus-related-card {
        padding: var(--gus-space-lg);
    }
    
    .gus-related-card-number {
        width: 28px;
        height: 28px;
        font-size: 0.625rem;
    }
    
    .gus-related-card h3 {
        font-size: 1rem;
        padding-right: 36px;
    }
    
    .gus-social-buttons {
        flex-direction: column;
    }
    
    .gus-social-buttons .gus-btn {
        width: 100%;
    }
    
    .gus-breadcrumb ol {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .gus-breadcrumb li span {
        display: none;
    }
    
    .gus-sticky-cta {
        display: block;
    }
    
    .gus-sticky-cta .gus-btn {
        padding: 10px var(--gus-space-md);
        font-size: 0.875rem;
        min-height: 44px;
    }
    
    .gus-main {
        padding-bottom: 80px;
    }
}

@media (max-width: 480px) {
    .gus-single {
        padding: var(--gus-space-md) var(--gus-space-sm);
    }
    
    .gus-title {
        font-size: 1.25rem;
    }
    
    .gus-section {
        padding: var(--gus-space-md);
    }
    
    .gus-btn {
        padding: 14px var(--gus-space-md);
        font-size: var(--gus-text-base);
        min-height: 48px;
    }
    
    .gus-tag {
        padding: 10px 14px;
        min-height: 48px;
        font-size: var(--gus-text-base);
    }
    
    .gus-mobile-toc-cta {
        width: 64px;
        height: 64px;
    }
    
    .gus-mobile-toc-icon-toc {
        font-size: 1.5rem;
    }
    
    .gus-mobile-toc-icon-ai {
        font-size: 0.875rem;
    }
}

/* アクセシビリティ */
.gus-btn:focus-visible,
.gus-tag:focus-visible,
.gus-toc-link:focus-visible {
    outline: 2px solid var(--gus-yellow);
    outline-offset: 2px;
}

/* プリント */
@media print {
    .gus-sidebar,
    .gus-actions,
    .gus-social-share,
    .gus-sticky-cta {
        display: none !important;
    }
    
    .gus-layout {
        grid-template-columns: 1fr;
    }
    
    .gus-section {
        page-break-inside: avoid;
    }
}

/* 高コントラストモード */
@media (prefers-contrast: high) {
    .gus-btn-primary,
    .gus-status-badge {
        border: 2px solid currentColor;
    }
}

/* モーション削減 */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ===============================================
   MODERN SECTION DESIGN - 対象者・必要書類
   =============================================== */

/* Modern Section Container */
.gus-modern-section {
    background: linear-gradient(135deg, var(--gus-gray-50) 0%, var(--gus-white) 100%);
    border: none;
    border-left: 4px solid var(--gus-gray-900);
    border-radius: 8px;
    padding: var(--gus-space-2xl);
    margin-bottom: var(--gus-space-xl);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.gus-modern-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

/* Modern Header */
.gus-modern-header {
    display: flex;
    align-items: center;
    gap: var(--gus-space-lg);
    margin-bottom: var(--gus-space-xl);
    padding-bottom: var(--gus-space-lg);
    border-bottom: 2px solid var(--gus-gray-200);
}

/* Modern Icon */
.gus-modern-icon {
    font-size: 2rem;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gus-white);
    border-radius: 12px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Modern Title */
.gus-modern-title {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--gus-black);
    margin: 0;
    letter-spacing: -0.01em;
    line-height: 1.3;
}

/* Modern Content */
.gus-modern-content {
    font-size: var(--gus-text-base);
    color: var(--gus-gray-800);
    line-height: 1.8;
}

.gus-modern-content p {
    margin-bottom: var(--gus-space-lg);
}

.gus-modern-content p:last-child {
    margin-bottom: 0;
}

.gus-modern-content ul,
.gus-modern-content ol {
    padding-left: 28px;
    margin-bottom: var(--gus-space-lg);
}

.gus-modern-content li {
    margin-bottom: var(--gus-space-md);
    padding-left: var(--gus-space-xs);
}

.gus-modern-content li::marker {
    color: var(--gus-gray-600);
    font-weight: 700;
}

.gus-modern-content h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    color: var(--gus-black);
    margin: var(--gus-space-xl) 0 var(--gus-space-md);
    padding-left: var(--gus-space-md);
    border-left: 3px solid var(--gus-yellow);
}

.gus-modern-content h3:first-child {
    margin-top: 0;
}

.gus-modern-content h4 {
    font-size: var(--gus-text-md);
    font-weight: 700;
    color: var(--gus-gray-800);
    margin: var(--gus-space-lg) 0 var(--gus-space-sm);
}

.gus-modern-content strong {
    color: var(--gus-black);
    font-weight: 700;
}

.gus-modern-content a {
    color: var(--gus-gray-900);
    text-decoration: underline;
    text-decoration-color: var(--gus-yellow);
    text-decoration-thickness: 2px;
    text-underline-offset: 3px;
    transition: var(--gus-transition);
}

.gus-modern-content a:hover {
    color: var(--gus-black);
    text-decoration-color: var(--gus-yellow-dark);
}

/* Highlight boxes within modern content */
.gus-modern-content .highlight-box {
    background: var(--gus-white);
    border-left: 4px solid var(--gus-yellow);
    padding: var(--gus-space-lg);
    margin: var(--gus-space-lg) 0;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .gus-modern-section {
        padding: var(--gus-space-lg);
        border-radius: 4px;
    }
    
    .gus-modern-header {
        gap: var(--gus-space-md);
    }
    
    .gus-modern-icon {
        font-size: 1.5rem;
        width: 48px;
        height: 48px;
        border-radius: 8px;
    }
    
    .gus-modern-title {
        font-size: 1.125rem;
    }
    
    .gus-modern-content {
        font-size: var(--gus-text-sm);
    }
}

/* ===============================================
   DUPLICATE CONTENT PREVENTION
   =============================================== */

/* Hide duplicate sections that appear in the_content() */
.gus-content-wrapper .grant-target,
.gus-content-wrapper .grant-target-section,
.gus-content-wrapper .eligible-expenses,
.gus-content-wrapper .eligible-expenses-detailed,
.gus-content-wrapper .required-documents,
.gus-content-wrapper .required-documents-detailed,
.gus-content-wrapper .required-documents-display {
    display: none !important;
}

/* Hide any divs with these specific class patterns in content area */
.gus-content-wrapper > div[class*="grant-target"],
.gus-content-wrapper > div[class*="eligible-expenses"],
.gus-content-wrapper > div[class*="required-documents"] {
    display: none !important;
}

@media (max-width: 480px) {
    .gus-modern-section {
        padding: var(--gus-space-md);
    }
    
    .gus-modern-icon {
        font-size: 1.25rem;
        width: 40px;
        height: 40px;
    }
    
    .gus-modern-title {
        font-size: 1rem;
    }
}
</style>

<main class="gus-single" itemscope itemtype="https://schema.org/Article">
    <div class="gus-layout">
        <!-- メインコンテンツ -->
        <article class="gus-main">
            <!-- ヘッダー -->
            <header class="gus-header">
                <div class="gus-header-top">
                    <div class="gus-status-badge <?php echo $status_data['class']; ?> <?php echo $deadline_class; ?>">
                        <?php echo $status_data['label']; ?>
                        <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                            · <?php echo $days_remaining; ?>日
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($grant_data['is_featured']): ?>
                    <div class="gus-featured-badge">
                        注目
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="gus-reading-time">
                    <span>📖</span>
                    <span>読了時間: 約<?php echo $reading_time; ?>分</span>
                    <span>·</span>
                    <time datetime="<?php echo $published_date; ?>" itemprop="datePublished">
                        更新: <?php echo get_the_modified_date('Y年n月j日'); ?>
                    </time>
                </div>
                
                <h1 class="gus-title" itemprop="headline"><?php the_title(); ?></h1>
                
                <meta itemprop="dateModified" content="<?php echo $modified_date; ?>">
                <meta itemprop="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <meta itemprop="image" content="<?php echo esc_url($og_image); ?>">
            </header>
            
            <!-- AI要約 -->
            <?php if ($grant_data['ai_summary']): ?>
            <section id="ai-summary" class="gus-section" style="border-left-color: var(--gus-yellow);">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">AI要約</h2>
                </header>
                <div class="gus-section-content">
                    <p><?php echo esc_html($grant_data['ai_summary']); ?></p>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- 詳細情報 -->
            <section id="details" class="gus-section" itemprop="articleBody">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">詳細情報</h2>
                </header>
                <div class="gus-section-content gus-content-wrapper">
                    <?php the_content(); ?>
                </div>
            </section>
            
            <!-- 補助金詳細 -->
            <section id="grant-details" class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">補助金詳細</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-table-wrapper">
                        <table class="gus-table">
                            <?php if ($formatted_amount): ?>
                            <tr>
                                <th>補助金額</th>
                                <td><strong style="font-size: var(--gus-text-lg);">最大 <?php echo esc_html($formatted_amount); ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if ($grant_data['organization']): ?>
                            <tr>
                                <th>主催</th>
                                <td><?php echo esc_html($grant_data['organization']); ?></td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if ($deadline_info): ?>
                            <tr>
                                <th>申請締切</th>
                                <td><?php echo esc_html($deadline_info); ?></td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if ($grant_data['subsidy_rate']): ?>
                            <tr>
                                <th>補助率</th>
                                <td><?php echo esc_html($grant_data['subsidy_rate']); ?></td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <th>申請難易度</th>
                                <td>
                                    <div class="gus-difficulty">
                                        <strong><?php echo $difficulty_data['label']; ?></strong>
                                        <div class="gus-difficulty-dots">
                                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                                <div class="gus-difficulty-dot <?php echo $i <= $difficulty_data['dots'] ? 'filled' : ''; ?>"></div>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="font-size: var(--gus-text-sm); color: var(--gus-gray-600);">(<?php echo $difficulty_data['description']; ?>)</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php if ($grant_data['adoption_rate'] > 0): ?>
                            <tr>
                                <th>採択率</th>
                                <td><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <th>閲覧数</th>
                                <td><?php echo number_format($grant_data['views_count']); ?> 回</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>
            
            <!-- 対象者・対象事業 -->
            <?php if ($grant_data['grant_target']): ?>
            <section id="target" class="gus-section gus-modern-section">
                <header class="gus-modern-header">
                    <div class="gus-modern-icon">👥</div>
                    <h2 class="gus-modern-title">対象者・対象事業</h2>
                </header>
                <div class="gus-modern-content">
                    <?php echo wp_kses_post($grant_data['grant_target']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- 必要書類 -->
            <?php if ($grant_data['required_documents']): ?>
            <section id="documents" class="gus-section gus-modern-section">
                <header class="gus-modern-header">
                    <div class="gus-modern-icon">📄</div>
                    <h2 class="gus-modern-title">必要書類</h2>
                </header>
                <div class="gus-modern-content">
                    <?php echo wp_kses_post($grant_data['required_documents']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- 申請の流れ -->
            <section id="application-flow" class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">📝 申請の流れ</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-application-flow">
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">1</div>
                            <div class="gus-flow-content">
                                <h3>必要書類の準備</h3>
                                <p>事業計画書、見積書などを用意します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">2</div>
                            <div class="gus-flow-content">
                                <h3>申請書類の提出</h3>
                                <p>オンラインまたは郵送で提出します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">3</div>
                            <div class="gus-flow-content">
                                <h3>審査</h3>
                                <p>通常1〜2ヶ月程度かかります。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">4</div>
                            <div class="gus-flow-content">
                                <h3>採択・交付決定</h3>
                                <p>結果通知と交付手続きを行います。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- よくある質問 -->
            <section id="faq" class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">❓ よくある質問</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-faq">
                        <?php if ($grant_data['grant_target']): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question" role="button" aria-expanded="false" tabindex="0">この補助金の対象者は誰ですか？</summary>
                            <div class="gus-faq-answer" role="region">
                                <?php echo wp_kses_post($grant_data['grant_target']); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['required_documents']): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question" role="button" aria-expanded="false" tabindex="0">申請に必要な書類は何ですか？</summary>
                            <div class="gus-faq-answer" role="region">
                                <?php echo wp_kses_post($grant_data['required_documents']); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question" role="button" aria-expanded="false" tabindex="0">申請から採択までどのくらいかかりますか？</summary>
                            <div class="gus-faq-answer" role="region">
                                通常、申請から採択決定まで1〜2ヶ月程度かかります。ただし、補助金の種類や申請時期によって異なる場合がありますので、詳しくは担当窓口にお問い合わせください。
                            </div>
                        </details>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question" role="button" aria-expanded="false" tabindex="0">不採択になった場合、再申請は可能ですか？</summary>
                            <div class="gus-faq-answer" role="region">
                                多くの場合、次回の募集期間で再申請が可能です。不採択の理由を確認し、改善した上で再度申請することをお勧めします。詳しくは担当窓口にお問い合わせください。
                            </div>
                        </details>
                    </div>
                </div>
            </section>
            
            <!-- お問い合わせ -->
            <?php if ($grant_data['contact_info']): ?>
            <section id="contact" class="gus-section">
                <header class="gus-section-header">
                    <div class="gus-icon gus-icon-document gus-section-icon"></div>
                    <h2 class="gus-section-title">お問い合わせ</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo nl2br(esc_html($grant_data['contact_info'])); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- ソーシャルシェア -->
            <div class="gus-social-share">
                <h3>この補助金情報をシェア</h3>
                <div class="gus-social-buttons">
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                       class="gus-btn gus-btn-secondary" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="Twitterでシェア">
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                       class="gus-btn gus-btn-secondary" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="Facebookでシェア">
                        Facebook
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" 
                       class="gus-btn gus-btn-secondary" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="LINEで送る">
                        LINE
                    </a>
                    <button class="gus-btn gus-btn-secondary" 
                            onclick="navigator.clipboard.writeText('<?php echo get_permalink(); ?>'); alert('URLをコピーしました');"
                            aria-label="URLをコピー">
                        URLコピー
                    </button>
                </div>
            </div>
            
            <!-- 関連する補助金 - スタイリッシュ版 -->
            <?php if ($related_query->have_posts()): ?>
            <section id="related" class="gus-related-section">
                <header class="gus-related-section-header">
                    <div class="gus-related-section-icon">📋</div>
                    <h2 class="gus-related-section-title">関連する補助金</h2>
                </header>
                <div class="gus-related-grid">
                    <?php 
                    $related_query->rewind_posts();
                    $related_count = 0;
                    while ($related_query->have_posts()) : 
                        $related_query->the_post();
                        $related_count++;
                    ?>
                    <article class="gus-related-card">
                        <div class="gus-related-card-number"><?php echo $related_count; ?></div>
                        <h3>
                            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title() . 'の詳細を見る'); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <?php
                        $related_max_amount = function_exists('get_field') ? get_field('max_amount', get_the_ID()) : '';
                        $related_deadline = function_exists('get_field') ? get_field('deadline', get_the_ID()) : '';
                        $related_org = function_exists('get_field') ? get_field('organization', get_the_ID()) : '';
                        ?>
                        <div class="gus-related-card-meta">
                            <?php if ($related_max_amount): ?>
                            <div class="gus-related-card-meta-item">
                                <span class="gus-related-card-meta-label">💰 金額</span>
                                <span class="gus-related-card-meta-value"><?php echo esc_html($related_max_amount); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($related_deadline): ?>
                            <div class="gus-related-card-meta-item">
                                <span class="gus-related-card-meta-label">📅 締切</span>
                                <span class="gus-related-card-meta-value"><?php echo esc_html($related_deadline); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($related_org): ?>
                            <div class="gus-related-card-meta-item">
                                <span class="gus-related-card-meta-label">🏢 主催</span>
                                <span class="gus-related-card-meta-value"><?php echo esc_html(wp_trim_words($related_org, 5, '...')); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" 
                           class="gus-btn gus-btn-primary" 
                           aria-label="<?php echo esc_attr(get_the_title() . 'の詳細ページへ'); ?>">
                            詳細を見る →
                        </a>
                    </article>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php 
            wp_reset_postdata();
            endif; 
            ?>
            
            <!-- カテゴリー・地域リンク -->
            <section class="gus-section" style="margin-top: var(--gus-space-2xl);">
                <h2 class="gus-section-title" style="font-size: var(--gus-text-lg); font-weight: 700; margin-bottom: var(--gus-space-lg);">
                    この補助金のカテゴリー・地域
                </h2>
                
                <div style="display: grid; gap: var(--gus-space-lg);">
                    <?php if (!empty($taxonomies['categories'])): ?>
                    <div>
                        <div class="gus-tags-label">カテゴリー</div>
                        <div class="gus-tags">
                            <?php foreach ($taxonomies['categories'] as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" 
                               class="gus-tag" 
                               aria-label="<?php echo esc_attr($cat->name . 'カテゴリーの補助金一覧を見る'); ?>">
                                <?php echo esc_html($cat->name); ?> の補助金を見る →
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($taxonomies['prefectures'])): ?>
                    <div>
                        <div class="gus-tags-label">都道府県</div>
                        <div class="gus-tags">
                            <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                            <a href="<?php echo get_term_link($pref); ?>" 
                               class="gus-tag" 
                               aria-label="<?php echo esc_attr($pref->name . 'の補助金一覧を見る'); ?>">
                                <?php echo esc_html($pref->name); ?> の補助金を見る →
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($taxonomies['municipalities'])): ?>
                    <div>
                        <div class="gus-tags-label">市町村</div>
                        <div class="gus-tags">
                            <?php foreach ($taxonomies['municipalities'] as $muni): ?>
                            <a href="<?php echo get_term_link($muni); ?>" 
                               class="gus-tag" 
                               aria-label="<?php echo esc_attr($muni->name . 'の補助金一覧を見る'); ?>">
                                <?php echo esc_html($muni->name); ?> の補助金を見る →
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            
            <!-- パンくずナビゲーション -->
            <nav class="gus-breadcrumb" aria-label="パンくずナビゲーション" itemscope itemtype="https://schema.org/BreadcrumbList">
                <ol>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/'); ?>" itemprop="item" aria-label="ホームに戻る">
                            <span itemprop="name">ホーム</span>
                        </a>
                        <meta itemprop="position" content="1">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/grant/'); ?>" itemprop="item" aria-label="補助金一覧ページ">
                            <span itemprop="name">補助金一覧</span>
                        </a>
                        <meta itemprop="position" content="2">
                        <?php if (!empty($taxonomies['categories'])): ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo get_term_link($taxonomies['categories'][0]); ?>" 
                           itemprop="item"
                           aria-label="<?php echo esc_attr($taxonomies['categories'][0]->name . 'カテゴリー'); ?>">
                            <span itemprop="name"><?php echo esc_html($taxonomies['categories'][0]->name); ?></span>
                        </a>
                        <meta itemprop="position" content="3">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="4">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php else: ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="3">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php endif; ?>
                </ol>
            </nav>
        </article>
        
        <!-- 右サイドバー -->
        <aside class="gus-sidebar" role="complementary" aria-label="サイドバー">
            <!-- 目次 -->
            <nav class="gus-sidebar-card" aria-label="目次">
                <h2 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-list"></span>
                    目次
                </h2>
                <ul class="gus-toc-list">
                    <?php if ($grant_data['ai_summary']): ?>
                    <li class="gus-toc-item">
                        <a href="#ai-summary" class="gus-toc-link">AI要約</a>
                    </li>
                    <?php endif; ?>
                    <li class="gus-toc-item">
                        <a href="#details" class="gus-toc-link">詳細情報</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#grant-details" class="gus-toc-link">補助金詳細</a>
                    </li>
                    <?php if ($grant_data['grant_target']): ?>
                    <li class="gus-toc-item">
                        <a href="#target" class="gus-toc-link">対象者・対象事業</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($grant_data['required_documents']): ?>
                    <li class="gus-toc-item">
                        <a href="#documents" class="gus-toc-link">必要書類</a>
                    </li>
                    <?php endif; ?>
                    <li class="gus-toc-item">
                        <a href="#application-flow" class="gus-toc-link">申請の流れ</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#faq" class="gus-toc-link">よくある質問</a>
                    </li>
                    <?php if ($grant_data['contact_info']): ?>
                    <li class="gus-toc-item">
                        <a href="#contact" class="gus-toc-link">お問い合わせ</a>
                    </li>
                    <?php endif; ?>
                    <li class="gus-toc-item">
                        <a href="#related" class="gus-toc-link">関連する補助金</a>
                    </li>
                </ul>
            </nav>
            
            <!-- アクション -->
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-link"></span>
                    アクション
                </h2>
                <div class="gus-actions">
                    <?php if ($grant_data['official_url']): ?>
                    <a href="<?php echo esc_url($grant_data['official_url']); ?>" 
                       class="gus-btn gus-btn-primary" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="公式サイトで詳細を確認">
                        <span class="gus-icon gus-icon-link"></span>
                        公式サイト
                    </a>
                    <?php endif; ?>
                    
                    <button class="gus-btn gus-btn-primary" 
                            id="pcAiBtn"
                            type="button"
                            aria-label="AIに質問する">
                        AI質問
                    </button>
                    
                    <button class="gus-btn gus-btn-secondary" 
                            onclick="window.print()"
                            aria-label="このページを印刷">
                        印刷
                    </button>
                </div>
            </div>
            
            <!-- 統計 -->
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-chart"></span>
                    統計
                </h2>
                <div class="gus-stats-grid">
                    <div class="gus-stat-item">
                        <div class="gus-stat-label">閲覧数</div>
                        <div class="gus-stat-value"><?php echo number_format($grant_data['views_count']); ?></div>
                    </div>
                    
                    <?php if ($days_remaining > 0): ?>
                    <div class="gus-stat-item">
                        <div class="gus-stat-label">残日数</div>
                        <div class="gus-stat-value"><?php echo $days_remaining; ?>日</div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="gus-stat-item">
                        <div class="gus-stat-label">難易度</div>
                        <div class="gus-stat-value"><?php echo $difficulty_data['label']; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- 関連補助金（サイドバー版） -->
            <?php if ($related_query->have_posts()): ?>
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-document"></span>
                    関連補助金
                </h2>
                <div class="gus-related-mini">
                    <?php 
                    $related_query->rewind_posts();
                    $count = 0;
                    while ($related_query->have_posts() && $count < 4) : 
                        $related_query->the_post();
                        $count++;
                    ?>
                    <a href="<?php the_permalink(); ?>" 
                       class="gus-related-mini-item"
                       aria-label="<?php echo esc_attr(get_the_title() . 'の詳細を見る'); ?>">
                        <div class="gus-related-mini-title">
                            <?php echo wp_trim_words(get_the_title(), 10, '...'); ?>
                        </div>
                        <?php
                        $related_amount = function_exists('get_field') ? get_field('max_amount', get_the_ID()) : '';
                        if ($related_amount):
                        ?>
                        <div class="gus-related-mini-meta">
                            最大: <?php echo esc_html($related_amount); ?>
                        </div>
                        <?php endif; ?>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php 
            wp_reset_postdata();
            endif; 
            ?>
            
            <!-- タグ -->
            <?php if ($taxonomies['categories'] || $taxonomies['prefectures'] || $taxonomies['municipalities'] || $taxonomies['tags']): ?>
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <span class="gus-icon gus-icon-tag"></span>
                    タグ
                </h2>
                
                <?php if ($taxonomies['categories']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">カテゴリー</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['categories'] as $cat): ?>
                        <a href="<?php echo get_term_link($cat); ?>" 
                           class="gus-tag"
                           aria-label="<?php echo esc_attr($cat->name . 'カテゴリーの補助金一覧'); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['prefectures']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">都道府県</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                        <a href="<?php echo get_term_link($pref); ?>" 
                           class="gus-tag"
                           aria-label="<?php echo esc_attr($pref->name . 'の補助金一覧'); ?>">
                            <?php echo esc_html($pref->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['municipalities']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">市町村</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['municipalities'] as $muni): ?>
                        <a href="<?php echo get_term_link($muni); ?>" 
                           class="gus-tag"
                           aria-label="<?php echo esc_attr($muni->name . 'の補助金一覧'); ?>">
                            <?php echo esc_html($muni->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['tags']): ?>
                <div class="gus-tags-section">
                    <div class="gus-tags-label">タグ</div>
                    <div class="gus-tags">
                        <?php foreach ($taxonomies['tags'] as $tag): ?>
                        <a href="<?php echo get_term_link($tag); ?>" 
                           class="gus-tag"
                           aria-label="<?php echo esc_attr($tag->name . 'タグの補助金一覧'); ?>">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
</main>

<!-- モバイル用統合ナビCTAボタン -->
<button class="gus-mobile-toc-cta" aria-label="目次・AI質問を開く" id="mobileTocBtn" type="button">
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
        <h2 class="gus-mobile-toc-title">ナビゲーション</h2>
        <button class="gus-mobile-toc-close" aria-label="閉じる" id="mobileTocClose" type="button">
            ✕
        </button>
    </div>
    
    <!-- タブナビゲーション -->
    <div class="gus-mobile-nav-tabs">
        <button class="gus-mobile-nav-tab active" data-tab="ai" aria-label="AI質問タブ" type="button">
            <span style="background: linear-gradient(135deg, #ffc107 0%, #fff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900;">AI</span> 質問
        </button>
        <button class="gus-mobile-nav-tab" data-tab="toc" aria-label="目次タブ" type="button">
            📑 目次
        </button>
    </div>
    
    <!-- AI質問コンテンツ（デフォルト） - アーカイブ統一デザイン -->
    <div class="gus-mobile-nav-content active" id="aiContent">
        <div class="gus-ai-panel">
            <div class="gus-ai-chat-messages" id="mobileAiMessages" role="log" aria-live="polite" aria-atomic="false">
                <!-- 初期ウェルカムメッセージ -->
                <div class="gus-ai-message gus-ai-message--assistant">
                    <div class="gus-ai-message-avatar" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M2 12h20"/>
                        </svg>
                    </div>
                    <div class="gus-ai-message-content">
                        こんにちは！この助成金について何でもお聞きください。<br>
                        申請条件、必要書類、申請方法、対象経費など、詳しくお答えします。
                    </div>
                </div>
            </div>
            
            <div class="gus-ai-input-container">
                <div class="gus-ai-input-wrapper">
                    <textarea 
                        class="gus-ai-input" 
                        id="mobileAiInput"
                        placeholder="例：申請条件は何ですか？"
                        rows="2"
                        aria-label="質問を入力してください"></textarea>
                    <button 
                        class="gus-ai-send" 
                        id="mobileAiSend"
                        type="button"
                        aria-label="質問を送信">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>
                </div>
                <div class="gus-ai-suggestions" role="group" aria-label="質問の候補">
                    <button class="gus-ai-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                        申請条件は？
                    </button>
                    <button class="gus-ai-suggestion" type="button" data-question="必要な書類を教えてください">
                        必要書類は？
                    </button>
                    <button class="gus-ai-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                        対象経費は？
                    </button>
                    <button class="gus-ai-suggestion" type="button" data-question="申請方法を教えてください">
                        申請方法は？
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 目次コンテンツ -->
    <div class="gus-mobile-nav-content" id="tocContent">
    <ul class="gus-mobile-toc-list">
        <?php if ($grant_data['ai_summary']): ?>
        <li class="gus-toc-item">
            <a href="#ai-summary" class="gus-toc-link mobile-toc-link">AI要約</a>
        </li>
        <?php endif; ?>
        <li class="gus-toc-item">
            <a href="#details" class="gus-toc-link mobile-toc-link">詳細情報</a>
        </li>
        <li class="gus-toc-item">
            <a href="#grant-details" class="gus-toc-link mobile-toc-link">補助金詳細</a>
        </li>
        <?php if ($grant_data['grant_target']): ?>
        <li class="gus-toc-item">
            <a href="#target" class="gus-toc-link mobile-toc-link">対象者・対象事業</a>
        </li>
        <?php endif; ?>
        <?php if ($grant_data['required_documents']): ?>
        <li class="gus-toc-item">
            <a href="#documents" class="gus-toc-link mobile-toc-link">必要書類</a>
        </li>
        <?php endif; ?>
        <li class="gus-toc-item">
            <a href="#application-flow" class="gus-toc-link mobile-toc-link">申請の流れ</a>
        </li>
        <li class="gus-toc-item">
            <a href="#faq" class="gus-toc-link mobile-toc-link">よくある質問</a>
        </li>
        <?php if ($grant_data['contact_info']): ?>
        <li class="gus-toc-item">
            <a href="#contact" class="gus-toc-link mobile-toc-link">お問い合わせ</a>
        </li>
        <?php endif; ?>
        <li class="gus-toc-item">
            <a href="#related" class="gus-toc-link mobile-toc-link">関連する補助金</a>
        </li>
    </ul>
    </div>
</div>

<!-- モバイル用スティッキーCTA -->
<?php if ($grant_data['official_url']): ?>
<div class="gus-sticky-cta">
    <a href="<?php echo esc_url($grant_data['official_url']); ?>" 
       class="gus-btn gus-btn-yellow" 
       target="_blank" 
       rel="noopener noreferrer"
       aria-label="公式サイトで申請する">
        公式サイトで申請する →
    </a>
</div>
<?php endif; ?>

<!-- PC用AIモーダル - アーカイブ統一デザイン -->
<div class="gus-pc-ai-modal" id="pcAiModal" role="dialog" aria-modal="true" aria-labelledby="pcAiModalTitle">
    <div class="gus-pc-ai-overlay" id="pcAiOverlay" aria-hidden="true"></div>
    <div class="gus-pc-ai-container">
        <div class="gus-pc-ai-header">
            <div class="gus-pc-ai-title" id="pcAiModalTitle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    <circle cx="9" cy="10" r="1" fill="currentColor"/>
                    <circle cx="15" cy="10" r="1" fill="currentColor"/>
                </svg>
                <span>AI助成金アシスタント</span>
            </div>
            <div class="gus-pc-ai-subtitle"><?php echo esc_html(get_the_title()); ?></div>
            <button class="gus-pc-ai-close" id="pcAiClose" type="button" aria-label="モーダルを閉じる">
                ×
            </button>
        </div>
        
        <div class="gus-pc-ai-body">
            <div class="gus-pc-ai-messages" id="pcAiMessages" role="log" aria-live="polite" aria-atomic="false">
                <!-- 初期ウェルカムメッセージ -->
                <div class="gus-ai-message gus-ai-message--assistant">
                    <div class="gus-ai-message-avatar" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M2 12h20"/>
                        </svg>
                    </div>
                    <div class="gus-ai-message-content">
                        こんにちは！この助成金について何でもお聞きください。<br>
                        申請条件、必要書類、申請方法、対象経費など、詳しくお答えします。
                    </div>
                </div>
            </div>
        </div>
        
        <div class="gus-pc-ai-input-container">
            <div class="gus-pc-ai-input-wrapper">
                <textarea 
                    class="gus-pc-ai-input" 
                    id="pcAiInput"
                    placeholder="例：申請条件は何ですか？"
                    rows="2"
                    aria-label="質問を入力してください"></textarea>
                <button 
                    class="gus-pc-ai-send" 
                    id="pcAiSend"
                    type="button"
                    aria-label="質問を送信">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
            <div class="gus-pc-ai-suggestions" role="group" aria-label="質問の候補">
                <button class="gus-pc-ai-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                    申請条件は？
                </button>
                <button class="gus-pc-ai-suggestion" type="button" data-question="必要な書類を教えてください">
                    必要書類は？
                </button>
                <button class="gus-pc-ai-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                    対象経費は？
                </button>
                <button class="gus-pc-ai-suggestion" type="button" data-question="申請方法を教えてください">
                    申請方法は？
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 目次のスムーススクロール & 重複コンテンツ削除 & モバイル目次 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // 重複コンテンツの削除
    // ========================================
    const contentWrapper = document.querySelector('.gus-content-wrapper');
    if (contentWrapper) {
        // 削除対象のクラスパターン
        const duplicatePatterns = [
            'grant-target',
            'grant-target-section',
            'eligible-expenses',
            'eligible-expenses-detailed',
            'required-documents',
            'required-documents-detailed',
            'required-documents-display'
        ];
        
        // パターンに一致する要素を削除
        duplicatePatterns.forEach(pattern => {
            const elements = contentWrapper.querySelectorAll(`[class*="${pattern}"]`);
            elements.forEach(el => {
                console.log('🗑️ Removing duplicate element:', el.className);
                el.remove();
            });
        });
        
        // 空のdivも削除
        const emptyDivs = contentWrapper.querySelectorAll('div:empty');
        emptyDivs.forEach(div => div.remove());
    }
    
    // ========================================
    // モバイル統合ナビCTA機能
    // ========================================
    const mobileTocBtn = document.getElementById('mobileTocBtn');
    const mobileTocOverlay = document.getElementById('mobileTocOverlay');
    const mobileTocPanel = document.getElementById('mobileTocPanel');
    const mobileTocClose = document.getElementById('mobileTocClose');
    const mobileTocLinks = document.querySelectorAll('.mobile-toc-link');
    const mobileNavTabs = document.querySelectorAll('.gus-mobile-nav-tab');
    const mobileNavContents = document.querySelectorAll('.gus-mobile-nav-content');
    const mobileAiInput = document.getElementById('mobileAiInput');
    const mobileAiSend = document.getElementById('mobileAiSend');
    const mobileAiMessages = document.getElementById('mobileAiMessages');
    const mobileAiSuggestions = document.querySelectorAll('#aiContent .gus-ai-suggestion');
    
    function openMobileToc() {
        if (mobileTocOverlay && mobileTocPanel) {
            mobileTocOverlay.classList.add('active');
            mobileTocPanel.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeMobileToc() {
        if (mobileTocOverlay && mobileTocPanel) {
            mobileTocOverlay.classList.remove('active');
            mobileTocPanel.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    if (mobileTocBtn) {
        mobileTocBtn.addEventListener('click', openMobileToc);
    }
    
    if (mobileTocClose) {
        mobileTocClose.addEventListener('click', closeMobileToc);
    }
    
    if (mobileTocOverlay) {
        mobileTocOverlay.addEventListener('click', closeMobileToc);
    }
    
    // モバイル目次リンクをクリックしたら閉じる
    mobileTocLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(closeMobileToc, 300);
        });
    });
    
    // タブ切り替え機能
    mobileNavTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // すべてのタブを非アクティブに
            mobileNavTabs.forEach(t => t.classList.remove('active'));
            mobileNavContents.forEach(c => c.classList.remove('active'));
            
            // クリックされたタブをアクティブに
            this.classList.add('active');
            
            // 対応するコンテンツを表示
            if (targetTab === 'toc') {
                document.getElementById('tocContent').classList.add('active');
            } else if (targetTab === 'ai') {
                document.getElementById('aiContent').classList.add('active');
            }
        });
    });
    
    // モバイル提案チップ機能
    mobileAiSuggestions.forEach(chip => {
        chip.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            if (mobileAiInput) {
                mobileAiInput.value = question;
                mobileAiInput.focus();
                // すぐに送信
                if (mobileAiSend) {
                    mobileAiSend.click();
                }
            }
        });
    });
    
    // モバイルAI質問送信機能（チャット形式）
    async function sendMobileAiQuestion() {
        const question = mobileAiInput.value.trim();
        
        if (!question) {
            return;
        }
        
        // ユーザーメッセージを追加
        addMobileMessage(question, 'user');
        
        // 入力フィールドをクリア
        mobileAiInput.value = '';
        mobileAiInput.style.height = 'auto';
        
        // 送信ボタンを無効化
        mobileAiSend.disabled = true;
        
        // タイピングインジケーターを表示
        const typingId = showMobileTyping();
        
        try {
            const formData = new FormData();
            formData.append('action', 'handle_grant_ai_question');
            formData.append('nonce', '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>');
            formData.append('post_id', '<?php echo $post_id; ?>');
            formData.append('question', question);
            
            const response = await fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // タイピングインジケーターを削除
            removeMobileTyping(typingId);
            
            if (data.success && data.data && data.data.answer) {
                addMobileMessage(data.data.answer, 'assistant');
            } else {
                let errorMsg = '申し訳ございません。回答の生成に失敗しました。';
                if (data.data && typeof data.data === 'string') {
                    errorMsg = data.data;
                } else if (data.data && data.data.message) {
                    errorMsg = data.data.message;
                }
                addMobileMessage(errorMsg, 'assistant');
            }
        } catch (error) {
            console.error('モバイルAI質問エラー:', error);
            removeMobileTyping(typingId);
            addMobileMessage('通信エラーが発生しました。ネットワーク接続を確認してください。', 'assistant');
        } finally {
            mobileAiSend.disabled = false;
        }
    }
    
    // モバイルメッセージ追加関数
    function addMobileMessage(content, type) {
        if (!mobileAiMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `gus-ai-message gus-ai-message--${type}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.setAttribute('aria-hidden', 'true');
        
        if (type === 'assistant') {
            avatar.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>';
        } else {
            avatar.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        }
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'gus-ai-message-content';
        contentDiv.innerHTML = content.replace(/\n/g, '<br>');
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        mobileAiMessages.appendChild(messageDiv);
        
        // スクロール
        mobileAiMessages.scrollTop = mobileAiMessages.scrollHeight;
    }
    
    // モバイルタイピングインジケーター表示
    function showMobileTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'gus-ai-typing';
        typingDiv.id = 'mobileTyping';
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>';
        
        const dotsDiv = document.createElement('div');
        dotsDiv.className = 'gus-ai-typing-dots';
        dotsDiv.innerHTML = '<div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div>';
        
        typingDiv.appendChild(avatar);
        typingDiv.appendChild(dotsDiv);
        mobileAiMessages.appendChild(typingDiv);
        mobileAiMessages.scrollTop = mobileAiMessages.scrollHeight;
        
        return 'mobileTyping';
    }
    
    // モバイルタイピングインジケーター削除
    function removeMobileTyping(id) {
        const typing = document.getElementById(id);
        if (typing) {
            typing.remove();
        }
    }
    
    // モバイルイベントリスナー
    if (mobileAiSend && mobileAiInput) {
        mobileAiSend.addEventListener('click', sendMobileAiQuestion);
        
        mobileAiInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMobileAiQuestion();
            }
        });
        
        // 自動リサイズ
        mobileAiInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    
    // ========================================
    // 目次リンクのスムーススクロール
    // ========================================
    const tocLinks = document.querySelectorAll('.gus-toc-link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // アクティブ状態の更新
                tocLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    // ========================================
    // FAQアコーディオンのARIA属性管理
    // ========================================
    const faqItems = document.querySelectorAll('.gus-faq-item');
    faqItems.forEach(item => {
        const summary = item.querySelector('.gus-faq-question');
        if (summary) {
            item.addEventListener('toggle', function() {
                const isOpen = item.open;
                summary.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
            
            // キーボード操作対応
            summary.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.open = !item.open;
                }
            });
        }
    });
    
    // ========================================
    // PC AI Modal
    // ========================================
    const pcAiBtn = document.getElementById('pcAiBtn');
    const pcAiModal = document.getElementById('pcAiModal');
    const pcAiOverlay = document.getElementById('pcAiOverlay');
    const pcAiClose = document.getElementById('pcAiClose');
    const pcAiInput = document.getElementById('pcAiInput');
    const pcAiSend = document.getElementById('pcAiSend');
    const pcAiMessages = document.getElementById('pcAiMessages');
    const pcAiSuggestions = document.querySelectorAll('.gus-pc-ai-suggestion');
    
    // PC AI モーダルを開く
    if (pcAiBtn) {
        pcAiBtn.addEventListener('click', function() {
            console.log('PC AI ボタンがクリックされました');
            pcAiModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            if (pcAiInput) {
                setTimeout(() => pcAiInput.focus(), 100);
            }
        });
    }
    
    // PC AI モーダルを閉じる
    function closePcAiModal() {
        pcAiModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (pcAiClose) {
        pcAiClose.addEventListener('click', closePcAiModal);
    }
    
    if (pcAiOverlay) {
        pcAiOverlay.addEventListener('click', closePcAiModal);
    }
    
    // ESCキーで閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && pcAiModal && pcAiModal.classList.contains('active')) {
            closePcAiModal();
        }
    });
    
    // PC提案チップ機能
    pcAiSuggestions.forEach(chip => {
        chip.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            if (pcAiInput) {
                pcAiInput.value = question;
                pcAiInput.focus();
                // すぐに送信
                if (pcAiSend) {
                    pcAiSend.click();
                }
            }
        });
    });
    
    // PC AI質問送信機能（チャット形式）
    async function sendPcAiQuestion() {
        const question = pcAiInput.value.trim();
        
        if (!question) {
            return;
        }
        
        // ユーザーメッセージを追加
        addPcMessage(question, 'user');
        
        // 入力フィールドをクリア
        pcAiInput.value = '';
        pcAiInput.style.height = 'auto';
        
        // 送信ボタンを無効化
        pcAiSend.disabled = true;
        
        // タイピングインジケーターを表示
        const typingId = showPcTyping();
        
        try {
            const formData = new FormData();
            formData.append('action', 'handle_grant_ai_question');
            formData.append('nonce', '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>');
            formData.append('post_id', '<?php echo $post_id; ?>');
            formData.append('question', question);
            
            const response = await fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // タイピングインジケーターを削除
            removePcTyping(typingId);
            
            if (data.success && data.data && data.data.answer) {
                addPcMessage(data.data.answer, 'assistant');
            } else {
                let errorMsg = '申し訳ございません。回答の生成に失敗しました。';
                if (data.data && typeof data.data === 'string') {
                    errorMsg = data.data;
                } else if (data.data && data.data.message) {
                    errorMsg = data.data.message;
                }
                addPcMessage(errorMsg, 'assistant');
            }
        } catch (error) {
            console.error('PC AI質問エラー:', error);
            removePcTyping(typingId);
            addPcMessage('通信エラーが発生しました。ネットワーク接続を確認してください。', 'assistant');
        } finally {
            pcAiSend.disabled = false;
        }
    }
    
    // PCメッセージ追加関数
    function addPcMessage(content, type) {
        if (!pcAiMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `gus-ai-message gus-ai-message--${type}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.setAttribute('aria-hidden', 'true');
        
        if (type === 'assistant') {
            avatar.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>';
        } else {
            avatar.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        }
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'gus-ai-message-content';
        contentDiv.innerHTML = content.replace(/\n/g, '<br>');
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        pcAiMessages.appendChild(messageDiv);
        
        // スクロール
        pcAiMessages.scrollTop = pcAiMessages.scrollHeight;
    }
    
    // PCタイピングインジケーター表示
    function showPcTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'gus-ai-typing';
        typingDiv.id = 'pcTyping';
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>';
        
        const dotsDiv = document.createElement('div');
        dotsDiv.className = 'gus-ai-typing-dots';
        dotsDiv.innerHTML = '<div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div>';
        
        typingDiv.appendChild(avatar);
        typingDiv.appendChild(dotsDiv);
        pcAiMessages.appendChild(typingDiv);
        pcAiMessages.scrollTop = pcAiMessages.scrollHeight;
        
        return 'pcTyping';
    }
    
    // PCタイピングインジケーター削除
    function removePcTyping(id) {
        const typing = document.getElementById(id);
        if (typing) {
            typing.remove();
        }
    }
    
    // PCイベントリスナー
    if (pcAiSend && pcAiInput) {
        pcAiSend.addEventListener('click', sendPcAiQuestion);
        
        pcAiInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendPcAiQuestion();
            }
        });
        
        // 自動リサイズ
        pcAiInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    
    // ========================================
    // スクロール位置に応じた目次のアクティブ状態更新
    // ========================================
    const sections = document.querySelectorAll('.gus-section[id]');
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                tocLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, observerOptions);
    
    sections.forEach(section => observer.observe(section));
});
</script>

<?php get_footer(); ?>
