<?php
/**
 * Template Part: Grant Tabs Section  
 * Yahoo! JAPAN風タブ式補助金ブラウジングセクション
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0 - Yahoo Style Implementation
 * 
 * 4つのタブ:
 * 1. 締切間近 (30日以内)
 * 2. おすすめ補助金 (注目記事)
 * 3. 新着補助金 (最新投稿)
 * 4. あなたにおすすめ (閲覧履歴ベース)
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// section-search.phpから渡された変数を取得
$deadline_soon_grants = get_query_var('deadline_soon_grants');
$recommended_grants = get_query_var('recommended_grants');
$new_grants = get_query_var('new_grants');

// 配列であることを保証（nullの場合は空配列に）
$deadline_soon_grants = is_array($deadline_soon_grants) ? $deadline_soon_grants : array();
$recommended_grants = is_array($recommended_grants) ? $recommended_grants : array();
$new_grants = is_array($new_grants) ? $new_grants : array();

// ===== コラムデータの取得 =====
// カテゴリ一覧を取得
$column_categories = get_terms(array(
    'taxonomy' => 'column_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10, // TOP 10カテゴリ
));
if (is_wp_error($column_categories)) {
    $column_categories = array();
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

// 記事ランキング（人気のコラムと同じだが、表示方法を変える）
$ranking_columns = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 10,
    'post_status' => 'publish',
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
));
?>

<!-- Yahoo!風タブ式補助金ブラウジング -->
<section class="grant-tabs-section" 
         aria-labelledby="grant-tabs-title"
         itemscope 
         itemtype="https://schema.org/ItemList">
    <div class="grant-tabs-wrapper">
        
        <!-- セクションヘッダー -->
        <header class="section-header">
            <div class="header-left">
                <h2 id="grant-tabs-title" class="section-title" itemprop="name">
                    <i class="fas fa-newspaper" aria-hidden="true"></i>
                    補助金ニュース
                </h2>
                <p class="section-desc" itemprop="description">
                    最新の補助金情報をタブで簡単チェック
                </p>
            </div>
            <div class="header-actions">
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                   class="view-all"
                   aria-label="補助金一覧を見る"
                   title="補助金一覧ページへ">
                    補助金すべて見る <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="<?php echo esc_url(home_url('/columns/')); ?>" 
                   class="view-all view-all-columns"
                   aria-label="コラム一覧を見る"
                   title="コラム一覧ページへ">
                    コラム一覧 <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
            </div>
        </header>

        <!-- タブナビゲーション (Yahoo!風) -->
        <nav class="grant-tabs-nav" role="tablist" aria-label="補助金タブ">
            <button class="grant-tab-btn active" 
                    id="tab-deadline-soon" 
                    role="tab" 
                    aria-selected="true" 
                    aria-controls="panel-deadline-soon"
                    data-tab="deadline-soon">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                <span>締切間近</span>
                <span class="tab-count"><?php echo count($deadline_soon_grants); ?></span>
            </button>
            <button class="grant-tab-btn" 
                    id="tab-recommended" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-recommended"
                    data-tab="recommended">
                <i class="fas fa-thumbs-up" aria-hidden="true"></i>
                <span>おすすめ</span>
                <span class="tab-count"><?php echo count($recommended_grants); ?></span>
            </button>
            <button class="grant-tab-btn" 
                    id="tab-new" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-new"
                    data-tab="new">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <span>新着</span>
                <span class="tab-count"><?php echo count($new_grants); ?></span>
            </button>
            <button class="grant-tab-btn" 
                    id="tab-personalized" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-personalized"
                    data-tab="personalized">
                <i class="fas fa-user-circle" aria-hidden="true"></i>
                <span>あなたにおすすめ</span>
                <span class="tab-badge" style="display: none;">閲覧ベース</span>
            </button>
            
            <!-- コラムタブ Section -->
            <div class="tab-separator">
                <span class="separator-text">コラム</span>
            </div>
            
            <button class="grant-tab-btn column-tab-btn" 
                    id="tab-column-all" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-column-all"
                    data-tab="column-all">
                <i class="fas fa-list" aria-hidden="true"></i>
                <span>全て</span>
                <span class="tab-count"><?php echo $all_columns->found_posts; ?></span>
            </button>
            <button class="grant-tab-btn column-tab-btn" 
                    id="tab-column-ranking" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-column-ranking"
                    data-tab="column-ranking">
                <i class="fas fa-trophy" aria-hidden="true"></i>
                <span>記事ランキング</span>
                <span class="tab-count"><?php echo $ranking_columns->found_posts; ?></span>
            </button>
            <button class="grant-tab-btn column-tab-btn" 
                    id="tab-column-popular" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-column-popular"
                    data-tab="column-popular">
                <i class="fas fa-fire" aria-hidden="true"></i>
                <span>人気</span>
                <span class="tab-count"><?php echo $popular_columns->found_posts; ?></span>
            </button>
            <button class="grant-tab-btn column-tab-btn" 
                    id="tab-column-recent" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-column-recent"
                    data-tab="column-recent">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <span>新着</span>
                <span class="tab-count"><?php echo $recent_columns->found_posts; ?></span>
            </button>
            <?php 
            // カテゴリタブを動的に追加
            if (!empty($column_categories)) :
                foreach (array_slice($column_categories, 0, 5) as $category) : 
            ?>
            <button class="grant-tab-btn column-tab-btn category-tab" 
                    id="tab-column-cat-<?php echo esc_attr($category->slug); ?>" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="panel-column-cat-<?php echo esc_attr($category->slug); ?>"
                    data-tab="column-cat-<?php echo esc_attr($category->slug); ?>"
                    data-category="<?php echo esc_attr($category->slug); ?>">
                <i class="fas fa-folder" aria-hidden="true"></i>
                <span><?php echo esc_html($category->name); ?></span>
                <span class="tab-count"><?php echo $category->count; ?></span>
            </button>
            <?php 
                endforeach;
            endif;
            ?>
        </nav>

        <!-- タブコンテンツエリア -->
        <div class="grant-tabs-content">
            
            <!-- タブ1: 締切間近 (30日以内) -->
            <div class="grant-tab-panel active" 
                 id="panel-deadline-soon" 
                 role="tabpanel" 
                 aria-labelledby="tab-deadline-soon">
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        締切間近の補助金（30日以内）
                    </h3>
                    <p class="panel-desc">
                        申請期限が迫っている補助金です。お早めにご確認ください。
                    </p>
                </div>
                <div class="grants-grid" role="list">
                    <?php 
                    if (!empty($deadline_soon_grants)) :
                        $position = 1;
                        foreach ($deadline_soon_grants as $grant) : 
                            set_query_var('grant', $grant);
                            set_query_var('position', $position++);
                            get_template_part('template-parts/grant/card');
                        endforeach;
                    else : 
                        // デバッグ情報を表示
                        $total_grants = wp_count_posts('grant');
                    ?>
                        <div class="no-data-info" role="status">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            <p>現在、締切間近（30日以内）の補助金はありません。</p>
                            <?php if ($total_grants && $total_grants->publish > 0) : ?>
                            <p class="debug-info">
                                <small>
                                    <strong>デバッグ情報:</strong><br>
                                    データベース内の補助金総数: <?php echo $total_grants->publish; ?>件<br>
                                    deadline_dateフィールドに30日以内の日付が設定されている補助金が表示されます。<br>
                                    <?php
                                    // サンプルデータの確認
                                    $sample_grant = get_posts(array('post_type' => 'grant', 'posts_per_page' => 1));
                                    if (!empty($sample_grant)) {
                                        $sample = $sample_grant[0];
                                        echo '<br><strong>サンプル確認:</strong><br>';
                                        echo 'ID: ' . $sample->ID . '<br>';
                                        echo 'タイトル: ' . esc_html($sample->post_title) . '<br>';
                                        $dd = get_field('deadline_date', $sample->ID);
                                        echo 'deadline_date (ACF): ' . ($dd ? $dd : '未設定') . '<br>';
                                        $dd2 = get_post_meta($sample->ID, 'deadline_date', true);
                                        echo 'deadline_date (meta): ' . ($dd2 ? $dd2 : '未設定') . '<br>';
                                    }
                                    ?>
                                </small>
                            </p>
                            <?php else : ?>
                            <p class="debug-info">
                                <small>補助金データがまだ登録されていません。</small>
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- タブ2: おすすめ補助金 -->
            <div class="grant-tab-panel" 
                 id="panel-recommended" 
                 role="tabpanel" 
                 aria-labelledby="tab-recommended" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-thumbs-up" aria-hidden="true"></i>
                        おすすめ補助金
                    </h3>
                    <p class="panel-desc">
                        注目度の高い補助金をピックアップしました。
                    </p>
                </div>
                <div class="grants-grid" role="list">
                    <?php 
                    if (!empty($recommended_grants)) :
                        $position = 1;
                        foreach ($recommended_grants as $grant) : 
                            set_query_var('grant', $grant);
                            set_query_var('position', $position++);
                            get_template_part('template-parts/grant/card');
                        endforeach;
                    else : 
                        $total_grants = wp_count_posts('grant');
                    ?>
                        <div class="no-data-info" role="status">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            <p>現在、おすすめの補助金はありません。</p>
                            <?php if ($total_grants && $total_grants->publish > 0) : ?>
                            <p class="debug-info">
                                <small>
                                    <strong>表示条件:</strong><br>
                                    is_featured='1'の補助金、または閲覧数が多い補助金が表示されます。<br>
                                    <?php
                                    // フォールバックテスト
                                    $fallback_test = get_posts(array(
                                        'post_type' => 'grant',
                                        'posts_per_page' => 3,
                                        'orderby' => 'ID',
                                        'order' => 'DESC'
                                    ));
                                    echo '<br><strong>フォールバック結果:</strong> ' . count($fallback_test) . '件取得<br>';
                                    ?>
                                </small>
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- タブ3: 新着補助金 -->
            <div class="grant-tab-panel" 
                 id="panel-new" 
                 role="tabpanel" 
                 aria-labelledby="tab-new" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        新着補助金
                        <span class="update-info">
                            <time datetime="<?php echo date('Y-m-d'); ?>">
                                <?php echo date('Y/m/d'); ?> (<?php echo array('日', '月', '火', '水', '木', '金', '土')[date('w')]; ?>)
                            </time> 更新
                        </span>
                    </h3>
                    <p class="panel-desc">
                        最新の補助金情報をお届けします。毎週月・木曜更新。
                    </p>
                </div>
                <div class="grants-grid" role="list">
                    <?php 
                    if (!empty($new_grants)) :
                        $position = 1;
                        foreach ($new_grants as $grant) : 
                            set_query_var('grant', $grant);
                            set_query_var('position', $position++);
                            get_template_part('template-parts/grant/card');
                        endforeach;
                    else : 
                        $total_grants = wp_count_posts('grant');
                    ?>
                        <div class="no-data-info" role="status">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            <p>現在、新着補助金はありません。</p>
                            <?php if ($total_grants && $total_grants->publish > 0) : ?>
                            <p class="debug-info">
                                <small>
                                    <strong>デバッグ情報:</strong><br>
                                    データベース内の補助金総数: <?php echo $total_grants->publish; ?>件<br>
                                    最新の投稿日順で表示されます。<br>
                                    <?php
                                    // クエリテスト
                                    $test_query = get_posts(array(
                                        'post_type' => 'grant',
                                        'posts_per_page' => 3,
                                        'orderby' => 'ID',
                                        'order' => 'DESC',
                                        'post_status' => 'publish'
                                    ));
                                    echo '<br><strong>テストクエリ結果:</strong><br>';
                                    echo '取得件数: ' . count($test_query) . '件<br>';
                                    if (!empty($test_query)) {
                                        foreach (array_slice($test_query, 0, 2) as $tg) {
                                            echo '- ID: ' . $tg->ID . ', タイトル: ' . esc_html(mb_substr($tg->post_title, 0, 30)) . '...<br>';
                                        }
                                    }
                                    ?>
                                </small>
                            </p>
                            <?php else : ?>
                            <p class="debug-info">
                                <small>補助金データがまだ登録されていません。</small>
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- タブ4: あなたにおすすめ (閲覧履歴ベース) -->
            <div class="grant-tab-panel" 
                 id="panel-personalized" 
                 role="tabpanel" 
                 aria-labelledby="tab-personalized" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-user-circle" aria-hidden="true"></i>
                        あなたにおすすめの補助金
                    </h3>
                    <p class="panel-desc">
                        あなたの閲覧履歴に基づいて、おすすめの補助金を表示します。
                    </p>
                </div>
                
                <!-- ローディング表示 -->
                <div class="personalized-loading" id="personalized-loading">
                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                    <p>あなたにおすすめの補助金を読み込んでいます...</p>
                </div>
                
                <!-- 履歴なしメッセージ -->
                <div class="no-history-message" id="no-history-message" style="display: none;">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    <h4>まだ閲覧履歴がありません</h4>
                    <p>補助金ページを閲覧すると、あなたにおすすめの補助金が表示されます。<br>
                    まずは「おすすめ」や「新着」タブから気になる補助金をチェックしてみましょう！</p>
                </div>
                
                <!-- パーソナライズされた補助金グリッド -->
                <div class="grants-grid" id="personalized-grants-grid" role="list" style="display: none;">
                    <!-- JavaScriptで動的に読み込まれます -->
                </div>
            </div>

            <!-- ===== コラムタブパネル ===== -->
            
            <!-- コラムタブ1: 全て -->
            <div class="grant-tab-panel column-tab-panel" 
                 id="panel-column-all" 
                 role="tabpanel" 
                 aria-labelledby="tab-column-all" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-list" aria-hidden="true"></i>
                        全てのコラム
                    </h3>
                    <p class="panel-desc">
                        最新のコラム記事を一覧でご覧いただけます。
                    </p>
                </div>
                <div class="columns-list" role="list">
                    <?php 
                    if ($all_columns->have_posts()) :
                        $position = 1;
                        while ($all_columns->have_posts()) : 
                            $all_columns->the_post();
                    ?>
                        <article class="column-card-compact" role="listitem">
                            <a href="<?php the_permalink(); ?>" class="column-card-link">
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="column-thumbnail">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                                <?php endif; ?>
                                <div class="column-content">
                                    <h4 class="column-title"><?php the_title(); ?></h4>
                                    <div class="column-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y/m/d'); ?></time>
                                        <?php
                                        $categories = get_the_terms(get_the_ID(), 'column_category');
                                        if ($categories && !is_wp_error($categories)) :
                                            $category = $categories[0];
                                        ?>
                                        <span class="category-tag"><?php echo esc_html($category->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <div class="no-data-info">
                            <i class="fas fa-info-circle"></i>
                            <p>現在、コラム記事はありません。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- コラムタブ2: 記事ランキング -->
            <div class="grant-tab-panel column-tab-panel" 
                 id="panel-column-ranking" 
                 role="tabpanel" 
                 aria-labelledby="tab-column-ranking" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-trophy" aria-hidden="true"></i>
                        記事ランキング
                    </h3>
                    <p class="panel-desc">
                        人気のコラム記事をランキング形式でお届けします。
                    </p>
                </div>
                <div class="columns-ranking" role="list">
                    <?php 
                    if ($ranking_columns->have_posts()) :
                        $rank = 1;
                        while ($ranking_columns->have_posts()) : 
                            $ranking_columns->the_post();
                            $view_count = get_post_meta(get_the_ID(), 'view_count', true);
                    ?>
                        <article class="column-card-ranking" role="listitem">
                            <div class="rank-badge rank-<?php echo $rank; ?>">
                                <span class="rank-number"><?php echo $rank; ?></span>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="column-card-link">
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="column-thumbnail">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                                <?php endif; ?>
                                <div class="column-content">
                                    <h4 class="column-title"><?php the_title(); ?></h4>
                                    <div class="column-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y/m/d'); ?></time>
                                        <?php if ($view_count) : ?>
                                        <span class="view-count">
                                            <i class="fas fa-eye"></i> <?php echo number_format($view_count); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php 
                        $rank++;
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <div class="no-data-info">
                            <i class="fas fa-info-circle"></i>
                            <p>現在、ランキングデータはありません。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- コラムタブ3: 人気 -->
            <div class="grant-tab-panel column-tab-panel" 
                 id="panel-column-popular" 
                 role="tabpanel" 
                 aria-labelledby="tab-column-popular" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-fire" aria-hidden="true"></i>
                        人気のコラム
                    </h3>
                    <p class="panel-desc">
                        閲覧数の多い人気記事をご紹介します。
                    </p>
                </div>
                <div class="columns-list" role="list">
                    <?php 
                    if ($popular_columns->have_posts()) :
                        while ($popular_columns->have_posts()) : 
                            $popular_columns->the_post();
                            $view_count = get_post_meta(get_the_ID(), 'view_count', true);
                    ?>
                        <article class="column-card-compact" role="listitem">
                            <a href="<?php the_permalink(); ?>" class="column-card-link">
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="column-thumbnail">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                                <?php endif; ?>
                                <div class="column-content">
                                    <h4 class="column-title"><?php the_title(); ?></h4>
                                    <div class="column-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y/m/d'); ?></time>
                                        <?php if ($view_count) : ?>
                                        <span class="view-count">
                                            <i class="fas fa-eye"></i> <?php echo number_format($view_count); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <div class="no-data-info">
                            <i class="fas fa-info-circle"></i>
                            <p>現在、人気コラムはありません。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- コラムタブ4: 新着 -->
            <div class="grant-tab-panel column-tab-panel" 
                 id="panel-column-recent" 
                 role="tabpanel" 
                 aria-labelledby="tab-column-recent" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        新着コラム
                    </h3>
                    <p class="panel-desc">
                        最新のコラム記事をいち早くお届けします。
                    </p>
                </div>
                <div class="columns-list" role="list">
                    <?php 
                    if ($recent_columns->have_posts()) :
                        while ($recent_columns->have_posts()) : 
                            $recent_columns->the_post();
                    ?>
                        <article class="column-card-compact" role="listitem">
                            <a href="<?php the_permalink(); ?>" class="column-card-link">
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="column-thumbnail">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                                <?php endif; ?>
                                <div class="column-content">
                                    <h4 class="column-title"><?php the_title(); ?></h4>
                                    <div class="column-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y/m/d'); ?></time>
                                        <span class="new-badge">NEW</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else : 
                    ?>
                        <div class="no-data-info">
                            <i class="fas fa-info-circle"></i>
                            <p>現在、新着コラムはありません。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- カテゴリタブパネル（動的に生成） -->
            <?php 
            if (!empty($column_categories)) :
                foreach (array_slice($column_categories, 0, 5) as $category) : 
            ?>
            <div class="grant-tab-panel column-tab-panel category-panel" 
                 id="panel-column-cat-<?php echo esc_attr($category->slug); ?>" 
                 role="tabpanel" 
                 aria-labelledby="tab-column-cat-<?php echo esc_attr($category->slug); ?>" 
                 hidden>
                <div class="tab-panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-folder" aria-hidden="true"></i>
                        <?php echo esc_html($category->name); ?>
                    </h3>
                    <p class="panel-desc">
                        <?php echo esc_html($category->description ? $category->description : $category->name . 'に関するコラム記事一覧'); ?>
                    </p>
                </div>
                <div class="columns-list category-loading" data-category="<?php echo esc_attr($category->slug); ?>">
                    <div class="loading-placeholder">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>コラムを読み込んでいます...</p>
                    </div>
                </div>
            </div>
            <?php 
                endforeach;
            endif;
            ?>

        </div>

    </div>
</section>

<style>
/* ============================================
   Grant Tabs Section - Yahoo! Style
   補助金タブセクション - Yahoo!風デザイン
   ============================================ */

