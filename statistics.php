<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';

	//Сделать возможным только одиночный выбор у чекбоксов в фильтре shop.php.

	authorize_user(array("Main administrator"));
	full_register();
	page_start("Statistika");
	display_messages();
	update_activity();

	$shop_earn = 'SELECT shopearn_date, SUM(shopearn_value) FROM shop_earn GROUP BY DATE(shopearn_date);';
	$shop_earn_query = mysqli_query($connect, $shop_earn);

	$monthlyEarn = 'SELECT shopearn_date, SUM(shopearn_value) FROM shop_earn GROUP BY MONTH(shopearn_date)';
	$monthlyEarn_query = mysqli_query($connect, $monthlyEarn);
	
	$withdraw = 'SELECT withdraw_date, SUM(withdraw_sum) FROM withdraw WHERE withdraw_status = 1 GROUP BY DATE(withdraw_date);';
	$withdraw_query = mysqli_query($connect, $withdraw);

	$codesActivated = 'SELECT code_activated, SUM(code_value) FROM codes WHERE code_type = "balance" AND code_used = 1 GROUP BY DATE(code_activated);';
	$codesActivated_query = mysqli_query($connect, $codesActivated);

	$unconfirmedItems = 'SELECT COUNT(*), create_date FROM shop WHERE checked = 0 GROUP BY DATE(create_date);';
	$unconfirmedItems_query = mysqli_query($connect, $unconfirmedItems);

	$confirmedItems = 'SELECT COUNT(*), confirmed_date FROM shop WHERE checked = 1 GROUP BY DATE(confirmed_date);';
	$confirmedItems_query = mysqli_query($connect, $confirmedItems);

	$boughtItems = 'SELECT COUNT(*), buy_time FROM buy_events GROUP BY DATE(buy_time);';
	$boughtItems_query = mysqli_query($connect, $boughtItems);

	$reportedItems = 'SELECT COUNT(*), buy_time FROM buy_events GROUP BY DATE(buy_time);';
	$reportedItems_query = mysqli_query($connect, $reportedItems);

	$registeredUsers = 'SELECT COUNT(*), register_date FROM users WHERE activated = 1 GROUP BY DATE(register_date);';
	$registeredUsers_query = mysqli_query($connect, $registeredUsers);

	$loggedUsers = 'SELECT COUNT(DISTINCT ul_user_id), ul_date FROM users_log GROUP BY DATE(ul_date);';
	$loggedUsers_query = mysqli_query($connect, $loggedUsers);
?>

<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-shopping-cart"></i> Statistika</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<div class="row mx-1 my-4">
	<div class="container text-center">
		<a id="finance-table-toggler" class="text-primary font-weight-bold"><i class="fas fa-caret-down" id="finance-table-arrow-down"></i><i class="fas fa-caret-up" id="finance-table-arrow-up" style="display:none;"></i> Tabulka financí</a>
		<div id="finance-table" style="display:none;">
		<?php
			if (mysqli_num_rows($monthlyEarn_query) == 0) {
				echo '<p class="text-center font-weight-bold">Zatím nejsou žádné zisky.</p>';
			} else {
				echo '<div class="table-responsive my-4 mx-auto text-center">
				<table class="table table-striped table-hover table-sm table-bordered">
					<thead>
						<tr>
							<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Měsíc/rok</th>
							<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Koupeno položek</th>
							<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Hrubý zisk</th>
							<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">'.TAXES_MULTIPLIER*100 .'% daň</th>
							<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Čistý zisk</th>
						</tr>
					</thead>
				<tbody>';
				while ($monthlyEarn_row = mysqli_fetch_array($monthlyEarn_query)) {
					$monthlyBoughtItems = 'SELECT COUNT(*) FROM buy_events WHERE MONTH(buy_time) = MONTH("'.$monthlyEarn_row['shopearn_date'].'") GROUP BY MONTH(buy_time);';
					$monthlyBoughtItems_query = mysqli_query($connect, $monthlyBoughtItems);
					//$tooltip = 'Hrubý zisk je '.$monthlyEarn_row['SUM(shopearn_value)'].' CZK, z toho je '.$monthlyEarn_row['SUM(shopearn_value)']*TAXES_MULTIPLIER.' CZK daň ('.TAXES_MULTIPLIER*100 .'%)';
					echo $post_row = sprintf("
					<tr class='h-100'>
						<td class='my-auto p-0'>%s</td>
						<td class='my-auto p-0'>%s</td>
						<td class='my-auto p-0'>%.1f CZK</td>
						<td class='my-auto p-0'>%.1f CZK</td>
						<td class='my-auto p-0'>%.1f CZK</td>
					</tr>
					",
					date('m/Y', strtotime($monthlyEarn_row['shopearn_date'])),
					mysqli_fetch_row($monthlyBoughtItems_query)[0],
					$monthlyEarn_row['SUM(shopearn_value)'],
					$monthlyEarn_row['SUM(shopearn_value)']*floatval(TAXES_MULTIPLIER),
					$monthlyEarn_row['SUM(shopearn_value)']*(1-floatval(TAXES_MULTIPLIER)));
				}
				echo '</tbody></table></div>';
			}
		?>
		</div>
		<div id="moneyChart" class="mt-2" style="height: 370px; width: 100%;"></div>
		<div id="itemsChart" style="height: 370px; width: 100%;"></div>
		<div id="usersChart" style="height: 370px; width: 100%;"></div>
	</div>
