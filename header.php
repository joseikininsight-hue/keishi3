<?php
/**
 * JOSEIKIN INSIGHT - Minna Style Header
 * 助成金インサイト専用 完全黒背景フルスクリーンメニュー
 * 
 * @package Joseikin_Insight_Header
 * @version 5.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#000000">
    
    <?php wp_head(); ?>
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <?php if (is_front_page() || is_home()) : ?>
    <link rel="preload" as="image" href="https://joseikin-insight.com/wp-content/uploads/2025/10/1.png" fetchpriority="high">
    <?php endif; ?>
    
    <!-- Optimized Font Loading -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    </noscript>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <style>
        /* ===============================================
           CRITICAL CSS
           =============================================== */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans JP', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #0a0a0a;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .ji-header-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
    
    <style>
        /* ===============================================
           JOSEIKIN INSIGHT - MINNA STYLE HEADER
           完全黒背景フルスクリーンメニュー - 助成金サイト専用
           =============================================== */
        
        :root {
            /* Brand Colors */
            --ji-black: #000000;
            --ji-white: #ffffff;
            --ji-gray-50: #fafafa;
            --ji-gray-100: #f5f5f5;
            --ji-gray-200: #e5e5e5;
            --ji-gray-300: #d4d4d4;
            --ji-gray-400: #a3a3a3;
            --ji-gray-500: #737373;
            --ji-gray-600: #525252;
            --ji-gray-700: #404040;
            --ji-gray-800: #262626;
            --ji-gray-900: #171717;
            
            /* Accent Colors */
            --ji-accent: #0066ff;
            --ji-success: #10b981;
            --ji-warning: #f59e0b;
            --ji-danger: #ef4444;
            
            /* Menu Colors */
            --ji-menu-bg: #000000;
            --ji-menu-text: #ffffff;
            --ji-menu-text-dim: #999999;
            --ji-menu-border: rgba(255, 255, 255, 0.08);
            --ji-menu-hover: rgba(255, 255, 255, 0.05);
            
            /* Typography */
            --font-primary: 'Noto Sans JP', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-secondary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Font Weights */
            --fw-light: 300;
            --fw-normal: 400;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;
            --fw-extrabold: 800;
            --fw-black: 900;
            
            /* Spacing */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            --space-16: 4rem;
            --space-20: 5rem;
            
            /* Border Radius */
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-2xl: 16px;
            --radius-full: 9999px;
            
            /* Transitions */
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Layout */
            --header-height: 56px;
            --max-width: 1200px;
        }
        
        /* ===============================================
           MAIN HEADER
           =============================================== */
        .ji-header {
            background: var(--ji-white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all var(--transition-base);
        }
        
        .ji-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        
        .ji-container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 var(--space-6);
        }
        
        @media (min-width: 768px) {
            .ji-container {
                padding: 0 var(--space-8);
            }
        }
        
        .ji-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: var(--header-height);
        }
        
        /* ===============================================
           LOGO
           =============================================== */
        .ji-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: opacity var(--transition-fast);
            flex-shrink: 0;
        }
        
        .ji-logo:hover {
            opacity: 0.75;
        }
        
        .ji-logo-image {
            height: 32px;
            width: auto;
            object-fit: contain;
        }
        
        @media (min-width: 768px) {
            .ji-logo-image {
                height: 36px;
            }
        }
        
        /* ===============================================
           DESKTOP NAVIGATION
           =============================================== */
        .ji-nav {
            display: none;
            align-items: center;
            gap: var(--space-2);
            flex: 1;
            justify-content: center;
            margin: 0 var(--space-8);
        }
        
        @media (min-width: 1024px) {
            .ji-nav {
                display: flex;
            }
        }
        
        .ji-nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-4);
            color: var(--ji-gray-700);
            text-decoration: none;
            font-weight: var(--fw-medium);
            font-size: 0.9375rem;
            border-radius: var(--radius-lg);
            transition: all var(--transition-fast);
            white-space: nowrap;
            position: relative;
        }
        
        .ji-nav-link i {
            font-size: 0.875rem;
            opacity: 0.7;
        }
        
        .ji-nav-link:hover {
            color: var(--ji-black);
            background: var(--ji-gray-100);
        }
        
        .ji-nav-link.current {
            color: var(--ji-black);
            background: var(--ji-gray-100);
            font-weight: var(--fw-bold);
        }
        
        /* ===============================================
           HEADER ACTIONS
           =============================================== */
        .ji-actions {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            flex-shrink: 0;
        }
        
        .ji-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-5);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-weight: var(--fw-semibold);
            font-size: 0.9375rem;
            transition: all var(--transition-fast);
            border: none;
            cursor: pointer;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }
        
        .ji-btn-primary {
            background: var(--ji-black);
            color: var(--ji-white);
            display: none;
        }
        
        @media (min-width: 768px) {
            .ji-btn-primary {
                display: inline-flex;
            }
        }
        
        .ji-btn-primary:hover {
            background: var(--ji-gray-800);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .ji-btn-icon {
            width: 44px;
            height: 44px;
            padding: 0;
            color: var(--ji-gray-700);
            background: transparent;
            border: 1px solid var(--ji-gray-200);
            display: none;
        }
        
        @media (min-width: 768px) {
            .ji-btn-icon {
                display: inline-flex;
            }
        }
        
        .ji-btn-icon:hover {
            color: var(--ji-black);
            background: var(--ji-gray-100);
            border-color: var(--ji-gray-300);
        }
        
        /* Mobile Menu Button */
        .ji-mobile-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            color: var(--ji-black);
            background: transparent;
            border: 1px solid var(--ji-gray-200);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        
        @media (min-width: 1024px) {
            .ji-mobile-btn {
                display: none;
            }
        }
        
        .ji-mobile-btn:hover {
            background: var(--ji-gray-100);
            border-color: var(--ji-gray-300);
        }
        
        /* ===============================================
           MOBILE MENU - FULL SCREEN BLACK BACKGROUND
           みんなの銀行スタイル - 助成金サイト専用カスタマイズ
           =============================================== */
        .ji-mobile-overlay {
            position: fixed;
            inset: 0;
            background: var(--ji-menu-bg);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-base);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .ji-mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .ji-mobile-menu {
            min-height: 100vh;
            background: var(--ji-menu-bg);
            color: var(--ji-menu-text);
        }
        
        /* Mobile Header */
        .ji-mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-3) var(--space-4);
            border-bottom: 1px solid var(--ji-menu-border);
            position: sticky;
            top: 0;
            background: var(--ji-menu-bg);
            z-index: 100;
            backdrop-filter: blur(10px);
        }
        
        .ji-mobile-logo {
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }
        
        .ji-mobile-logo-icon {
            width: 22px;
            height: 22px;
            background: var(--ji-white);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--fw-black);
            font-size: 0.6875rem;
            color: var(--ji-black);
            letter-spacing: -0.02em;
        }
        
        .ji-mobile-logo-text {
            font-size: 0.6875rem;
            font-weight: var(--fw-bold);
            color: var(--ji-white);
            letter-spacing: 0.01em;
        }
        
        .ji-mobile-close {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ji-white);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
            font-size: 1rem;
        }
        
        .ji-mobile-close:hover {
            transform: rotate(90deg);
            opacity: 0.7;
        }
        
        /* Mobile Menu Content */
        .ji-mobile-content {
            padding: var(--space-4) var(--space-4) var(--space-8);
        }
        
        /* Menu Section */
        .ji-menu-section {
            margin-bottom: var(--space-5);
        }
        
        .ji-section-label {
            font-size: 0.8125rem;  /* 13px - 2 levels up (9px × 1.44) */
            color: var(--ji-menu-text-dim);
            font-weight: var(--fw-semibold);
            margin-bottom: var(--space-2);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        .ji-section-title {
            font-size: 1.625rem;  /* 26px - 2 levels up (18px × 1.44) */
            color: var(--ji-white);
            font-weight: var(--fw-bold);
            margin-bottom: var(--space-4);
            letter-spacing: -0.02em;
            line-height: 1.3;
        }
        
        /* Menu Grid - 2 Columns Layout */
        /* Force 2-column grid - overrides any external CSS */
        .ji-menu-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: var(--space-4) var(--space-3) !important;
            margin-bottom: var(--space-3) !important;
        }
        
        /* Removed responsive breakpoint - always show 2 columns */
        /* User requested 2-column layout on all screen sizes */
        
        /* Menu Column - Contains title and items */
        .ji-menu-column {
            display: flex !important;
            flex-direction: column !important;
            gap: var(--space-1) !important;
        }
        
        /* Column Title */
        .ji-menu-column-title {
            color: var(--ji-white);
            font-size: 1.0625rem;  /* 17px - 2 levels up (12px × 1.44) */
            font-weight: var(--fw-bold);
            margin-bottom: var(--space-1);
            letter-spacing: -0.01em;
        }
        
        /* Menu Item - Links inside columns */
        .ji-menu-item {
            display: block;
            color: var(--ji-white);
            text-decoration: none;
            font-size: 1.0rem;  /* 16px - 2 levels up (11px × 1.45) */
            font-weight: var(--fw-medium);
            padding: 0.125rem 0;
            transition: all var(--transition-fast);
            position: relative;
            line-height: 1.5;
            padding-left: var(--space-2);
        }
        
        .ji-menu-item::before {
            content: '−';
            position: absolute;
            left: 0;
            opacity: 0;
            transition: all var(--transition-fast);
            font-weight: var(--fw-light);
        }
        
        .ji-menu-item:hover {
            color: var(--ji-menu-text-dim);
            padding-left: var(--space-3);
        }
        
        .ji-menu-item:hover::before {
            opacity: 1;
        }
        
        /* Divider */
        .ji-divider {
            height: 1px;
            background: var(--ji-menu-border);
            margin: var(--space-4) 0;
        }
        
        /* Mobile CTA */
        .ji-mobile-cta {
            background: var(--ji-white);
            color: var(--ji-black);
            padding: var(--space-3) var(--space-5);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: var(--fw-bold);
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-1);
            transition: all var(--transition-fast);
            margin-top: var(--space-4);
            letter-spacing: 0.01em;
        }
        
        .ji-mobile-cta:hover {
            background: var(--ji-gray-100);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.1);
        }
        
        .ji-mobile-cta i {
            font-size: 1.125rem;
        }
        
        /* Stats Display */
        .ji-mobile-stats {
            display: flex;
            justify-content: center;
            gap: var(--space-4);
            margin-top: var(--space-4);
            padding: var(--space-3) 0;
            border-top: 1px solid var(--ji-menu-border);
        }
        
        .ji-stat-item {
            text-align: center;
        }
        
        .ji-stat-number {
            font-size: 1.125rem;
            font-weight: var(--fw-black);
            color: var(--ji-white);
            display: block;
            margin-bottom: 0.125rem;
            letter-spacing: -0.02em;
        }
        
        .ji-stat-label {
            font-size: 0.5625rem;
            color: var(--ji-menu-text-dim);
            font-weight: var(--fw-semibold);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        /* Social Links */
        .ji-social-links {
            display: flex;
            justify-content: center;
            gap: var(--space-2);
            margin-top: var(--space-4);
            padding-top: var(--space-4);
            border-top: 1px solid var(--ji-menu-border);
        }
        
        .ji-social-link {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ji-white);
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-full);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
        }
        
        .ji-social-link:hover {
            background: var(--ji-white);
            color: var(--ji-black);
            transform: translateY(-2px);
        }
        
        /* Footer Info */
        .ji-mobile-footer {
            text-align: center;
            padding: var(--space-4) var(--space-4);
            border-top: 1px solid var(--ji-menu-border);
            background: var(--ji-menu-bg);
        }
        
        .ji-footer-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.08);
            color: var(--ji-white);
            padding: 0.125rem var(--space-2);
            border-radius: var(--radius-full);
            font-size: 0.625rem;
            font-weight: var(--fw-semibold);
            margin-bottom: var(--space-2);
            letter-spacing: 0.05em;
        }
        
        .ji-footer-text {
            font-size: 0.75rem;
            color: var(--ji-menu-text-dim);
            line-height: 1.6;
            font-weight: var(--fw-medium);
        }
        
        .ji-footer-text strong {
            color: var(--ji-white);
            font-weight: var(--fw-bold);
        }
        
        /* ===============================================
           SCROLLBAR STYLING
           =============================================== */
        .ji-mobile-overlay::-webkit-scrollbar {
            width: 8px;
        }
        
        .ji-mobile-overlay::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .ji-mobile-overlay::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: var(--radius-full);
        }
        
        .ji-mobile-overlay::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        
        /* ===============================================
           SEARCH BAR
           =============================================== */
        .ji-search-bar {
            background: var(--ji-white);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            display: none;
            transform: translateY(-20px);
            opacity: 0;
            transition: all var(--transition-base);
        }
        
        .ji-search-bar.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }
        
        .ji-search-form {
            padding: var(--space-8);
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }
        
        @media (min-width: 768px) {
            .ji-search-form {
                flex-direction: row;
                align-items: center;
            }
        }
        
        .ji-search-input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .ji-search-input {
            width: 100%;
            padding: var(--space-4) var(--space-6) var(--space-4) 3.5rem;
            border: 1px solid var(--ji-gray-200);
            border-radius: var(--radius-xl);
            font-size: 0.9375rem;
            transition: all var(--transition-fast);
            background: var(--ji-white);
            color: var(--ji-black);
            font-weight: var(--fw-normal);
            font-family: var(--font-primary);
        }
        
        .ji-search-input:focus {
            outline: none;
            border-color: var(--ji-gray-400);
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.04);
        }
        
        .ji-search-input::placeholder {
            color: var(--ji-gray-400);
        }
        
        .ji-search-icon {
            position: absolute;
            left: var(--space-5);
            top: 50%;
            transform: translateY(-50%);
            color: var(--ji-gray-400);
            font-size: 1rem;
        }
        
        .ji-search-filters {
            display: flex;
            gap: var(--space-3);
            flex-wrap: wrap;
        }
        
        .ji-search-select {
            padding: var(--space-4) var(--space-5);
            border: 1px solid var(--ji-gray-200);
            border-radius: var(--radius-xl);
            background: var(--ji-white);
            color: var(--ji-black);
            font-size: 0.875rem;
            font-weight: var(--fw-medium);
            min-width: 150px;
            transition: all var(--transition-fast);
            cursor: pointer;
            font-family: var(--font-primary);
        }
        
        .ji-search-select:focus {
            outline: none;
            border-color: var(--ji-gray-400);
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.04);
        }
        
        .ji-search-submit {
            background: var(--ji-black);
            color: var(--ji-white);
            border: 1px solid var(--ji-black);
            padding: var(--space-4) var(--space-8);
            border-radius: var(--radius-xl);
            font-weight: var(--fw-semibold);
            font-size: 0.875rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            white-space: nowrap;
            font-family: var(--font-primary);
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .ji-search-submit:hover {
            background: var(--ji-gray-800);
            border-color: var(--ji-gray-800);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* ===============================================
           ACCESSIBILITY
           =============================================== */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        select:focus-visible {
            outline: 2px solid var(--ji-accent);
            outline-offset: 2px;
        }
        
        body.menu-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        
        /* ===============================================
           UTILITY CLASSES
           =============================================== */
        .ji-hidden {
            display: none !important;
        }
        
        .ji-sr-only {
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
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Main Header -->
<header id="ji-site-header" class="ji-header">
    <div class="ji-container">
        <div class="ji-header-inner">
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-logo" aria-label="<?php bloginfo('name'); ?> ホームページへ">
                <img src="https://joseikin-insight.com/wp-content/uploads/2025/11/cropped-cropped-名称未設定のデザイン１.png" 
                     alt="<?php bloginfo('name'); ?>" 
                     class="ji-logo-image"
                     width="200"
                     height="60"
                     loading="eager"
                     fetchpriority="high"
                     decoding="async">
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="ji-nav" role="navigation" aria-label="メインナビゲーション">
                <?php
                $current_url = home_url(add_query_arg(null, null));
                $home_url = home_url('/');
                $grants_url = get_post_type_archive_link('grant');
                $diagnosis_url = home_url('/subsidy-diagnosis/');
                $how_to_use_url = home_url('/how-to-use/');
                $contact_url = home_url('/contact/');
                
                $menu_items = array(
                    array('url' => $home_url, 'title' => 'ホーム', 'icon' => 'fas fa-home', 'current' => ($current_url === $home_url)),
                    array('url' => $how_to_use_url, 'title' => '使い方', 'icon' => 'fas fa-book-open', 'current' => (strpos($current_url, '/how-to-use/') !== false)),
                    array('url' => $grants_url, 'title' => '助成金一覧', 'icon' => 'fas fa-list-ul', 'current' => (strpos($current_url, 'grants') !== false || is_post_type_archive('grant') || is_singular('grant'))),
                    array('url' => $diagnosis_url, 'title' => '診断システム', 'icon' => 'fas fa-stethoscope', 'current' => (strpos($current_url, '/subsidy-diagnosis/') !== false)),
                    array('url' => $contact_url, 'title' => 'お問い合わせ', 'icon' => 'fas fa-envelope', 'current' => (strpos($current_url, '/contact/') !== false)),
                );
                
                foreach ($menu_items as $item) {
                    $class = 'ji-nav-link';
                    if ($item['current']) $class .= ' current';
                    echo '<a href="' . esc_url($item['url']) . '" class="' . $class . '">';
                    echo '<i class="' . esc_attr($item['icon']) . '"></i>';
                    echo '<span>' . esc_html($item['title']) . '</span>';
                    echo '</a>';
                }
                ?>
            </nav>
            
            <!-- Header Actions -->
            <div class="ji-actions">
                <!-- Search Toggle -->
                <button type="button" id="ji-search-toggle" class="ji-btn ji-btn-icon" title="検索" aria-label="検索を開く">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- CTA Button -->
                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="ji-btn ji-btn-primary">
                    <i class="fas fa-search"></i>
                    <span>助成金を探す</span>
                </a>
                
                <!-- Mobile Menu Button -->
                <button type="button" id="ji-mobile-menu-btn" class="ji-mobile-btn" aria-label="メニューを開く" aria-expanded="false">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div id="ji-search-bar" class="ji-search-bar ji-hidden" role="search">
            <form id="ji-search-form" class="ji-search-form">
                <div class="ji-search-input-wrapper">
                    <input type="text" 
                           id="ji-search-input"
                           name="search" 
                           placeholder="助成金名、実施組織名、キーワードで検索..." 
                           class="ji-search-input"
                           autocomplete="off"
                           aria-label="検索キーワード">
                    <i class="fas fa-search ji-search-icon" aria-hidden="true"></i>
                </div>
                
                <div class="ji-search-filters">
                    <select name="category" class="ji-search-select" aria-label="カテゴリー選択">
                        <option value="">すべてのカテゴリー</option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'grant_category',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 30
                        ));
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->slug) . '">';
                                echo esc_html($category->name) . ' (' . $category->count . ')';
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <select name="prefecture" class="ji-search-select" aria-label="都道府県選択">
                        <option value="">すべての都道府県</option>
                        <?php
                        $prefectures = get_terms(array(
                            'taxonomy' => 'grant_prefecture',
                            'hide_empty' => true,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ));
                        if ($prefectures && !is_wp_error($prefectures)) {
                            foreach ($prefectures as $prefecture) {
                                echo '<option value="' . esc_attr($prefecture->slug) . '">';
                                echo esc_html($prefecture->name) . ' (' . $prefecture->count . ')';
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <button type="submit" class="ji-search-submit">
                        <i class="fas fa-search"></i>
                        <span>検索</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu - Full Screen Black Background -->
<div id="ji-mobile-overlay" class="ji-mobile-overlay" role="dialog" aria-modal="true" aria-label="モバイルメニュー">
    <div class="ji-mobile-menu">
        <!-- Mobile Header -->
        <div class="ji-mobile-header">
            <div class="ji-mobile-logo">
                <div class="ji-mobile-logo-icon">JI</div>
                <div class="ji-mobile-logo-text">Joseikin Insight</div>
            </div>
            <button type="button" id="ji-mobile-close" class="ji-mobile-close" aria-label="メニューを閉じる">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu Content -->
        <div class="ji-mobile-content">
            <!-- Main Services Section -->
            <div class="ji-menu-section">
                <div class="ji-section-label">Services</div>
                <div class="ji-section-title">サービス一覧</div>
                
                <div class="ji-menu-grid">
                    <div class="ji-menu-column">
                        <div class="ji-menu-column-title">助成金を探す</div>
                        <a href="<?php echo esc_url($grants_url); ?>" class="ji-menu-item">助成金一覧</a>
                        <a href="<?php echo esc_url($grants_url . '?status=active'); ?>" class="ji-menu-item">募集中の助成金</a>
                        <a href="<?php echo esc_url($grants_url . '?orderby=new'); ?>" class="ji-menu-item">新着助成金</a>
                        <a href="<?php echo esc_url($grants_url . '?orderby=popular'); ?>" class="ji-menu-item">人気の助成金</a>
                    </div>
                    
                    <div class="ji-menu-column">
                        <div class="ji-menu-column-title">カテゴリーから探す</div>
                        <?php
                        $top_categories = get_terms(array(
                            'taxonomy' => 'grant_category',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 4
                        ));
                        if ($top_categories && !is_wp_error($top_categories)) {
                            foreach ($top_categories as $category) {
                                echo '<a href="' . esc_url(get_term_link($category)) . '" class="ji-menu-item">';
                                echo esc_html($category->name);
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="ji-menu-column">
                        <div class="ji-menu-column-title">地域から探す</div>
                        <?php
                        $top_prefectures = get_terms(array(
                            'taxonomy' => 'grant_prefecture',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 4
                        ));
                        if ($top_prefectures && !is_wp_error($top_prefectures)) {
                            foreach ($top_prefectures as $prefecture) {
                                echo '<a href="' . esc_url(get_term_link($prefecture)) . '" class="ji-menu-item">';
                                echo esc_html($prefecture->name);
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="ji-menu-column">
                        <div class="ji-menu-column-title">対象者から探す</div>
                        <a href="<?php echo esc_url($grants_url . '?target=individual'); ?>" class="ji-menu-item">個人向け</a>
                        <a href="<?php echo esc_url($grants_url . '?target=business'); ?>" class="ji-menu-item">法人・事業者向け</a>
                        <a href="<?php echo esc_url($grants_url . '?target=npo'); ?>" class="ji-menu-item">NPO・団体向け</a>
                        <a href="<?php echo esc_url($grants_url . '?target=startup'); ?>" class="ji-menu-item">スタートアップ向け</a>
                    </div>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- Tools Section -->
            <div class="ji-menu-section">
                <div class="ji-section-label">Tools</div>
                <div class="ji-section-title">便利なツール</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url($diagnosis_url); ?>" class="ji-menu-item" style="font-weight: 700;">助成金診断システム</a>
                    <a href="<?php echo esc_url(home_url('/calculator/')); ?>" class="ji-menu-item" style="font-weight: 700;">助成金計算ツール</a>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- Guide Section -->
            <div class="ji-menu-section">
                <div class="ji-section-label">Guide</div>
                <div class="ji-section-title">初めての方へ</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url($home_url); ?>" class="ji-menu-item">助成金インサイトとは</a>
                    <a href="<?php echo esc_url($how_to_use_url); ?>" class="ji-menu-item">使い方ガイド</a>
                    <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="ji-menu-item">よくある質問</a>
                    <a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="ji-menu-item">用語集</a>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- Knowledge Base -->
            <div class="ji-menu-section">
                <div class="ji-section-label">Knowledge</div>
                <div class="ji-section-title">助成金の基礎知識</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url(home_url('/knowledge/')); ?>" class="ji-menu-item" style="font-weight: 700;">助成金とは</a>
                    <a href="<?php echo esc_url(home_url('/knowledge/how-to-apply/')); ?>" class="ji-menu-item" style="font-weight: 700;">申請方法</a>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- Blog & News -->
            <div class="ji-menu-section">
                <div class="ji-section-label">News</div>
                <div class="ji-section-title">ブログ・お知らせ</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="ji-menu-item" style="font-weight: 700;">ブログ</a>
                    <a href="<?php echo esc_url(home_url('/news/')); ?>" class="ji-menu-item" style="font-weight: 700;">お知らせ</a>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- Support -->
            <div class="ji-menu-section">
                <div class="ji-section-label">Support</div>
                <div class="ji-section-title">サポート</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url($contact_url); ?>" class="ji-menu-item">お問い合わせ</a>
                    <a href="<?php echo esc_url(home_url('/support/')); ?>" class="ji-menu-item">ヘルプセンター</a>
                </div>
            </div>
            
            <div class="ji-divider"></div>
            
            <!-- About -->
            <div class="ji-menu-section">
                <div class="ji-section-label">About</div>
                <div class="ji-section-title">運営について</div>
                
                <div class="ji-menu-grid">
                    <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-menu-item">運営者情報</a>
                    <a href="<?php echo esc_url(home_url('/privacy/')); ?>" class="ji-menu-item">プライバシーポリシー</a>
                    <a href="<?php echo esc_url(home_url('/terms/')); ?>" class="ji-menu-item">利用規約</a>
                    <a href="<?php echo esc_url(home_url('/sitemap/')); ?>" class="ji-menu-item">サイトマップ</a>
                </div>
            </div>
            
            <!-- CTA Button -->
            <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="ji-mobile-cta">
                <i class="fas fa-search"></i>
                <span>助成金を探す</span>
            </a>
            
            <!-- Stats Display -->
            <?php
            $stats = gi_get_cached_stats();
            if ($stats && !empty($stats['total_grants'])):
            ?>
            <div class="ji-mobile-stats">
                <div class="ji-stat-item">
                    <span class="ji-stat-number"><?php echo number_format($stats['total_grants']); ?></span>
                    <span class="ji-stat-label">Total Grants</span>
                </div>
                <?php if (!empty($stats['active_grants'])): ?>
                <div class="ji-stat-item">
                    <span class="ji-stat-number"><?php echo number_format($stats['active_grants']); ?></span>
                    <span class="ji-stat-label">Active Now</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($stats['total_views'])): ?>
                <div class="ji-stat-item">
                    <span class="ji-stat-number"><?php echo number_format($stats['total_views']); ?></span>
                    <span class="ji-stat-label">Total Views</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Social Links -->
            <div class="ji-social-links">
                <a href="https://twitter.com/joseikin_insight" class="ji-social-link" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://facebook.com/joseikin.insight" class="ji-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://instagram.com/joseikin_insight" class="ji-social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.youtube.com/@joseikin-insight" class="ji-social-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://note.com/joseikin_insight" class="ji-social-link" aria-label="Note" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-sticky-note"></i>
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="ji-mobile-footer">
            <div class="ji-footer-badge">POWERED BY AI</div>
            <p class="ji-footer-text">
                <strong>みんなの助成金情報プラットフォーム</strong><br>
                最新の助成金情報を随時更新中
            </p>
        </div>
    </div>
</div>

<script>
/**
 * Joseikin Insight - Minna Style Header JavaScript
 * 完全最適化版
 */
(function() {
    'use strict';
    
    // Elements Cache
    const elements = {
        header: document.getElementById('ji-site-header'),
        searchToggle: document.getElementById('ji-search-toggle'),
        searchBar: document.getElementById('ji-search-bar'),
        searchForm: document.getElementById('ji-search-form'),
        searchInput: document.getElementById('ji-search-input'),
        mobileMenuBtn: document.getElementById('ji-mobile-menu-btn'),
        mobileOverlay: document.getElementById('ji-mobile-overlay'),
        mobileClose: document.getElementById('ji-mobile-close')
    };
    
    // State Management
    const state = {
        lastScrollTop: 0,
        isSearchOpen: false,
        isMobileMenuOpen: false,
        scrollTimeout: null
    };
    
    /**
     * Scroll Handler
     */
    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 50) {
            elements.header?.classList.add('scrolled');
        } else {
            elements.header?.classList.remove('scrolled');
        }
        
        state.lastScrollTop = scrollTop;
    }
    
    window.addEventListener('scroll', function() {
        if (state.scrollTimeout) clearTimeout(state.scrollTimeout);
        state.scrollTimeout = setTimeout(handleScroll, 10);
    }, { passive: true });
    
    /**
     * Search Toggle
     */
    function toggleSearch() {
        state.isSearchOpen = !state.isSearchOpen;
        
        if (state.isSearchOpen) {
            elements.searchBar?.classList.add('show');
            elements.searchBar?.classList.remove('ji-hidden');
            setTimeout(() => elements.searchInput?.focus(), 200);
            
            if (elements.searchToggle) {
                elements.searchToggle.innerHTML = '<i class="fas fa-times"></i>';
                elements.searchToggle.title = '閉じる';
                elements.searchToggle.setAttribute('aria-label', '検索を閉じる');
            }
        } else {
            elements.searchBar?.classList.remove('show');
            setTimeout(() => elements.searchBar?.classList.add('ji-hidden'), 300);
            
            if (elements.searchToggle) {
                elements.searchToggle.innerHTML = '<i class="fas fa-search"></i>';
                elements.searchToggle.title = '検索';
                elements.searchToggle.setAttribute('aria-label', '検索を開く');
            }
        }
    }
    
    elements.searchToggle?.addEventListener('click', toggleSearch);
    
    /**
     * Search Form Submission
     */
    if (elements.searchForm) {
        elements.searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.ji-search-submit');
            if (submitBtn) {
                submitBtn.classList.add('ji-loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>検索中</span>';
            }
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                if (value.trim()) params.append(key, value);
            }
            
            const archiveUrl = '<?php echo esc_url(get_post_type_archive_link("grant")); ?>';
            const searchUrl = archiveUrl + (params.toString() ? '?' + params.toString() : '');
            
            setTimeout(() => window.location.href = searchUrl, 300);
        });
    }
    
    /**
     * Mobile Menu Functions
     */
    function openMobileMenu() {
        state.isMobileMenuOpen = true;
        elements.mobileOverlay?.classList.add('show');
        document.body.classList.add('menu-open');
        elements.mobileMenuBtn?.setAttribute('aria-expanded', 'true');
    }
    
    function closeMobileMenu() {
        state.isMobileMenuOpen = false;
        elements.mobileOverlay?.classList.remove('show');
        document.body.classList.remove('menu-open');
        elements.mobileMenuBtn?.setAttribute('aria-expanded', 'false');
    }
    
    elements.mobileMenuBtn?.addEventListener('click', openMobileMenu);
    elements.mobileClose?.addEventListener('click', closeMobileMenu);
    
    /**
     * Keyboard Navigation
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (state.isMobileMenuOpen) closeMobileMenu();
            else if (state.isSearchOpen) toggleSearch();
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (!state.isSearchOpen) toggleSearch();
        }
    });
    
    /**
     * Adjust Main Content Margin
     */
    function adjustMainContentMargin() {
        const mainContent = document.getElementById('main-content');
        if (mainContent && elements.header) {
            const headerHeight = elements.header.offsetHeight;
            mainContent.style.marginTop = (headerHeight + 24) + 'px';
        }
    }
    
    /**
     * Initialization
     */
    function init() {
        setTimeout(adjustMainContentMargin, 100);
        window.addEventListener('resize', adjustMainContentMargin);
        
        console.log('[✓] Joseikin Insight Header initialized');
        console.log('[✓] Minna Style - Full Screen Menu Ready');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    /**
     * Global API
     */
    window.JoseikinHeader = {
        toggleSearch,
        openMobileMenu,
        closeMobileMenu,
        isSearchOpen: () => state.isSearchOpen,
        isMobileMenuOpen: () => state.isMobileMenuOpen,
        adjustMainContentMargin
    };
    
})();
</script>

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "url": "<?php echo esc_url(home_url('/')); ?>",
    "description": "<?php echo esc_js(get_bloginfo('description')); ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "<?php echo esc_url(get_post_type_archive_link('grant')); ?>?search={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

<main id="main-content" class="ji-main-content">