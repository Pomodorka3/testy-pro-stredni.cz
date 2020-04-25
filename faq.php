<?php 

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

page_start("FAQ");
display_messages();
if (isset($_SESSION['user_id'])) {
	update_activity();
}

$user_id = $_SESSION['user_id'];

$content = "SELECT faq_id, faq_title, faq_content, faq_category FROM faq WHERE faq_visible = '1';";
$content_query = mysqli_query($connect, $content);

//Добавить подтверждение удаления faq.
?>

<div class="container">
	<div class="row mt-4">
		<!-- <div class="col-md-4 mt-3">
			<div>
				<h5 class="text-center font-weight-bold my-3" style="font-family: 'Baloo', cursive;">Mate dalsi otazky?</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam excepturi repellat dicta! Nam eligendi hic sequi possimus quia, vitae veritatis ratione!<br> Quibusdam possimus nemo, deleniti molestiae sint voluptate tenetur, illum, delectus reiciendis dolorem labore. Assumenda, delectus. Quisquam, assumenda ipsam maxime.
				You can ask other questions <a href="tickets"><u>here</u></a>
				</p>
			</div>
			<hr>
			<div>
				<h5 class="text-center font-weight-bold my-3" style="font-family: 'Baloo', cursive;">Lorem ipsum dolor?</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem perspiciatis itaque dolorum, iste, ex, voluptatem eum odio tempore illum neque repellendus earum doloremque odit, mollitia dolore.<br> Fugiat nobis aliquid dignissimos, ipsam quo omnis ratione. Sunt molestiae libero, maiores aut optio!</p>
			</div>
		</div> -->
		<div class="col-md-12">
			<!-- Add Jquery accordion -->
			<h4 class="mb-3 text-center font-weight-bold" style="font-family: 'Baloo', cursive;"><i class="far fa-question-circle"></i> Často kladené otázky</h4>
			<hr class="black mt-0 z-depth-1" style="width:100px;">
			Zde naleznete odpovědi na nejčastěji kladené otázky, pokud jste tady odpověď na Vaší otázku nenašli nebo se chcete podrobněji zeptat přejděte do sekce Tikety.
			<div class="accordion">
			<?php
			if (!$content_query) {
				echo "<p>Došlo k chybě při načítání FAQ.</p>";
			} else {
				$i = 0;
				while ($row = mysqli_fetch_array($content_query)) {
					$i++;
					if (user_in_group("Main administrator", $user_id)) {
						$removeButton = '<a data-toggle="modal" data-target="#removeModal'.$i.'" href=""><i class="fas fa-trash-alt text-danger mr-2" data-toggle="tooltip" title="Odstranit"></i></a>';
						$removeModal = '
						<div class="modal fade" id="removeModal'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel'.$i.'" aria-hidden="true">
							<div class="modal-dialog modal-center" role="document">
							<div class="modal-content">
								<div class="modal-header">
								<h4 class="modal-title w-100" id="removeModalLabel'.$i.'">Odstranit vybranou FAQ?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
									<span aria-hidden="true">&times;</span>
								</button>
								</div>
								<div class="modal-body d-flex">
									<a class="btn btn-success btn-sm mx-auto" href="faq_action?remove_id='.$row['faq_id'].'">Potvrdit</a>
								</div>
							</div>
							</div>
						</div>';
					}
				printf("<h6 class='my-4 heavy-rain-gradient'>%s%s</h6><div class='z-depth-1'>%s</div>%s",
				$removeButton,
				$row['faq_title'],
				$row['faq_content'],
				$removeModal);
				}
			}
			?>
			</div>
			<?php
			if (user_in_group("Main administrator", $user_id)) {
				echo "<div class='text-center mb-3'><a class='text-primary font-weight-bold' id='faq_add_button'><i class='fas fa-plus text-success'></i> Vytvořit novou FAQ</a></div>";
				echo "
				<form action='faq_action' method='post' style='display: none;' id='faq_add_form'>
					<input type='text' class='form-control mb-3' name='title' placeholder='Nadpis' maxlength='100' required autocomplete='off'>
					<textarea name='content' class='form-control mb-3' placeholder='Obsah bez fn(htmlspecialchars)' maxlength='1000' required></textarea>
					<button type='submit' class='btn btn-success d-flex mx-auto btn-sm mb-4'>Vytvořit</button>
				</form>";
			}
			?>
		</div>
	</div>
</div>
<?php
	page_end(true);
?>
</body>
  <!-- JQuery -->
  <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Accordion plugin -->
  <script type="text/javascript" src="js/plug_accordion/accordion.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script.js"></script>
  <?php echo $_SESSION['js_modal_show']; ?>


</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>