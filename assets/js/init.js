$(document).ready(function() {

  $('.superform_color_picker').ColorPicker({
  	onSubmit: function(hsb, hex, rgb, el) {
  		$(el).val(hex);
  		$(el).ColorPickerHide();
  	},
  	onBeforeShow: function () {
  		$(this).ColorPickerSetColor(this.value);
  	},
  	onChange: function (hsb, hex, rgb, el) {
  	 $(el).css('backgroundColor', '#' + hex);
  	 $(el).val(hex);
  	}

  })
  .bind('keyup', function(){
  	$(this).ColorPickerSetColor(this.value);
  });

});