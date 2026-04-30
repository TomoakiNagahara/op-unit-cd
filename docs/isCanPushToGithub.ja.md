# `isCanPushToGithub()` の仕様

## 概要

`isCanPushToGithub()` は、ある branch を GitHub へ push してよいかどうかを判定する関数です。

対象ファイル:

`asset/unit/cd/function/isCanPushToGithub.php`

この関数は、ONEPIECE Framework における CD 側の安全制御の一部です。

## 目的

この関数の目的は、意図しないコードが GitHub に push されることを防ぐことです。

特に、次のような branch の push を防ぐ意図があります。

- 開発途中のコード
- 実験用のコード
- 研究用の branch
- 共有リモート履歴に載せる前提ではない branch

これにより、次のような問題を減らすことを目的としています。

- 履歴の汚染
- 未完成な作業内容の意図しない共有
- 不安定な branch による不要なコンフリクト

## 何を判定するか

この関数は、次の 2 つを受け取ります。

- remote 名
- branch 名

そのうえで、その branch を GitHub に push してよいかどうかを判定します。

## 許可される条件

次の場合、この関数は `true` を返します。

### 1. branch がアプリケーション branch である

branch 名が `_OP_APP_BRANCH_` と一致する場合、push を許可します。

例:

```php
define('_OP_APP_BRANCH_', 2030);
```

この場合、branch `2030` は許可されます。

### 2. branch 名が Year 形式である

branch 名が `2025`, `2026`, `2030`, `2031` のような Year 形式に一致する場合、push を許可します。

現在の実装では、次のパターンを使っています。

```text
^20[2,3]\d$
```

### 3. push 先が GitHub ではない

remote URL が GitHub ではない場合、この GitHub 向け制限は適用されません。

そのため、この関数は push を許可します。

この挙動の意図は運用上の判断にあります。

- repository が GitHub ではない場合は、プライベートな領域の repository を使っていると判断する
- プライベートな領域にあるなら、うっかり push しても GitHub に公開される場合より問題が小さいとみなす

これは framework の実務的な信頼境界の置き方です。

### 4. CD 設定で branch が明示的に許可されている

関数は CD 設定を読み込み、許可 branch 一覧にその branch 名が含まれているかを確認します。

既定の設定ファイル:

`asset/unit/cd/config.php`

関数内で案内されている上書き設定先:

`asset/config/cd.php`

実務上、branch が block されていて、それを許可したい場合の通常手順は次です。

1. `asset/unit/cd/config.php` をコピーする
2. `asset/config/cd.php` を作成する
3. 許可したい branch 名を `branch` list に追加する

framework の思想としては、次のように役割分担します。

- `asset/unit/cd/config.php` は default config
- `asset/config/cd.php` は各環境向けの config

そのため、`asset/unit/cd/config.php` を直接編集することは推奨されません。

推奨パターンは、unit 側 config に default を残し、local や environment-specific な変更は `asset/config/cd.php` 側に置くことです。

## 確認済みの実装詳細

この挙動は、current 実装で直接確認できます。

実際の流れは次です。

1. `isCanPushToGithub()` が `OP()->Config('cd')` を呼ぶ
2. `Config.class.php` が `asset/unit/cd/config.php` を読む
3. `asset/config/cd.php` が存在すれば、それを続けて読む
4. 後段の層が `array_replace_recursive()` で前段を上書きする
5. `isCanPushToGithub()` が `$_config['branch']` を確認する

したがって、`asset/config/cd.php` 側で `branch` list に branch 名を追加すれば、current 実装ではその branch は許可対象になります。

厳密には、既定 file 全体をコピーすること自体は技術的必須ではありません。

必要な override 構造だけを `asset/config/cd.php` に書いても動作します。

## ブロックされる条件

push 先が GitHub であり、かつ branch が上記の許可条件のどれにも当てはまらない場合、この関数は push をブロックします。

ブロック時には、次の情報を表示します。

- 現在の作業ディレクトリ
- remote 名
- branch 名
- remote URL
- 許可 branch 設定についての案内

## 設計上の意図

この関数が存在する理由は、すべての branch を配布対象 branch として扱わないためです。

ONEPIECE Framework では、次を明確に分けています。

- 共有やリリース、統合に使う branch
- ローカル開発、実験、調査のためだけに使う branch

branch 名で GitHub push を制限することで、共有履歴を整理された状態に保ちやすくしています。

その意味では、ONEPIECE Framework は GitHub に対して部分的にベンダーロックインしているとも言えます。より強い branch 公開制御は GitHub への push に対して適用されるためです。

## この関数の責務

この関数は、push に関するすべてのルールを単独で制御するわけではありません。

責務は、GitHub への push に対する branch 名ベースの制限です。

それ以外のルールは別の仕組みで制御されます。たとえば:

- commit message prefix のチェック
- CI 通過済み commit のチェック
- hook によるローカル検証

## 確認済みの current 実装境界

current 実装では、責務境界は明確に分かれています。

- `asset/unit/cd/function/isCanPushToGithub.php`
  GitHub への push に対する branch 名ベース制限を担当する
- `asset/init/hooks/pre-push-prefix.php`
  push 対象 commit の commit message prefix 検証を担当する
- `asset/init/hooks/pre-push.sh`
  先に CI script を実行し、その後で `pre-push-prefix.php` を実行する

つまり、current の As-Is 実装では、commit message prefix による push block は `op-unit-cd` の内部には入っていません。

順序としては次です。

1. CI 関連 enforcement が走る
2. その後で prefix 検証が走る
3. `op-unit-cd` は branch 名ベースの公開制御を担当する

したがって、prefix block は push policy 全体の一部ではありますが、`isCanPushToGithub()` 自身の内部責務ではありません。

## [DOC-GAP] prefix enforcement の散在

歴史的経緯として、prefix による push block は場当たり的に段階追加されました。

そのため、current 実装では責務が `op-unit-cd` にきれいに集約されず、複数箇所に散在しています。

つまり、この current 配置は理想的な最終責務配置ではなく、歴史的な As-Is として理解すべきです。

## [DOC-FUTURE] `op-unit-cd` への集約予定

長期的に意図している方向は、prefix 関連 block を含む push policy enforcement を `op-unit-cd` に集約することです。

言い換えると:

- current の散在実装は、互換性と歴史的経緯のために許容されている
- 将来的には、より集中した CD 側責務モデルへ寄せる予定である

さらに広く言えば、理想的な To-Be は CD 関連の責務全体を `op-unit-cd` に集約することです。

これには次を含みます。

- branch 名ベースの公開制御
- push policy enforcement
- CD 側の delivery 判断

## まとめ

`isCanPushToGithub()` は、GitHub への push に対する branch gate です。

次の場合に push を許可します。

- branch が `_OP_APP_BRANCH_` と一致する
- branch が Year ベースである
- branch が CD 設定で明示的に許可されている
- remote が GitHub ではない

それ以外の GitHub push は、未整理または未完成な作業の公開を防ぐためにブロックします。
