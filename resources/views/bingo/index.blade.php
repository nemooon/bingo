<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1280, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
  </head>
  <body>

    <div class="container-fluid">

      <div class="row bg-inverse" style="padding: 5px 0;">
        <div class="col-sm-4 text-left">
          Bingooon <small class="text-muted">1.0.0-alpha</small>
        </div>
        <div class="col-sm-4 text-center">
          <small class="text-muted">Game token: <span id="game-token">---</span></small>
        </div>
        <div class="col-sm-4 text-right">
          <button id="reset" type="button" class="btn btn-white-outline btn-sm">Reset</button>
        </div>
      </div>

      <div class="row">

        <div class="bingo-col col-b">
          <h2 class="bingo-label">B</h2>
          <div class="bingo-numbers">
@for ($i = 1; $i <= 15; $i++)
            <div class="bingo-number">
              <i data-number="" class="">{{ $i }}</i>
            </div>
@endfor
          </div>
        </div>
        <div class="bingo-col col-i">
          <h2 class="bingo-label">I</h2>
          <div class="bingo-numbers">
@for ($i = 16; $i <= 30; $i++)
            <div class="bingo-number">
              <i data-number="" class="">{{ $i }}</i>
            </div>
@endfor
          </div>
        </div>
        <div class="bingo-col col-n">
          <h2 class="bingo-label">N</h2>
          <div class="bingo-numbers">
@for ($i = 31; $i <= 45; $i++)
            <div class="bingo-number">
              <i data-number="" class="">{{ $i }}</i>
            </div>
@endfor
          </div>
        </div>
        <div class="bingo-col col-g">
          <h2 class="bingo-label">G</h2>
          <div class="bingo-numbers">
@for ($i = 46; $i <= 60; $i++)
            <div class="bingo-number">
              <i data-number="" class="">{{ $i }}</i>
            </div>
@endfor
          </div>
        </div>
        <div class="bingo-col col-o">
          <h2 class="bingo-label">O</h2>
          <div class="bingo-numbers">
@for ($i = 61; $i <= 75; $i++)
            <div class="bingo-number">
              <i data-number="" class="">{{ $i }}</i>
            </div>
@endfor
          </div>
        </div>

      </div>

      <div class="row" style="padding: 20px 0;">

        <div class="col-sm-1">
          <button id="se" type="button" class="btn btn-white-outline btn-lg btn-block"><i class="fa fa-clone"></i></button>
        </div>

        <div class="col-sm-4 col-sm-offset-7">
          <button id="call" type="button" class="btn btn-white-outline btn-lg btn-block" disabled>Call</button>
        </div>

      </div>

    </div>

    <div class="modal fade" id="result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="jumbotron">
          <h1 id="call-result" class="display-4" style="padding: 6rem 0; font-size: 12rem; font-weight: 600;"></h1>
        </div>
      </div>
    </div>


    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <script>
    function start() {
      var d = $.Deferred();
      var c = 0;
      var id = setInterval(function() {
        var col = $('.bingo-col').eq(c);
        if (col.size()>0) {
          col.addClass('active');
          c++;
        } else {
          clearInterval(id);
          d.resolve();
        }
      }, 200);
      return d.promise();
    }
    function hloop() {
      var d = $.Deferred();
      var i = 0;
      var s = 3;
      var n = $('.bingo-numbers').size();
      var m = s*n;
      var id = setInterval(function() {
        var b = Math.floor(i / s) % n;
        var a = Math.floor(i / m);
        var j = (i % m ? i % m : m) % s + a*s;
        var num = $('.bingo-numbers').eq(b).find('.bingo-number i').eq(j);
        if (num.size()>0) {
          num.toggleClass('active');
          i++;
        } else {
          clearInterval(id);
          d.resolve();
        }
      }, 10);
      return d.promise();
    }
    function vloop() {
      var d = $.Deferred();
      var i = 0;
      var s = 3;
      var h = $('.bingo-numbers').size();
      var m = s * h;
      var id = setInterval(function() {
        var n = Math.floor(i / h) % m % s;
        var k = Math.floor(i / m) * m;
        var j = ((i % m) - (n * h)) * s + n + k;
        var num = $('.bingo-number i').eq(j);
        if (num.size()>0) {
          num.toggleClass('active');
          i++;
        } else {
          clearInterval(id);
          d.resolve();
        }
      }, 10);
      return d.promise();
    }
    function blink() {
      var d = $.Deferred();
      var i = 0;
      $('.bingo-number i').removeClass('active');
      var id = setInterval(function() {
        if (i < 4) {
          $('.bingo-number i').toggleClass('active');
          i++;
        } else {
          clearInterval(id);
          d.resolve();
        }
      }, 150);
      return d.promise();
    }
    $(function() {
        start()
          .then(hloop)
          .then(vloop)
          .done(function(){
            $.ajax({
              type: 'POST',
              url: '{{ route('start') }}',
              success: function(data) {
                $('#game-token').text(data.game_token);
                blink()
                  .done(function() {
                    $.each(data.numbers, function(i, number) {
                      var num = $('.bingo-number i').eq(i);
                      num.data('number', number.id);
                      num.attr('data-number', number.id);
                      num.text(number.label);
                      num.toggleClass('active', number.call_at != null);
                    });
                    $('#call').removeAttr('disabled');
                  });
              }
            });
          });
    });
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var context = new AudioContext();
    var callAudioData;
    function lodeSECall() {
      var request = new XMLHttpRequest();
      request.open('GET', '/sound/nc51953.mp3', true);
      request.responseType = 'arraybuffer';
      request.onload = function(){
        callAudioData = request.response;
      }
      request.send();
    }
    function SECall(callback) {
      context.decodeAudioData(callAudioData, function(buffer) {
        var source = context.createBufferSource();
        source.connect(context.destination);
        source.buffer = buffer;
        source.start(0);
      });
      setTimeout(callback, 2000);
    }
    function SEShow(text) {
      var request = new XMLHttpRequest();
      request.open('GET', '/voicetext/'+text, true);
      request.responseType = 'arraybuffer';
      request.onload = function(){
        context.decodeAudioData(request.response, function(buffer) {
          var source = context.createBufferSource();
          source.connect(context.destination);
          source.buffer = buffer;
          source.start(0);
        });
      }
      request.send();
    }
    $(function() {
      lodeSECall();
      $('#call').on('click', function() {
        $.ajax({
          type: 'POST',
          url: '{{ route('call') }}',
          success: function(data) {
            var $number = $('.bingo-number i[data-number='+data.call_number+']');
            var $col = $number.closest('.bingo-col');
            var $label = $col.find('.bingo-label');
            SECall(function (){
              SEShow($number.text());
              $('#call-result').closest('.jumbotron').css('background-color', $col.css('background-color'));
              $('#call-result').text($label.text()+'-'+$number.text());
              $('#result').modal();
              $number.addClass('active');
            });
          }
        });
      });
      $('#reset').on('click', function() {
        $.ajax({
          type: 'POST',
          url: '{{ route('reset') }}',
          success: function(data) {
            blink()
              .done(function() {
                $('#game-token').text('---');
                $('#call').attr('disabled', 'disabled');
              });
          }
        });
      });
    });
    $('#se').on('click', function() {
      $('#result').modal();
    });
    </script>
  </body>
</html>
