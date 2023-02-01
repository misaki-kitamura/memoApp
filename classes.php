<?php
    require_once '.\memoApp.php';

class Memo
{
    // メモデータを保存するフォルダ名
    private $memoName;
    // メモデータを保存するディレクトリのパス
    private $dirPath;
    // メモデータを連想配列型で格納する
    private $data = array();
    
    /* 
     * memoNameが有効な文字列なら$memoNameに格納する
     * データ用のディレクトリ内にmemoNameのフォルダを作成しディレクトリのパスを$dirPathに格納する
     * memoNameのディレクトリにメモデータを保存するメモファイルを作成する
     * @param string $memoName メモデータのフォルダ名
     * @throws NoInputException　InvalidNameException
     */
    public function __construct(string $memoName)
    {
        // memoNameに半角英数字以外が使われていたら例外を投げる
        if (empty($memoName)) {
            throw new NoInputException("名前を入力してください");
        }
        // memoNameに半角英数字以外が使われていたら例外を投げる
        if (!preg_match('/^[a-zA-Z0-9]+$/ui', $memoName)) {
            throw new InvalidNameException("半角英数字のみで入力してください");
        }
        $this->memoName = $memoName;
        
        // メモデータを保存するフォルダの存在を確認し、なければ作成する
        $dirPath = './'.DIR_NAME."/";
        if (!is_readable($dirPath)) {
            mkdir($dirPath, 0777);
        }
        // memoNameのフォルダの存在を確認し、なければ作成する
        $dirPath = $dirPath.$memoName.'/';
        if (!is_readable($dirPath)) {
            mkdir($dirPath, 0777);
        }
        $this->dirPath = $dirPath;
        
        $filePath = $this->dirPath.SAVE_NAME;
        // メモデータを保存するファイルの存在を確認し、なければ空のファイルを作成する
        if (!file_exists($filePath)) {
            touch($filePath);
        }
    }
    
    /*
     *　memoNameを返す
     * @return string
     */
    public function getMemoName(): string
    {
        return $this->memoName;
    }
    /*
     * メモデータが保存されているディレクトリのパスを返す
     * @return string
     */
    public function getDirPath(): string
    {
        return $this->dirPath;
    }
    /*
     * メモデータの連想配列を返す
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    /*
     * ファイルからメモデータを読み込み$dataに連想配列型で格納する
     * @throws InvalidFileException
     */
    public function loadFile(): void
    {
        $filePath = $this->dirPath.SAVE_NAME;

        // ファイルからデータを取得する
        $lines = file_get_contents($filePath);
        
        foreach (explode("\n", $lines) as $line) {
            if (empty($line)) {
                continue;
            }
            // 保存形式が想定と違う場合は例外を発生させる
            if (strpos($line, "\t")===false) {
                throw new InvalidFileException("データのロードに失敗しました");
            }
            // タブで分割して各データを取得する
            list($id, $date, $text, $img) = explode("\t", $line);
            
            // $dataに各データを連想配列型で格納する
            array_unshift($this->data, array(
                ID=>$id,
                DATE=>$date,
                TEXT=>$text,
                IMG_FILE=>$img
            ));
        }
    }
    
    /*
     * メモデータをファイルに保存する
     */
    private function saveFile(): void
    {
        // 各データをタブで分割した文字列に変換する
        $lines = '';
        foreach ($this->data as $d) {
            $lines = $d[ID]."\t".$d[DATE]."\t".$d[TEXT]."\t".$d[IMG_FILE]."\n".$lines;
        }
        
        // ファイルに保存する
        file_put_contents($this->dirPath.SAVE_NAME, $lines);
    }
    
    /*
     * メモを追加する
     * @param string $text
     * @param string $imgPath
     * @throws NoInputException　TextTooLongException
     */
    public function addMemo(string $text, string $imgPath): void
    {
        // テキストも画像も空なら例外を投げる
        if (empty($text) && empty($imgPath)) {
            throw new NoInputException("テキストを入力するか画像を選択してください");
        }
        // テキストの文字数が200文字を超えていたら例外を投げる
        if (mb_strlen($text) > 200) {
            throw new TextTooLongException("テキストは200文字以内で入力してください");
        }
        // メモデータ用に有効な文字列に変換する
        $text = $this->toValidText($text);
        
        // $dataに配列を追加する
        array_unshift($this->data, array(
            ID => uniqid(),
            DATE => date('Y年m月d日H:i:s'),
            TEXT => $text,
            IMG_FILE => $imgPath
        ));
        
        // ファイルに保存する
        $this->saveFile();
    }
    
    /*
     * 入力されたテキストをメモデータに有効な文字列に変換して返す
     * @param string $text
     * @return string 変換した文字列
     */
    private function toValidText(string $text): string
    {
        $text = htmlspecialchars($text);
        // 改行コードを統一する
        $newLines = array("\r\n", "\r", "\n");
        $text = str_replace($newLines, "<br>", $text);
        // タブを除去する
        $text = str_replace("\t", "", $text);
        
        return $text;
    }
    
    /*
     * 指定したidのメモを削除する
     * @param array $deleteId 削除するメモのIDの配列
     */
    public function deleteMemo(array $deleteIds): void
    {
        $dataNum = count($this->data);
        // 指定されたIDと一致するメモデータがあれば削除する
        for ($i = 0; $i < $dataNum; $i++) {
            $d = $this->data[$i];
            if (in_array($d[ID], $deleteIds)) {
                if (is_readable($d[IMG_FILE])) {
                unlink($d[IMG_FILE]);
                }
                unset($this->data[$i]);
            }
        }
        $this->data = array_values($this->data);
        
        // ファイルに保存する
        $this->saveFile();
    }
}
