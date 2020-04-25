<?php 

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

authorize_user();
full_register();
//school_check();
last_ip();
display_messages();
update_activity();

$user_id = $_SESSION['user_id'];

if ($_SESSION['school_id'] == '0') {
	handle_error("Zatím nemáte nastavenou školu!", "school_info (session[school_id]=0)");
}

if (isset($_GET['school_id'])) {
	$school_id = $_GET['school_id'];

	$school_select = sprintf("SELECT school_name, school_created FROM school WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $school_id));
	$school_select_query = mysqli_query($connect, $school_select);
	if (mysqli_num_rows($school_select_query) == 0) {
		handle_error("Tato škola neexistuje!", "school_info (wrong school_id)");
	}
	$school_select_row = mysqli_fetch_array($school_select_query);
	$school_created = strtotime($school_select_row['school_created']);

	$members_count = sprintf("SELECT COUNT(user_id) FROM users WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $school_id));
	$members_count_query = mysqli_query($connect, $members_count);
	$members_count_row = mysqli_fetch_row($members_count_query);

	$users_balance = sprintf("SELECT balance FROM users WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $school_id));
	$users_balance_query = mysqli_query($connect, $users_balance);
	$total_balance = 0;
	while ($users_balance_row = mysqli_fetch_row($users_balance_query)) {
		$total_balance += $users_balance_row[0];
	}

	$items_count = sprintf("SELECT COUNT(item_id) FROM shop WHERE school_id = '%d' AND visible = 1 AND checked = 1;",
	mysqli_real_escape_string($connect, $school_id));
	$items_count_query = mysqli_query($connect, $items_count);
	$items_count_row = mysqli_fetch_row($items_count_query);

	$validators = sprintf("SELECT u.user_id, u.username, u.last_action, ug.event_date FROM users u, users_groups ug WHERE u.school_id = '%d' AND ug.group_id = 2 AND ug.user_id = u.user_id;",
	mysqli_real_escape_string($connect, $school_id));
	$validators_query = mysqli_query($connect, $validators);

	$validators_total = sprintf("SELECT COUNT(u.user_id) FROM users u, users_groups ug WHERE u.school_id = '%d' AND ug.group_id = 2 AND ug.user_id = u.user_id;",
	mysqli_real_escape_string($connect, $school_id));
	$validators_total_query = mysqli_query($connect, $validators_total);
	$validators_total_row = mysqli_fetch_row($validators_total_query);
} else {
	$school_select = sprintf("SELECT school_name, school_created FROM school WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$school_select_query = mysqli_query($connect, $school_select);
	$school_select_row = mysqli_fetch_array($school_select_query);
	$school_created = strtotime($school_select_row['school_created']);

	$members_count = sprintf("SELECT COUNT(user_id) FROM users WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$members_count_query = mysqli_query($connect, $members_count);
	$members_count_row = mysqli_fetch_row($members_count_query);

	$users_balance = sprintf("SELECT balance FROM users WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$users_balance_query = mysqli_query($connect, $users_balance);
	$total_balance = 0;
	while ($users_balance_row = mysqli_fetch_row($users_balance_query)) {
		$total_balance += $users_balance_row[0];
	}

	$items_count = sprintf("SELECT COUNT(item_id) FROM shop WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$items_count_query = mysqli_query($connect, $items_count);
	$items_count_row = mysqli_fetch_row($items_count_query);

	$validators = sprintf("SELECT u.user_id, u.username, u.last_action, ug.event_date FROM users u, users_groups ug WHERE u.school_id = '%d' AND ug.group_id = 2 AND ug.user_id = u.user_id;",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$validators_query = mysqli_query($connect, $validators);

	$validators_total = sprintf("SELECT COUNT(u.user_id) FROM users u, users_groups ug WHERE u.school_id = '%d' AND ug.group_id = 2 AND ug.user_id = u.user_id;",
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$validators_total_query = mysqli_query($connect, $validators_total);
	$validators_total_row = mysqli_fetch_row($validators_total_query);
}

page_start("Info o škole");

?>
<div class="container mt-4">
	<h4 style="font-family: 'Baloo', cursive;" class="text-center"><i class="fas fa-graduation-cap"></i> <?php echo $school_select_row['school_name']; ?></h4>
	<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
	<div class="row">
		<div class="col-md-6 text-center text-md-left">
			<h5 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">Informace</h5>
			<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
			<p><span class="font-weight-bold">Přidána:</span> <?php echo date('d.m.Y', $school_created); ?></p>
			<p><span class="font-weight-bold">Členů:</span> <?php echo $members_count_row[0]; ?></p>
			<p><span class="font-weight-bold">Položek na prodej:</span> <?php echo $items_count_row[0]; ?></p>
			<p><span class="font-weight-bold">Celkem peněz:</span> <?php echo $total_balance; ?> Kc</p>
		</div>
		<div class="col-md-6">
			<div class="border border-light rounded p-3 cloudy-knoxville-gradient z-depth-1">
			<h5 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">Validátoři</h5>
			<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
					<?php
					if (mysqli_num_rows($validators_query) == 0) {
						echo "<p class='text-center'>V této škole zatím ještě nejsou žádní Validátoři. Jestli se chcete stát jedním z nich, <a data-toggle='modal' data-target='#applyModal' class='text-primary font-weight-bold'>zde</a> můžete podat žádost. (V každé škole může být maximálně 5 Validátorů)</p>";
					} else {
						echo '<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Uživ. jméno</th>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Od</th>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Status</th>
								</tr>
							</thead>
						<tbody>';
						while ($validators_row = mysqli_fetch_array($validators_query)) {
							$status = '';
							$date = date("Y-m-d H:i:s", strtotime('-2 minutes'));
							if ($validators_row['last_action'] > $date ) {
								$status = '<i class="fas fa-circle text-success" data-toggle="tooltip" title="Online"></i>';
							} else {
								$status = '<i class="far fa-circle grey-text" data-toggle="tooltip" title="Offline"></i>';
							}
							$event_date = strtotime($validators_row['event_date']);
							echo $validators_list = sprintf("
								<tr class='h-100'>
									<td class='my-auto'><a href='profile_show?profile_id=%d' class='font-weight-bold text-primary'>%s</a></td>
									<td class='my-auto'>%s</td>
									<td class='my-auto'>%s</td>
								</tr>
							",
							$validators_row['user_id'],
							$validators_row['username'],
							date('d.m.Y', $event_date),
							$status);
						}
						echo '</tbody></table></div>';
					}
					?>
			<?php
			if ($validators_total_row[0] < VALIDATORS_NEEDED) {
				echo "<div class='d-flex'><a class='btn btn-success btn-sm mx-auto' data-toggle='modal' data-target='#applyModal'>Podat žádost</a></div>";
				echo <<<EOD
				<div class='modal fade' id='applyModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		            <div class='modal-dialog modal-center' role='document'>
		              <div class='modal-content'>
		                <div class='modal-header'>
		                  <h4 class='modal-title w-100' id='myModalLabel' style="font-family: 'Baloo', cursive;">Podat žádost o přidání do skupiny Validátorů</h4>
		                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
		                    <span aria-hidden='true'>&times;</span>
		                  </button>
		                </div>
		                <div class='modal-body'>
		                  <form action='validators_apply' class='text-center' method='post'>
							<textarea name='biography' id='biography' rows='6' class='form-control rounded mb-4' minlength='40' maxlength='1000' autocomplete='off' required placeholder='Krátký životopis o sobě (minimálně 40 slov):'></textarea>
							<p class="text-center font-weight-bold text-danger" style="font-size:17px;">Nezapomeňte si nastavit své reálné jméno a přílmení, a alespoň 1 sociální síť v nastavení svého účtu!</p>
		                    <p class="text-center">V případě nutnosti Vás budeme kontaktovat pro vysvětlení/upřesnění informací.</p>
		                    <button type='submit' class='btn btn-success btn-sm d-flex mx-auto mt-3'>Odeslat</button>
		                  </form>
		                </div>
		              </div>
		            </div>
		          </div>
EOD;
			} else {
				echo "<p class='text-center'>V této škole je dostatečný počet validátorů.</p>";
			}
			?>
		</div>
	</div>
	</div>
</div>

<?php
	page_end(true);
?>
</body>
  <!-- JQuery -->
  <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
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