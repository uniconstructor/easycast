(function(){
  var segments = window.location.href.split('?');
  if(segments[1]){
    segments = segments[1].split('&');
    for(var i=0; i<segments.length; i++){
      var segment = segments[i].split('=');
      if(segment[0] == 'utm_source'){
        document.write('<iframe src="https://secure.pikock.com/track_source?utm_source='+ segment[1] +'" style="display:none;"></iframe>');
      }
    }
  }
})();