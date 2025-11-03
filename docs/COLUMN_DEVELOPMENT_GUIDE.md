# WordPress「keishi12」テーマ コラム機能開発 完全指示書
**Version: 2.1 (実際のカラースキーム反映版)**  
**作成日: 2025年11月2日**  
**最終更新: 2025年11月2日**

---

## 🎯 プロジェクト概要

### 目的
既存WordPressテーマ「keishi12」（Grant Insight Perfect）にコラム機能を追加

### サイト情報
- **URL**: https://joseikin-insight.com
- **テーマ名**: Grant Insight Perfect - Tailwind CSS Build Edition
- **バージョン**: 5.2.0-tailwind-build

### 開発方針
1. ✅ **既存のデザイン・色使いを完全踏襲**（緑系ブランドカラー）
2. ✅ **ファイル数を最小限に**（保守性重視）
3. ✅ **Yahoo! JAPANのレイアウト構造のみ参考**
4. ✅ **妥協なし・完全実装**（機能不完全は許容しない）
5. ✅ **Tailwind CSS活用**（既存のビルド版を使用）

---

## 🎨 デザイン仕様

### カラースキーム（既存サイトから抽出 - 正確版）

```css
/* ===================================
   プライマリカラー（緑系） - 既存テーマのメインカラー
   =================================== */
--color-primary: #059669;           /* メイングリーン（ブランドカラー） */
--color-primary-dark: #047857;      /* 濃いグリーン（hover時など） */
--color-primary-darker: #065f46;    /* より濃いグリーン */
--color-primary-light: #10b981;     /* 明るいグリーン */

/* セカンダリカラー（黒系） */
--color-secondary: #000000;         /* 純黒 */
--color-secondary-light: #1a1a1a;   /* ダークグレー */

/* アクセントカラー（黄色） */
--color-accent: #ffeb3b;            /* アクセントイエロー */
--color-accent-light: #fff9c4;      /* 薄いイエロー */
--color-accent-dark: #fbc02d;       /* 濃いイエロー */

/* グレースケール */
--color-gray-50: #f9fafb;           /* 最も薄いグレー */
--color-gray-100: #f3f4f6;          /* 薄いグレー */
--color-gray-200: #e5e7eb;          /* ボーダー用グレー */
--color-gray-300: #d1d5db;          /* 中間グレー */
--color-gray-400: #6b7280;          /* サブテキスト用 */
--color-gray-500: #6b7280;
--color-gray-600: #4b5563;
--color-gray-700: #374151;
--color-gray-800: #1f2937;
--color-gray-900: #111827;          /* 濃いグレー（見出し用）*/

/* 状態カラー */
--color-success: #10b981;           /* 成功メッセージ */
--color-warning: #f59e0b;           /* 警告メッセージ */
--color-error: #ef4444;             /* エラーメッセージ */
--color-info: #3b82f6;              /* 情報メッセージ */

/* 背景カラー */
--color-bg-primary: #ffffff;        /* メイン背景（白） */
--color-bg-secondary: #f9fafb;      /* セクション背景 */
--color-bg-tertiary: #f3f4f6;       /* カード背景 */

/* テキストカラー */
--color-text-primary: #111827;      /* メインテキスト */
--color-text-secondary: #374151;    /* サブテキスト */
--color-text-tertiary: #4b5563;     /* 補助テキスト */
--color-text-muted: #6b7280;        /* 薄いテキスト */

/* ボーダーカラー */
--color-border-light: #e5e7eb;      /* 薄いボーダー */
--color-border-medium: #d1d5db;     /* 中間ボーダー */
--color-border-dark: #6b7280;       /* 濃いボーダー */
```

### Tailwindクラス対応表

| 用途 | Tailwindクラス | 色 |
|------|---------------|-----|
| **プライマリボタン** | `bg-primary hover:bg-primary-dark` | 緑系 |
| **セカンダリボタン** | `bg-gray-100 hover:bg-gray-200` | グレー系 |
| **アクセント** | `bg-accent text-gray-900` | 黄色 |
| **見出し** | `text-gray-900` | #111827 |
| **本文** | `text-gray-700` | #374151 |
| **サブテキスト** | `text-gray-600` | #4b5563 |
| **薄いテキスト** | `text-gray-500` | #6b7280 |
| **ボーダー** | `border-gray-200` | #e5e7eb |
| **背景（薄）** | `bg-gray-50` | #f9fafb |
| **カード背景** | `bg-white` | #ffffff |

