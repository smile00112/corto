if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) var is_safari = true;
jQuery(window).load(function(){
	//$(".m-info-carousel .category-info").fadeIn();
	jQuery('#pic-block .category-info').animate({
        opacity: 1
	  }, 2000, 'easeOutQuad');
	  

	if (Modernizr.csstransforms3d) {
		setTimeout(function() {
			jQuery(".col .cube").addClass('rotate-front');
			jQuery("#page-block").css('opacity', 1);
		}, 600);
	}else{
		jQuery("#page-block").css('opacity', 1);
		jQuery(".col .cube").addClass('rotate-front');
	}

	//photo slider
    jQuery('.product-photo__slider').carouFredSel({
    	responsive	: true,
		items: {
		  visible: 1,
		  width		: 720,
		  height: "variable"
		},
		swipe: {
		  onTouch: true
		},
		auto: false,
		prev: '.photoslider-prev',
		next: '.photoslider-next',
		pagination: '.photoslider-pager'
    });

    //
    jQuery('.big-product-item').eq(0).show();

    //
    jQuery('.product-nav').find('.empty').width(jQuery('.prod-nav').width());

    //
    setTimeout(function(){
    	jQuery('.alert-success').slideUp();
    }, 2500);

    //for ajax reload
    setTimeout(function(){
    	jQuery('.b-product-img').height(jQuery('.main-img').height());
    }, 100);
//Выводим окно подписки через 2 минуты
      function popup(){
	   if (!jQuery.cookie('was')) {
                jQuery.fancybox.open({
		    type: 'inline', 
		    href: '#popuppodpiska',
		    
                    height: '383'
                })
            }
	    // Запомним в куках, что посетитель к нам уже заходил
	    jQuery.cookie('was', true, {
            expires: 365,  //сколько дней храним куки 
            path: '/'
            });
        }
//	setTimeout(popup, 300000);

//Конец вывода всплывающего окна подписки
//Выводим всплывающее окна адреса
      function popupadres(){
	   if (!jQuery.cookie('was2')) {
                jQuery.fancybox.open({
		    type: 'inline', 
		    href: '#popupadres',
		    width: '900',
                    height: '383'
                })
            }
	    // Запомним в куках, что посетитель к нам уже заходил
	    jQuery.cookie('was2', true, {
            expires: 365,  //сколько дней храним куки 
            path: '/'
            });
        }
	setTimeout(popupadres, 120000);

//Конец вывода всплывающего окна адреса
	var messLgth = jQuery('#system-message-container').children().length;
	if(messLgth !== 0){
		jQuery.fancybox.open(
			jQuery('#system-message-container'),{
					padding : 0
				}
		);
		
		jQuery('a.close').on('click', function(){
			$.fancybox.close();
		});
	}
	//Всплыывающие формы регистрации и авторизации
});

function popup_comment(){
	if (!jQuery.cookie('was2')) {

	}

	jQuery.fancybox.open({
		src  : '#hidden_comment',
		type : 'inline',
		opts : {
			afterShow : function( instance, current ) {
				//console.info( 'done!' );
			}
		}
	});
	// Запомним в куках, что посетитель к нам уже заходил
	jQuery.cookie('was2', true, {
		 expires: 365,  //сколько дней храним куки 
		 path: '/'
	});
}

