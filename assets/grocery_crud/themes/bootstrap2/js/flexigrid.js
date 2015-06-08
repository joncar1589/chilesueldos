$(function(){
	$('.quickSearchButton').click(function(){
		$(this).closest('.flexigrid').find('.quickSearchBox').slideToggle('normal');
	});

	$('.ptogtitle').click(function(){
		if ($(this).hasClass('vsble')) {
			$(this).removeClass('vsble');
			$(this).closest('.flexigrid').find('.main-table-box').slideDown("slow");
		} else {
			$(this).addClass('vsble');
			$(this).closest('.flexigrid').find('.main-table-box').slideUp("slow");
		}
	});

	var call_fancybox = function(){
		$('.image-thumbnail').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'overlayShow'	:	false
		});
	};

	call_fancybox();
	add_edit_button_listener();
                  displaying_and_pages();

    $('.filtering_form').submit(function(){
            $(".ajax_refresh_and_loading").addClass('loading');
            var ajax_list_info_url = $(this).attr('data-ajax-list-info-url');
            d = new FormData(document.getElementById('filtering_form'));
            d.append('page',crud_pagin);
            $.ajax({
                url:ajax_list_info_url,
                data:d,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success:function(data){
                    data = JSON.parse(data);
                    $('.flexigrid').find('.total_items').html( data.total_results);                    
                    total_results = data.total_results;         
                    displaying_and_pages($('.flexigrid'));
                    d = new FormData(document.getElementById('filtering_form'));
                    d.append('page',crud_pagin);
                    $.ajax({
                        url:ajax_list,
                        data:d,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success:function(data){
                            $('.flexigrid').find('.ajax_list').html(data);
                            $(".ajax_refresh_and_loading").removeClass('loading');
                            call_fancybox();
                            add_edit_button_listener();
                        },
                        error:function(data){                    
                            error_message('Ha ocurrido un error consultando los datos, ERROR: '+data.status);
                            $(".ajax_refresh_and_loading").removeClass('loading');
                        }
                    });
                },
                error:function(data){                    
                    error_message('Ha ocurrido un error consultando la información de los datos, ERROR: '+data.status);
                    $(".ajax_refresh_and_loading").removeClass('loading');
                }
            });

            return false;
    });
        
        $(document).on('keyup','.flexigrid input',function(e){
            if(e.which==13){
                $(document).find('.filtering_form').trigger('submit');
            }
        })

	$('.crud_search').click(function(){
		$(this).closest('.flexigrid').find('.crud_page').val('1');
		$(this).closest('.flexigrid').find('.filtering_form').trigger('submit');
	});

	$('.per_page').change(function(){                
                                    
		$(this).closest('.flexigrid').find('.crud_page').val('1');
		$(document).find('.filtering_form').trigger('submit');
	});

	$('.ajax_refresh_and_loading').click(function(){
                
		$(document).find('.filtering_form').trigger('submit');
	});

	$('.ajax_list').on('click','.field-sorting', function(){
		$(this).closest('.flexigrid').find('.hidden-sorting').val($(this).attr('rel'));

		if ($(this).hasClass('asc')) {
			$(this).closest('.flexigrid').find('.hidden-ordering').val('desc');
		} else {
			$(this).closest('.flexigrid').find('.hidden-ordering').val('asc');
		}

		$(this).closest('.flexigrid').find('.crud_page').val('1');
		$(this).closest('.flexigrid').find('.filtering_form').trigger('submit');
	});

	$('.ajax_list').on('click','.delete-row', function(){
		var delete_url = $(this).attr('href');

		var this_container = $(this).closest('.flexigrid');

		if( confirm( message_alert_delete ) )
		{
			$.ajax({
				url: delete_url,
				dataType: 'json',
				success: function(data)
				{
					if(data.success)
					{
						this_container.find('.ajax_refresh_and_loading').trigger('click');

						success_message(data.success_message);
					}
					else
					{
						error_message(data.error_message);

					}
				}
			});
		}

		return false;
	});

	$('.export-anchor').click(function(){
		var export_url = $(this).attr('data-url');

		var form_input_html = '';
		$.each($(this).closest('.flexigrid').find('.filtering_form').serializeArray(), function(i, field) {
		    form_input_html = form_input_html + '<input type="hidden" name="'+field.name+'" value="'+field.value+'">';
		});

		var form_on_demand = $("<form/>").attr("id","export_form").attr("method","post").attr("target","_blank")
								.attr("action",export_url).html(form_input_html);

		$(this).closest('.flexigrid').find('.hidden-operations').html(form_on_demand);

		$(this).closest('.flexigrid').find('.hidden-operations').find('#export_form').submit();
	});

	$('.print-anchor').click(function(){
		var print_url = $(this).attr('data-url');

		var form_input_html = '';
		$.each($(this).closest('.flexigrid').find('.filtering_form').serializeArray(), function(i, field) {
		    form_input_html = form_input_html + '<input type="hidden" name="'+field.name+'" value="'+field.value+'">';
		});

		var form_on_demand = $("<form/>").attr("id","print_form").attr("method","post").attr("action",print_url).html(form_input_html);

		$(this).closest('.flexigrid').find('.hidden-operations').html(form_on_demand);

		var _this_button = $(this);

		$(this).closest('.flexigrid').find('#print_form').ajaxSubmit({
			beforeSend: function(){
				_this_button.find('.fbutton').addClass('loading');
				_this_button.find('.fbutton>div').css('opacity','0.4');
			},
			complete: function(){
				_this_button.find('.fbutton').removeClass('loading');
				_this_button.find('.fbutton>div').css('opacity','1');
			},
			success: function(html_data){
				$("<div/>").html(html_data).printElement();
			}
		});
	});

	$('.crud_page').numeric();


	if ($('.flexigrid').length == 1) {	 //disable cookie storing for multiple grids in one page
		var cookie_crud_page = readCookie('crud_page_'+unique_hash);
		var cookie_per_page  = readCookie('per_page_'+unique_hash);
		var hidden_ordering  = readCookie('hidden_ordering_'+unique_hash);
		var hidden_sorting  = readCookie('hidden_sorting_'+unique_hash);
		var cookie_search_text  = readCookie('search_text_'+unique_hash);
		var cookie_search_field  = readCookie('search_field_'+unique_hash);

		if(cookie_crud_page !== null && cookie_per_page !== null)
		{
			$('#crud_page').val(cookie_crud_page);
			$('#per_page').val(cookie_per_page);
			$('#hidden-ordering').val(hidden_ordering);
			$('#hidden-sorting').val(hidden_sorting);
			$('#search_text').val(cookie_search_text);
			$('#search_field').val(cookie_search_field);

			if(cookie_search_text !== '')
				$('#quickSearchButton').trigger('click');

			$('#filtering_form').trigger('submit');
		}
	}

});

function displaying_and_pages(this_container)
{
        var crud_page 		= parseInt(crud_pagin, 10) ;
        var per_page	 	= parseInt($(".flexigrid").find('.per_page').val(), 10 );
        var total_items                                = parseInt(total_results, 10 );
        $(".pagination").paging(total_items, {
                format: '[< ncnnn >]',
                page: crud_page,
                perpage: per_page,
                onSelect: function (page) {
                       if(crud_pagin!=page){
                            crud_pagin = page;
                            $(document).find('.filtering_form').trigger('submit');
                        }
                },
                onFormat: function (type) {
                        switch (type) {
                        case 'block': // n and c
                                cl = crud_page==this.value?'active':'';
                                return '<li class="'+cl+'"><a>' + this.value + '</a></li>';
                        case 'next': // >
                                return '<li><a>&gt;</a></li>';
                        case 'prev': // <
                                return '<li><a>&lt;</a></li>';
                        case 'first': // [
                                return '<li><a>&laquo;</a></li>';
                        case 'last': // ]
                                return '<li><a>&raquo;</a></li>';
                        }
                }
        });                      
}
