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

	$todolist = $app->getTodos();
	$reqMethod = $_SERVER['REQUEST_METHOD'];

	if ($reqMethod === 'POST') {
		if (isset($_POST['ekle']) && !empty($_POST['mytask'])) {
			$app->add($_POST['mytask'], $_POST['status']=='on'?1:0);
		}
		if (isset($_POST['update']) && !empty($_POST['mytask'])) {
			$app->update($_SESSION['task_id'], $_POST['mytask'], $_POST['status']=='on'?1:0);
			session_destroy();
		}
	}else if($reqMethod === 'GET'){
		if ($_GET['action']==='delete' && !empty($_GET['id'])) {
			$app->delete($_GET['id']);
		}else if ($_GET['action']==='update' && !empty($_GET['id'])) {
			$_SESSION['task_id'] = $_GET['id'];
		}
	}

	?>

	<h4><?=date('Ymd')?></h4>
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
		foreach($todolist as $k=>$v){
			echo '<li>'.($v[1]==1?"<del>$v[0]</del>":$v[0]).'<span>
			<a href="?action=delete&id='.($k+1).'">Sil</a>
			<a href="?action=update&id='.($k+1).'">Düzenle</a></span></li>';
		}
		?>
	</ul>
</div>
</body>
</html>