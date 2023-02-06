<!--<script type="text/javascript" src="js/ui/js/jquery-ui-1.10.4.min.js"></script>-->
<script
  src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
  integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
  crossorigin="anonymous"></script>
<!--<script type="text/javascript" src="js/ui/js/jquery-ui-1.13.2/jquery-ui.min.js"></script>-->
<script type="text/javascript" src="js/ui/development-bundle/ui/i18n/jquery.ui.datepicker-pt-BR.js"></script>
<script src="js/jquery.maskedinput.js" type="text/javascript"></script>

<script type="text/javascript">
$.fx.speeds._default = 1000;
	$(function() {
		$( "#manipulacao" ).dialog({autoOpen: false,modal: true, widht: window.innerWidth * 7/10, show: 'highlight',hide: 'fade'
});
	});	

function edita(id) {
	$( "#manipulacao" ).dialog({
		autoOpen: false,
		title:'Alterar <?php echo $controllernome; ?>',
		position: { my: "center", at: "center", of: window },
		width: 'auto', 
    	maxWidth: 600,
    	height: 'auto',
		fluid: true,
		buttons: {},
		modal: true,
		closeText: "",
		close: function() {
			$( this ).dialog( "close" );
		}
	}); 
  var parametros = {'controller':'<?php echo $controllernomeplural; ?>','acao':'veredit', 'id':id, 'pagina': '<?php echo $pagina; ?>' };
  var dados = parametros;
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
      $("#manipulacao").html(data);
      //$("#cadastrados").html('Testando');
      $("#spinner").css({'display':'none'});
      $( "#manipulacao" ).dialog( "open" );

    },
    error: function() {},
    data: dados ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
 }

               
function cad() {
	$( "#manipulacao" ).dialog({
		autoOpen: false,
		text: false,
		title:'Cadastrar <?php echo $controllernome; ?>',
		position: { my: "center", at: "center", of: window },
		width: 'auto', 
    	maxWidth: 600,
    	height: 'auto',
		fluid: true,
		modal: true,
		closeText: "",
		close: function() {
			$( this ).dialog( "close" );
		}
	 
	}); 
  var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'vercad', 'pagina':'<?php echo $pagina; ?>'};
  var dados = parametros;
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
      $("#manipulacao").html(data);
      //$("#cadastrados").html('Testando');
      $("#spinner").css({'display':'none'});
      $( "#manipulacao" ).dialog( "open" );

    },
    error: function() {},
    data: dados ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
 }
 
 
function exclui(id, nome, id) {
		var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'exclui', 'id':id, 'pagina': '<?php echo $pagina; ?>', 'nome':nome };

		var idcorreto = id.replace("#del","");
		var linhaidnova = '#linha'+idcorreto;
		var classanterior = $(linhaidnova).attr('class');
		var styleanterior = $(linhaidnova).attr('style');
		var styletdanterior = $(linhaidnova+' td').attr('style');
		

		
		$(linhaidnova).removeClass();
		$(linhaidnova).removeAttr('style');
		$(linhaidnova +' td').removeAttr('style');
		
		
		$(linhaidnova+' td').attr('style', 'background:none repeat scroll 0 0 yellow;');
		$(linhaidnova).attr('style', 'background:none repeat scroll 0 0 yellow;');
		
		var idreal = id.replace("#del","");
		var linha = '#linha' + idreal;
		var dados = parametros;
	    $( "#manipulacao" ).dialog({
	  		autoOpen: false,
			position: { my: "center", at: "center", of: id },
			title:'Excluir <?php echo $controllernome; ?>',
			width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
			modal: true,
			buttons: {},
			closeText: "",
			close: function() {$( this ).dialog( "close" );}
	    });
		
		$( "#manipulacao" ).html('Tem certeza que deseja excluir o <?php echo $controllernome; ?> ->'+nome+' ?');
		$( "#manipulacao" ).dialog({
			resizable: false,
			widht: window.innerWidth * 2/10,
			position: { my: "center", at: "center", of: id },
			height:200,
			modal: true,
			buttons: {
				"Excluir": function() {
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
							      //$("#manipulacao").html(msg);
							      //$(nome).css({'background-color':'#800000','color':'#fff'});
							      alert(mensagem);
								}else{
							      //$("#manipulacao").html(data);
							      //$(nome).css({'background-color':'#008000','color':'#fff'});
							      $("#spinner").css({'display':'none'});
							      $(linha).remove();
							      $("#totalsomatorio").remove();
							      //$( "#manipulacao" ).dialog( "open" );
								}

								$(linhaidnova).removeClass();
								$(linhaidnova).removeAttr('style');
								$(linhaidnova +' td').removeAttr('style');
								$(linhaidnova).attr('style',styleanterior);
								$(linhaidnova).removeClass().addClass(classanterior);
								$(linhaidnova +' td').attr('style',styletdanterior);
								
								
						
						//  $("#listagem").html(data);
						  $("#spinner").css({'display':'none'});

						},
						error: function() {},
						data: dados ,
						datatype: 'json',
						contentType: 'application/x-www-form-urlencoded'
					  });
					
					$( this ).dialog( "close" );
				},
				Cancelar: function() {
					$(linhaidnova).removeClass();
					$(linhaidnova).removeAttr('style');
					$(linhaidnova +' td').removeAttr('style');
					$(linhaidnova).attr('style',styleanterior);
					$(linhaidnova).removeClass().addClass(classanterior);
					$(linhaidnova +' td').attr('style',styletdanterior);
				$( this ).dialog( "close" );
					
				}
			}
		});
      $( "#manipulacao" ).dialog( "open" );
  
  
 }
 


