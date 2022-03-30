//URLから引数に入っている値を渡す処理
function get_param(name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return false;
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}


$(document).on('click','.like_btn',function(e){
  e.stopPropagation();
  var $this = $(this),
      user_id = get_param('user_id'),
      answer_id = get_param('answer_id');
  $.ajax({
      type: 'POST',
      url: '../question_disp.php',
      dataType: 'json',
      data: { user_id: user_id,
              answer_id: answer_id}
  }).done(function(data){
      location.reload();
  }).fail(function() {
    location.reload();
  });
});