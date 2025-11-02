<?php
/**
 * Grant Card List Portal - Light Mode Only v2.1 FINAL
 * template-parts/grant-card-list-portal.php
 * 
 * ポータルサイト風1列リスト表示テンプレート
 * AIチャット機能完全動作保証 + ライトモード固定
 * 
 * @package Grant_Insight_Portal
 * @version 2.1.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// グローバル変数から必要データを取得
global $post;

$post_id = get_the_ID();
if (!$post_id) {
    return;
}

// 基本データ取得
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

// 抜粋が空の場合は本文から生成
if (empty($excerpt)) {
    $content = get_the_content($post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
}

// ACFフィールド取得
$grant_data = array(
    'organization' => get_field('organization', $post_id) ?: '',
    'organization_type' => get_field('organization_type', $post_id) ?: 'national',
    'max_amount' => get_field('max_amount', $post_id) ?: '',
    'max_amount_numeric' => intval(get_field('max_amount_numeric', $post_id)),
    'min_amount' => intval(get_field('min_amount', $post_id)),
    'deadline' => get_field('deadline', $post_id) ?: '',
    'deadline_date' => get_field('deadline_date', $post_id) ?: '',
    'application_status' => get_field('application_status', $post_id) ?: 'open',
    'application_period' => get_field('application_period', $post_id) ?: '',
    'grant_target' => get_field('grant_target', $post_id) ?: '',
    'eligible_expenses' => get_field('eligible_expenses', $post_id) ?: '',
    'eligible_expenses_detailed' => get_field('eligible_expenses_detailed', $post_id) ?: '',
    'grant_difficulty' => get_field('grant_difficulty', $post_id) ?: 'normal',
    'difficulty_level' => get_field('difficulty_level', $post_id) ?: '中級',
    'adoption_rate' => floatval(get_field('adoption_rate', $post_id)),
    'required_documents' => get_field('required_documents', $post_id) ?: '',
    'required_documents_detailed' => get_field('required_documents_detailed', $post_id) ?: '',
    'subsidy_rate_detailed' => get_field('subsidy_rate_detailed', $post_id) ?: '',
    'application_method' => get_field('application_method', $post_id) ?: 'online',
    'contact_info' => get_field('contact_info', $post_id) ?: '',
    'official_url' => get_field('official_url', $post_id) ?: '',
    'is_featured' => get_field('is_featured', $post_id) ?: false,
    'ai_summary' => get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true),
    'area_notes' => get_field('area_notes', $post_id) ?: '',
    'regional_limitation' => get_field('regional_limitation', $post_id) ?: 'nationwide',
);

extract($grant_data);

// タクソノミーデータ
$categories = get_the_terms($post_id, 'grant_category');
$prefectures = get_the_terms($post_id, 'grant_prefecture');

// メインカテゴリ
$main_category = '';
$main_category_slug = '';
if ($categories && !is_wp_error($categories)) {
    $main_category = $categories[0]->name;
    $main_category_slug = $categories[0]->slug;
}

// 地域表示（都道府県のみ）
$region_display = '全国';

if ($prefectures && !is_wp_error($prefectures)) {
    $prefecture_count = count($prefectures);
    
    if ($prefecture_count >= 47 || $prefecture_count >= 20) {
        $region_display = '全国';
    } elseif ($prefecture_count > 3) {
        $region_display = $prefecture_count . '都道府県';
    } elseif ($prefecture_count > 1) {
        $region_names = array_map(function($p) { return $p->name; }, array_slice($prefectures, 0, 2));
        $region_display = implode('・', $region_names);
        if ($prefecture_count > 2) {
            $region_display .= '他';
        }
    } else {
        $region_display = $prefectures[0]->name;
    }
}

// 金額フォーマット
$formatted_amount = '';
$amount_range = '';
if ($max_amount_numeric > 0) {
    if ($max_amount_numeric >= 100000000) {
        $formatted_amount = number_format($max_amount_numeric / 100000000, 1) . '億円';
    } elseif ($max_amount_numeric >= 10000) {
        $formatted_amount = number_format($max_amount_numeric / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_numeric) . '円';
    }
    
    if ($min_amount > 0) {
        $formatted_min = '';
        if ($min_amount >= 10000) {
            $formatted_min = number_format($min_amount / 10000) . '万円';
        } else {
            $formatted_min = number_format($min_amount) . '円';
        }
        $amount_range = $formatted_min . ' 〜 ' . $formatted_amount;
    } else {
        $amount_range = '上限 ' . $formatted_amount;
    }
} elseif ($max_amount) {
    $formatted_amount = $max_amount;
    $amount_range = $max_amount;
}

// ステータス表示
$status_config = array(
    'open' => array('label' => '募集中', 'class' => 'status-open'),
    'upcoming' => array('label' => '募集予定', 'class' => 'status-upcoming'),
    'closed' => array('label' => '募集終了', 'class' => 'status-closed'),
    'suspended' => array('label' => '一時停止', 'class' => 'status-suspended'),
);
$status_data = $status_config[$application_status] ?? $status_config['open'];

// 締切日情報
$deadline_info = array();
$days_remaining = 0;

if ($deadline_date) {
    $deadline_timestamp = strtotime($deadline_date);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
        
        if ($days_remaining <= 0) {
            $deadline_info = array('class' => 'deadline-expired', 'text' => '締切済', 'urgent' => true);
        } elseif ($days_remaining <= 7) {
            $deadline_info = array('class' => 'deadline-critical', 'text' => '残り'.$days_remaining.'日', 'urgent' => true);
        } elseif ($days_remaining <= 30) {
            $deadline_info = array('class' => 'deadline-warning', 'text' => '残り'.$days_remaining.'日', 'urgent' => false);
        } else {
            $deadline_info = array('class' => 'deadline-normal', 'text' => date('Y/m/d', $deadline_timestamp), 'urgent' => false);
        }
    }
} elseif ($deadline) {
    $deadline_info = array('class' => 'deadline-normal', 'text' => $deadline, 'urgent' => false);
}

// 申請方法ラベル
$method_labels = array(
    'online' => 'オンライン',
    'mail' => '郵送',
    'visit' => '窓口',
    'mixed' => 'オンライン・郵送',
);
$method_label = $method_labels[$application_method] ?? '詳細参照';

// 組織タイプ表示
$org_type_labels = array(
    'national' => '国',
    'prefecture' => '都道府県',
    'city' => '市区町村',
    'public_org' => '公的機関',
    'private_org' => '民間',
    'other' => 'その他',
);
$org_type_label = $org_type_labels[$organization_type] ?? '';

// レコメンド理由を生成
$recommend_reasons = array();

if ($is_featured) {
    $recommend_reasons[] = '注目の助成金';
}

if ($adoption_rate >= 70) {
    $recommend_reasons[] = '高採択率';
} elseif ($adoption_rate >= 50) {
    $recommend_reasons[] = '採択実績あり';
}

if ($days_remaining > 0 && $days_remaining <= 30) {
    $recommend_reasons[] = '締切間近';
}

if ($max_amount_numeric >= 10000000) {
    $recommend_reasons[] = '高額助成';
}

if ($grant_difficulty === 'easy') {
    $recommend_reasons[] = '申請しやすい';
}

if (empty($recommend_reasons)) {
    $recommend_reasons[] = '新着情報';
}
?>

<style>
/* ============================================
   🎨 Portal List Card Design v2.1 FINAL
   Light Mode Only - ダークモード完全無効化
============================================ */