</div>
<?php
	/*$i = 0;
	//$dataPoints[$i]['label'] = '10.06.2019';
	//$dataPoints[$i]['y'] = 0;
	$shop_earn = array();
	while ($shop_earn_row = mysqli_fetch_array($shop_earn_query)) {
		$i++;
		//array("label"=> date('d.m.Y', strtotime($date_row['shopearn_date'])), "y"=> $date_row['SUM(shopearn_value)']);
		//$dataPoints[$i]['label'] = date('d.m.Y', strtotime($date_row['shopearn_date']));
		//$dataPoints[$i]['y'] = $date_row['SUM(shopearn_value)'];
		array_push($shop_earn, array('label' => date('d.m.Y', strtotime($shop_earn_row['shopearn_date'])), 'y' => $shop_earn_row['SUM(shopearn_value)']));
	}
	$i = 0;
	$withdraws = array();
	while ($withdraw_row = mysqli_fetch_array($withdraw_query)) {
		$i++;
		array_push($withdraws, array('label' => date('d.m.Y', strtotime($withdraw_row['withdraw_date'])), 'y' => $withdraw_row['SUM(withdraw_sum)']));
	}*/
	$i = 0;
	$shop_earn = array();
	while ($shop_earn_row = mysqli_fetch_array($shop_earn_query)) {
		$i++;
		array_push($shop_earn, array('x' => strtotime(date('d.m.Y.', strtotime($shop_earn_row['shopearn_date']))).'000', 'y' => $shop_earn_row['SUM(shopearn_value)']));
	}

	$i = 0;
	$withdraws = array();
	while ($withdraw_row = mysqli_fetch_array($withdraw_query)) {
		$i++;
		array_push($withdraws, array('x' => strtotime(date('d.m.Y.', strtotime($withdraw_row['withdraw_date']))).'000', 'y' => $withdraw_row['SUM(withdraw_sum)']));
	}

	$i = 0;
	$codesActivated = array();
	while ($codesActivated_row = mysqli_fetch_array($codesActivated_query)) {
		if ($codesActivated_row['code_activated'] == '0000-00-00 00:00:00') {
			continue;
		}
		$i++;
		array_push($codesActivated, array('x' => strtotime(date('d.m.Y.', strtotime($codesActivated_row['code_activated']))).'000', 'y' => $codesActivated_row['SUM(code_value)']));
	}

	$i = 0;
	$unconfirmedItems = array();
	while ($unconfirmedItems_row = mysqli_fetch_array($unconfirmedItems_query)) {
		$i++;
		array_push($unconfirmedItems, array('x' => strtotime(date('d.m.Y.', strtotime($unconfirmedItems_row['create_date']))).'000', 'y' => $unconfirmedItems_row['COUNT(*)']));
	}

	$i = 0;
	$confirmedItems = array();
	while ($confirmedItems_row = mysqli_fetch_array($confirmedItems_query)) {
		if ($confirmedItems_row['confirmed_date'] == '0000-00-00 00:00:00') {
			continue;
		}
		$i++;
		array_push($confirmedItems, array('x' => strtotime(date('d.m.Y.', strtotime($confirmedItems_row['confirmed_date']))).'000', 'y' => $confirmedItems_row['COUNT(*)']));
	}

	$i = 0;
	$boughtItems = array();
	while ($boughtItems_row = mysqli_fetch_array($boughtItems_query)) {
		$i++;
		array_push($boughtItems, array('x' => strtotime(date('d.m.Y.', strtotime($boughtItems_row['buy_time']))).'000', 'y' => $boughtItems_row['COUNT(*)']));
	}

	$i = 0;
	$registeredUsers = array();
	while ($registeredUsers_row = mysqli_fetch_array($registeredUsers_query)) {
		$i++;
		array_push($registeredUsers, array('x' => strtotime(date('d.m.Y.', strtotime($registeredUsers_row['register_date']))).'000', 'y' => $registeredUsers_row['COUNT(*)']));
	}

	$i = 0;
	$loggedUsers = array();
	while ($loggedUsers_row = mysqli_fetch_array($loggedUsers_query)) {
		if ($loggedUsers_row['ul_date'] == '0000-00-00 00:00:00') {
			continue;
		}
		$i++;
		array_push($loggedUsers, array('x' => strtotime(date('d.m.Y.', strtotime($loggedUsers_row['ul_date']))).'000', 'y' => $loggedUsers_row['COUNT(DISTINCT ul_user_id)']));
	}
	//print_r($loggedUsers);
