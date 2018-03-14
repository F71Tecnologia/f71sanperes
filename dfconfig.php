<?php

  //DFCalendar Configuration
  
  //Check to see if data was entered and add it to the configuration string
  $conf_string = "dfcalendar.php?";
  
  //table settings
  if ($tcs) { $conf_string .= "tcs=". $tcs ."&"; }
  if ($tcp) { $conf_string .= "tcp=". $tcp ."&"; }
  if ($tbd) { $conf_string .= "tbd=". $tbd ."&"; }
  
  //set date
  if ($mn) { $conf_string .= "mn=". $mn ."&"; }
  if ($dy) { $conf_string .= "dy=". $dy ."&"; }
  if ($yr) { $conf_string .= "yr=". $yr ."&"; }
  if ($nm) { $conf_string .= "nm=". $nm ."&"; }
  if ($lm) { $conf_string .= "lm=". $lm ."&"; }
  if ($mt) { $conf_string .= "mt=". $mt ."&"; }
  
  //date box properties
  if ($dbh) { $conf_string .= "dbh=". $dbh ."&"; }
  if ($dbw) { $conf_string .= "dbw=". $dbw ."&"; }
  if ($wbh) { $conf_string .= "wbh=". $wbh ."&"; }
  if ($ctbc) { $conf_string .= "ctbc=". $ctbc ."&"; }
  if ($ndbc) { $conf_string .= "ndbc=". $ndbc ."&"; }
  if ($mdbc) { $conf_string .= "mdbc=". $mdbc ."&"; }
  if ($bdbc) { $conf_string .= "bdbc=". $bdbc ."&"; }
  if ($wtbc) { $conf_string .= "wtbc=". $wtbc ."&"; }
  
  //font properties
  if ($ctfs) { $conf_string .= "ctfs=". $ctfs ."&"; }
  if ($ctfw) { $conf_string .= "ctfw=". $ctfw ."&"; }
  if ($ctfc) { $conf_string .= "ctfc=". $ctfc ."&"; }
  if ($ctal) { $conf_string .= "ctal=". $ctal ."&"; }
  if ($ndfs) { $conf_string .= "ndfs=". $ndfs ."&"; }
  if ($ndfc) { $conf_string .= "ndfc=". $ndfc ."&"; }
  if ($ndal) { $conf_string .= "ndal=". $ndal ."&"; }
  if ($ndva) { $conf_string .= "ndva=". $ndva ."&"; }
  if ($mdfc) { $conf_string .= "mdfc=". $mdfc ."&"; }
  if ($wtfs) { $conf_string .= "wtfs=". $wtfs ."&"; }
  if ($wtfc) { $conf_string .= "wtfc=". $wtfc ."&"; }
  if ($wtal) { $conf_string .= "wtal=". $wtal ."&"; }
  if ($wtva) { $conf_string .= "wtva=". $wtva ."&"; }
  if ($wk) { $conf_string .= "wk=". $wk ."&"; }
  
  //Finished, now truncate the last character of the string.
  $conf_string = substr($conf_string, 0, strlen($conf_string)-1);

  //Setup array to rebuild form
  $ar[0] = $tcs;
  $ar[1] = $tcp;
  $ar[2] = $tbd;
  $ar[3] = $mn;
  $ar[4] = $dy;
  $ar[5] = $yr;
  $ar[6] = $nm;
  $ar[7] = $lm;
  $ar[8] = $mt;
  $ar[9] = $dbh;
  $ar[10] = $dbw;
  $ar[11] = $wbh;
  $ar[12] = $ctbc;
  $ar[13] = $ndbc;
  $ar[14] = $mdbc;
  $ar[15] = $bdbc;
  $ar[16] = $wtbc;
  $ar[17] = $ctfs;
  $ar[18] = $ctfw;
  $ar[19] = $ctfc;
  $ar[20] = $ctal;
  $ar[21] = $ndfs;
  $ar[22] = $ndfc;
  $ar[23] = $ndal;
  $ar[24] = $ndva;
  $ar[25] = $mdfc;
  $ar[26] = $wtfs;
  $ar[27] = $wtfc;
  $ar[28] = $wtal;
  $ar[29] = $wtva;
  $ar[30] = $wk;
