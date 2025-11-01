# 未使用ファイル調査レポート
生成日時: 2025-10-31

## 調査対象ディレクトリ

選択された4つのディレクトリについて、使用状況と関連性を調査しました。

### 1. `/config` ディレクトリ

**内容:**
- `postcss.config.js` - PostCSS設定（高度な設定）
- `tailwind.config.js` - Tailwind CSS設定（高度な設定）
- `vite.config.js` - Vite ビルドツール設定
- `README.md` - 設定ファイルの説明書

**使用状況:**
- ❌ **現在未使用** - package.jsonのnpmスクリプトがこれらの設定を参照していない
- Viteによるビルドプロセスは現在使用されていない
- 実際に使用されているのはルートディレクトリの設定ファイル

**重複ファイル:**
- ルートディレクトリに `postcss.config.js` と `tailwind.config.js` が存在し、**こちらが実際に使用されている**

**目的:**
- 将来的にViteベースのモダンビルドシステムへの移行を想定
- より高度なビルド最適化とHot Module Replacement (HMR) のサポート

**推奨アクション:**
- ⚠️ **移行計画がない場合は削除可能**
- 移行予定がある場合は保持
- README.mdには有用な情報が記載されているため、削除前に確認

---

### 2. `/deploy-files` ディレクトリ

**内容:**
- `functions.php` - functions.phpの旧バージョン（v9.0.0）
- `theme-foundation.php` - theme-foundation.phpの旧バージョン（v8.0.0）

**使用状況:**
- ❌ **完全に未使用**
- プロジェクト内のどのファイルからも参照されていない
- ルートの `functions.php` と `inc/theme-foundation.php` が実際に使用されている

**バージョン比較:**
- deploy-files/functions.php: v9.0.0 (古い)
- 現在のfunctions.php: v9.1.0 (最新)
- deploy-files/theme-foundation.php: v8.0.0 (かなり古い)
- 現在のtheme-foundation.php: 最新版

**目的:**
- デプロイ時のバックアップまたはロールバック用と思われる
- しかし、実際のデプロイプロセスで使用されていない

**推奨アクション:**
- ✅ **削除推奨** - Gitでバージョン管理されているため、バックアップとしての役割は不要
- 削除前に念のため内容を確認し、独自の機能がないことを確認

---

### 3. `/dev-tools` ディレクトリ

**内容:**
- `README.md` - 開発ツールの説明
- `scroll-fix-bookmarklet.js` - スクロール問題修正用ブックマークレット
- `SEO-BULK-UPDATE-GUIDE.md` - SEO一括更新ガイド
- `SEO-IMPLEMENTATION-SUMMARY.md` - SEO実装サマリー
- `SEO-TESTING-GUIDE.md` - SEOテストガイド

**使用状況:**
- ✅ **開発・デバッグ用として有用**
- 本番環境のコードからは参照されていない（これは正常）
- ドキュメントとしての価値が高い

**目的:**
- 開発中のデバッグツール
- SEO実装と更新のガイドライン文書
- 将来のメンテナンス用リファレンス

**推奨アクション:**
- ✅ **保持推奨** - ドキュメントとツールは今後の開発・保守で有用
- 本番環境には影響しない（WordPressの読み込みパスに含まれていない）

---

### 4. `/node_modules` ディレクトリ

**内容:**
- npmパッケージの依存関係（tailwindcss, postcss, autoprefixerなど）

**使用状況:**
- ✅ **現在使用中**
- package.jsonで定義されたビルドプロセスで使用
- `npm run dev`, `npm run build` コマンドで必要

**package.jsonの依存関係:**
```json
"devDependencies": {
  "tailwindcss": "^3.4.1",
  "autoprefixer": "^10.4.17",
  "postcss": "^8.4.33"
}
```

**推奨アクション:**
- ✅ **保持必須** - ビルドプロセスに必須
- 通常、`.gitignore` で除外されるべき（バージョン管理には含めない）

---

## 現在の設定ファイル使用状況

### 実際に使用されている設定ファイル

| ファイル | 場所 | 使用状況 |
|---------|------|---------|
| `postcss.config.js` | `/` (ルート) | ✅ 使用中 |
| `tailwind.config.js` | `/` (ルート) | ✅ 使用中 |
| `package.json` | `/` (ルート) | ✅ 使用中 |

### 未使用の設定ファイル

| ファイル | 場所 | 状態 |
|---------|------|------|
| `postcss.config.js` | `/config` | ❌ 未使用（重複） |
| `tailwind.config.js` | `/config` | ❌ 未使用（重複） |
| `vite.config.js` | `/config` | ❌ 未使用（Vite未使用） |

---

## ビルドプロセスの現状

### 現在のビルドフロー

