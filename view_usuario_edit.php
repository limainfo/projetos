

<div class="block">
	<div class="block_content">

		<form accept-charset="utf-8" action="controller_<?php echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="<?php echo $controllernome; ?>Form" onSubmit="return false;">
			<h3>Email:</h3>
			<p>
			<input type="text" name="email" id="email" value="<?php echo $dadosusuario['email']; ?>"  readonly="readonly"  class="text small" /></p>		
			</p>
			<h3>Senha:</h3>
			<p>
			<input type="password" name="senha"  id="senha"   class="text medium" value="<?php echo $dadosusuario['senha']; ?>">
			</p>
			<h3>Perfil:</h3>
			<p>
			<select name="perfil" id="perfil" class="multiple styled" >
			<?php 
				echo '<option value="'.$dadosquestao['perfil'].'"  selected="selected" >'.$dadosusuario['perfil'].'</option>';
			?>
				<option value="ADMIN"  >ADMIN</option>
			<option value="INSERE_ESTATISTICA"  >INSERE ESTATÍSTICA</option>
			<option value="ANALISA_ESTATISTICA"  >ANALISA ESTATISTICA</option>
			<option value="DASHBOARD"  >DASHBOARD</option>
			</select>
			</p>		
			<hr>
			<input type="hidden" name="acao" id="acao" value="edit" />
			<input type="hidden" name="<?php echo $campouuid; ?>" id="<?php echo $campouuid; ?>" value="<?php echo $dadosusuario[$campouuid]; ?>" />
			<input type="hidden" name="controller" id="controller" value="<?php echo $controllernome; ?>" />
			<input type="hidden" name="pagina" id="pagina" value="1" />

			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' id='erro' ><p id='txterroform'></p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div></div>


			<p><input type="submit" class="submit small" value="Cadastrar" onClick="envia();"/>
			</p>
		</form>		
		
<script type="text/javascript">
//<![CDATA[
$('#erro').hide();
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

 
function atualiza() {
  var id = $("#SetoresUnidadeID").val();
  var parametros = {'controller':'unidadesregionais','action':'select', 'unidadeid':id  };
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
	  $('select#SetoresRegionalID').html(data);
      $("#spinner").css({'display':'none'});
    },
    error: function() {},
    data: parametros ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
  
 }



function mudaselect(controller, campoorigem, campodestino) {
  var valor = $("#"+campoorigem).val();
  var parametros = {'controller':controller,'acao':'select', 'campo':campoorigem, 'valor': valor, 'campodestino':campodestino };
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
	  $('#'+campodestino).html(data);
      $("#spinner").css({'display':'none'});
    },
    error: function() {},
    data: parametros ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
  
 }

 
//]]>
</script>

	</div>		<!-- .block_content ends -->
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>		<!-- .block ends -->



<script type="text/javascript">
jQuery(document).ready(function($) {
  	 $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] ); 
		 $( ".datepicker" ).datepicker({
			 showButtonPanel: true,
			 buttonImage: "images/calendario.png",
			 buttonImageOnly: true
			}); 
		 $( ".datepicker" ).datepicker();

});    
</script>


