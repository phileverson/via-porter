<script>
	console.log('hi?');
	</script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script type="text/javascript" src="html2canvas.js"></script>

<h2>test!!!</h2>
<a href="test">test</a>

<canvas id="myCanvas" width="240" height="297"
style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.
</canvas>
<script>
	
		console.log("in the window.ready...");

		html2canvas(document.body);

		html2canvas(document.body, {
    onrendered: function(canvas) {
        // canvas is the final rendered <canvas> element
        // var canvasELM = document.getElementById("myCanvas");
        // canvasELM = canvas;
        $("body").append(canvas); 
    // var ctx = canvas.getContext("2d");
    }
});
	
</script>