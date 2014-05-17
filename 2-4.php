<?php
	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
	// Get events
	$events = array();
	$st = $pdo->query("SELECT * FROM events");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		$name = htmlspecialchars($row['name']);
		$events[$id] = $name;
	}
	// Get users
	$users = array();
	$st = $pdo->query("SELECT * FROM users ORDER BY name");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		$name = htmlspecialchars($row['name']);
		$users[$id] = $name;
	}
	// UPDATE payments
	$result = "";
	foreach ($_POST[seisan] as $s) {
		// ステータスを精算済にするよ
		$st = $pdo->query("UPDATE payments SET status='1' WHERE id=$s");
		// 2-2.php,2-3.phpと同じ
		$st = $pdo->query("SELECT * FROM payments WHERE id=$s");
		while ($row = $st->fetch()) {
			$sUser = htmlspecialchars($row['sUser']);
			$dUser = htmlspecialchars($row['dUser']);
			$event = $events[htmlspecialchars($row['event'])];
			$price = htmlspecialchars($row['price']);
			$memo = htmlspecialchars($row['memo']);
			$result .= "\t<tr>\n";
			$result .= "\t\t<td>$users[$sUser]</td>\n";		
			$result .= "\t\t<td>$users[$dUser]</td>\n";		
			$result .= "\t\t<td>$event</td>\n";
			$result .= "\t\t<td>$memo</td>\n";
			$result .= "\t\t<td>¥";
			$result .= number_format($price);
			$result .= "</td>\n";
			$result .= "\t</tr>\n";
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：精算-精算完了</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
</head>
<body>


<p>
<?php echo "下記支払情報の精算が完了しました\n"; ?>
</p>
<form method="POST" action="main.php">
<table>
	<tr>
		<th>支払元</th>
		<th>支払先</th>
		<th>イベント</th>
		<th>品目</th>
		<th>金額</th>
	</tr>
<?php
	echo "$result";
?>
</table>
<input type="submit" value="メインメニューに戻る" />
</form>
</body>
</html>
