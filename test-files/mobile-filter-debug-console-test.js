/**
 * モバイルフィルター デバッグ & テスト用コンソールコード
 * 
 * 使い方:
 * 1. ブラウザで該当ページを開く
 * 2. F12キーで開発者ツールを開く
 * 3. Consoleタブを選択
 * 4. このコードをコピー＆ペーストして実行
 */

console.log('🔧 モバイルフィルター デバッグモード開始');

// ===== 1. 要素の存在確認 =====
console.log('\n📋 要素の存在確認:');
const filterToggle = document.getElementById('mobile-filter-toggle');
const filterClose = document.getElementById('mobile-filter-close');
const filterPanel = document.getElementById('filter-panel');
const filterOverlay = document.getElementById('filter-panel-overlay');

console.log('フィルターボタン:', filterToggle ? '✅ 存在する' : '❌ 存在しない', filterToggle);
console.log('クローズボタン:', filterClose ? '✅ 存在する' : '❌ 存在しない', filterClose);
console.log('フィルターパネル:', filterPanel ? '✅ 存在する' : '❌ 存在しない', filterPanel);
console.log('オーバーレイ:', filterOverlay ? '✅ 存在する' : '❌ 存在しない', filterOverlay);

// ===== 2. 現在の状態確認 =====
console.log('\n📊 現在の状態:');
if (filterPanel) {
    console.log('パネルのクラス:', filterPanel.className);
    console.log('パネルが開いている:', filterPanel.classList.contains('active') ? '✅ はい' : '❌ いいえ');
}
if (filterOverlay) {
    console.log('オーバーレイのクラス:', filterOverlay.className);
    console.log('オーバーレイが表示:', filterOverlay.classList.contains('active') ? '✅ はい' : '❌ いいえ');
}

// ===== 3. イベントリスナー確認 =====
console.log('\n🎯 イベントリスナーの確認:');
if (filterToggle) {
    const listeners = getEventListeners(filterToggle);
    console.log('フィルターボタンのイベント:', listeners);
}
if (filterClose) {
    const listeners = getEventListeners(filterClose);
    console.log('クローズボタンのイベント:', listeners);
}
if (filterOverlay) {
    const listeners = getEventListeners(filterOverlay);
    console.log('オーバーレイのイベント:', listeners);
}

// ===== 4. 手動でパネルを開く関数 =====
window.testOpenFilter = function() {
    console.log('\n🔓 パネルを開きます...');
    if (filterPanel) {
        filterPanel.classList.add('active');
        console.log('✅ パネルに active クラスを追加');
    }
    if (filterOverlay) {
        filterOverlay.classList.add('active');
        console.log('✅ オーバーレイに active クラスを追加');
    }
    document.body.style.overflow = 'hidden';
    console.log('✅ body のスクロールをロック');
};

// ===== 5. 手動でパネルを閉じる関数 =====
window.testCloseFilter = function() {
    console.log('\n🔒 パネルを閉じます...');
    if (filterPanel) {
        filterPanel.classList.remove('active');
        console.log('✅ パネルから active クラスを削除');
    }
    if (filterOverlay) {
        filterOverlay.classList.remove('active');
        console.log('✅ オーバーレイから active クラスを削除');
    }
    document.body.style.overflow = '';
    console.log('✅ body のスクロールロックを解除');
};

// ===== 6. クリックイベントを強制的に追加 =====
window.forceAddClickEvents = function() {
    console.log('\n🔧 クリックイベントを強制的に追加...');
    
    // フィルターボタン
    if (filterToggle) {
        filterToggle.addEventListener('click', function(e) {
            console.log('📱 フィルターボタンがクリックされました');
            e.stopPropagation();
            window.testOpenFilter();
        });
        console.log('✅ フィルターボタンにイベント追加');
    }
    
    // クローズボタン
    if (filterClose) {
        filterClose.addEventListener('click', function(e) {
            console.log('❌ クローズボタンがクリックされました');
            e.stopPropagation();
            window.testCloseFilter();
        });
        console.log('✅ クローズボタンにイベント追加');
    }
    
    // オーバーレイ
    if (filterOverlay) {
        filterOverlay.addEventListener('click', function(e) {
            console.log('🌑 オーバーレイがクリックされました');
            e.stopPropagation();
            window.testCloseFilter();
        });
        console.log('✅ オーバーレイにイベント追加');
    }
    
    // ESCキー
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('⌨️ ESCキーが押されました');
            window.testCloseFilter();
        }
    });
    console.log('✅ ESCキーにイベント追加');
};

// ===== 7. すべてのクラスをリセット =====
window.resetFilter = function() {
    console.log('\n🔄 フィルターをリセット...');
    if (filterPanel) {
        filterPanel.classList.remove('active');
    }
    if (filterOverlay) {
        filterOverlay.classList.remove('active');
    }
    document.body.style.overflow = '';
    console.log('✅ リセット完了');
};

// ===== 8. CSSスタイルの確認 =====
window.checkStyles = function() {
    console.log('\n🎨 CSSスタイルの確認:');
    if (filterPanel) {
        const styles = window.getComputedStyle(filterPanel);
        console.log('パネルのスタイル:');
        console.log('  position:', styles.position);
        console.log('  transform:', styles.transform);
        console.log('  z-index:', styles.zIndex);
        console.log('  display:', styles.display);
    }
    if (filterOverlay) {
        const styles = window.getComputedStyle(filterOverlay);
        console.log('オーバーレイのスタイル:');
        console.log('  position:', styles.position);
        console.log('  display:', styles.display);
        console.log('  z-index:', styles.zIndex);
        console.log('  opacity:', styles.opacity);
    }
};

// ===== 使い方の説明 =====
console.log('\n📖 使い方:');
console.log('1. testOpenFilter() - パネルを開く');
console.log('2. testCloseFilter() - パネルを閉じる');
console.log('3. forceAddClickEvents() - クリックイベントを強制追加');
console.log('4. resetFilter() - すべてをリセット');
console.log('5. checkStyles() - CSSスタイルを確認');

console.log('\n💡 推奨テスト手順:');
console.log('1. checkStyles() で現在のスタイルを確認');
console.log('2. testOpenFilter() でパネルが開くか確認');
console.log('3. testCloseFilter() でパネルが閉じるか確認');
console.log('4. forceAddClickEvents() でイベントを追加');
console.log('5. フィルターボタンをクリックしてテスト');

console.log('\n✅ デバッグモード準備完了！');
console.log('まず checkStyles() を実行してください。');
