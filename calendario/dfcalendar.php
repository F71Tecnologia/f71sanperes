<?php
  //************************************************************
  //  Script:           DF Calendar
  //  Programmer:       Chris Jackson
  //  Date:             7/2/03
  //  Version:          0.2.57
  //************************************************************
  
  //************************************************************
  //Versions:
  //************************************************************
  // 0.1.13 - Initial Release.
  //
  // 0.2.57 - Added the ability to custom configure the calendar
  //to any month, day or year along with cosmetic variables
  //allowing you to be able to totally customize the 
  //calendar.
  
  //************************************************************
  //External variables:  (ex. calendar.php?mn=12&dy=3&yr=2003)
  //************************************************************
  // tcs = Set Table Cell Spacing
  // tcp = Set Table Cell Padding
  // tbd = Set Table Border
  // mn = Set month (1-12)
  // dy = Set day (1-31)
  // yr = Set year (4 digit year)
  // nm = Set to Next Month (equal to 1)
  // lm = Set to Last Month (equal to 1)
  // mt = Set Calendar to NOT Mark Today (equal to 1)
  // dbh = Set Day Box Height (pixel height of day boxes)
  // dbw = Set Day Box Width (pixel width of day boxes)
  // wbh = Set Weekday Box Height (pixel height of weekday boxes)
  // ctfs = Set Calendar Title Font Size (ex. 10, 12, 14)
  // ctfw = Set Calendar Title Font Weight (ex. bold)
  // ctfc = Set Calendar Title Font Color (ex. 000000)
  // ctbc = Set Calendar Title Background Color (ex. 000000)
  // ctal = Set Calendar Title Alignment (ex. left, right, center)
  // ndfs = Set Normal Day Font Size (ex. 10, 12, 14)
  // ndfc = Set Normal Day Font Color (ex. 000000)
  // ndbc = Set Normal Day Background Color (ex. 000000)
  // ndal = Set Normal Day Alignment (ex. left, right, center)
  // ndva = Set Normal Day Vertical Alignment (ex. top, middle, bottom)
  // mdfc = Set Mark Day Font Color (ex. FF0000)
  // mdbc = Set Mark Day Background Color (ex. 000000)
  // bdbc = Set Blank Day Background Color (ex. 000000)
  // wtfs = Set Weekday Title Font Size (ex. 10, 12, 14)
  // wtfc = Set Weekday Title Font Color (ex. 000000)
  // wtbc = Set Weekday Title Background Color (ex. 000000)
  // wtal = Set Weekday Title Alignment (ex. left, right, center)
  // wtva = Set Weekday Title Vertical Alignment (ex. top, middle, bottom)
  // wk = Set weekday titles to Full names (equal to 1)  

  //Set the months of the year into an array.
  $month[1] = "Janeiro";
  $month[2] = "Fevereiro";
  $month[3] = "Março";
  $month[4] = "Abril";
  $month[5] = "Maio";
  $month[6] = "Junho";
  $month[7] = "Julho";
  $month[8] = "Agosto";
  $month[9] = "Setembro";
  $month[10] = "Outubro";
  $month[11] = "Novembro";
  $month[12] = "Dezembro";

  //Set week title names for the days of the week.
  if ($wk) { 
    //Use full names for week days
    $week[0] = "Domingo";  // Sunday
    $week[1] = "Segunda";  // Monday
    $week[2] = "terça";  // Tuesday
    $week[3] = "Quarta";  // Wednesday
    $week[4] = "Quinta";  // Thursday
    $week[5] = "Sexta";  // Friday
    $week[6] = "Sábado";  // Saturday
  }
  else {
    //Use short names for week days
    $week[0] = "D";  // Sunday
    $week[1] = "S";  // Monday
    $week[2] = "T";  // Tuesday
    $week[3] = "Q";  // Wednesday
    $week[4] = "Q";  // Thursday
    $week[5] = "S";  // Friday
    $week[6] = "S";  // Saturday
  }

  //Get current month, returned in numeric format 1-12
  if (!$mn) { $current_month = date("n"); } else { $current_month = $mn; }

  //Set Calendar to Next Month
  if ($nm) {
    if ($current_month == 12) { $current_month=1; } else { $current_month++; }
	$mt=1;	//Turn off the today mark
  }
  
  //Set Calendar to Last Month
  if ($lm) {
    if ($current_month == 1) { $current_month=12; } else { $current_month--; }
	$mt=1;  //Turn off the today mark
  }

  //Get current day, returned in numeric format 1-31
  if (!$dy) { $current_day = date("j"); } else { $current_day = $dy; }

  //Get current year, returned in four digit numeric format
  if (!$yr) { $current_year = date("Y"); } else { $current_year = $yr; }

  //Get Days in this month, returned in numeric format 28-31
  $days_in_month = date("t", mktime(0,0,0, $current_month, $current_day, $current_year));

  //Get the day of the week that the first day of the month falls on, returned numeric format 0-6
  // (same numbering for days of week as the $week array above)
  $first_day = date("w", mktime(0,0,0,$current_month,1,$current_year));

  //Table Properties
  if (!$tcs) { $tcs = 1; } 		//Cell Spacing
  if (!$tcp) { $tcp = 0; }		//Cell Padding
  if (!$tbd) { $tbd = 0; }		//Border 
  
  //Set Day Box Height
  //If no value is passed, then default to mini calendar settings.
  if (!$dbh) { $dbh = "20"; }
  
  //Set Day Box Width
  //If no value is passed, then default to mini calendar settings.
  if (!$dbw) { $dbw = "25"; }
  
  //Set Weekday Box Height
  //If no value is passed, then default to mini calendar settings.
  if (!$wbh) { $wbh = "0"; }
  
  //Set to NOT mark Today
  if (!$mt) { $mt = 0; }

  //Calendar Title Options
  //********************
  if (!$ctfs) { $ctfs = 16; }			//Set Font Size
  if (!$ctfw) { $ctfw = "bold"; }		//Set Font Weight
  if (!$ctfc) { $ctfc = "#000000"; }	//Set Font Color
  if (!$ctbc) { $ctbc = ""; }			//Set Cell Background Color
  if (!$ctal) { $ctal = "center"; }		//Set Alignment
  
  //Normal Day Options
  //********************
  if (!$ndfs) { $ndfs = 10; }			//Font Size
  if (!$ndfc) { $ndfc = "#000000"; }	//Font Color
  if (!$ndbc) { $ndbc = "#ECF8FF"; }	//Background Color
  if (!$ndal) { $ndal = "center"; }		//Text Alignment
  if (!$ndva) { $ndva = "middle"; }		//Vertical Alignment

  //Mark Day Options
  //********************
  if (!$mdfc) { $mdfc = "#FF0000"; }	//Font Color
  if (!$mdbc) { $mdbc = "#ECF8FF"; }	//Background Color
  
  //Blank Day Options
  //********************
  if (!$bdbc) { $bdbc = "#EAF5FF"; }	//Background Color
  
  //Weekday Title Options
  //********************
  if (!$wtfs) { $wtfs = 10; }			//Font Size
  if (!$wtfc) { $wtfc = "#000000"; }	//Font Color
  if (!$wtbc) { $wtbc = "#FFFFE8"; }	//Background Color
  if (!$wtal) { $wtal = "center"; } 	//Text Alignment
  if (!$wtva) { $wtva = "middle"; }		//Vertical Alignment
  
