@if (is_rtl() == 'rtl')
  <script src="/admin/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-rtl.min.js" type="text/javascript"></script>
@else
  <script src="/admin/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
@endif

<script src="/vendor/laravel-filemanager/js/single-stand-alone-button.js"></script>


<script>
		$(document).ready(function()
		{
				$('#clickmewow').click(function()
				{
						$('#radio1003').attr('checked', 'checked');
				});
		})
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $(".emojioneArea").emojioneArea();
  });
</script>

<style>

  .emojionearea .emojionearea-picker.emojionearea-picker-position-top {
  	margin-bottom: -286px!important;
  	right: -14px;
  	z-index: 90000000000000;
  }

  .emojionearea .emojionearea-button.active+.emojionearea-picker-position-top {
      margin-top: 0px!important;
  }
</style>

<script>

  // DELETE ROW FROM DATATABLE
  function deleteRow(url)
  {
      var _token  = $('input[name=_token]').val();

      bootbox.confirm({
          message: '{{__('apps::driver.general.delete_message')}}',
          buttons: {
              confirm: {
                  label: '{{__('apps::driver.general.yes_btn')}}',
                  className: 'btn-success'
              },
              cancel: {
                  label: '{{__('apps::driver.general.no_btn')}}',
                  className: 'btn-danger'
              }
          },

          callback: function (result) {
              if(result){

                  $.ajax({
                      method  : 'DELETE',
                      url     : url,
                      data    : {
                              _token  : _token
                          },
                      success: function(msg) {
                          toastr["success"](msg[1]);
                          $('#dataTable').DataTable().ajax.reload();
                      },
                      error: function( msg ) {
                          toastr["error"](msg[1]);
                          $('#dataTable').DataTable().ajax.reload();
                      }
                  });

              }
          }
      });
  }

  // DELETE ROW FROM DATATABLE
  function deleteAllChecked(url)
  {
      var someObj = {};
      someObj.fruitsGranted = [];

      $("input:checkbox").each(function(){
          var $this = $(this);

          if($this.is(":checked")){
              someObj.fruitsGranted.push($this.attr("value"));
          }
      });

      var ids = someObj.fruitsGranted;

      bootbox.confirm({
          message: '{{__('apps::driver.general.deleteAll_message')}}',
          buttons: {
              confirm: {
                  label: '{{__('apps::driver.general.delete_yes_btn')}}',
                  className: 'btn-success'
              },
              cancel: {
                  label: '{{__('apps::driver.general.delete_no_btn')}}',
                  className: 'btn-danger'
              }
          },

          callback: function (result) {
              if(result){

                  $.ajax({
                      type    : "GET",
                      url     : url,
                      data    : {
                              ids     : ids,
                          },
                      success: function(msg) {

                          if (msg[0] == true){
                              toastr["success"](msg[1]);
                              $('#dataTable').DataTable().ajax.reload();
                          }
                          else{
                              toastr["error"](msg[1]);
                          }

                      },
                      error: function( msg ) {
                          toastr["error"](msg[1]);
                          $('#dataTable').DataTable().ajax.reload();
                      }
                  });

              }
          }
      });
  }

  $(document).ready(function()
  {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        if (start.isValid()&& end.isValid()) {
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            $('input[name="from"]').val(start.format('YYYY-MM-DD'));
            $('input[name="to"]').val(end.format('YYYY-MM-DD'));
        }else{
            $('#reportrange .form-control').val('Without Dates');
            $('input[name="from"]').val('');
            $('input[name="to"]').val('');
        }
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           '{{__('apps::driver.general.date_range.today')}}'         : [moment(), moment()],
           '{{__('apps::driver.general.date_range.yesterday')}}'     : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '{{__('apps::driver.general.date_range.7days')}}'         : [moment().subtract(6, 'days'), moment()],
           '{{__('apps::driver.general.date_range.30days')}}'        : [moment().subtract(29, 'days'), moment()],
           '{{__('apps::driver.general.date_range.month')}}'         : [moment().startOf('month'), moment().endOf('month')],
           '{{__('apps::driver.general.date_range.last_month')}}'    : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
          @if (is_rtl() == 'rtl')
          opens: 'left',
          @endif
          buttonClasses	 : ['btn'],
          applyClass	   : 'btn-primary',
          cancelClass	   : 'btn-danger',
          format 		     : 'YYYY-MM-DD',
          separator		   : 'to',
          locale: {
              applyLabel		    : '{{__('apps::driver.general.date_range.save')}}',
              cancelLabel		    : '{{__('apps::driver.general.date_range.cancel')}}',
              fromLabel			    : 'from',
              toLabel			      : 'to',
              customRangeLabel	: '{{__('apps::driver.general.date_range.custom')}}',
              firstDay: 1
          }
    }, cb);

    cb(start, end);

  });

</script>

<script>

  $('.lfm').filemanager('image');

  $('.delete').click(function() {
      $(this).closest('.form-group').find($('.' + $(this).data('input'))).val('');
      $(this).closest('.form-group').find($('.' + $(this).data('preview'))).html('');
  });

</script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script src="https://js.pusher.com/5.0/pusher.min.js"></script>

<script>

    var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
        cluster: '{{env('PUSHER_APP_CLUSTER')}}',
        forceTLS: true
    });


    pusher.subscribe('{{ config('core.config.constants.DRIVER_DASHBOARD_CHANNEL') }}').bind('{{ config('core.config.constants.DRIVER_DASHBOARD_ACTIVITY_LOG') }}', function (data) {
        $('#dataTable').DataTable().ajax.reload();
        var userId = parseInt('{{ auth()->id() }}');

        $.each(data.activity ,function (index,item){
            if(userId == item.ids){
                openActivity(item);
            }
        });
    });

    var audio = new Audio('{{ url('uploads/media/Porter_Partner_Order__Notification__SMS.mp3') }}');

    function playSound() {
        audio.loop = true;
        audio.play();
    }

    function stopSound() {
        audio.pause();
        audio.currentTime = 0;
    }

    function openActivity(response) {
        playSound();
        // Show
        var showUrl = response.url;

        Swal.fire({
            title: response.description_{{locale()}},
            icon: "success",
            showCloseButton: true,
            showCancelButton: true,
            showDenyButton: true,
            dangerMode: true,
            confirmButtonText: "{{__('order::driver.orders.show.title')}}",
            denyButtonText: "{{__('order::driver.orders.show.accept')}}",
            cancelButtonText: "{{__('order::driver.orders.show.close')}}",
            denyButtonColor: "#7cd1f9"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = showUrl;
            }
            if (result.isDenied) {
                window.location.href = response.accept;
            }
            stopSound();
        });
    }

</script>
