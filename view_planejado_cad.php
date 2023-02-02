
<div class="block">
	<div class="block_content">
		<form accept-charset="utf-8" action="controller_<?php echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="<?php echo $controllernome; ?>Form" onSubmit="return false;">
			<p><h3>Ano:</h3><input type="text" name="ano" id="ano" style="text-transform: uppercase;"  value="2015" /></p>		
			<p><h3>Subdivisão:</h3><input type="text" name="subdivisao" id="subdivisao_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Início:</h3><input type="text" name="inicio" id="inicio" class="datepicker styled" style="text-transform: uppercase;"  /></p>		
			<p><h3>Término:</h3><input type="text" name="termino" id="termino" class="datepicker styled" style="text-transform: uppercase;" /></p>		
			<p><h3>Mês:</h3><input type="text" name="mes" id="mes_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Status:</h3><input type="text" name="status" id="status_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Tipo:</h3><input type="text" name="tipo" id="tipo_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Descrição:</h3><input type="text" name="descricao" id="descricao_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Local:</h3><input type="text" name="local" id="local_" class="text medium" style="text-transform: uppercase;" /></p>		
			<p><h3>Qtd militar:</h3><input type="text" name="qtd_militar" id="qtd_militar_" style="text-transform: uppercase;"  onchange="calculadiariapassagem('qtd_militar_');"  value="0" /></p>		
			<p><h3>Qtd diária:</h3><input type="text" name="qtd_diaria" id="qtd_diaria_" style="text-transform: uppercase;"  value="0"  onchange="calculadiaria('qtd_diaria_');"  /></p>		
			<p><h3>Adicional:</h3><input type="text" name="adicional" id="adicional_" class="text medium" style="text-transform: uppercase;"  value="95"  onchange="calculadiaria('adicional_');"  /></p>		
			<p><h3>Valor diária:</h3><input type="text" name="valor_diaria" id="valor_diaria_" style="text-transform: uppercase;"  value="0"   onchange="calculadiaria('valor_diaria_');" /></p>		
			<p><h3>Valor passagem:</h3><input type="text" name="valor_passagem" id="valor_passagem_" style="text-transform: uppercase;"  value="0"   onchange="calculadiariapassagem('valor_passagem_');" /></p>		
			<p><h3>Pln diária:</h3><input type="text" name="pln_diaria" id="pln_diaria_" style="text-transform: uppercase;"  value="0"   readonly="readonly" /></p>		
			<p><h3>Pln passagem:</h3><input type="text" name="pln_passagem" id="pln_passagem_" style="text-transform: uppercase;"  value="0"   readonly="readonly"  /></p>		
			<p><h3>Real diária:</h3><input type="text" name="real_diaria" id="real_diaria_" style="text-transform: uppercase;"  value="0"   readonly="readonly"  /></p>		
			<p><h3>Real passagem:</h3><input type="text" name="real_passagem" id="real_passagem_" style="text-transform: uppercase;"  value="0"   readonly="readonly"  /></p>		
			<hr>
			<input type="hidden" name="acao" id="acao" value="cad" />
			<input type="hidden" name="controller" id="controller" value="<?php echo $controllernome; ?>" />
			<input type="hidden" name="pagina" id="pagina" value="1" />

			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' id='erro' ><p id='txterroform'></p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div></div>


			<p><input type="submit" class="submit small" value="Cadastrar" onClick="envia();"/>
			</p>
		</form>

<script type="text/javascript">
function convertbr2usa (campo, id){
	var nome = '#'+campo+id;
	var dadocampo = eval("$('"+nome+"').val()");
	if($.isNumeric(dadocampo)){
		return dadocampo;
	}
	dadocampo = dadocampo.replace('.','');
	dadocampo = dadocampo.replace(',','.');
	if(!$.isNumeric(dadocampo)){
		dadocampo = 0;
		eval("$('"+nome+"').val(0);");
	}else{
		eval("$('"+nome+"').val('"+dadocampo+"')");
	}
	return dadocampo;
}



function calculadiaria(campo) {

	var dadocampo = convertbr2usa(campo,'');

	var dadocampovalordiaria = convertbr2usa('valor_diaria_','');
	
	var dadocampoqtddiaria = convertbr2usa('qtd_diaria_','');
	
	var dadocampoqtdmilitar = convertbr2usa('qtd_militar_','');

	var dadocampoadicional = convertbr2usa('adicional_','');

	var calculodiaria = ((dadocampoqtdmilitar * (dadocampoqtddiaria * dadocampovalordiaria+Number(dadocampoadicional))));
	$('#pln_diaria_').val(calculodiaria);

	return false;
}

function calculadiariapassagem(campo) {

	var dadocampo = convertbr2usa(campo,'');

	var dadocampovalordiaria = convertbr2usa('valor_diaria_','');
	
	var dadocampoqtddiaria = convertbr2usa('qtd_diaria_','');
	
	var dadocampoqtdmilitar = convertbr2usa('qtd_militar_','');

	var dadocampoadicional = convertbr2usa('adicional_','');

	var dadocampovalorpassagem = convertbr2usa('valor_passagem_','');


	var calculodiaria = ((dadocampoqtdmilitar * (dadocampoqtddiaria * dadocampovalordiaria+Number(dadocampoadicional))));
	var calculopassagem = ((dadocampoqtdmilitar *  dadocampovalorpassagem));
	$('#pln_diaria_').val(calculodiaria);
	$('#pln_passagem_').val(calculopassagem);
    return false;
}

</script>		
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



//]]>
</script>

	</div>		<!-- .block_content ends -->
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>		<!-- .block ends -->



<script type="text/javascript">
$(function() {
		//var valores = ["ATM","AIS","AAA"];
	    //$( "#subdivisao_" ).autocomplete({ source: valores });    
  	    $( "#ano" ).autocomplete({ source: [ <?php echo $completaano; ?>] });    
  	    $( "#status_" ).autocomplete({ source: [ <?php echo $completastatus; ?>] });    
	    $( "#mes_" ).autocomplete({ source: [ <?php echo $completames; ?>] });    
	    $( "#tipo_" ).autocomplete({ source: [ <?php echo $completatipo; ?>] });    
	    $( "#descricao_" ).autocomplete({ source: [ <?php echo $completadescricao; ?>] });    
	    $( "#local_" ).autocomplete({ source: [ <?php echo $completalocal; ?>] });    
  	    $( "#subdivisao_" ).autocomplete({ source: [ <?php echo $completasubdivisao; ?>] });  
  	      
});
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


