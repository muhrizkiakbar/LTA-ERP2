
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Login System - ERP 2.0 Integrated System ERP & SAP | LTA - TAA</title>
  <meta content="ERP 2.0 Integrated System ERP & SAP" name="description" />
  <meta content="IT Team" name="author" />
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/css/loading.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/login/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/login/css/style.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="overlay" style="display:none;">
    <div class="spinner-border text-primary" role="status"></div>
    <br/>
    Loading...
  </div>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-4">
					<div class="login-wrap py-5">
		      	<div class="img d-flex align-items-center justify-content-center" style="background-image: url(assets/images/logo-lta-clear.png);"></div>
		      	{{-- <h3 class="text-center mb-0">Login</h3> --}}
		      	<p style="text-transform: uppercase; color: #000;" class="text-center">ERP LTA Web Version</p>
						<form method="POST" id="login-form"> 
		      		<div class="form-group">
		      			<div class="icon d-flex align-items-center justify-content-center">
                  <span class="icon-user"></span>
                </div>
		      			<input type="text" class="form-control" name="username" placeholder="Username" required>
		      		</div>
	            <div class="form-group">
	            	<div class="icon d-flex align-items-center justify-content-center">
                  <span class="icon-lock"></span>
                </div>
	              <input type="password" class="form-control"  name="password" placeholder="Password" required>
	            </div>
              {{ csrf_field() }}
	            <div class="form-group">
	            	<button type="submit" class="btn form-control btn-primary px-3 oten">Login</button>
	            </div>
	          </form>
	        </div>
				</div>
			</div>
		</div>
	</section>

  <script src="{{ asset('assets/login/js/jquery.min.js') }}"></script>
  {{-- <script src="{{ asset('assets/login/js/popper.js') }}"></script>
  <script src="{{ asset('assets/login/js/bootstrap.min.js') }}"></script> --}}
  <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('assets/login/js/main.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      var swalInit = swal.mixin({
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-light',
          denyButton: 'btn btn-light',
          input: 'form-control'
        }
      });

      $(".oten").click( function(e) {
        e.preventDefault();
        $('#overlay').fadeIn();
        $.ajax({
          url: "{!! route('login') !!}",
          type: "POST",
          data: $("#login-form").serialize(),
          dataType: 'JSON',
          success:function(response){
            if (response.message == "sukses_login") {
              $('#overlay').hide();
								Swal.fire({
								icon: 'success',
								type: 'success',
								title: 'Login Berhasil!',
								text: 'Anda akan di arahkan dalam 3 Detik',
								timer: 1500,
								showCancelButton: false,
								showConfirmButton: false
							}).then (function() {
                window.location.href = "{!! route('dashboard') !!}";
              });
            } else if (response.message == "error_password") {
              $('#overlay').hide();
              Swal.fire({
								icon: 'error',
								type: 'warning',
								title: 'Oops...',
								text: 'Password salah !',
								timer: 1500,
								showCancelButton: false,
								showConfirmButton: false
							});
            } else if (response.message == "error_notfound") {
              $('#overlay').hide();
              Swal.fire({
								icon: 'error',
								type: 'warning',
								title: 'Oops...',
								text: 'User Not Found!',
								timer: 1500,
								showCancelButton: false,
								showConfirmButton: false
							});
            }
          },
        });
      });
    });
  </script>
</body>
</html>