<?php 
$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$appID = '';
$status = '';



//echo $filtro;
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	//include("view_usuario_login.php");
        ?>
<div class="block small center login">
	<div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2><img src="images/ico/24x24/business_male_female_users.png" style="vertical-align:middle;">&nbsp;&nbsp;&nbsp;Cadastro de usuário para contracheque</h2>       
      </div>
      <div class="block_content">
		<form accept-charset="utf-8" action="controller_cadastrasenha.php" method="post" enctype="multipart/form-data" id="UsuarioForm" >
			<p>
			<h3>Usuário:</h3>
			<input type="text" name="usuario"  id="usuario"   class="required text" value="">
			</p>
			<p>
			<h3>Senha:</h3>
			<input type="password" name="senha"  id="senha"   class="required text" value="">		
			</p>
			
			<input type="hidden" value="35A62A8C-903E-86BA-C6D4-0F0DBC9325C7" name="appID">	
			<input type="hidden" value="cadastrasenha" name="i">			
			<input type="hidden" value="cadastra" name="acao">			
			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' id='erro' ><p id='txterroform'>Esta ferramenta é reservada ao setor de intendência.</p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div>
                        </div>

			<br>
			<p>
			<input type="submit" class="submit small" value="CADASTRAR" />
			</p>
		</form>
	</div>		<!-- .block_content ends -->
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>		<!-- .block ends -->
<script type="text/javascript">
//<![CDATA[
$('.mensagensform').hide();

function envia() {
  var dados = $("#<?php echo $controllernome; ?>Form").serialize();
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
	  registros = data;
	  status = registros['status'];
	  mensagem = registros['mensagemstatus'];
	  if(status=='ERRO'){
		  $("#txterroform").html(mensagem);
		  $('.mensagensform').show('bounce');       
		  $('#erro').show('bounce');       
		}else{
			conteudo = decodeURIComponent(registros['conteudo']);
			$("#listagem").html(conteudo);
			$("#spinner").css({'display':'none'});
			$( "#manipulacao" ).dialog( "close" );
		}
      $("#spinner").css({'display':'none'});
    },
    error: function() {},
    data: dados ,
    datatype: 'json',
    contentType: 'application/x-www-form-urlencoded'
  });
 }
</script>
		
<?php
	include("view_pagina_rodape.php");



?>		
