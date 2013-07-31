<?php
function getPIPE() {
	$pipe = file_get_contents('php://stdin');
	return $pipe;
}

function getHelp() {
	$str = "";
	$str .= "example\n";
	$str .= "cat Localizable.strings | php iPhone2Android.php > string.xml\n";
	$str .= "\n";
	$str .= "if you want to merge existing strings.xml for android,\n";
	$str .= "cat Localizable.strings | php iPhone2Android.php -m strings.xml\n";
	return $str;
}

function getOutputFileName($argv) {
	if (count($argv) == 3 && $argv[1] == "-m")
		return $argv[2];
	if (count($argv) == 2 && $argv[1] != "help")
		return $argv[1];
	return false;
}

function getMergeXML($argv) {
	// print_r($argv);
	if (count($argv) < 3 || $argv[1] != "-m")
		return "";

	$result = "";
	$xml = file_get_contents($argv[2]);
	$lines = explode("\n", $xml);
	$inComment = false;
	foreach ($lines as $i => $line) {
		if (strpos($line, "<!-- start android") !== false) {
			$inComment = true;
			$result .= "<!-- start android only -->" . "\n";
			continue;
		}
		if (strpos($line, "<!-- end android") !== false) {
			$inComment = false;
			$result .= "<!-- end android only -->" . "\n";
			continue;
		}
		if ($inComment) {
			$result .= $line . "\n";
		}
	}
	return $result;
}
/*
 * 文言部分を取得する
 * "hoge\"foo"; => hoge\"foo
 */
function getValue($v) {
	$v = trim($v);
	$v = str_replace(";", "", $v);
	
	$v = str_replace("\\\"", "####Qt####", $v);
	$v = str_replace("\"", "", $v);
	$v = str_replace("####Qt####", "\\\"", $v);
	
	// %d -> %1$d
	$v = str_replace("\\%", "####per####", $v);
	$ary = explode("%", $v);
	$v = $ary[0];
	for ($i=1; $i < count($ary); $i++) { 
		$v .= '%' . $i . '$' . $ary[$i];
	}
	$v = str_replace("####per####", "\\%", $v);
	
	return $v;
}

// "hoge" = "foo";//comment
// -> <string name="hoge">foo</string><!-- comment -->
function exchangeLine($line) {
	$result = "";
	$line = trim($line);
	$a = explode("//", $line);
	if (strlen($a[0]) > 0) {
		$b = explode("=", $a[0]);
		if (count($b) == 2) {
			$key = trim($b[0]);
			$value = getValue($b[1]);
			$result .= "<string name=" . $key . ">" . $value . "</string>";
		}
	}

	// 後半のコメント
	if (count($a) > 1) {
		$result .= "<!-- " . trim($a[1]) . " -->";
	}
	return $result;
}

function hasStartComment($line) {
	return strpos($line, "/*") !== false;
}

function hasEndComment($line) {
	return strpos($line, "*/") !== false;
}

function exchange($input) {
	$result = "";
	$lines = explode("\n", $input);
	$inComment = false;
	foreach ($lines as $i => $line) {
		// コメントの開始
		if (hasStartComment($line)) {
			$line = str_replace("/*", "<!-- ", $line);
			$inComment = true;
		}
		// コメント終了
		if (hasEndComment($line)) {
			$line = str_replace("*/", " -->", $line);
			$inComment = false;
			$result .= $line . "\n";
			continue;
		}

		if ($inComment) {
			// コメント内なら何もしない
			$result .= $line . "\n";
		} else {
			// コメント外なら行をxml化する
			$result .= exchangeLine($line) . "\n";
		}
	}
	return $result;
}

if (count($argv) > 1 && $argv[1] == "help") {
	echo getHelp();
	exit ;
}
$input = getPIPE();

$result = "";
$result .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$result .= "<resources>\n";
$result .= exchange($input);
$result .= getMergeXML($argv);
$result .= "</resources>\n";

$filename = getOutputFileName($argv);
if ($filename !== false) {
	file_put_contents($filename, $result);
} else {
	echo $result;
}
