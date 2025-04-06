<?php
require 'db.php';
$stmt = $pdo->query("
    SELECT h.*, GROUP_CONCAT(a.name) as amenities 
    FROM houses h 
    LEFT JOIN house_amenities ha ON h.id = ha.house_id 
    LEFT JOIN amenities a ON ha.amenity_id = a.id 
    GROUP BY h.id
");
$houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>YayStay</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="css/style.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="css/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
   </head>
   <body>
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
      <header>
         <div class="header">
            <div class="container">
               <div class="row">
                  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                     <div class="full">
                        <div class="center-desk">
                        <div class="logo">
                              <a href="index.html"><img src="images/LOGOTO2.png" alt="#" /></a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                     <nav class="navigation navbar navbar-expand-md navbar-dark">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                           <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarsExample04">
                           <ul class="navbar-nav mr-auto">
                              <li class="nav-item">
                                 <a class="nav-link" href="index.html">Начало</a>
                              </li>
                                 <li class="nav-item">
                                 <a class="nav-link" href="about.html">За нас</a>
                              </li>
                              <li class="nav-item active">
                                 <a class="nav-link" href="room.php">Къщи</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" href="contact.html">Контакти</a>
                              </li>
                           </ul>
                        </div>
                     </nav>
                  </div>
               </div>
            </div>
         </div>
      </header>
      <div class="back_re">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="title">
                     <h2>Къщи</h2>
                  </div>
               </div>
            </div>
         </div>
      </div>
     
      <div class="our_room">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <p class="margin_0"></p>
                  </div>
               </div>
            </div>
            <div class="row">
               <?php foreach ($houses as $house): ?>
               <div class="col-md-3 col-sm-6">
                  <a href="house.php?id=<?= $house['id'] ?>">
                     <div id="serv_hover" class="room">
                        <div class="room_img">
                           <figure><img src="<?= htmlspecialchars($house['image_url'] ?? 'images/default.jpg') ?>" alt="<?= htmlspecialchars($house['name']) ?>"/></figure>
                        </div>
                        <div class="bed_room">
                           <h3><?= htmlspecialchars($house['name']) ?></h3>
                           <p><?= htmlspecialchars($house['location']) ?></p>
                        </div>
                     </div>
                  </a>
               </div>
               <?php endforeach; ?>
            </div>
         </div>
      </div>
      <!-- end our_room -->
      <!-- footer -->
      <footer>
         <div class="footer">
            <div class="container">
               <div class="row">
                  <div class="col-md-4">
                     <h3>Контакти</h3>
                     <ul class="conta">
                        <li><i class="fa fa-map-marker" aria-hidden="true"></i>
                           <a href="https://maps.app.goo.gl/NLft9sbNBYc4vfML8" target="_blank">Адрес</a>
                        </li>
                        <li><i class="fa fa-mobile" aria-hidden="true"></i>
                           <a href="tel:+359889538887">+359 889538887</a>
                        </li>
                        <li><i class="fa fa-envelope" aria-hidden="true"></i>
                           <a href="mailto:d.g.2006@abv.bg">d.g.2006@abv.bg</a>
                        </li>
                     </ul>
                     </div>
                  <div class="col-md-4">
                     <h3>Меню</h3>
                     <ul class="link_menu">
                        <li class="active"><a href="#">Начало</a></li>
                        <li><a href="about.html"> За нас</a></li>
                        <li><a href="room.php">Къщи</a></li>
                        <li><a href="index.html">Блог</a></li>
                        <li><a href="contact.html">Контаки</a></li>
                     </ul>
                  </div>
                  <div class="col-md-4">
                     <h3>Електронен бюлетин</h3>
                     <form class="bottom_form">
                        <input class="enter" placeholder="Въведете имейл" type="text" name="Въведете имейл">
                        <button class="sub_btn">Абониране</button>
                  </div>
               </div>
               </div>
            <div class="copyright">
               <div class="container">
                  <div class="row">
                     <div class="col-md-10 offset-md-1">
                        
                        <p>
                        Design by <a href="https://teams.live.com/v2/?tenantId=9188040d-6c67-4c5b-b112-36a304b66dad&login_hint=359889538887"> Dariya Stoyanova</a>
                        <br><br>
                        <a href="" target="_blank"></a>
                        </p>

                     </div>
                  </div>
            </div>
         </div>
      </footer>
      
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <!-- sidebar -->
      <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
      <script src="js/custom.js"></script>
   </body>
</html>