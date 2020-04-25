<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';

session_start();
//page_start("Sign in");
display_messages();

//Check if session_hash from table corresponds with session_hash from $_COOKIE
$sessionHash = sprintf("SELECT session_hash FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $_SESSION['user_id']));
$sessionHash_query = mysqli_query($connect, $sessionHash);
$sessionHash_row = mysqli_fetch_row($sessionHash_query);

if (!isset($_COOKIE['session_hash']) || $sessionHash_row[0] != $_COOKIE['session_hash']) {
	setcookie('session_hash', $hash, 1, '/');
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	unset($_SESSION['balance']);
	unset($_SESSION['first_name']);
	unset($_SESSION['last_name']);
	unset($_SESSION['school_id']);
	unset($_SESSION['image_path']);
	unset($_SESSION['status']);
	unset($_SESSION['notifications_count']);
}
//-------------------------------

if (isset($_GET['ref'])) {
	$refCode = '?ref='.$_GET['ref'];
	$refCode_pagination = '&ref='.$_GET['ref'];
} else {
	$refCode = '';
	$refCode_pagination = '';
}

if (!isset($_GET['page']) || ($_GET['page'] == "") || ($_GET['page'] == 0)) {
	$page = 1;
} else {
	$page = $_GET['page'];
}
$items_per_page = 4;
$offset = ($page-1)*$items_per_page;
$total_pages_sql = 'SELECT COUNT(*) FROM news WHERE news_visible = 1;';
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);

$news = sprintf('SELECT n.news_id, n.news_title, n.news_date, n.news_content, n.news_createdby, u.username FROM news n, users u WHERE n.news_visible = 1 AND u.user_id = n.news_createdby ORDER BY n.news_date DESC LIMIT %s, %s;',
$offset,
$items_per_page);
$news_query = mysqli_query($connect, $news);

$registeredUsers = 'SELECT COUNT(*) FROM users';
$registeredUsers_query = mysqli_query($connect, $registeredUsers);
$registeredUsers_row = mysqli_fetch_row($registeredUsers_query);

