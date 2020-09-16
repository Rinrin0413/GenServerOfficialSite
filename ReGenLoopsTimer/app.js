const btn = document.querySelector("#btn-play");   // <button>

//--------------------------
// インスタンスを生成
//--------------------------
const bgm1 = new Howl({
    // 読み込む音声ファイル
    src: [
      'op.ogg',
      'op.mp3'  // Ogg非対応の場合にMP3を採用する
    ],

    // 設定 (以下はデフォルト値です)
    preload: true,   // 事前ロード
    volume: 1.0,     // 音量(0.0〜1.0の範囲で指定)
    loop: false,     // ループ再生するか
    autoplay: false, // 自動再生するか

    // 読み込み完了時に実行する処理
    onload: ()=>{
      btn.removeAttribute("disabled");  // ボタンを使用可能にする
    }
});

/**
 * [event] ボタンクリック時に実行
 */
btn.addEventListener("click", ()=>{
  // true=>再生中, false=>停止
  if( bgm1.playing() ){
    btn.innerHTML = '<i class="fas fa-play"></i>';  // 「再生ボタン」に変更
    bgm1.pause();
  }
  else{
    btn.innerHTML = '<i class="fas fa-pause"></i>';  // 「停止ボタン」に変更
    bgm1.play();
  }
});

/**
 * [event] 再生終了時に実行
 */
bgm1.on("end", ()=>{
  bgm1.seek(0);  // 再生位置を先頭に移動
  btn.innerHTML = '<i class="fas fa-play"></i>';  // 「再生ボタン」に変更
});
