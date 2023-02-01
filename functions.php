<?php
/*
 * 参考URL： https://abyss-to-salena.net/web/php%E7%94%BB%E5%83%8F%E6%8A%95%E7%A8%BF%E3%83%A9%E3%82%A4%E3%83%96%E3%83%A9%E3%83%AA/
 */

require 'vendor/autoload.php';

/*
 * 画像ファイルをアップロードし、アップロードしたファイルのパスを返す
 * @param string $dirPath アップロードするディレクトリのパス
 * @param array $files アップロードされたファイル
 * @return string アップロードしたファイルのパス
 * @throws NoInputException InvalidFileException
 */
function imageUpload(string $dirPath, array $files): string
{
    if ($files['error']===UPLOAD_ERR_NO_FILE) {
        throw new NoInputException();
    }
    
    $fileName = 'img' . date('Ymd_His');
    $handle = new \Verot\Upload\Upload($files);
    if ($handle->uploaded) {
        $handle->allowed = [
            'image/*'
        ];
        
        $handle->image_resize = true;
        $handle->image_ratio = true;
        $handle->file_src_name_body	= $fileName;
        $handle->image_y = 60;
        $handle->image_ratio = true;
        
        $handle->Process($dirPath);
        if (!$handle->processed) {
            throw new InvalidFileException("画像アップロードに失敗しました");
        }
    }
    $uploadedFileName = $handle->file_src_name_body.'.'.$handle->file_src_name_ext;
    $imagePath = $dirPath.$uploadedFileName;
    
    return $imagePath;
}

/*
 * ポップアップでアラートを表示させる
 * @param string $text アラートに表示させる文字列
 */
function popUpAlert(string $text): void
{
    $alert = "<script type='text/javascript'>alert('".$text."');</script>";
    echo $alert;
}
