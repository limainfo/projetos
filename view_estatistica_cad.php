
<link rel="stylesheet" href="js/jwysiwyg-master/jquery.wysiwyg.css" type="text/css"/>
<link rel="stylesheet" href="js/jwysiwyg-master/plugins/fileManager/wysiwyg.fileManager.css" type="text/css"/> 
<script type="text/javascript" src="js/jwysiwyg-master/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="js/jwysiwyg-master/controls/wysiwyg.image.js"></script>
<script type="text/javascript" src="js/jwysiwyg-master/controls/wysiwyg.link.js"></script>
<script type="text/javascript" src="js/jwysiwyg-master/controls/wysiwyg.table.js"></script>
<script type="text/javascript" src="js/jwysiwyg-master/plugins/wysiwyg.fileManager.js"></script> 

<script type="text/javascript">
(function($) {
	$(document).ready(function() {
		$('#item').wysiwyg({
			height: 500,
			initialContent: "<p>Digite o texto da questão</p>",
			controls: {
				"fileManager": { 
					visible: true,
					groupIndex: 12,
					tooltip: "File Manager",
					exec: function () {
						$.wysiwyg.fileManager.init(function (file) {
							file ? alert(file) : alert("No file selected.");
						});
					}
				}
			}
		});
		$.wysiwyg.fileManager.setAjaxHandler("./uploadimagens/file-manager.php");

		$('#resposta_comentada').wysiwyg({
			height: 500,
			initialContent: "<p>Digite o comentário</p>",
			controls: {
				"fileManager": { 
					visible: true,
					groupIndex: 12,
					tooltip: "File Manager",
					exec: function () {
						$.wysiwyg.fileManager.init(function (file) {
							file ? alert(file) : alert("No file selected.");
						});
					}
				}
			}
		});
		$.wysiwyg.fileManager.setAjaxHandler("./uploadimagens/file-manager.php");
		
	});
})(jQuery);
</script>

<div class="block">
	<div class="block_content">
		<form accept-charset="utf-8" action="controller_<?php echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="<?php echo $controllernome; ?>Form" onSubmit="return false;">
			<h3>Matéria:</h3>
			<select name="materia"  id="materia"   class="multiple styled" onchange="mudaselect('<?php echo $controllernome; ?>','materia','disciplina');">
			<?php 
				echo '<option value="" selected="selected"  ></option>';
			foreach($selectmateria as $indice=>$valor){
				echo '<option value="'.$valor['materia'].'"  >'.$valor['materia'].'</option>';
			}
			?>
			</select>
			<h3>Disciplina:</h3>
			<select name="disciplina" id="disciplina"   class="multiple styled">
			<option value=""  ></option>
			</select>		
			<h3>Concurso:</h3>
			<select name="concurso"  id="concurso"   class="multiple styled">
			<?php 
				echo '<option value="" selected="selected"  ></option>';
			foreach($selectconcurso as $indice=>$valor){
				echo '<option value="'.$valor['concurso'].'"  >'.$valor['label'].'</option>';
			}
			?>
						</select>
			<h3>Dificuldade:</h3>
			<select name="dificuldade"  id="dificuldade"   class="multiple styled">
			<?php 
			echo '<option value="" selected="selected"  ></option>';
			foreach($selectdificuldade as $indice=>$valor){
				echo '<option value="'.$indice.'"  >'.$valor.'</option>';
			}
			?>
			</select>
			<h3>Item:</h3>
			<textarea name="item" id="item" style="text-transform: uppercase;"  /></textarea>
			<h3>Resposta:</h3>
			<select name="resposta" id="resposta"   class="multiple styled">
			<option value="A" >A</option>
			<option value="B" >B</option>
			<option value="C" >C</option>
			<option value="D" >D</option>
			<option value="E" >E</option>
			</select>
			<h3>Comentário:</h3>
			<textarea name="resposta_comentada" id="resposta_comentada" style="text-transform: uppercase;"  /></textarea>		
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
		$('#item-wysiwyg-iframe').attr('height','380px');
		$('#resposta_comentada-wysiwyg-iframe').attr('height','380px');
		$('.wysiwyg').attr('height','400px');
		//$( "#materia" ).autocomplete({ source: [ <?php echo $completamateria; ?>] });    
		
	   //	 $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] ); 
	   	 
	    //$( "#qtd_militar_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });	    $( "#qtd_diaria_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });
		 //$( ".datepicker" ).datepicker({ showButtonPanel: true,	 buttonImage: "images/calendario.png",	 buttonImageOnly: true		}); 
   		// $( ".datepicker" ).datepicker();
		 
});    
</script>


