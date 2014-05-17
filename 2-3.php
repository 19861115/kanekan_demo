<?php
	$u1 = $_POST[user1]; // なんでか知らんがPOSTの値を直接読むとうまくいかなくなったので、いったん変数に退避
	$u2 = $_POST[user2]; // なんでか知らんがPOSTの値を直接読むとうまくいかなくなったので、いったん変数に退避
	
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

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：精算-確認</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
<script type="text/javascript">
<!--


-->
</script>
</head>
<body>


<form name="f1" method="POST" action="2-4.php">
<p>
	精算確認
</p>
<table>
	<tr>
		<th>支払元</th>
		<th>支払先</th>
		<th>イベント</th>
		<th>品目</th>
		<th>金額</th>
	</tr>
<?php
	$payment[$u1] = 0;
	$payment[$u2] = 0;
	foreach ($_POST[seisan] as $s) {
		$st = $pdo->query("SELECT * FROM payments WHERE id=$s");
		while ($row = $st->fetch()) {
			// 2-2.phpと同じ
			// 事後追記：これももっとスマートに出来たな
			$id = htmlspecialchars($row['id']);
			$sUser = htmlspecialchars($row['sUser']);
			$dUser = htmlspecialchars($row['dUser']);
			$event = $events[htmlspecialchars($row['event'])];
			$price = htmlspecialchars($row['price']);
			$memo = htmlspecialchars($row['memo']);
			echo "\t<tr>\n";
			echo "\t\t<td>$users[$sUser]</td>\n";
			echo "\t\t<td>$users[$dUser]</td>\n";
			echo "\t\t<td>$event</td>\n";
			echo "\t\t<td>$memo</td>\n";
			echo "\t\t<td>¥" . number_format($price) . "</td>\n";
			echo "\t\t<input type=\"hidden\" name=\"seisan[]\" value=\"$id\" />\n";
			echo "\t</tr>\n";
			$payment[$sUser] += intval($price);
		}
	}
?>
</table>

<hr />

<p>
	相殺結果：
<?php
	if ($payment[$u1] == $payment[$u2]) {
		echo "¥0 (支払金額なし)\n";
	} else if ($payment[$u1] > $payment[$u2]) {
		echo "$users[$u1] が $users[$u2] に ¥" . number_format($payment[$u1] - $payment[$u2]) . " 支払います\n";
	} else {
		echo "$users[$u2] が $users[$u1] に ¥" . number_format($payment[$u2] - $payment[$u1]) . " 支払います\n";
	} 
?>
</p>

<input type="button" value="戻る" onClick="history.back(); false;" />
<input type="submit" value="確定" />
</form>
</body>
</html>
