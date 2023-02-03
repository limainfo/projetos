<div class="block">
	<div class="block_content">
        <form accept-charset="utf-8" action="controller_<?php echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="<?php echo $controllernome; ?>Form"  id="upload-form" class="upload-form" method="post">
			<h3>Informe o arquivo:</h3>
			<p>
			<input class="submit small" type="file" id="upl-file" name="upl_file">
			<span id="chk-error"></span>
			</p>			
			<p>
			</p>
			<h3>Origem do arquivo:</h3>
			<p>
			<select name="origem" id="origem" class="multiple styled" >
			<option value="TATIC"  >TATIC (txt)</option>
			<option value="SAGITARIO"  >SAGITARIO (TXT)</option>

			</select>
			</p>
			<p><input type="submit" class="submit small" value="Upload"  id="upload-file" /></p>
			<input type="hidden" name="acao" id="acao" value="cad" />
			<input type="hidden" name="controller" id="controller" value="<?php echo $controllernome; ?>" />
			<input type="hidden" name="pagina" id="pagina" value="1" />

			<div class="mensagensform"  style="margin:0px;padding:0px;diplay:none;">
			<div class='message errormsg' id='erro' ><p id='txterroform'></p><span title='Dismiss' class='close' onclick="$('.mensagensform').hide('slow');"></span></div></div>
        </form>
        <hr>
        <div class="row align-items-center">
          <div class="col">
            <div class="progress">
              <div id="file-progress-bar" class="progress-bar"></div>
           </div>
         </div>
        </div>
        <div class="row align-items-center">  
          <div class="col">
            <div id="uploaded_file"></div>
        </div>
      </div>
 
    </div>
</div>
 
    <script type="text/javascript">
        jQuery(document).on('submit', '#<?php echo $controllernome; ?>Form', function(e){
            jQuery("#chk-error").html('');
            e.preventDefault();
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();         
                    xhr.upload.addEventListener("progress", function(element) {
                        if (element.lengthComputable) {
                            var percentComplete = ((element.loaded / element.total) * 100);
                            $("#file-progress-bar").width(percentComplete + '%');
                            $("#file-progress-bar").html(percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                type: 'POST',
                url: 'upload.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                dataType:'json',
 
                beforeSend: function(){
                    $("#file-progress-bar").width('0%');
                },
 
                success: function(json){
                    if(json == 'success'){
                        $('#upload-form')[0].reset();
                        $('#uploaded_file').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                    }else if(json == 'failed'){
                        $('#uploaded_file').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                  console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
      
        // Check File type validation
        $("#upl-file").change(function(){
            var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.ms-office', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            var file = this.files[0];
            var fileType = file.type;
            if(!allowedTypes.includes(fileType)) {
                jQuery("#chk-error").html('<small class="text-danger">Favor escolher um formato de arquivo válido (TXT)</small>');
                $("#upl-file").val('');
                return false;
            } else {
              jQuery("#chk-error").html('');
            }
        });
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
	    
		//$( "#materia" ).autocomplete({ source: [ <?php //echo $completamateria; ?>] });    
		
	   //	 $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] ); 
	   	 
	    //$( "#qtd_militar_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });	    $( "#qtd_diaria_" ).spinner({ icons: { down: "custom-down-icon", up: "custom-up-icon" } });
		 //$( ".datepicker" ).datepicker({ showButtonPanel: true,	 buttonImage: "images/calendario.png",	 buttonImageOnly: true		}); 
   		// $( ".datepicker" ).datepicker();
		 
});    
</script>