?>

<?php
	page_end(false);
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
  <script>
  //Timestamps are used in milliseconds!
window.onload = function () {
  	var chart1 = new CanvasJS.Chart("moneyChart", {
		zoomEnabled: true,
		animationEnabled: true,
		title: {
			text: "Finance"
		},
		axisX: {
			valueFormatString: "DD.MM.YYYY"
		},
		axisY: {
			title: "",
			prefix: "",
			suffix: " CZK"
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor: "pointer",
			verticalAlign: "top",
			horizontalAlign: "center",
			dockInsidePlotArea: true,
			itemclick: toogleDataSeries
		},
		data: [{
			type:"line",
			axisYType: "primary",
			lineColor: "lightgreen",
			name: "Hrubý zisk",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,### CZK",
			dataPoints: <?php echo json_encode($shop_earn, JSON_NUMERIC_CHECK); ?>
		},{
			type: "line",
			axisYType: "primary",
			lineColor: "red",
			name: "Vyplaceno",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,### CZK",
			dataPoints: <?php echo json_encode($withdraws, JSON_NUMERIC_CHECK); ?>
		},{
			type: "line",
			axisYType: "primary",
			lineColor: "green",
			name: "Kódy",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,### CZK",
			dataPoints: <?php echo json_encode($codesActivated, JSON_NUMERIC_CHECK); ?>
		}]
	});
	var chart2 = new CanvasJS.Chart("itemsChart", {
		zoomEnabled: true,
		animationEnabled: true,
		title: {
			text: "Testy"
		},
		axisX: {
			valueFormatString: "DD.MM.YYYY"
		},
		axisY: {
			title: "",
			prefix: "",
			suffix: ""
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor: "pointer",
			verticalAlign: "top",
			horizontalAlign: "center",
			dockInsidePlotArea: true,
			itemclick: toogleDataSeries
		},
		data: [{
			type:"line",
			axisYType: "primary",
			lineColor: "red",
			name: "Přidané nezkontrolované testy",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,###",
			dataPoints: <?php echo json_encode($unconfirmedItems, JSON_NUMERIC_CHECK); ?>
		},{
			type: "line",
			axisYType: "primary",
			lineColor: "green",
			name: "Přidané zkontrolované testy",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,###",
			dataPoints: <?php echo json_encode($confirmedItems, JSON_NUMERIC_CHECK); ?>
		},{
			type: "line",
			axisYType: "primary",
			lineColor: "yellow",
			name: "Koupené předměty",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,###",
			dataPoints: <?php echo json_encode($boughtItems, JSON_NUMERIC_CHECK); ?>
		}]
	});
	var chart3 = new CanvasJS.Chart("usersChart", {
		zoomEnabled: true,
		animationEnabled: true,
		title: {
			text: "Uživatelé"
		},
		axisX: {
			valueFormatString: "DD.MM.YYYY"
		},
		axisY: {
			title: "",
			prefix: "",
			suffix: ""
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor: "pointer",
			verticalAlign: "top",
			horizontalAlign: "center",
			dockInsidePlotArea: true,
			itemclick: toogleDataSeries
		},
		data: [{
			type:"line",
			axisYType: "primary",
			lineColor: "blue",
			name: "Noví uživatelé",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,###",
			dataPoints: <?php echo json_encode($registeredUsers, JSON_NUMERIC_CHECK); ?>
		},{
			type: "line",
			axisYType: "primary",
			lineColor: "red",
			name: "Přihlásilo se",
			showInLegend: true,
			xValueType: "dateTime",
			xValueFormatString: "DD.MM.YYYY",
			markerSize: 5,
			yValueFormatString: "#,###",
			dataPoints: <?php echo json_encode($loggedUsers, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart1.render();
	chart2.render();
	chart3.render();
	
	function toogleDataSeries(e){
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		} else{
			e.dataSeries.visible = true;
		}
		chart1.render();
		chart2.render();
		chart3.render();
	}
}
//Toggle finance table
$("#finance-table-toggler").click(function(){
    $("#finance-table").slideToggle("slow");
    $("#finance-table-arrow-down").toggle();
    $("#finance-table-arrow-up").toggle();
});
</script>
<script src="js/CanvasJs/canvasjs.min.js"></script>
<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>