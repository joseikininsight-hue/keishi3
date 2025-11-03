# 🧪 コンソールテスト手順書

## 📝 準備

1. ブラウザで助成金アーカイブページを開く
2. F12キーを押して開発者ツールを開く
3. **Console**タブを選択
4. 画面幅を768px以下に設定（モバイル表示）

---

## 🚀 クイックテスト（コピペで実行）

### テスト1: 要素の存在確認
```javascript
console.log('=== 要素の存在確認 ===');
console.log('フィルターボタン:', document.getElementById('mobile-filter-toggle'));
console.log('クローズボタン:', document.getElementById('mobile-filter-close'));
console.log('フィルターパネル:', document.getElementById('filter-panel'));
console.log('オーバーレイ:', document.getElementById('filter-panel-overlay'));
```

**期待される結果**: すべての要素が `<element>` として表示される（`null`でない）

---

### テスト2: パネルを手動で開く
```javascript
const panel = document.getElementById('filter-panel');
const overlay = document.getElementById('filter-panel-overlay');
if (panel) panel.classList.add('active');
if (overlay) overlay.classList.add('active');
document.body.style.overflow = 'hidden';
console.log('✅ パネルを開きました');
```

**期待される結果**: 
- パネルが右からスライドイン
- 背景が半透明の黒になる

---

### テスト3: パネルを手動で閉じる
```javascript
const panel = document.getElementById('filter-panel');
const overlay = document.getElementById('filter-panel-overlay');
if (panel) panel.classList.remove('active');
if (overlay) overlay.classList.remove('active');
document.body.style.overflow = '';
console.log('✅ パネルを閉じました');
```

**期待される結果**: 
- パネルが左から右へスライドアウト
- 背景が元に戻る

---

### テスト4: クリックイベントを強制追加
```javascript
// オーバーレイにクリックイベント追加
const overlay = document.getElementById('filter-panel-overlay');
if (overlay) {
    overlay.onclick = function(e) {
        console.log('🌑 オーバーレイがクリックされました');
        const panel = document.getElementById('filter-panel');
        if (panel) panel.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };
    console.log('✅ オーバーレイにイベント追加完了');
}

// クローズボタンにクリックイベント追加
const closeBtn = document.getElementById('mobile-filter-close');
if (closeBtn) {
    closeBtn.onclick = function(e) {
        console.log('❌ クローズボタンがクリックされました');
        const panel = document.getElementById('filter-panel');
        const overlay = document.getElementById('filter-panel-overlay');
        if (panel) panel.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    };
    console.log('✅ クローズボタンにイベント追加完了');
}
```

**期待される結果**: コンソールに「イベント追加完了」と表示される

---

### テスト5: イベントが動作するか確認
```javascript
// パネルを開く
const panel = document.getElementById('filter-panel');
const overlay = document.getElementById('filter-panel-overlay');
if (panel) panel.classList.add('active');
if (overlay) overlay.classList.add('active');
console.log('✅ パネルを開きました。オーバーレイまたはクローズボタンをクリックしてください');
```

**期待される動作**:
1. パネルが開く
2. オーバーレイ（黒い背景）をクリック → パネルが閉じる
3. または、クローズボタン（×）をクリック → パネルが閉じる

---

### テスト6: CSSスタイルの確認
```javascript
const panel = document.getElementById('filter-panel');
const overlay = document.getElementById('filter-panel-overlay');

if (panel) {
    const styles = window.getComputedStyle(panel);
    console.log('=== パネルのスタイル ===');
    console.log('position:', styles.position);
    console.log('transform:', styles.transform);
    console.log('z-index:', styles.zIndex);
    console.log('display:', styles.display);
}

if (overlay) {
    const styles = window.getComputedStyle(overlay);
    console.log('=== オーバーレイのスタイル ===');
    console.log('position:', styles.position);
    console.log('display:', styles.display);
    console.log('z-index:', styles.zIndex);
    console.log('background:', styles.background);
    console.log('opacity:', styles.opacity);
}
```

**期待される結果**:
- パネル: `position: fixed`, `z-index: 998`
- オーバーレイ: `position: fixed`, `z-index: 997`, `background: rgba(0, 0, 0, 0.5)`

---

## 🔍 問題診断

