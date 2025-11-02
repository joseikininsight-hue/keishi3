<?php
/**
 * Single Column Template
 * コラム記事詳細ページ
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 1.0.0
 */

get_header();

// PV計測は自動実行される（column-system.phpのフィルタで処理済み）

while (have_posts()): the_post();

// メタ情報を取得
$post_id = get_the_ID();
$read_time = get_field('estimated_read_time', $post_id);
$view_count = get_field('view_count', $post_id);
$difficulty = get_field('difficulty_level', $post_id);
$last_updated = get_field('last_updated', $post_id);
$key_points = get_field('key_points', $post_id);
$target_audience = get_field('target_audience', $post_id);
$related_grants = get_field('related_grants', $post_id);
$categories = get_the_terms($post_id, 'column_category');
$tags = get_the_terms($post_id, 'column_tag');
$related_columns = gi_get_related_columns($post_id, 3);

// SEO情報
$seo_title = get_field('seo_title', $post_id) ?: get_the_title();
$seo_description = get_field('seo_description', $post_id) ?: wp_trim_words(get_the_excerpt(), 30);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('column-single bg-white'); ?>>
    
    <!-- ヘッダーセクション -->
    <header class="column-header bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 max-w-4xl py-8">
            
            <!-- パンくずリスト -->
            <nav class="breadcrumb text-sm text-gray-600 mb-6" aria-label="パンくず">
                <ol class="flex flex-wrap items-center space-x-2">
                    <li><a href="<?php echo home_url('/'); ?>" class="hover:text-primary">ホーム</a></li>
                    <li><span class="text-gray-400">&gt;</span></li>
                    <li><a href="<?php echo get_post_type_archive_link('column'); ?>" class="hover:text-primary">コラム</a></li>
                    <?php if ($categories && !is_wp_error($categories)): ?>
                        <li><span class="text-gray-400">&gt;</span></li>
                        <li><a href="<?php echo get_term_link($categories[0]); ?>" class="hover:text-primary"><?php echo esc_html($categories[0]->name); ?></a></li>
                    <?php endif; ?>
                    <li><span class="text-gray-400">&gt;</span></li>
                    <li class="text-gray-900 truncate max-w-xs" aria-current="page"><?php the_title(); ?></li>
                </ol>
            </nav>

            <!-- カテゴリとメタ情報 -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <?php if ($categories && !is_wp_error($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo get_term_link($cat); ?>" 
                           class="bg-primary text-white px-3 py-1 rounded-full text-sm font-medium hover:bg-primary-dark transition-colors">
                            <?php echo gi_get_category_icon($cat->slug); ?>
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if ($difficulty): ?>
                    <?php
                    $difficulty_colors = array(
                        'beginner' => 'bg-success',
                        'intermediate' => 'bg-warning',
                        'advanced' => 'bg-error',
                    );
                    $bg_color = isset($difficulty_colors[$difficulty]) ? $difficulty_colors[$difficulty] : 'bg-gray-500';
                    ?>
                    <span class="<?php echo $bg_color; ?> text-white px-3 py-1 rounded-full text-sm font-medium">
                        <?php echo gi_get_difficulty_label($difficulty); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- タイトル -->
            <h1 class="text-h1 font-bold text-gray-900 mb-4 leading-tight">
                <?php the_title(); ?>
            </h1>

            <!-- メタ情報バー -->
            <div class="article-meta flex flex-wrap items-center gap-4 text-gray-600 text-sm border-t border-gray-200 pt-4">
                <div class="flex items-center">
                    <span class="mr-2">📅</span>
                    <time datetime="<?php echo get_the_date('c'); ?>">
                        <?php echo get_the_date('Y年m月d日'); ?>
                    </time>
                </div>
                
                <?php if ($last_updated && $last_updated !== get_the_date('Y-m-d')): ?>
                    <div class="flex items-center">
                        <span class="mr-2">🔄</span>
                        <span>更新: <?php echo date('Y年m月d日', strtotime($last_updated)); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($read_time): ?>
                    <div class="flex items-center">
                        <span class="mr-2">⏱️</span>
                        <span><?php echo esc_html($read_time); ?>分で読めます</span>
                    </div>
                <?php endif; ?>
                
                <div class="flex items-center font-medium">
                    <span class="mr-2">👁️</span>
                    <span><?php echo number_format($view_count); ?> views</span>
                </div>
            </div>

        </div>
    </header>

    <!-- アイキャッチ画像 -->
    <?php if (has_post_thumbnail()): ?>
        <div class="article-thumbnail">
            <div class="container mx-auto px-4 max-w-4xl py-8">
                <figure class="rounded-lg overflow-hidden shadow-lg">
                    <?php the_post_thumbnail('large', array('class' => 'w-full h-auto')); ?>
                </figure>
            </div>
        </div>
    <?php endif; ?>

    <!-- 記事本文エリア -->
    <div class="container mx-auto px-4 max-w-4xl py-8">
        
        <!-- 対象読者 -->
        <?php if ($target_audience && is_array($target_audience) && count($target_audience) > 0): ?>
            <div class="target-audience bg-blue-50 border-l-4 border-info p-4 rounded mb-8">
                <h3 class="text-sm font-bold text-gray-900 mb-2 flex items-center">
                    <span class="mr-2">👥</span>
                    この記事はこんな方におすすめ
                </h3>
                <ul class="text-sm text-gray-700 space-y-1">
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
                        <li class="flex items-start">
                            <span class="text-primary mr-2">✓</span>
                            <?php echo esc_html($audience_labels[$audience]); ?>
                        </li>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- キーポイント（要点まとめ） -->
        <?php if ($key_points): ?>
            <div class="key-points bg-accent-light border border-accent rounded-lg p-5 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <span class="mr-2">💡</span>
                    この記事のポイント
                </h2>
                <div class="text-sm text-gray-800 prose prose-sm max-w-none">
                    <?php echo wp_kses_post($key_points); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 記事本文 -->
        <div class="article-content prose prose-lg max-w-none mb-8">
            <?php the_content(); ?>
        </div>

        <!-- ソーシャルシェアボタン (Phase 2) -->
        <div class="social-share-buttons mb-8 pb-8 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                <span class="mr-2">📢</span>
                この記事をシェア
            </h3>
            <div class="flex flex-wrap gap-3">
                <!-- Twitter -->
                <button data-share="twitter" 
                        class="share-btn flex items-center space-x-2 bg-[#1DA1F2] text-white px-4 py-2 rounded-lg hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                    </svg>
                    <span class="text-sm font-medium">Twitter</span>
                </button>

                <!-- Facebook -->
                <button data-share="facebook" 
                        class="share-btn flex items-center space-x-2 bg-[#1877F2] text-white px-4 py-2 rounded-lg hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-sm font-medium">Facebook</span>
                </button>

                <!-- LINE -->
                <button data-share="line" 
                        class="share-btn flex items-center space-x-2 bg-[#00B900] text-white px-4 py-2 rounded-lg hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                    </svg>
                    <span class="text-sm font-medium">LINE</span>
                </button>

                <!-- Pocket -->
                <button data-share="pocket" 
                        class="share-btn flex items-center space-x-2 bg-[#EF3F56] text-white px-4 py-2 rounded-lg hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.813 10c0-.529-.427-.956-.955-.956H6.142c-.528 0-.955.427-.955.956 0 .529.427.955.955.955h11.716c.528 0 .955-.426.955-.955zm2.629-8.448H2.561C1.139 1.552.017 2.673.017 4.095v5.498c0 6.356 5.165 11.523 11.522 11.523 6.356 0 11.523-5.167 11.523-11.523V4.095c0-1.422-1.122-2.543-2.543-2.543h-.077zm-11.261 12.83l-3.665-3.665c-.3-.3-.3-.786 0-1.086.3-.3.786-.3 1.086 0l3.079 3.08 3.08-3.08c.3-.3.785-.3 1.085 0 .3.3.3.786 0 1.086l-3.665 3.665c-.3.3-.785.3-1.085 0h.085z"/>
                    </svg>
                    <span class="text-sm font-medium">Pocket</span>
                </button>

                <!-- はてなブックマーク -->
                <button data-share="hatena" 
                        class="share-btn flex items-center space-x-2 bg-[#00A4DE] text-white px-4 py-2 rounded-lg hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 3H4c-.55 0-1 .45-1 1v16c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zM9.41 17H7.23V8.29c0-.21.17-.39.38-.39h1.41c1.62 0 2.94.91 2.94 2.53 0 .99-.5 1.68-1.18 2.07.88.42 1.49 1.22 1.49 2.31 0 1.79-1.39 2.58-3.24 2.58h-.62zm7.34-.03h-2.23v-2.1c0-.28.23-.51.51-.51h1.21c.28 0 .51.23.51.51v2.1zm0-3.68h-1.21c-.28 0-.51-.23-.51-.51V9.92c0-.28.23-.51.51-.51h1.21c.28 0 .51.23.51.51v2.86c0 .28-.23.51-.51.51z"/>
                    </svg>
                    <span class="text-sm font-medium">はてブ</span>
                </button>
            </div>
        </div>

        <!-- タグ -->
        <?php if ($tags && !is_wp_error($tags) && count($tags) > 0): ?>
            <div class="article-tags mb-8 pb-8 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3">関連タグ:</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?php echo get_term_link($tag); ?>" 
                           class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm hover:bg-primary hover:text-white transition-colors">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 関連補助金ウィジェット -->
        <?php if ($related_grants && is_array($related_grants) && count($related_grants) > 0): ?>
            <div class="related-grants-widget bg-gray-50 rounded-lg p-6 mb-12 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center border-b border-gray-200 pb-3">
                    <span class="mr-2 text-2xl">💰</span>
                    この記事に関連する補助金
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($related_grants as $grant_id): ?>
                        <?php
                        $grant_title = get_the_title($grant_id);
                        $grant_deadline = get_field('application_deadline', $grant_id);
                        $grant_amount = get_field('subsidy_amount_max', $grant_id);
                        $grant_categories = get_the_terms($grant_id, 'grant_category');
                        ?>
                        <a href="<?php echo get_permalink($grant_id); ?>" 
                           class="block bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all group">
                            <h3 class="font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                <?php echo esc_html($grant_title); ?>
                            </h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <?php if ($grant_deadline): ?>
                                    <div class="flex items-center">
                                        <span class="mr-2">📅</span>
                                        締切: <?php echo date('Y/m/d', strtotime($grant_deadline)); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($grant_amount): ?>
                                    <div class="flex items-center">
                                        <span class="mr-2">💵</span>
                                        最大: <?php echo number_format($grant_amount); ?>万円
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 関連記事 -->
        <?php if ($related_columns->have_posts()): ?>
            <div class="related-articles mb-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center border-b border-gray-200 pb-3">
                    <span class="mr-2 text-2xl">📚</span>
                    関連記事
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php while ($related_columns->have_posts()): $related_columns->the_post(); ?>
                        <?php get_template_part('template-parts/column/card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 記事一覧へ戻るボタン -->
        <div class="text-center py-8 border-t border-gray-200">
            <a href="<?php echo get_post_type_archive_link('column'); ?>" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-900 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                コラム一覧へ戻る
            </a>
        </div>

    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>

<style>
/* 記事本文スタイル */
.article-content {
    font-size: 16px;
    line-height: 1.8;
    color: #374151;
}

.article-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2.5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.article-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.article-content h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 1.75rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content a {
    color: #059669;
    text-decoration: underline;
}

.article-content a:hover {
    color: #047857;
}

.article-content img {
    border-radius: 0.5rem;
    margin: 2rem auto;
    max-width: 100%;
    height: auto;
}

.article-content blockquote {
    border-left: 4px solid #059669;
    background-color: #f9fafb;
    padding: 1rem 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #4b5563;
}

.article-content code {
    background-color: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875em;
}

.article-content pre {
    background-color: #1f2937;
    color: #f3f4f6;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.article-content pre code {
    background-color: transparent;
    padding: 0;
}

/* テキスト切り詰め */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