?>

<html>
  
<head>
<style type="text/css">
<!--
.CalendarTitle {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <?php print $ctfs; ?>px;
	font-weight: <?php print $ctfw; ?>;
	color: <?php print $ctfc; ?>;
	background-color: <?php print $ctbc; ?>;
	text-align: <?php print $ctal; ?>;
}
.NormalDay {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <?php print $ndfs; ?>px;
	color: <?php print $ndfc; ?>;
	background-color: <?php print $ndbc; ?>;
	text-align: <?php print $ndal; ?>;
	vertical-align: <?php print $ndva; ?>;
}
.MarkDay {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <?php print $ndfs; ?>px;
	color: <?php print $mdfc; ?>;
	background-color: <?php print $mdbc; ?>;
	text-align: <?php print $ndal; ?>;
	vertical-align: <?php print $ndva; ?>;
}
.BlankDay {
	background-color: <?php print $bdbc; ?>;
}
.WeekdayTitle {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <?php print $wtfs; ?>px;
	color: <?php print $wtfc; ?>;
	background-color: <?php print $wtbc; ?>;
	text-align: <?php print $wtal; ?>;
	vertical-align: <?php print $wtva; ?>;
}
-->
</style>
</head>
<body>
<table border="<?php print $tbd; ?>" cellpadding="<?php print $tcp; ?>" cellspacing="<?php print $tcs; ?>" width="<?php print $dbw * 7; ?>">
    <tr>
      <td class="CalendarTitle">
<?php

 print "$month[$current_month] $current_year";

?>
      </td>
    </tr>
  </table>
  <table border="<?php print $tbd; ?>" cellpadding="<?php print $tcp; ?>" cellspacing="<?php print $tcs; ?>" width="<?php print $dbw * 7; ?>">
    
  <tr> 
    <?php

  //Loop through days of the week and display the short names above the columns in the calendar
  for ($i=0;$i<7;$i++) {
    print "      <td width=\"$dbw\" height=\"$wbh\" class=\"WeekdayTitle\">$week[$i]</td>\n";
  }

?>
  </tr>
  <?php

  //Set the day counter to the first day, 1.
  $day_counter = 1;

  //This line continues running the loop until all the days of the month have been displayed.
  while($day_counter <= $days_in_month) {
    print "  <tr>\n";

    //Loop through the days of the week to display each day number.
    for($i = 0; $i < 7; $i++) {
      //This line makes sure that a day should be drawn on this particular calendar spot.
	  //To draw a day for this spot, there must be one of two cases true:
	  // 1 - The day counter is still within the amount of days for this month and the first
	  //day is not set to 0 and the first day is set to this spot.
	  // 2 - The day counter is still within the amount of days for this month and the first
	  //day is equal to 0.
	  if ((($day_counter <= $days_in_month) && ($first_day != 0) && ($first_day == $i)) || (($day_counter <= $days_in_month) && ($first_day == 0))) {
        //check to see if the current day counter is today's day
		if (($day_counter == $current_day) && (!$mt)) {
	        print "      <td width=\"$dbw\" height=\"$dbh\" class=\"MarkDay\">$day_counter</td>\n";
		}
		else {
	        print "      <td width=\"$dbw\" height=\"$dbh\" class=\"NormalDay\">$day_counter</td>\n";
		}
        //Resetting first_day tells us that the first day of the month has already been drawn.
        $first_day = 0;
        $day_counter++;
      }
      else {
	    //No day number should be drawn on this calendar spot, therefore draw a blank spot.
        print "      <td width=\"$dbw\" height=\"$dbh\" class=\"BlankDay\">&nbsp;</td>\n";
      }
    }

    print "  </tr>\n";
  }

?>
</table>
</body>
</html>