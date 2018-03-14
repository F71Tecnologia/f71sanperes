<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Responsive Calendar Widget that will make your day</title>
    <meta name="distributor" content="Global" />
    <meta itemprop="contentRating" content="General" />
    <meta name="robots" content="All" />
    <meta name="revisit-after" content="7 days" />
    <meta name="description" content="The source of truly unique and awesome jquery plugins." />
    <meta name="keywords" content="slider, carousel, responsive, swipe, one to one movement, touch devices, jquery, plugin, bootstrap compatible, html5, css3" />
    <meta name="author" content="w3widgets.com">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Economica' rel='stylesheet' type='text/css'>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <!-- Respomsive slider -->
    <link href="../css/responsive-calendar.css" rel="stylesheet">
  </head>
  <body>
  
  <div id="popup">
 <a class="fechar" href="#"><img src="../img/fechar.png"></a>
		  <table class="table">
			<thead>
			  <tr>
				<th>Firstname</th>
				<th>Lastname</th>
				<th>Email</th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<td>John</td>
				<td>Doe</td>
				<td>john@example.com</td>
			  </tr>
			  <tr>
				<td>Mary</td>
				<td>Moe</td>
				<td>mary@example.com</td>
			  </tr>
			  <tr>
				<td>July</td>
				<td>Dooley</td>
				<td>july@example.com</td>
			  </tr>
			</tbody>
		  </table>
 
  
  </div>
  <div id="opacidade"></div>
    <div class="container">
      <!-- Responsive calendar - START -->
    	<div class="responsive-calendar">
        <div class="controls">
            <a class="pull-left" data-go="prev"><div class="btn btn-success">Prev</div></a>
            <h4><span data-head-year></span> <span data-head-month></span></h4>
            <a class="pull-right" data-go="next"><div class="btn btn-success">Next</div></a>
        </div><hr/>
        <div class="day-headers">
          <div class="day header">D</div>
          <div class="day header">S</div>
          <div class="day header">T</div>
          <div class="day header">Q</div>
          <div class="day header">Q</div>
          <div class="day header">S</div>
          <div class="day header">S</div>
        </div>
        <div class="days data" data-group="days">
          
        </div>
      </div>
      <!-- Responsive calendar - END -->
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/responsive-calendar.js"></script>
    <script type="text/javascript">
      
	  
	  $(document).ready(function () {
        $(".responsive-calendar").responsiveCalendar({
          time: '2016-01',
          events: {
            "2013-04-30": {"number": 5, "url": "http://w3widgets.com/responsive-slider"},
            "2013-04-26": {"number": 1, "url": "http://w3widgets.com"}, 
            "2013-05-03":{"number": 1}, 
            "2013-06-12": {}}
        });
		
		$(".data").click(function(){
		
			$("#popup").fadeIn(500);
			alert(date);
		
		});
	
	$(".fechar").click(function(){
		
			$("#popup").fadeOut(500);
		
		});
		
      });
    </script>
  </body>
</html>

	