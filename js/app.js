var photos = ['1.jpg', '2.jpg', '3.jpg', '4.jpg'],
    currPhoto = 0;

$('#up').on('click', function() {
  if(currPhoto == 0) $('#down').removeAttr('disabled');
  currPhoto++;
  $('#old').attr('src', 'images/' + photos[currPhoto]);
  if(currPhoto == photos.length - 1) $(this).attr('disabled', 'disabled');
  $('#curr').text('Изображение ' + (currPhoto + 1) + ' из ' + photos.length);

  $('#result').attr('style', 'display: none');
});

$('#down').on('click', function() {
  if(currPhoto == photos.length - 1) $('#up').removeAttr('disabled');
  currPhoto--;
  $('#old').attr('src', 'images/' + photos[currPhoto]);
  if(currPhoto == 0) $(this).attr('disabled', 'disabled');
  $('#curr').text('Изображение ' + (currPhoto + 1) + ' из ' + photos.length);
});

$('#radius').on('input', function () {
  $(this).prev().text('Радиус окрестности: ' + $(this).val());
});

$('#n').on('input', function () {
  $(this).prev().text('Множитель: ' + $(this).val());
});

$('#submit').on('click', function() {
  $('#result').attr('style', 'display: none');

  $(this).find('.before').attr('style', 'display: none');
  $(this).find('.after').removeAttr('style');
  $(this).attr('disabled', 'disabled');

  var downStatus = true, upStatus = true;
  if(currPhoto == 0) downStatus = false;
  if(currPhoto == (photos.length - 1)) upStatus = false;

  $('#down').attr('disabled', 'disabled');
  $('#up').attr('disabled', 'disabled');
  $('#radius').attr('disabled', 'disabled');
  $('#n').attr('disabled', 'disabled');

  var url = '/ajax.php?photo=' + photos[currPhoto] + '&radius=' + $('#radius').val() + '&n=' + $('#n').val();

  $('#new').on('load', function() {
    $('#result').removeAttr('style');

    if(downStatus) {
      $('#down').removeAttr('disabled');
    }

    if(upStatus) {
      $('#up').removeAttr('disabled');
    }

    $('#radius').removeAttr('disabled');
    $('#n').removeAttr('disabled');

    $('#submit').find('.before').removeAttr('style');
    $('#submit').find('.after').attr('style', 'display: none');
    $('#submit').removeAttr('disabled');
  }).attr('src', url);
});
