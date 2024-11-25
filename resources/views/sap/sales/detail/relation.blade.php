<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="myExtraLargeModalLabel">{{ $title }}</h5>
    </div>
    <div class="modal-body">
    <div id="wrapper-relation">
      <ul class="tree horizontal">
        <li>
          <div>
            {{ $check['CardCode'] }} <br> {{ $check['CardName'] }}
          </div>
          <ul>
            <li>
              <div>
                <strong>Sales Order</strong> <br>
                {!! $check['order_status']=='O' ? '<i class="feather-unlock"></i>' : '<i class="feather-lock text-danger"></i>' !!}<br><br>
                {{ $check['order_document'] }} <br>
                {{ $check['order_date'] }} <br>
                {{ rupiah($check['order_value']) }}
              </div>
              @if (isset($check['delivery_document']))
              <ul>
                <li>
                  <div>
                    <strong>Delivery</strong> <br>
                    {!! $check['delivery_status']=='O' ? '<i class="feather-unlock"></i>' : '<i class="feather-lock text-danger"></i>' !!}
                    {!! $check['delivery_print']=='Y' ? '<i class="feather-printer text-info"></i>' : '' !!}<br><br>
                    {{ $check['delivery_document'] }} <br>
                    {{ $check['delivery_date'] }} <br>
                    {{ rupiah($check['delivery_value']) }}
                  </div>
                  @if (isset($check['invoice_document']))
                  <ul>
                    <li>
                      <div>
                        <strong>A/R Invoice</strong> <br>
                        {!! $check['invoice_status']=='O' ? '<i class="feather-unlock"></i>' : '<i class="feather-lock text-danger"></i>' !!}<br><br>
                        {{ $check['invoice_document'] }} <br>
                        {{ $check['invoice_date'] }} <br>
                        {{ rupiah($check['invoice_value']) }}
                      </div>
                    </li>
                  </ul>
                  @endif
                </li>
              </ul>   
              @endif
            </li>
          </ul>
        </li>
      </ul>
    </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
    </form>
  </div>
</div>