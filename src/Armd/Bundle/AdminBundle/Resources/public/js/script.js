$(document).ready(function() {
    $('.span10-statika-header-close').click(function() {
		$('.span10').remove();
		$('.span9').css('width','100%')
	});
	
	$('.d-tab .d-tab-popup').hide();
	$('.d-tab td a').click(function() {
        $(this).next('.d-tab-popup').toggle()
    
    });
    $('#_sys_add_favorive').click(function() {
        $.ajax
            ({
                async: false,
                url: $('#_sys_add_favorive_url').attr('value'),
                success: function () {
                    $('#_sys_add_favorive').hide();
                    $('#_sys_delete_favorit').show();
                }
            })
    })
    $('#_sys_delete_favorit').click(function() {
        $.ajax
            ({
                async: false,
                url: $('#_sys_delete_favorit_url').attr('value'),
                success: function () {
                    $('#_sys_delete_favorit').hide();
                    $('#_sys_add_favorive').show();
                }
            })
    })
	
	    $('.sys_delete_favorit').click(function() {
            _this = $(this);
            $.ajax
                ({
                    async: false,
                    url: $(this).next('._sys_delete_favorit_url').attr('value'),
                    success: function () {
                        $(_this).closest('div.table-wrap').remove();
                        
                    }
                })})
    
	$('.menuTreeWrap select, select.uniform, .filter_hidden select, .controls select:not([multiple])').uniform();
	
	$('.filter_fieldset legend').click(function(){
		$(this).next().slideToggle();
		$(this).toggleClass('active');
		$(this).parent().toggleClass('active');
	});

    /*if ($.datepicker) {
        $.datepicker.setDefaults($.datepicker.regional['ru']);
        $.datepicker.setDefaults({
            monthNamesShort: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
            monthNames: ["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"]
        });
        $('div.datepicker').each(function(i, el){
            var jInput = $(el).find('input:first');
            jInput.datepicker({
                changeMonth: true,
                changeYear: true,
                showOn: "button",
                showButtonPanel: true,
                buttonImage: "/bundles/armdadmin/img/calendar.png"
            }).addClass('first');
            if (jInput.val() == '') {
                jInput.datepicker('setDate', new Date());
            }
        });
    }*/
	
	 if ($.datepicker && $('.datepicker').length > 0) {
			var n = 0;
			
			$('.controls > .datepicker').each(function(){
				
				var newDateInput = $('<input  class="sonata-date" type="text" />')
				
				
				if ( $(this).find('.datepicker').length > 0 ) {
					$(this).find('.datepicker').attr('id','picker'+n);
				} else {
					$(this).attr('id','picker'+n);
				}
				var inDatepicker = $('#picker'+n);
				newDateInput.prependTo(inDatepicker);
				$('input.date_original',inDatepicker).css({'height':0,'width':0,'visibility':'hidden'});
				
				$.datepicker.setDefaults($.datepicker.regional['ru']); 
				$.datepicker.setDefaults({ 
					dateFormat: 'dd MM yy'
				});	

				if ('ru' == 'ru')	 {
					$.datepicker.setDefaults({
						monthNamesShort: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
						monthNames: ["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"]
					})
					
					
				}
				
				$('.sonata-date', inDatepicker).datepicker({
					changeMonth: true,
					changeYear: true,
					showOn: "both",
					showButtonPanel: true,
					altField:'#picker'+n+' input.date_original',
					altFormat:'dd.mm.yy',
					beforeShow: function (input, inst) {
						var offset = $(input).offset();
						var height = $(input).height();
						window.setTimeout(function () {
							inst.dpDiv.css({ top: (offset.top + height + 17) + 'px', left: offset.left + 'px', zIndex: 100 })
						}, 1);
						uni();
					},
					onChangeMonthYear: function(year, month, inst) {
						uni();
					}
				});
				
				var dateStart = $('input.date_original',inDatepicker).val();
				if (dateStart == '') {
					var todaysDate = new Date(),
						tdate = todaysDate.getDate(),
						tmonth = todaysDate.getMonth() + 1,
						tyear = todaysDate.getFullYear(),
						thours = todaysDate.getHours(),
						tmins = todaysDate.getMinutes();
						
					dateStart = tdate+'.'+tmonth+'.'+tyear;
					$('input[type="time"]',inDatepicker).val(thours+':'+tmins);				
				}		
				
				$('.sonata-date', inDatepicker).val($.datepicker.formatDate('dd MM yy', new Date($.datepicker.parseDate('dd.mm.yy', dateStart)))); 
				$('input.date_original', inDatepicker).val(dateStart); 
				n ++;
				
			})
			
			
			
			function uni(){
				setTimeout(function() {
						$('.ui-datepicker-month').uniform();
						$('.ui-datepicker-year').uniform();
						$('.ui-datepicker-header').prepend('<span class="calendar-arrow"></span>');
				},10);
			};
			
        };
    
	
	$('.controls input[type="file"]').each(function(){
		if ($('#fileupload').length <= 0) {
			$(this).uniform();
		}
	})
	
	/*SnapShots*/
	var loading = $('<div class="loading"></div>');
	var error =  $('<div class="error"></div>');	
		
	$('.snap-links a').click( function(){
		var targetLink = $(this),
			url = targetLink.attr('href'),
			snapType = targetLink.attr('class'),
			linksBlock = targetLink.closest('.snap-links'),
			iconsBlock = linksBlock.siblings('.snap-icons'),
			request;
		
		
		linksBlock.after(loading).hide();
		loading.show();
		
		request = $.ajax({
		  url: url,
		  type: "GET"
		});

		request.done(function(msg) {
		  //console.log(msg.status);
		  loading.hide();
		  linksBlock.show();
		  targetLink.hide();
		  if ( snapType == 'snap-to-publish') {
				$('.snap-to-unpublish', linksBlock).show();
				$('.snap-test', iconsBlock).addClass('snap-test-published');
				$('.snap', iconsBlock).addClass('snap-published').css('display','inline-block');
				$('.snap-del', iconsBlock).css('display','inline-block');
		  } else {
				$('.snap-to-publish', linksBlock).show();
				$('.snap-test', iconsBlock).removeClass('snap-test-published');
				$('.snap', iconsBlock).addClass('snap-unpublished').hide();
				$('.snap-del', iconsBlock).hide();
		  }
		});

		request.fail(function(jqXHR, textStatus) {
		  error.html('Ошибка: '+jqXHR.statusText);
		  linksBlock.after(error).show();
		  loading.hide();

		});
				
		return false;
	})

	$('input[type="time"]').attr('required','required');
	
    
    /*JCROP remove link*/
    
    
        $('.controls .left .options').each(function(){
            var removeLi = $('<li class="jcrop-remove"><a href="#" title="Remove"></a></li>'),
                options = $(this);
            
            options.prepend(removeLi);
            
            removeLi.on('click', $(this), function(){
                options.prev().find('.jcrop-holder').remove().end()
                              .find('input').val('');
                return false;
            })
        })
   
  /* HEADER */
  $('.dropdown').each(function() {
    if ($(this).find('li').length > 9) {

      var col = 8;

      var grid = Math.ceil($(this).find('li').length / 8); // Количество колонок

      $(this).addClass('dropdown-full');

      $(this).addClass('dropdown-grid' + grid);

      $(this).find('li').appendTo($(this));

      $(this).find('ul').remove();

      var container = $('<ul></ul>');

      var i = 1;

      while ($(this).children('li').length) {
        container.append($(this).children()[0]);
        if ( i % col == 0 ) {
          $(this).append(container);
          container = $('<ul></ul>');
        }
        if ($(this).children('li').length == 0) {
          $(this).append(container);
        }
        i++;
      }     

    } else {
      $(this).parent().css('position', 'relative');
    }
  });
/*
  $('.nav-item').click(function(e) {
    var $this = $(this);
    var firstClick = false;
    e.stopPropagation();   
    if ($(this).find('.dropdown').css('display') == 'none') {
      $('.nav-item').find('.dropdown').hide();
      $('.nav-item').removeClass('active');
      $this.toggleClass('active');
      $(this).find('.dropdown').slideToggle();   

      $(document).click(function() {
        if (firstClick) {
          if ($('.nav-item').hasClass('active')) {
            $('.active').find('.dropdown').hide();
            $('.nav-item').removeClass('active');
          }
        }
      }); 

      firstClick = true;
    } else {
      $(this).find('.dropdown').slideToggle(function() {
        $this.toggleClass('active');
      });
    }
  });
*/
});