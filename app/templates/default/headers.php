<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<?php

$laurl= ($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; 
$punto=strpos($laurl,'.'); 
$subdomain=substr($laurl,0,$punto); 
$maindomain=str_replace($subdomain.'.','',$laurl); 
if($subdomain) $subdomain='/'.$subdomain;
$elpath=$subdomain.$_SERVER['PHP_SELF'];
$pathseguro='https://'.$laurl.$_SERVER['PHP_SELF'];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<script language="JavaScript" type="text/javascript">
            var _sf_startpt=(new Date()).getTime();
            var root_dir = '<?php echo Conf::RootDir()?>';
            var img_dir = '<?php echo Conf::ImgDir()?>';
        </script>
	<title><?php echo Conf::AppName()?> - <?php echo  $this->titulo ?></title>
	<!-- <?php echo Conf::TimestampDeployCSS()?> -->
	<link rel="stylesheet" type="text/css" href="<?php echo Conf::RootDir()?>/app/templates/<?php echo Conf::Templates()?>/css/deploy/all.1226330411.css" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo Conf::RootDir()?>/app/templates/<?php echo Conf::Templates()?>/css/datepicker.css" />-->

	<? require_once Conf::ServerDir().'/interfaces/fs-pat.js.php'; ?>
	
	<!--<script language="JavaScript" type="text/javascript" src="<?php echo Conf::RootDir()?>/fw/js/src/EditInPlace.js"></script>-->
	<script language="JavaScript" type="text/javascript" src="<?php echo Conf::RootDir()?>/app/deploy/all.1234370043.js"></script> 
	<!--<script language="JavaScript" type="text/javascript" src="<?php echo Conf::RootDir()?>/fw/js/src/datepicker.js"></script>-->
<style type="text/css"> 
	.border_plomo {
		border: 1px solid black;
	}
</style>
<script type="text/javascript">
	Element.addMethods('iframe', {
    doc: function(element) {
        element = $(element);
        if (element.contentWindow)
            return element.contentWindow.document;
        else if (element.contentDocument)
            return element.contentDocument;
        else
            return null;
    },
    $: function(element, frameElement) {
        element = $(element);
        var frameDocument = element.doc();
        if (arguments.length > 2) {
            for (var i = 1, frameElements = [], length = arguments.length; i < length; i++)
                frameElements.push(element.$(arguments[i]));
            return frameElements;
        }
        if (Object.isString(frameElement))
            frameElement = frameDocument.getElementById(frameElement);
        return frameElement || element;
    }
});
</script>
</head>
