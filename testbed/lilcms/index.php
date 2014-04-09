<?php 
/**
 * adapted from original php code at http://www.lilcms.com/
 *
 * (C) @http://www.lilcms.com/
 */

$CPATH="./content/";

function file_list($dir) {
  if (is_dir($dir)) {
    $fd = @opendir($dir);
    while (($part = @readdir($fd)) == true) {
      clearstatcache();
      if ($part != "." && $part != "..") {
            if (!is_dir($part)) {
        $file_array[] = $part;
        }
      }
    }
    if ($fd == true) {
      closedir($fd);
    }
    if (is_array($file_array)) {
      natsort($file_array);
      return $file_array;
    } else {
      return $file_array = NULL;
    }
  } else {
    return false;
  }
}

$FL=file_list($CPATH);

if($_POST["select"] && $_POST["Submit"]=="Read") {
//Readfile
$text=file_get_contents($CPATH.$_POST["select"]);
}

if($_POST["editing"] && $_POST["textfield"] && $_POST["Submit"]=="Write") {
//Write file
$filename = $CPATH.$_POST["editing"];
$text=$_POST["textfield"];
if (get_magic_quotes_gpc()) {$text = stripslashes($text);}
$fp = fopen ($filename, "w");
fwrite($fp, $text);
fclose($fp);
}

if($_POST["editing"] && $_POST["textfield"] && $_POST["Submit"]=="Publish") {
//Write file
$filename = "./publish/".$_POST["editing"];
$text=$_POST["textfield"];
$fp = fopen ($filename, "w");
fwrite($fp, $text);
fclose($fp);
}

if($_POST["editing"] && $_POST["Submit"]=="Publish") {
//Write file
$filename = "./publish/".$_POST["editing"];
$text=trim($_POST["textfield"]);
if (get_magic_quotes_gpc()) {$text = stripslashes($text);}
$fp = fopen ($filename, "w");
fwrite($fp, $text);
fclose($fp);
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>LilCMS Admin Area</title>
</head>

<body BGCOLOR="#FFFFFF">
<div align="center"><TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD BGCOLOR="#F2F2F2"><TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="5" CELLSPACING="1"> 
<TR> <TD ALIGN="LEFT"><B><FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2">Lil' 
CMS 1.1</FONT></B></TD><TD ALIGN="RIGHT"><FONT SIZE="1" FACE="Verdana, Arial, Helvetica, sans-serif">for 
support and tutorial please visit www.lilcms.com</FONT></TD></TR> </TABLE></TD></TR></TABLE></div><form id="form1" name="form1" method="post" action=""><TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#CCCCCC"><TR><TD><TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="5" CELLSPACING="1"> 
<TR> <TD WIDTH="250" ROWSPAN="2" ALIGN="center" BGCOLOR="#FFFFFF"><FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">Select 
File to Edit:</FONT><BR /> <SELECT NAME="select" SIZE="10"> <?php foreach ($FL as $key) {?><OPTION VALUE="<?php echo $key ?>" <?php if ($_POST["select"]==$key) {echo "selected";} ?>><?php if (!is_writable($CPATH.$key)) {echo "*";} ?><?php echo $key ?></OPTION><?php } ?> 
</SELECT> <BR /> <INPUT TYPE="submit" NAME="Submit" VALUE="Read" /> <BR /> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="1">* 
Means write protected </FONT></TD><TD ALIGN="center" BGCOLOR="#FFFFFF"><FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">Editing 
File: <?php echo $_POST["select"] ?> <INPUT NAME="editing" TYPE="hidden" ID="editing" VALUE="<?php echo $_POST["select"] ?>" /></FONT> 
<BR /> <TEXTAREA NAME="textfield" COLS="60" ROWS="10"><?php echo $text ?></TEXTAREA> 
<BR /> <INPUT NAME="Submit" TYPE="submit" ID="Submit" VALUE="Write" /></TD></TR> 
<TR> <TD ALIGN="center" BGCOLOR="#FFFFFF"><FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">Publish
folder is <?php if (!is_writable("../backup")) {echo "protected!";} else {echo "ok.";}?></FONT> 
<BR /> <INPUT NAME="Submit" TYPE="submit" ID="Submit" VALUE="Publish" /></TR>
</TABLE></TD></TR></TABLE></form>
</body>
</html>
