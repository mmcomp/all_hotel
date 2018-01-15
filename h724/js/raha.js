function search(){
  $("#frm_search").submit();
}
function setEzafe(i,ted){
  if($("#room-select-"+i+"-ezafe").length===1){
    var ht = '';
    for(var j = 0;j <= ted;j++){
      ht += '<option value="'+j+'">'+j+'</option>';
    }
    $("#room-select-"+i+"-ezafe").html(ht);
  }
}
function calcGhimatMain(i){
  if($("#room-select-"+i).length===1 && $("#room-select-"+i+"-ezafe").length===1){
    var tedad = parseInt($("#room-select-"+i).val(),10);
    var zarfiat_ezafe = parseInt($("#room-select-"+i+"-ezafe").data('zarfiat_ezafe'),10);
    if(!isNaN(zarfiat_ezafe) && !isNaN(tedad)){
      setEzafe(i,tedad*zarfiat_ezafe);
    }else{
      setEzafe(i,0);
    }
  }
  calcGhimat();
}
function calcGhimat(){
  var sum = 0;
  $(".room-selection").each(function(id,feild){
    var tedad = parseInt($(feild).val(),10);
    var ghimat = parseInt($(feild).data('ghimat'),10);
    var i = $(feild).prop('id').split('-')[2];
    var ghimat_ezafe = 0;
    if($("#room-select-"+i+"-ezafe").length===1){
      ghimat_ezafe = parseInt($("#room-select-"+i+"-ezafe").data('ghimat_ezafe'),10);
      var ted = parseInt($("#room-select-"+i+"-ezafe").val(),10);
      if(isNaN(ghimat_ezafe) || isNaN(ted)){
        ghimat_ezafe = 0;
      }else{
        ghimat_ezafe = ghimat_ezafe*ted;
      }
    }
    if(!isNaN(tedad) && !isNaN(ghimat)){
      sum += tedad*ghimat;
      sum += ghimat_ezafe;
    }
  });
  $(".ghimat-class").text(sum);
  return sum;
}
function nextPhase(){
  $("#frm1").submit();
}
function nextPhase1(){
  var sum = 0;
  var selected_zarfiat = 0;
  var adult = isNaN(parseInt($("#adult_r").val(),10))?0:parseInt($("#adult_r").val(),10);
  var child = isNaN(parseInt($("#child_r").val(),10))?0:parseInt($("#child_r").val(),10);
  var nafar = adult + child;
  var misezafe = false;
  $(".room-selection").each(function(id,feild){
    var tedad = parseInt($(feild).val(),10);
    var ghimat = parseInt($(feild).data('ghimat'),10);
    var zarfiat = parseInt($(feild).data('zarfiat'),10);
    var ghimat_ezafe = 0;
    var zarfiat_ezafe = 0;
    var i = $(feild).prop('id').split('-')[2];
    var ted = 0;
    var displayed_rcount = parseInt($(feild).data('rcount'),10);
    if($("#room-select-"+i+"-ezafe").length===1){
      ghimat_ezafe = parseInt($(feild).data('ghimat_ezafe'),10);
      zarfiat_ezafe = parseInt($("#room-select-"+i+"-ezafe").data('zarfiat_ezafe'),10);
      ted = parseInt($("#room-select-"+i+"-ezafe").val(),10);
      if(isNaN(ghimat_ezafe) || isNaN(ted) || isNaN(zarfiat_ezafe) || isNaN(displayed_rcount)){
        ghimat_ezafe = 0;
        zarfiat_ezafe = 0;
      }else{
        ghimat_ezafe = ghimat_ezafe*ted;
        if(displayed_rcount<ted){
          misezafe = true;
          ted = 0;
        }
      }
    }

    if(!isNaN(tedad) && !isNaN(ghimat) && !isNaN(zarfiat)){
      sum += tedad*ghimat;
      sum += ghimat_ezafe;
      selected_zarfiat += tedad*zarfiat;
      selected_zarfiat += ted;
    }
  });
  $(".ghimat-class").text(sum);
  console.log(nafar,selected_zarfiat);
  if(selected_zarfiat<nafar || nafar === 0){
    alert('تعداد مسافر با ظرفیت حداکثری اتاق ها هماهنگی ندارد');
  }else if(misezafe){
    alert('تعداد سرویس اضافه اشتباه وارد شده است');
  }else{
    var err = false;
    $("input[required],textarea[required]").each(function(id,feild){
      if($(feild).val().trim()===''){
        $(feild).css('border','dashed red 2px');
        err = true;
      }else{
        $(feild).css('border','');
      }
    });
    if(err){
      alert('لطفا قسمت های قرمز را وارد کنید');
    }else{
      $("#myModal").modal('show');
    }
  }
}
function gotoBank(){
// 				alert('Connecting to BANK!!!!');
  var rnd = Math.random(1000,10000);
  var sum = calcGhimat();
  $("#ResNum").val(rnd);
  $("#Amount").val(sum);
  $("#frm2").submit();
  /*
  $.get("req.php",{req:req},function(result){
    $("#ResNum").val(result.ResNum);
    $("#Amount").val(result.sum);
    $("#frm2").submit();

  });
  */
}
function gotoReserve(){
  $('html, body').animate({
      scrollTop: $("#reserve_section").offset().top
  }, 2000);
}
var id;
$(document).ready(function(){
  $(".pdate").each(function(id,feild){
    id = $(feild).prop('id').trim();
    if(id!==''){
      Calendar.setup({
          inputField: id,
          ifFormat: '%Y/%m/%d',
          dateType: 'jalali'
      });
    }
  });
});
