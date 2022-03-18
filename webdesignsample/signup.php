<?php

	//SIGNUP
	session_start();
	unset($_SESSION['user']);
	
	//--------------------------------------------------------------------
	//CABEÇALHO
	include 'cabecalho.php';
	
	//verificar se foram inseridos dados de utilizador
	if(!isset($_POST['btn_submit']))
	{
		ApresentarFormulario();
	}
	else
	{
		RegistarUtilizador();
	}
	
	//--------------------------------------------------------------------
	//RODAPE
	include 'rodape.php';
	
	//--------------------------------------------------------------------
	//FUNÇÕES
	//--------------------------------------------------------------------
	function ApresentarFormulario()
	{
		//apresenta o formulário para adição de novo utilizador
		echo '
			<form class="form_signup" method="post" action="signup.php?a=signup" enctype="multipart/form-data">
				<h3>Signup</h3><hr><br>
				Username:<br><input type="text" size="20" name="text_utilizador"><br><br>
				Password:<br><input type="password" size="20" name="text_password_1"><br><br>
				Re-escrever password:<br><input type="password" size="20" name="text_password_2"><br><br>
				<input type="hidden" name="MAX_FILE_SIZE" value="50000">
				Avatar:<input type="file" name="imagem_avatar"><br>
				<small>(Imagem do tipo <strong>JPG</strong>, tamanho máximo: <strong>50Kb</strong>)</small><br><br>
				<input type="submit" name="btn_submit" value="Registar"><br><br>
				<a href="index.php">Voltar</a>
			</form>
		';
	}
	
	//--------------------------------------------------------------------------------------------------------------------
	
	function RegistarUtilizador()
	{
		//executar as operacoens necessárias para o registo de um novo utilizador
		$utilizador = $_POST['text_utilizador'];
		$password_1 = $_POST['text_password_1'];
		$password_2 = $_POST['text_password_2'];
		//avatar
		$avatar = $_FILES['imagem_avatar'];
		$erro = false;
		
		//--------------------------------------------------------------------------------------------------------------------
		//verificacao de erros do utilizador
		if($utilizador == "" || $password_1 == "" || $password_2 == "")
		{
			//erro - nao foram preenchido os campos necessários
			echo '<div classs="erro">Não foram preencidos os campos necessários.</div>';
			$erro = true;
		}
		else if($password_1 != $password_2)
		{
			echo '<div class="erro">As passwords não coincidem.</div>';
			$erro = true;
		}
		else if ($avatar['name'] != "" && $avatar['type'] != "imagem/jpeg")
		{
			echo '<div class="erro">Ficheiro de imaguem inválido</div>';
			$erro = true;
		}
		else if ($avatar['name'] != "" && $avatar['size'] > $_POST ['MAX_FILE_SIZE'])
		{
			echo '<div class="erro">Ficheiro de imaguem maior do que o permitido</div>';
			$erro = true;
		}
		
		//veridicar se existem erros 
		if($erro)
		{
			ApresentarFormulario();
			include 'rodape.php';
			exit;
		}
		
		//registo do novo utilizador
		include 'config.php';
		$ligacao = new PDO("mysql:dbname=$base_dados;host=$host", $user,$password);
		
		//verificar se já existe um utilizador com o mesmo username
		$motor = $ligacao->prepare("SELECT username From users WHERE username= ?");
		$motot->bindParam(1, $utilizador, PDO::PARAM_STR);
		$motor->execute();
		
		if($motor->rowCount () != 0)
		{
			//erro - utilizador ja se encontra registreado
			echo '<div class="erro">Já existe um membro do forum com o mesmo username.</div>';
			$ligacao = null;
			ApresentarFormulario();
			include 'rodape.php';
			exit;
		}
		else
		{
			//registro do novo utilizador
			$motor = $ligacao->prepare("SELECT MAX(id_user) AS MaxID FROM users");
			$motor->execute();
			$id_temp = $motor->fetch(PDO::FETCH_ASSOC) ['MaxID'];
			if($id_temp == NULL)
			{
				$id_temp = 0;
			}
			else
			{
				$id_temp++;
			}
			//encriptar a password
			$passwordEncriptada = md5($password_1);
			
			$sql = "INSERT INTO users VALUES(:id_user, :user, :pass, :avatar)";
			$motor = $ligacao->prepare(sql);
			$motor->bindParam(":id_user", $id_temp, PDO::PARAM_INT);
			$motor->bindParam(":user", $utilizador, PDO::PARAM_STR);
			$motor->bindParam(":pass", $passwordEncriptada, PDO::PARAM_STR);
			$motor->bindParam(":avatar", $avatar['name'], PDO::PARAM_STR};
			$motor->execute();
			$ligacao = null;
		
			//upload do ficheiro de imagem do avatar
			move_uploaded_file($avatar[''tmp_name], "image/avatar/".$avatar['name']);

			//apresentar uma mensaguem de boas vindas ao novo utilizador
			echo '<div class="novo_registro_sucesso">Bem vindo ao Forum,<strong>'.$utilizador.'</strong><br><br>A partir deste
			momento está em condições de fazer o seu login e participar nesta comunidade online<br><br>
			<a href="index.php">Quadro de Login</a></div>';
			}
		}
	?>
