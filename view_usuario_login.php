<div class="block small center login">
	<div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2><img src="images/ico/24x24/business_male_female_users.png" style="vertical-align:middle;">&nbsp;&nbsp;&nbsp;Acesso ao Sistema</h2>       
      </div>
      <div class="block_content">
		<form accept-charset="utf-8" action="controller_usuario.php" method="post" enctype="multipart/form-data" id="UsuarioForm" >
			<p>
			<h3>E-mail:</h3>
			<input type="text" name="email"  id="email"   class="required text" value="">
			</p>
			<p>
			<h3>Senha:</h3>
			<input type="password" name="senha"  id="senha"   class="required text" value="">		
			</p>
			
			<input type="hidden" value="35A62A8C-903E-86BA-C6D4-0F0DBC9325C7" name="appID">			
			<input type="hidden" value="usuario" name="i">			
			<input type="hidden" value="login" name="acao">			
			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' style='display:none;' id='erro' ><p id='txterroform'></p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div></div>

			<br>
			<p>
			<input type="submit" class="submit small" value="ACESSAR" />
			<span class="note"><a href="?i=rememberpwd&amp;appID=35A62A8C-903E-86BA-C6D4-0F0DBC9325C7">Esqueceu a senha?</a></span>			
			</p>
		</form>
		
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
	  var valor = $('#'+origem).val();
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

function comutacampo(controller, origem, destino, campodestino, resposta, ordem  , tipodestino, valor) {
	  var parametros = {'controller':controller,'acao':'comuta', 'campo':origem, 'valor': valor, 'campodestino':campodestino, 'destino':destino, 'resposta':resposta, 'ordem':ordem, 'tipodestino':tipodestino };
	  $.ajax({
		type: 'POST',
		 processData: true,
	    url: 'controller_<?php echo $controllernome; ?>.php',
	    beforeSend: function(){
	      $("#spinner").css({'display':'block'});
		},
	    success: function(data) {
		    if(tipodestino == 'select'){
		  		$('#'+destino).html(data);
			}else{
				if(tipodestino == 'qtd'){
					$('#'+destino).spinner({ max: data });
					$('#'+destino).val(data);
				}else{
					$('#'+destino).val(data);
				}
			}

		    
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
		$('.wysiwyg').attr('height','400px');
	    $( "#minutos" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" }, min:0 });
	    $( "#quantidade" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" }, min:0 });
	    
		//$( "#materia" ).autocomplete({ source: [ <?php echo $completamateria; ?>] });    
		
	   //	 $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] ); 
	   	 
	    //$( "#qtd_militar_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });	    $( "#qtd_diaria_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });
		 //$( ".datepicker" ).datepicker({ showButtonPanel: true,	 buttonImage: "images/calendario.png",	 buttonImageOnly: true		}); 
   		// $( ".datepicker" ).datepicker();
		 
});    
</script>