function ver(id, nome) {
		var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'ver', 'id':id, 'pagina': '<?php echo $pagina; ?>' };
		  var dados = parametros;
	
		$( "#manipulacao" ).dialog({
			autoOpen: false,
			title:'Visualizar dados do <?php echo $controllernome; ?>',
			resizable: true,
			modal: true,
			position: { my: "center", at: "center", of: window },
			width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	  $.ajax({
		type: 'POST',
		 processData: true,
		url: 'controller_<?php echo $controllernome; ?>.php',
		beforeSend: function(){
		  $("#spinner").css({'display':'block'});
		},
		success: function(data) {
		  $("#manipulacao").html(data);
		  $("#spinner").css({'display':'none'});
		  $("#manipulacao" ).dialog( "open" );

		},
		error: function() {},
		data: dados ,
		datatype: 'html',
		contentType: 'application/x-www-form-urlencoded'
	  });
		
  
  
 }


function buscar(nome) {
	  var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'busca', 'pagina': '<?php echo $pagina; ?>', 'busca':nome };
	  var dados = parametros;
	  $.ajax({
		type: 'POST',
		processData: true,
		url: 'controller_<?php echo $controllernome; ?>.php',
		beforeSend: function(){
		  $("#spinner").css({'display':'block'});
		},
		success: function(data) {
			$("#spinner").hide();
			$("#listagem").html(data);
		},
		error: function() {},
		data: dados ,
		datatype: 'html',
		contentType: 'application/x-www-form-urlencoded'
	  });
 }
 
 function designa(id) {
	$( "#manipulacao" ).dialog({
		autoOpen: false,
		title:'RN003 -> Gerente-> Designar estagiário',
		position: { my: "center", at: "center", of: window },
		width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
		buttons: {},
		modal: true,
		closeText: "",
		close: function() {$( this ).dialog( "close" );
		}
	}); 
  var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'vercaddesigna', 'id':id, 'pagina':'<?php echo $pagina; ?>'};
  var dados = parametros;
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
      $("#manipulacao").html(data);
      //$("#cadastrados").html('Testando');
      $("#spinner").css({'display':'none'});
      $( "#manipulacao" ).dialog( "open" );

    },
    error: function() {},
    data: dados ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
 }

	

