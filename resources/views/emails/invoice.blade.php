
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
  <!-- Meta Tags -->
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Laralink">
  <!-- Site Title -->
  <title>General Purpose Invoice-3</title>
  <link rel="stylesheet" href="{{asset('assets/invoices/css/style.css')}}">
</head>

<body>
  <div class="tm_container">
    <div class="tm_invoice_wrap">
      <div class="tm_invoice tm_style1 tm_type1" id="tm_download_section">
        <div class="tm_invoice_in">
          <div class="tm_invoice_head tm_top_head tm_mb15 tm_align_center">
            <div class="tm_invoice_left">
              <div class="tm_logo"><img src="{{asset('assets/images/logo.png')}}" alt="Logo"></div>
            </div>
            <div class="tm_invoice_right tm_text_right tm_mobile_hide">
              <div class="tm_f50 tm_text_uppercase tm_white_color">Facture</div>
            </div>
            <div class="tm_shape_bg tm_accent_bg tm_mobile_hide"></div>
          </div>
          <div class="tm_invoice_info tm_mb25">
            <div class="tm_card_note tm_mobile_hide"><b class="tm_primary_color">Mode de paiement: </b>Mobile</div>
            <div class="tm_invoice_info_list tm_white_color">
              <p class="tm_invoice_number tm_m0">Facture: <b>{{$order->order_number}}</b></p>
              <p class="tm_invoice_date tm_m0">Date: <b>{{$order->created_at}}</b></p>
            </div>
            <div class="tm_invoice_seperator tm_accent_bg"></div>
          </div>
          <div class="tm_invoice_head tm_mb10">
            <div class="tm_invoice_left">
              <p class="tm_mb2"><b class="tm_primary_color">Client:</b></p>
              <p>
                {{$order->billing_fullname}}<br>
                {{$order->shipping_adresse}}<br>
                {{$order->email}}<br>
                {{$order->phone}}
              </p>
            </div>
            <div class="tm_invoice_right tm_text_right">
              <p class="tm_mb2"><b class="tm_primary_color">Pay To:</b></p>
              <p>
                Nunua <br>
                Goma, Q les volcans, <br>12 av du commerce derriere orange, Goma
                support@nunua.com
              </p>
            </div>
          </div>
          <div class="tm_table tm_style1">
            <div class="">
              <div class="tm_table_responsive">
                <table>
                  <thead>
                    <tr class="tm_accent_bg">
                      <th class="tm_width_3 tm_semi_bold tm_white_color">Article</th>
                      {{-- <th class="tm_width_4 tm_semi_bold tm_white_color">Description</th> --}}
                      <th class="tm_width_2 tm_semi_bold tm_white_color">Prix unitaire</th>
                      <th class="tm_width_1 tm_semi_bold tm_white_color">Quantité</th>
                      <th class="tm_width_2 tm_semi_bold tm_white_color tm_text_right">Prix total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $i = 1
                    @endphp
                    @foreach ($order['details'] as $item)
                    <tr>
                        <td class="tm_width_3">{{$i++}} {{$item['name_produit']}}</td>
                        {{-- <td class="tm_width_4">Six web page designs and three times revision</td> --}}
                        <td class="tm_width_2">{{$item['pivot']['price']}} {{$order->billing_currency}}</td>
                        <td class="tm_width_1">{{$item['pivot']['quantity']}} {{$item['mesure']['name']}}</td>
                        <td class="tm_width_2 tm_text_right">{{$item['pivot']['price'] * $item['pivot']['quantity']}} {{$order->billing_currency}}</td>
                      </tr>
                    @endforeach
                   
                    
                    
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tm_invoice_footer tm_border_top tm_mb15 tm_m0_md">
              <div class="tm_left_footer">
                <p class="tm_mb2"><b class="tm_primary_color">Payment info:</b></p>
                <p class="tm_m0">Credit Card - 236***********928 <br>Amount: $1732</p>
              </div>
              <div class="tm_right_footer">
                <table class="tm_mb15">
                  <tbody>
                    <tr class="tm_gray_bg ">
                      <td class="tm_width_3 tm_primary_color tm_bold">Sous-total</td>
                      <td class="tm_width_3 tm_primary_color tm_bold tm_text_right">{{$order->total}} {{$order->billing_currency}}</td>
                    </tr>
                    <tr class="tm_gray_bg">
                      <td class="tm_width_3 tm_primary_color">Frais de livraison <span class="tm_ternary_color"></span></td>
                      <td class="tm_width_3 tm_primary_color tm_text_right">{{$order->free_shipping}} {{$order->billing_currency}}</td>
                    </tr>
                    <tr class="tm_accent_bg">
                      <td class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_white_color">Total général</td>
                      <td class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_white_color tm_text_right">{{$order->grand_total}} {{$order->billing_currency}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            {{-- <div class="tm_invoice_footer tm_type1">
              <div class="tm_left_footer"></div>
              <div class="tm_right_footer">
                <div class="tm_sign tm_text_center">
                  <img src="assets/img/sign.svg" alt="Sign">
                  <p class="tm_m0 tm_ternary_color">Jhon Donate</p>
                  <p class="tm_m0 tm_f16 tm_primary_color">Accounts Manager</p>
                </div>
              </div>
            </div> --}}
          </div>
          <div class="tm_note tm_text_center tm_font_style_normal">
            <hr class="tm_mb15">
            <p class="tm_mb2"><b class="tm_primary_color">Terms & Conditions:</b></p>
            <p class="tm_m0">All claims relating to quantity or shipping errors shall be waived by Buyer unless made in writing to <br>Seller within thirty (30) days after delivery of goods to the address stated.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
  <script src="{{asset('assets/invoices/js/jquery.min.js')}}"></script>
  <script src="{{asset('assets/invoices/js/jspdf.min.js')}}"></script>
  <script src="{{asset('assets/invoices/js/html2canvas.min.js')}}"></script>
  <script src="{{asset('assets/invoices/js/main.js')}}"></script>
</body>
</html>