$shopItems = 'SELECT COUNT(*) FROM shop;';
$shopItems_query = mysqli_query($connect, $shopItems);
$shopItems_row = mysqli_fetch_row($shopItems_query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-150448559-1"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-150448559-1');
	</script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="Chcete být úspěšní ve studiu a přitom si přivydělávat? Na našem portálu je to možné! Zde můžete nejen nakupovat středoškolské testy jiných uživatelů, ale i prodávat svoje!">
  <title>Testy pro střední školy | Nakupujte a prodávejte testy pro střední školy</title>
  <!-- Favicon -->
  <link rel="icon" href="/img/system/TS-mini.png">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-index.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Signika|Jura|Baloo|Righteous|Oswald&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/animate.min.css">
  <link rel="stylesheet" href="css/component.css">
  <script src="js/CreativeLinkEffects"></script>
  <style type="text/css">

  </style>
</head>

<body>
<div class='modal fade' id='contactModal' tabindex='-1' role='dialog' aria-labelledby='contactModalLabel' aria-hidden='true'>
  <div class='modal-dialog modal-center' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h4 class='modal-title w-100' id='contactModalLabel'  style="font-family: 'Baloo', cursive;">Kontakty</h4>
        <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class="text-center mb-3">
          <p class="mb-0"><span class="font-weight-bold">Podnikající osoba:</span> Michael Berezovský</p>
          <p class="mb-0"><span class="font-weight-bold">Tel.:</span> +420775323772</p>
          <p class="mb-0"><span class="font-weight-bold">Adresa sídla:</span> Pod Lihovarem 2232, 256 01, Benešov</p>
          <p class="mb-0"><span class="mb-1 font-weight-bold">IČO:</span> 08585547</p>
          <p class="mb-0"><span class="mb-1 font-weight-bold">E-mail:</span> admin@testy-pro-stredni.cz</p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class='modal fade' id='createPostModal' tabindex='-1' role='dialog' aria-labelledby='createPostModalLabel' aria-hidden='true'>
  <div class='modal-dialog modal-center' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h4 class='modal-title w-100' id='createPostModalLabel' style="color:#000;">Vytvořit nový příspěvek</h4>
        <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <form action='index_action?action=createPost' class='text-center' method='POST'>
			<input type="text" name="post_title" class="form-control mb-4" maxlength="50" autocomplete="off" required placeholder="Nadpis">
        	<textarea name='post_content' id='post_content' rows='6' class='form-control rounded mb-4' maxlength='1000' autocomplete='off' required placeholder='Obsah (bez fn(htmlspecialchars) pro vytváření potřebných odkazů)'></textarea>
        	<button type='submit' class='btn btn-success btn-sm d-flex mx-auto mt-3'>Vytvořit</button>
        </form>
      </div>
    </div>
  </div>
</div>
	<!-- Navbar -->
	<nav class="navbar fixed-top navbar-expand-lg navbar-dark scrolling-navbar cl-effect-21">
      <a class="navbar-brand no-effect" href="index">
        <img src="/img/system/TS-small.png" alt="Koupit testy" class="img-fluid" style="height:50px;">
      </a>

      <!-- Collapse -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Zobrazit/schovat navigaci">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Links -->
      <div class="collapse navbar-collapse" id="navbarSupportedContent">

        <!-- Left -->
        <ul class="navbar-nav mr-auto">
          <li class="nav-item mx-auto mx-md-2">
            <a class="nav-link" href="#about-us">O nás</a>
          </li>
          <li class="nav-item mx-auto mx-md-2">
            <a class="nav-link" href="#news">Novinky</a>
		  </li>
		  <?php
			if (isset($_SESSION['user_id']) && user_in_group("Main administrator", $_SESSION['user_id'])) {
				echo '
				<li class="nav-item mx-auto mx-md-2">
					<a class="nav-link font-weight-bold text-primary" data-toggle="modal" data-target="#createPostModal">Vytvořit nový příspěvek</a>
				</li>';
			}
			?>
        </ul>

        <!-- Right -->
        <ul class="navbar-nav nav-flex-icons">
			<?php
				if (isset($_SESSION['user_id'])) {
					echo '
					<li class="nav-item mx-auto mx-md-2">
						<a href="profile" class="nav-link border border-light rounded no-effect"><i class="fas fa-user"></i> Profil</a>
				  	</li>';
				} else {
					echo '
					<li class="nav-item mx-auto mx-md-2">
						<a href="signup'.$refCode.'" class="nav-link border border-light rounded no-effect" style="background:rgba(0,255,0,0.5);">Registrace</a>
					</li>
					<li class="nav-item mx-auto mx-md-2">
						<a href="signin" class="nav-link border border-light rounded no-effect">Přihlášení</a>
					</li>';
				}
			?>
         
        </ul>

      </div>
  </nav>
  <!-- Navbar -->
<div id="carousel-example-1z" class="carousel slide carousel-fade" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#carousel-example-1z" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-example-1z" data-slide-to="1"></li>
      <li data-target="#carousel-example-1z" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
      <div class="carousel-item active">
        <div class="view" style="background-image: url('../img/system/index-1.jpg'); background-repeat: no-repeat; background-size: cover;">
          <div class="mask rgba-black-light d-flex justify-content-center align-items-center">
            <div class="text-center white-text mx-5 wow fadeIn">
			  <img src="<?php echo LARGE_LOGO_SRC; ?>" alt="Nakoupit testy" class="img-fluid" style="">
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="view" style="background-image: url('../img/system/index-2.jpg'); background-repeat: no-repeat; background-size: cover;">
          <div class="mask rgba-black-light d-flex justify-content-center align-items-center">
            <div class="text-center white-text mx-5 wow fadeIn">
			<img src="/<?php echo LARGE_LOGO_SRC; ?>" alt="Nakoupit testy" class="img-fluid" style="">
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="view" style="background-image: url('../img/system/index-3.jpg'); background-repeat: no-repeat; background-size: cover;">
          <div class="mask rgba-black-light d-flex justify-content-center align-items-center">
            <div class="text-center white-text mx-5 wow fadeIn">
				<img src="<?php echo LARGE_LOGO_SRC; ?>" alt="Nakoupit testy" class="img-fluid" style="">
			<!-- <h1 class="mb-4">
                <strong style="font-family: 'Baloo', cursive;">S naším systémem Vám škola půjde líp</strong>
			  </h1>
			  <hr class="white">
              <p class="mb-4">
                <strong>Jedinečná platforma se školními materiály a testy v České Republice</strong>
              </p>

              <a target="_blank" href="signup" class="btn btn-outline-white btn-lg">Připojit se<i class="fas fa-graduation-cap ml-2"></i>
              </a> -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <a class="carousel-control-prev" href="#carousel-example-1z" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Předchozí</span>
    </a>
    <a class="carousel-control-next" href="#carousel-example-1z" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Další</span>
    </a>

  </div>

  <!-- <main style="background-image: url('img/system/background.jpg'); background-repeat: no-repeat; background-size:cover;"> -->
  <main style="background-image: linear-gradient(to bottom,#30cfd0 0,#330867 100%);">
  <div id="particles-js"></div>
	<div class="">
		<section id="about-us" class="py-5">
			<div class="container">
				<h4 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">O nás</h4>
				<hr class="black" style="width:100px;">
				<!-- <p class="p-5">Tato webová stránka slouží jako obchod s testy pro střední školy, vyšší ročníky gymnázií a jiné "ústavy". Testy zde můžete nakupovat ve formě souborů a nabízet (pokud test máte) s určitou cenou a ziskem. Nabízením testů/přidáním školy rozšíříte pro studenty Vaší školy
učební/testovací materiály, které jim při troše snahy dost přilepší, zvlášť pokud neví, co se mají učit.</p> -->
				<p class="p-5">
					Určitě každý z vás měl představu že by psal všechny testy na jedničku a nikdy nedostával špatné známky. 
					Zameškali jste školu, nepřipravili jste se na test, nebo vůbec nemáte chuť se něco učit? 
					Tento portál vám s těmito problémy může pomoct, právě zde můžete nakupovat a prodávat své staré testy ze střední školy. To je dobré, že? Lehký přivýdělek a dobré známky najednou už nejsou pouhým snem každého středoškoláka, s tímto webem se to vše může stát realitou.<br><br>
					Na našem webu je nákup a prodej testů velice jednoduchý, i "předškolák" by to zvládnul.
					Pokud se budete řídit našimi podmínkami, nepřinese vám ani koupě ani prodej testů žádné problémy. Prodat a koupit u nás test můžete <span class="font-weight-bold">úplně legálně!</span><br><br>
					Minimálně můžete získat lehké peníze. A to pouze tím, že si nastavíte referální kód a budete<br>sdílet tuto stránku pomocí tlačítka <span class="font-weight-bold">sdílet</span> v <span class="font-weight-bold orange-text">Nastavení > Kódy > Sdílet</span>
				</p>
				<div class="row text-black">
					<div class="col-md-8">
					<h4 class="text-center text-dark" style="font-family: 'Baloo', cursive;">Proč využívat naše služby?</h4>
					<hr style="width: 100px;" class="black">
						<div class="row d-flex text-center my-4">
							<div class="col-md-4 my-md-auto text-md-right mb-3 mt-2">
								<img src="img/system/coins.svg" alt="Coins" style="width:80px;">
							</div>
							<div class="col-md-4 my-auto border rounded cloudy-knoxville-gradient z-depth-2 mx-3 mx-md-0">
								<p class="mb-0 p-3">Náš systém Vám umožní získávát lehké peníze. A to buď prodáváním svých starých testů, nebo pozváním kamarádů.</p>
							</div>
						</div>
						<div class="row d-flex text-center my-4">
							<div class="col-md-4 mb-3 mt-2 mb-md-0">
								<img src="img/system/clock.svg" alt="Clock" style="width:80px;" class="d-md-none">
							</div>
							<div class="col-md-4 my-auto border rounded cloudy-knoxville-gradient z-depth-2 mx-3 mx-md-0">
								<p class="mb-0 p-3">Veškeré operace v našem systému probíhají okamžitě a bezpochybně. V případě špatného popisku, nebo špatné fotky Vám budou Vaše peníze vráceny.</p>
							</div>
							<div class="col-md-4 my-auto text-left">
								<img src="img/system/clock.svg" alt="Clock" style="width:80px;" class="d-none d-md-block">
							</div>
						</div>
						<div class="row d-flex text-center my-4">
							<div class="col-md-4 my-md-auto text-md-right mb-3 mt-2">
								<img src="img/system/anonymous.svg" alt="Anonymous" style="width:80px;">
							</div>
							<div class="col-md-4 my-auto border rounded cloudy-knoxville-gradient z-depth-2 mx-3 mx-md-0">
								<p class="mb-0 p-3">Každý uživatel je anonymizován, ale v případě zájmu můžete na svém profilu zveřejnit i své sociální síťě.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<h4 class="text-center text-dark" style="font-family: 'Baloo', cursive;">Jak to u nás vypadá?</h4>
						<hr>
						<!-- <p class="text-center">Nemáte úspěchy ve škole? Máte stres před testem? Chcete si přivydělat? Toto vše Vám pomůžeme vyřešit! Náš systém je jedinečný na trhu. Vedeme férové a průhledné podnikání. Včas provádíme veškeré finanční operace. </p> -->
						<p class="text-center">Vpravo nahoře se můžete přihlásit a registrovat. Po přihlášení se Vám spustí tutoriál. Po nastavení svého profilu budete mít zpřístupněný obchod a ostatní funkce.
V horní liště se nachází Obchod s testy, Vklad, Výběr, Pozvaní lidé, FAQ, Tikety a napravo se nachází aktuální stav peněženky a další rozbalovací menu s obsahem: Profil, koupené testy, prodávané testy, vklad a nastavení.</p>
						<div id="carouselImages" class="carousel slide carousel-fade rounded mt-5" data-ride="carousel">
							<div class="carousel-inner">
								<div class="carousel-item active">
									<img class="d-block w-100 rounded" src="img/system/Interface-1.png">
								</div>
								<div class="carousel-item">
									<img class="d-block w-100 rounded" src="img/system/Interface-2.png">
								</div>
								<div class="carousel-item">
									<img class="d-block w-100 rounded" src="img/system/Interface-3.png">
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</section>
		<!--<section id="image1">
		<img src="img/pexels-photo-2110950.jpeg" alt="" class="img-fluid image1">
		</section>-->
		<section id="statistics" class="mx-3 mx-md-0">
			<div class="container border py-5 rounded custom-shadow">
				<div class="row container m-0">
					<div class="col-md-6">
						<h4 class="font-weight-bold text-center" style="font-family: 'Baloo', cursive;">Zaregistrováno uživatelů:</h4>
						<p class="font-weight-bold text-center h3"><?php echo $registeredUsers_row[0] + 40; ?></p>
					</div>
					<div class="col-md-6">
						<h4 class="font-weight-bold text-center" style="font-family: 'Baloo', cursive;">Testů na prodej:</h4>
						<p class="font-weight-bold text-center h3"><?php echo $shopItems_row[0] + 10; ?></p>
					</div>
				</div>
			</div>
		</section>
		<section class="mt-5 container" id="news">
			<h4 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">Aktuality</h4>
			<hr class="black" style="width:100px;">
			<div class="row">
				<?php
					if (mysqli_num_rows($news_query) == 0) {
						echo '<p class="mx-auto text-center">Zde nejsou žádné příspěvky nebo jste na špatné stránce!</p>';
					} else {
						$i = 0;
						while ($news_row = mysqli_fetch_array($news_query)) {
							$i++;
							if (isset($_SESSION['user_id'])) {
								$createdBy = 'uživatelem <a href="profile_show?profile_id='.$news_row['news_createdby'].'" class="font-weight-bold text-primary">'.$news_row['username'].'</a>';
							} else {
								$createdBy = 'uživatelem <span class="font-weight-bold text-primary">'.$news_row['username'].'</span>';
							}
							if ($news_row['news_createdby'] == 0) {
								$createdBy = '<span class="font-weight-bold text-primary">Systémem</span>';
							}
							if (isset($_SESSION['user_id']) && user_in_group("Main administrator", $_SESSION['user_id'])) {
								$removeButton = '<a data-toggle="modal" data-target="#removeModal'.$i.'"><i class="fas fa-trash text-danger" data-toggle="tooltip" title="Odstranit"></i></a>';
							}
							$removeModal = "
							<div class='modal fade' id='removeModal".$i."' tabindex='-1' role='dialog' aria-labelledby='removeModalLabel".$i."' aria-hidden='true'>
							  <div class='modal-dialog modal-center' role='document'>
								<div class='modal-content'>
								  <div class='modal-header'>
									<h4 class='modal-title w-100' id='removeModalLabel".$i."' style='color:#000;'>Odstranit vybraný příspěvek?</h4>
									<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
									  <span aria-hidden='true'>&times;</span>
									</button>
								  </div>
								  <div class='modal-body d-flex'>
									<a class='btn btn-danger btn-sm mx-auto' href='index_action?action=removePost&post_id=".$news_row['news_id']."'>Potvrdit</a>
								  </div>
								</div>
							  </div>
							</div>";
							printf('
							<div class="col-md-6">
								<div class="text-center unique-color-dark p-3 custom-shadow rounded my-3">
									<h5 class="font-weight-bold mb-0">%s</h5>
									<p style="font-size:13px;">%s dne %s</p>
									<hr class="white" style="width:100px;">
									<p>%s</p>
									%s
								</div>
							</div>
							%s',
							$news_row['news_title'],
							$createdBy,
							date('d.m.Y \v H:i', strtotime($news_row['news_date'])),
							$news_row['news_content'],
							$removeButton,
							$removeModal);
						}
						//Pagination
						$current_number = $page;
						if ($total_pages == 1) {
							$prev_class = 'd-none';
							$next_class = 'd-none';
							//$backward_class = 'd-none';
							//$forward_class = 'd-none';
							//$current_class = 'd-none';
							$current_page = $current_number;
							$prev_number = '';
							$next_number = '';
						} else {
							$prev_number = $current_number - 1;
							$next_number = $current_number + 1;
						}
						if ($page <= 1) {
							$prev_class = 'd-none';
							$prev_number = '';
						} else {
							$prev_class = '';
						}
						if ($page >= $total_pages) {
							$next_class = 'd-none';
							$next_number = '';
						} else {
							$next_class = '';
						}
						echo $pagination = "
						<div class='container d-flex mx-auto my-3'>
							<nav aria-label='Page nav' class='mx-auto'>
								<ul class='pagination pg-blue'>
									<li class='page-item $backward_class'><a href='index?page=1$refCode_pagination#news' id='1' class='page-link page'><i class='fas fa-fast-backward'></i></a></li>
									<li class='page-item $prev_class'><a href='index?page=$prev_number$refCode_pagination#news' id='".$prev_number."' class='page-link page'>".$prev_number."</a></li>
									<li class='page-item $current_class'><a href='index?page=$current_number$refCode_pagination#news' id='".$current_number."' class='page-link page'><u>".$current_number."</u></a></li>
									<li class='page-item $next_class'><a href='index?page=$next_number$refCode_pagination#news' id='".$next_number."' class='page-link page'>".$next_number."</a></li>
									<li class='page-item $forward_class'><a href='index?page=$total_pages$refCode_pagination#news' id='$total_pages' class='page-link page'><i class='fas fa-fast-forward'></i></a></li>
								</ul>
							</nav>
						</div>";
					}
				?>
			</div>
		</section>
	</div>
  </main>
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
  <script type="text/javascript" src="js/custom-script-noAjax.js"></script>
  <script src="js/Parallax/simpleParallax.js"></script>
  <script src="js/particles/particles.js"></script>
  <script>
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
	})
  </script>
  <script>
	  	var image = document.getElementsByClassName('image1');
		new simpleParallax(image, {
			scale: 1.5
		});

		particlesJS.load('particles-js', 'js/particles/particles-config.json', function() {
			console.log('callback - particles.js config loaded');
		});
  </script>
  <?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>