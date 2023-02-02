<div class="block">
	<div class="block_content">
		<form accept-charset="utf-8" action="controller_<?php echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="<?php echo $controllernome; ?>Form" onSubmit="return false;">
		<table style="background-color: whitesmoke;margin-left: -16px;" class="vazia"  width="100%">
		<tr style="background-color: silver;">
		<th>&nbsp;</th>
		<th>CAMPO</th>
		<th>CRITÉRIO</th>
		<th>VALOR</th>
		<th>CONECTIVO</th>
		<th>&nbsp;</th>
		</tr>
		<?php 
			for($conta=10;$conta<=15;$conta++){
		?>
		<tr>
		<th>&nbsp;</th>
		<th>
			<select class="styled"  style="width: 100%;" name="campo<?php echo $conta; ?>" id="campo<?php echo $conta; ?>">
					<option value=''></option>
			<?php
			foreach ($campos as $titulo=>$valor){
				$selecionado = '';
				$titulo = strtoupper($titulo);
				if(!empty($formbusca['campo'.$conta]) && $formbusca['campo'.$conta]==$valor){
					$selecionado = ' selected=selected ';
				}
				echo "<option value='$valor' $selecionado >$titulo</option>";
			}
			?>
			</select>
		</th>
		<th>
			<select class="styled"  style="width: 100%;" name="criterio<?php echo $conta; ?>" id="criterio<?php echo $conta; ?>">
				<option value=''></option>
				<optgroup label="Data">
						<option value="dt_maiorque">Maior que</option>
						<option value="dt_menorque">Menor que</option>
						<option value="dt_igual">Igual a</option>
						<option value="dt_diferente">Diferente de</option>
				</optgroup>
					
					<optgroup label="Texto">
						<option value="txt_contem">Contém</option>
						<option value="txt_naocontem">Não contém</option>
						<option value="txt_igual">Igual a</option>
						<option value="txt_diferente">Diferente de</option>
					</optgroup>
					<optgroup label="Numero">
						<option value="num_maiorque">Maior que</option>
						<option value="num_menorque">Menor que</option>
						<option value="num_igual">Igual a</option>
						<option value="num_diferente">Diferente de</option>
					</optgroup>
					<?php
					 
					if(!empty($formbusca['criterio'.$conta])){
						$tmp = $filtrostipo[$formbusca['criterio'.$conta]];
						$tmptitulo = $filtros[$formbusca['criterio'.$conta]];
						$tmpvalor = $formbusca['criterio'.$conta];
						
						echo "<optgroup label='$tmp'><option value='$tmpvalor' selected=selected >$tmptitulo</option></optgroup>";
					}
						
					
					?>							
			</select>
		</th>
		<th><p><input type="text"style="text-transform: uppercase;width:100%;margin-top: 10px;"  class="text small"  name="valor<?php echo $conta; ?>" id="valor<?php echo $conta; ?>" value="<?php if(!empty($formbusca['valor'.$conta])){ echo $formbusca['valor'.$conta]; } ?>" /></p>
		</th>
		<th>
							<select class="styled" style="width: 100%;" name="conectivo<?php echo $conta; ?>" id="conectivo<?php echo $conta; ?>" >
									<option value=""></option>
									<option value="AND">E</option>
									<option value="OR">OU</option>
					<?php
					 
					if(!empty($formbusca['conectivo'.$conta])){
						$tmp = $filtrostipo[$formbusca['conectivo'.$conta]];
						$tmptitulo = $filtros[$formbusca['conectivo'.$conta]];
						$tmpvalor = $formbusca['conectivo'.$conta];
						
						if($tmpvalor=='OR'){
							echo "<option value='$tmpvalor' selected=selected >OU</option>";
						}
						if($tmpvalor=='AND'){
							echo "<option value='$tmpvalor' selected=selected >E</option>";
						}
					}
						
					
					?>							
					</select>
		</th>
		<th>&nbsp;</th>
		</tr>
		<?php 
		}
		?>
		
		</table>
						
		
			<hr>
			<input type="hidden" name="acao" id="acao" value="filtro" />
			<input type="hidden" name="controller" id="controller" value="<?php echo $controllernome; ?>" />
			<input type="hidden" name="pagina" id="pagina" value="1" />

			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' id='erro' ><p id='txterroform'></p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div></div>


			<p><input type="submit" class="submit small" value="Cadastrar" onClick="envia();"/><input type="button" class="submit small"  value="Limpa"  onclick="limpaform('#<?php echo $controllernome; ?>Form');" />
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

function limpaform(id){

	    // iterate over all of the inputs for the form

	    // element that was passed in
		var form = $(id);
	    $(':input', form).each(function() {

	      var type = this.type;

	      var tag = this.tagName.toLowerCase(); // normalize case

	      // it's ok to reset the value attr of text inputs,

	      // password inputs, and textareas

	      if (type == 'text' || type == 'password' || tag == 'textarea')

	        this.value = "";

	      // checkboxes and radios need to have their checked state cleared

	      // but should *not* have their 'value' changed

	      else if (type == 'checkbox' || type == 'radio')

	        this.checked = false;

	      // select elements need to have their 'selectedIndex' property set to -1

	      // (this works for both single and multiple select elements)

	      else if (tag == 'select')

	        this.selectedIndex = -1;

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
	   	 
	    $( "#qtd_militar_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });
	    $( "#qtd_diaria_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });
		 $( ".datepicker" ).datepicker({
			 showButtonPanel: true,
			 buttonImage: "images/calendario.png",
			 buttonImageOnly: true
			}); 
   		 $( ".datepicker" ).datepicker();
		 
});    
</script>