### フォント設定

```javascript
// tailwind.config.js より
fontFamily: {
  'inter': ['Inter', 'sans-serif'],
  'outfit': ['Outfit', 'sans-serif'],
  'space': ['Space Grotesk', 'sans-serif'],
  'noto': ['Noto Sans JP', 'sans-serif'],  // 日本語用
}

// 基本使用: font-noto（日本語コンテンツ）
```

### タイポグラフィ

```javascript
// 見出しサイズ（Tailwind config）
fontSize: {
  'h1': 'clamp(28px, 4vw, 36px)',
  'h2': 'clamp(22px, 3vw, 28px)',
  'h3': 'clamp(18px, 2.5vw, 22px)',
  'h4': '18px',
  'h5': '16px',
  'h6': '14px',
}

// 基本テキスト
--font-size-base: 16px;
--font-size-small: 14px;
--font-size-xs: 13px;
--font-size-xxs: 12px;
```

### スペーシング（8pxグリッド）

```javascript
spacing: {
  '1': '4px',
  '2': '8px',
  '3': '12px',
  '4': '16px',
  '5': '20px',
  '6': '24px',
  '8': '32px',
  '10': '40px',
  '12': '48px',
  '16': '64px',
  '20': '80px',
  '24': '96px',
}
```

### ボーダー・角丸

```javascript
borderRadius: {
  'sm': '4px',
  'DEFAULT': '8px',
  'md': '12px',
  'lg': '16px',
  'xl': '20px',
  '2xl': '24px',
}
```

### シャドウ

```javascript
boxShadow: {
  'sm': '0 1px 2px rgba(0, 0, 0, 0.05)',
  'DEFAULT': '0 2px 8px rgba(0, 0, 0, 0.08)',
  'md': '0 4px 12px rgba(0, 0, 0, 0.1)',
  'lg': '0 8px 24px rgba(0, 0, 0, 0.12)',
}
```

### レイアウト

- **PC**: メイン70% + サイドバー30% (Yahoo!風2カラム)
- **タブレット**: メイン65% + サイドバー35%
- **モバイル**: 1カラム（サイドバーは下部）
- **最大幅**: 1280px（既存サイトと統一）
- **余白**: 既存のTailwindスペーシングを使用

---

## 📁 最小ファイル構成

```
keishi12/
├── inc/
│   └── column-system.php              ★ 全機能統合ファイル ★
│                                      (投稿タイプ、タクソノミー、ACF、Ajax、承認、Analytics)
│
├── template-parts/
│   └── column/
│       ├── zone.php                   コラムゾーン本体（トップページ用）
│       ├── card.php                   記事カード（共通パーツ）
│       └── sidebar.php                サイドバー（共通パーツ）
│
├── single-column.php                  記事詳細ページ
├── archive-column.php                 記事一覧ページ
│
├── assets/css/
│   └── column.css                     コラム専用CSS（Tailwindで不足する部分のみ）
│
└── assets/js/
    └── column.js                      コラム専用JS（Ajax、タブ切替、インタラクション）
```

### 修正する既存ファイル

1. **functions.php** - 1行追加
   ```php
   require_once get_template_directory() . '/inc/column-system.php';
   ```

2. **front-page.php** - コラムゾーン呼び出し追加
   ```php
   <section class="front-page-section section-animate" id="column-section">
       <?php get_template_part('template-parts/column/zone'); ?>
   </section>
   ```

---

## 🚀 実装フェーズ

### Phase 1: コア機能（最優先 - 今回実装）

#### 1. column-system.php（統合管理ファイル）

