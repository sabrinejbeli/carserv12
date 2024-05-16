<?php
session_start();
include('includes/config.php');
error_reporting(0);

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    // Récupérer les données du formulaire
    $productId = $_POST['product_id']; // ID du produit
    $userId = $_SESSION['user_id']; // ID de l'utilisateur (vous devez gérer l'authentification des utilisateurs)
    $quantity = 1; // Quantité par défaut, vous pouvez la modifier selon vos besoins

    // Vérifier si le produit est déjà dans le panier
    $sql_check = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $query_check = $dbh->prepare($sql_check);
    $query_check->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $query_check->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $query_check->execute();
    $count = $query_check->rowCount();

    if ($count > 0) {
        // Mettre à jour la quantité du produit dans le panier
        $sql_update = "UPDATE cart SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $query_update = $dbh->prepare($sql_update);
        $query_update->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query_update->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $query_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $query_update->execute();
    } else {
        // Insérer le produit dans le panier s'il n'existe pas encore
        $sql_insert = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $query_insert = $dbh->prepare($sql_insert);
        $query_insert->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query_insert->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $query_insert->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $query_insert->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<?php
include "s.php";
?>
</head>
<body>
 <!-- Spinner Start -->
 <?php
include "d.php";
?>

<section class="section-padding gray-bg" style="margin-top: -100px;">
    <div class="container">
        <div class="section-header text-center">
            <h2>Trouvez la meilleure <span>adresse de vendre de voiture</span></h2>
            <p>Carserv facilite la recherche de voitures de luxe en vente à des prix abordables. Notre plateforme offre une sélection variée de véhicules haut de gamme pour répondre à tous les besoins et à tous les budgets.</p>
        </div>
        <div class="row">
            <!-- Nav tabs -->
            <div class="recent-tab">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#resentnewcar" role="tab" data-toggle="tab">Location</a></li>
                </ul>
            </div>
            <!-- Recently Listed New Cars -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="resentnewcar">
                    <?php
                    $sql = "SELECT tblvehiclesvendre.VehiclesTitle,tblbrands.BrandName,tblvehiclesvendre.prix,tblvehiclesvendre.FuelType,tblvehiclesvendre.ModelYear,tblvehiclesvendre.id,tblvehiclesvendre.SeatingCapacity,tblvehiclesvendre.VehiclesOverview,tblvehiclesvendre.Vimage1 FROM tblvehiclesvendre JOIN tblbrands ON tblbrands.id=tblvehiclesvendre.VehiclesBrand";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    if ($query->rowCount() > 0) {
                        foreach ($results as $result) {
                            ?>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <div class="col-list-3">
                                    <div class="recent-car-list">
                                        <div class="car-info-box">
                                            <a href="details.php?vhid=<?php echo htmlentities($result->id); ?>">
                                                <img src="../admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" class="img-responsive" alt="image">
                                            </a>
                                            <ul>
                                                <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType); ?></li>
                                                <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear); ?> Model</li>
                                                <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity); ?> seats</li>
                                            </ul>
                                        </div>
                                        <div class="car-title-m">
                                            <h6><a href="details.php?vhid=<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->BrandName); ?>, <?php echo htmlentities($result->VehiclesTitle); ?></a></h6>
                                            <span class="price"><?php echo htmlentities($result->prix); ?>DT /Jour</span>
                                        </div>
                                        <div class="inventory_info_m">
                                            <p><?php echo substr($result->VehiclesOverview, 0, 70); ?></p>
                                            <!-- Ajoutez ici les détails du produit, par exemple : -->
                                            <input type="hidden" name="product_id" value="<?php echo $result->id; ?>">
                                            <!-- Ajoutez d'autres champs du produit ici -->
                                            <!-- Bouton "Ajouter au panier" -->
                                            <button type="submit" name="add_to_cart"><i class="fa fa-shopping-cart"></i> Ajouter au panier</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h4 class="text-light mb-4">Adresse</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Manouba, khair eddine</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+216 51 423 614</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>Carserv@gmail.com</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-light mb-4">Horaires d'ouvertures</h4>
                <h6 class="text-light">Lundi - Vednredi:</h6>
                <p class="mb-4">09.00 AM - 09.00 PM</p>
                <h6 class="text-light">Samedi - Dimanche:</h6>
                <p class="mb-0">Fermé</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-light mb-4">Services</h4>
                <a class="btn btn-link" href="">Location</a>
                <a class="btn btn-link" href="">Vendre</a>

            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-light mb-4">Newsletter</h4>
                <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
                <div class="position-relative mx-auto" style="max-width: 400px;">
                    <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                    <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">CarServ</a>, Tous droits réservés.

                    <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                    Droit d'auteur par <a class="border-bottom" href="https://htmlcodex.com">JBELI Sabri</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="">Acceuil</a>
                        <a href="">A propos</a>
                        <a href="">Services</a>
                        <a href="">Cpntact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/counterup/counterup.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/tempusdominus/js/moment.min.js"></script>
<script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="js/main.js"></script>

</body>
</html>
