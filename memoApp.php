<?php
/* 
 * メモアプリのメインページ。
 * 入力を受け取り、メモを表示させる。
 * 参考URL： https://code-notes.com/lesson/4
 */

require_once '.\functions.php';
require_once '.\classes.php';
require_once '.\exceptions.php';

session_start();

// 保存ディレクトリ名
const DIR_NAME = 'data';
// 保存ファイル名
const SAVE_NAME = 'memo';
// メモデータのKey
const ID = 'id';
const DATE = 'date';
const TEXT = 'text';
const IMG_FILE = 'img_file';

// 表示するメモデータを格納する
$memoData;

// セッションにMEMOのインスタンスがある場合$memoDataに格納する
if (!empty($_SESSION['memoData'])) {
    $memoData = unserialize($_SESSION['memoData']);
    try {
        // 表示させるメモデータをファイルから読み込む
        $memoData->loadFile();
    } catch(InvalidFileException $e) {
        // 読み込みに失敗したらセッションから削除する
        popUpAlert($e->getMessage());
        unset($_SESSION['memoData']);
        
        header("Location: memoApp.php");
        exit;
    }
}

// 名前のリストを格納する
$nameList;
$dirPath = './'.DIR_NAME;
if (is_readable($dirPath)) {
    // ディレクトリから名前のリストを取得する
    $nameList = array_diff(scandir($dirPath), array(".", ".."));
}

// memoNameを受け取る
if (isset($_POST['memoname'])) {
    $memoName = $_POST['memoname'];
    try {
        $memoData = new MemoManager($memoName);
    } catch (RuntimeException $e) {
        popUpAlert($e->getMessage());
        
        header("Location: memoApp.php");
        exit;
    }
    // インスタンスの生成に成功したらセッションに保存する
    $_SESSION['memoData'] = serialize($memoData);
    
    header("Location: memoApp.php");
    exit;
}

// メモの追加を受け取る
if (isset($_POST['memotext'])) {
    // テキストを受け取る
    $text = $_POST['memotext'];

    // 画像アップロードを受け取る
    $imgpath;
    $imgFile = $_FILES['imgFile'];
    try {
        // 画像をメモデータと同じフォルダ内にアップロードし、パスを取得する
        $imgpath = imageUpload($memoData->getDirPath(), $imgFile);
    } catch (NoInputException $e) {
        $imgpath = "";
    } catch (InvalidFileException $e) {
        popUpAlert($e->getMessage());

        header("Location: memoApp.php");
        exit();
    }
    try {
        // メモを追加する
        $memoData->addMemo($text, $imgpath);
    } catch (RuntimeException $e) {
        popUpAlert($e->getMessage());
        
        header("Location: memoApp.php");
        exit();
    }
    
    header("Location: memoApp.php");
    exit;
}

// メモの削除を受け取る
if (isset($_POST['id']) && is_array($_POST['id'])) {
    $memoData->deleteMemo($_POST['id']);
    
    header("Location: memoApp.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<link rel="stylesheet" href="base.css">
<head>
    <meta name= "viewport" content= "width=device-width, initial-scale= 1.0">
    <meta http-equiv= "content-type" charset= "utf-8">
    <title>MemoApp</title>
</head>
<body>
	<div>
		<h1>MEMO</h1>

		<!-- 名前を入力 -->
		Name (半角英数字のみ)<br>
		<form method="post">
		<input type="text" name="memoname" list="namelist" autocomplete="off"></input>
		<?php if(!$nameList===false): ?>
		<datalist id="namelist">
		<?php foreach($nameList as $memoName): ?>
		<option value="<?php echo($memoName)?>"><?php echo($memoName)?></option>
		<?php endforeach; ?>
		</datalist>
		<?php endif ?>
			<input type="submit" value="OK">
		</form>

		<!-- メモデータを表示 -->
	<?php if(!empty($memoData)): ?>
	<h2><?php echo $memoData->getMemoName() ?> さん</h2>
	
	<?php if(count($memoData->getData())>0) :?>

		<form method="post">
		<table>
		<?php foreach($memoData->getData() as $d): ?>
    		<tr>
			<td><?php echo($d[DATE])?></td>
			<td><label>
			<input type="checkbox" name="id[]"value="<?php echo($d[ID])?>">
			delete</label></td>
			</tr>
    		<?php if(!empty($d[TEXT])): ?>
    		<tr>
			<td style="word-break: break-all;"><?php echo($d[TEXT])?></td>
			</tr>
    		<?php endif ?>
    		<?php if(!empty($d[IMG_FILE])): ?>
        		<tr>
				<td><img src="<?php echo($d[IMG_FILE]) ?>"></td>
				</tr>
			<?php endif ?>
		<?php endforeach; ?>
		</table>
		<input type="submit" value="削除">
		</form>
		<hr>
	<?php endif; ?>
    <!-- メモデータ表示ここまで -->
    
		<!-- 画像・テキストをメモに追加する入力フォーム -->
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="max_file_size" value="2097152">
			<input type="file" name="imgFile"><br>
			<textarea name="memotext"></textarea>
			<br><input type="submit" value="メモを追加">
		</form>
	<?php endif; ?>
	</div>
</body>