```php
<?php
/**
 * Column System - Complete Integration File
 * コラム機能の全機能を統合管理
 * 
 * 含まれる機能:
 * - カスタム投稿タイプ「column」登録
 * - タクソノミー（カテゴリ・タグ）登録
 * - ACFフィールド定義
 * - 補助金連携関数
 * - Ajax処理ハンドラー
 * - 承認システム
 * - Analytics機能
 */

// 以下、全機能を実装...
```

**実装内容**:
- カスタム投稿タイプ `column` 登録
- タクソノミー `column_category` (階層あり)
- タクソノミー `column_tag` (階層なし)
- ACFフィールドグループ定義
- 補助金自動連携関数
- Ajax処理ハンドラー
- 承認ワークフロー
- PV計測・ランキング生成

#### 2. zone.php（コラムゾーン）

**レイアウト構造**:
```html
<div class="column-zone bg-white py-12">
  <div class="container mx-auto px-4 max-w-7xl">
    
    <!-- セクションヘッダー -->
    <div class="section-header mb-8">
      <h2 class="text-h2 font-bold text-gray-900">補助金コラム</h2>
      <p class="text-gray-600 mt-2">補助金活用のヒントやノウハウをお届け</p>
    </div>
    
    <!-- タブナビゲーション -->
    <div class="tab-navigation mb-6">
      <ul class="flex space-x-4 border-b border-gray-200">
        <li><a href="#" data-category="all" class="tab-link active">すべて</a></li>
        <li><a href="#" data-category="tips" class="tab-link">申請のコツ</a></li>
        <li><a href="#" data-category="system" class="tab-link">制度解説</a></li>
        <li><a href="#" data-category="trend" class="tab-link">動向</a></li>
        <li><a href="#" data-category="success" class="tab-link">成功事例</a></li>
      </ul>
    </div>
    
    <div class="grid grid-cols-12 gap-6">
      <!-- メインコンテンツ（PC: 70%, Tablet: 65%） -->
      <div class="col-span-12 lg:col-span-8">
        
        <!-- 特集記事エリア -->
        <div class="featured-articles mb-8">
          <!-- 大きな記事カード×1 -->
        </div>
        
        <!-- 記事一覧グリッド -->
        <div class="article-grid grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- 記事カード×6 -->
        </div>
        
        <!-- もっと見るボタン -->
        <div class="text-center mt-8">
          <a href="/column/" class="btn-primary">もっと見る</a>
        </div>
        
      </div>
      
      <!-- サイドバー（PC: 30%, Tablet: 35%） -->
      <aside class="col-span-12 lg:col-span-4">
        <?php get_template_part('template-parts/column/sidebar'); ?>
      </aside>
      
    </div>
  </div>
</div>
```

#### 3. card.php（記事カード）

**カードデザイン**:
```html
<article class="column-card bg-white rounded-lg shadow hover:shadow-md transition-all duration-200">
  <!-- アイキャッチ画像 -->
  <div class="card-thumbnail">
    <a href="<?php the_permalink(); ?>">
      <img src="<?php echo get_the_post_thumbnail_url(); ?>" 
           alt="<?php the_title(); ?>"
           class="w-full h-48 object-cover rounded-t-lg">
    </a>
    <!-- カテゴリバッジ -->
    <span class="category-badge bg-primary text-white">申請のコツ</span>
  </div>
  
  <!-- カード本文 -->
  <div class="card-body p-4">
    <!-- タイトル -->
    <h3 class="text-lg font-bold text-gray-900 mb-2">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    
    <!-- 要約 -->
    <p class="text-sm text-gray-600 mb-3">
      <?php echo wp_trim_words(get_the_excerpt(), 50); ?>
    </p>
    
    <!-- メタ情報 -->
    <div class="card-meta flex items-center justify-between text-xs text-gray-500">
      <div class="flex items-center space-x-3">
        <span>📅 <?php echo get_the_date('Y.m.d'); ?></span>
        <span>⏱️ <?php echo get_field('estimated_read_time'); ?>分</span>
      </div>
      <span>👁️ <?php echo get_field('view_count'); ?></span>
    </div>
    
    <!-- 関連補助金リンク -->
    <?php if (get_field('related_grants')): ?>
    <div class="related-grants mt-3 pt-3 border-t border-gray-200">
      <span class="text-xs text-gray-500">関連補助金:</span>
      <a href="<?php echo get_permalink(get_field('related_grants')[0]); ?>" 
         class="text-xs text-primary hover:underline ml-2">
        事業再構築補助金
      </a>
    </div>
    <?php endif; ?>
  </div>
</article>
```

