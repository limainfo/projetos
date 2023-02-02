	<div class="mensagens"  style="margin:0px;padding:0px;">
<?php 

switch ($status){
	case 'OK':	echo ("<div class='message info' id='sucesso'><p>{$mensagemstatus}</p><span title='Dismiss' class='close' onclick=\"$('.mensagens').hide('slow');\"></span></div>");break;
	case 'ERRO':	echo ("<div class='message errormsg' id='erro' ><p>{$mensagemstatus}</p><span title='Dismiss' class='close' onclick=\"$('.mensagens').hide('slow');\"></span></div>");break;
	case 'WARNING':	echo ("<div class='message warning' id='warning' ><p>{$mensagemstatus}</p><span title='Dismiss' class='close' onclick=\"$('.mensagens').hide('slow');\"></span></div>");break;
}

?>
</div>
