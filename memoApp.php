<?php

require_once '.\functions.php';
require_once '.\class.php';

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

$memoData;
$dirPath;

if (!empty($_SESSION['memoData'])) {
    $memoData = unserialize($_SESSION['memoData']);
}

// 作成者の名前を受け取る
if (!empty($_POST['memoname'])) {
    $name = $_POST['memoname'];
    $dirPath = generateDirPath($name, DIR_NAME);
    $memoData = new Memo($name, $dirPath.SAVE_NAME);
    $_SESSION['memoData'] = serialize($memoData);
    header("Location: memoApp.php");
    exit;
}

// メモの追加を受け取る
if (!empty($_POST['text']) || !empty($_FILES['imgFile'])) {
    $text;
    $imgpath;
    if (empty($_POST['text'])) {
        $text = "";
    } else {
        $text = $_POST['text'];
    }
    if (array_count_values($_FILES['imgFile']) > 1) {
        $imgFile = $_FILES['imgFile'];
        $dirPath = generateDirPath($memoData->getName(), DIR_NAME);
        $imgpath = imageUpload($dirPath, $imgFile);
    } else {
        $imgpath = "";
    }
    if (empty($text) && empty($imgpath)) {
        
    }
    
    $memoData->addMemo($text, $imgpath);
    $memoData->saveFile();
    $_SESSION['memoData'] = serialize($memoData);
    
    header("Location: memoApp.php");
    exit;
}

if (isset($_POST['id']) && is_array($_POST['id'])) {
    $memoData->deleteMemo($_POST['id']);
    $memoData->saveFile();
    $_SESSION['memoData'] = serialize($memoData);
    
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
	Name<br>
	<form method="post">
	<input type="text" name="memoname" autocomplete="off"></input>
	<input type="submit" value="OK">
	</form>
	
	<!-- メモデータを表示 -->
	<?php if(!empty($memoData)): ?>
	<?php $memoData = $memoData ?>
	<h2><?php echo $memoData->getName() ?> さん</h2>
	
	<?php if(count($memoData->getData())>0) :?>
		<form method="post">
		<table>
		<?php foreach($memoData->getData() as $d): ?>
		<tr>
			<td><?php echo($d[DATE])?></td>
			<td><?php echo($d[TEXT])?></td>
			<td><label><input type="checkbox" name="id[]" value="<?php echo($d[ID])?>">delete</label></td>
		</tr>
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
    	
    	<!-- メモを追加 -->
    	<form method="post" enctype="multipart/form-data" >
    	<input type="hidden" name="max_file_size" value="2097152">
    	<input type="file" name="imgFile"><br>
    	<textarea name="text"></textarea><br>
    	<input type="submit" value="メモを追加">
    	</form>
	<?php endif; ?>
	</div>
</body>
