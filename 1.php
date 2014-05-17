<?php
	// MySQL START
	$pdo = new PDO("mysql:dbname=kanekan", "root");
	// Get events
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
	// ユーザ名リストボックスが縦に長過ぎたら見づらいので
	// とりあえずMAX5行で表示させる用のアレ
	$size = count($users)>5 ? 5 : count($users);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>かねかん(仮)：支払情報登録</title>
<link rel="stylesheet" type="text/css" href="./css/styles.css">
<script type="text/javascript"> 
<!--
	// イベント新規作成を選んだ時だけ
	// イベント名入力欄が表示されるようにしたい
	function eventChange() {
		obj = document.f1.event;
		index = obj.selectedIndex;
		if (index != 0) {
			//document.f1.newEventName.disabled = true;
			document.getElementById("newEvent").style.visibility = "hidden";
		} else {
			//document.f1.newEventName.disabled = false;
			document.getElementById("newEvent").style.visibility = "visible";
		}
	}

	// submit時の各種チェック	
	function formCheck() {
		obj = document.f1;
		errFlag = 0;
		// イベント新規作成なのに、イベント名空打ちはダメよ
		if (obj.event.selectedIndex == 0 && obj.newEventName.value == "") {
			errFlag = 1;
		}
		// 支払元のユーザが一人も選択されていなかったらダメよ
		if (document.getElementById("src").selectedIndex < 0) {
			errFlag = 2;
		}
		// 支払先のユーザが選択されていなかったらダメよ
		if (obj.dest.selectedIndex < 0) {
			errFlag = 3;
		}
		// 金額が入力されていなかったらダメよ
		// MEMO:NUMERICチェックもやっておくべきだ
		if (obj.price.value == "") {
			errFlag = 4;
		}
		// メモが入力されていなかったらダメよ
		if (obj.memo.value == "") {
			errFlag = 5;
		}
		// 全チェック通過時のみsubmit
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
	後々にDBに追加することを考え、
	フォームでイベント名／ユーザ名などをやりとりするのではなく、
	イベントID／ユーザIDを使う
	イベント名／ユーザ名はあくまでも画面表示用にする
-->
<form name="f1" method="POST" action="1-2.php" onSubmit="return formCheck()">
イベント：<br />
<select name="event" onChange="eventChange()">
<?php
	/*
		イベント名の連想配列をもとに、セレクトボックスを作成する
		インデックス０は新規イベント作成用
		インデックス１〜ｎが既存イベント用
		MEMO:もうちょい簡単に書けねぇのかな…
	*/
	echo "<option value='0' selected>新規イベントを作成</option>\n";
	foreach ($events as $key=>$val) {
		echo "<option value='$key'>$val</option>\n";
	}
?>
</select><br />
<div id="newEvent">
新規イベント名：<br />
<input type="text" name="newEventName" /><br />
</div>
支払元：<br />
<?php
	/*
		ユーザ名の連想配列をもとに、セレクトボックスを作成する
		複数選択OK
	*/
	echo "<select id='src' name='src[]' multiple size='$size'>\n";
	foreach ($users as $key => $val) {
		echo "<option value='$key'>$val</option>\n";
	}
?>
</select><br />
支払先：<br />
<?php
	/*
		ユーザ名の連想配列をもとに、セレクトボックスを作成する
		複数選択はダメ
	*/
	echo "<select name='dest' size='$size'>\n";
	foreach ($users as $key => $val) {
		echo "<option value='$key'>$val</option>\n";
	}
?>
</select><br />
金額：<br />
<input type="text" name="price" /><br />
金額切り上げ桁：<br />
<select name="round">
	<option value="1">1円単位</option>
	<option value="10">10円単位</option>
	<option value="100">100円単位</option>
	<option value="1000">1,000円単位</option>
</select><br />
品目：<br />
<input type="text" name="memo" /><br />
<br />
<input type="button" value="戻る" onClick="location.href='main.php'" />
<input type="submit" value="確認画面へ"/>
</form>

</body>
</html>
