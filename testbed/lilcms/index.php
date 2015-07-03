<?php 
session_start();
$_SESSION['connection'] = ssh2_connect('localhost', 22);
if(isset($_SESSION['login']) && isset($_SESSION['pwd'])){
	ssh2_auth_password($_SESSION['connection'], $_SESSION['login'], $_SESSION["pwd"]);
}


if(isset($_POST["Connect"]) && isset($_POST["login"]) && isset($_POST["pwd"]) ){
	
	$_SESSION['auth']=false;
	$_SESSION['auth']=ssh2_auth_password($_SESSION['connection'], $_POST["login"], $_POST["pwd"]);
	
	if ($_SESSION['auth']) {
		$_SESSION['login'] = $_POST["login"];
		$_SESSION['pwd'] = $_POST["pwd"];
		$sftp = ssh2_sftp($_SESSION['connection']);

		$_SESSION['content_dir'] ="ssh2.sftp://$sftp/opt/lampp/htdocs/lilcms/content/";
		$_SESSION['publish_dir']="ssh2.sftp://$sftp/opt/lampp/htdocs/lilcms/publish/";
		$_SESSION['content_path']="/opt/lampp/htdocs/lilcms/content/";
		$_SESSION['publish_path']="/opt/lampp/htdocs/lilcms/publish/";

		$_SESSION['user_mode']='subscriber_mode';
		
		$_SESSION['FL']=file_list($_SESSION['publish_dir']);
		$_SESSION['current_path']=$_SESSION['publish_path'];

	} else {
		$authentication="Wrong login or password !!!";
	}
}

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


if(isset($_POST["user_select"]) && isset($_POST["Submit"]) && $_POST["Submit"]=="Select Mode" ) {
	
	$_SESSION['user_mode']=$_POST['user_select'];
	$sftp = ssh2_sftp($_SESSION['connection']);

	if ($_SESSION['user_mode']=='editor_mode') {
		$_SESSION['FL']=file_list($_SESSION['content_dir']);
		$_SESSION['current_path']=$_SESSION['content_path'];
	} elseif ($_SESSION['user_mode']=='subscriber_mode') {
		$_SESSION['FL']=file_list($_SESSION['publish_dir']);
		$_SESSION['current_path']=$_SESSION['publish_path'];
	}

}

if(isset($_POST["select"]) && isset($_POST["Submit"])) {
	if($_POST["select"] && $_POST["Submit"]=="Read") {
		//Readfile
		$cmd='read_cms '.$_SESSION['current_path'].$_POST["select"];

		$stream = ssh2_exec($_SESSION['connection'], $cmd);
		stream_set_blocking($stream, true);
		$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
		$text=stream_get_contents($stream_out);
	}
}

if(isset($_POST["editing"]) && isset($_POST["textfield"]) && isset($_POST["Submit"])) {
  if($_POST["editing"] && $_POST["textfield"] && $_POST["Submit"]=="Write") {
    //Write file
    $filename = $_SESSION['content_path'].$_POST["editing"];
    $text=$_POST["textfield"];
    if (get_magic_quotes_gpc()) {$text = stripslashes($text);}
    ssh2_exec($_SESSION['connection'], 'write_cms '.$filename.' "'.$text.'"');
  }
}

if(isset($_POST["editing"]) && isset($_POST["textfield"]) && isset($_POST["Submit"])) {
  if($_POST["editing"] && $_POST["textfield"] && $_POST["Submit"]=="Publish") {
    //Write file
    $filename = $_SESSION['content_path'].$_POST["editing"];
    ssh2_exec($_SESSION['connection'], 'publish_cms '.$filename);
 }
}

