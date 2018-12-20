$(function() {

  // Video
  $f("a.video", "/web/js/flowplayer/flowplayer-3.2.18.swf", {
    clip: {
      autoPlay: false
    }
  });

  // Audio
  $f("a.audio", "/web/js/flowplayer/flowplayer-3.2.18.swf", {
    clip: {
      autoPlay: false,
      provider: 'audio'
    },
    plugins: {
      audio: {
        url: "flowplayer.audio-3.2.11.swf"
      }
    }
  });

  // rtmp
  $f("a.rtmp", "/web/js/flowplayer/flowplayer-3.2.18.swf", {
    clip: {
      autoPlay: false,
      provider: 'rtmp'
    },
    plugins: {
      rtmp: {
        url: 'flowplayer.rtmp-3.2.13.swf'
      }
    }
  });


});