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

        <div class="col-sm-4">
          <button id="start" type="button" class="btn btn-white-outline btn-lg btn-block" disabled>Start</button>
        </div>

        <div class="col-sm-4">
          <button id="se" type="button" class="btn btn-white-outline btn-lg btn-block">SE</button>
        </div>

        <div class="col-sm-4">
          <button id="call" type="button" class="btn btn-white-outline btn-lg btn-block" disabled>Call</button>
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
          // .then(blink)
          .done(function(){
            $('#start').removeAttr('disabled');
          });
    });
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var context = new AudioContext();
    $(function() {
      $('#start').on('click', function() {
        $.ajax({
          type: 'POST',
          url: '{{ route('start') }}',
          success: function(data) {
            $('#game-token').text(data.game_token);
            $('#start').attr('disabled', 'disabled');
            $('#call').removeAttr('disabled');
            blink()
              .done(function() {
                show('そんじゃ、楽しいビンゴを始めましょうか');
                $.each(data.numbers, function(i, number) {
                  var num = $('.bingo-number i').eq(i);
                  num.data('number', number.id);
                  num.attr('data-number', number.id);
                  num.text(number.label);
                  num.toggleClass('active', number.call_at != null);
                });
              });
          }
        });
      });
      $('#call').on('click', function() {
        var source = context.createBufferSource();
        source.connect(context.destination);
        var request = new XMLHttpRequest();
        request.open('GET', '/sound/nc51953.mp3', true);
        request.responseType = 'arraybuffer';
        request.onload = function(){
          context.decodeAudioData(request.response, function(buffer) {
            source.buffer = buffer;
            $.ajax({
              type: 'POST',
              url: '{{ route('call') }}',
              success: function(data) {
                var $number = $('.bingo-number i[data-number='+data.call_number+']');
                source.start(0);
                setTimeout(function (){
                  show($number.text());
                  $number.addClass('active');
                }, 2000);
              }
            });
          });
        }
        request.send();
      });
      $('#reset').on('click', function() {
        $.ajax({
          type: 'POST',
          url: '{{ route('reset') }}',
          success: function(data) {
            blink()
              .done(function() {
                $('#game-token').text('---');
                $('#start').removeAttr('disabled');
                $('#call').attr('disabled', 'disabled');
              });
          }
        });
      });
      function show(text) {
        var request = new XMLHttpRequest();
        request.open('GET', '/voicetext/'+text, true);
        request.responseType = 'arraybuffer';
        request.withCredentials = true;
        request.send('');
        request.onload = function(){
          context.decodeAudioData(request.response, function(buffer) {
            var source = context.createBufferSource();
            source.buffer = buffer;
            source.connect(context.destination);
            source.start(0);
          });
        }
      }
    });
    // $('#se').on('click');
    </script>
  </body>
</html>
