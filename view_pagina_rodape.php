
<style>
<!--

#spinner {
    height: 100px;
    left: 50%;
    margin: -25px 0 0 -25px;
    position: fixed;
    top: 50%;
    width: 100px;
    z-index: 1000;
    display:none;
}

-->
</style>

<div id="spinner">
	<img src="images/loading.gif" alt="">
</div>



<div id="footer">

	<p class="left">
		<a href="#"><b>SIOP</b> <i>versão <b>1.1</b></i></a>
	</p>
	<p class="center">
	<?php 
	$tempo = microtime(TRUE) - $medidorinicio;
	echo  'Tempo:<b>'.number_format($tempo, 6) . ' s</b>';
	?>
	</p>
	<p class="right">
		<a href="mailto:limainfo@gmail.com">limainfo@gmail.com</a>
	</p>

</div>


</div>
<!-- wrapper ends -->

</div>
<!-- #hld ends -->



</div>
<div style="display: none;" id="facebox">
	<div class="popup">
		<table>
			<tbody>
				<tr>
					<td class="tl"></td>
					<td class="b"></td>
					<td class="tr"></td>
				</tr>
				<tr>
					<td class="b"></td>
					<td class="body">
						<div class="content"></div>
						<div class="footer">
							<a class="close" href="#"> <img class="close_image" title="close"
								src="images/closelabel.gif">
							</a>
						</div>
					</td>
					<td class="b"></td>
				</tr>
				<tr>
					<td class="bl"></td>
					<td class="b"></td>
					<td class="br"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div>

	<div style="width: 100%;" id="manipulacao"
		class="ui-dialog-content ui-widget-content"></div>

</div>
</body>
</html>