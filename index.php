<!DOCTYPE html>
<html>
<head>
	<title>To Do List App</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	session_start();

	include('config.php');
	include('todoList.php');
	require('vendor/autoload.php');
	
	$app = new TodoList( date('Ymd') );
	//$todolist = array_reverse($app->getTodos());
	$todolist = $app->getTodos();

	$reqMethod = $_SERVER['REQUEST_METHOD'];
	if ($reqMethod === 'POST') {
		// yeni todo ekle
		if (isset($_POST['ekle']) && !empty($_POST['mytask'])) {
			$app->add($_POST['mytask'], $_POST['status']=='on'?1:0);
		}
		// todo güncelle
		if (isset($_POST['update']) && !empty($_POST['mytask'])) {
			$app->update($_SESSION['task_id'], $_POST['mytask'], $_POST['status']=='on'?1:0);
			session_destroy();
		}
		// todo sıralamasını değiştir
		if (isset($_POST["oldIndis"]) && isset($_POST["newIndis"])) {
			$app->customSort($_POST["oldIndis"], $_POST["newIndis"]);
		}
	}else if($reqMethod === 'GET'){
		// todo sil
		if ($_GET['action']==='delete' && !empty($_GET['id'])) {
			$app->delete($_GET['id']);
		}
		// guncellenecek veriyi sessiona at (inputta taskı yazdırmak için)
		else if ($_GET['action']==='update' && !empty($_GET['id'])) {
			$_SESSION['task_id'] = $_GET['id'];
		}
	}
	?>

	<h4><?=$app->getTodoName()?></h4>
	<div style="overflow: hidden; position: relative; width: 100%;">
	<div class="lines"></div>
	<ul class="list">
		<li id="first">
			<form method="post">
				<input type="text" name="mytask" id="input" autofocus value="<?= $_GET['action']==='update'?$todolist[$_SESSION['task_id']-1][0]:'' ?>">
				<label style="display: none;">Yapıldı mı?</label>
				<input type="checkbox" name="status" <?= $_GET['action']==='update' && $todolist[$_SESSION['task_id']-1][1]===1 ? 'checked':'' ?> >	
				<input type="submit" value="Kaydet" name="<?= $_GET['action']==='update'?'update':'ekle' ?>">
			</form>
		</li>
		<?php
		for ($j=count($todolist); $j >0; $j--) {
			$v=$todolist[$j-1]; 
		 	echo '<li>'.($v[1]==1?"<del>$v[0]</del>":$v[0]).'<span>
			<a href="?action=delete&id='.($j).'">Sil</a>
			<a href="?action=update&id='.($j).'">Düzenle</a></span></li>';
		 } 
		/*foreach($todolist as $k=>$v){
			echo '<li>'.($v[1]==1?"<del>$v[0]</del>":$v[0]).'<span>
			<a href="?action=delete&id='.($k+1).'">Sil</a>
			<a href="?action=update&id='.($k+1).'">Düzenle</a></span></li>';
		}*/
		?>
	</ul>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
	var itemCount = $("ul.list li:not(#first)").length;
    $("ul.list").sortable({
    	items: "li:not(#first)",
		start: function (event, ui) {
			$(ui.item).data("startindis", (itemCount - ui.item.index()));
		},
		stop: function (event, ui) {
			self.sendUpdatedIndex(ui.item);
		}
	}).disableSelection();

	self.sendUpdatedIndex = function ($item) {
		var startIndis = $item.data("startindis");
		var newIndis = itemCount - $item.index();
		if (newIndis !== startIndis) {
			console.log(" oldIndis: ",startIndis," newIndis: ",newIndis);
			$.post(
				"index.php",
				{ oldIndis: startIndis, newIndis: newIndis}
			);
			// url lerdeki idlerin güncellenmesi için f5 atıyorum.
			// yapılmazsa silme ve düzenleme işlemleri başka elemana uygulanır.
			window.location.reload(true);
		}
	}
});
</script>
</html>