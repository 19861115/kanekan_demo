<?php
	$u1 = $_POST[user1]; // なんでか知らんがPOSTの値を直接読むとうまくいかなくなったので、いったん変数に退避
	$u2 = $_POST[user2]; // なんでか知らんがPOSTの値を直接読むとうまくいかなくなったので、いったん変数に退避
	$counter1 = 0;
	$counter2 = 0;

	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
	// Get events
	// SELECT文を工夫すればこの処理要らないはずなんだけど
	// JOINをよくわかってないので、とりあえずイベントを取得しておく
	$events = array();
	$st = $pdo->query("SELECT * FROM events");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		$name = htmlspecialchars($row['name']);
		// 画面表示用に、イベント名の連想配列を作っておく
		// キーはイベントID
		$events[$id] = $name;
	}
	// Get users
	$users = array();
	$st = $pdo->query("SELECT * FROM users ORDER BY name");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		$name = htmlspecialchars($row['name']);
		// 画面表示用に、ユーザ名の連想配列を作っておく
		// キーはユーザID
		$users[$id] = $name;
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：精算-対象の選択</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
<script type="text/javascript">
<!--


-->
</script>
</head>
<body>


<form name="f1" method="POST" action="2-3.php">
<p>
	<?php echo "$users[$u1] から $users[$u2] への未精算"; ?>
</p>
<table>
	<tr>
		<th>イベント</th>
		<th>品目</th>
		<th>金額</th>
		<th>精算対象</th>
	</tr>
<?php
	// 支払元がu1で、支払先がu2のレコード抽出
	$st = $pdo->query("SELECT * FROM payments WHERE sUser=\"$u1\" AND dUser=\"$u2\" AND status=\"0\"");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		/*
			イベントIDを引っ張ってくるので
			↑で作った連想配列を使ってイベント名を出す
			
			事後追記：
			SELECT p.id, sUser, su.name AS sName, dUser, du.name AS dName, event, e.name AS eName,
				price, memo, status FROM payments AS p
				JOIN users AS su ON sUser=su.id
				JOIN users AS du ON dUser=du.id
				JOIN events AS e ON event=e.id;
			とかやれば、変なことせずにイベント名もユーザ名も一気に引っ張ってこれたな
			処理買えるの面倒だから、本番に期待
		*/
		$event = $events[htmlspecialchars($row['event'])];
		$price = htmlspecialchars($row['price']);
		$memo = htmlspecialchars($row['memo']);
		echo "\t<tr>\n";
		echo "\t\t<td>$event</td>\n";
		echo "\t\t<td>$memo</td>\n";
		echo "\t\t<td>¥" . number_format($price) . "</td>\n";
		echo "\t\t<td><input type=\"checkbox\" name=\"seisan[]\" value=\"$id\" checked />\n";
		echo "\t</tr>\n";
		$counter1++;
	}
	echo "<input type=\"hidden\" name=\"user1\" value=\"$u1\" />\n";
?>
</table>

<?php
	if ($counter1==0) {
		echo "<p>未精算なし</p>\n";
	}
?>
<hr />

<p>
	<?php echo "$users[$u2] から $users[$u1] への未精算"; ?>
</p>
<table>
	<tr>
		<th>イベント</th>
		<th>品目</th>
		<th>金額</th>
		<th>精算対象</th>
	</tr>
<?php
	// 支払元がu2で、支払先がu1のレコード抽出
	// 全く同じ処理が２個あるというのも、無駄だなぁ
	// まとめられるならまとめたい
	$st = $pdo->query("SELECT * FROM payments WHERE sUser=\"$u2\" AND dUser=\"$u1\" AND status=\"0\"");
	while ($row = $st->fetch()) {
		$id = htmlspecialchars($row['id']);
		// 同上
		$event = $events[htmlspecialchars($row['event'])];
		$price = htmlspecialchars($row['price']);
		$memo = htmlspecialchars($row['memo']);
		echo "\t<tr>\n";
		echo "\t\t<td>$event</td>\n";
		echo "\t\t<td>$memo</td>\n";
		echo "\t\t<td>¥" . number_format($price) . "</td>\n";
		echo "\t\t<td><input type=\"checkbox\" name=\"seisan[]\" value=\"$id\" checked />\n";
		echo "\t</tr>\n";
		$counter2++;
	}
	echo "<input type=\"hidden\" name=\"user2\" value=\"$u2\" />\n";
?>
</table>

<?php
	if ($counter2==0) {
		echo "<p>未精算なし</p>\n";
	}
?>


<input type="button" value="戻る" onClick="history.back(); false;" />
<?php
	if ($counter1 + $counter2 > 0) {
		echo "<input type=\"submit\" value=\"確認画面へ\" />";
	}
?>
</form>
</body>
</html>
