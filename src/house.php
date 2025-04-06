<?php
require 'db.php';

$house_id = $_GET['id'] ?? null;
if (!$house_id) {
    header('Location: room.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT h.*, GROUP_CONCAT(a.name) as amenities 
    FROM houses h 
    LEFT JOIN house_amenities ha ON h.id = ha.house_id 
    LEFT JOIN amenities a ON ha.amenity_id = a.id 
    WHERE h.id = ?
    GROUP BY h.id
");
$stmt->execute([$house_id]);
$house = $stmt->fetch(PDO::FETCH_ASSOC);

$current_month = (int)date('m');
$price = ($current_month >= 6 && $current_month <= 8) ? $house['summer_price'] : $house['winter_price'];

if (!$house) {
    header('Location: room.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <title><?= htmlspecialchars($house['name']) ?></title>
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
      <!-- loader -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
      <!-- header -->
      <header>
         
      </header>
      
      <div class="back_re">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="title">
                     <h2><?= htmlspecialchars($house['name']) ?></h2>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="our_room">
         <div class="container">
            <div class="row">
               <div class="col-md-6">
                  <div id="serv_hover" class="room">
                     <div class="room_img">
                        <div id="main_slider" class="carousel slide" data-ride="carousel">
                           <div class="carousel-inner">
                              <?php if ($house['image1_url']): ?>
                              <div class="carousel-item active">
                                 <img src="<?= htmlspecialchars($house['image1_url']) ?>" alt="#"/>
                              </div>
                              <?php endif; ?>
                              <?php if ($house['image2_url']): ?>
                              <div class="carousel-item">
                                 <img src="<?= htmlspecialchars($house['image2_url']) ?>" alt="#"/>
                              </div>
                              <?php endif; ?>
                              <?php if ($house['image3_url']): ?>
                              <div class="carousel-item">
                                 <img src="<?= htmlspecialchars($house['image3_url']) ?>" alt="#"/>
                              </div>
                              <?php endif; ?>
                           </div>
                           <a class="carousel-control-prev" href="#main_slider" role="button" data-slide="prev">
                              <i class="fa fa-angle-left"></i>
                           </a>
                           <a class="carousel-control-next" href="#main_slider" role="button" data-slide="next">
                              <i class="fa fa-angle-right"></i>
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="bed_room">
                     <h3><?= htmlspecialchars($house['name']) ?></h3>
                     <p><strong>Локация:</strong> <?= htmlspecialchars($house['location']) ?></p>
                     <p><strong>Цена - зимен сезон:</strong> <?= htmlspecialchars($house['winter_price']) ?> лв</p>
                     <p><strong>Цена - летен сезон:</strong> <?= htmlspecialchars($house['summer_price']) ?> лв</p>
                     <p><strong>Максимален брой гости:</strong> <?= htmlspecialchars($house['max_guests']) ?></p>
                     
                     <?php if (!empty($house['amenities'])): ?>
                     <div class="amenities">
                        <h4>Удобства:</h4>
                        <ul>
                           <?php foreach (explode(',', $house['amenities']) as $amenity): ?>
                           <li><?= htmlspecialchars(trim($amenity)) ?></li>
                           <?php endforeach; ?>
                        </ul>
                     </div>
                     <?php endif; ?>

                     <div class="reservation-form">
                        <h4>Направи резервация</h4>
                        <form action="make_reservation.php" method="post" id="reservationForm">
                           <input type="hidden" name="house_id" value="<?= $house['id'] ?>">
                           <input type="hidden" name="total_price" value="0">
                           
                           <div class="form-group">
                              <label>Име и фамилия:</label>
                              <input type="text" name="guest_name" class="form-control" required>
                           </div>

                           <div class="form-group">
                              <label>Имейл:</label>
                              <input type="email" name="guest_email" class="form-control" required>
                           </div>

                           <div class="form-group">
                              <label>Телефон:</label>
                              <input type="tel" name="guest_phone" class="form-control" required>
                           </div>

                           <div class="form-group">
                              <label>Дата на настаняване:</label>
                              <input type="date" name="checkin" class="form-control" required 
                                     min="<?= date('Y-m-d') ?>" onchange="calculateTotal()">
                           </div>

                           <div class="form-group">
                              <label>Дата на напускане:</label>
                              <input type="date" name="checkout" class="form-control" required 
                                     min="<?= date('Y-m-d') ?>" onchange="calculateTotal()">
                           </div>

                           <div class="form-group">
                              <label>Брой гости:</label>
                              <select name="guests" class="form-control" required onchange="calculateTotal()">
                                 <?php for($i = 1; $i <= $house['max_guests']; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                 <?php endfor; ?>
                              </select>
                           </div>

                           <div class="form-group">
                              <label>Начин на плащане:</label>
                              <select name="payment_method" class="form-control" required>
                                 <option value="bank">Банков превод</option>
                                 <option value="cash">В брой при настаняване</option>
                              </select>
                           </div>

                           <div class="price-info">
                              <p>Цена на нощувка: <span class="price-per-night"><?= $price ?> лв.</span></p>
                              <p>Общо нощувки: <span id="totalNights">0</span></p>
                              <p>Обща сума: <span id="totalPrice">0</span> лв.</p>
                           </div>

                           <button type="submit" class="btn btn-primary">Резервирай</button>
                        </form>
                     </div>

                     <script>
                     function calculateTotal() {
                         const checkin = new Date(document.querySelector('[name="checkin"]').value);
                         const checkout = new Date(document.querySelector('[name="checkout"]').value);
                         const pricePerNight = <?= $price ?>;

                         if (checkin && checkout && checkout > checkin) {
                             const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                             const total = nights * pricePerNight;
                             
                             document.getElementById('totalNights').textContent = nights;
                             document.getElementById('totalPrice').textContent = total;
                         }
                     }

                     document.getElementById('reservationForm').onsubmit = function(e) {
                         const checkin = new Date(document.querySelector('[name="checkin"]').value);
                         const checkout = new Date(document.querySelector('[name="checkout"]').value);
                         const guests = parseInt(document.querySelector('[name="guests"]').value);
                         const maxGuests = <?= $house['max_guests'] ?>;

                         if (checkout <= checkin) {
                             alert('Датата на напускане трябва да бъде след датата на настаняване!');
                             e.preventDefault();
                             return false;
                         }

                         if (guests > maxGuests) {
                             alert('Максималният брой гости е ' + maxGuests + '!');
                             e.preventDefault();
                             return false;
                         }

                         return true;
                     };

                     document.getElementById('reservationForm').addEventListener('submit', function(e) {
                         e.preventDefault();
                         
                         const formData = new FormData(this);
                         
                         fetch('make_reservation.php', {
                             method: 'POST',
                             body: formData
                         })
                         .then(response => response.json())
                         .then(data => {
                             if (data.status === 'error') {
                                 let message = data.message;
                                 
                                 if (data.busyDates) {
                                     message += '\n\nДоли дати:\n';
                                     data.busyDates.forEach(date => {
                                         message += `${date.start} - ${date.end}\n`;
                                     });
                                     
                                     if (data.suggestion) {
                                         message += `\n${data.suggestion}`;
                                     }
                                 }
                                 
                                 Swal.fire({
                                     title: 'Грешка!',
                                     text: message,
                                     icon: 'error',
                                     confirmButtonText: 'OK'
                                 });
                             } else {
                                 window.location.href = 'reservation_success.php?id=' + data.reservation_id;
                             }
                         })
                         .catch(error => {
                             console.error('Error:', error);
                             alert('Възникна грешка. Моля, опитайте отново.');
                         });
                     });
                     </script>

                     <script>
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true; 
    
    const formData = new FormData(this);
    
    fetch('make_reservation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'reservation_success.php?id=' + data.reservation_id;
        } else {
            submitButton.disabled = false; 
            Swal.fire({
                title: 'Грешка!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        submitButton.disabled = false;
        console.error('Error:', error);
        Swal.fire({
            title: 'Грешка!',
            text: 'Възникна грешка при обработката на заявката',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
</script>

                     <script>
                     document.getElementById('reservationForm').addEventListener('submit', function(e) {
                         e.preventDefault();
                         
                         const formData = new FormData(this);
                         
                         fetch('make_reservation.php', {
                             method: 'POST',
                             body: formData
                         })
                         .then(response => response.json())
                         .then(data => {
                             if (data.status === 'success') {
                                 window.location.href = 'reservation_success.php?id=' + data.reservation_id;
                             } else {
                                 Swal.fire({
                                     title: 'Hata!',
                                     text: data.message,
                                     icon: 'error',
                                     confirmButtonText: 'Tamam'
                                 });
                             }
                         })
                         .catch(error => {
                             console.error('Error:', error);
                             Swal.fire({
                                 title: 'Hata!',
                                 text: 'Bir hata oluştu, lütfen tekrar deneyin.',
                                 icon: 'error',
                                 confirmButtonText: 'Tamam'
                             });
                         });
                     });
                     </script>

                     <script>
                     function calculateTotal() {
                         const checkin = new Date(document.querySelector('[name="checkin"]').value);
                         const checkout = new Date(document.querySelector('[name="checkout"]').value);
                         const guests = parseInt(document.querySelector('[name="guests"]').value);

                         if (checkin && checkout && !isNaN(checkin) && !isNaN(checkout)) {
                             
                             const timeDiff = Math.abs(checkout.getTime() - checkin.getTime());
                             const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                             
                             const month = checkin.getMonth() + 1;
                             const price = (month >= 6 && month <= 8) ? <?= $house['summer_price'] ?> : <?= $house['winter_price'] ?>;
                             
                             const total = nights * price;
                             
                             document.getElementById('totalNights').textContent = nights;
                             document.getElementById('totalPrice').textContent = total;
                             document.querySelector('input[name="total_price"]').value = total;
                         }
                     }

                     document.addEventListener('DOMContentLoaded', function() {
                         const inputs = document.querySelectorAll('input[name="checkin"], input[name="checkout"], select[name="guests"]');
                         inputs.forEach(input => input.addEventListener('change', calculateTotal));
                     });
                     </script>

                     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                     <style>
                     .reservation-form {
                         background: #f9f9f9;
                         padding: 20px;
                         border-radius: 5px;
                         margin-top: 20px;
                     }
                     .form-group {
                         margin-bottom: 15px;
                     }
                     .form-control {
                         width: 100%;
                         padding: 8px;
                         border: 1px solid #ddd;
                         border-radius: 4px;
                     }
                     .price-info {
                         margin: 20px 0;
                         padding: 15px;
                         background: #fff;
                         border-radius: 4px;
                     }
                     </style>
                  </div>
               </div>
            </div>
         </div>
      </div>

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