### 問題1: オーバーレイが表示されない
```javascript
// オーバーレイの存在と表示を確認
const overlay = document.getElementById('filter-panel-overlay');
console.log('オーバーレイ要素:', overlay);
if (overlay) {
    console.log('display:', window.getComputedStyle(overlay).display);
    console.log('opacity:', window.getComputedStyle(overlay).opacity);
    console.log('z-index:', window.getComputedStyle(overlay).zIndex);
    
    // 強制的に表示
    overlay.style.display = 'block';
    overlay.style.opacity = '1';
    console.log('✅ オーバーレイを強制表示しました');
}
```

### 問題2: クリックイベントが発火しない
```javascript
// イベントリスナーの確認（Chrome/Edge専用）
const overlay = document.getElementById('filter-panel-overlay');
if (typeof getEventListeners !== 'undefined') {
    console.log('オーバーレイのイベント:', getEventListeners(overlay));
} else {
    console.log('⚠️ getEventListeners は Chrome/Edge でのみ使用可能です');
}

// 手動でイベント発火テスト
if (overlay) {
    console.log('オーバーレイをクリックしてください...');
    overlay.addEventListener('click', function() {
        console.log('🎯 クリックイベントが発火しました！');
    }, { once: true });
}
```

### 問題3: パネルがスライドしない
```javascript
const panel = document.getElementById('filter-panel');
if (panel) {
    const styles = window.getComputedStyle(panel);
    console.log('=== トランジション設定 ===');
    console.log('transition:', styles.transition);
    console.log('transform:', styles.transform);
    
    // activeクラスを追加
    panel.classList.add('active');
    console.log('activeクラス追加後の transform:', 
        window.getComputedStyle(panel).transform);
}
```

---

## 🆘 緊急リセット

すべてがおかしくなった場合:
```javascript
// 完全リセット
const panel = document.getElementById('filter-panel');
const overlay = document.getElementById('filter-panel-overlay');
if (panel) {
    panel.classList.remove('active');
    panel.style = '';
}
if (overlay) {
    overlay.classList.remove('active');
    overlay.style = '';
}
document.body.style.overflow = '';
console.log('🔄 完全リセット完了');
```

---

## 📊 診断レポート生成

すべての情報を一度に確認:
```javascript
console.log('=== 🔍 診断レポート ===\n');

// 1. 要素の存在
const elements = {
    filterToggle: document.getElementById('mobile-filter-toggle'),
    filterClose: document.getElementById('mobile-filter-close'),
    filterPanel: document.getElementById('filter-panel'),
    filterOverlay: document.getElementById('filter-panel-overlay')
};

console.log('1️⃣ 要素の存在:');
Object.keys(elements).forEach(key => {
    console.log(`  ${key}: ${elements[key] ? '✅' : '❌'}`);
});

// 2. activeクラスの状態
console.log('\n2️⃣ activeクラスの状態:');
if (elements.filterPanel) {
    console.log('  パネル:', elements.filterPanel.classList.contains('active') ? '開いている' : '閉じている');
}
if (elements.filterOverlay) {
    console.log('  オーバーレイ:', elements.filterOverlay.classList.contains('active') ? '表示中' : '非表示');
}

// 3. z-index階層
console.log('\n3️⃣ z-index階層:');
Object.keys(elements).forEach(key => {
    const el = elements[key];
    if (el) {
        const zIndex = window.getComputedStyle(el).zIndex;
        console.log(`  ${key}: ${zIndex}`);
    }
});

// 4. 画面幅
console.log('\n4️⃣ 画面幅:', window.innerWidth, 'px');
console.log('  モバイル表示:', window.innerWidth <= 768 ? '✅ はい' : '❌ いいえ');

console.log('\n=== レポート終了 ===');
```

---

## 💡 よくある問題と解決策

| 問題 | 解決策 |
|------|--------|
| オーバーレイが表示されない | `overlay.style.display = 'block'` を実行 |
| クリックしても何も起きない | **テスト4**でイベントを強制追加 |
| パネルが開かない | **テスト2**で手動で開いてCSSを確認 |
| 画面幅が768px以上 | デバイスツールバーで768px以下に設定 |
| キャッシュが残っている | `Ctrl+Shift+R` で強制リロード |

---

## 📞 サポート情報

問題が解決しない場合は、以下の情報を共有してください:

1. **診断レポート生成**のコードを実行した結果
2. ブラウザ名とバージョン
3. 画面幅の設定
4. コンソールのエラーメッセージ（赤文字）

---

**作成日**: 2025年11月3日  
**バージョン**: v1.0  
**対象ファイル**: archive-grant.php
