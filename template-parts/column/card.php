<?php
/**
 * Template Part: Column Card
 * コラム記事カード（共通パーツ）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 現在の投稿情報を取得
$post_id = get_the_ID();
$read_time = get_field('estimated_read_time', $post_id);
$view_count = get_field('view_count', $post_id);
$difficulty = get_field('difficulty_level', $post_id);
$categories = get_the_terms($post_id, 'column_category');
$related_grants = get_field('related_grants', $post_id);
?>

<article class="column-card bg-white rounded-lg shadow hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col h-full">
    
    <!-- アイキャッチ画像 -->
    <div class="card-thumbnail relative">
        <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden group">
            <?php if (has_post_thumbnail()): ?>
                <img 
                    src="<?php echo get_the_post_thumbnail_url($post_id, 'medium_large'); ?>" 
                    alt="<?php the_title_attribute(); ?>"
                    class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                    loading="lazy">
            <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-5xl text-gray-400">📝</span>
                </div>
            <?php endif; ?>
            
            <!-- オーバーレイ効果 -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
        </a>

        <!-- カテゴリバッジ -->
        <?php if ($categories && !is_wp_error($categories)): ?>
            <?php $cat = $categories[0]; ?>
            <div class="absolute top-3 left-3">
                <a href="<?php echo get_term_link($cat); ?>" 
                   class="bg-primary text-white px-3 py-1 rounded-full text-xs font-medium hover:bg-primary-dark transition-colors shadow-md">
                    <?php echo esc_html($cat->name); ?>
                </a>
            </div>
        <?php endif; ?>

        <!-- 難易度バッジ（右上） -->
        <?php if ($difficulty): ?>
            <div class="absolute top-3 right-3">
                <?php
                $difficulty_colors = array(
                    'beginner' => 'bg-success',
                    'intermediate' => 'bg-warning',
                    'advanced' => 'bg-error',
                );
                $difficulty_icons = array(
                    'beginner' => '🌱',
                    'intermediate' => '📈',
                    'advanced' => '🎯',
                );
                $bg_color = isset($difficulty_colors[$difficulty]) ? $difficulty_colors[$difficulty] : 'bg-gray-500';
                $icon = isset($difficulty_icons[$difficulty]) ? $difficulty_icons[$difficulty] : '';
                ?>
                <span class="<?php echo $bg_color; ?> text-white px-2 py-1 rounded text-xs font-medium shadow-md">
                    <?php echo $icon; ?> <?php echo gi_get_difficulty_label($difficulty); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- カード本文 -->
    <div class="card-body p-4 flex flex-col flex-grow">
        
        <!-- タイトル -->
        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight hover:text-primary transition-colors">
            <a href="<?php the_permalink(); ?>" class="line-clamp-2">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- 要約 -->
        <p class="text-sm text-gray-600 mb-3 line-clamp-3 flex-grow">
            <?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?>
        </p>

        <!-- メタ情報 -->
        <div class="card-meta flex items-center justify-between text-xs text-gray-500 pb-3 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <span class="flex items-center" title="投稿日">
                    📅 <?php echo get_the_date('Y.m.d'); ?>
                </span>
                <?php if ($read_time): ?>
                    <span class="flex items-center" title="読了時間">
                        ⏱️ <?php echo esc_html($read_time); ?>分
                    </span>
                <?php endif; ?>
            </div>
            <span class="flex items-center font-medium" title="閲覧数">
                👁️ <?php echo number_format($view_count); ?>
            </span>
        </div>

        <!-- 関連補助金リンク -->
        <?php if ($related_grants && is_array($related_grants) && count($related_grants) > 0): ?>
            <div class="related-grants mt-3">
                <div class="flex items-start">
                    <span class="text-xs text-gray-500 mr-2 flex-shrink-0">💰 関連:</span>
                    <div class="flex flex-wrap gap-1">
                        <?php 
                        // 最大2件まで表示
                        $display_grants = array_slice($related_grants, 0, 2);
                        foreach ($display_grants as $grant_id): 
                            $grant_title = get_the_title($grant_id);
                            // タイトルが長い場合は短縮
                            $short_title = mb_strlen($grant_title, 'UTF-8') > 20 
                                ? mb_substr($grant_title, 0, 20, 'UTF-8') . '...' 
                                : $grant_title;
                        ?>
                            <a href="<?php echo get_permalink($grant_id); ?>" 
                               class="text-xs text-primary hover:underline hover:text-primary-dark"
                               title="<?php echo esc_attr($grant_title); ?>">
                                <?php echo esc_html($short_title); ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (count($related_grants) > 2): ?>
                            <span class="text-xs text-gray-400">他<?php echo count($related_grants) - 2; ?>件</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- タグ（オプション） -->
        <?php
        $tags = get_the_terms($post_id, 'column_tag');
        if ($tags && !is_wp_error($tags) && count($tags) > 0):
        ?>
            <div class="card-tags mt-3 flex flex-wrap gap-1">
                <?php 
                // 最大3件まで表示
                $display_tags = array_slice($tags, 0, 3);
                foreach ($display_tags as $tag): 
                ?>
                    <a href="<?php echo get_term_link($tag); ?>" 
                       class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition-colors">
                        #<?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</article>

<style>
/* カードホバーエフェクト */
.column-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.column-card:hover {
    transform: translateY(-4px);
}

/* テキスト切り詰め（2行） */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* テキスト切り詰め（3行） */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
