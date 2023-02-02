<?php

if(empty($_GET['d'])){
	$_GET['d']='';
	
}

?>
<div id='listagem'>
	<div class="block">
			<div class="block_head">
				<div class="bheadl"></div>
					<div class="bheadr"></div>
						<h2><b><u><?php echo $nometitulo; ?></u></b>: &nbsp;<?php echo $total; ?> &nbsp;&nbsp;&nbsp;
						 <form accept-charset="utf-8" action="controller_<?php  echo $controllernome; ?>.php" method="get"  enctype="multipart/form-data" id="registros" class="vazio">
						 <input type="hidden" name="d" value="<?php  echo $_GET['d']; ?>">
						 <input type="hidden" name="acao" value="list">
						 <input type="hidden" name="controller" value="<?php  echo $controllernome; ?>">
						 <input type="hidden" name="i" value="<?php  echo $controllernome; ?>">
						 <label>registros por página:</label><select  class="registrosporpagina" name="registrosporpagina" id="registrosporpagina" onchange="submit(this);" >
							<?php
									for($i=10;$i<=100;$i=$i+10){
										echo  "<option value=\"$i\">$i</option>";
								}
										echo  "<option value=\"1000\">1000</option>";
										echo  "<option value=\"$maximoregistros\" selected=selected>$maximoregistros</option>";
								?>
							</select>
						</form>						
						Pág.: <?php echo $pagina.'/'.$paginas; ?>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
						if(strlen($filtro)>3){
							// echo '<font color="red">FILTRO ATIVADO</font>';
							$filtroativado = 'search_database_ativado'; 
						}else{
							$filtroativado = 'search_database';
						}
						 ?>
						 <?php 
						 if(!empty($vetorclassificacao)){
						 	foreach($vetorclassificacao as $campo=>$ordem){
						 		echo '<font color="black" size="1" face="fonts/Quantico/Quantico-Regular.ttf">'.$campo.'<font color="red" size="1" face="fonts/Quantico/Quantico-Regular.ttf">['.$ordem.']</font></font>';
						 	}
						 }
						 	
						 ?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						 </h2>
						<div align="right" style="margin-top: 10px;">
						<a href="javascript:verfiltro('planejado');" title="Criar filtro"><img src="images/ico/32x32/<?php echo $filtroativado; ?>.png" alt="Pdf"></a>
						</div>  
					</div>		<!-- .block_head ends -->
					<div class="block_content">
					<form accept-charset="utf-8" action="" method="post" enctype="multipart/form-data" id="facilidade" onsubmit="return false;">
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
							<thead>
								<tr>
								<th>&nbsp;</th>
								<?php
								foreach($campos as $titulo=>$campo){
									$ascendente = '';
									$descendente = '';
									$oascendente = 'ASC';
									$odescendente = 'DESC';
									if(!empty($vetorclassificacao)){
										switch ($vetorclassificacao[$campo]){
											case 'ASC': $descendente = '';
														$ascendente = '_ativo';
														$oascendente = '';
														$odescendente = 'DESC';
														break; 
											case 'DESC': $descendente = '_ativo';
														$ascendente = '';
														$oascendente = 'ASC';
														$odescendente = '';
														break;
														
										}
									}

									echo '<th><a href="controller_'.$controllernome.'.php?d='.$_GET['d'].'&i='.$controllernome.'&controller='.$controllernome.'&campo='.$campo.'&ordem='.$oascendente.'&acao=list" style="vertical-align:top;margin-top:0px;"  id="'.$campo.'_asc"><img src="images/sorta'.$ascendente.'.gif"></a><a href="controller_'.$controllernome.'.php?d='.$_GET['d'].'&i='.$controllernome.'&controller='.$controllernome.'&campo='.$campo.'&ordem='.$odescendente.'&acao=list" style="vertical-align:down;margin-left:-8px;top:12px;position:relative;"  id="'.$campo.'_desc"><img src="images/sortd'.$descendente.'.gif"></a>&nbsp;&nbsp;'.$titulo.'</th>';
								}
								?>
									<th colspan="3">
											<table width="60%" cellpadding="0" cellspacing="0" border="0" align="right">
											<tr>
												<td class="acoes" width="33%"><a href="javascript:cad();" title="Novo Registro"><img src="images/novo.png" alt="Cadastrar"></a></td>
												<td class="acoes" width="33%"><a href="controller_<?php echo $controllernome; ?>.php?d=<?php echo $_GET['d']; ?>&i=<?php echo $controllernome; ?>&controller=<?php echo $controllernome; ?>&acao=pdf" title="Gerar PDF"><img src="images/pdf.png" alt="Pdf"></a></td>
												<td class="acoes" width="33%"><a href="controller_<?php echo $controllernome; ?>.php?d=<?php echo $_GET['d']; ?>&i=<?php echo $controllernome; ?>&controller=<?php echo $controllernome; ?>&acao=xls" title="Gerar XLS"><img src="images/xls.png" alt="Excel"></a></td>
												
											</tr>
										</table>
									</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								$i=0;
								$classe = '';
								$datas = ''; 
								foreach($conteudoconsulta as $resultados){
								if($i % 2){
									$classe = '';
								}else{
									$classe = 'even';
								}	
								$i++;
							?>
								<tr class="<?php echo $classe; ?>"  id='linha<?php echo $resultados['id']; ?>'>
								<?php 
								$esquemacores = '';
								switch($resultados['status']){
									case 'CONCLUÍDA':
										$esquemacores = 'class = "concluida";';
										break;
									case 'CONCLUÍDA D':
										$esquemacores = 'class = "concluida"';//cyan
										break;
									case 'ATRASADA':
										$esquemacores = 'class = "atrasada"';
										break;
									case 'INICIADA DP':
										$esquemacores = 'class = "iniciada"';//green
										break;
									case 'INICIADA D':
										$esquemacores = 'class = "iniciada"';//green
										break;
									case 'CANCELADA':
										$esquemacores = 'class = "cancelada"';//red
										break;
									case 'EXCLUÍDA':
										$esquemacores = 'class = "cancelada"';//red
										break;
									case 'NÃO CONCLUÍDA':
										$esquemacores = 'class = "naoiniciada"';
										break;
									case 'NÃO INICIADA':
										$esquemacores = 'class = "naoiniciada"';
										break;
								}
								
								?>
								<td <?php echo $esquemacores; ?> id="td<?php echo $resultados['id']; ?>">&nbsp;</td>
								
								<?php
								foreach($campos as $titulo=>$campo){
									$valorobtido = $resultados[$campo];
									$resultados[$campo]=$valorobtido;
									$estilo = '';
									$onclick = '';
									if($campo =='inicio' || $campo == 'termino'){
										$estilo = ' class="datepicker styled" ';
										$exp = explode('-', $resultados[$campo]);
										if(strlen($resultados[$campo])>6 && ($exp[1]!='00' || $exp[2]!='00' || $exp[0]!='00')){
											$dataformatada = $exp[2].'/'.$exp[1].'/'.$exp[0];
											$resultados[$campo] = $dataformatada;
											//$datas .= "$( \"#{$campo}{$resultados['id']}\" ).datepicker(\"setDate\", $.datepicker.parseDate('yy-mm-dd', '$resultados[$campo]'));";
											$onclick = " onclick=\"$(this).datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', '$resultados[$campo]'));\" ";
										}
									}
									if($campoautocompletar[$titulo]>0){
										$estilo = ' class="'.$campo.'" ';
									}
									if($campoautosomar[$titulo]>0){
											$tmpvalor = $campoautosomarvalor[$titulo];
											$campoautosomarvalor[$titulo] = $tmpvalor +  $resultados[$campo];
									}
									if($tipos[$campo] =='dt'){
										$onclick = " onclick=\"$(this).datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', '$resultados[$campo]'));\" ";
										//echo "<td><input style='font-size:10px;' size='{$tamanhocampo[$titulo]}' type='text' name='{$campo}{$resultados['id']}' id='{$campo}{$resultados['id']}' value='{$dataformatada}' ".(($leitura[$campo])?' readonly=readonly ':'')." onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} ></td>";
										//echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}');\"  onchange=\"var k=".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modificastatus('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} >{$resultados[$campo]}</p></td>";
									}else{
										if($campo=='status'){
												//echo "<td><input style='font-size:10px;' size='{$tamanhocampo[$titulo]}' type='text' name='{$campo}{$resultados['id']}' id='{$campo}{$resultados['id']}' value='{$resultados[$campo]}' ".(($leitura[$campo])?' readonly=readonly ':'')." onchange=\"var k=".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modificastatus('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} ></td>";
												//echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}');\"  onchange=\"var k=".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modificastatus('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} >{$resultados[$campo]}</p></td>";
										}else{
												if($campo=='qtd_militar'){
													//echo "<td><input style='font-size:10px;' size='{$tamanhocampo[$titulo]}' type='text' name='{$campo}{$resultados['id']}' id='{$campo}{$resultados['id']}' value='{$resultados[$campo]}' ".(($leitura[$campo])?' readonly=readonly ':'')." onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modifica('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} ></td>";
												//	echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}');\" onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modifica('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick}>{$resultados[$campo]}</p></td>";
														
												}else{
													if($campo=='qtd_diaria'){
														//echo "<td><input style='font-size:10px;' size='{$tamanhocampo[$titulo]}' type='text' name='{$campo}{$resultados['id']}' id='{$campo}{$resultados['id']}' value='{$resultados[$campo]}' ".(($leitura[$campo])?' readonly=readonly ':'')." onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');modifica('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} ></td>";
													//	echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}');\" onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} >{$resultados[$campo]}</p></td>";
														
													}else{
														//echo "<td><input style='font-size:10px;' size='{$tamanhocampo[$titulo]}' type='text' name='{$campo}{$resultados['id']}' id='{$campo}{$resultados['id']}' value='{$resultados[$campo]}' ".(($leitura[$campo])?' readonly=readonly ':'')."  ></td>";
												//		echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}');\" onchange=\"".($funcoes[$campo])."('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');\" {$estilo} {$onclick} >{$resultados[$campo]}</p></td>";
													}
												}
										}
																				
											}
											$completafuncao = '';
									if($campo=='status'){
										$completafuncao = UrlEncodeNew("modificastatus('{$resultados['id']}','{$campo}', 'planejado', 'ajaxmodifica');");
										echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}','$completafuncao');\"  >{$resultados[$campo]}</p></td>";
									}else{
										if($campo=='qtd_militar' || $campo=='qtd_diaria' || $campo=='adicional' || $campo=='valor_diaria'|| $campo=='valor_passagem'){
											$completafuncao = UrlEncodeNew("calculadiariapassagem('{$resultados['id']}','{$campo}','planejado','ajaxmodifica');");
											echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}','$completafuncao');\"  >{$resultados[$campo]}</p></td>";
										}else{
											echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$resultados['id']}' onclick=\"{$funcoes[$campo]}('{$campo}','{$resultados['id']}','{$controllernome}','{$tipos[$campo]}','$completafuncao');\" >{$resultados[$campo]}</p></td>";
												
										}
									}
									
										
									}
								
								?>
									<!-- <td class="acoes"><a href="javascript:edita('<?php echo $resultados['id']; ?>');" title="Modificar"><img src="images/edit.png" alt="Editar"></a></td> -->
									<!-- -->
									<td class="acoes">&nbsp;</td>
									<td class="acoes"><a href="javascript:exclui('<?php echo $resultados['id']; ?>', '<?php echo $resultados['subdivisao'].'->('.$resultados['inicio'].'-'.$resultados['termino']; ?>)','#del<?php echo $resultados['id']; ?>');" title="Excluir" id='del<?php echo $resultados['id']; ?>'><img src="images/lixo.png" alt="Excluir"></a></td>
									<td class="acoes"><a href="javascript:ver('<?php echo $resultados['id']; ?>');" title="Detalhes"><img src="images/lupa.png" alt="Visualizar"></a></td>
									 
								</tr>
							<?php 
									}
									setlocale(LC_MONETARY,  "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
							?>
								<tr id="totalsomatorio" style="background-color:palegoldenrod;font-size:9px;">
								<?php
								echo "<td><p align='center'>&nbsp;</p></td>";
								foreach($campos as $titulo=>$campo){
									$somatorio = 0.0;
									if($campoautosomar[$titulo]){
										$somatorio = 	$campoautosomarvalor[$titulo];								
										//$somatorio = money_format('%i', $somatorio);
										echo "<td><p align='center'><b>{$somatorio}</b></p></td>";
									}else{
										echo "<td><p align='center'>&nbsp;</p></td>";
									}
								}
								
								?>
									<td class="acoes" colspan="3">&nbsp;</td>
								
								</tr>
																								
							</tbody>
						</table>

</form>
			
		<div class="pagination right">
			<form class="pagination right"  method="GET">
			<?php
				$s = 0;
				for($i=1;$i<=$paginas;$i++){
					$atributo = '';
					if($i==$pagina)	{
						$atributo = 'class="active"';
					}				
					if(($paginas==$pagina)&&($i==$pagina))	{
						$atributo = 'class="active"';
					}
					echo "<a href='?i={$_GET['i']}&acao={$_GET['acao']}&pagina=$i' $atributo    >$i</a>";
					$atributo = '';
				}
				if(($paginas==$pagina)&&($i==$pagina))	{
					$atributo = 'class="active"';
				}
				
				if($pagina<$paginas)	{
					$depois = $pagina + 1; 
					echo "<a href='?i={$_GET['i']}&acao={$_GET['acao']}&pagina=$depois' $atributo    >&raquo;</a>";
				}
				
			
			?>
			<input type="text" name="pagina" size="3" value="<?php echo $pagina; ?>"><input type="hidden" name="i" value="<?php echo $_GET['i']; ?>"><input type="hidden" name="acao" value="list">
			<input type="hidden" name="class_anterior" id="class_anterior" value="">
			<input type="hidden" name="attr_anterior" id="attr_anterior" value="">
			<input type="hidden" name="linhaid" id="linhaid" value="">
			<input type="hidden" name="tdid" id="tdid" value="">
			</form>
		</div>		<!-- .pagination ends -->
	</div>		<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
	</div>		<!-- .block ends -->




</div>
<?php 
	$autocomplete_ano = $db->fetch_array("select ano from orcamento_planejado group by ano order by ano asc ");
	$completaano = '';
	foreach($autocomplete_ano as $indica=>$valor){
		//$completasubdivisao .= "'".iconv('latin1', 'utf8', $valor['subdivisao'])."',"; 
		$completaano .= "'".$valor['ano']."',"; 
	}
	$completaano .= "''";


	$autocomplete_subdivisao = $db->fetch_array("select subdivisao from orcamento_planejado group by subdivisao order by subdivisao asc ");
	$completasubdivisao = '';
	foreach($autocomplete_subdivisao as $indica=>$valor){
		//$completasubdivisao .= "'".iconv('latin1', 'utf8', $valor['subdivisao'])."',"; 
		$completasubdivisao .= "'".$valor['subdivisao']."',"; 
	}
	$completasubdivisao .= "''";
	
	$autocomplete_status = $db->fetch_array("select status from orcamento_planejado group by status order by status asc ");
	$completastatus = '';
	foreach($autocomplete_status as $indica=>$valor){
		//$completastatus .= "'".iconv('latin1', 'utf8', $valor['status'])."',"; 
		$completastatus .= "'".$valor['status']."',"; 
	}
	$completastatus .= "''";

	$autocomplete_tipo = $db->fetch_array("select tipo from orcamento_planejado group by tipo order by tipo asc ");
	$completatipo = '';
	foreach($autocomplete_tipo as $indica=>$valor){
		//$completatipo .= "'".iconv('latin1', 'utf8', $valor['tipo'])."',";
		$completatipo .= "'".$valor['tipo']."',";
	}
	$completatipo .= "''";
	
	$autocomplete_descricao = $db->fetch_array("select descricao from orcamento_planejado group by descricao order by descricao asc ");
	$completadescricao = '';
	foreach($autocomplete_descricao as $indica=>$valor){
		//$completadescricao .= "'".iconv('latin1', 'utf8', $valor['descricao'])."',";
		$completadescricao .= "'".$valor['descricao']."',";
	}
	$completadescricao .= "''";
	
	$autocomplete_local = $db->fetch_array("select local from orcamento_planejado group by local order by local asc ");
	$completalocal = '';
	foreach($autocomplete_local as $indica=>$valor){
		//$completalocal .= "'".iconv('latin1', 'utf8', $valor['local'])."',";
		$completalocal .= "'".$valor['local']."',";
	}
	$completalocal .= "''";
	
	$autocomplete_mes = $db->fetch_array("select mes from orcamento_planejado group by mes order by mes asc ");
	$completames = '';
	foreach($autocomplete_mes as $indica=>$valor){
		//$completames .= "'".iconv('latin1', 'utf8', $valor['mes'])."',";
		$completames .= "'".$valor['mes']."',";
	}
	$completames .= "''";

	echo '<script type="text/javascript">
					
														';
	foreach($campos as $titulo=>$campo){
		if($campoautocompletar[$titulo]>0){
			eval('$temp=$completa'.$campo.';');
			echo 'var '.$campo.'src = ['.$temp.'];';
		}else{
			echo 'var '.$campo.'src = "";';
	
		}
	}
	echo '$(function() {';
	foreach($campos as $titulo=>$campo){
		if($campoautocompletar[$titulo]>0){
			echo 'if( typeof '.$campo.'src  != "" ){';
			echo '$( ".'.$campo.'" ).autocomplete({ source: '.$campo.'src });}';
		}
	}
	echo '});</script>';
	
?>
<script type="text/javascript">

function prepara(id){

	var linhaidnova = '#linha'+id;
	var tdidnova = '#td'+id;

	linhaid = $('#linhaid').val();
	tdid = $('#tdid').val();
	classanterior = $('#class_anterior').val();
	styleanterior = $('#attr_anterior').val();
	$(linhaid +' td').removeAttr('style');
	$(linhaid).removeClass().addClass(classanterior);
	$(tdid).removeAttr('style');

	$('#attr_anterior').val($(linhaidnova).attr('style'));
	$('#class_anterior').val($(linhaidnova).attr('class'));
	$('#linhaid').val($(linhaidnova).val());
	$('#tdid').val($(tdidnova).val());
	
	$(linhaidnova+' td').attr('style', 'background:none repeat scroll 0 0 yellow;');
	$(tdidnova).removeAttr('style');
	
}


</script>
<?php 
	
	$autocomplete_planejado = $db->fetch_array("select descricao from orcamento_planejado group by descricao order by descricao asc ");
	$completaplanejado = '';
	foreach($autocomplete_planejado as $indica=>$valor){
		//$completasubdivisao .= "'".iconv('latin1', 'utf8', $valor['subdivisao'])."',"; 
		$completaplanejado .= "'".$valor['descricao']."',"; 
	}
	$completaplanejado .= "''";

echo '<script type="text/javascript">';
foreach($campos as $titulo=>$campo){
	if($campoautocompletar[$titulo]>0){
		eval('$temp=$completa'.$campo.';');
		echo 'var '.$campo.'src = ['.$temp.'];';
	}else{
		echo 'var '.$campo.'src = "";';
		
	}
}
echo '$(function() {';
foreach($campos as $titulo=>$campo){
	if($campoautocompletar[$titulo]>0){
	echo 'if( typeof '.$campo.'src  != "" ){';
		echo '$( ".'.$campo.'" ).autocomplete({ source: '.$campo.'src });}';
	}
}
echo '});</script>';


?>
