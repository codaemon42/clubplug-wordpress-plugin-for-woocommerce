
 (function($) {
    $('#order_review_heading').html('Your Due Review');
    $('#see-members-list').click(()=>{

        $('#members-list-section').show();
        
        $('body,html').animate({ scrollTop: "800px" }, 1000);
    });
    
    $("button[name='naims_confirm_amount_btn']").click(function(){
        $("button[name='naims_confirm_amount_btn']").css("transform", "scale(0.8,0.8)"); 
		setTimeout(function(){ 
                $("button[name='naims_confirm_amount_btn']").css("transform", "scale(1,1)");
             }, 200);
        let amount = $('#naims_checkout_amount').val();
        if(amount < 999){
            $("button[name='woocommerce_checkout_place_order']").attr('style', 'pointer-events: none');
            let toast = 
            $(".payment_section").prepend(`
                <p style="background:#ff0000; color:#fff; padding:10px; border-radius:5px; box-shadow: 2px 2px 6px 2px #323232; margin:10px; text-align:center" class="naims_toast"> You have to pay more than 10 Tk </p>
            `);
            setTimeout(function(){ 
                $(".naims_toast").remove();
             }, 3000)
        }
        else{
            $("button[name='woocommerce_checkout_place_order']").attr('style', 'pointer-events: auto');
            $("button[name='woocommerce_checkout_place_order']").html('Pay Now');
        }
    });
    console.log('input');
    // ================js ui=========================
    var inputs = document.querySelectorAll('.file-input')

    for (var i = 0, len = inputs.length; i < len; i++) {
      customInput(inputs[i])
    }
    
    function customInput (el) {
      const fileInput = el.querySelector('[type="file"]')
      const label = el.querySelector('[data-js-label]')
      
      fileInput.onchange =
      fileInput.onmouseout = function () {
        if (!fileInput.value) return
        
        var value = fileInput.value.replace(/^.*[\\\/]/, '')
        el.className += ' -chosen'
        label.innerText = value
      }
    }
    // ================show image=========================
    // function readURL(input) {
    //     console.log(input);
    //     if (input.files && input.files[0]) {
    //         var reader = new FileReader();

    //         reader.onload = function (e) {
    //             $('#blah')
    //                 .attr('src', e.target.result)
    //                 .width(150)
    //                 .height(200);
    //         };

    //         reader.readAsDataURL(input.files[0]);
    //     }
    // }
    $("input[name=image]").change(function(e) {
    
            var file = e.originalEvent.srcElement.files[0];
    
            var imgref = $(".naims-photo-append");
           
            var reader = new FileReader();
            reader.onloadend = function() {
                 
                 imgref.append(`
                 <h5 style='color:#0bb52f; text-align:center;'> Hit the "<span style='color: #ee1c1f '>Save changes</span>" button below to upload profile photo successfully </h5>
                 <img src='${reader.result}' style='width:100px;box-shadow: 1px 1px 30px 2px #33333340;' />`);
            }
            reader.readAsDataURL(file);
            //imgref.after(img);

    });

    // scrolling


      
   
      
  })( jQuery );