if(isset($_POST["editing"]) && isset($_POST["Submit"])) {
  if($_POST["editing"] && $_POST["Submit"]=="Publish") {
    //Write file
    $filename = $_SESSION['content_path'].$_POST["editing"];
    ssh2_exec($_SESSION['connection'], 'publish_cms '.$filename);
 }
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LilCMS Admin Area</title>
</head>

<body BGCOLOR="#FFFFFF">

<div align="center">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR><TD BGCOLOR="#F2F2F2">
		<TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="5" CELLSPACING="1"> 
		<TR>
		<TD ALIGN="LEFT">
			<B><FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2">Lil'CMS 1.1</FONT></B>
		</TD>
		<TD ALIGN="RIGHT">
			<FONT SIZE="1" FACE="Verdana, Arial, Helvetica, sans-serif">
			for support and tutorial please visit www.lilcms.com
			</FONT>
		</TD>
		</TR> 
		</TABLE>
	</TD></TR>
	</TABLE>
</div>

<form id="formC" name="formC" method="post" action="">
	<TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="5" CELLSPACING="1"> 
	<TR><TD ALIGN="center">
		<?php if(!isset($_SESSION['login'])) : ?>
			<?php if (isset($authentication) ) {echo $authentication;} ?><br/>
			Login : <input type="text" name="login">
			<br />
			Password : <input type="password" name="pwd"><br />
			<INPUT TYPE="submit" NAME="Connect" VALUE="Connect" ID="Connect" />
		<?php elseif(isset($_SESSION)) : ?>
			Welcome, <?php echo $_SESSION['login'];?>. User mode: <?php echo $_SESSION['user_mode'];?>
		<?php endif; ?>
	</TD></TR>
	</TABLE>
</form>

<?php if(isset($_SESSION['login'])) : ?>
<form id="form1" name="form1" method="post" action="">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#CCCCCC">
	<TR><TD>
		<TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="5" CELLSPACING="1"> 
		<TR> <TD WIDTH="250" ROWSPAN="2" ALIGN="center" BGCOLOR="#FFFFFF">
			<FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">Select User Mode:</FONT><BR/>

			<SELECT NAME="user_select" SIZE="2"> 
				<OPTION VALUE="editor_mode">Editor Mode</OPTION>
				<OPTION VALUE="subscriber_mode">Subscriber Mode</OPTION> 
			</SELECT><BR />
			 <INPUT TYPE="submit" NAME="Submit" VALUE="Select Mode" /> <BR /> <BR />

			<FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">Select File to Edit:</FONT><BR/>

			<SELECT NAME="select" SIZE="10"> 
				<?php foreach ($_SESSION['FL'] as $key) {?>
					<OPTION VALUE="<?php echo $key ?>" <?php if (isset($_POST["select"]) && $_POST["select"]==$key) {echo "selected";} ?>>
					<?php echo $key ?>
					</OPTION>
				<?php }?> 
			</SELECT><BR />

			<INPUT TYPE="submit" NAME="Submit" VALUE="Read" /> <BR /> 
		</TD>
		<TD ALIGN="center" BGCOLOR="#FFFFFF">
			<FONT FACE="Arial, Helvetica, sans-serif" SIZE="2">
				Editing File: <?php if (isset($_POST["select"])){ echo $_POST["select"];} ?> 
				<INPUT NAME="editing" TYPE="hidden" ID="editing" VALUE="<?php echo $_POST["select"] ?>" />
			</FONT> <BR /> 

			<TEXTAREA NAME="textfield" COLS="60" ROWS="10"><?php if (isset($text)){ echo $text;} ?></TEXTAREA> <BR/>
			<?php if($_SESSION['user_mode']=='editor_mode') : ?>
			<INPUT NAME="Submit" TYPE="submit" ID="Submit" VALUE="Write" />
			<?php endif; ?>

		</TD></TR> 
		<?php if($_SESSION['user_mode']=='editor_mode') : ?>
		<TR><TD ALIGN="center" BGCOLOR="#FFFFFF">
			<INPUT NAME="Submit" TYPE="submit" ID="Submit" VALUE="Publish" />
		</TD></TR>
		<?php endif; ?>
		</TABLE>
	</TD></TR>
	</TABLE>
</form>

<?php endif; ?>
