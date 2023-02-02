# memoApp

開発環境：Eclipse
ライブラリ管理：composer
使用言語：PHP

#
使用ライブラリ
・class.upload.php：　https://github.com/verot/class.upload.php

参考URL： https://abyss-to-salena.net/web/php%e7%94%bb%e5%83%8f%e6%8a%95%e7%a8%bf%e3%83%a9%e3%82%a4%e3%83%96%e3%83%a9%e3%83%aa/

#
このアプリケーションについて

提出用に作成したWeb上で動くメモアプリです。

ソースコードを見て頂くのが目的のため、自分がコードを書いたphpファイルのみをアップロードしています。

作成にあたって下記WEBサイト・書籍を参考しました。

・簡易メモ帳の作り方： https://code-notes.com/lesson/4

・PHP公式リファレンス

・PHP本格入門［上］・［下］

・その他WEB上の記事等

このアプリはデータベースを使用せずにファイルにメモのデータを保存し、

それを読み込むことで表示させるメモアプリです。

工夫した点は

・機能を自分なりに整理してMemoクラスと関数を作成した

・名前ごとにデータを分けて保存し、入力した名前によって表示させるようにした

・画像ファイルのアップロード・表示をできるようにした

・テキストやファイルに制限を付け、想定しないものは例外を発生させるようにした

#
javaにしなかった理由について

・一週間前後で作れる規模のjavaアプリケーションのイメージが浮かばなかった

・seleniumを使ったプログラムをゼロから作ったことがなかった

上記の理由から一週間で提出するのは難しいと思い、学習用に少し作り始めていたPHPのメモアプリを作成し提出しました。