#### 4. sidebar.php（サイドバー）

**サイドバーコンポーネント**:
```html
<div class="column-sidebar space-y-6">
  
  <!-- 人気記事ランキング -->
  <div class="sidebar-widget bg-gray-50 rounded-lg p-4">
    <h3 class="widget-title text-lg font-bold text-gray-900 mb-4 flex items-center">
      🔥 人気記事ランキング
    </h3>
    <ol class="space-y-3">
      <?php for ($i = 1; $i <= 5; $i++): ?>
      <li class="flex items-start">
        <span class="rank-number bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3"><?php echo $i; ?></span>
        <div class="flex-1">
          <a href="#" class="text-sm font-medium text-gray-900 hover:text-primary line-clamp-2">
            補助金申請で失敗しないための5つのポイント
          </a>
          <div class="text-xs text-gray-500 mt-1">
            👁️ 1,234 views
          </div>
        </div>
      </li>
      <?php endfor; ?>
    </ol>
  </div>
  
  <!-- 注目キーワード -->
  <div class="sidebar-widget bg-gray-50 rounded-lg p-4">
    <h3 class="widget-title text-lg font-bold text-gray-900 mb-4">
      🏷️ 注目キーワード
    </h3>
    <div class="tag-cloud flex flex-wrap gap-2">
      <?php 
      $tags = ['事業再構築', 'IT導入', 'DX推進', '設備投資', '人材育成'];
      foreach ($tags as $tag): 
      ?>
      <a href="#" class="tag-item bg-white border border-gray-200 rounded-full px-3 py-1 text-sm text-gray-700 hover:bg-primary hover:text-white hover:border-primary transition-colors">
        #<?php echo $tag; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- 関連補助金 -->
  <div class="sidebar-widget bg-gray-50 rounded-lg p-4">
    <h3 class="widget-title text-lg font-bold text-gray-900 mb-4">
      💰 関連補助金
    </h3>
    
    <!-- タブ -->
    <div class="grant-tabs flex border-b border-gray-200 mb-4">
      <button class="tab-btn active flex-1 py-2 text-sm">締切間近</button>
      <button class="tab-btn flex-1 py-2 text-sm">新着</button>
      <button class="tab-btn flex-1 py-2 text-sm">人気</button>
    </div>
    
    <!-- 補助金リスト -->
    <ul class="space-y-3">
      <?php for ($i = 1; $i <= 3; $i++): ?>
      <li class="border-b border-gray-200 pb-3 last:border-0">
        <a href="#" class="block hover:bg-white rounded p-2 -m-2 transition-colors">
          <div class="text-sm font-medium text-gray-900 mb-1">
            事業再構築補助金 第12回
          </div>
          <div class="text-xs text-gray-500 flex items-center space-x-2">
            <span>📅 締切: 2025/12/31</span>
            <span class="text-error">残り15日</span>
          </div>
        </a>
      </li>
      <?php endfor; ?>
    </ul>
  </div>
  
</div>
```

#### 5. single-column.php（記事詳細ページ）

