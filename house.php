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
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>YayStay</title>
      <!--детайли на къщите-->
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="css/style.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="css/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
      <!-- Font Awesome -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
      <!-- Custom CSS -->
      <style>
         .house-details {
            padding: 50px 0;
         }
         .house-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 30px;
         }
         .house-info {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
         }
         .amenities {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
         }
         .amenity-item {
            display: flex;
            align-items: center;
            gap: 10px;
         }
         .reservation-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
         }
         .price-info {
            margin: 20px 0;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
         }
         .contact-info {
            margin-top: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
         }
         .btn-reserve {
            background: #ff00aa;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
         }
         .btn-reserve:hover {
            background: #d6008f;
         }
         .reservation-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
         }
         .form-group {
            margin-bottom: 20px;
         }
         .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
         }
         .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
         }
         textarea.form-control {
            resize: vertical;
            min-height: 100px;
         }
         .btn-reserve {
            width: 100%;
            margin-top: 10px;
         }
         .payment-info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
         }
         #bank_details {
            margin-top: 10px;
            border-left: 4px solid #ff00aa;
         }
         #bank_details p {
            margin-bottom: 8px;
         }
         select.form-control {
            height: 45px;
         }
         .image-gallery {
            position: relative;
            margin-bottom: 30px;
         }
         .main-image-container {
            position: relative;
            width: 100%;
            height: 880px;
            overflow: hidden;
            border-radius: 2px;
         }
         .house-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease;
         }
         .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
         }
         .nav-btn:hover {
            background: rgba(255, 255, 255, 0.9);
         }
         .prev-btn {
            left: 10px;
         }
         .next-btn {
            right: 10px;
         }
         .thumbnail-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            justify-content: center;
         }
         .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            opacity: 0.6;
            transition: all 0.3s ease;
         }
         .thumbnail:hover {
            opacity: 0.8;
         }
         .thumbnail.active {
            opacity: 1;
            border: 2px solid #ff00aa;
         }
         @media (max-width: 768px) {
            .main-image-container {
               height: 600px;
            }
            .nav-btn {
               width: 35px;
               height: 35px;
            }
            .thumbnail {
               width: 60px;
               height: 45px;
            }
         }
      </style>
   </head>
   <body>
      <!-- loader -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
      <!-- header -->
      <header>
         
      </header>
      <main>
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

      <div class="container house-details">
         <div class="row">
            <div class="col-md-8">
               <div class="image-gallery">
                  <div class="main-image-container">
                     <img id="mainImage" src="<?= htmlspecialchars($house['image1_url']) ?>" alt="<?= htmlspecialchars($house['name']) ?>" class="house-image">
                     <button class="nav-btn prev-btn" onclick="changeImage(-1)">
                        <i class="fas fa-chevron-left"></i>
                     </button>
                     <button class="nav-btn next-btn" onclick="changeImage(1)">
                        <i class="fas fa-chevron-right"></i>
                     </button>
                  </div>
                  <div class="thumbnail-container">
                     <?php
                     $images = [
                        $house['image1_url'],
                        $house['image2_url'],
                        $house['image3_url']
                     ];
                     foreach($images as $index => $image):
                        if($image):
                     ?>
                        <img src="<?= htmlspecialchars($image) ?>" 
                             alt="Thumbnail <?= $index + 1 ?>" 
                             class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                             onclick="setImage(<?= $index ?>)">
                     <?php 
                        endif;
                     endforeach; 
                     ?>
                  </div>
               </div>
               
               <div class="house-info">
                  <h2><?= htmlspecialchars($house['name']) ?></h2>
                  <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($house['location']) ?></p>
                  
                  <div class="price-info">
                     <h4>Цени</h4>
                     <p><i class="fas fa-snowflake"></i> Зимен сезон: <?= htmlspecialchars($house['winter_price']) ?> лв.</p>
                     <p><i class="fas fa-sun"></i> Летен сезон: <?= htmlspecialchars($house['summer_price']) ?> лв.</p>
                  </div>

                  <h4>Удобства</h4>
                  <div class="amenities">
                     <?php foreach (explode(',', $house['amenities']) as $amenity): ?>
                     <div class="amenity-item">
                        <i class="fas fa-check"></i>
                        <span><?= htmlspecialchars(trim($amenity)) ?></span>
                     </div>
                     <?php endforeach; ?>
                  </div>
               </div>
            </div>

            <div class="col-md-4">
               <div class="reservation-form">
                  <h3>Резервация</h3>
                  <form id="reservationForm" method="POST">
                     <!-- Guest Information -->
                     <div class="form-group">
                        <label>Име и фамилия</label>
                        <input type="text" class="form-control" id="guest_name" name="guest_name" required>
                     </div>
                     <div class="form-group">
                        <label>Имейл</label>
                        <input type="email" class="form-control" id="guest_email" name="guest_email" required>
                     </div>
                     <div class="form-group">
                        <label>Телефон</label>
                        <input type="tel" class="form-control" id="guest_phone" name="guest_phone" required>
                     </div>
                     
                     <!-- Existing Date Fields -->
                     <div class="form-group">
                        <label>Настаняване</label>
                        <input type="date" class="form-control" id="checkin" name="checkin" required>
                     </div>
                     <div class="form-group">
                        <label>Напускане</label>
                        <input type="date" class="form-control" id="checkout" name="checkout" required>
                     </div>
                     <div class="form-group">
                        <label>Брой гости</label>
                        <input type="number" class="form-control" id="guests" name="guests" min="1" max="<?= $house['max_guests'] ?>" required>
                     </div>
                     
                     <!-- Additional Notes -->
                     <div class="form-group">
                        <label>Допълнителни бележки</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                     </div>
                     
                     <!-- Payment Method -->
                     <div class="form-group">
                        <label>Начин на плащане</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                           <option value="cash">В брой при настаняване</option>
                           <option value="card">Кредитна/Дебитна карта</option>
                        </select>
                     </div>
                     
                     <!-- Add this div that will show bank details when bank transfer is selected -->
                     <div id="bank_details" style="display: none;" class="form-group alert alert-info">
                        <h5>Банкова информация</h5>
                        <p><strong>Банка:</strong> YayStay Bank</p>
                        <p><strong>IBAN:</strong> BG00 XXXX 0000 0000 0000</p>
                        <p><strong>BIC:</strong> YAYSTBGSF</p>
                        <p class="mb-0"><small>Моля, използвайте името си като основание за превода</small></p>
                     </div>
                     
                     <button type="submit" class="btn-reserve">
                        <i class="fas fa-calendar-check"></i> Резервирай
                     </button>
                  </form>
               </div>

               <div class="contact-info">
                  <h4>Контакти</h4>
                  <?php if (!empty($house['phone'])): ?>
                     <p><i class="fas fa-phone"></i> <a href="tel:<?= htmlspecialchars($house['phone']) ?>"><?= htmlspecialchars($house['phone']) ?></a></p>
                  <?php endif; ?>
                  
                  <?php if (!empty($house['email'])): ?>
                     <p><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars($house['email']) ?>"><?= htmlspecialchars($house['email']) ?></a></p>
                  <?php endif; ?>
                  
                  <?php if (!empty($house['facebook'])): ?>
                     <p><i class="fab fa-facebook"></i> <a href="<?= htmlspecialchars($house['facebook']) ?>" target="_blank">Facebook страница</a></p>
                  <?php endif; ?>
                  
                  
                  <?php if (empty($house['phone']) && empty($house['email']) && empty($house['facebook'])): ?>
                     <p>За контакт, моля свържете се с нас чрез формата за контакт или ни потърсете на място:</p>
                     
                     <div class="col-md-6">
                     <div class="map-container">
   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1049.466479952089!2d26.828282559575555!3d43.71394684463045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40afa2b10a12b4f9%3A0x222c7c63425b9488!2z0YPQuy4g0JLQsNGB0LjQuyDQm9C10LLRgdC60LggNjUsIDc0MDAg0JjRgdC_0LXRgNC40YU!5e0!3m2!1sbg!2sbg!4v1739332585514!5m2!1sbg!2sbg" allowfullscreen="" loading="lazy"></iframe>
