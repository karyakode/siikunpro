      <!-- Main Footer -->
      <footer class="main-footer fixed-bottom border-top-0">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
          <?php echo $text_aplication_name; ?>
        </div>
        <!-- Default to the left -->
        <strong><?php echo $text_footer; ?>.</strong>
      </footer>
    </div>
    <!-- ./wrapper -->

    <script type="text/javascript" src="siikunpro/view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="siikunpro/view/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="siikunpro/view/javascript/pace-progress/pace.min.js"></script>
    <script type="text/javascript" src="siikunpro/view/javascript/moment.js"></script>
    <!-- SweetAlert2 -->
    <script src="siikunpro/view/javascript/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="siikunpro/view/javascript/toastr/toastr.min.js"></script>


    <script type="text/javascript" src="siikunpro/view/javascript/siikun.js"></script>

    <script>
      $("#lisensi_batas_waktu").on("change", function() {
        var value = moment(this.value, "YYYY-MM-DD").format( 'Y-M-D' );
        this.setAttribute("data-date",value);
      }).trigger("change");


      // Makes tooltips work on ajax generated content
      $(document).ajaxStop(function() {

      });


      $(document).ready(function() {
      	//Form Submit for IE Browser
      	$('button[type=\'submit\']').on('click', function(e) {
          e.preventDefault();
      		$("form[id*='form-']").submit();
      	});

      	// Highlight any found errors
      	$('.text-danger').each(function() {
      		var element = $(this).parent().parent();

      		if (element.hasClass('form-group')) {
      			element.addClass('has-error');
      		}
      	});

      });



      $('#tombol-siikun button').on('click', function(e) {
      	e.preventDefault();
        $(this).startObfuscator();
      });


      /* Agree to Terms */
      $(document).delegate('.agree', 'click', function(e) {
      	e.preventDefault();

      	$('#modal-agree').remove();

      	var element = this;

      	$.ajax({
      		url: $(element).attr('href'),
      		type: 'get',
      		dataType: 'html',
      		success: function(data) {

            html  = '<div class="modal fade" id="modal-agree">';
            html +=   '<div class="modal-dialog">';
            html +=     '<div class="modal-content">';
            html +=       '<div class="modal-header">';
            html +=         '<h4 class="modal-title">' + $(element).text() + '</h4>';
            html +=         '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            html +=           '<span aria-hidden="true">&times;</span>';
            html +=         '</button>';
            html +=       '</div>';
            html +=       '<div class="modal-body">' + data + '</div>';
            html +=     '</div>';
            html +=   '</div>';
            html += '</div>';

      			$('body').append(html);

      			$('#modal-agree').modal('show');
      		}
      	});
      });


      const Toast = Swal.mixin({
        toast: false,
        //position: 'top-end',
        showConfirmButton: true,
        //timer: 3000
      });



      const TextLoading = 'Loading...';

      $('#siikun').siikun();
      $('#content-dashboard').dashboard();

      //$('#siikun').getFileCopy();
      //$('#siikun').getFileObfuscator();
      /*var update = function(update_id,has_sql,version,step){
        $('#update-siikun').update(update_id,has_sql,version,step);
      },
      cek_update = function(){
        $('#cek-update-siikun').cek_update();
      }*/
    </script>
  </body>
</html>
