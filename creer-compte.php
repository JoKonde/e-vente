<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

		<title>Electro - Connexion</title>

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
						<li><a href="connexion.php"><i class="fa fa-user"></i> Connexion</a></li>
						<li><a href="creer-compte.php"><i class="fa fa-user-o"></i> Créer Compte</a></li>
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

						

						
					</div>
					<!-- row -->
				</div>
				<!-- container -->
			</div>
			<!-- /MAIN HEADER -->
		</header>
		<!-- /HEADER -->

		

		

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row justify-content-center">
				<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center  h-100">
      <h2 class="text-dark">Nouveau Compte</h2>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
	  <?php
	  session_start();
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
        <form method="post" action="t2.php">
          

        <div class="mb-4">
                    <label class="form-label" for="signup-name">Nom Complet</label>
                    <input class="form-control" type="text" id="signup-name" name="noms" placeholder="Nom Complet..." required>
                  </div>
                  <div class="form-outline mb-4">
                    <label class="form-label mt-4" for="signup-name">Sexe</label>
                    <select name="sexe" id="sexe" class="form-select form-control mb-2">
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                    
                                    </select>
                  </div>
          <div class="form-outline mb-4">
          <label class="form-label" for="form3Example3">Email</label>
            <input type="email" id="form3Example3" name="email" class="form-control form-control-lg"
              placeholder="Votre adresse email" />
            
          </div>

          <!-- Password input -->
          <div class="form-outline">
          <label class="form-label" for="form3Example4">Mot de passe</label>
            <input type="password" id="form3Example4" name="password" class="form-control form-control-lg"
              placeholder="Votre mot de passe" />
            
          </div>

		  <br>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="submit" class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Créer compte</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">Vous avez compte? <a href="connexion.php"
                class="link-danger">Connectez-vous</a></p>
            <p class="small fw-bold mt-2 pt-1 mb-0"> <a href="index.php"
                class="link-primary"><i class="fa fa-home"></i> Accueil</a></p>
          </div>

        </form>
      </div>
    </div>
  </div>
  
</section>
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