function editsuspenso(campo, id, controller, tipo, completafuncao) {
	  if(completafuncao === undefined) {
		  completafuncao = '';
	   }
		   
	var nome = '#'+campo+'p'+id;
	var nomecampo = '#'+campo+id;
	var obj =  '#'+campo+id;
	var dadocampo = eval("$('"+nome+"').text()");
	//var dados =  {campo: campo, id: id, valor: dadocampo , controller: controller, acao: acao };
	//$(nome).css({'background-color':'#000','color':'#fff'});
    $( "#manipulacao" ).dialog({
  		autoOpen: false,
		position: { my: "center", at: "center", of: nome },
		title:'Status <?php echo $controllernome; ?>',
		width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
		modal: true,
		buttons: {},
		closeText: "",		
		close: function() {$( this ).dialog( "close" );}
    });
    //var caixa = '<textarea cols="30" rows="2"  class="'+campo+' ui-autocomplete-input"   id="'+campo+id+'" name="'+campo+id+'" size="15" style="font-size:10px;" autocomplete="off">'+dadocampo+'</textarea><input type="button" name="GRAVAR" value="GRAVAR"  onclick="var x=modifica(\''+id+'\',\''+campo+'\', \''+controller+'\', \'ajaxmodifica\');if(x){$(this).remove();}"  >';
    var caixa = '';
	if(typeof tipo !="undefined"){
	    if(tipo =='dttime'){
	        caixa = '<input type="text"  class="styled" onclick="$(this).mask(\'99/99/9999 99:99\');"   id="'+campo+id+'" name="'+campo+id+'" size="15" style="font-size:10px;" value="'+dadocampo+'"><input type="button" name="GRAVAR" value="GRAVAR"  onclick="var x=modifica(\''+id+'\',\''+campo+'\', \''+controller+'\', \'ajaxmodifica\',\''+tipo+'\',\''+completafuncao+'\');'+decodeURIComponent(completafuncao)+'if(x){$(\''+campo+id+'\').remove();}"  >';
	     }
	    if(tipo =='dt'){
	        caixa = '<input type="text"  class="styled" onclick="$(this).mask(\'99/99/9999\');"   id="'+campo+id+'" name="'+campo+id+'" size="15" style="font-size:10px;" value="'+dadocampo+'"><input type="button" name="GRAVAR" value="GRAVAR"  onclick="var x=modifica(\''+id+'\',\''+campo+'\', \''+controller+'\', \'ajaxmodifica\',\''+tipo+'\',\''+completafuncao+'\');'+decodeURIComponent(completafuncao)+'if(x){$(\''+campo+id+'\').remove();}"  >';
	     }else{
		    caixa = '<input type="text"  class="'+campo+' ui-autocomplete-input"   id="'+campo+id+'" name="'+campo+id+'" size="15" style="font-size:10px;" autocomplete="off" value="'+dadocampo+'"><input type="button" name="GRAVAR" value="GRAVAR"  onclick="var x=modifica(\''+id+'\',\''+campo+'\', \''+controller+'\', \'ajaxmodifica\',\''+tipo+'\',\''+completafuncao+'\');'+decodeURIComponent(completafuncao)+'if(x){$(\''+campo+id+'\').remove();}"  >';
		}		
	}else{
            caixa = '<input type="text"  class="'+campo+' ui-autocomplete-input"   id="'+campo+id+'" name="'+campo+id+'" size="15" style="font-size:10px;" autocomplete="off" value="'+dadocampo+'"><input type="button" name="GRAVAR" value="GRAVAR"  onclick="var x=modifica(\''+id+'\',\''+campo+'\', \''+controller+'\', \'ajaxmodifica\',\''+tipo+'\',\''+completafuncao+'\');'+decodeURIComponent(completafuncao)+'if(x){$(\''+campo+id+'\').remove();}"  >';
	}
    
    if($.isEmptyObject($(obj))){
       // alert('Existe');
        return false;
       }
    $(nome).html(caixa);
    var testecampo = eval(campo+'src');
    if( testecampo != ""){
    	$(nomecampo).autocomplete({source: testecampo});
    }
    $(nome).removeAttr('onclick');
    
	/*
	  $.ajax({
			type: 'POST',
            context: this,			
                    processData: true,
		    url: 'controller_'+controller+'.php',
		    beforeSend: function(){
		      $("#spinner").css({'display':'block'});
			},
		    success: function(data) {
		  	  registros = data;
			  status = registros['status'];
			  mensagem = registros['mensagemstatus'];
			  if(status=='ERRO'){
			      //$("#manipulacao").html(msg);
			      $(nome).css({'background-color':'#800000','color':'#fff'});
				}else{
			      //$("#manipulacao").html(data);
			      $(nome).css({'background-color':'#008000','color':'#fff'});
			      $("#spinner").css({'display':'none'});
			      $("#totalsomatorio").remove();
			      //$( "#manipulacao" ).dialog( "open" );
				}
		    

},
		    error: function(msg) {
			      $("#manipulacao").html("<b>Problemas com a conexão!</b>");
			      $(nome).css({'background-color':'#008000','color':'#fff'});
			      $("#spinner").css({'display':'none'});
			      $( "#manipulacao" ).dialog( "open" );
},
		    data: dados ,
		    datatype: 'json',
		    contentType: 'application/x-www-form-urlencoded'
		  });
	
    */
    return false;
 }
 
