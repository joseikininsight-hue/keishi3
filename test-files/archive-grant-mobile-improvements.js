/**
 * Archive Grant - Mobile Filter Improvements
 * モバイルフィルター改善JavaScript
 * 
 * このファイルの内容を archive-grant.php の JavaScript セクションに統合してください
 */

// ===== elements オブジェクトに追加する項目 =====
// 以下を elements オブジェクトに追加:
/*
    // モバイルフィルター
    mobileFilterToggle: document.getElementById('mobile-filter-toggle'),
    mobileFilterClose: document.getElementById('mobile-filter-close'),
    filterPanel: document.getElementById('filter-panel'),
    mobileFilterCount: document.getElementById('mobile-filter-count'),
*/

// ===== 新しい関数を追加 =====

function openMobileFilter() {
    if (elements.filterPanel) {
        elements.filterPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (elements.mobileFilterToggle) {
            elements.mobileFilterToggle.setAttribute('aria-expanded', 'true');
        }
        console.log('📱 Mobile filter opened');
    }
}

function closeMobileFilter() {
    if (elements.filterPanel) {
        elements.filterPanel.classList.remove('active');
        document.body.style.overflow = '';
        if (elements.mobileFilterToggle) {
            elements.mobileFilterToggle.setAttribute('aria-expanded', 'false');
        }
        console.log('📱 Mobile filter closed');
    }
}

// ===== setupEventListeners() 関数の最後に追加 =====
/*
    // モバイルフィルターイベント
    if (elements.mobileFilterToggle) {
        elements.mobileFilterToggle.addEventListener('click', openMobileFilter);
    }
    
    if (elements.mobileFilterClose) {
        elements.mobileFilterClose.addEventListener('click', closeMobileFilter);
    }
    
    // フィルターパネル外クリックで閉じる（モバイルのみ）
    if (elements.filterPanel) {
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (elements.filterPanel.classList.contains('active') && 
                    !elements.filterPanel.contains(e.target) && 
                    !elements.mobileFilterToggle.contains(e.target)) {
                    closeMobileFilter();
                }
            }
        });
    }
    
    // ESCキーでフィルターを閉じる
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && window.innerWidth <= 768) {
            if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                closeMobileFilter();
            }
        }
    });
*/

// ===== updateActiveFiltersDisplay() 関数内の tags.length === 0 の分岐に追加 =====
/*
    if (tags.length === 0) {
        elements.activeFilters.style.display = 'none';
        elements.resetAllFiltersBtn.style.display = 'none';
        // 以下を追加:
        if (elements.mobileFilterCount) {
            elements.mobileFilterCount.style.display = 'none';
        }
        return;
    }
*/

// ===== updateActiveFiltersDisplay() 関数内の tags.length > 0 の分岐に追加 =====
/*
    elements.activeFilters.style.display = 'flex';
    elements.resetAllFiltersBtn.style.display = 'flex';
    
    // 以下を追加:
    // モバイルフィルターバッジ更新
    if (elements.mobileFilterCount) {
        elements.mobileFilterCount.textContent = tags.length;
        elements.mobileFilterCount.style.display = 'flex';
    }
*/

// ===== displayGrants() 関数の最後に追加 =====
/*
    // モバイルでフィルター適用後は自動的にパネルを閉じる
    if (window.innerWidth <= 768) {
        closeMobileFilter();
    }
*/

// ===== loadGrants() 関数の開始時に追加（パフォーマンス測定） =====
/*
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        
        // 以下を追加:
        // パフォーマンス測定開始
        if (window.performance && window.performance.mark) {
            performance.mark('grants-load-start');
        }
        
        showLoading(true);
        // ... 残りのコード
*/

// ===== loadGrants() 関数の finally ブロックに追加 =====
/*
        .finally(() => {
            state.isLoading = false;
            showLoading(false);
            
            // 以下を追加:
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
*/

// ===== displayGrants() 関数の最適化（仮想スクロール対応） =====
/*
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
}
*/

console.log('✅ Mobile filter improvements script loaded');