.grant-card-list-portal {
    /* ライトモード固定カラー */
    --portal-primary: #000000;
    --portal-secondary: #333333;
    --portal-accent: #FFEB3B;
    --portal-bg: #FFFFFF;
    --portal-surface: #F8F8F8;
    --portal-border: #E5E5E5;
    --portal-text: #1A1A1A;
    --portal-text-muted: #666666;
    --portal-text-light: #999999;
    --portal-success: #10B981;
    --portal-warning: #F59E0B;
    --portal-danger: #EF4444;
    --portal-info: #3B82F6;
    
    /* AI Modal colors - ライトモード固定 */
    --ai-modal-bg: #FFFFFF;
    --ai-modal-overlay: rgba(0, 0, 0, 0.75);
    --ai-modal-header-bg: #000000;
    --ai-modal-header-text: #FFFFFF;
    --ai-modal-border: #E5E5E5;
    --ai-modal-body-bg: #FAFAFA;
    --ai-message-assistant-bg: #F8F8F8;
    --ai-message-assistant-text: #1A1A1A;
    --ai-message-user-bg: #000000;
    --ai-message-user-text: #FFFFFF;
    --ai-input-bg: #F8F8F8;
    --ai-input-focus-bg: #FFFFFF;
    --ai-input-text: #1A1A1A;
    --ai-button-bg: #FFEB3B;
    --ai-button-text: #000000;
    --ai-button-hover-bg: #000000;
    --ai-button-hover-text: #FFFFFF;
    
    /* ダークモード強制無効化 */
    color-scheme: light !important;
}