function vazio(id, campo, controller, acao) {
	return false;
}
 
function modifica(id, campo, controller, acao, tipo, completafuncao) {

	//alert(id+' '+campo+' '+controller+' '+acao+' '+tipo+' '+completafuncao);

	if(completafuncao === undefined) {
		  completafuncao = '';
	   }
	  if(tipo === undefined) {
		  tipo = '';
	   }
	   
$( "#manipulacao" ).dialog({
  		autoOpen: false,
		position: { my: "center", at: "center", of: nome },
		title:'Status <?php echo $controllernome; ?>',
		width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
		modal: true,
		buttons: {},
		closeText: "",		
		close: function() {$( this ).dialog( "close" );}
    });
	var nome = '#'+campo+id;
	var nomep = '#'+campo+'p'+id;
	var obj = $(nomep);
	var objo = $(nome);
	var dadocampo = eval("$('"+nome+"').val()");
	if($.isEmptyObject(objo) && !$.isEmptyObject(obj)){
		var dadocampo = eval("$('"+nomep+"').text()");
	}


	var nome = '#'+campo+id;
	var nomep = '#'+campo+'p'+id;

	var obj = $(nome);
	var objp = $(nomep);

	if(obj.val() === undefined) {
			 var dadocampo = objp.text();
	}else{
			var dadocampo = obj.val();
	}

	
	var dados =  {campo: campo, id: id, valor: dadocampo , controller: controller, acao: acao };
	$(nome).css({'background-color':'red','color':'#fff'});
	
	  $.ajax({
			type: 'POST',
            context: this,			
            processData: true,
		    url: 'controller_'+controller+'.php',
		    beforeSend: function(){
		      $("#spinner").css({'display':'block'});
			},
		    success: function(data) {
		  	  registros = data;
			  status = registros['status'];
			  mensagem = registros['mensagemstatus'];
			  if(status=='ERRO'){
			      $(nome).css({'background-color':'red','color':'#fff'});
			      $("#spinner").css({'display':'none'});
				}else{
				 $(nome).css({'background-color':'green','color':'#fff'});
			      if(!$.isEmptyObject($(obj))){

			    	  $(nomep).attr('onclick','editsuspenso(\''+campo+'\',\''+id+'\' ,\''+controller+'\' ,\''+tipo+'\' ,\''+(completafuncao)+'\'  );');
						
				      $(nomep).html(dadocampo);
				      $(nomep).css({'background-color':'#008000','color':'#fff'});
				      $("#spinner").css({'display':'none'});
				      return true;
				}
			  			      
			      $("#spinner").css({'display':'none'});
			      if(!$.isEmptyObject($("#totalsomatorio"))){
			      		$("#totalsomatorio").remove();
			      }
				}
		    

			},
		    error: function(msg) {
			      $("#spinner").css({'display':'none'});
			},
		    data: dados ,
		    datatype: 'json',
		    contentType: 'application/x-www-form-urlencoded'
		  });
    
	  return false;
}

$(function() {
	 $( ".datepicker" ).datepicker( $.datepicker.regional[ "pt-BR" ] ); 
	 //$( ".datepicker" ).datepicker("option","dateFormat","yy-mm-dd"); 
	 $( ".datepicker" ).datepicker({
		 showButtonPanel: true,
		 buttonImage: "images/calendario.png",
		 buttonImageOnly: true
		}); 
		<?php echo $datas; ?>
		 $( ".datepicker" ).datepicker();
});


