<?php
	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
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
	// ユーザ名リストボックスが縦に長過ぎたら見づらいので
	// とりあえずMAX5行で表示させる用のアレ
	$size = count($users) > 5 ? 5 : count($users);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：精算</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
<script type="text/javascript"> 
<!--
	function formCheck() {
		obj = document.f1;
		errFlag = 0;
		if (obj.user1.selectedIndex == obj.user2.selectedIndex) {
			errFlag = 1;
		}
		if (errFlag == 0) {
			return true;
		} else {
			return false;
		}
	}
-->
</script>
</head>
<body>

<!--
	リストボックスを１つにして、２レコード選んでもらうように
	しても良かったんだが、とりあえず２つにしてみよう
	→結果、面倒なことに…
-->
<form name="f1" method="POST" action="2-2.php" onSubmit="return formCheck()">
精算対象者その１：<br />
<?php
	/*
		ユーザ名の連想配列をもとに、セレクトボックスを作成する
		複数選択はダメ
	*/
	echo "<select name='user1' size='$size'>\n";
	foreach ($users as $key => $val) {
		echo "<option value='$key'>$val</option>\n";
	}
?>
</select><br />
精算対象者その２：<br />
<?php
	/*
		ユーザ名の連想配列をもとに、セレクトボックスを作成する
		複数選択はダメ
	*/
	echo "<select name='user2' size='$size'>\n";
	foreach ($users as $key => $val) {
		echo "<option value='$key'>$val</option>\n";
	}
?>
</select><br />
<input type="button" value="戻る" onClick="location.href='main.php'" />
<input type="submit" value="精算をする"/>
</form>

</body>
</html>
