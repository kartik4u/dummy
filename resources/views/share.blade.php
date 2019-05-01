
 <html>
 
 <head> 
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <title>Episodic</title>
</head>
 <body class="custom-class">
     
<script>

$(document).ready(function(){

    setTimeout(function(){
      if(navigator.platform == 'iPhone'){
             window.location = 'Episodic://df?story_id=<?php echo isset($story_id) && !empty($story_id) ? $story_id : ''; ?>
              "&story_id="<?php echo isset($episode_id) && !empty($episode_id) ? $episode_id : ''; ?>
             ?>  ';
         }
        
        }, 200);

});
</script>
 </body>
 </html>
