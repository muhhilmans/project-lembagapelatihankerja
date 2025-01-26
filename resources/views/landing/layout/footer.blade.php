<footer class="footer__section position-relative">
    <div class="container">
        {{-- <div class="newsletter-section">
          <h3 class="title subcribe-title wow fadeInDown" data-wow-delay="0.4s">
             Subscribe to Our Newsletter
          </h3>
          <form action="#">
             <span class="fz-16 title d-block fw-700 prafont">
                Enter your email
             </span>
             <input type="text">
             <button type="button" class="cmn--btn">
                <span>
                   Explore Our Offers
                </span>
             </button>
          </form>
       </div> --}}
        <div class="footer__top pb-120">
            <div class="row g-4">
                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-6 wow fadeInUp" data-wow-duration="2.1s">
                    <div class="footer__item">
                        <a href="index.html" class="footer-logo">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="img"
                                style="max-height: 100px; width: auto;">
                        </a>
                        <p class="prag">
                            Platform Penyalur ART terbaik di Jabodetabek melalui layanan yang cepat, transparan, dan
                            terpercaya.
                        </p>
                        <ul class="footer-social d-flex align-items-center">
                            <li>
                                <a href="https://youtube.com/@sipembantu?si=h4Jw2PZv3ykJyvl9" target="_blank">
                                    <svg width="25" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                        <path
                                            d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="http://instagram.com/si.pembantu" target="_blank">
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18.8571 0H6.85714C3.49714 0 0.857143 2.64 0.857143 6V18C0.857143 21.36 3.49714 24 6.85714 24H18.8571C22.2171 24 24.8571 21.36 24.8571 18V6C24.8571 2.64 22.2171 0 18.8571 0ZM22.4571 18C22.4571 20.04 20.8971 21.6 18.8571 21.6H6.85714C4.81714 21.6 3.25714 20.04 3.25714 18V6C3.25714 3.96 4.81714 2.4 6.85714 2.4H18.8571C20.8971 2.4 22.4571 3.96 22.4571 6V18Z"
                                            fill="#032B52" />
                                        <path
                                            d="M12.8571 6C9.49714 6 6.85714 8.64 6.85714 12C6.85714 15.36 9.49714 18 12.8571 18C16.2171 18 18.8571 15.36 18.8571 12C18.8571 8.64 16.2171 6 12.8571 6ZM12.8571 15.6C10.8171 15.6 9.25714 14.04 9.25714 12C9.25714 9.96 10.8171 8.4 12.8571 8.4C14.8971 8.4 16.4571 9.96 16.4571 12C16.4571 14.04 14.8971 15.6 12.8571 15.6Z"
                                            fill="#032B52" />
                                        <path
                                            d="M18.8566 7.1998C19.5194 7.1998 20.0566 6.66255 20.0566 5.9998C20.0566 5.33706 19.5194 4.7998 18.8566 4.7998C18.1939 4.7998 17.6566 5.33706 17.6566 5.9998C17.6566 6.66255 18.1939 7.1998 18.8566 7.1998Z"
                                            fill="#032B52" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.tiktok.com/@sipembantu.com" target="_blank">
                                    <svg width="25" height="24" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512">
                                        <path
                                            d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 wow fadeInUp" data-wow-duration="2.3s">
                    <div class="footer__item">
                        <h4 class="footer__title">
                            Quick Links
                        </h4>
                        <ul class="clink">
                            <li>
                                <a href="#about">
                                    Tentang Kami
                                </a>
                            </li>
                            <li>
                                <a href="#service">
                                    Servis
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('all-blogs') }}">
                                    Blog
                                </a>
                            </li>
                            <li>
                                <a href="#contact">
                                    Kontak Kami
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-3 col-md-6 col-sm-6 wow fadeInUp" data-wow-duration="2.2s">
                    <div class="footer__item">
                        <h5 class="footer__title">
                            Kontak Kami
                        </h5>
                        <ul class="get__touch">
                            <li>
                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.84091 12.25L0.5 14.875V1C0.5 0.58579 0.83579 0.25 1.25 0.25H14.75C15.1642 0.25 15.5 0.58579 15.5 1V11.5C15.5 11.9142 15.1642 12.25 14.75 12.25H3.84091ZM3.32211 10.75H14V1.75H2V11.7888L3.32211 10.75ZM7.25 5.5H8.75V7H7.25V5.5ZM4.25 5.5H5.75V7H4.25V5.5ZM10.25 5.5H11.75V7H10.25V5.5Z"
                                        fill="#2295FF" />
                                </svg>
                                <a href="mailto:info@sipembantu.com" target="_blank">
                                    info@sipembantu.com
                                </a>
                            </li>
                            <li>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.02417 6.01158C5.7265 7.2466 6.7534 8.2735 7.98842 8.9758L8.6518 8.04708C8.87238 7.73838 9.2887 7.64297 9.6217 7.82485C10.6768 8.40123 11.8428 8.75148 13.0592 8.84778C13.4492 8.87868 13.75 9.20417 13.75 9.59545V12.9426C13.75 13.3271 13.4591 13.6493 13.0766 13.6886C12.6792 13.7295 12.2783 13.75 11.875 13.75C5.45469 13.75 0.25 8.5453 0.25 2.125C0.25 1.7217 0.270565 1.32078 0.311418 0.92332C0.350725 0.540812 0.672955 0.25 1.05749 0.25H4.40456C4.79583 0.25 5.12135 0.55078 5.15222 0.940817C5.2485 2.15716 5.59877 3.32323 6.17515 4.37833C6.35703 4.7113 6.26162 5.12766 5.95292 5.34818L5.02417 6.01158ZM3.13319 5.5189L4.55815 4.50107C4.1541 3.62885 3.87721 2.70387 3.73545 1.75H1.7568C1.75227 1.87474 1.75 1.99975 1.75 2.125C1.75 7.71685 6.28315 12.25 11.875 12.25C12.0002 12.25 12.1253 12.2478 12.25 12.2432V10.2645C11.2962 10.1228 10.3712 9.84587 9.49892 9.44185L8.4811 10.8668C8.06935 10.7069 7.6717 10.5186 7.29055 10.3046L7.24697 10.2797C5.77728 9.44402 4.55601 8.22272 3.72025 6.75303L3.69545 6.70945C3.48137 6.3283 3.29316 5.93065 3.13319 5.5189Z"
                                        fill="#2295FF" />
                                </svg>
                                <a href="https://wa.link/9ab2ix" target="_blank">
                                    (+62) 851-1700-9996
                                </a>
                            </li>
                            {{-- <li>
                         <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.75 0.75V2.25H10.25V0.75H11.75V2.25H14.75C15.1642 2.25 15.5 2.58579 15.5 3V15C15.5 15.4142 15.1642 15.75 14.75 15.75H1.25C0.83579 15.75 0.5 15.4142 0.5 15V3C0.5 2.58579 0.83579 2.25 1.25 2.25H4.25V0.75H5.75ZM14 8.25H2V14.25H14V8.25ZM4.25 3.75H2V6.75H14V3.75H11.75V5.25H10.25V3.75H5.75V5.25H4.25V3.75Z" fill="#2295FF"/>
                         </svg>                                                      
                         <span>
                            Mon - Fri 12:00 - 18:00  
                         </span>                        
                      </li> --}}
                            <li>
                                <svg width="14" height="17" viewBox="0 0 14 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7 14.6746L10.7123 10.9623C12.7625 8.91208 12.7625 5.58794 10.7123 3.53769C8.66208 1.48744 5.33794 1.48744 3.28769 3.53769C1.23744 5.58794 1.23744 8.91208 3.28769 10.9623L7 14.6746ZM7 16.7959L2.22703 12.023C-0.40901 9.3869 -0.40901 5.11307 2.22703 2.47703C4.86307 -0.15901 9.1369 -0.15901 11.773 2.47703C14.409 5.11307 14.409 9.3869 11.773 12.023L7 16.7959ZM7 8.75C7.82845 8.75 8.5 8.07845 8.5 7.25C8.5 6.42157 7.82845 5.75 7 5.75C6.17155 5.75 5.5 6.42157 5.5 7.25C5.5 8.07845 6.17155 8.75 7 8.75ZM7 10.25C5.34314 10.25 4 8.90683 4 7.25C4 5.59314 5.34314 4.25 7 4.25C8.65683 4.25 10 5.59314 10 7.25C10 8.90683 8.65683 10.25 7 10.25Z"
                                        fill="#2295FF" />
                                </svg>
                                <span>
                                    Jl. Poris Paradise 2, RT.005/RW.010, Cipondoh Indah, Kec. Cipondoh, Kota Tangerang,
                                    Banten 15148
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <p>
            Version 1.1. Copyright &copy; SIPEMBANTU {{ date('Y') }}. All Rights Reserved.
        </p>
    </div>
    <img src="{{ asset('landing/assets/images/footer/footer-spra.png') }}" alt="img" class="footer-spara">
    <img src="{{ asset('landing/assets/images/icon/working-ball.png') }}" alt="img" class="footer-working">
</footer>