/* ダークモードメディアクエリを無効化 */
@media (prefers-color-scheme: dark) {
    .grant-card-list-portal,
    .portal-ai-modal,
    .portal-ai-modal-container,
    .portal-ai-modal-header,
    .portal-ai-modal-body,
    .portal-ai-chat-messages,
    .portal-ai-message-content {
        /* 強制的にライトモードカラーを適用 */
        color-scheme: light !important;
        background: var(--portal-bg) !important;
        color: var(--portal-text) !important;
    }
}

.grant-card-list-portal {
    background: var(--portal-bg);
    border: 2px solid var(--portal-border);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    gap: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    isolation: isolate;
}

.grant-card-list-portal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--portal-accent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.grant-card-list-portal:hover {
    border-color: var(--portal-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.grant-card-list-portal:hover::before {
    opacity: 1;
}

/* ===== 左サイド：メイン情報 ===== */
.card-main-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ヘッダー */
.card-header-portal {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.card-badges-portal {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 6px;
}

.badge-featured {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000000;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(255, 165, 0, 0.3);
}

.badge-category {
    background: var(--portal-surface);
    color: var(--portal-text);
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid var(--portal-border);
}

.badge-status {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid;
}

.status-open {
    background: #ECFDF5;
    color: #059669;
    border-color: #059669;
}

.status-upcoming {
    background: #EFF6FF;
    color: #2563EB;
    border-color: #2563EB;
}

.status-closed {
    background: #F3F4F6;
    color: #6B7280;
    border-color: #9CA3AF;
}

/* タイトル */
.card-title-portal {
    font-size: 17px;
    font-weight: 700;
    line-height: 1.4;
    margin: 0 0 10px 0;
    color: var(--portal-text);
}

.card-title-portal a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
    pointer-events: auto;
    z-index: 1;
    position: relative;
}

.card-title-portal a:hover {
    color: var(--portal-info);
}

/* 地域情報 */
.card-region-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--portal-text-muted);
    margin-bottom: 10px;
}

.region-icon {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
}

.region-main {
    font-weight: 600;
    color: var(--portal-text);
}

