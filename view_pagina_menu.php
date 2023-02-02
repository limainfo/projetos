
		<div id="header">
			<ul id="nav">
				<li><a href="#"><img src="images/menu_gerente01.png">&nbsp;Admin</a>
					<ul>
								<li><a
							href="controller_usuario.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=usuario&amp;acao=list"><img
								src="images/menu_parametro05.png">&nbsp;Usuários</a></li>								

					 </ul></li>


				<li><a href="#"><img src="images/menu_relatorio01.png">&nbsp;Insere ESTATÍSTICA</a>
					<ul>
						<li><a
							href="controller_planejado.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=planejado&amp;acao=list&amp;pagina=1"><img
								src="images/menu_gerente02.png">&nbsp;Upload de Arquivo</a></li>
						<li><a
							href="controller_movimento.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;controller=movimento&amp;acao=list"><img
								src="images/menu_relatorio02.png">&nbsp;Arquivos Enviados</a></li>
						<li>
					</ul></li>


				<li class=""><a href="#"><img src="images/menu_parametro01.png">&nbsp;Analisa ESTATÍSTICA</a>
					<ul>
						<li><a
							href="controller_estatistica.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=estatistica&amp;acao=list"><img
								src="images/menu_parametro04.png">&nbsp;Arquivos Enviados</a></li>
					</ul></li>

				<li><a href="manual/ManualSistema.pdf" target="_blank"><img src="images/menu_gerente04.png">&nbsp;DASHBOARD</a>
				<ul>
						<li><a
							href="controller_estatistica.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=estatistica&amp;acao=list"><img
								src="images/menu_parametro04.png">&nbsp;Perfis</a></li>

						<li><a
							href="controller_estatistica.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=estatistica&amp;acao=list"><img
								src="images/menu_parametro05.png">&nbsp;Usuários</a></li>

						<li class="nobg"><a
							href="controller_estatistica.php?d=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7&amp;i=estatistica&amp;acao=SQL"><img
								src="images/menu_parametro05.png">&nbsp;SQL</a></li>

					</ul>			</li>
					
			</ul>
			<p class="user">
				Seja Bem Vindo, <a href="#"><?php echo $usuario; ?> </a> | <a
					href="index.php">Sair</a>
			</p>
		</div>
		<script>
	//$( "#manipulacao" ).dialog( "close" );
</script>