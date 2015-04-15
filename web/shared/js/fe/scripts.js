// Gallery block
var siteGallery;
var Gallery = function () {
	var elem,
		frames,
		transport,
		framesControlLeft,
		framesControlRight,
		pagesControls,
		galleryWidth,
		frameWidth,
		frameWidthTmp,
		currentFrame = 0,
		currentPage = 0,
		framesPerPage = 1,
		animationSpeed = 1.2, // animation speed time per pixel
		easing = 'easeOutQuart', // easeInOutBack
		intervalID,
		interval = 6000,
		animated = false,
		automated = false,
		diff,

		changePage = function (v) {
			var destination;
			if (v === 'nextPage') {
				destination = currentFrame + framesPerPage;
				currentPage = currentPage + 1;
			} else if (v === 'prevPage') {
				destination = currentFrame - framesPerPage;
				currentPage = currentPage - 1;
			} else {
				destination = v * framesPerPage;
				currentPage = Math.floor(destination / framesPerPage);
			}
			if (destination === (0 - framesPerPage)) {
				destination = (frames.length - framesPerPage);
				currentPage = Math.floor((frames.length - 1) / framesPerPage);
			} else if (destination < 0) {
				destination = 0;
				currentPage = 0;
			} else if (destination === (frames.length)) {
				destination = 0;
				currentPage = 0;
			} else if (destination > (frames.length - framesPerPage)) {
				destination = (frames.length - framesPerPage);
				currentPage = Math.floor((frames.length - 1) / framesPerPage);
			}
			if (destination !== currentFrame) {
				if (elem.hasClass('_withPagesControls')) {
					pagesControls.removeClass('_animated _active');
					pagesControls.eq(currentPage).addClass('_animated _active');
				}
				currentFrame = destination;
				frameWidth = frames.eq(1).width() + parseInt(frames.eq(1).css('margin-left'), 10);
				changeFrameAnimation(animationSpeed);
			}
		},

		changeFrameAnimation = function (s) {
			animated = true;
			transport.stop(true);
			clearTimeout(intervalID);
			diff = Math.abs(Math.abs(parseInt(transport.css('left'), 10)) - currentFrame * frameWidth);
			transport.animate({
				left: (currentFrame * -frameWidth)
			}, Math.round(s * diff), easing, function () {
				if (elem.hasClass('_withPagesControls')) {
					pagesControls.removeClass('_animated');
				}
				animated = false;
				startGallery();
			});
		},

		startGallery = function () {
			if (automated) {
				intervalID = setTimeout(function () { changePage('nextPage'); }, interval);
			}
		},

		init = function (el) {
			elem = el;
			transport = elem.find('div.galleryWrap > div.row');
			frames = transport.find('> div[class^="col"]');
			galleryWidth = elem.width();
			framesPerPage = elem.data('galleryFramesPerPage') || framesPerPage;
			animationSpeed = elem.data('galleryAnimationTimePerPixel') || animationSpeed;
			easing = elem.data('galleryEasing') || easing;
			interval = elem.data('galleryInterval') || interval;
			if (frames.length > framesPerPage) {
				if (elem.hasClass('_automated')) {
					automated = true;
				}
				if (elem.hasClass('_withControls')) {
					framesControlLeft = $('<div class="galleryControlLeft" />');
					framesControlRight = $('<div class="galleryControlRight" />');
					framesControlLeft.click(function () {
						changePage('prevPage');
					});
					framesControlRight.click(function () {
						changePage('nextPage');
					});
					framesControlLeft.add(framesControlRight).hover(function () {
						$(this).addClass('_hover');
					}, function () {
						$(this).removeClass('_hover');
					}).on('touchend', function () {
						var self = $(this);
						setTimeout(function () { self.removeClass('_hover'); }, 500);
					});
					elem.append(framesControlLeft.add(framesControlRight));
				}
				if (elem.hasClass('_withPagesControls')) {
					pagesControls = '<div class="galleryFrames">';
					for (var i = 0; i <= Math.floor((frames.length - 1) / framesPerPage); i = i + 1) {
						pagesControls = pagesControls + '<span rel=' + i + ' />';
					}
					pagesControls = pagesControls + '</div>';
					elem.append(pagesControls);
					pagesControls = elem.find('div.galleryFrames > span');
					pagesControls.eq(0).addClass('_active');
					pagesControls.click(function () {
						if (!$(this).hasClass('_animated') && !$(this).hasClass('_active')) {
							changePage(parseInt($(this).attr('rel'), 10));
						}
					});
				}
				$(window).resize(function () {
					frameWidthTmp = frames.eq(1).width() + parseInt(frames.eq(1).css('margin-left'), 10);
					if (frameWidth !== frameWidthTmp) {
						frameWidth = frameWidthTmp;
						changeFrameAnimation(0);
					}
				});
			}
			startGallery();
		};

	return {
		init: init
	};

};


$(document).ready(function () {

	$('div.gallery').each(function () {
		siteGallery = new Gallery();
		siteGallery.init($(this));
	});

	var fancyboxElements = $('.fancybox');
	if (fancyboxElements.length > 0) {
		fancyboxElements.fancybox({
			helpers: {
				overlay : {
					locked : false
				}
			}
		});
	}

	$('.sliderHead > span').click(function () {
		$(this).parent().next('.sliderBody').toggle();
	});

	$('div.tabs').each(function () {
		var heads, conts;
		heads = $(this).find('div.tabs_head > ul > li');
		conts = $(this).find('div.tabs_cont');
		heads.click(function (e) {
			e.preventDefault();
			if (!$(this).hasClass('_active')) {
				heads.add(conts).removeClass('_active');
				$(this).add(conts.eq($(this).index())).addClass('_active');
			}
		});
	});

});