**ページ構造**:
```html
<article class="column-single max-w-4xl mx-auto px-4 py-12">
  
  <!-- パンくずリスト -->
  <nav class="breadcrumb text-sm text-gray-500 mb-6">
    ホーム > コラム > 申請のコツ > 記事タイトル
  </nav>
  
  <!-- ヘッダー -->
  <header class="article-header mb-8">
    <div class="category-badge bg-primary text-white inline-block mb-3">
      申請のコツ
    </div>
    <h1 class="text-h1 font-bold text-gray-900 mb-4">
      <?php the_title(); ?>
    </h1>
    <div class="article-meta flex items-center space-x-4 text-gray-600">
      <span>📅 <?php echo get_the_date('Y年m月d日'); ?></span>
      <span>⏱️ <?php echo get_field('estimated_read_time'); ?>分</span>
      <span>👁️ <?php echo get_field('view_count'); ?>回閲覧</span>
    </div>
  </header>
  
  <!-- アイキャッチ画像 -->
  <div class="article-thumbnail mb-8">
    <img src="<?php echo get_the_post_thumbnail_url(); ?>" 
         alt="<?php the_title(); ?>"
         class="w-full h-auto rounded-lg">
  </div>
  
  <!-- 記事本文 -->
  <div class="article-content prose prose-lg max-w-none">
    <?php the_content(); ?>
  </div>
  
  <!-- 関連補助金ウィジェット -->
  <?php if (get_field('related_grants')): ?>
  <div class="related-grants-widget bg-gray-50 rounded-lg p-6 mt-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">
      💰 この記事に関連する補助金
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- 補助金カード -->
    </div>
  </div>
  <?php endif; ?>
  
  <!-- 関連記事 -->
  <div class="related-articles mt-12">
    <h3 class="text-xl font-bold text-gray-900 mb-6">
      📚 関連記事
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- 記事カード -->
    </div>
  </div>
  
</article>
```

#### 6. column.css（最小限のカスタムCSS）

```css
/**
 * Column System Custom Styles
 * Tailwindで対応できない部分のみ
 */

/* タブナビゲーション */
.tab-link {
    @apply px-4 py-2 text-sm font-medium text-gray-600 border-b-2 border-transparent transition-colors;
}

.tab-link:hover {
    @apply text-gray-900 border-gray-300;
}

.tab-link.active {
    @apply text-primary border-primary;
}

/* カードホバーエフェクト */
.column-card {
    @apply transition-transform duration-200;
}

.column-card:hover {
    @apply -translate-y-1;
}

/* カテゴリバッジ */
.category-badge {
    @apply absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium;
}

/* ランクナンバー */
.rank-number {
    min-width: 24px;
}

/* 記事本文スタイル（prose拡張） */
.article-content {
    @apply text-gray-700 leading-relaxed;
}

.article-content h2 {
    @apply text-h2 font-bold text-gray-900 mt-8 mb-4;
}

.article-content h3 {
    @apply text-h3 font-bold text-gray-900 mt-6 mb-3;
}

.article-content p {
    @apply mb-4;
}

.article-content ul,
.article-content ol {
    @apply mb-4 pl-6;
}

.article-content li {
    @apply mb-2;
}

.article-content a {
    @apply text-primary underline hover:text-primary-dark;
}
```

### Phase 2: 高度な機能（次回実装）

- archive-column.php（カテゴリ・タグアーカイブ）
- column.js（Ajax読み込み、無限スクロール）
- 補助金自動連携（AIマッチング）
- 検索機能（コラム内検索）

### Phase 3: 管理・分析（最終実装）

- 承認ワークフロー画面
- Analytics ダッシュボード
- PV計測・ランキング自動更新
- メール通知機能

---

## 💾 データベース設計

### カスタム投稿タイプ: column

```php
'post_type' => 'column',
'labels' => [
    'name' => 'コラム',
    'singular_name' => 'コラム',
],
'public' => true,
'has_archive' => true,
'rewrite' => ['slug' => 'column'],
'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'],
'menu_icon' => 'dashicons-edit-large',
'show_in_rest' => true,
```

### タクソノミー

#### column_category（階層あり）

```php
'taxonomy' => 'column_category',
'object_type' => ['column'],
'hierarchical' => true,
'labels' => ['name' => 'カテゴリ'],
'rewrite' => ['slug' => 'column-category'],
'show_in_rest' => true,
```

**初期カテゴリ**:
- 申請のコツ
- 制度解説
- 動向・ニュース
- 成功事例
- その他

#### column_tag（階層なし）

```php
'taxonomy' => 'column_tag',
'object_type' => ['column'],
'hierarchical' => false,
'labels' => ['name' => 'タグ'],
'rewrite' => ['slug' => 'column-tag'],
'show_in_rest' => true,
```