function popup_message(){


	jQuery.fancybox.open({
		src  : '#hidden_message',
		type : 'inline',
		opts : {
			afterShow : function( instance, current ) {
				//console.info( 'done!' );
			}
		}
	});
	
}
jQuery(document).ready(function () {

	if(jQuery('#jsMenuBtnContainer').length){
		jQuery('#jsMenuBtnContainer').on('click', function(){
			jQuery('#nav-block').toggleClass('menu-close')
		});
	}
jQuery('.regpopup').fancybox();	
	width = Math.max(jQuery(window).width(), window.innerWidth);

	//link 3d
	if (Modernizr.csstransforms3d && !is_safari) {
		jQuery('body').on('click', 'a', function (e) {
			var $target = jQuery(this).prop('target');
			if( $target == '_blank') return;
			if(jQuery(this).hasClass('dropdown-item')) return;	
			e.preventDefault();
			var hrefLink = jQuery(this).attr("href");
			var linkReg = /^#.*/.exec(hrefLink);
			if(linkReg){
				return false;
			}else{
				jQuery(".col .cube").addClass('rotate-right');
				jQuery("#page-block").css('opacity', 0);
				setTimeout(function(){
		          document.location.href = hrefLink;
		        }, 600);
			}
		});
	}else{
		jQuery("#page-block").css('opacity', 1);
	}

	//up
	jQuery('.btn-up').click(function (e) {
      e.preventDefault();
      jQuery('body,html').animate({
        scrollTop: 0
      }, 500, 'easeOutQuad');
    });

	//main slider
    jQuery('.product-slider').owlCarousel({
	    loop:true,
	    nav:false,
	    responsiveClass:true,
	    mouseDrag: false,
	    touchDrag: false,
	    autoplay: true,
	    autoplayTimeout: 4000,
	    autoplaySpeed: 1000,
	    responsive:{
	        0:{
	            items:4
	        },
	        1600:{
	            items:5
	        },
	        2000:{
	            items:6
	        }
	    },
	    onInitialized: function(e){
	    	var pb = jQuery('.product-slider-wrap').height() + 70;
	    	var lh = jQuery('#container').height() - pb;
			if(Math.max(jQuery(window).width(), window.innerWidth) > 779){
                jQuery('.big-product').css('padding-bottom', pb);
                jQuery('.big-product-list').css('height', lh);
				jQuery('.big-product-item').css('line-height', lh+'px');
			}else{
                jQuery('.big-product').css('padding-bottom', jQuery('.product-slider-wrap').height());
			}
			jQuery('.owl-item.active').eq(0).find('.small-product-item').addClass('select');
	    }
	});
	var owl = jQuery('.product-slider');
	jQuery('.mainslider-next').click(function() {
	    owl.trigger('next.owl.carousel',[400]);
	});
	jQuery('.mainslider-prev').click(function() {
	    owl.trigger('prev.owl.carousel', [400]);
	});
	jQuery('.product-slider').on('translate.owl.carousel', function(event) {
		var item  		= event.item.index;
 	    var nextItem = jQuery('.owl-item').eq(item).find('.small-product-item');

 	    if(Math.max(jQuery(window).width(), window.innerWidth) < 779){
            jQuery('.big-product-list').css('height', jQuery('.big-product-list').height());
		}
 	    jQuery('.small-product-item').removeClass('select');
 	    nextItem.addClass('select');
 	    prod = nextItem.data('prod');
 	    jQuery('.big-product-item').fadeOut(490);
		jQuery('#'+prod).delay(500).fadeIn(700);
	});

    //scroll to items
	  jQuery(".scroll-link").on('click', function(event) {
	    var jQueryanchor = jQuery(this);
	    jQuery('html, body').stop().animate({
	      scrollTop: (jQuery(jQueryanchor.attr('href')).offset().top - 60)
				}, 600, 'easeOutQuad');
	      event.preventDefault();
	  });

   jQuery(".easybook__faq-link").on('click', function(event){
		event.preventDefault();
		popup_comment();
	});	  

   jQuery("[data-popup]").on('click', function(event){
		event.preventDefault();
		popup_message();
	});	  	

   jQuery('[name="gbookForm"]').on('submit', function(event){
		event.preventDefault();
		var params = $(this).serialize();
		$.ajax({
            url: '/ajax-comment/',
            type: 'post',
            data: 'key=555&'+params,
            dataType: 'json',
            beforeSend: function() {
                $('#system-message-container').html('');
            },
            complete: function() {
                
            },
            success: function(json) {
				// console.log(json)
				
				if(json.error){
					$('#system-message-container').html( json.error );
				}else{
					jQuery.fancybox.close();
					$('[name="gbookForm"]').find('input, textarea').val('');
					//$('#system-message-container').html( json.success );
					jQuery.fancybox.open({
						src  : '#hidden_post_message',
						type : 'inline',
						opts : {
							
						}
					});
				}
                //$с.html(json.telephone);
                //$с.removeClass('phone-content-number-сut'); 
                //$(".phone-content-number-сut").unbind('click')
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

	});
	
   jQuery('[name="messageForm"]').on('submit', function(event){
		event.preventDefault();
		var params = $(this).serialize();
		$.ajax({
            url: '/ajax-message/',
            type: 'post',
            data: 'key=555&'+params,
            dataType: 'json',
            beforeSend: function() {
                $('#system-message-container2').html('');
            },
            complete: function() {
                
            },
            success: function(json) {
				
				
				if(json.error){
					$('#system-message-container2').html( json.error );
				}else{
					$('[name="messageForm"]').find('input, textarea').val('');
					//$('#system-message-container2').html( json.success );
					jQuery.fancybox.close();
						/*jQuery.fancybox.open({
							type: 'inline', 
							href: '#hidden_post_message',

							//height: '383'
						});
						*/
					jQuery.fancybox.open({
						src  : '#hidden_post_message',
						type : 'inline',
						opts : {
							
						}
					});
				}
                //$с.html(json.telephone);
                //$с.removeClass('phone-content-number-сut'); 
                //$(".phone-content-number-сut").unbind('click')
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

	});	  	
    //inside menu
    jQuery(".product-menu__toggle").on('click', function(){
    	jQuery(this).toggleClass('i-m-open');
    	jQuery(this).next('.product-menu__list').toggle();
    });

	//check load and resize
	function checkWindowSize() {
		width = Math.max(jQuery(window).width(), window.innerWidth);
		height = Math.max(jQuery(window).height(), window.innerHeight);

		if (height >= jQuery(".page-block__inside").height()){
			jQuery('.mouse-scroll').hide();
		}			

		jQuery(window).scroll(function () {
	      if (jQuery(this).scrollTop() > (jQuery(".page-block__inside").height() - height*1.5)) {
	        jQuery('.mouse-scroll').fadeOut();
	      } else {
	        jQuery('.mouse-scroll').fadeIn();
	      }
	    });

	    ///btn up hide
	    if(jQuery(".page-block__inside").height() <= height)
	    	jQuery('.btn-up').hide();

	    //m-info-carousel
	    var carouselH = jQuery(window).height();
		if(width > 779){
            if(height <= 720)
                var carouselH = 720;
            jQuery('.m-info-carousel').carouFredSel({
                items: {
                    visible: 1,
                    height: carouselH
                },
                swipe: {
                    onTouch: true
                },
                scroll: {
                    fx: "crossfade",
                    duration: 800
                },
                auto: false,
                pagination: '.m-info-pager'
            });

            //
            var pb = jQuery('.product-slider-wrap').height() + 70;
            var lh = jQuery("#container").height() - pb;
            jQuery('.big-product-item').css('line-height', lh+'px');
            jQuery('.big-product-list').css('height', lh);
		}else{
            jQuery('.m-info-carousel').carouFredSel({
                items: {
                    visible: 1,
                },
                swipe: {
                    onTouch: true
                },
                scroll: {
                    fx: "crossfade",
                    duration: 800
                },
                auto: false,
                pagination: '.m-info-pager',
            });
            jQuery('.big-product-item').css('line-height', 'normal');
            jQuery('.big-product-list').css('height', 'auto');
            jQuery('.big-product').css('padding-bottom', jQuery('.product-slider-wrap').height());
		}

		
	}
	checkWindowSize();

	//resize
	jQuery(window).resize(function(e) {
		checkWindowSize();
		//for ajax reload
		jQuery('.b-product-img').height(jQuery('.main-img').height());
	});	       jQuery(".mouse-scroll").click(function(){	          jQuery("html, body").animate({scrollTop:jQuery(document).height()},"slow")               })



 jQuery(".magazin-drop").on('click', function(){
    	jQuery('.magazin-hide').toggleClass('magazin-hide-show');
    	
    });
});

function setProductImage($image){
	if($('#list_product_image_middle').length){
		$('#list_product_image_middle img').prop('src', $image);
	}
}