```
package.json (npm scripts)
    ↓
tailwindcss CLI
    ↓
postcss.config.js (ルート) ← 実際に使用
    ↓
tailwind.config.js (ルート) ← 実際に使用
    ↓
assets/css/tailwind-build.css (出力)
```

### 問題点

1. **ソースファイル不在**: `./assets/css/src/tailwind.css` が存在しない
   - `npm run build` が失敗する
   - ビルド済みファイル `tailwind-build.css` は存在するが、再ビルド不可

2. **config/ ディレクトリとの不一致**:
   - configディレクトリに設定ファイルがあるが使用されていない
   - ルートディレクトリの設定ファイルが使用されている

---

## CSSファイルの読み込み状況

### フロントエンド（theme-foundation.php）

```php
wp_enqueue_style('gi-tailwind', '.../tailwind-build.css');  // Tailwind CSS
wp_enqueue_style('gi-style', style.css);                     // テーマCSS
wp_enqueue_style('gi-unified-frontend', 'unified-frontend.css'); // カスタムCSS
wp_enqueue_style('google-fonts-noto', Google Fonts);         // フォント
```

### 管理画面

```php
wp_enqueue_style('gi-admin-consolidated', 'admin-consolidated.css');
```

---

## 各ディレクトリの関連性

### 関連図

```
/config (未使用)
    ├── Vite設定 → 未使用（将来用）
    ├── PostCSS設定（高度版） → 未使用
    └── Tailwind設定（高度版） → 未使用

/deploy-files (未使用)
    ├── functions.php (旧版) → 削除可能
    └── theme-foundation.php (旧版) → 削除可能

/dev-tools (開発用・保持推奨)
    ├── ブックマークレット → 開発時デバッグ用
    └── SEOドキュメント → メンテナンス用リファレンス

/node_modules (使用中・必須)
    └── npm依存関係 → ビルドプロセスに必須

/ (ルート・使用中)
    ├── postcss.config.js → 実際に使用中
    ├── tailwind.config.js → 実際に使用中
    ├── package.json → 実際に使用中
    └── functions.php → 実際に使用中

/inc (使用中・必須)
    └── theme-foundation.php → 実際に使用中
```

---

## 推奨される整理アクション

### 🔴 即座に削除可能（未使用・重複）

1. **`/deploy-files` ディレクトリ全体**
   - 理由: 完全に未使用、Gitでバージョン管理済み
   - リスク: なし（Gitで復元可能）

### 🟡 条件付きで削除可能

2. **`/config` ディレクトリ**
   - **削除条件**: Viteへの移行予定がない場合
   - **保持条件**: 将来のビルドシステム改善を予定している場合
   - リスク: 低（必要時に再作成可能）

### 🟢 保持推奨

3. **`/dev-tools` ディレクトリ**
   - 理由: ドキュメントとデバッグツールとして有用
   - 本番環境に影響なし

4. **`/node_modules` ディレクトリ**
   - 理由: ビルドに必須
   - 注意: `.gitignore` に追加すべき

---

## 修正が必要な問題

### 1. ビルドプロセスの修正

**問題:**
```bash
$ npm run build
Error: Specified input file ./assets/css/src/tailwind.css does not exist.
```

**解決策:**
- `assets/css/src/tailwind.css` を作成
- または package.json のビルドスクリプトを修正

### 2. 設定ファイルの統一

**現状:** ルートと config/ に重複した設定ファイル

**推奨:**
- Option A: config/ を削除し、ルートの設定のみを使用（シンプル）
- Option B: config/ の設定を使用し、package.json を更新（モダン）

---

## まとめ

| ディレクトリ | 状態 | 推奨アクション | 優先度 |
|-------------|------|---------------|--------|
| `/config` | 未使用（将来用） | Vite不使用なら削除 | 🟡 中 |
| `/deploy-files` | 完全未使用 | 削除推奨 | 🔴 高 |
| `/dev-tools` | 開発用ドキュメント | 保持推奨 | 🟢 - |
| `/node_modules` | 使用中・必須 | 保持必須 | 🟢 - |

### 安全な削除順序

1. まず `/deploy-files` を削除（リスク: なし）
2. Vite移行予定を確認
3. 予定がなければ `/config` を削除
4. ビルドプロセスの修正（src/tailwind.css作成）

---

## 次のステップ

1. ✅ この調査レポートを確認
2. ⚠️ deploy-filesディレクトリの削除判断
3. ⚠️ configディレクトリの削除判断（Vite移行予定の確認）
4. 🔧 ビルドプロセスの修正（必要な場合）
5. 📝 .gitignoreの確認（node_modules除外確認）

---

**作成者注記:**
- このレポートは静的解析に基づいています
- 削除前に必ずGitでコミット済みであることを確認してください
- 本番環境への影響を避けるため、ローカル/開発環境で先にテストしてください
