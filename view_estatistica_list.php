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
						<a href="javascript:verfiltro('andamento');" title="Criar filtro"><img src="images/ico/32x32/<?php echo $filtroativado; ?>.png" alt="Pdf"></a>
						</div>  
					</div>		<!-- .block_head ends -->
					<div class="block_content">
					<form accept-charset="utf-8" action="controller_<?php  echo $controllernome; ?>.php" method="post" enctype="multipart/form-data" id="facilidade" onsubmit="return false;">
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
							<thead>
								<tr>
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
									<th colspan="2">
										<table width="60%" cellpadding="0" cellspacing="0" border="0" align="right">
											<tr>
												<td class="acoes"><a href="javascript:cad();" title="Novo Registro"><img src="images/novo.png" alt="Cadastrar"></a></td>
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
								<tr class="<?php echo $classe; ?>"  id='linha<?php
								$idchave = ''; 
								for($i=0;$i<count($campochave);$i++){
									$idchave .=$resultados[$campochave[$i]];
								}
								
								echo $idchave; ?>'><?php
								foreach($campos as $titulo=>$campo){
									$resultados[$campo]=$resultados[$campo];
									$estilo = '';
									$onclick = '';
									if($tipos[$campo] =='dt' ){
										$estilo = ' class="datepicker styled" ';
										$exp = explode('-', $resultados[$campo]);
										if(strlen($resultados[$campo])>6 && ($exp[1]!='00' || $exp[2]!='00' || $exp[0]!='00')){
											$dataformatada = $exp[2].'/'.$exp[1].'/'.$exp[0];
											$resultados[$campo] = $dataformatada;
											//$datas .= "$( \"#{$campo}{$resultados['id']}\" ).datepicker(\"setDate\", $.datepicker.parseDate('yy-mm-dd', '$resultados[$campo]'));";
											//$onclick = " onclick=\"$(this).datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', '$resultados[$campo]'));\" ";
										}
									}
									if($campoautocompletar[$titulo]>0){
										$estilo = ' class="'.$campo.'" ';
									}
									if($campoautosomar[$titulo]>0){
											$tmpvalor = $campoautosomarvalor[$titulo];
											$campoautosomarvalor[$titulo] = $tmpvalor +  $resultados[$campo];
									}
									
									echo "<td><p style='font-size:9px;padding-bottom:2px;color:black;min-height:15px;' id='{$campo}p{$idchave}' onclick=\"{$funcoes[$campo]}('{$campo}','{$idchave}','{$controllernome}','{$tipos[$campo]}');\" >{$resultados[$campo]}</p></td>";
										
									}
								
								?>
									<td class="acoes"><a href="javascript:exclui('<?php echo $idchave; ?>', '<?php echo $resultados[$campochave[0]]; ?>','#del<?php echo $idchave; ?>');" title="Excluir" id='del<?php echo $idchave; ?>' class='lixo'></a></td>
									<td class="acoes"><a href="javascript:edita('<?php echo $idchave; ?>');" title="Gerar prova" class='gerprova'></a></td>								
								</tr>
							<?php 
									}
									setlocale(LC_MONETARY,  "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
							?>
								<tr id="totalsomatorio" style="background-color:palegoldenrod;font-size:9px;">
								<?php
								foreach($campos as $titulo=>$campo){
									$somatorio = 0.0;
									if($campoautosomar[$titulo]){
										$somatorio = 	$campoautosomarvalor[$titulo];								
										$somatorio = money_format('%i', $somatorio);
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
					echo "<a href='?i={$_GET['i']}&acao={$_GET['acao']}&pagina=$i&pesquisa=".urlencode($_GET['pesquisa'])."' $atributo    >$i</a>";
					$atributo = '';
				}
				if(($paginas==$pagina)&&($i==$pagina))	{
					$atributo = 'class="active"';
				}
				
				if($pagina<$paginas)	{
					$depois = $pagina + 1; 
					echo "<a href='?i={$_GET['i']}&acao={$_GET['acao']}&pagina=$depois&pesquisa=".urlencode($_GET['pesquisa'])."' $atributo    >&raquo;</a>";
				}
				
			
			?>
			<input type="text" name="pagina" size="3" value="<?php echo $pagina; ?>"><input type="hidden" name="i" value="<?php echo $_GET['i']; ?>"><input type="hidden" name="acao" value="list"><input type="hidden" name="pesquisa" value="<?php echo urlencode($_GET['pesquisa']); ?>"></form>
		</div>		<!-- .pagination ends -->
	</div>		<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
	</div>		<!-- .block ends -->




</div>

<?php 
	
	$autocomplete_materia = $db->fetch_array("select materia from questoes_materia order by materia asc ");
	foreach($autocomplete_materia as $indica=>$valor){
		$completamateria .= "'".$valor['materia']."',"; 
	}
	$completamateria .= "''";

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
		echo '$( "#'.$campo.'" ).autocomplete({ source: '.$campo.'src });}';
	}
}
echo '});</script>';


?>