/* AI要約 */
.card-ai-summary-portal {
    background: linear-gradient(135deg, #FFFBEA 0%, #FFF9E6 100%);
    border: 2px solid var(--portal-accent);
    border-radius: 6px;
    padding: 12px;
    position: relative;
    margin-bottom: 12px;
}

.card-ai-summary-portal::before {
    content: 'AI要約';
    position: absolute;
    top: -9px;
    left: 10px;
    background: var(--portal-accent);
    color: var(--portal-primary);
    padding: 2px 8px;
    font-size: 9px;
    font-weight: 800;
    border-radius: 3px;
    letter-spacing: 0.5px;
}

.ai-summary-text {
    font-size: 12px;
    line-height: 1.6;
    color: var(--portal-secondary);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 詳細情報グリッド */
.card-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    padding: 12px;
    background: var(--portal-surface);
    border-radius: 6px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.detail-item-icon {
    width: 13px;
    height: 13px;
    stroke: var(--portal-text-muted);
    stroke-width: 2;
    margin-bottom: 2px;
}

.detail-label {
    font-size: 10px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.detail-value {
    font-size: 13px;
    color: var(--portal-text);
    font-weight: 600;
}

/* 実施機関情報 */
.card-organization-detail {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    flex-wrap: wrap;
}

.org-type-badge {
    background: var(--portal-primary);
    color: var(--portal-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 600;
}

/* 抜粋テキスト */
.card-excerpt-portal {
    padding: 12px;
    background: var(--portal-bg);
    border: 1px solid var(--portal-border);
    border-radius: 6px;
    margin-top: 12px;
}

.excerpt-label {
    font-size: 10px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.excerpt-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.excerpt-text {
    font-size: 13px;
    line-height: 1.6;
    color: var(--portal-text);
    margin: 0;
}

/* タグリスト */
.card-tags-portal {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.tag-item {
    background: var(--portal-bg);
    border: 1px solid var(--portal-border);
    color: var(--portal-text-muted);
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 3px;
}

.tag-icon {
    width: 11px;
    height: 11px;
    stroke: currentColor;
    stroke-width: 2;
}

.tag-item:hover {
    background: var(--portal-primary);
    color: var(--portal-bg);
    border-color: var(--portal-primary);
}

/* ===== 右サイド：アクション＆メタ情報 ===== */
.card-side-section {
    width: 240px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-shrink: 0;
}

/* 金額表示 */
.card-amount-box {
    background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
    border: 2px solid #10B981;
    border-radius: 6px;
    padding: 12px;
    text-align: center;
}

.amount-icon {
    width: 20px;
    height: 20px;
    stroke: #059669;
    stroke-width: 2;
    margin: 0 auto 6px;
}

.amount-label {
    font-size: 10px;
    color: #065F46;
    font-weight: 600;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.amount-value {
    font-size: 20px;
    font-weight: 800;
    color: #059669;
    line-height: 1.2;
}

.amount-range {
    font-size: 11px;
    color: #047857;
    margin-top: 3px;
}

/* 締切表示 */
.card-deadline-box {
    background: var(--portal-surface);
    border: 2px solid var(--portal-border);
    border-radius: 6px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.deadline-icon {
    width: 20px;
    height: 20px;
    stroke: var(--portal-text-muted);
    stroke-width: 2;
    flex-shrink: 0;
}

.deadline-content {
    flex: 1;
}

.deadline-label {
    font-size: 9px;
    color: var(--portal-text-light);
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.deadline-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--portal-text);
}

.deadline-critical {
    border-color: var(--portal-danger);
    background: #FEF2F2;
}

.deadline-critical .deadline-icon {
    stroke: var(--portal-danger);
}

.deadline-critical .deadline-value {
    color: var(--portal-danger);
}

.deadline-warning {
    border-color: var(--portal-warning);
    background: #FFFBEB;
}

.deadline-warning .deadline-icon {
    stroke: var(--portal-warning);
}

.deadline-warning .deadline-value {
    color: var(--portal-warning);
}

/* 採択率バー */
.adoption-rate-display {
    background: var(--portal-surface);
    border-radius: 6px;
    padding: 10px;
}

.rate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.rate-label {
    font-size: 10px;
    color: var(--portal-text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 3px;
}

.rate-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.rate-percentage {
    font-size: 14px;
    font-weight: 700;
    color: var(--portal-success);
}

.rate-bar-container {
    height: 6px;
    background: var(--portal-border);
    border-radius: 3px;
    overflow: hidden;
}

.rate-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10B981, #34D399);
    transition: width 1s ease-out;
    border-radius: 3px;
}

/* レコメンド表示 */
.card-recommend-box {
    background: var(--portal-surface);
    border: 2px solid var(--portal-border);
    border-radius: 6px;
    padding: 10px;
}

.recommend-label {
    font-size: 9px;
    color: var(--portal-text-light);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.recommend-icon {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
}

.recommend-reasons {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.recommend-reason {
    font-size: 11px;
    color: var(--portal-text);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.recommend-reason::before {
    content: '✓';
    font-size: 10px;
    font-weight: 700;
    color: var(--portal-success);
}

/* アクションボタン - コンパクト版 */
.card-actions-portal {
    display: flex;
    flex-direction: column;
    gap: 0px;
}

.btn-portal {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    border: 2px solid;
    pointer-events: auto !important;
    position: relative;
    z-index: 100;
    touch-action: manipulation;
}

.btn-icon {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    pointer-events: none; /* アイコンだけpointer-eventsを無効化 */
}

/* テキストノードには影響しないため、安全 */

.btn-primary {
    background: var(--portal-primary);
    color: var(--portal-bg);
    border-color: var(--portal-primary);
}

.btn-primary:hover {
    background: var(--portal-secondary);
    border-color: var(--portal-secondary);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}


/* ===== モバイル最適化 ===== */
@media (max-width: 1024px) {
    .grant-card-list-portal {
        flex-direction: column;
    }
    
    .card-side-section {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .card-actions-portal {
        grid-column: 1 / -1;
        display: flex;
        gap: 0px;
    }
    
    .card-details-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .grant-card-list-portal {
        padding: 16px;
        gap: 14px;
    }
    
    .card-title-portal {
        font-size: 16px;
    }
    
    .card-side-section {
        grid-template-columns: 1fr;
    }
    
    .card-actions-portal {
        display: flex;
    }
    
    .card-details-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .amount-value {
        font-size: 18px;
    }
    
    /* スマホでコンパクト化：難易度、最大助成額、採択率、おすすめポイントを非表示 */
    .card-amount-box,
    .adoption-rate-display,
    .card-recommend-box {
        display: none !important;
    }
    
    /* 難易度タグを非表示 */
    .card-tags-portal .tag-item:last-child {
        display: none !important;
    }
    
    /* スマホでのタッチ対応を強化 */
    .btn-portal {
        min-height: 48px;
        -webkit-tap-highlight-color: rgba(255, 235, 59, 0.3);
        user-select: none;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }
    
    .btn-ai {
        z-index: 150 !important;
        isolation: isolate;
    }
}

@media (max-width: 480px) {
    .grant-card-list-portal {
        padding: 12px;
        gap: 12px;
    }
    
    .card-title-portal {
        font-size: 15px;
    }
    
    .card-badges-portal {
        gap: 4px;
    }
    
    .badge-featured,
    .badge-category,
    .badge-status {
        font-size: 9px;
        padding: 2px 6px;
    }
    
    .card-details-grid {
        padding: 10px;
        gap: 8px;
    }
    
    .detail-label {
        font-size: 9px;
    }
    
    .detail-value {
        font-size: 12px;
    }
    
    .excerpt-text {
        font-size: 12px;
    }
    
    .btn-portal {
        padding: 12px 16px;
        font-size: 13px;
        min-height: 52px;
        font-weight: 700;
    }
    
    .btn-ai {
        background: var(--portal-accent) !important;
        color: var(--portal-primary) !important;
        border-color: var(--portal-accent) !important;
        z-index: 200 !important;
    }
    
    .btn-ai:active {
        transform: scale(0.95);
        background: var(--portal-primary) !important;
        color: var(--portal-accent) !important;
    }
}

</style>

<article class="grant-card-list-portal" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         itemscope 
         itemtype="https://schema.org/GovernmentService"
         role="article"
         aria-label="<?php echo esc_attr($title); ?>">
    
    <!-- 左サイド：メイン情報 -->
    <div class="card-main-section">
        
        <!-- ヘッダー -->
        <div class="card-header-portal">
            <div style="flex: 1;">
                <!-- バッジ -->
                <div class="card-badges-portal">
                    <?php if ($is_featured): ?>
                        <span class="badge-featured">注目</span>
                    <?php endif; ?>
                    
                    <?php if ($main_category): ?>
                        <span class="badge-category" itemprop="category"><?php echo esc_html($main_category); ?></span>
                    <?php endif; ?>
                    
                    <span class="badge-status <?php echo esc_attr($status_data['class']); ?>">
                        <?php echo esc_html($status_data['label']); ?>
                    </span>
                </div>
                
                <!-- タイトル -->
                <h3 class="card-title-portal" itemprop="name">
                    <a href="<?php echo esc_url($permalink); ?>" 
                       itemprop="url"
                       aria-label="<?php echo esc_attr($title); ?>の詳細">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>
                
                <!-- 地域情報 -->
                <div class="card-region-info" itemprop="areaServed">
                    <svg class="region-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="region-main"><?php echo esc_html($region_display); ?></span>
                </div>
            </div>
        </div>
        
        <!-- AI要約 -->
        <?php if ($ai_summary): ?>
        <div class="card-ai-summary-portal">
            <p class="ai-summary-text" itemprop="description"><?php echo esc_html($ai_summary); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- 詳細情報グリッド -->
        <div class="card-details-grid">
            
            <!-- 対象者 -->
            <?php if ($grant_target): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    対象者
                </span>
                <span class="detail-value" itemprop="audience">
                    <?php echo wp_trim_words(strip_tags($grant_target), 10, '...'); ?>
                </span>
            </div>
            <?php endif; ?>
            
            <!-- 実施機関 -->
            <?php if ($organization): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M7 8h10M7 12h7M7 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    実施機関
                </span>
                <div class="detail-value card-organization-detail" itemprop="provider" itemscope itemtype="https://schema.org/Organization">
                    <?php if ($org_type_label): ?>
                        <span class="org-type-badge"><?php echo esc_html($org_type_label); ?></span>
                    <?php endif; ?>
                    <span itemprop="name"><?php echo esc_html($organization); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 補助率 -->
            <?php if ($subsidy_rate_detailed): ?>
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    補助率
                </span>
                <span class="detail-value"><?php echo esc_html($subsidy_rate_detailed); ?></span>
            </div>
            <?php endif; ?>
            
            <!-- 申請方法 -->
            <div class="detail-item">
                <span class="detail-label">
                    <svg class="detail-item-icon" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    申請方法
                </span>
                <span class="detail-value"><?php echo esc_html($method_label); ?></span>
            </div>
            
        </div>
        
        <!-- 抜粋テキスト（SEO対策） -->
        <?php if ($excerpt): ?>
        <div class="card-excerpt-portal">
            <div class="excerpt-label">
                <svg class="excerpt-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6h16M4 12h16M4 18h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                概要
            </div>
            <p class="excerpt-text" itemprop="abstract"><?php echo esc_html($excerpt); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- タグ -->
        <?php if ($eligible_expenses || $required_documents || $difficulty_level): ?>
        <div class="card-tags-portal">
            <?php if ($eligible_expenses): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    対象経費あり
                </span>
            <?php endif; ?>
            <?php if ($required_documents): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                        <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    必要書類あり
                </span>
            <?php endif; ?>
            <?php if ($difficulty_level): ?>
                <span class="tag-item">
                    <svg class="tag-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    難易度: <?php echo esc_html($difficulty_level); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    </div>
    
    <!-- 右サイド：アクション＆メタ情報 -->
    <div class="card-side-section">
        
        <!-- 金額表示 -->
        <?php if ($formatted_amount): ?>
        <div class="card-amount-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <svg class="amount-icon" viewBox="0 0 24 24" fill="none">
                <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="amount-label">最大助成額</div>
            <div class="amount-value" itemprop="price" content="<?php echo esc_attr($max_amount_numeric); ?>">
                <?php echo esc_html($formatted_amount); ?>
            </div>
            <meta itemprop="priceCurrency" content="JPY">
            <?php if ($amount_range && $amount_range !== $formatted_amount): ?>
                <div class="amount-range"><?php echo esc_html($amount_range); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- 締切表示 -->
        <?php if (!empty($deadline_info)): ?>
        <div class="card-deadline-box <?php echo esc_attr($deadline_info['class']); ?>">
            <svg class="deadline-icon" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="deadline-content">
                <div class="deadline-label">締切</div>
                <div class="deadline-value" itemprop="validThrough" content="<?php echo esc_attr($deadline_date); ?>">
                    <?php echo esc_html($deadline_info['text']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 採択率 -->
        <?php if ($adoption_rate > 0): ?>
        <div class="adoption-rate-display">
            <div class="rate-header">
                <span class="rate-label">
                    <svg class="rate-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    採択率
                </span>
                <span class="rate-percentage"><?php echo esc_html($adoption_rate); ?>%</span>
            </div>
            <div class="rate-bar-container">
                <div class="rate-bar-fill" style="width: <?php echo esc_attr($adoption_rate); ?>%"></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- レコメンド表示 -->
        <?php if (!empty($recommend_reasons)): ?>
        <div class="card-recommend-box">
            <div class="recommend-label">
                <svg class="recommend-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
                おすすめポイント
            </div>
            <div class="recommend-reasons">
                <?php foreach (array_slice($recommend_reasons, 0, 3) as $reason): ?>
                    <span class="recommend-reason"><?php echo esc_html($reason); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- アクションボタン -->
        <div class="card-actions-portal">
            
            <a href="<?php echo esc_url($permalink); ?>" class="btn-portal btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                詳細を見る
            </a>
            
        </div>
        
    </div>
    
</article>
