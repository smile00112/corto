
$(function ($) {
	//Увееличение на стр товара
	$('.img-popup-link').magnificPopup({
		type: 'image'
		// other options
	  });
	  
	//Делаем разряд у чисел
//	$('.product-item__price, .header-cart__del-info').text();
	$('.product-item__price, .header-cart__del-info, .header-cart__sum').each(function(){
		var $t = $(this).text().replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ");
		$(this).text($t);
	})
	
	//$('.image-link').magnificPopup({type:'image',closeOnContentClick:true});

	$('.popup-youtube').click(function(e) {
		e.preventDefault();
		console.log($(this).prop('href'));

		$.magnificPopup.open({
			items: {
				src: $(this).prop('href').replace('https://www.youtube.com/embed/','https://www.youtube.com/watch?v='), // can be a HTML string, jQuery object, or CSS selector
			},
			type: 'iframe',

		});
	});

	$('.popup-youtube-2').click(function(e) {
		e.preventDefault();
		//console.log($(this).prop('href'));

		$.magnificPopup.open({
			items: {
				src: $(this).data('href').replace('https://www.youtube.com/embed/','https://www.youtube.com/watch?v='), // can be a HTML string, jQuery object, or CSS selector
			},
			type: 'iframe',

		});
	});

	$( document ).on( "click", ".quick_view", function() { 

		var $this = $(this),
			$product_id = $this.data('product_id');
/*		$('#fastview_modal').load('index.php?route=product/product/fastview&product_id='+$product_id, function(){
			
			setTimeout(function () {
					show_message_modal_by_id('fastview_modal');
			}, 100);
		});
*/
		$.magnificPopup.open({
			tLoading: 'Загрузка...',
			mainClass: 'mfp-fastview',
			items: {
				src: 'index.php?route=product/product/fastview&product_id=' + $product_id,
				type: 'ajax'
			},
			midClick: true,
			removalDelay: 200
		});

	});

	$( document ).on( "click", ".mfp-close,.mfp-close2", function() { 
		$.magnificPopup.close();
	});

	$( document ).on( "click", "a.plus", function() { 
		var inp = $(this).prev();
		var value = inp.val()*1;
		var limit =  inp.data('limit');
		if(limit){
			if(value < limit) inp.val(value+1);
		}
	});
	
	$( document ).on( "click", "a.minus", function() { 

		var inp = $(this).next();
		var value = inp.val()*1;
		var limit =  inp.data('limit');
		
		if(limit){
			if(value > 1) inp.val(value-1);
		}
	});	
}); 

/*
	function show_message_modal_by_id(id) {
		//alert(message);
			$.magnificPopup.open({
			  items: {
				src: $('#'+id).html(),
			  },
			  type: 'inline'
			});
	}
	*/