</div>
<style>
.map-container {
   width: 300px;
   max-width: 600px;
   margin: 0 auto;
   overflow: hidden;
}

.map-container iframe {
   width: 100%;
   height: 450px;
   border: 0;
   display: block;
   border-radius: 8px;
}
</style>


                     </div>
                  <?php endif; ?>
               </div>
         </div>
      </div>
      </main>
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
                            <li class="active"><a href="index.html">Начало</a></li>
                            <li><a href="about.html">За нас</a></li>
                            <li><a href="room.php">Къщи</a></li>
                            <li><a href="index.html">Блог</a></li>
                            <li><a href="contact.html">Контаки</a></li>
                        </ul>
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
         </div>
      </footer>
      
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <!-- sidebar -->
      <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
      <script src="js/custom.js"></script>
      <script>
         document.getElementById('reservationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
               house_id: <?= $house['id'] ?>,
               guest_name: document.getElementById('guest_name').value,
               guest_email: document.getElementById('guest_email').value,
               guest_phone: document.getElementById('guest_phone').value,
               checkin: document.getElementById('checkin').value,
               checkout: document.getElementById('checkout').value,
               guests: document.getElementById('guests').value,
               notes: document.getElementById('notes').value,
               payment_method: document.getElementById('payment_method').value
            };

            // AJAX request to handle reservation
            fetch('make_reservation.php', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json',
               },
               body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                  alert('Резервацията е успешна!') 
                  window.location.href = 'reservation_success.php';
               } else {
                  alert(data.message || 'Възникна грешка при резервацията.');
               }
            })
            .catch(error => {
               console.error('Error:', error);
               alert('Възникна грешка при обработката на резервацията.');
            });
         });

         // Add date validation
         document.getElementById('checkin').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            const checkoutInput = document.getElementById('checkout');
            
            // Set minimum checkout date to day after checkin
            const minCheckout = new Date(checkinDate);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkoutInput.min = minCheckout.toISOString().split('T')[0];
            
            // If current checkout date is before new minimum, update it
            if (checkoutInput.value && new Date(checkoutInput.value) < minCheckout) {
               checkoutInput.value = minCheckout.toISOString().split('T')[0];
            }
         });

         // Set minimum checkin date to today
         const today = new Date();
         document.getElementById('checkin').min = today.toISOString().split('T')[0];

         // Add event listener for payment method change
         document.getElementById('payment_method').addEventListener('change', function() {
            const bankDetails = document.getElementById('bank_details');
            if (this.value === 'bank_transfer') {
               bankDetails.style.display = 'block';
            } else {
               bankDetails.style.display = 'none';
            }
         });

         const images = [
            <?php 
            $validImages = array_filter([$house['image1_url'], $house['image2_url'], $house['image3_url']]);
            echo '"' . implode('","', array_map('htmlspecialchars', $validImages)) . '"';
            ?>
         ];

         let currentImageIndex = 0;
         const mainImage = document.getElementById('mainImage');
         const thumbnails = document.querySelectorAll('.thumbnail');

         function updateImage() {
            mainImage.style.opacity = '0';
            setTimeout(() => {
               mainImage.src = images[currentImageIndex];
               mainImage.style.opacity = '1';
               
               // Update thumbnails
               thumbnails.forEach((thumb, index) => {
                  if (index === currentImageIndex) {
                     thumb.classList.add('active');
                  } else {
                     thumb.classList.remove('active');
                  }
               });
            }, 200);
         }

         function changeImage(direction) {
            currentImageIndex += direction;
            if (currentImageIndex >= images.length) {
               currentImageIndex = 0;
            } else if (currentImageIndex < 0) {
               currentImageIndex = images.length - 1;
            }
            updateImage();
         }

         function setImage(index) {
            currentImageIndex = index;
            updateImage();
         }

         // Add keyboard navigation
         document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
               changeImage(-1);
            } else if (e.key === 'ArrowRight') {
               changeImage(1);
            }
         });
      </script>
   </body>
</html>
