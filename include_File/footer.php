<!-- Footer Section -->
<footer id="footer-section" class="sg-footer">

   <!-- Top CTA strip -->
   <div class="sg-footer-cta">
      <div class="container">
         <div class="row align-items-center">
            <div class="col-md-8 col-sm-12">
               <h5>Have any questions or want a free estimate?</h5>
               <h3 >Call Now: <span>+91-9891569725   </span>||<span>   01136803196</span></h3>
            </div>
            <div class="col-md-4 col-sm-12 text-md-right text-sm-left sg-footer-cta-right">
               <a href="tel:+919891569725" class="sg-footer-call-btn">
                  <i class="fa fa-phone"></i> Call Us Now
               </a>
            </div>
         </div>
      </div>
   </div>

   <!-- Main footer content -->
   <div class="sg-footer-main">
      <div class="container">
         <div class="row footer-widgets">

            <!-- Our Contacts (LEFT) -->
            <aside class="col-md-4 col-sm-6 widget">
               <h3 class="sg-footer-title">Our Contacts</h3>
               <ul class="sg-footer-list sg-footer-contact">
                  <li>
                     <span class="sg-footer-icon">
                        <i class="fa fa-clock-o"></i>
                     </span>
                     <div class="sg-footer-text">
                        <p>Monday – Friday</p>
                        <strong>10:00 am – 6:00 pm</strong>
                     </div>
                  </li>
                  <li>
                     <span class="sg-footer-icon">
                        <i class="fa fa-phone"></i>
                     </span>
                    <div class="sg-footer-text">
                     <p>24/7 Emergency Service</p>
                     <strong>
                        <a href="tel:+91 9310653788" class="sg-footer-phone">
                              +91 9310653788
                        </a>
                     </strong>
                     <strong>
                        <a href="tel:01136803196" class="sg-footer-phone">
                                0 1136803196
                        </a>
                     </strong>
                      </div>

                   </li>

                   <li>
                     <span class="sg-footer-icon">
                        <i class="fa fa-envelope"></i>
                     </span>

                     <div class="sg-footer-text">
                        <p>Mail Us Anytime</p>
                        <strong>
                           <a href="mailto:sgssservices@gmail.com" class="sg-footer-phone">
                                 sgssservices@gmail.com
                           </a>
                           <br>
                           <a href="mailto:sgssgemtender@gmail.com" class="sg-footer-phone">
                                 sgssgemtender@gmail.com
                           </a>
                        </strong>
                     </div>
                  </li>

               </ul>
            </aside>

            <!-- Page Links (MIDDLE) -->
            <aside class="col-md-3 col-sm-6 widget">
               <h3 class="sg-footer-title">Page Links</h3>
               <ul class="sg-footer-list sg-footer-links">
                  <li><a href="index.php"><i class="fas fa-shield-alt"></i> Home</a></li>
                  <li><a href="gallery.php"><i class="fas fa-shield-alt"></i> Gallery</a></li>
                  <li><a href="services.php"><i class="fas fa-shield-alt"></i> Services</a></li>
                  <li><a href="about.php"><i class="fas fa-shield-alt"></i> About Us</a></li>
                  <li><a href="contact.php"><i class="fas fa-shield-alt"></i> Contact Us</a></li>
                  <li><a href="companyprofile.php"><i class="fas fa-shield-alt"></i> Company Profile</a></li>
               </ul>
            </aside>

            <!-- Our Location / Map (RIGHT) -->
            <aside class="col-md-5 col-sm-12 widget">
               <h3 class="sg-footer-title">Our Location</h3>
               <div class="sg-footer-map">
                  <iframe
                     src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3503.2526196264057!2d77.08299857450831!3d28.592197385912996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1b458e9910bb%3A0xa2d25f51dd1bfc62!2sNasirpur%20Rd%2C%20Mangalpuri%20II%2C%20Palam%2C%20Delhi!5e0!3m2!1sen!2sin!4v1718972966627!5m2!1sen!2sin"
                     width="100%" height="230" style="border:0;" allowfullscreen=""
                     loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
               </div>
            </aside>

         </div>

         <!-- Bottom bar -->
         <div class="sg-footer-bottom">
            <p>2025 &copy; Super Global Sefty Security Services. Designed By
               <a href="https://codesparrow.org/" target="_blank">Code Sparrow</a>
            </p>
         </div>
      </div>
   </div>

   
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
var swiper = new Swiper(".mySwiper", {
    loop: true,
    autoplay: { delay: 3000 },
    pagination: { el: ".swiper-pagination", clickable: true },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.sg-hamburger');
    const mobileMenu = document.querySelector('.sg-mobile-menu');
    const overlay    = document.querySelector('.sg-mobile-overlay');
    const closeBtn   = document.querySelector('.sg-mobile-close');

    function openMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.add('is-open');
        if (overlay) overlay.classList.add('is-visible');
        document.body.classList.add('sg-menu-open');
    }

    function closeMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-visible');
        document.body.classList.remove('sg-menu-open');
    }

    if (hamburger) {
        hamburger.addEventListener('click', function (e) {
            e.preventDefault();
            openMenu();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeMenu();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMenu);
    }
});
</script>


</footer>
<!-- Footer Section /- -->