function show_message_modal_by_id(id) {
	//e.preventDefault();

	$.magnificPopup.open({
	  items: {
		src: '<div class="white-popup-c pop-up"><p>'+$('#'+id).html()+'</p></div>',
	  },
	  type: 'inline'
	});
}
	
	function show_message(message) {
		//alert(message);
			$.magnificPopup.open({
			  items: {
				src: '<div class="white-popup-c"><p>'+message+'</p></div>',
			  },
			  type: 'inline'
			});
	}

	function change_catalog_view(view) {
		$.ajax({
			type:'post',
			data:'view_metod='+view,
			url:'index.php?route=product/category/changecatalogview',
			success:function (data) {
				window.location.reload();
				//console.log(data)

		}
	});
	}

	function get_oct_popup_found_cheaper(product_id) {
		setTimeout(function () {
			$.magnificPopup.open({
				tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
				items: {
					src: 'index.php?route=extension/module/oct_popup_found_cheaper&product_id=' + product_id,
					type: 'ajax'
				},
				midClick: true,
				removalDelay: 200
			});
		}, 1);
	}
	
	function get_oct_popup_remeber_pass() {
		setTimeout(function () {
			$.magnificPopup.open({
				tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
				items: {
					src: 'index.php?route=account/reset',
					type: 'ajax'
				},
				midClick: true,
				removalDelay: 200
			});
		}, 1);
	}
	function get_oct_popup_purchase(product_id) {
		setTimeout(function () {
			$.magnificPopup.open({
				tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
				items: {
					src: 'index.php?route=extension/module/oct_popup_purchase&product_id=' + product_id,
					type: 'ajax'
				},
				midClick: true,
				removalDelay: 200
			});
		}, 1);
	}

	function get_oct_popup_subscribe() {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: 'index.php?route=extension/module/oct_popup_subscribe',
				type: 'ajax'
			},
			midClick: true,
			removalDelay: 200
		});
	}

	function get_oct_popup_call_phone() {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: 'index.php?route=extension/module/oct_popup_call_phone',
				type: 'ajax'
			},
			midClick: true,
			removalDelay: 200
		});
	}

	function get_oct_product_preorder(product_id) {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: 'index.php?route=extension/module/oct_product_preorder&product_id=' + product_id,
				type: 'ajax'
			},
			midClick: true,
			removalDelay: 200
		});
	}

	function oct_get_product_id(data) {
		var product_id = 0;
		var arr = data.split("&");

		for (var i = 0; i < arr.length; i++) {
			var product_id = arr[i].split("=");
			if (product_id[0] === "product_id") {
				return product_id[1];
			}
		}
	}

	function get_oct_popup_product_options(product_id) {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: "index.php?route=extension/module/oct_popup_product_options&product_id=" + product_id,
				type: "ajax"
			},
			midClick: true,
			removalDelay: 200
		});
	}

	function get_oct_popup_product_view(product_id) {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: "index.php?route=extension/module/oct_popup_view&product_id=" + product_id,
				type: "ajax"
			},
			midClick: true,
			removalDelay: 200
		});
	}

	function get_oct_popup_login() {
		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: "index.php?route=extension/module/oct_popup_login",
				type: "ajax"
			},
			midClick: true,
		//	removalDelay: 200
		});
	}
	function get_oct_popup_login2() { 
		$.magnificPopup.close();

$.ajax({
			url: 'index.php?route=extension/module/oct_popup_login',
			type: 'post',
			data: 'ajax=1',
			dataType: 'text',
			beforeSend: function() {
				$.magnificPopup.close();
			},
			complete: function() {
			},
			success: function(data) {
				//console.log(data);
				$data = $(data)
					$.magnificPopup.open({
						tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
						items: {
						src:   $data, // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					  },
						midClick: false
						//removalDelay: 200
					});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		
	}
		
	
	function get_oct_popup_register() {
		//$.magnificPopup.close();
		$.ajax({
			url: 'index.php?route=account/register',
			type: 'post',
			data: 'ajax=1',
			dataType: 'text',
			beforeSend: function() {
				$.magnificPopup.close();
			},
			complete: function() {
			},
			success: function(data) {
				//console.log(data);
				$data = $(data).find('#content');
				$data.find('.modal').removeClass('.modal-2');
				$data = $data.find('.modal');
				$data.find('[type="submit"]').addClass('btn_disabled');
					$.magnificPopup.open({
						tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
						items: {
						src:   $data, // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					  },
						midClick: true
						//removalDelay: 200
					});
					//$('[name="telephone"]').inputmask({"mask": "(999) 999-9999"});
					$('[name="email"]').inputmask({ alias: "email"});
					  
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	
/*
	//Форма регистрации - Обработчик
	if (window.Controller === undefined) { Controller = {}; }

	Controller.RegForm = function(options) {
		this.switchers = [];
		this.messages = [];
	//    this.options = $.extend({}, Prmn.CityManager.DEFAULTS, options);   
	// reg_form
		this.cities_popup = null;
	};
*/

	$( document ).on( "keydown", "#reg_form input_", function() {
		
		var email = $('[name="email"]').val(),
			password = $('[name="password"]').val(),
			repassword = $('[name="confirm"]').val(),
			firstname = $('[name="firstname"]').val(),
			telephone = $('[name="telephone"]').val(),
			error = false;
			
		if(!email){error = true; console.error(1); }console.warn($('[name="email"]').inputmask("isComplete"));
		if(!firstname){error = true; console.error(2);}
		if(!telephone){error = true; console.error(3); }console.warn($('[name="telephone"]').inputmask("isComplete"));
		if(!password){error = true; console.error(4);}
		if(!repassword){error = true; console.error(5);}
		if(password != repassword){error = true; console.error(6);}
		
		if(!error) alert('!!!');
			//<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
	});	
	
	$( document ).on( "mouseover", ".regform-warn-icon", function() { 
		if(!$(this).next().is(':visible'))
			$(this).next().fadeIn();
	});
	
	$( document ).on( "mouseout", ".regform-warn-icon", function() { 
		if($(this).next().is(':visible'))
			$(this).next().fadeOut();
	});	
	
	$( document ).on( "click", "#reg_form .btn_disabled", function() { 
		var email = $('[name="email"]'),
			password = $('[name="password"]'),
			repassword = $('[name="confirm"]'),
			firstname = $('[name="firstname"]'),
			telephone = $('[name="telephone"]'),
			agree = $('[name="agree"]'),
			error = false,
			form = $(this).parents('form');
		
		//Чистим предыдущие сообщения
		$('.reg-form-warn').html('');
		
		if(!email.val() || !$('[name="email"]').inputmask("isComplete")){
			error = true; 
					console.error(1)
			$('.reg-form-email-warn').prepend('<div class="text-danger-rf">Неверный формат email</div>');
			$('.reg-form-email-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		};
		if(!firstname.val()){
			error = true;
			console.error(2);
			$('.reg-form-firstname-warn').prepend('<div class="text-danger-rf">Заполните поле</div>');
			$('.reg-form-firstname-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}
		if(!telephone.val() || !$('[name="telephone"]').inputmask("isComplete") ){
			error = true; 
			console.error(3); 
			$('.reg-form-telephone-warn').prepend('<div class="text-danger-rf">Неверный формат номера</div>');
			$('.reg-form-telephone-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}
		if(!password.val()){
			error = true; 
			console.error(4);
			$('.reg-form-password-warn').prepend('<div class="text-danger-rf">Заполните поле</div>');
			$('.reg-form-password-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}
		/*else if(password.length <=4){
			$('.reg-form-password-warn').prepend('<div class="text-danger-rf">Пароль должен быть более 4 символов длиной</div>');
			$('.reg-form-password-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}*/
		if(!repassword.val()){
			error = true; 
			console.error(5);
			$('.reg-form-confirm-warn').prepend('<div class="text-danger-rf">Заполните поле</div>');
			$('.reg-form-confirm-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}
		if(password.val() != repassword.val()){
			error = true; 
			console.error(6);
			$('.reg-form-confirm-warn').prepend('<div class="text-danger-rf">Пароли не совпадают</div>');
			$('.reg-form-confirm-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon" aria-hidden="true"></i>');
		}
		if(!agree.is(':checked')){
			error = true; 
			console.error(6);
			$('.reg-form-agree-warn').prepend('<div class="text-danger-rf">Ознакомьтесь с условиями обработки персональных данных</div>');
			$('.reg-form-agree-warn').prepend('<i class="fa fa-exclamation-triangle regform-warn-icon regform-warn-icon-agree" aria-hidden="true"></i>');
		}		
		if(error) {
			//alert('!!!2');
			}
		else{
			
			$(this).removeClass('btn_disabled'); 
			form.submit();
		}
		
		
	});
	
	function submit_register(form) {
		
		var action = form.prop('action'); 
		//alert(action)
		//console.log(form.html()); 
		
			$.ajax({		async: false, 
							type: "POST",
							data :"&step=1",
							url: action,
							dataType: "html",
								success: function(data)
								{	
								
								console.info(data);
									
									
								},
								error: function(req){
										crash = 'Что то поломалось при обработке прайса. Сообщите программисту';
										//alert('error_1')					
										console.error(req.responseText);
										
								}
								
					});	

		return false;
	}
	
	function get_oct_popup_add_to_wishlist(product_id) {
		$.ajax({
			url: "index.php?route=account/wishlist/add",
			type: "post",
			data: "product_id=" + product_id,
			dataType: "json",
			success: function (json) {
				$.magnificPopup.open({
					tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
					items: {
						src: "index.php?route=extension/module/oct_popup_add_to_wishlist&product_id=" + product_id,
						type: "ajax"
					},
					midClick: true,
					removalDelay: 200
				});

				$("#wishlist-total span").html(json['total']);
				$("#wishlist-total").attr("title", json['total']);

				$.ajax({
					url: 'index.php?route=extension/module/oct_page_bar/update_html',
					type: 'get',
					dataType: 'json',
					success: function (json) {
						$("#oct-favorite-quantity").html(json['total_wishlist']);
					}
				});

			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function remove_wishlist(product_id) {
		$.ajax({
			url: "index.php?route=extension/module/oct_page_bar/remove_wishlist&remove=" + product_id,
			type: "get",
			dataType: "json",
			success: function (json) {
				$.ajax({
					url: 'index.php?route=extension/module/oct_page_bar/update_html',
					type: 'get',
					dataType: 'json',
					success: function (json) {
						$("#oct-favorite-quantity").html(json['total_wishlist']);
					}
				});

				$('#oct-favorite-content').load('index.php?route=extension/module/oct_page_bar/block_wishlist');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function get_oct_popup_add_to_compare(product_id) {
		$.ajax({
			url: "index.php?route=product/compare/add",
			type: "post",
			data: "product_id=" + product_id,
			dataType: "json",
			success: function (json) {
				$.magnificPopup.open({
					tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
					items: {
						src: "index.php?route=extension/module/oct_popup_add_to_compare&product_id=" + product_id,
						type: "ajax"
					},
					midClick: true,
					removalDelay: 200
				});

				$("#compare-total").html(json['total']);

				$.ajax({
					url: 'index.php?route=extension/module/oct_page_bar/update_html',
					type: 'get',
					dataType: 'json',
					success: function (json) {
						$("#oct-compare-quantity").html(json['total_compare']);
					}
				});
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function remove_compare(product_id) {
		$.ajax({
			url: "index.php?route=extension/module/oct_page_bar/remove_compare&remove=" + product_id,
			type: "get",
			dataType: "json",
			success: function (json) {
				$.ajax({
					url: 'index.php?route=extension/module/oct_page_bar/update_html',
					type: 'get',
					dataType: 'json',
					success: function (json) {
						$("#oct-compare-quantity").html(json['total_compare']);
					}
				});

				$('#oct-compare-content').load('index.php?route=extension/module/oct_page_bar/block_compare');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function get_oct_popup_cart() {

		$.magnificPopup.open({
			tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
			items: {
				src: "index.php?route=extension/module/oct_popup_cart",
				type: "ajax"
			},
			midClick: !0,
			removalDelay: 200
		})
	}

	function get_oct_popup_add_to_cart(product_id, quantity) {
		$.ajax({
			url: "index.php?route=checkout/cart/add",
			type: "post",
			data: "product_id=" + product_id + "&quantity=" + ("undefined" != typeof quantity ? quantity : 1),
			dataType: "json",
			success: function (json) {
				//Обновляем корзину
				$('.main-header__cart').html(json.header_cart);

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					// $.magnificPopup.open({
					//   tLoading: '<img src="catalog/view/theme/oct_techstore/image/ring-alt.svg" />',
					//   items: {
					//    src: "index.php?route=extension/module/oct_popup_add_to_cart&product_id=" + product_id,
					//    type: "ajax"
					//   },
					// midClick: true,
					// removalDelay: 200
					// });

					get_oct_popup_cart();

					$("#cart-total").html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');

					$.ajax({
						url: 'index.php?route=extension/module/oct_page_bar/update_html',
						type: 'get',
						dataType: 'json',
						success: function (json) {
							$("#oct-bottom-cart-quantity").html(json['total_cart']);
						}
					});
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function validate(input) {
		input.value = input.value.replace(/[^\d,]/g, '');
	}

	function doLiveSearch(a, b) {
		//if(b.length < 3) return;
		return 38 != a.keyCode && 40 != a.keyCode && ($("#livesearch_search_results").remove(), updown = -1, !("" == b || b.length < 3 || (b = encodeURI(b), $.ajax({
			url: $("base").attr("href") + "index.php?route=product/search/aj_search"  /*+ "&filter_category_id=" + $('#search input[name=category_id]').val()*/,
			data: 'search=' + b ,
			dataType: "json",
			type: 'post',
			beforeSend: function() {
				view_preloder(true);
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
				view_preloder(false);
			},
			success: function (a) {
					$('.aj-search-result').remove();
					$('body').append(a.result)

					var $message = $('#catalog_search').parent();
					var firstClick = true;
					$(document).bind('click.myEvent', function(e) {
						if (!firstClick && $(e.target).closest('#catalog_search').length == 0) {
							$message.hide();
							$(document).unbind('click.myEvent');
							firstClick = true;
						}
						firstClick = false;
					});	

				if (a.length > 0) { 

/*
					var c = document.createElement("ul");
					c.id = "livesearch_search_results";
					var d, e;
					for (var f in a) {
						if (d = document.createElement("li"), eListHr = document.createElement("hr"), eListDiv = document.createElement("div"), eListDiv.setAttribute("style", "height: 10px; clear: both;"), eListDivpr = document.createElement("span"), eListDivpr.innerHTML = (a[f].price) ? a[f].price : '', eListDivpr.setAttribute("style", "height: 14px; color: #147927;"), eListDivprspec = document.createElement("span"), eListDivprspec.innerHTML = a[f].special, eListDivprspec.setAttribute("style", "font-weight: bold; margin-left: 8px; color: #a70d0d; font-size: 16px;"), eListImg = document.createElement("img"), eListImg.src = a[f].image, eListImg.setAttribute("style", "margin-right: 10px;"), eListImg.align = "left", eListDivstatus = document.createElement("span"), eListDivstatus.innerHTML = a[f].stock, eListDivstatus.setAttribute("style", "height: 14px; color: #337ab7; margin-left: 15px; font-weight: bold;"), e = document.createElement("a"), e.setAttribute("style", "display: block;"), e.appendChild(document.createTextNode(a[f].name)), "undefined" != typeof a[f].href) {
							"" != a[f].special && eListDivpr.setAttribute("style", "text-decoration: line-through;");
							var g = decodeURIComponent(a[f].href);

							e.href = g.replace('&amp;', "&");
						} else e.href = $("base").attr("href") + "index.php?route=product/product&product_id=" + a[f].product_id + "&keyword=" + b;
						d.appendChild(e), c.appendChild(eListImg), c.appendChild(d), c.appendChild(eListDivpr), "" != a[f].special && c.appendChild(eListDivprspec), c.appendChild(eListDivstatus), c.appendChild(eListHr), c.appendChild(eListDiv)
					}
					$("#livesearch_search_results").length > 0 && $("#livesearch_search_results").remove(), $("#search").append(c), $("#livesearch_search_results").css("display", "none"), $("#livesearch_search_results").slideDown("slow"), $("#livesearch_search_results").animate({
						backgroundColor: "rgba(255, 255, 255, 0.98)"
					}, 2e3)
*/

				}
			}
		}), 0)))
	}

	function doLiveSearchMobile(ev, keywords) {
		if (ev.keyCode == 38 || ev.keyCode == 40) {
			return false;
		}

		$('#livesearch_search_results').remove();
		updown = -1;

		if (keywords == '' || keywords.length < 3) {
			return false;
		}
		keywords = encodeURI(keywords);

		$.ajax({
			url: $('base').attr('href') + 'index.php?route=product/search/ajax&keyword=' + keywords,
			dataType: 'json',
			success: function (result) {
				if (result.length > 0) {
					var eList = document.createElement('ul');
					eList.id = 'msearchresults';
					var eListElem;
					var eLink;
					for (var i in result) {
						eListElem = document.createElement('li');

						eListDiv = document.createElement('div');
						eListDiv.setAttribute("style", "height: 10px; clear: both;");

						eListDivpr = document.createElement("span");
						eListDivpr.innerHTML = result[i].price;
						eListDivpr.setAttribute("style", "height: 14px; color: #147927;");
						"" != result[i].special && eListDivpr.setAttribute("style", "text-decoration: line-through;");

						eListDivprspec = document.createElement("span");
						eListDivprspec.innerHTML = result[i].special;
						eListDivprspec.setAttribute("style", "font-weight: bold; margin-left: 8px; color: #a70d0d; font-size: 16px;");

						eListDivstatus = document.createElement("span");
						eListDivstatus.innerHTML = result[i].stock;
						eListDivstatus.setAttribute("style", "height: 14px; color: #337ab7; margin-left: 15px; font-weight: bold;");

						eListImg = document.createElement('img');
						eListImg.src = result[i].image;
						eListImg.setAttribute("style", "margin-right: 10px;");
						eListImg.align = 'left';

						eLink = document.createElement('a');
						eLink.setAttribute("style", "display: block;");
						eLink.appendChild(document.createTextNode(result[i].name));
						if (typeof (result[i].href) != 'undefined') {
							var convertlink = decodeURIComponent(result[i].href);
							eLink.href = convertlink.replace('&amp;', "&");

						} else {
							eLink.href = $('base').attr('href') + 'index.php?route=product/product&product_id=' + result[i].product_id + '&keyword=' + keywords;
						}
						eListElem.appendChild(eLink);
						eList.appendChild(eListImg);
						eList.appendChild(eListElem);
						eList.appendChild(eListDivpr);
						"" != result[i].special && eList.appendChild(eListDivprspec);
						eList.appendChild(eListDivstatus);
						eList.appendChild(eListDiv);
					}
					if ($('#msearchresults').length > 0) {
						$('#msearchresults').remove();
					}
					$('#searchm').append(eList);
				}
			}
		});

		return true;
	}

	function upDownEvent(a) {
		var b = document.getElementById("livesearch_search_results");
		if ($("#search").find("[name=search]").first(), b) {
			var c = b.childNodes.length - 1;
			if (updown != -1 && "undefined" != typeof b.childNodes[updown] && $(b.childNodes[updown]).removeClass("highlighted"), 38 == a.keyCode ? updown = updown > 0 ? --updown : updown : 40 == a.keyCode && (updown = updown < c ? ++updown : updown), updown >= 0 && updown <= c) {
				$(b.childNodes[updown]).addClass("highlighted");
				var d = b.childNodes[updown].childNodes[0].text;
				"undefined" == typeof d && (d = b.childNodes[updown].childNodes[0].innerText), $("#search").find("[name=search]").first().val(new String(d).replace(/(\s\(.*?\))$/, ""))
			}
		}
		return !1
	}



function viewport() {
		var e = window,
			a = 'inner';
		if (!('innerWidth' in window)) {
			a = 'client';
			e = document.documentElement || document.body;
		}
		return {
			width: e[a + 'Width'],
			height: e[a + 'Height']
		};
	}
$(document).ready(function () {
	var menuResp = viewport().width;
	var sheight = $(window).height();
/*	if (menuResp <= 992) {
		$("#menu-mobile-box").prepend( $( "#menu" ) );
	} else {
		$('ul.menu.flex').flexMenu();
		$("ul.flexMenu-popup").mouseleave(function() {
			$(".flexMenu-popup").css("display", "none");
		});
	}
*/
	$('#menu-mobile-toggle').on('click', function() {
		$('#menu-mobile').slideToggle(50, "swing");
		$('html').toggleClass('noscroll');
		$('#oct-bluring-box').css("height",sheight);
	});

	$('.megamenu-toggle-a').on('click', function() {
		$(this).parent().toggleClass("open");
	});

	$(".parent-title-toggle").on("click", function(t) {
		$(this).toggleClass("opened"), $(this).next().toggleClass("megamenu-ischild-opened"), t.preventDefault(), t.stopPropagation()
	});

	$("#menu .navbar-header").on("click", function(t) {
		$(this).next().toggleClass("in"), t.preventDefault(), t.stopPropagation()
	});

	$("#back-top").hide(), $(function () {
		$(window).scroll(function () {
			$(this).scrollTop() > 450 ? $("#back-top").fadeIn() : $("#back-top").fadeOut()
		}), $("#back-top a").click(function () {
			return $("body,html").animate({
				scrollTop: 0
			}, 800), !1
		})
	})

	var timer = 0;
	$("#search").find("[name=search]").first().keyup(function (a) {
		var $s = this.value;
		clearTimeout(timer);
		timer = setTimeout(
			 function(){
				doLiveSearch(a, $s)
			 }, 
			 1000);
	}).focus(function (a) {
		if($('.aj-search-result').length)
			$('.aj-search-result').fadeIn();
		//doLiveSearch(a, this.value)
	}).keydown(function (a) {
		upDownEvent(a)
	}).blur(function () {
		//window.setTimeout("$('#livesearch_search_results').remove();updown=0;", 1500)
		//window.setTimeout("$('.aj-search-result').fadeOut();updown=0;", 500)
	}), $(document).bind("keydown", function (a) {
		try {
			13 == a.keyCode && $(".highlighted").length > 0 && (document.location.href = $(".highlighted").find("a").first().attr("href"))
		} catch (a) {}
	});


	$('.navbar-nav > li > .dropdown-toggle').click(function () {
		if ($(this).attr('href') === undefined) {
			//return false;
		} else {
			window.location = $(this).attr('href');
		}
	});


	$("#msrch").find("[name=search]").first().keyup(function (ev) {
		doLiveSearchMobile(ev, this.value);
	}).focus(function (ev) {
		doLiveSearchMobile(ev, this.value);
	}).keydown(function (ev) {
		upDownEvent(ev);
	}).blur(function () {});
	$(document).bind('keydown', function (ev) {
		try {
			if (ev.keyCode == 13 && $('.highlighted').length > 0) {
				document.location.href = $('.highlighted').find('a').first().attr('href');
			}
		} catch (e) {}
	});

	$('#oct-m-search-button').on('click', function () {
		srchurl = $('base').attr('href') + 'index.php?route=product/search';
		var input_value = $('input[name=\'search\']').val();
		if (input_value.length <= 0) {
			return false;
		}
		if (input_value) {
			srchurl += '&search=' + encodeURIComponent(input_value);
		}
		location = srchurl;
	});
	$("#oct-mobile-search-box input[name='search']").on("keydown", function (a) {
		if (13 == a.keyCode) {
			var b = $("input[name='search']").val();
			if (b.length <= 0) return !1;
			$("#oct-m-search-button").trigger("click");
		}
	});

	$("#oct-search-button").on("click", function () {
		srchurl = $("base").attr("href") + "index.php?route=product/search";
		var a = $("#search input[name='search']").val();
		if (a.length <= 0) return !1;
		a && (srchurl += "&search=" + encodeURIComponent(a));
		var b = $("input[name='category_id']").prop("value");
		b > 0 && (srchurl += "&sub_category=true&category_id=" + encodeURIComponent(b)), location = srchurl
	});
	$("#search input[name='search']").on("keydown", function (a) {
		if (13 == a.keyCode) {
			var b = $("input[name='search']").val();
			if (b.length <= 0) return !1;
			$("#oct-search-button").trigger("click");
		}
	});
	$("#search a").on('click', function () {
		$(".cats-button").html('<span class="category-name">' + $(this).html() + ' </span><i class="fa fa-caret-down" aria-hidden="true"></i>');
		$(".selected_oct_cat").val($(this).attr("id"));
	});
});

function hidePanel() {
	$('#hide-slide-panel').fadeOut();
	$('#oct-slide-panel .oct-slide-panel-content').removeClass('oct-slide-panel-content-opened');
	$('#oct-bluring-box').removeClass('oct-bluring');
	$('.oct-slide-panel-item-content').removeClass('oct-panel-active');
	$('.oct-panel-link-active').removeClass('oct-panel-link-active');
}


$(document).ready(function () {

	$('#search .dropdown').on('click', function () {
		$(this).toggleClass('open-dropdown');
	});

	$("#search .dropdown").mouseleave(function () {
		$(this).removeClass('open-dropdown');
	});


	$('.thumbnails a').on('click', function (e) {
		$(".thumbnails a").removeClass("selected-thumb");
		$(this).addClass("selected-thumb");
	});

	//cat-menu

	$('#sstore-3-level li.active').addClass('open').children('ul').show();
	$('#sstore-3-level li.has-sub>a.toggle-a').on('click', function () {
		$(this).removeAttr('href');
		var element = $(this).parent('li');
		if (element.hasClass('open')) {
			element.removeClass('open');
			element.find('li').removeClass('open');
			element.find('ul').slideUp(200);
		} else {
			element.addClass('open');
			element.children('ul').slideDown(200);
			element.siblings('li').children('ul').slideUp(200);
			element.siblings('li').removeClass('open');
			element.siblings('li').find('li').removeClass('open');
			element.siblings('li').find('ul').slideUp(200);
		}
	});

	//текущей ссылке присваиваем класс current-link
	var url = document.location.toString();
	// $("a").filter(function () {
	// 	return url.indexOf(this.href) != -1;
	// }).addClass("current-link");

	// bottom-slide-panel
	$('.oct-panel-link').on('click', function () {
		if ($(this).parent().hasClass('oct-panel-link-active')) {
			$(this).parent().removeClass('oct-panel-link-active');
			hidePanel();
		} else {
			$('#hide-slide-panel').fadeIn();
			$("#oct-bluring-box").addClass('oct-bluring');
			$("#oct-slide-panel .oct-slide-panel-content").addClass('oct-slide-panel-content-opened');
			$('.oct-slide-panel-heading > .container > div').removeClass('oct-panel-link-active');
			$(this).parent().addClass('oct-panel-link-active');
			$('.oct-slide-panel-item-content').removeClass('oct-panel-active');
			var linkId = $(this).parent()[0].id;
			if (linkId === 'oct-last-seen-link') {
				$('#oct-last-seen-content').toggleClass('oct-panel-active').load('index.php?route=extension/module/oct_page_bar/block_viewed');
			} else if (linkId === 'oct-favorite-link') {
				$('#oct-favorite-content').toggleClass("oct-panel-active").load('index.php?route=extension/module/oct_page_bar/block_wishlist');
			} else if (linkId === 'oct-compare-link') {
				$('#oct-compare-content').toggleClass("oct-panel-active").load('index.php?route=extension/module/oct_page_bar/block_compare');
			} else if (linkId === 'oct-bottom-cart-link') {
				$('#oct-bottom-cart-content').toggleClass("oct-panel-active").load('index.php?route=extension/module/oct_page_bar/block_cart');
			}
		}
	});
	$('#oct-bluring-box, #hide-slide-panel').click(function () {
		hidePanel();
	});


	$('#info-mobile-toggle').on('click', function () {
		$('#info-mobile').slideToggle(50, "swing");
		$('html').toggleClass('noscroll');
	});
	$('#search-mobile-toggle').on('click', function () {
		$('.oct-m-search').slideToggle(50, "swing");
		$('html').toggleClass('noscroll');
	});

	$('#oct-menu-box').css('overflow', 'visible');

});


$(function () {
	var sheight = $(window).height();

/*
	$('.dropdown-menu button').click(function (e) {
		e.stopPropagation();
	});


	var sulheight = $(window).height() - 58;
	var m4 = viewport().width;
	var $fclone = $('.footer-contacts-ul').clone();

	$(".closempanel").click(function () {
		$(".m-panel-box").fadeOut(100,"easeInQuart");
		$('#oct-bluring-box').removeAttr("style");
		$('html').removeClass('noscroll');
	});

	if (m4 <= 992) {
		$('#m-wishlist').append($('#oct-favorite-quantity'));
		$('#m-compare').append($('#oct-compare-quantity'));
		$('#m-cart').append($('#oct-bottom-cart-quantity'));
		$('.product-thumb').bind('touchmove', true);
		$(".product-buttons-box a").removeAttr("data-toggle");
		$('#info-mobile-box').html($fclone);
		$('#info-mobile ul').prepend($('.top-left-info-links li'));
		$('#oct-mobile-search-box, #menu-mobile-box, #info-mobile-box').css("height", sulheight);
		$('#info-mobile .footer-contacts-ul').prepend($('#language'));
		$('#info-mobile .footer-contacts-ul').prepend($('#currency'));
	} else {
		$('ul.menu.flex').flexMenu();
	}

	if (m4 < 768) {
		$('.content-row .left-info').prepend($('.product-header'));
		$('#content').prepend($('.oct-news-panel'));

		$('footer .third-row .h5').on('click', function () {
			$(this).next().slideToggle();
			$(this).toggleClass('open');
		});
	}

	$(window).on('resize', function () {
		var win = $(this);
		if (win.width() <= 992) {
			$('#m-wishlist').append($('#oct-favorite-quantity'));
			$('#m-compare').append($('#oct-compare-quantity'));
			$('#m-cart').append($('#oct-bottom-cart-quantity'));
			$('#info-mobile-box').html($fclone);
			$('#info-mobile ul').append($('.top-left-info-links li.apppli'));
			$('#info-mobile .footer-contacts-ul').prepend($('#language'));
			$('#info-mobile .footer-contacts-ul').prepend($('#currency'));
			$("#menu-mobile-box").prepend($("#menu"));
			var sulheight = $(window).height() - 58;
			$('#oct-mobile-search-box, #menu-mobile-box, #info-mobile-box').css("height", sulheight);
		} else {
			$('#oct-favorite-link .oct-panel-link').append($('#oct-favorite-quantity'));
			$('#oct-compare-link .oct-panel-link').append($('#oct-compare-quantity'));
			$('#oct-bottom-cart-link .oct-panel-link').append($('#oct-bottom-cart-quantity'));
			$('#top-left-links ul').append($('#info-mobile ul li.apppli'));
			$('.language-currency').prepend($('#currency'));
			$('.language-currency').prepend($('#language'));
			$("#oct-menu-box").prepend($("#menu"));
			$('ul.menu.flex').flexMenu();
			var sulheight = $(window).height() - 58;
			$('#oct-mobile-search-box, #menu-mobile-box, #info-mobile-box').css("height", sulheight);
		}

		if (win.width() < 768) {
			$('.content-row .left-info').prepend($('.product-header'));
		} else {
			$('#product-info-right').prepend($('.product-header'));
		}
	});

*/
});

jQuery.browser = {};
(function () {
	jQuery.browser.msie = false;
	jQuery.browser.version = 0;

	if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
		jQuery.browser.msie = true;
		jQuery.browser.version = RegExp.$1;
	}
})();
