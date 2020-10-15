<?php
  $max = 5; //コンテンツの最大数
  $contents = array();

  for ($i = 0; $i < 50; $i++) {
    $contents[] = ($i+1) . '個目のコンテンツ';
  }

  $contents_sum = count($contents); //コンテンツの総数
  $max_page = ceil($contents_sum / $max); //ページの最大値

  if (!isset($_GET['page'])) {
    $page = 1;
  } else {
    $page = $_GET['page'];
  }

  $start = $max * ($page - 1); //スタートするページを取得
  $view_page = array_slice($contents, $start, $max, true); //表示するページを取得

 ?>

 <!DOCTYPE html>
 <html lang="ja" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>ページング</title>
   </head>
   <body>
     <!-- コンテンツを表示 -->
     <?php
     foreach ($view_page as $value) {
       echo $value . '<br />';
     }
      ?>
      <!-- ページ移動 -->
    <?php  if ($page > 1): ?>
      <a href="index.php?page=<?php echo ($page-1); ?>">前のページへ</a>
    <?php endif; ?>
    <?php  if ($page < $max_page): ?>
      <a href="index.php?page=<?php echo ($page+1); ?>">次のページへ</a>
    <?php endif; ?>

   </body>
 </html>
