<?php
	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
	// Get eventName or INSERT events
	$eventName = "";
	$eventMSG = "";
	$eventID = intval($_POST[event]);
	if ($eventID == 0) {
		// 新規イベントを追加する
		$eventName = $_POST[newEventName];
		$st = $pdo->prepare("INSERT INTO events(name, date) VALUES(?, ?)");
		$st->execute(array($eventName, date("Y-m-d")));
		$eventMSG = "新規イベント「" . $eventName ."」";
		$st = $pdo->query("SELECT id FROM events WHERE name=\"$eventName\"");
		while ($row = $st->fetch()) {
			$eventID = htmlspecialchars($row['id']);
		}
	} else {
		// 既存イベントのときは、イベント名を取ってくるだけ
		// FORMで貰えば、この処理は要らんな
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
	// INSERT payments
	$st = $pdo->prepare("INSERT INTO payments(sUser, dUser, event, price, memo, status) VALUES(?, ?, ?, ?, ?, '0')");
	$result = "";
	// 支払元の人数分だけINSERTするよ
	foreach ($_POST[src] as $src) {
		// 支払元≠支払先のときだけにしよう
		if ($src != $_POST[dest]) {
			$st->execute(array($src, $_POST[dest], $eventID, $_POST[price2], $_POST[memo]));
			$d = $_POST[dest];
			// あとで画面表示するときに、再度SELECTするとか面倒くさそうなので
			// 画面表示用のテキストを作っておく
			$result .= "\t\t<tr><td>$users[$src]</td><td>$users[$d]</td><td>$_POST[memo]</td><td>¥" . number_format($_POST[price2]) . "</td></tr>\n";
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：支払情報登録-登録完了</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
</head>
<body>


<p>
<?php echo "$eventName の支払情報を追加しました\n"; ?>
</p>
<table>
	<tr>
		<th>支払元</th>
		<th>支払先</th>
		<th>品目</th>
		<th>金額</th>
	</tr>
<?php echo $result; ?>
</table>
<form method="POST" action="main.php">
<input type="submit" value="メインメニューに戻る" />
</form>
</body>
</html>
