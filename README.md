LangChanger
===========
多言語ファイルの変換スクリプトです。
iPhoneの言語ファイルをAndroidの言語ファイルに変換します。
===========
For example
基本の変換
cat Localizable.strings | php iPhone2Android.php > string.xml

既存のAndroidの言語ファイルにマージする
cat Localizable.strings | php iPhone2Android.php -m strings.xml
これを実行するとandroid内の特殊なコメントで囲まれた部分は上書きされずに残る

特殊なコメント↓
<!-- start android only -->
// 残る部分
<!-- end android only -->

とたえば
iPhone
"hoge"="hogehoge";

Android
<string name="foo">foo</string>
<!-- start android only -->
<string name="bar">bar</string>
<!-- end android only -->

結果
<string name="hoge">hogehoge</string>
<!-- start android only -->
<string name="bar">bar</string>
<!-- end android only -->
