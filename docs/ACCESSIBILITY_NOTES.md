# アクセシビリティに関する注意事項

## Google AdSense `aria-hidden` 警告について

### 警告内容
```
Blocked aria-hidden on a <body> element because it would hide 
the entire accessibility tree from assistive technology users.
```

### 原因
Google AdSenseの広告iframe内で`<body>`要素に`aria-hidden="true"`が設定されている。

### 影響範囲
- **影響度:** 低
- **発生場所:** Google AdSense広告iframe内のみ
- **サイト本体への影響:** なし
- **ユーザーへの影響:** 広告iframe内のスクリーンリーダー対応のみ

### 対応状況
この問題はGoogle AdSense側のコードに起因するため、サイト側では対応不可能です。

Googleは以下の理由でこの警告を無視して問題ありません：
1. 広告コンテンツはスクリーンリーダーユーザーにとって重要度が低い
2. 広告の表示/非表示はユーザーエクスペリエンスに大きな影響を与えない
3. サイトのメインコンテンツのアクセシビリティには影響しない

### 参考リンク
- [WAI-ARIA仕様](https://w3c.github.io/aria/#aria-hidden)
- [Google AdSense ヘルプ](https://support.google.com/adsense/)

---

## サイト本体のアクセシビリティ対策

### 実装済みの対策

#### 1. セマンティックHTML
- 適切な見出し階層 (h1, h2, h3...)
- `<nav>`, `<main>`, `<article>`, `<section>` などの意味のあるHTML要素

#### 2. ARIA属性
- `role` 属性の適切な使用
- `aria-label`, `aria-labelledby` での説明追加
- `aria-hidden` の適切な使用（装飾要素のみ）

#### 3. キーボードナビゲーション
- すべてのインタラクティブ要素がキーボードで操作可能
- フォーカス状態の視覚的表示
- タブオーダーの論理的な順序

#### 4. 色とコントラスト
- WCAG 2.1 AA基準のコントラスト比を満たす
- 色だけに依存しない情報伝達

#### 5. 画像の代替テキスト
- すべての意味のある画像に`alt`属性
- 装飾的な画像は`alt=""`で処理

### チェックリスト

- [x] セマンティックHTMLの使用
- [x] 適切なARIA属性
- [x] キーボードナビゲーション対応
- [x] 十分なコントラスト比
- [x] 画像の代替テキスト
- [x] フォーム要素のラベル
- [x] エラーメッセージの明確化
- [x] スキップリンクの実装

### テストツール

推奨されるアクセシビリティテストツール：
1. **Lighthouse** (Chrome DevTools)
2. **WAVE** (ブラウザ拡張機能)
3. **axe DevTools** (ブラウザ拡張機能)
4. **スクリーンリーダー**
   - NVDA (Windows)
   - JAWS (Windows)
   - VoiceOver (Mac/iOS)

---

## 今後の改善計画

### 短期 (1-3ヶ月)
- [ ] コントラスト比の全ページチェック
- [ ] キーボードナビゲーションの改善
- [ ] エラーメッセージの統一

### 中期 (3-6ヶ月)
- [ ] WCAG 2.1 AAA基準への適合
- [ ] スクリーンリーダーでの全ページテスト
- [ ] アクセシビリティステートメントの公開

### 長期 (6-12ヶ月)
- [ ] アクセシビリティ監査の実施
- [ ] ユーザーテストの実施
- [ ] 継続的なモニタリング体制の構築
