# プロジェクト名: 全国サウナ検索サイト

![License](https://img.shields.io/badge/license-MIT-blue.svg)

## プロジェクトの概要

全国のサウナを検索、登録できるサイト
サウナ活動(サ活)を店舗ごとにレビューできます

## サイトURL

https://sauna-searcher.com/saunas

## テストアカウント情報

| ユーザ名      | パスワード     |
|---------------|----------------|
| sample@mail     | password       |

## アプリケーションの制作経緯

サウナへよく行く知人が、サ活をX(旧ツイッター)へ投稿をしており、その中で「写真を撮るわけではないから過去の投稿からどんな雰囲気のサウナだったかを把握しにくい」と言っていたため、

公式ページのキュレーションサイトのような媒体にレビュー機能をつけることで解決できるのではないかと思い、学習のためにも作成してみました。

ユーザー情報の登録、店舗情報の登録、店舗に対してレビューの作成ができるようになっています。

アプリの機能面やUI部分はまだまだなのですが、自身の学習を通してアプリの保守・運用を続けていきたいと思っています。

## アプリケーション詳細

### 考慮した内容

1. 現場で使われやすいモダンな技術の使用 (AWS, Vue.js)
2. CSSフレームワークのtailwindを使用
3. データベースはより拡張しやすいように設計

### 使用イメージ

![スクリーンショット1](path/to/screenshot1.png)
![スクリーンショット2](path/to/screenshot2.png)

## 使用技術

- フロントエンド: Vue.js
- バックエンド: PHP (Laravel)
- インフラ環境: AWS
- CSS: tailwind
- フロント開発環境: vite

### AWS構成図

![AWS構成図](https://github.com/tai22222/images/raw/main/sauna_aws.png)

### 技術選定の基準

#### バックエンドの言語

「長期的に使用していけるか」と「学習教材が豊富」を基準に選定しました。PHP、Rubyが候補となり、案件数の数の伸びや需要の安定からPHPを選択し、その中でもフレームワークは情報量が多く、経験が浅いことを踏まえてLaravelを選択しました。

#### フロントエンド

ページ表示速度の速さからVue.jsとReactのどちらかに絞り、学習教材が豊富という観点から学習コストを考慮してVue.jsを選択しました。

### 使用技術の詳細

#### フロントエンド

- Vue 3.3.31
- JavaScript
- HTML / SCSS

#### バックエンド

- PHP 8.0.2
- Laravel Framework 9.19

#### インフラ

- MySQL 8.0.35
- AWS (EC2, ELB, ACM, RDS, Route53, VPC, EIP, IAM)

#### その他使用ツール

- Intelチップ搭載 MacBook Air / MacOS version 14.2.1（23C71）
- Visual Studio Code
- SourceTree
- GitHub
- Confluence
- Figma

### ER図

![ER図](https://github.com/tai22222/images/raw/main/sauna_db_er-diagram.png)

### 機能一覧

- 一般ユーザー登録関連
  - アカウント新規登録
  - ログイン、ログアウト機能
- ページネーション機能
- ユーザー写真(アイコン画像)投稿
- サウナ登録関連(新規登録、編集)
- Google Map Api
- サウナレビュー機能
- エラーハンドリング (システムエラー、バリデーションエラー、認証エラー、NotFoundエラー)

## 今後の課題

課題は山積みですが、優先して下記の機能を実装しようと思っております。

- サウナ情報のデータを充実
- 検索機能の充実
- サウナ情報の削除周り
- 各施設ごとに紐づいたレビューの表示(画像反映)
- ツイッター等のシェア機能
- 

まだまだ課題も多いですが、追加機能の希望があれば教えていただきたく思います。

---

プロジェクトの開発期間: 1.5ヶ月ほど (2024/1末〜2024/3初旬)
