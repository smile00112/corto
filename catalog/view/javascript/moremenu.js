(function (factory) {
	if(typeof define === "function" && define.amd) {		
		define(["jquery"], factory);
	} else {		
		factory(jQuery);
	}
}(function ($) {
	var moreObjects = [];
	
	function adjustMoreMenu() {
		$(moreObjects).each(function () {
			$(this).moreMenu({
				"undo" : true
			}).moreMenu(this.options);
		});
	}	

	$(window).resize(function () {		
		adjustMoreMenu();
	});

	$.fn.moreMenu = function (options) {
		var checkMoreObject,
			s = $.extend({
				"threshold": 2,				
				"linkText": "...",				
				"undo": false
			}, options);
		this.options = s;
		checkMoreObject = $.inArray(this, moreObjects);
		if(checkMoreObject >= 0) {
			moreObjects.splice(checkMoreObject, 1);
		} else {
			moreObjects.push(this);
		}
		return this.each(function () {
			var $this = $(this),
				isTopMenu = $this.hasClass("store-horizontal"),
				$items = $this.find("> li"),
				$firstItem = $items.first(),
				$lastItem = $items.last(),
				numItems = $this.find("li").length,
				firstItemTop = Math.floor($firstItem.offset().top),
				firstItemHeight = Math.floor($firstItem.outerHeight(true)),
				$lastChild,
				keepLooking,
				$moreItem,				
				numToRemove,				
				$menu,
				i;
			
			function needsMenu($itemOfInterest) {
				var result = (Math.ceil($itemOfInterest.offset().top) >= (firstItemTop + firstItemHeight)) ? true : false;				
				return result;
			}
			
			if(needsMenu($lastItem) && numItems > s.threshold && !s.undo && $this.is(":visible")) {
				var $popup = !!isTopMenu ? $("<ul class='dropdown-menu more-menu'></ul>") : $("<ul class='submenuMore'></ul>");
				
				for(i = numItems; i > 1; i--) {					
					$lastChild = $this.find("> li:last-child");
					keepLooking = (needsMenu($lastChild));
					if(keepLooking) {
						$lastChild.appendTo($popup);
					} else {
						break;
					}					
				}
				
				if(!!isTopMenu)
					$this.append("<li class='dropdown more'><a href='javascript:void(0)'>" + s.linkText + "</a></li>");
				else
					$this.append("<li class='parentMore'><a href='javascript:void(0)'>" + s.linkText + "</a><span class='arrow'></span></li>");

				$moreItem = !!isTopMenu ? $this.find("> li.more") : $this.find("> li.parentMore");
				if(needsMenu($moreItem)) {
					$this.find("> li:nth-last-child(2)").appendTo($popup);
				}				
				
				$popup.children().each(function (i, li) {
					$popup.prepend(li);
				});
				
				$moreItem.append($popup);
				if(!!isTopMenu) {
					$moreItem.hover(function() {
						var menu = $(this).closest(".store-horizontal"),
							menuWidth = menu.outerWidth(),
							menuLeft = menu.offset().left,
							menuRight = menuLeft + menuWidth,											
							dropdownMenu = $(this).children(".dropdown-menu"),
							dropdownMenuWidth = dropdownMenu.outerWidth(),					
							dropdownMenuLeft = $(this).offset().left,
							dropdownMenuRight = dropdownMenuLeft + dropdownMenuWidth;
						if(dropdownMenuRight > menuRight) {
							dropdownMenu.css({"right": "0"});
						}
						dropdownMenu.stop(true, true).delay(200).fadeIn(150);
					}, function() {
						$(this).children(".dropdown-menu").stop(true, true).delay(200).fadeOut(150);
					});
				} else {
					$moreItem.hover(function() {
						var pos = $(this).position(),
							menu = $(this).closest(".left-menu"),
							dropdownMenu = $(this).children(".submenuMore"),
							dropdownMenuLeft = pos.left + "px",
							dropdownMenuTop = pos.top + $(this).height() + 13 + "px",
							arrow = $(this).children(".arrow"),
							arrowLeft = pos.left + ($(this).width() / 2) + "px",
							arrowTop = pos.top + $(this).height() + 3 + "px";
						if(menu.width() - pos.left < $popup.width()) {
							dropdownMenu.css({"left": "auto", "right": "10px", "top": dropdownMenuTop});
							arrow.css({"left": arrowLeft, "top": arrowTop});
						} else {
							dropdownMenu.css({"left": dropdownMenuLeft, "right": "auto", "top": dropdownMenuTop});
							arrow.css({"left": arrowLeft, "top": arrowTop});
						}
						dropdownMenu.stop(true, true).delay(200).fadeIn(150);
						arrow.stop(true, true).delay(200).fadeIn(150);
					}, function() {
						$(this).children(".submenuMore").stop(true, true).delay(200).fadeOut(150);
						$(this).children(".arrow").stop(true, true).delay(200).fadeOut(150);
					});
				}		
			} else if(s.undo && ($this.find("ul.submenuMore") || $this.find("ul.more-menu"))) {
				$menu = !!isTopMenu ? $this.find("ul.more-menu") : $this.find("ul.submenuMore");
				numToRemove = $menu.find("li").length;
				for(i = 1; i <= numToRemove; i++) {
					$menu.find("> li:first-child").appendTo($this);
				}
				$menu.remove();
				if(!!isTopMenu) {
					$this.find("> li.more").remove();
					$this.find(".dropdown-menu").removeAttr("style");
				} else {
					$this.find("> li.parentMore").remove();
					$this.find(".arrow, .catalog-section-childs").removeAttr("style");
				}
			}
		});
	};
}));