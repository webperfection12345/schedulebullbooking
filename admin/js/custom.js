jQuery(".checkedpromocode").click(function(){
 if(jQuery(this).is(":checked")) {
   alert(jQuery(this).val());
        jQuery(this).attr('checked');
   }else{
       jQuery(this).removeAttr('checked');
   }
});