?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.Title {  font-family: Geneva, Arial, Helvetica, san-serif; font-size: 16px; font-weight: bold; background-color: #FFE7CE}
.text {  font-family: Geneva, Arial, Helvetica, san-serif; font-size: 10px; text-align: left; vertical-align: middle; background-color: #FFFFE6}
.WhiteBackground {  background-color: #FFFFFF}
.Code {  font-family: Geneva, Arial, Helvetica, san-serif; font-size: 12px; font-weight: bold; color: #0033FF}
.BlankText {  font-family: Geneva, Arial, Helvetica, san-serif; font-size: 10px}
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">
<span class="Title">Calendar Configuration</span><br>
<span class="BlankText">dfcalendar 0.2</span><br>
<span class="BlankText">http://software.dragonforce.net</span><br>
<hr noshade>
<?php include 'dfcalendar.php'; ?>
<br>
Current Calendar: <span class="Code"><?php print $conf_string; ?> <br>
</span> <br>
<span class="BlankText">Just simply copy the blue text above and call the calendar 
script using that code to get the same calendar you see above.</span><br>
<hr noshade>
<form name="form1" method="post" action="">
  <table border="0" cellspacing="0" cellpadding="0" width="760" class="text">
    <tr> 
      <td width="200" valign="top" class="Title" height="19">Table Settings</td>
      <td valign="top" width="50" class="Title">&nbsp;</td>
      <td valign="top" width="499" class="Title">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" height="14">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" height="28">Table Cell Spacing</td>
      <td valign="top"> 
        <input type="text" name="tcs" size="3" maxlength="2" value="<?php print $ar[0]; ?>">
      </td>
      <td valign="top">* The number of pixels between each cell of the table.</td>
    </tr>
    <tr> 
      <td valign="top" height="28">Table Cell Padding</td>
      <td valign="top"> 
        <input type="text" name="tcp" size="3" maxlength="2" value="<?php print $ar[1]; ?>">
      </td>
      <td valign="top">* The number of pixels around the text within the cell 
        of the table.</td>
    </tr>
    <tr> 
      <td valign="top" height="28">Table Border</td>
      <td valign="top"> 
        <input type="text" name="tbd" size="3" maxlength="2" value="<?php print $ar[2]; ?>">
      </td>
      <td valign="top">* The border thickness of the table.</td>
    </tr>
    <tr> 
      <td valign="top" height="14" class="WhiteBackground">&nbsp;</td>
      <td valign="top" class="WhiteBackground">&nbsp; </td>
      <td valign="top" class="WhiteBackground">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" height="14" class="Title">Set Date</td>
      <td valign="top" class="Title">&nbsp; </td>
      <td valign="top" class="Title">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" height="14">&nbsp;</td>
      <td valign="top">&nbsp; </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" height="19">Enter Month</td>
      <td valign="top"> 
        <select name="mn">
          <option value="0"<?php if ($ar[3]=="0") { print " selected"; } ?>>Today</option>
          <option value="1"<?php if ($ar[3]=="1") { print " selected"; } ?>>Jan</option>
          <option value="2"<?php if ($ar[3]=="2") { print " selected"; } ?>>Feb</option>
          <option value="3"<?php if ($ar[3]=="3") { print " selected"; } ?>>Mar</option>
          <option value="4"<?php if ($ar[3]=="4") { print " selected"; } ?>>Aprl</option>
          <option value="5"<?php if ($ar[3]=="5") { print " selected"; } ?>>May</option>
          <option value="6"<?php if ($ar[3]=="6") { print " selected"; } ?>>June</option>
          <option value="7"<?php if ($ar[3]=="7") { print " selected"; } ?>>July</option>
          <option value="8"<?php if ($ar[3]=="8") { print " selected"; } ?>>Aug</option>
          <option value="9"<?php if ($ar[3]=="9") { print " selected"; } ?>>Sept</option>
          <option value="10"<?php if ($ar[3]=="10") { print " selected"; } ?>>Oct</option>
          <option value="11"<?php if ($ar[3]=="11") { print " selected"; } ?>>Nov</option>
          <option value="12"<?php if ($ar[3]=="12") { print " selected"; } ?>>Dec</option>
        </select>
      </td>
      <td valign="top">* Select the month you wish to view, TODAY will display 
        today's month.</td>
    </tr>
    <tr> 
      <td valign="top" height="28">Enter Day</td>
      <td valign="top"> 
        <input type="text" name="dy" size="3" maxlength="2" value="<?php print $ar[4]; ?>">
      </td>
      <td valign="top">* Enter in the day number that you wish to mark on the 
        calendar. </td>
    </tr>
    <tr> 
      <td valign="top">Enter Year</td>
      <td valign="top"> 
        <input type="text" name="yr" size="5" maxlength="4" value="<?php print $ar[5]; ?>">
      </td>
      <td valign="top">* Enter the year you wish to display the calendar in.</td>
    </tr>
    <tr> 
      <td valign="top">Display Next Month</td>
      <td valign="top"> 
        <input type="checkbox" name="nm" value="1"<?php if ($ar[6]) { print " checked"; } ?>>
      </td>
      <td valign="top">* Check the box to display next month's calendar.</td>
    </tr>
    <tr> 
      <td valign="top">Display Last Month</td>
      <td valign="top"> 
        <input type="checkbox" name="lm" value="1"<?php if ($ar[7]) { print " checked"; } ?>>
      </td>
      <td valign="top">* Check the box to display last month's calendar.</td>
    </tr>
    <tr> 
      <td valign="top">Do Not Mark Today</td>
      <td valign="top"> 
        <input type="checkbox" name="mt" value="1"<?php if ($ar[8]) { print " checked"; } ?>>
      </td>
      <td valign="top">* Check the box if you do not want today's date to be marked 
        on the calendar.</td>
    </tr>
    <tr> 
      <td valign="top" class="WhiteBackground">&nbsp;</td>
      <td valign="top" class="WhiteBackground">&nbsp; </td>
      <td valign="top" class="WhiteBackground">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" class="Title">Date Box Properties</td>
      <td valign="top" class="Title">&nbsp; </td>
      <td valign="top" class="Title">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Day Box Height</td>
      <td valign="top"> 
        <input type="text" name="dbh" size="4" maxlength="3" value="<?php print $ar[9]; ?>">
      </td>
      <td valign="top">* Enter the height in pixels of the Day Boxes.</td>
    </tr>
    <tr> 
      <td valign="top">Day Box Width</td>
      <td valign="top"> 
        <input type="text" name="dbw" size="4" maxlength="3" value="<?php print $ar[10]; ?>">
      </td>
      <td valign="top">* Enter the width in pixels of the Day Boxes.</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Box Height</td>
      <td valign="top"> 
        <input type="text" name="wbh" size="4" maxlength="3" value="<?php print $ar[11]; ?>">
      </td>
      <td valign="top">* Enter the height in pixels of the Weekday Boxes. (The 
        Width is used from the Day Box Width)</td>
    </tr>
    <tr> 
      <td valign="top">Month &amp; Year Background Color</td>
      <td valign="top"> 
        <input type="text" name="ctbc" size="8" maxlength="6" value="<?php print $ar[12]; ?>">
      </td>
      <td valign="top">* Enter a color in 6 digit Hex Code. (EX. 000000 = Black, 
        FFFFFF = White)</td>
    </tr>
    <tr> 
      <td valign="top">Normal Day Background Color</td>
      <td valign="top"> 
        <input type="text" name="ndbc" size="8" maxlength="6" value="<?php print $ar[13]; ?>">
      </td>
      <td valign="top">* Check the box if you do not want today's date to be marked 
        on the calendar.</td>
    </tr>
    <tr> 
      <td valign="top">Marked Day Background Color</td>
      <td valign="top"> 
        <input type="text" name="mdbc" size="8" maxlength="6" value="<?php print $ar[14]; ?>">
      </td>
      <td valign="top">* Check the box if you do not want today's date to be marked 
        on the calendar.</td>
    </tr>
    <tr> 
      <td valign="top">Blank Day Background Color</td>
      <td valign="top"> 
        <input type="text" name="bdbc" size="8" maxlength="6" value="<?php print $ar[15]; ?>">
      </td>
      <td valign="top">* Check the box if you do not want today's date to be marked 
        on the calendar.</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Background Color</td>
      <td valign="top"> 
        <input type="text" name="wtbc" size="8" maxlength="6" value="<?php print $ar[16]; ?>">
      </td>
      <td valign="top">* Check the box if you do not want today's date to be marked 
        on the calendar.</td>
    </tr>
    <tr> 
      <td valign="top" class="WhiteBackground">&nbsp;</td>
      <td valign="top" class="WhiteBackground">&nbsp; </td>
      <td valign="top" class="WhiteBackground">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top" class="Title">Font Properties</td>
      <td valign="top" class="Title">&nbsp; </td>
      <td valign="top" class="Title">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Month &amp; Year Font Size</td>
      <td valign="top">
        <input type="text" name="ctfs" size="3" maxlength="2" value="<?php print $ar[17]; ?>">
      </td>
      <td valign="top">* Enter the font size for the Month &amp; Year Title.</td>
    </tr>
    <tr> 
      <td valign="top">Month &amp; Year Font Weight</td>
      <td valign="top">
        <select name="ctfw">
          <option value="0"<?php if ($ar[18]=="0") { print " selected"; } ?>>None</option>
          <option value="normal"<?php if ($ar[18]=="normal") { print " selected"; } ?>>Normal</option>
          <option value="bold"<?php if ($ar[18]=="bold") { print " selected"; } ?>>Bold</option>
          <option value="bolder"<?php if ($ar[18]=="bolder") { print " selected"; } ?>>Bolder</option>
          <option value="lighter"<?php if ($ar[18]=="lighter") { print " selected"; } ?>>Lighter</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Month &amp; Year Font Color</td>
      <td valign="top">
        <input type="text" name="ctfc" size="8" maxlength="6" value="<?php print $ar[19]; ?>">
      </td>
      <td valign="top">* Enter the font color for the Month &amp; Year title. 
        (EX. 000000 = Black, FFFFFF = White) </td>
    </tr>
    <tr> 
      <td valign="top">Month &amp; Year Alignment</td>
      <td valign="top">
        <select name="ctal">
          <option value="0"<?php if ($ar[20]=="0") { print " selected"; } ?>>None</option>
          <option value="left"<?php if ($ar[20]=="left") { print " selected"; } ?>>Left</option>
          <option value="right"<?php if ($ar[20]=="right") { print " selected"; } ?>>Right</option>
          <option value="center"<?php if ($ar[20]=="center") { print " selected"; } ?>>Center</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Normal Day Font Size</td>
      <td valign="top">
        <input type="text" name="ndfs" size="3" maxlength="2" value="<?php print $ar[21]; ?>">
      </td>
      <td valign="top">* Enter the font size for the Normal Day numbers.</td>
    </tr>
    <tr> 
      <td valign="top">Normal Day Font Color</td>
      <td valign="top">
        <input type="text" name="ndfc" size="8" maxlength="6" value="<?php print $ar[22]; ?>">
      </td>
      <td valign="top">* Enter the font color for the Normal Day numbers. (EX. 
        000000 = Black, FFFFFF = White)</td>
    </tr>
    <tr> 
      <td valign="top">Normal Day Alignment</td>
      <td valign="top">
        <select name="ndal">
          <option value="0"<?php if ($ar[23]=="0") { print " selected"; } ?>>None</option>
          <option value="left"<?php if ($ar[23]=="left") { print " selected"; } ?>>Left</option>
          <option value="right"<?php if ($ar[23]=="right") { print " selected"; } ?>>Right</option>
          <option value="center"<?php if ($ar[23]=="center") { print " selected"; } ?>>Center</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Normal Day Vertical Alignment</td>
      <td valign="top">
        <select name="ndva">
          <option value="0"<?php if ($ar[24]=="0") { print " selected"; } ?>>None</option>
          <option value="top"<?php if ($ar[24]=="top") { print " selected"; } ?>>Top</option>
          <option value="middle"<?php if ($ar[24]=="middle") { print " selected"; } ?>>Middle</option>
          <option value="bottom"<?php if ($ar[24]=="bottom") { print " selected"; } ?>>Bottom</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Marked Day Font Color</td>
      <td valign="top">
        <input type="text" name="mdfc" size="8" maxlength="6" value="<?php print $ar[25]; ?>">
      </td>
      <td valign="top">* Enter the font color for the Marked Day. (EX. 000000 
        = Black, FFFFFF = White)</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Font Size</td>
      <td valign="top">
        <input type="text" name="wtfs" size="3" maxlength="2" value="<?php print $ar[26]; ?>">
      </td>
      <td valign="top">* Enter the font size for the Weeday Title.</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Font Color</td>
      <td valign="top">
        <input type="text" name="wtfc" size="8" maxlength="6" value="<?php print $ar[27]; ?>">
      </td>
      <td valign="top">* Enter the font color for the Weekday Title. (EX. 000000 
        = Black, FFFFFF = White)</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Alignment</td>
      <td valign="top">
        <select name="wtal">
          <option value="0"<?php if ($ar[28]=="0") { print " selected"; } ?>>None</option>
          <option value="left"<?php if ($ar[28]=="left") { print " selected"; } ?>>Left</option>
          <option value="right"<?php if ($ar[28]=="right") { print " selected"; } ?>>Right</option>
          <option value="center"<?php if ($ar[28]=="center") { print " selected"; } ?>>Center</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Vertical Alignment</td>
      <td valign="top">
        <select name="wtva">
          <option value="0"<?php if ($ar[29]=="0") { print " selected"; } ?>>None</option>
          <option value="top"<?php if ($ar[29]=="top") { print " selected"; } ?>>Top</option>
          <option value="middle"<?php if ($ar[29]=="middle") { print " selected"; } ?>>Middle</option>
          <option value="bottom"<?php if ($ar[29]=="bottom") { print " selected"; } ?>>Bottom</option>
        </select>
      </td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">Weekday Title Full Names</td>
      <td valign="top"> 
        <input type="checkbox" name="wk" value="1"<?php if ($ar[30]=="1") { print " checked"; } ?>>
      </td>
      <td valign="top">* Set Weekday Titles to Full Weekday names.</td>
    </tr>
    <tr> 
      <td height="0"></td>
      <td></td>
      <td></td>
    </tr>
  </table>

<br>
  <input type="submit" name="Submit" value="Submit">
  <input type="reset" name="Submit2" value="Reset">
  <br>
  <span class="BlankText"><a href="config.php">Click Here</a> to clear calendar 
  back to default settings</span><br>
</form>
<br>
</body>
</html>
