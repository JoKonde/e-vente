<!DOCTYPE html>
<?php
session_start();
require_once 'Database.php';
require_once 'User.php';
require_once 'Role.php';
require_once 'Categorie.php';
require_once 'Produit.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$role = new Role($db);
$categorie = new Categorie($db);
$produit = new Produit($db);

//on cherche si les roles existent deja sinon on le cree
$listRole = $role->read();
if (count($listRole) <= 0) {
  $role->nom = "Admin";
  $role->create();
  $role->nom = "Client";
  $role->create();
}
$listUser = $user->read();
if (count($listUser) <= 0) {
  $user->noms = 'AMON POULET';
  $user->sexe = 'M';
  $user->email = 'amon@gmail.com';
  $user->password = '123456789';
  $user->role_id = 1; // Admin
  $user->create();
  
}
$listCategorie=$categorie->read();
if (count($listCategorie) <= 0) {
	$categorie->nom = 'ORDINATEUR PORTABLE';
	$categorie->create();
	$categorie->nom = 'SMARTPHONE';
	$categorie->create();
	$categorie->nom = 'ACCESSOIRES ELECTRONIQUES';
	$categorie->create();
	
  }
$listProduits=$produit->read();
if (count($listProduits) <= 0) {
	$produit->nom = 'DELL SPOK';
	$produit->prix = 150;
	$produit->image = 'product01.png';
	$produit->categorie_id = 1;
	$produit->create();
	$produit->nom = 'HP PROBOOK';
	$produit->prix = 178;
	$produit->image = 'product03.png';
	$produit->categorie_id = 1;
	$produit->create();
	$produit->nom = 'TABLETTE SAMSUNG A 99';
	$produit->categorie_id = 2;
	$produit->prix = 120;
	$produit->image = 'product04.png';
	$produit->create();
	$produit->nom = 'CASQUES DELL';
	$produit->categorie_id = 3;
	$produit->prix = 75;
	$produit->image = 'product02.png';
	$produit->create();
	$produit->nom = 'CASQUES POOL';
	$produit->categorie_id = 3;
	$produit->prix = 45;
	$produit->image = 'product05.png';
	$produit->create();
	$produit->nom = 'TECHNO POP 12';
	$produit->categorie_id = 2;
	$produit->prix = 89;
	$produit->image = 'product07.png';
	$produit->create();
	$produit->nom = 'APPAREIL PHOTO KANON';
	$produit->categorie_id = 3;
	$produit->prix = 450;
	$produit->image = 'product09.png';
	$produit->create();
	
  }
  //est ce qu'il est connecté?
  $isLoggedIn = isset($_SESSION['idUser']) ? true : false;
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

		<title>Electro - Acceuil</title>

		<!-- Google font -->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

		<!-- Bootstrap -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>

		<!-- Slick -->
		<link type="text/css" rel="stylesheet" href="css/slick.css"/>
		<link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>

		<!-- nouislider -->
		<link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>

		<!-- Font Awesome Icon -->
		<link rel="stylesheet" href="css/font-awesome.min.css">

		<!-- Custom stlylesheet -->
		<link type="text/css" rel="stylesheet" href="css/style.css"/>

		

    </head>
	<body>
		<!-- HEADER -->
		<header>
			<!-- TOP HEADER -->
			<div id="top-header">
				<div class="container">
					
					<ul class="header-links pull-right">
						<?php 
						if(isset($_SESSION['email'])){
							if($_SESSION['email']){?>
								<li class="text-light"><a href="#"><i class="fa fa-user"></i><?php echo $_SESSION['noms'] ?> </a></li>
								<li><a href="deconnexion.php"><i class="fa fa-sign-out"></i> Deconnexion</a></li>
								<?php	}
						}else{?>
							<li><a href="connexion.php"><i class="fa fa-user"></i> Connexion</a></li>
							<li><a href="creer-compte.php"><i class="fa fa-user-o"></i> Créer Compte</a></li>
							<?php }

						?>
						
					</ul>
				</div>
			</div>
			<!-- /TOP HEADER -->

			<!-- MAIN HEADER -->
			<div id="header">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<!-- LOGO -->
						<div class="col-md-3">
							<div class="header-logo">
								<a href="index.php" class="logo">
									<img src="./img/logo.png" alt="">
								</a>
							</div>
						</div>
						<!-- /LOGO -->

						<!-- SEARCH BAR -->
						<div class="col-md-6">
							<div class="header-search">
								<form>
									<select class="input-select">
										<option value="0">Categories</option>
										<option value="1">Categorie 1</option>
										<option value="1">Categorie 2</option>
									</select>
									<input class="input" placeholder="Recherche...">
									<button class="search-btn">Chercher</button>
								</form>
							</div>
						</div>
						<!-- /SEARCH BAR -->

						<!-- ACCOUNT -->
						<div class="col-md-3 clearfix">
							<div class="header-ctn">
								

								<!-- Cart -->
								<div class="dropdown">
									<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
										<i class="fa fa-shopping-cart"></i>
										<span>Panier</span>
										<div class="qty"><?php 
										$totalQte = 0;
										
										if (isset($_SESSION['panier'])) {
											foreach ($_SESSION['panier'] as $p) {
												$totalQte += $p['qte'];
											}
										}
										echo $totalQte;
										
										?>
										</div>
									</a>
									<div class="cart-dropdown">
										<div class="cart-list">
											<div class="product-widget">
												<div class="product-img">
													<img src="./img/product01.png" alt="">
												</div>
												<div class="product-body">
													<h3 class="product-name"><a href="#">product name goes here</a></h3>
													<h4 class="product-price"><span class="qty">1x</span>$980.00</h4>
												</div>
												<button class="delete"><i class="fa fa-close"></i></button>
											</div>

											<div class="product-widget">
												<div class="product-img">
													<img src="./img/product02.png" alt="">
												</div>
												<div class="product-body">
													<h3 class="product-name"><a href="#">product name goes here</a></h3>
													<h4 class="product-price"><span class="qty">3x</span>$980.00</h4>
												</div>
												<button class="delete"><i class="fa fa-close"></i></button>
											</div>
										</div>
										<div class="cart-summary">
											<small>3 Item(s) selected</small>
											<h5>SUBTOTAL: $2940.00</h5>
										</div>
										<div class="cart-btns">
											<a href="#">View Cart</a>
											<a href="#">Checkout  <i class="fa fa-arrow-circle-right"></i></a>
										</div>
									</div>
								</div>
								<!-- /Cart -->

								<!-- Menu Toogle -->
								<div class="menu-toggle">
									<a href="#">
										<i class="fa fa-bars"></i>
										<span>Menu</span>
									</a>
								</div>
								<!-- /Menu Toogle -->
							</div>
						</div>
						<!-- /ACCOUNT -->
					</div>
					<!-- row -->
				</div>
				<!-- container -->
			</div>
			<!-- /MAIN HEADER -->
		</header>
		<!-- /HEADER -->

		<!-- NAVIGATION -->
		<nav id="navigation">
			<!-- container -->
			<div class="container">
				<!-- responsive-nav -->
				<div id="responsive-nav">
					<!-- NAV -->
					<ul class="main-nav nav navbar-nav">
						<li class="active"><a href="#">Acceuil</a></li>
						
					</ul>
					<!-- /NAV -->
				</div>
				<!-- /responsive-nav -->
			</div>
			<!-- /container -->
		</nav>
		<!-- /NAVIGATION -->

		

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
				<?php
                  if (isset($_SESSION['msg'])) {
                    echo "<p class='alert alert-danger'>" . $_SESSION['msg'] . "</p>";
                    // Supprimer le message d'erreur après l'affichage
                    unset($_SESSION['msg']);
                  }
                  if (isset($_SESSION['msgSuc'])) {
                    echo "<p class='alert alert-success'>" . $_SESSION['msgSuc'] . "</p>";
                    // Supprimer le message d'erreur après l'affichage
                    unset($_SESSION['msgSuc']);
                  }
                  ?>
					<!-- section title -->
					<div class="col-md-12">
						<div class="section-title">
							<h3 class="title">Nos Produits</h3>
							
						</div>
					</div>
					<!-- /section title -->

					<!-- Products tab & slick -->
					<div class="col-md-12">
						<div class="row">
							<div class="products-tabs">
								<!-- tab -->
								<div id="tab1" class="tab-pane active">
									<div class="products-slick" data-nav="#slick-nav-1">
										
									<?php 
									$listProduits=$produit->read();
									foreach ($listProduits as $p) {
										$cat = $categorie->findById($p['categorie_id']);
									  ?>
									<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/<?php echo $p['image'] ?>" alt="">
												<div class="product-label">
													
												</div>
											</div>
											<div class="product-body">
												<p class="product-category"><?php echo $cat->nom ?></p>
												<h3 class="product-name"><a href="#"><?php echo $p['nom'] ?></a></h3>
												<h4 class="product-price">$<?php echo $p['prix'] ?> <del class="product-old-price">$<?php echo $p['prix']+($p['prix']*0.3) ?></del></h4>
												<div class="product-rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">Favoris</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">Comparer</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">Voir</span></button>
												</div>
											</div>
											<div class="add-to-cart">
											<form <?php if ($isLoggedIn) { echo "action='t3.php'"; } ?> method="post">

													<input type="hidden" name="nom" value="<?php echo $p['nom'] ?>">
													<input type="hidden" name="prix" value="<?php echo $p['prix'] ?>">
													<input type="hidden" name="image" value="<?php echo $p['image'] ?>">
													<input type="hidden" name="qte" value=1>
													<input type="hidden" name="id" value="<?php echo $p['id'] ?>">
													<?php 
															if($isLoggedIn){
																?>
																<button class="add-to-cart-btn"  name="ajouterAuPanier"><i class="fa fa-shopping-cart"></i> Ajouter au Panier</button>
																<?php 	}else{ ?>
																	<button class="add-to-cart-btn"  onclick="alert('Veuillez d\'abord vous connecter ou si vous n\'avez pas encore un compte, créez-en un.');"><i class="fa fa-shopping-cart"></i> Ajouter au Panier</button>
																	<?php  }?>
												
												</form>
												
												
											</div>
										</div>
										<!-- /product -->
										<?php } ?>
									</div>
									<div id="slick-nav-1" class="products-slick-nav"></div>
								</div>
								<!-- /tab -->
							</div>
						</div>
					</div>
					<!-- Products tab & slick -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

		

		

		

		<!-- FOOTER -->
		<footer id="footer">
			

			<!-- bottom footer -->
			<div id="bottom-footer" class="section">
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-12 text-center">
							<ul class="footer-payments">
								<li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
								<li><a href="#"><i class="fa fa-credit-card"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
							</ul>
							<span class="copyright">
								<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
								Copyright &copy;<script>document.write(new Date().getFullYear());</script> Tous les droits reservés.
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
							</span>


						</div>
					</div>
						<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /bottom footer -->
		</footer>
		<!-- /FOOTER -->

		<!-- jQuery Plugins -->
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/slick.min.js"></script>
		<script src="js/nouislider.min.js"></script>
		<script src="js/jquery.zoom.min.js"></script>
		<script src="js/main.js"></script>

	</body>
</html>
