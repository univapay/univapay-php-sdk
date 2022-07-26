# UnivaPay PHP SDK

[![PHP lint & test](https://github.com/univapay/univapay-php-sdk/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/univapay/univapay-php-sdk/actions/workflows/php.yml)

UnivaPay PHP SDKは、UnivaPay決済ゲートウェイと連携する便利なメソッドを提供します。

*[English](README_en.md)*

## 必要なもの

- PHP 7.1.x 以上
- Composer
- npm (dev only)
- UnivaPayのストアアプリケーショントークンまたはマーチャントアプリケーショントークン

## インストール

```shell
composer require univapay/php-sdk
```

## 利用方法

```php
use Univapay\UnivapayClient;
use Univapay\UnivapayClientOptions;
use Univapay\Requests\Handlers\RateLimitHandler;

$client = new UnivapayClient(AppJWT::createToken('token', 'secret'));

// その他のオプションは、クライアントをインスタンス化する前にクライアントオプションオブジェクトを作成および変更します
// すべてのオプションについては、UnivapayClientOptionsを参照してください
$clientOptions = new UnivapayClientOptions();
$clientOptions->rateLimitHandler = new RateLimitHandler(5, 2);
$client = new UnivapayClient(AppJWT::createToken('token', 'secret'), $clientOptions);

// 使用例については、examplesフォルダを参照してください
```

### アプリケーショントークン

このSDKでは、ストアタイプとマーチャントタイプの両方のアプリケーショントークンがサポートされています。 ストアタイプトークンを必要とするトランザクショントークンや課金の作成以外のすべての機能は、両方のトークンタイプでサポートされています。

### 通貨モデル
このSDKは`moneyphp`ライブラリを使用して金額と通貨をモデル化します。詳細は、[ドキュメント](http://moneyphp.org/en/latest/index.html)を参照してください。

すべての通貨と金額は自動的に`Currency`と`Money`オブジェクトに変換されます。フォーマットされた金額（`.*Formatted`キーで示される）のみがString形式になります。

```php
use Money\Currency;
use Money\Money;
use Univapay\PaymentMethod\CardPayment;

$paymentMethod = new CardPayment(...);
$charge = $client
    ->createToken($paymentMethod)
    ->createCharge(Money::USD(1000));

$charge->currency === new Currency('USD'); // true
$charge->requestAmount === new Money(1000, $charge->currency); // true
```

### 列挙型

PHPにはネイティブの組み込み列挙型サポートがないため、列挙子を操作するときに型の安全性を提供するために、`TypedEnum`というクラスを提供します。各列挙子クラスは最終版であり、 `TypedEnum`を拡張して、Javaなどの他の言語の列挙子と同様に動作する静的関数を提供します。列挙型クラスは、`Univapay\Enums`という名前空間にあります。

_デフォルトでは、作成時に値が指定されていない場合、スネークケースになります。_

```php
use Univapay\Enums\ChargeStatus;

$values = ChargeStatus::findValues(); // 列挙子のすべての名前と値のリストを取得する
$chargeStatus = ChargeStatus::PENDING(); // 最後のカッコに注意してください
$chargeStatus->getValue() === 'pending'; // true
$chargeStatus === ChargeStatus::fromValue('pending'); // true
// switchステートメントでも機能します
switch ($chargeStatus) {
    case ChargeStatus::PENDING():
        // Do something
        break;
    // ...
}
```

### リソースモデルの更新
リソースモデル（`Resource`を拡張するモデルクラス）を更新するには、下記のようにします。

```php
$charge->fetch();
```

### ポーリング
次のリソースは、ステータス変更を待機するためのロングポーリングがサポートされています。
- `Charge`
- `Refund`
- `Cancel`
- `Subscription`

これらのリクエストは最初に`PENDING`ステータスを戻します。ロングポーリングでは、リソースのステータスが変更されたときに、更新されたモデルをフェッチできます。3秒以内に変更が発生しない場合、その時点のリソースが返されます。

```php
$charge = $client
    ->createCharge($token->id, Money::USD(1000)) // $charge->status == PENDING
    ->awaitResult(); // $charge->status == SUCCESSFUL
```

### リストとページネーション

SDKのすべてのリスト関数は、作成日時の降順で`Paginated`オブジェクトとして返されます。配列を介してパラメーターを渡すときは、入力が期待されるタイプと一致するように注意してください。一致しない場合、`InvalidArgumentException`がスローされます。

```php
use InvalidArgumentException;
use Univapay\Enums\CursorDirection;

try {
    $transactionList = $client->listTransactionsByOptions([
        'from' => date_create('-1 week'),
        'to' => date_create('+1 week')
    ]);
} catch (InvalidArgumentException $error) {
    // 入力パラメーターが正しいタイプに対応していない場合
}

$transactions = $transactionList->items; // 1ページあたりのデフォルトの上限 = 10アイテム

if ($transactionList->hasMore) {
    $transactionList = $transactionList->getNext(); // リストは内部で変化しない
    $transactions = array_merge($transactions, $transactionList->items);
}

$firstTenItems = $client->listTransactionsByOptions([
    'from' => date_create('-1 week'),
    'to' => date_create('+1 week'),
    'cursor_direction' => CursorDirection::ASC()
]);
```

### リクエスト/レスポンスハンドラ

データをオブジェクトに解析する前に追加の変更または応答への反応を必要とする場合の使用例です。 SDKは、APIからのバックプレッシャーに基づいてリクエストを調整する `RateLimitHandler`を提供します（これはデフォルトで` UnivapayClientOptions-> rateLimitHandler`に実装されています）。 さらに、 `BasicRetryHandler`も提供されており、再試行のために特定の例外をキャッチしてフィルタリングします。 キャッチする例外を指定するには：

```php
use Univapay\Requests\Handlers\BasicRetryHandler;

$subscriptionTokenRetryHandler = new BasicRetryHandler(
    UnivapayResourceConflictError::class,
    5, // 5回トライする
    2, // 2秒毎
    // エラーに基づいたより具体的なフィルタリングは、最初のパラメーターからエラー内容を取得してください
    // 再試行する場合はtrueを、無視する場合はfalseを返します
    function (UnivapayResourceConflictError $error) {
        return $error->code === 'NON_UNIQUE_ACTIVE_TOKEN';
    }
);
$client->addHandlers($subscriptionTokenRetryHandler);

// 新しいハンドラーをリセットするか、クリアして最初から追加する
// rateLimitHandlerはUnivapayClientOptionsから自動的に追加されます
$client->setHandlers($subscriptionTokenRetryHandler);
```

## SDK開発者向け

ビルド:
```shell
composer install
npm install

# 必要に応じて
npm install -g grunt
```

コードフォーマット:
```shell
grunt phpcs
```

テスト:

テストを実行するには、次の環境変数が必要です。

- `UNIVAPAY_PHP_TEST_TOKEN` - `test`モードのトークンである必要があります
- `UNIVAPAY_PHP_TEST_SECRET`
- `UNIVAPAY_PHP_TEST_ENDPOINT` - ローカルAPIインスタンスまたはステージングインスタンスを指します

```shell
grunt phpunit
```
_注：Github Actionsは、プルリクエストがOpenされているブランチでのみ実行されます_