function verfiltro(model) {
	$( "#manipulacao" ).dialog({
		autoOpen: false,
		title:'Filtros ativos para  <?php echo $controllernome; ?>',
		position: { my: "center", at: "center", of: window },
		width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
		buttons: {},
		modal: true,
		closeText: "",		
		close: function() {$( this ).dialog( "close" );
		}
	}); 
  var parametros = {'controller':'<?php echo $controllernome; ?>','acao':'verfiltro', 'pagina':'<?php echo $pagina; ?>'};
  var dados = parametros;
  $.ajax({
	type: 'POST',
	 processData: true,
    url: 'controller_<?php echo $controllernome; ?>.php',
    beforeSend: function(){
      $("#spinner").css({'display':'block'});
	},
    success: function(data) {
      $("#manipulacao").html(data);
      $("#spinner").css({'display':'none'});
      $('#manipulacao').dialog({autoOpen: false});      
      $("#manipulacao").dialog( "close" );
      $("#manipulacao").dialog( "open" );
    },
    error: function(data) {
        $("#manipulacao").html(data);
        $("#spinner").css({'display':'none'});
        $("#manipulacao").dialog({autoOpen: false});      
        $("#manipulacao").dialog( "close" );
        $("#manipulacao").dialog( "open" );
},
    data: dados ,
    datatype: 'html',
    contentType: 'application/x-www-form-urlencoded'
  });
 }


 