**タグ例**:
- 事業再構築補助金
- IT導入補助金
- DX推進
- 設備投資
- 人材育成
- 環境・省エネ

### カスタムフィールド（ACF）

```php
// フィールドグループ: column_fields
[
    // ステータス管理
    [
        'key' => 'field_column_status',
        'label' => '記事ステータス',
        'name' => 'column_status',
        'type' => 'select',
        'choices' => [
            'draft' => '下書き',
            'pending' => 'レビュー待ち',
            'approved' => '承認済み',
            'featured' => '特集記事',
        ],
        'default_value' => 'draft',
    ],
    
    // 読了時間
    [
        'key' => 'field_estimated_read_time',
        'label' => '読了時間（分）',
        'name' => 'estimated_read_time',
        'type' => 'number',
        'default_value' => 5,
        'min' => 1,
        'max' => 60,
    ],
    
    // 難易度
    [
        'key' => 'field_difficulty_level',
        'label' => '難易度',
        'name' => 'difficulty_level',
        'type' => 'select',
        'choices' => [
            'beginner' => '初心者向け',
            'intermediate' => '中級者向け',
            'advanced' => '上級者向け',
        ],
    ],
    
    // 関連補助金（Relationship）
    [
        'key' => 'field_related_grants',
        'label' => '関連補助金',
        'name' => 'related_grants',
        'type' => 'relationship',
        'post_type' => ['grant'],
        'return_format' => 'id',
        'max' => 5,
    ],
    
    // 関連度スコア（内部用・非表示）
    [
        'key' => 'field_relation_scores',
        'label' => '関連度スコア',
        'name' => 'relation_scores',
        'type' => 'textarea',
        'default_value' => '[]',
        'wrapper' => ['class' => 'hidden'],
    ],
    
    // 閲覧回数（自動更新）
    [
        'key' => 'field_view_count',
        'label' => '閲覧回数',
        'name' => 'view_count',
        'type' => 'number',
        'default_value' => 0,
        'readonly' => 1,
    ],
    
    // 最終更新日
    [
        'key' => 'field_last_updated',
        'label' => '最終更新日',
        'name' => 'last_updated',
        'type' => 'date_picker',
        'return_format' => 'Y-m-d',
        'display_format' => 'Y年m月d日',
    ],
    
    // SEO タイトル
    [
        'key' => 'field_seo_title',
        'label' => 'SEO タイトル',
        'name' => 'seo_title',
        'type' => 'text',
        'maxlength' => 60,
        'placeholder' => '空欄の場合、記事タイトルが使用されます',
    ],
    
    // SEO 説明文
    [
        'key' => 'field_seo_description',
        'label' => 'SEO 説明文',
        'name' => 'seo_description',
        'type' => 'textarea',
        'maxlength' => 160,
        'rows' => 3,
    ],
    
    // 対象読者
    [
        'key' => 'field_target_audience',
        'label' => '対象読者',
        'name' => 'target_audience',
        'type' => 'checkbox',
        'choices' => [
            'startup' => '創業・スタートアップ',
            'sme' => '中小企業',
            'individual' => '個人事業主',
            'npo' => 'NPO・一般社団法人',
            'agriculture' => '農業者',
        ],
    ],
]
```

---

## 🔧 主要機能の実装ロジック

### 1. 補助金自動連携（AIマッチング）

```php
/**
 * 記事保存時に補助金との関連付けを自動実行
 */
function gi_column_auto_link_grants($post_id) {
    // 1. 記事本文からキーワード抽出
    $content = get_post_field('post_content', $post_id);
    $title = get_post_field('post_title', $post_id);
    $keywords = gi_extract_keywords($content . ' ' . $title);
    
    // 2. 補助金データベースとマッチング
    $grants = get_posts([
        'post_type' => 'grant',
        'posts_per_page' => -1,
    ]);
    
    $scores = [];
    foreach ($grants as $grant) {
        $grant_text = $grant->post_title . ' ' . $grant->post_content;
        $score = gi_calculate_similarity($keywords, $grant_text);
        if ($score >= 0.7) { // 関連度70%以上
            $scores[$grant->ID] = $score;
        }
    }
    
    // 3. スコアでソート
    arsort($scores);
    
    // 4. 上位5件を関連補助金として保存
    $related_grant_ids = array_slice(array_keys($scores), 0, 5);
    update_field('related_grants', $related_grant_ids, $post_id);
    update_field('relation_scores', json_encode($scores), $post_id);
}

add_action('save_post_column', 'gi_column_auto_link_grants', 20);
```

