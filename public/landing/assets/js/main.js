"user strict";

/* ======== js Documentation =======

	# Template Name: Clenis
	# Version: 1.0
	# Date: 00/00/2024
	# Author: gramentheme
	# Author URI: 
	# Description: Clenis - HTML Templates

    ==================================================

     01. Added Smooth Scroll 
     -------------------------------------------------
     02. Preloader
     -------------------------------------------------
     03. Scroll To Top With Progress
     -------------------------------------------------
     04. Nice Select
     -------------------------------------------------
     05. Custom Menu
     -------------------------------------------------
     06. Video Popup
     -------------------------------------------------
     07. Odemoter
     -------------------------------------------------
     08. Wow Animation
     -------------------------------------------------
     09. Swipper Slider
     -------------------------------------------------
     10. Custom Slider
     -------------------------------------------------
     11. footer copyright year
     -------------------------------------------------
     12. odometer counter
     -------------------------------------------------
     13. video popup
     -------------------------------------------------
     14. Date Time End
     -------------------------------------------------
     15. Title Animation
     -------------------------------------------------
    
     -------------------------------------------------
     17. 
     -------------------------------------------------
     18. 
     -------------------------------------------------
     19. 
     -------------------------------------------------
     20. 
     -------------------------------------------------
     21. 
     -------------------------------------------------
     22. 
     -------------------------------------------------
     23. 
     -------------------------------------------------
     24.
     -------------------------------------------------
     25. Title Animation
     -------------------------------------------------
     26. Footer Styele Three

    ==================================================
============== */