$( "#mensagens" ).show();


	/* jQuery */
	jQuery.ready(function() {
		$( "#manipulacao" ).dialog({
			autoOpen: false,
			title:'Filtros ativos para  <?php echo $controllernome; ?>',
			position: { my: "center", at: "center", of: window },
			width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
			buttons: {},
			modal: true,
			closeText: "",			
			close: function() {$( this ).dialog( "close" );
			}
		}); 
	jQuery('a.minibutton').bind({
		mousedown: function() {
		  jQuery(this).addClass('mousedown');
		},
		blur: function() {
		  jQuery(this).removeClass('mousedown');
		},
		mouseup: function() {
		  jQuery(this).removeClass('mousedown');
		}
	  });
	});

	$('.show').click(function() {
      $('#hide').show('slow', function() {
        // Animation complete.
      });
    });
	$('.hide').click(function() {
      $('#hide').hide('slow', function() {
        // Animation complete.
      });
    });




	function modificastatus(id, campo, controller, acao, tipo) {
		var nome = '#'+campo+id;
		var linha = '#td'+id;
		var objo = $(nome);
		var dadocampo = eval("$('"+nome+"').val()");
		if($.isEmptyObject(objo) && !$.isEmptyObject(obj)){
			var dadocampo = eval("$('"+nomep+"').text()");
		}
		//alert(dadocampo);
		//$(nome).css({'background-color':'red','color':'#fff'});

		switch(dadocampo){
			case 'CONCLUÍDA':
				$(linha).removeClass().addClass("concluida");
				break;
			case 'CONCLUÍDA D':
				$(linha).removeClass().addClass("concluida");//cyan
				break;
			case 'ATRASADA':
				$(linha).removeClass().addClass("atrasada");
				break;
			case 'INICIADA DP':
				$(linha).removeClass().addClass("iniciada");//green
				break;
			case 'INICIADA D':
				$(linha).removeClass().addClass("iniciada");//green
				break;
			case 'CANCELADA':
				$(linha).removeClass().addClass("cancelada");//red
				break;
			case 'EXCLUÍDA':
				$(linha).removeClass().addClass("cancelada");//red
				break;
			case 'NÃO CONCLUÍDA':
				$(linha).removeClass().addClass("naoiniciada");
				break;
			case 'NÃO INICIADA':
				$(linha).removeClass().addClass("naoiniciada");
				break;
		}
		
		
		  return false;
	}



	function convertbr2usa (campo, id){
		var nome = '#'+campo+id;
		var dadocampo = eval("$('"+nome+"').val()");
		if($.isNumeric(dadocampo)){
			return Math.round(dadocampo * 100) / 100;
		}
		dadocampo = dadocampo.replace('.','');
		dadocampo = dadocampo.replace(',','.');
		if(!$.isNumeric(dadocampo)){
			dadocampo = 0;
			eval("$('"+nome+"').val(0);");
		}else{
			eval("$('"+nome+"').val('"+dadocampo+"')");
		}
		return Math.round(dadocampo * 100) / 100;
	}

	function convertmoeda (valor){
		var dadocampo = valor;
		if($.isNumeric(dadocampo)){
			return Math.round(dadocampo * 100) / 100;
		}
		dadocampo = dadocampo.replace('.','');
		dadocampo = dadocampo.replace(',','.');
		if(!$.isNumeric(dadocampo)){
			dadocampo = 0;
		}
		return Math.round(dadocampo * 100) / 100;
	}

	

	function calculadiaria(id, campo, controller, acao) {
		var nome = '#'+campo+id;
		var dadocampo = eval("$('"+nome+"').val()");

		var nomevalordiaria = '#valor_diaria'+id;
		var dadocampovalordiaria = eval("$('"+nomevalordiaria+"').val()");
		if(!$.isNumeric(dadocampovalordiaria)){
			dadocampovalordiaria = 0;
			eval("$('"+nomevalordiaria+"').val(0);");
		}

		var nomeqtddiaria = '#qtd_diaria'+id;
		var dadocampoqtddiaria = eval("$('"+nomeqtddiaria+"').val()");
		if(!$.isNumeric(dadocampoqtddiaria)){
			dadocampoqtddiaria = 0;
			eval("$('"+nomeqtddiaria+"').val(0);");
		}
		
		var nomeqtdmilitar = '#qtd_militar'+id;
		var dadocampoqtdmilitar = eval("$('"+nomeqtdmilitar+"').val()");
		if(!$.isNumeric(dadocampoqtdmilitar)){
			dadocampoqtdmilitar = 0;
			eval("$('"+nomeqtdmilitar+"').val(0);");
		}
		
		var nomeadicional = '#adicional'+id;
		var dadocampoadicional = eval("$('"+nomeadicional+"').val()");
		if(!$.isNumeric(dadocampoadicional)){
			dadocampoadicional = 0;
			eval("$('"+nomeadicional+"').val(0);");
		}

		var calculodiaria = (Number(dadocampoadicional) + (dadocampoqtdmilitar * dadocampoqtddiaria * dadocampovalordiaria));
		$('#pln_diaria'+id).val(convertmoeda(calculodiaria));
		$('#pln_diaria'+id).change();
		var dados =  {campo: campo, id: id, valor: dadocampo , controller: controller, acao: acao };
		$(nome).css({'background-color':'#000','color':'#fff'});
	    $( "#manipulacao" ).dialog({
	  		autoOpen: false,
			position: { my: "center", at: "center", of: nome },
			title:'Status <?php echo $controllernome; ?>',
			width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
			modal: true,
			buttons: {},
			closeText: "",			
			close: function() {$( this ).dialog( "close" );}
	    });
		
		  $.ajax({
				type: 'POST',
	            context: this,			
	                    processData: true,
			    url: 'controller_'+controller+'.php',
			    beforeSend: function(){
			      $("#spinner").css({'display':'block'});
				},
			    success: function(data) {
			  	  registros = data;
				  status = registros['status'];
				  mensagem = registros['mensagemstatus'];
				  if(status=='ERRO'){
				      $(nome).css({'background-color':'#800000','color':'#fff'});
					}else{
				      $(nome).css({'background-color':'#008000','color':'#fff'});
				      $("#spinner").css({'display':'none'});
					}
			    

	},
			    error: function(msg) {
				      $("#manipulacao").html("<b>Problemas com a conexão!</b>");
				      $(nome).css({'background-color':'#008000','color':'#fff'});
				      $("#spinner").css({'display':'none'});
				      $( "#manipulacao" ).dialog( "open" );
	},
			    data: dados ,
			    datatype: 'json',
			    contentType: 'application/x-www-form-urlencoded'
			  });
		
	    
	    return false;
	}

	function calculadiariapassagem(id, campo, controller, acao) {

		//alert(id+' '+campo+' '+controller+' '+acao+' ');
		var nome = '#'+campo+id;
		var nomep = '#'+campo+'p'+id;

		var obj = $(nome);
		var objp = $(nomep);

		if(obj.val() === undefined) {
				 var dadocampo = eval("$('"+nomep+"').text()");
			     dadocampo = Number(dadocampo)
				 if(!$.isNumeric(dadocampo)){
						dadocampo = 0;
						eval("$('"+nomep+"').text(0);");
					}
			}else{
				var dadocampo = eval("$('"+nome+"').val()");
			     dadocampo = Number(dadocampo)
				 if(!$.isNumeric(dadocampo)){
						dadocampo = 0;
						eval("$('"+nome+"').val(0);");
				}
			}
		
		
		var nomevalordiaria = '#valor_diaria'+id;
		var nomevalordiariap = '#valor_diariap'+id;
		obj = $(nomevalordiaria);
		objp = $(nomevalordiariap);
		if(obj.val() === undefined) {
				 var dadocampovalordiaria = eval("$('"+nomevalordiariap+"').text()");
				 dadocampovalordiaria = Number(dadocampovalordiaria)
				 if(!$.isNumeric(dadocampovalordiaria)){
					 dadocampovalordiaria = 0;
					 eval("$('"+nomevalordiariap+"').text(0);");
					}
			}else{
				var dadocampovalordiaria = eval("$('"+nomevalordiaria+"').val()");
				dadocampovalordiaria = Number(dadocampovalordiaria)
				 if(!$.isNumeric(dadocampovalordiaria)){
					 dadocampovalordiaria = 0;
					 eval("$('"+nomevalordiaria+"').val(0);");
				}
			}

		var nomevalorpassagem = '#valor_passagem'+id;
		var nomevalorpassagemp = '#valor_passagemp'+id;
		obj = $(nomevalorpassagem);
		objp = $(nomevalorpassagemp);
		if(obj.val() === undefined) {
				 var dadocampovalorpassagem = eval("$('"+nomevalorpassagemp+"').text()");
				 dadocampovalorpassagem = Number(dadocampovalorpassagem)
				 if(!$.isNumeric(dadocampovalorpassagem)){
					 dadocampovalorpassagem = 0;
					 eval("$('"+nomevalorpassagemp+"').text(0);");
					}
			}else{
				var dadocampovalorpassagem = eval("$('"+nomevalorpassagem+"').val()");
				dadocampovalorpassagem = Number(dadocampovalorpassagem)
				 if(!$.isNumeric(dadocampovalorpassagem)){
					 dadocampovalorpassagem = 0;
					 eval("$('"+nomevalorpassagem+"').val(0);");
				}
			}

		var nomeqtddiaria = '#qtd_diaria'+id;
		var nomeqtddiariap = '#qtd_diariap'+id;
		obj = $(nomeqtddiaria);
		objp = $(nomeqtddiariap);
		if(obj.val() === undefined) {
				 var dadocampoqtddiaria = eval("$('"+nomeqtddiariap+"').text()");
				 dadocampoqtddiaria = Number(dadocampoqtddiaria)
				 if(!$.isNumeric(dadocampoqtddiaria)){
					 dadocampoqtddiaria = 0;
					 eval("$('"+nomeqtddiariap+"').text(0);");
					}
			}else{
				var dadocampoqtddiaria = eval("$('"+nomeqtddiaria+"').val()");
				dadocampoqtddiaria = Number(dadocampoqtddiaria)
				 if(!$.isNumeric(dadocampoqtddiaria)){
					 dadocampoqtddiaria = 0;
					 eval("$('"+nomeqtddiaria+"').val(0);");
				}
			}


		var nomeqtdmilitar = '#qtd_militar'+id;
		var nomeqtdmilitarp = '#qtd_militarp'+id;
		obj = $(nomeqtdmilitar);
		objp = $(nomeqtdmilitarp);
		if(obj.val() === undefined) {
				 var dadocampoqtdmilitar = eval("$('"+nomeqtdmilitarp+"').text()");
				 dadocampoqtdmilitar = Number(dadocampoqtdmilitar)
				 if(!$.isNumeric(dadocampoqtdmilitar)){
					 dadocampoqtdmilitar = 0;
					 eval("$('"+nomeqtdmilitarp+"').text(0);");
					}
			}else{
				var dadocampoqtdmilitar = eval("$('"+nomeqtdmilitar+"').val()");
				dadocampoqtdmilitar = Number(dadocampoqtdmilitar)
				 if(!$.isNumeric(dadocampoqtdmilitar)){
					 dadocampoqtdmilitar = 0;
					 eval("$('"+nomeqtdmilitar+"').val(0);");
				}
			}


		var nomeadicional = '#adicional'+id;
		var nomeadicionalp = '#adicionalp'+id;
		obj = $(nomeadicional);
		objp = $(nomeadicionalp);
		if(obj.val() === undefined) {
				 var dadocampoadicional = eval("$('"+nomeadicionalp+"').text()");
				 dadocampoadicional = Number(dadocampoadicional)
				 if(!$.isNumeric(dadocampoadicional)){
					 dadocampoadicional = 0;
					 eval("$('"+nomeadicionalp+"').text(0);");
					}
			}else{
				var dadocampoadicional = eval("$('"+nomeadicional+"').val()");
				dadocampoadicional = Number(dadocampoadicional)
				 if(!$.isNumeric(dadocampoadicional)){
					 dadocampoadicional = 0;
					 eval("$('"+nomeadicional+"').val(0);");
				}
			}

		  
		var calculodiaria = convertmoeda(Number(dadocampoadicional) + (dadocampoqtdmilitar * dadocampoqtddiaria * dadocampovalordiaria));
		var calculopassagem = convertmoeda((dadocampoqtdmilitar *  dadocampovalorpassagem));

		//---------------------------------------------------------
		//alert(convertmoeda(calculodiaria)+' '+calculopassagem+' '+'#pln_diariap'+id);
		
		$('#pln_diariap'+id).text(convertmoeda(calculodiaria));
		$('#pln_diariap'+id).change();
		$('#pln_passagemp'+id).text(convertmoeda(calculopassagem));
		$('#pln_passagemp'+id).change();
		$('#totalsomatorio').remove();
		
		var campos = [campo, "valor_diaria", "valor_passagem", "qtd_diaria", "qtd_militar", "adicional", "pln_diaria", "pln_passagem"];
		var valores= [dadocampo, dadocampovalordiaria, dadocampovalorpassagem, dadocampoqtddiaria, dadocampoqtdmilitar, dadocampoadicional, calculodiaria, calculopassagem];

	    $( "#manipulacao" ).dialog({
	  		autoOpen: false,
			position: { my: "center", at: "center", of: nome },
			title:'Status <?php echo $controllernome; ?>',
			width: 'auto', 
			maxWidth: 600,
			height: 'auto',
			fluid: true,
			modal: true,
			buttons: {},
			closeText: "",			
			close: function() {$( this ).dialog( "close" );}
	    });
		for( index=0;index<campos.length;index++){
			
			//var dados =  {campo: campo, id: id, valor: dadocampo , controller: controller, acao: acao };
			campo = campos[index];
			//alert(campo);
			dadocampo = valores[index];
			var dados =  {campo: campo, id: id, valor: dadocampo , controller: controller, acao: acao };
			nome = '#'+campo+id;
			nomep = '#'+campo+'p'+id;
			$(nome).css({'background-color':'#000','color':'#fff'});
	    	$(nomep).attr('onclick','editsuspenso(\''+campo+'\',\''+id+'\' ,\''+controller+'\' ,\'num\' ,\'\'  );');
	    	//alert(nomep);
		    $(nomep).html(dadocampo);
		    $(nomep).change();
		    $(nomep).css({'background-color':'#808000','color':'#fff'});
			
		  $.ajax({
				type: 'POST',
	            context: this,			
	                    processData: true,
			    url: 'controller_'+controller+'.php',
			    beforeSend: function(){
			      $("#spinner").css({'display':'block'});
				},
			    success: function(data) {
			  	  registros = data;
				  status = registros['status'];
				  mensagem = registros['mensagemstatus'];
				  if(status=='ERRO'){
				      $(nome).css({'background-color':'#800000','color':'#fff'});
					}else{
				      $(nome).css({'background-color':'#008000','color':'#fff'});
				      $("#spinner").css({'display':'none'});
					}
			    

	},
			    error: function(msg) {
				      $("#manipulacao").html("<b>Problemas com a conexão!</b>");
				      $(nome).css({'background-color':'#008000','color':'#fff'});
				      $("#spinner").css({'display':'none'});
				      $( "#manipulacao" ).dialog( "open" );
	},
			    data: dados ,
			    datatype: 'json',
			    contentType: 'application/x-www-form-urlencoded'
			  });
		}
	    
	    return false;
	}
		
	
</script>
