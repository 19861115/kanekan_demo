<?php
	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
	// Get eventName
	$eventName = ""; //イベント名
	$eventMSG = ""; //イベント名を画面に表示するための文字列
	if (intval($_POST[event]) == 0) {
		$eventName = $_POST[newEventName];
		$eventMSG = "新規イベント「" . $eventName . "」";
	} else {
		// ここでもう一回イベント名を取りに行くのが無駄な気がする
		// FORMで受け取ったほうがいいかなぁ
		$st = $pdo->query("SELECT name FROM events WHERE id=$_POST[event]");
		while ($row = $st->fetch()) {
			$eventName = htmlspecialchars($row['name']);
			$eventMSG = "イベント「" . $eventName . "」";
		}
	}
	// Get users
	// フォームからはユーザIDが送られてくるので、それに対応するユーザ名を表示するために
	// ユーザも連想配列を作り直す（無駄…）
	$users = array();
	$st = $pdo->query("SELECT * FROM users ORDER BY name");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		$name = htmlspecialchars($row['name']);
		$users[$id] = $name;
	}
	// Calc price
	// 支払い金額の計算
	$price = intval($_POST[price]); // フォームに入力された金額
	$price2 = $price; // ひとりあたりの金額
	$priceMsg = "¥" . number_format($price);
	// 支払元が複数選択されていた時は、割り勘にする
	// 選択された桁で切り上げる
	$cnt = count($_POST[src]);
	if ($cnt > 1) {
		$price2 = ceil($price2 / $cnt / $_POST[round]) * $_POST[round];
		$priceMsg .= "<br />(一人 ¥" . number_format($price2) ." )";
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：支払情報登録-確認</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
</head>
<body>


<p>
	<?php echo "$eventMSG に下記データを追加します\n"; ?>
</p>
<table>
	<tr>
		<th>支払元</th>
		<th>支払先</th>
		<th>品目</th>
		<th>金額</th>
	</tr>
	<tr>
		<td>
			<?php foreach ($_POST[src] as $s) echo "$users[$s]<br />"; echo "\n"; ?>
		</td>
		<td>
			<?php $d = $_POST[dest]; echo "$users[$d]\n"; ?>
		</td>
		<td>
			<?php echo "$_POST[memo]\n"; ?>
		</td>
		<td>
			<?php echo "$priceMsg\n"; ?>
		</td>
	</tr>
</table>
<form name="f1" method="POST" action="1-3.php">
<?php
	echo "<input type='hidden' name='event' value='$_POST[event]' />\n";
	echo "<input type='hidden' name='newEventName' value='$_POST[newEventName]' />\n";
	foreach ($_POST[src] as $s) {
		echo "<input type='hidden' name='src[]' value='$s' />\n";
	}
	echo "<input type='hidden' name='dest' value='$_POST[dest]' />\n";
	echo "<input type='hidden' name='price' value='$_POST[price]' />\n";
	echo "<input type='hidden' name='price2' value='$price2' />\n";
	echo "<input type='hidden' name='memo' value='$_POST[memo]' />\n";
?>
<input type="button" value="入力画面に戻る" onClick="history.back(); false;" />
<input type="submit" value="確定" />
</form>
</body>
</html>
