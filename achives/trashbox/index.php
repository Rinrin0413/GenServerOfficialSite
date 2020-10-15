<?php
/**
 * ページング機能サンプル
 */
// エラーを表示してデバッグできるようにする
ini_set('display_errors');
error_reporting(E_ALL);

/**
 * エスケープ
 * @param string $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
}

/**
 * 現在のページ番号を取得する
 * @return int
 */
function getCurrentPage()
{
    return (int) filter_input(INPUT_GET, 'page');
}

/**
 * ページャーを組み立てる
 * @return string
 */
function pagination($rec)
{
    $count = $rec['recordCount'];
    $limit = 10;

    //レコード総数がゼロのときは何も出力しない
    if (0 === $count) {
        return '';
    }

    //現在表示中のページ番号（ゼロスタート）
    $intCurrentPage = getCurrentPage();

    //ページの最大数
    $intMaxpage = ceil($count / $limit);

    //現在ページの前後３ページを出力
    $intStartpage = (2 < $intCurrentPage) ? $intCurrentPage - 3 : 0;
    $intEndpage = (($intStartpage + 7) < $intMaxpage) ? $intStartpage + 7 : $intMaxpage;

    //url組み立て
    $urlparams = filter_input_array(INPUT_GET);

    $items = [];

    //最初
    $urlparams['page'] = 0;
    $items[] = sprintf('<span><a href="?%s">%s</a></span>'
        , http_build_query($urlparams)
        , '最初'
    );

    //表示中のページが先頭ではない時
    if (0 < $intCurrentPage) {
        $urlparams['page'] = $intCurrentPage - 1;
        $items[] = sprintf('<span><a href="?%s">%s</a></span>'
            , http_build_query($urlparams)
            , '前へ'
        );
    }

    for ($i = $intStartpage; $i < $intEndpage; $i++) {
        $urlparams['page'] = $i;
        $items[] = sprintf('<span%s><a href="?%s">%s</a></span>'
            , ($intCurrentPage == $i) ? ' class="current"' : ''
            , http_build_query($urlparams)
            , $i + 1
        );
    }

    //表示中のページが最後ではない時
    if ($intCurrentPage < $intMaxpage) {
        $urlparams['page'] = $intCurrentPage + 1;
        $items[] = sprintf('<span><a href="?%s">%s</a></span>'
            , http_build_query($urlparams)
            , '次へ'
        );
    }

    //最後
    $urlparams['page'] = $intMaxpage - 1;
    $items[] = sprintf('<span><a href="?%s">%s</a></span>'
        , http_build_query($urlparams)
        , '最後'
    );

    return sprintf('<div class="pagination">%s</div>', implode(PHP_EOL, $items));
}

/**
 * データベースに接続する
 * @return \PDO
 * @throws Exception
 */
function connect()
{
    try {
        $dsn = "mysql:dbname=datadbase;host=localhost;charset=utf8";
        $username = 'root';
        $password = 'password';
        $options = [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION];

        $dbh = new PDO($dsn, $username, $password, $options);
        return $dbh;
    } catch (PDOException $e) {
        throw new Exception('データベース接続に失敗しました。', $e->getCode(), $e);
    }
}

/**
 * レコードセットを取得する
 * @param string $sql
 * @param array $params
 * @param array $order
 * @param int $limit
 * @return array
 */
function select($sql, array $params = null, array $order = null, $limit = null)
{
    $statement = $sql;
    if (!is_null($order)) {
        $orderToken = [];
        foreach ($order as $field => $value) {
            $orderToken[] = sprintf('%s %s', $field, $value);
        }
        $orderStr = implode(', ', $orderToken);
        $statement .= " ORDER BY {$orderStr}";
    }

    $dbh = connect();

    try {
        // ここからちょっと手抜きw
        // 本来は SELECT count(*) FROM ... で全レコード数をとるべき
        $stmt = $dbh->prepare($statement);
        $stmt->execute($params);
        $recset = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $recCount = count($recset);

        // LIMITが設定されているとき
        if (!is_null($limit)) {
            $start = getCurrentPage() * $limit;
            $statement .= sprintf(' LIMIT %d, %d', $start, $limit);
            $stmt = $dbh->prepare($statement);
            $stmt->execute($params);
            $recset = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return ['recordSet' => $recset, 'recordCount' => $recCount];
    } catch (PDOException $e) {
        throw new Exception('データベース・エラーが発生しました。', $e->getCode(), $e);
    }
}

try {
    $sql = 'SELECT id, name, field FROM table WHERE field = :value';
    $params = [':value' => '○○○○'];

    // ORDER句
    $order = ['name' => 'asc'];

    // 1ページに表示するレコード数
    $limit = 10;

    //  レコードセットを取得
    $res = select($sql, $params, $order, $limit);
} catch (Exception $e) {
    $err = $e->getMessage();
}
?>
<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ページング機能サンプル</title>
    </head>
    <body>
        <?php if (isset($err)) : ?>
            <p><?= h($err); ?></p>
        <?php else : ?>

            <?php if (0 < count($res['recordCount'])) : ?>

                <p><?= h(number_format($res['recordCount'])); ?> 件のレコードが見つかりました。</p>

                <table>
                    <?php foreach ($res['recordSet'] as $record): ?>
                        <tr>
                            <td><?= h($record['id']); ?></td>
                            <td><?= h($record['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php else : ?>

                <p>検索条件にヒットするデータが見つかりませんでした。</p>

            <?php endif; ?>
            <?= pagination($res); ?>

        <?php endif; ?>
    </body>
</html>