### 2. 承認システム（ワークフロー）

```
執筆者が投稿
  ↓ (status: draft)
  
編集者がレビュー
  ↓ (status: pending)
  
編集長が承認
  ↓ (status: approved)
  
公開
  ↓ (status: publish + column_status: approved/featured)
```

```php
/**
 * 承認ステータスによる表示制御
 */
function gi_column_can_display($post_id) {
    $status = get_field('column_status', $post_id);
    $post_status = get_post_status($post_id);
    
    // 公開済み + 承認済みのみ表示
    return $post_status === 'publish' && 
           in_array($status, ['approved', 'featured']);
}
```

### 3. PV計測・ランキング

```php
/**
 * 記事閲覧時にPVカウントを増加
 */
function gi_column_count_view($post_id) {
    // Cookieで重複カウント防止（1日1回）
    $cookie_name = 'column_viewed_' . $post_id;
    if (isset($_COOKIE[$cookie_name])) {
        return;
    }
    
    // カウント増加
    $current_count = (int) get_field('view_count', $post_id);
    update_field('view_count', $current_count + 1, $post_id);
    
    // Cookie設定（24時間）
    setcookie($cookie_name, '1', time() + 86400, '/');
}

// single-column.phpで実行
if (is_singular('column')) {
    gi_column_count_view(get_the_ID());
}
```

```php
/**
 * 人気記事ランキング取得
 */
function gi_get_popular_columns($limit = 10) {
    return new WP_Query([
        'post_type' => 'column',
        'posts_per_page' => $limit,
        'meta_key' => 'view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'column_status',
                'value' => ['approved', 'featured'],
                'compare' => 'IN',
            ],
        ],
    ]);
}
```

---

## 📝 AI開発者への指示

### 🔴 絶対守るべき原則

1. **妥協禁止**: 機能不完全は許容しない。コードが長くても完全実装すること
2. **既存デザイン踏襲**: 色（緑系）・フォント・余白を既存サイトと完全一致させる
3. **Tailwind CSS活用**: 既存のTailwind設定を最大限活用する
4. **ファイル最小化**: 上記の最小ファイル構成を厳守
5. **セキュリティ**: nonce検証、エスケープ処理を必ず実装
6. **パフォーマンス**: SQLクエリの最適化、キャッシュ活用
7. **引き継ぎ前提**: セッション終了時は詳細な進捗レポート作成
8. **テスト必須**: 各機能実装後、動作確認手順を明記

### 📋 開発セッションの進め方

#### 開始時
1. ✅ 前回の進捗状況を確認
2. ✅ 今回実装する機能を明確化
3. ✅ 既存ファイルとの整合性チェック

#### 作業中
1. ✅ コード作成
2. ✅ コメント詳細に記述（日本語可）
3. ✅ エラーハンドリング実装
4. ✅ セキュリティ対策実装（nonce, sanitize, escape）

#### 終了時
1. ✅ 完了した機能リスト
2. ✅ 未完了の機能と理由
3. ✅ 次回やるべきことリスト
4. ✅ テスト手順書
5. ✅ 既知の問題点

### 🎯 実装開始前の確認事項

AI開発者は以下を確認してから開始:

- [x] 既存テーマの構成を理解しましたか？
- [x] 既存の色（緑系）・フォントを確認しましたか?
- [x] Tailwind CSSの使い方を理解しましたか?
- [ ] ACF (Advanced Custom Fields) の使い方を知っていますか?
- [ ] WordPress Ajax APIを理解していますか?
- [x] 最小ファイル構成を把握しましたか?
- [x] Phase 1から順番に実装することを理解しましたか?

---

## 🔄 引き継ぎテンプレート

