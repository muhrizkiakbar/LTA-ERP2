<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row mb-3">
          @include('sap.sales.create.form_top')
        </div>
        <div class="row mb-3">
          <label class="col-sm-1 col-form-label fw-bolder">Cari Item</label>
          <div class="col-sm-2">{!! Form::text('ItemName',null,['class'=>'form-control form-control-sm','id'=>'itemName']) !!}</div>
        </div>
        <div class="row">
          <div class="col-12">
            <div id="loadTable"></div>
          </div>
          <hr>
        </div>
        <div class="row">
          @include('sap.sales.create.form_bottom')
        </div>
        <div class="row mb-3">
          <div class="col-sm-2 d-grid">
            <a href="javascript:void(0);" id="save" class="btn btn-md btn-primary">Save</a>
            {{-- <button type="submit" class="btn btn-md btn-secondary">Cancel</button> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepick').datepicker({
    	autoClose:true
    });
    
    loadTable();

    $("#cardName").on('keypress', function (e) {
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        var cardName = $("#cardName").val();
        var csrf = "{{ csrf_token() }}";
        var url = '{{ route('dashboard.search_customer') }}';
        $.ajax({
          url : url,
          data  : {cardName:cardName,_token:csrf},
          type : "POST",
          success: function (ajaxData){
            $("#modalEx").html(ajaxData);
            $("#modalEx").modal('show',{backdrop: 'true'});
          }
        });
      }
    });

    $("#itemName").on('keypress', function (e) {
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        var itemName = $("#itemName").val();
        var cardCode = $("#cardCode").val();
        var csrf = "{{ csrf_token() }}";
        var url = '{{ route('dashboard.searchItem') }}';
        $.ajax({
          url : url,
          data  : {itemName:itemName,cardCode:cardCode,_token:csrf},
          type : "POST",
          success: function (ajaxData){
            if (ajaxData.message == "error") {
              Swal.fire({
                icon: 'error',
                type: 'warning',
                title: 'Oops...',
                text: 'Data customer belum dipilih !',
                timer: 1500,
                showCancelButton: false,
                showConfirmButton: false
              });
            }else {
              $("#modalEx2").html(ajaxData);
              $("#modalEx2").modal('show',{backdrop: 'true'});
            }
          }
        });
      }
    });

    $("#save").click(function(ex) {
      ex.preventDefault();
      $('#overlay').show();
      var cardCode = $("#cardCode").val();
      var docDate = $("#docDate").val();
      var docDueDate = $("#docDueDate").val();
      var numAtCard = $("#numAtCard").val();
      var SalesPersonCode = $("#SalesPersonCode").val();
      var Comments = $("#remarks").val();
      var BplId = $("#BplId").val();
      var Nopol1 = $("#Nopol1").val();
      var Nopol2 = $("#Nopol2").val();
      var csrf = "{!! csrf_token() !!}";
      var url = '{{ route('sales.manual') }}';
      $.ajax({
        url : url,
        data  : {
          cardCode:cardCode,
          docDate:docDate,
          docDueDate:docDueDate,
          numAtCard:numAtCard,
          SalesPersonCode:SalesPersonCode,
          Comments:Comments,
          BplId:BplId,
          Nopol1:Nopol1,
          Nopol2:Nopol2,
          _token:csrf},
        type : "POST",
        dataType : "JSON",
        success: function (response){
          if (response.message=="sukses") {
            var base = "{{ url('/sales/detail/') }}";
            var href = base+"/"+response.docnum;
            $('#overlay').hide();
            Swal.fire({
              icon: 'success',
              type: 'success',
              title: 'Push dokumen berhasil !',
              text: 'Anda akan di arahkan dalam 3 Detik',
              timer: 1500,
              showCancelButton: false,
              showConfirmButton: false
            }).then (function() {
              window.location.href = href
            });
          } else {
            $('#overlay').hide();
            Swal.fire({
              icon: 'error',
              type: 'warning',
              title: 'Oops...',
              text: 'Error, harap cek history !',
              timer: 3000,
              showCancelButton: false,
              showConfirmButton: false
            });
          }
        }
      });
    });
  });

  function loadTable(){
    var url = '{{ route('sales.create_temp_load') }}';
    $.ajax({
      url: url,
      type: "GET",
      success : function(data){
        $('#loadTable').html(data);
      }
    });
  }
</script>