//>> Javascrip Js <<//
$(document).ready(function () {

	//side contact added
	$(".remove__click").on("click", function (e) {
		$(".subside__barmenu").toggleClass("active");
	});
	//side contact added

	//>> Swiper Slider <<//
	
	//>>>> service slide
	var swiper = new Swiper(".we-provide-wrap", {
		slidesPerView: 1,
		spaceBetween: 24,
		navigation: {
			nextEl: ".mcustom__next",
			prevEl: ".mcustom__prev",
		},
		loop: true,
		speed: 5000,
		autoplay: {
			delay: 2000,
		},
		pagination: {
		  el: ".swiper-paginatio",
		  clickable: true,
		},
		breakpoints: {
			320: {
			  slidesPerView: 1,
			},
			480: {
				slidesPerView: 2,
			},
			575: {
				slidesPerView: 2,
			},
			767: {
				slidesPerView: 3,
			},
			991: {
				slidesPerView: 4,
			},
			1700: {
			  slidesPerView: 4.5,
			},
		}
	});
	//sponsor slide
	var swiper = new Swiper(".sponsor-wrap", {
		slidesPerView: 1,
		spaceBetween: 24,
		navigation: {
			nextEl: ".mcustom__next",
			prevEl: ".mcustom__prev",
		},
		loop: true,
		speed: 5000,
		autoplay: {
			delay: 2000,
		},
		pagination: {
		  el: ".swiper-paginatio",
		  clickable: true,
		},
		breakpoints: {
			320: {
			  slidesPerView: 2,
			},
			480: {
				slidesPerView: 2,
			},
			575: {
				slidesPerView: 3,
			},
			767: {
				slidesPerView: 3,
			},
			991: {
				slidesPerView: 4,
			},
			1700: {
			  slidesPerView: 5,
			},
		}
	});
	//sponsor slide
	var swiper = new Swiper(".testimonial-wrapper", {
		slidesPerView: 1,
		loop: true,
		spaceBetween: 24,
		speed: 4000,
		// centeredSlides: true,
		autoplay: {
			delay: 800,
		},
		pagination: {
			el: ".tes-paginatio",
			clickable: true,
		},
		breakpoints: {
			320: {
				slidesPerView: 1,
			},
			480: {
				slidesPerView: 1,
			},
			660: {
				slidesPerView: 2,
			},
			767: {
				slidesPerView: 2,
			},
			991: {
				slidesPerView: 3,
			},
			1700: {
				slidesPerView: 4.5,
			},
		}
	});

	//testimonial section
	var swiper = new Swiper(".testimonial-wrapper2", {
		slidesPerView: 1,
		spaceBetween: 10,
		navigation: {
			nextEl: ".mcustom__next1",
			prevEl: ".mcustom__prev1",
		},
		loop: true,
		speed: 3000,
		autoplay: {
			delay: 2000,
		},
		pagination: {
		  el: ".swiper-paginatio",
		  clickable: true,
		},
		breakpoints: {
			320: {
			  slidesPerView: 1,
			},
			480: {
				slidesPerView: 1,
			},
			575: {
				slidesPerView: 1,
			},
			767: {
				slidesPerView: 1,
			},
			991: {
				slidesPerView: 1,
			},
			1700: {
			  slidesPerView: 1,
			},
		}
	});
	//project made
	var swiper = new Swiper(".custom-project-slidewrap", {
		loop: true,
		centeredSlides: true,
		slidesPerView: 1,
		spaceBetween: 24,
        freeMode: true,
		navigation: {
			nextEl: ".mcustom__next2",
			prevEl: ".mcustom__prev2",
		},
		loop: true,
		speed: 1000,
		autoplay: {
			delay: 2000,
		},
		breakpoints: {
			320: {
			  slidesPerView: 1,
			},
			480: {
				slidesPerView: 1,
			},
			575: {
				slidesPerView: 2,
			},
			767: {
				slidesPerView: 2,
			},
			991: {
				slidesPerView: 2,
			},
			1200: {
			  slidesPerView: 3,
			},
		}
	});

	//>> Swiper Slider <<//		
	
	//>> Nice Select <<//
	$('select').niceSelect();
	//>> Nice Select <<//

	//>> Menu Fixed Components <<//
	var fixed_top = $(".main-headerwrap");
	$(window).on("scroll", function () {
		if ($(this).scrollTop() > 20) {
			fixed_top.addClass("menu-fixed animated fadeInDown");
			fixed_top.removeClass("slideInUp");
			$("body").addClass("body-padding");
		} else {
			fixed_top.removeClass("menu-fixed fadeInDown");
			fixed_top.addClass("slideInUp");
			$("body").removeClass("body-padding");
		}
	});
	//>> Menu Fixed Components <<//

	      // navbar custom//
		  $('.navbar-toggle-btn').on('click', function () {
			$('.navbar-toggle-item').slideToggle(300);
			$('body').toggleClass('overflow-hidden');
			$(this).toggleClass('open');
		  });
		  $('.menu-item button').on('click', function () {
			$(this).siblings("ul").slideToggle(300);
		  });
		  // navbar custom//

	//>> img hide <<//
	$(".option").click(function(){
		$(".option").removeClass("active");
		$(this).addClass("active");
		
	 });
	//>> Magnific Popup <<//
	$('.video-btn').magnificPopup({
		type: 'iframe',
		callbacks: {
			
	  	}
	});
	$('.imgc').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true,
		}
	  });
	//>> Magnific Popup <<//

	//>> Odometer Counter <<//
	$(".odometer-item").each(function () {
		$(this).isInViewport(function (status) {
			if (status === "entered") {
				for (
					var i = 0;
					i < document.querySelectorAll(".odometer").length;
					i++
				) {
					var el = document.querySelectorAll(".odometer")[i];
					el.innerHTML = el.getAttribute("data-odometer-final");
				}
			}
		});
	});
	//>> Odometer Counter <<//

	//>> Wow Animation <<//
	new WOW().init();
	//>> Wow Animation <<//


	//>> Preloader <<//
	setTimeout(function(){
		$('.preloaders').fadeToggle();
	}, 1500);
	//>> Preloader <<//

	//>> Search Popup <<//
	$(function () {
		$('a[href="#search"]').on('click', function(event) {
			event.preventDefault();
			$('#search').addClass('open');
			$('#search > form > input[type="search"]').focus();
		});
		
		$('#search, #search button.close').on('click keyup', function(event) {
			if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
				$(this).removeClass('open');
			}
		});
		
		$('form').submit(function(event) {
			event.preventDefault();
			return false;
		})
	});
	//>> Search Popup <<//

		// Custom Tabs //
	$(".tablinks .nav-links").each(function () {
		var targetTab = $(this).closest(".singletab");
		targetTab.find(".tablinks .nav-links").each(function() {
		var navBtn = targetTab.find(".tablinks .nav-links");
		navBtn.click(function(){
			navBtn.removeClass('active');
			$(this).addClass('active');
			var indexNum = $(this).closest("li").index();
			var tabcontent = targetTab.find(".tabcontents .tabitem");
			$(tabcontent).removeClass('active');
			$(tabcontent).eq(indexNum).addClass('active');
		});
		});
	});
	// Custom Tabs //

      // img comparison //
	  function initComparisons() {
		var x, i;
		x = document.getElementsByClassName("img-comp-overlay");
		for (i = 0; i < x.length; i++) {
		  compareImages(x[i]);
		}
		function compareImages(img) {
		  var slider, img, clicked = 0, w, h;
		  w = img.offsetWidth;
		  h = img.offsetHeight;
		  img.style.width = (w / 2) + "px";
		  slider = document.createElement("DIV");
		  slider.setAttribute("class", "img-comp-slider");
		  img.parentElement.insertBefore(slider, img);
		  slider.style.top = (h / 2) - (slider.offsetHeight / 2) + "px";
		  slider.style.left = (w / 2) - (slider.offsetWidth / 2) + "px";
		  slider.addEventListener("mousedown", slideReady);
		  window.addEventListener("mouseup", slideFinish);
		  slider.addEventListener("touchstart", slideReady);
		  window.addEventListener("touchend", slideFinish);
		  function slideReady(e) {
			e.preventDefault();
			clicked = 1;
			window.addEventListener("mousemove", slideMove);
			window.addEventListener("touchmove", slideMove);
		  }
		  function slideFinish() {
			clicked = 0;
		  }
		  function slideMove(e) {
			var pos;
			if (clicked == 0) return false;
			pos = getCursorPos(e)
			if (pos < 0) pos = 0;
			if (pos > w) pos = w;
			slide(pos);
		  }
		  function getCursorPos(e) {
			var a, x = 0;
			e = (e.changedTouches) ? e.changedTouches[0] : e;
			a = img.getBoundingClientRect();
			x = e.pageX - a.left;
			x = x - window.pageXOffset;
			return x;
		  }
		  function slide(x) {
			img.style.width = x + "px";
			slider.style.left = img.offsetWidth - (slider.offsetWidth / 2) + "px";
		  }
		}
	  }

	  initComparisons();

});


//>> Javascrip Js <<//

