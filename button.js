jQuery(document).ready(function($){
  var mediaUploader;
  var btn="";
  var split="";
	$(document).on("click",".amondi-image-upload",function(event){
			event.preventDefault();
			btn=event.target.id;
			if(btn.includes("upload")){
				split=btn.split("-");
			}
			// If the uploader object has already been created, reopen the dialog
			  if (mediaUploader) {
			  mediaUploader.open();
			  return;
			}
			// Extend the wp.media object
			mediaUploader = wp.media.frames.file_frame = wp.media({
			  title: 'Choose Image',
			  button: {
			  text: 'Choose Image'
			}, multiple: false });

			// When a file is selected, grab the URL and set it as the text field's value
			mediaUploader.on('select', function() {
			  attachment = mediaUploader.state().get('selection').first().toJSON();
			  $('#image-url-'+split[2]).val(attachment.url);
			});
			// Open the uploader dialog
			mediaUploader.open();
	});
});