.grant-tabs-section {
    background: var(--color-secondary, #ffffff);
    padding: 40px 0;
    width: 100%;
    overflow: visible;
}

.grant-tabs-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

/* セクションヘッダー */
.grant-tabs-section .section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.grant-tabs-section .header-left {
    flex: 1;
    min-width: 200px;
}

.grant-tabs-section .section-title {
    font-size: 24px;
    font-weight: 900;
    color: var(--color-primary, #000000);
    margin: 0 0 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.grant-tabs-section .section-title i {
    font-size: 26px;
}

.grant-tabs-section .section-desc {
    font-size: 14px;
    color: var(--color-gray-600, #525252);
    margin: 0;
    line-height: 1.5;
}

.grant-tabs-section .view-all {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary, #000000);
    background: var(--color-secondary, #ffffff);
    border: 2px solid var(--color-primary, #000000);
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.grant-tabs-section .view-all:hover {
    background: var(--color-accent, #ffeb3b);
    transform: translateY(-1px);
}

/* Header Actions - Multiple Links */
.grant-tabs-section .header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.grant-tabs-section .view-all-columns {
    background: var(--color-accent, #ffeb3b);
    border-color: var(--color-accent, #ffeb3b);
    color: var(--color-primary, #000000);
}

.grant-tabs-section .view-all-columns:hover {
    background: var(--color-primary, #000000);
    border-color: var(--color-primary, #000000);
    color: var(--color-secondary, #ffffff);
}

/* タブナビゲーション (Yahoo!風) */
.grant-tabs-nav {
    display: flex;
    gap: 4px;
    margin-bottom: 0;
    border-bottom: 3px solid var(--color-primary, #000000);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.grant-tabs-nav::-webkit-scrollbar {
    height: 6px;
}

.grant-tabs-nav::-webkit-scrollbar-track {
    background: var(--color-gray-100, #f5f5f5);
}

.grant-tabs-nav::-webkit-scrollbar-thumb {
    background: var(--color-gray-400, #a3a3a3);
    border-radius: 3px;
}

.grant-tab-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 28px;
    font-size: 16px;
    font-weight: 700;
    color: #666666;
    background: #f8f8f8;
    border: 2px solid #e5e5e5;
    border-bottom: 4px solid transparent;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    position: relative;
    margin-bottom: -4px;
    border-radius: 8px 8px 0 0;
}

.grant-tab-btn:hover {
    background: #ffffff;
    color: #000000;
    transform: translateY(-2px);
    border-color: #cccccc;
}

.grant-tab-btn.active {
    color: #000000;
    background: #ffffff;
    border-color: #000000;
    border-bottom-color: #ffeb3b;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

.grant-tab-btn i {
    font-size: 16px;
}

.grant-tab-btn .tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 20px;
    padding: 0 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-secondary, #ffffff);
    background: var(--color-primary, #000000);
    border-radius: 10px;
}

.grant-tab-btn.active .tab-count {
    background: var(--color-accent, #ffeb3b);
    color: var(--color-primary, #000000);
}

.grant-tab-btn .tab-badge {
    font-size: 10px;
    padding: 2px 6px;
    background: var(--color-accent, #ffeb3b);
    color: var(--color-primary, #000000);
    border-radius: 3px;
    font-weight: 600;
}

/* タブコンテンツ */
.grant-tabs-content {
    background: var(--color-secondary, #ffffff);
    border: 2px solid var(--color-primary, #000000);
    border-top: none;
    padding: 24px 16px;
}

.grant-tab-panel {
    display: none;
}

.grant-tab-panel.active {
    display: block;
    animation: fadeIn 0.3s ease;
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

.grant-tab-panel[hidden] {
    display: none !important;
}

/* タブパネルヘッダー */
.tab-panel-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--color-gray-200, #e5e5e5);
}

.tab-panel-header .panel-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--color-primary, #000000);
    margin: 0 0 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.tab-panel-header .panel-title i {
    font-size: 22px;
}

.tab-panel-header .update-info {
    font-size: 13px;
    font-weight: 400;
    color: var(--color-gray-600, #525252);
    margin-left: auto;
}

.tab-panel-header .panel-desc {
    font-size: 14px;
    color: var(--color-gray-600, #525252);
    margin: 0;
    line-height: 1.6;
}

/* 補助金グリッド (Yahoo!ニュース風大型カード対応) */
.grants-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 640px) {
    .grants-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (min-width: 1024px) {
    .grants-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }
}

/* データなしメッセージ */
.no-data {
    text-align: center;
    padding: 48px 24px;
    color: var(--color-gray-600, #525252);
    font-size: 15px;
    font-weight: 500;
    background: var(--color-gray-50, #fafafa);
    border: 2px dashed var(--color-gray-300, #d4d4d4);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.no-data i {
    font-size: 20px;
    color: var(--color-gray-500, #737373);
}

.no-data-info {
    text-align: center;
    padding: 32px 24px;
    background: #f8f9fa;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
}

.no-data-info i {
    font-size: 32px;
    color: #ffeb3b;
    margin-bottom: 12px;
}

.no-data-info p {
    font-size: 15px;
    color: #333333;
    margin: 8px 0;
}

.no-data-info .debug-info {
    margin-top: 16px;
    padding: 12px;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
}

.no-data-info .debug-info small {
    font-size: 13px;
    color: #666666;
    line-height: 1.6;
}

/* パーソナライズドセクション */
.personalized-loading {
    text-align: center;
    padding: 48px 24px;
    color: var(--color-gray-600, #525252);
}

.personalized-loading i {
    font-size: 32px;
    color: var(--color-primary, #000000);
    margin-bottom: 12px;
}

.personalized-loading p {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}

.no-history-message {
    text-align: center;
    padding: 48px 24px;
    background: var(--color-gray-50, #fafafa);
    border: 2px solid var(--color-gray-200, #e5e5e5);
    border-radius: 8px;
}

.no-history-message i {
    font-size: 48px;
    color: var(--color-accent, #ffeb3b);
    margin-bottom: 16px;
}

.no-history-message h4 {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-primary, #000000);
    margin: 0 0 12px;
}

.no-history-message p {
    font-size: 14px;
    color: var(--color-gray-600, #525252);
    margin: 0;
    line-height: 1.8;
}

/* ============================================
   Column Tabs Styling
   コラムタブ専用スタイル
   ============================================ */

/* Tab Separator */
.tab-separator {
    display: flex;
    align-items: center;
    width: 100%;
    margin: 12px 0 8px;
    padding: 0 8px;
}

.tab-separator .separator-text {
    font-size: 12px;
    font-weight: 700;
    color: var(--color-gray-600, #525252);
    background: var(--color-gray-100, #f5f5f5);
    padding: 4px 12px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Column Tab Buttons */
.column-tab-btn {
    background: #fff5e6;
    border-color: #ffeb3b;
}

.column-tab-btn:hover {
    background: #ffeb3b;
}

.column-tab-btn.active {
    background: #ffeb3b;
    border-color: #000000;
    border-bottom-color: #ffeb3b;
}

/* Column Cards - Compact Vertical List Style */
.columns-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.column-card-compact {
    background: #ffffff;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.column-card-compact:hover {
    border-color: #ffeb3b;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.column-card-link {
    display: flex;
    gap: 12px;
    padding: 12px;
    text-decoration: none;
    color: inherit;
}

.column-thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 6px;
    background: #f5f5f5;
}

.column-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.column-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 6px;
}

.column-title {
    font-size: 15px;
    font-weight: 700;
    color: #000000;
    margin: 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.column-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #666666;
}

.column-meta time {
    color: #999999;
}

.category-tag {
    display: inline-block;
    padding: 2px 8px;
    background: #f0f0f0;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    color: #666666;
}

.view-count {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #ff6b6b;
    font-weight: 600;
}

.new-badge {
    display: inline-block;
    padding: 2px 6px;
    background: #ff4444;
    color: #ffffff;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.05em;
}

/* Ranking Cards */
.columns-ranking {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.column-card-ranking {
    position: relative;
    background: #ffffff;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.column-card-ranking:hover {
    border-color: #ffeb3b;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.rank-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #666666;
    color: #ffffff;
    border-radius: 50%;
    font-weight: 900;
    font-size: 14px;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.rank-badge.rank-1 {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000000;
}

.rank-badge.rank-2 {
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
    color: #000000;
}

.rank-badge.rank-3 {
    background: linear-gradient(135deg, #cd7f32, #e89e6a);
    color: #ffffff;
}

.rank-number {
    font-size: 16px;
}

/* Loading States */
.loading-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 24px;
    color: #666666;
}

.loading-placeholder i {
    font-size: 32px;
    margin-bottom: 12px;
    color: #ffeb3b;
}

.loading-placeholder p {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

/* レスポンシブ調整 */
@media (max-width: 767px) {
    .grant-tabs-section {
        padding: 32px 0;
    }
    
    .grant-tabs-wrapper {
        padding: 0 12px;
    }
    
    .grant-tab-btn {
        padding: 12px 14px;
        font-size: 13px;
    }
    
    .grant-tab-btn span:not(.tab-count):not(.tab-badge) {
        display: none;
    }
    
    .grant-tab-btn i {
        margin-right: 0;
    }
    
    .grant-tabs-content {
        padding: 16px 12px;
    }
    
    .tab-panel-header .panel-title {
        font-size: 18px;
    }
}

@media (min-width: 768px) {
    .grant-tabs-wrapper {
        max-width: 1200px;
    }
}

@media (min-width: 1024px) {
    .grant-tabs-wrapper {
        max-width: 1200px;
    }
    
    .grant-tabs-content {
        padding: 32px 24px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // タブ切り替え機能
    function initGrantTabs() {
        const tabButtons = document.querySelectorAll('.grant-tab-btn');
        const tabPanels = document.querySelectorAll('.grant-tab-panel');
        
        if (!tabButtons.length || !tabPanels.length) {
            return;
        }
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                
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
                const targetPanel = document.getElementById('panel-' + targetTab);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.removeAttribute('hidden');
                }
                
                // パーソナライズドタブの場合は履歴ベースのコンテンツを読み込み
                if (targetTab === 'personalized') {
                    loadPersonalizedGrants();
                }
                
                // カテゴリタブの場合はAjaxでコンテンツを読み込み
                if (targetTab.startsWith('column-cat-')) {
                    const categorySlug = this.dataset.category;
                    const panel = targetPanel.querySelector('.category-loading');
                    if (panel && !panel.dataset.loaded) {
                        loadCategoryColumns(panel, categorySlug);
                    }
                }
                
                console.log('[Grant Tabs] Switched to:', targetTab);
            });
        });
    }
    
    // パーソナライズされた補助金を読み込み
    function loadPersonalizedGrants() {
        const loading = document.getElementById('personalized-loading');
        const noHistory = document.getElementById('no-history-message');
        const grid = document.getElementById('personalized-grants-grid');
        
        if (!loading || !noHistory || !grid) {
            return;
        }
        
        // 既に読み込み済みの場合はスキップ
        if (grid.dataset.loaded === 'true') {
            return;
        }
        
        // ローディング表示
        loading.style.display = 'block';
        noHistory.style.display = 'none';
        grid.style.display = 'none';
        
        // 閲覧履歴APIを使用
        if (typeof window.giViewingHistory !== 'undefined') {
            window.giViewingHistory.fetchPersonalized(function(error, data) {
                loading.style.display = 'none';
                
                if (error) {
                    console.error('[Personalized] Error:', error);
                    noHistory.style.display = 'block';
                    return;
                }
                
                if (!data.hasHistory) {
                    // 履歴がない場合
                    noHistory.style.display = 'block';
                } else if (data.grants && data.grants.length > 0) {
                    // 補助金データを表示
                    grid.innerHTML = data.grants.map((grant, index) => {
                        return renderGrantCard(grant, index + 1);
                    }).join('');
                    grid.style.display = 'grid';
                    grid.dataset.loaded = 'true';
                } else {
                    // データはあるが補助金が見つからない
                    noHistory.style.display = 'block';
                }
            });
        } else {
            // APIが利用できない場合
            console.warn('[Personalized] Viewing history API not available');
            loading.style.display = 'none';
            noHistory.style.display = 'block';
        }
    }
    
    // 補助金カードをレンダリング (簡易版)
    function renderGrantCard(grant, position) {
        // この関数は実際にはWordPress側で生成されたHTMLを使用する
        // ここでは最小限の実装
        return `
            <article class="grant-card">
                <a href="${grant.url}" class="card-link">
                    <h3 class="card-title">${grant.title}</h3>
                </a>
            </article>
        `;
    }
    
    // カテゴリコラムを読み込み
    async function loadCategoryColumns(panel, categorySlug) {
        if (!panel || !categorySlug) return;
        
        try {
            // WordPress REST APIを使用してカテゴリのコラムを取得
            const restUrl = window.location.origin + '/wp-json/wp/v2/columns?column_category=' + categorySlug + '&per_page=9&_embed';
            const response = await fetch(restUrl);
            
            if (!response.ok) {
                throw new Error('Failed to fetch columns');
            }
            
            const columns = await response.json();
            
            if (columns.length === 0) {
                panel.innerHTML = `
                    <div class="no-data-info">
                        <i class="fas fa-info-circle"></i>
                        <p>このカテゴリにはまだコラムがありません。</p>
                    </div>
                `;
            } else {
                // コラムカードを生成
                const cardsHTML = columns.map(column => {
                    const title = column.title.rendered;
                    const url = column.link;
                    const date = new Date(column.date).toLocaleDateString('ja-JP');
                    let thumbnailHTML = '';
                    
                    // サムネイル画像の取得
                    if (column._embedded && column._embedded['wp:featuredmedia']) {
                        const media = column._embedded['wp:featuredmedia'][0];
                        if (media.media_details && media.media_details.sizes && media.media_details.sizes.thumbnail) {
                            const thumbUrl = media.media_details.sizes.thumbnail.source_url;
                            thumbnailHTML = `
                                <div class="column-thumbnail">
                                    <img src="${thumbUrl}" alt="${title}">
                                </div>
                            `;
                        }
                    }
                    
                    return `
                        <article class="column-card-compact" role="listitem">
                            <a href="${url}" class="column-card-link">
                                ${thumbnailHTML}
                                <div class="column-content">
                                    <h4 class="column-title">${title}</h4>
                                    <div class="column-meta">
                                        <time>${date}</time>
                                    </div>
                                </div>
                            </a>
                        </article>
                    `;
                }).join('');
                
                panel.innerHTML = cardsHTML;
            }
            
            panel.dataset.loaded = 'true';
            console.log('[Column Tabs] Loaded category:', categorySlug, columns.length, 'columns');
            
        } catch (error) {
            console.error('[Column Tabs] Error loading category:', error);
            panel.innerHTML = `
                <div class="no-data-info">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>コラムの読み込みに失敗しました。</p>
                </div>
            `;
        }
    }
    
    // 初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrantTabs);
    } else {
        initGrantTabs();
    }
    
    console.log('[OK] Grant Tabs initialized');
    
})();
</script>
