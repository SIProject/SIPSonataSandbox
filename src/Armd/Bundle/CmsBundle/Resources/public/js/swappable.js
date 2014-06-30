$(document).ready(function () {
	$('#d-tabs a').click(function(){
		var showTab = $(this).attr('href');
		$('#d-tabs li').removeClass('current');
		$(this).parent().addClass('current');
		$('.d-tab').hide();
		$(showTab).show();
		return false;
	});
	$('.tbl-hover tr').hover(function(){
		$('td',this).css({background:'#ededed'});
	},function(){
		$('td',this).css({background:'#fff'});
	});

         
 $('[data-type=swappable]').each(function() {
        if($(this).attr("id") !== "") {
			new Swappable($(this));
        }
       
    });               
          
});


var draggable_obj;
var close_obj;
var bb;
var Swappable = function(el) {

	var placeholder = $('<div>').addClass('draggable-placeholder').append($('<div>').addClass('draggable-placeholder-wrap'));
	var options = {
		stack: '[data-type=swappable]',
		opacity: 0.9,
		cursorAt: {left: 10, top: 10},
	    helper: 'clone',
	    start: function(event, ui) {
            el.disableSelection();
            draggable_obj = $(this); 
	    	placeholder.insertBefore(el).height(el.height()-18);
	    	el.hide();
            close_obj = $(this).find('.th-close-wrap').clone();
           
	    },
        drag: function(event, ui) {
            el.removeClass('replaceable');
        },        
	    stop: function(event, ui) {
            el.removeClass('replaceable');
	    	placeholder.remove();
	    	el.show();
	    }
	};
	
	if(el.attr("id")) {
		el.draggable(options).find('disible-drag').remove();
	} else {
		el.draggable('disable').find('.table-wrap').append('<div class="disible-drag"></div>');
	}
	
	
	el.closest('[data-type=droppable]').each(function() {

		var item = $(this);
		item.droppable({
			over: function(event, ui) {
				item.find('[data-type=swappable]').addClass('replaceable');
			},
			out: function(event, ui) {
				item.find('[data-type=swappable]').removeClass('replaceable');
			},
			drop: function(event, ui) {
                item.find('[data-type=swappable]').removeClass('replaceable');
                $.ajax
                ({
                    async: false,
                    url: 'copy',
                    data: {
                        source: draggable_obj.attr("id"),
                        target: item.find('[data-type=swappable]').attr("id"),
                        targetContainerId: item.find('.th-wrap').attr("id")
                    },
                    success: function () {
                        bb = true
                        
                    },
                    error: function () {
                        bb = false
                    }
                })
               
                if (bb == true) {
                    var array = draggable_obj.find('.param-drop');
                    var a = array.clone();
                    $(this).find('.param-drop').remove();
                    $( this ).find('.table-bordered tbody tr td:first-child').after(a)
                    $(this).find('.table-bordered .th-wrap').after(close_obj)
                  }                

				
			}
		});
      
	});
};