各セッション終了時に以下フォーマットで報告:

```markdown
## セッション終了レポート - 2025/XX/XX

### ✅ 実装完了
- [x] column-system.php - 投稿タイプ登録部分 (行1-150)
- [x] column-system.php - タクソノミー登録部分 (行151-250)
- [x] zone.php - 基本レイアウト構造
- [ ] card.php - 未着手

### 📝 次回タスク（優先順位順）
1. **card.php の実装** (優先度: 最高)
   - アイキャッチ画像表示
   - カテゴリバッジ
   - メタ情報（日付・読了時間・PV）
   
2. **sidebar.php の実装** (優先度: 高)
   - 人気記事ランキング
   - 注目キーワード
   
3. **column.css の作成** (優先度: 中)

### ⚠️ 既知の問題
- なし

### ✅ 動作確認方法
1. WordPressダッシュボードで「コラム」メニュー表示確認
   - URL: `/wp-admin/edit.php?post_type=column`
2. 新規コラム投稿画面でカスタムフィールド表示確認
   - URL: `/wp-admin/post-new.php?post_type=column`
3. フロントページでコラムゾーン表示確認
   - URL: `/`

### 📁 コード配置場所
- `/home/user/webapp/inc/column-system.php` (新規作成)
- `/home/user/webapp/template-parts/column/zone.php` (新規作成)

### 💡 技術メモ
- ACFフィールドは`acf_add_local_field_group()`で定義
- Tailwindクラスは`bg-primary`（緑系）を使用
- 補助金連携は後回し（Phase 2）
```

---

## 🚦 品質チェックリスト

各機能実装後、以下を確認:

### コード品質
- [ ] コメント適切に記述（日本語可）
- [ ] 変数名・関数名が意味的（英語推奨）
- [ ] エラーハンドリング実装
- [ ] SQLインジェクション対策
- [ ] XSS対策（エスケープ処理）
- [ ] nonce検証実装（Ajax）

### デザイン品質
- [ ] 既存サイトと色が一致（緑系）
- [ ] レスポンシブ対応（PC/タブレット/モバイル）
- [ ] アクセシビリティ配慮（altタグ、ARIAラベル）
- [ ] ブラウザ互換性（Chrome, Safari, Firefox, Edge）

### 機能品質
- [ ] 想定通りに動作
- [ ] エラー時の挙動適切
- [ ] パフォーマンス問題なし（N+1クエリ回避）
- [ ] 既存機能への影響なし

---

## 📞 サポート情報

### 参考資料
- **WordPress Codex**: https://codex.wordpress.org/
- **ACF Documentation**: https://www.advancedcustomfields.com/resources/
- **Tailwind CSS Docs**: https://tailwindcss.com/docs
- **既存テーマ**: `/home/user/webapp/` 内のファイルを参照

### 質問すべき時
- ❓ 既存コードの意図が不明な時
- ❓ 複数の実装方法がある時
- ❓ セキュリティ懸念がある時
- ❓ パフォーマンス影響が大きい時

---

## ✅ 最終目標

### ユーザー体験
- ✅ トップページにYahoo!風コラムゾーンが表示
- ✅ カテゴリタブでスムーズに切り替え
- ✅ 記事カードから詳細ページへ遷移
- ✅ 記事から関連補助金へ簡単アクセス
- ✅ 人気記事ランキングで人気コンテンツ発見

### 管理者体験
- ✅ 直感的な記事投稿画面
- ✅ 承認フローがスムーズ
- ✅ 補助金との関連付けが自動
- ✅ Analytics データで効果測定

### 技術的品質
- ✅ 既存サイトと完全に調和（緑系カラー）
- ✅ パフォーマンス影響最小
- ✅ 保守しやすいコード
- ✅ 拡張可能な設計

---

## 🎬 開発スタート

この指示書を基に、**Phase 1から順番に**、妥協なく完全な実装を期待します。  
**長いコードでも問題ありません。品質を最優先してください。**

---

**作成者**: AI開発サポートシステム  
**最終更新**: 2025年11月2日  
**バージョン**: 2.1 (実際のカラースキーム反映版)
