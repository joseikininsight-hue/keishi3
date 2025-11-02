<?php
/**
 * Archive Column Template
 * コラム記事一覧ページ
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 1.0.0
 */

get_header();

// カテゴリ一覧を取得
$categories = gi_get_column_categories(true);
$current_category = get_queried_object();
$is_category = is_tax('column_category');
$is_tag = is_tax('column_tag');
?>

<div class="archive-column bg-white min-h-screen">
    
    <!-- ヘッダーセクション -->
    <header class="archive-header bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 max-w-7xl py-12">
            
            <!-- パンくずリスト -->
            <nav class="breadcrumb text-sm text-gray-600 mb-6">
                <ol class="flex items-center space-x-2">
                    <li><a href="<?php echo home_url('/'); ?>" class="hover:text-primary">ホーム</a></li>
                    <li><span class="text-gray-400">&gt;</span></li>
                    <li><a href="<?php echo get_post_type_archive_link('column'); ?>" class="hover:text-primary">コラム</a></li>
                    <?php if ($is_category || $is_tag): ?>
                        <li><span class="text-gray-400">&gt;</span></li>
                        <li class="text-gray-900" aria-current="page"><?php single_term_title(); ?></li>
                    <?php endif; ?>
                </ol>
            </nav>

            <!-- タイトル -->
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-3">📝</span>
                <h1 class="text-h1 font-bold text-gray-900">
                    <?php
                    if ($is_category || $is_tag) {
                        single_term_title();
                    } else {
                        echo '補助金コラム';
                    }
                    ?>
                </h1>
            </div>

            <!-- 説明文 -->
            <?php if ($is_category && $current_category->description): ?>
                <p class="text-gray-600 max-w-3xl">
                    <?php echo esc_html($current_category->description); ?>
                </p>
            <?php elseif (!$is_category && !$is_tag): ?>
                <p class="text-gray-600 max-w-3xl">
                    補助金活用のヒントやノウハウ、最新情報をお届けします。
                </p>
            <?php endif; ?>

        </div>
    </header>

    <!-- メインコンテンツ -->
    <div class="container mx-auto px-4 max-w-7xl py-12">
        
        <!-- カテゴリフィルタ -->
        <?php if (!empty($categories) && !$is_tag): ?>
            <div class="category-filter mb-8">
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo get_post_type_archive_link('column'); ?>" 
                       class="filter-btn <?php echo (!$is_category) ? 'active' : ''; ?>">
                        すべて
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="<?php echo get_term_link($category); ?>" 
                           class="filter-btn <?php echo ($is_category && $current_category->term_id === $category->term_id) ? 'active' : ''; ?>">
                            <?php echo gi_get_category_icon($category->slug); ?>
                            <?php echo esc_html($category->name); ?>
                            <span class="count">(<?php echo $category->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 記事グリッド -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- メインコンテンツ -->
            <div class="lg:col-span-8">
                <?php if (have_posts()): ?>
                    <div class="article-grid grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <?php while (have_posts()): the_post(); ?>
                            <?php get_template_part('template-parts/column/card'); ?>
                        <?php endwhile; ?>
                    </div>

                    <!-- ページネーション -->
                    <div class="pagination flex justify-center space-x-2">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '&laquo; 前へ',
                            'next_text' => '次へ &raquo;',
                            'type' => 'list',
                            'class' => 'flex space-x-2',
                        ));
                        ?>
                    </div>

                <?php else: ?>
                    <div class="no-posts text-center py-16">
                        <span class="text-6xl mb-4 block">📄</span>
                        <p class="text-xl text-gray-600 mb-4">記事が見つかりませんでした</p>
                        <a href="<?php echo get_post_type_archive_link('column'); ?>" 
                           class="inline-block px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors">
                            すべての記事を見る
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- サイドバー -->
            <aside class="lg:col-span-4">
                <?php get_template_part('template-parts/column/sidebar'); ?>
            </aside>

        </div>

    </div>

</div>

<?php get_footer(); ?>

<style>
/* フィルタボタンスタイル */
.filter-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
}

.filter-btn:hover {
    background-color: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

.filter-btn.active {
    background-color: #059669;
    border-color: #059669;
    color: #ffffff;
}

.filter-btn .count {
    margin-left: 0.375rem;
    font-size: 0.75rem;
    opacity: 0.8;
}

/* ページネーション */
.pagination ul {
    display: flex;
    list-style: none;
    gap: 0.5rem;
}

.pagination a,
.pagination span {
    display: inline-block;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.pagination a {
    color: #374151;
    background-color: #ffffff;
}

.pagination a:hover {
    background-color: #f9fafb;
    border-color: #059669;
    color: #059669;
}

.pagination .current {
    background-color: #059669;
    border-color: #059669;
    color: #ffffff;
    font-weight: 600;
}
</style>
