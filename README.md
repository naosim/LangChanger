LangChanger
===========
多言語ファイルの変換スクリプトです。<br>
iPhoneの言語ファイルをAndroidの言語ファイルに変換します。<br>
===========
基本の変換<br>
cat Localizable.strings | php iPhone2Android.php > string.xml<br>
<br>
既存のAndroidの言語ファイルにマージする<br>
cat Localizable.strings | php iPhone2Android.php -m strings.xml<br>
これを実行するとandroid内の特殊なコメントで囲まれた部分は上書きされずに残る<br>
<br>
特殊なコメント↓<br>
&lt;!-- start android only --&gt;<br>
// 残る部分<br>
&lt;!-- end android only --&gt;<br>
<br>
たとえば<br>
iPhone<br>
"hoge"="hogehoge";<br>
<br>
Android<br>
&lt;string name="foo">foo&lt;/string&gt;<br>
&lt;!-- start android only --&gt;<br>
&lt;string name="bar">bar&lt;/string&gt;<br>
&lt;!-- end android only --&gt;<br>
<br>
結果<br>
&lt;string name="hoge">hogehoge&lt;/string&gt;<br>
&lt;!-- start android only --&gt;<br>
&lt;string name="bar">bar&lt;/string&gt;<br>
&lt;!-- end android only --&gt;<br>
