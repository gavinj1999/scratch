<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "gavin.jones@rising5th.co.uk" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "4d95ad" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'0E0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxBog0MIQyOgQgiYlMEWlgdHR0EEESC2gVaWBtCISpAzspaunUsKWrIkOzkNyHpg5FTISAHdjcgs3NAxV+VIRY3AcAtz3KO9jLiwAAAAAASUVORK5CYII=',
			'5931' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGVqRxQIaWFtZGx2mooqJNDo0BIQiiwUGAMUaHWB6wU4Km7Z0adbUVUtR3NfKGIikDirGADIP1d5WFgwxkSlgt6CIsQaA3RwaMAjCj4oQi/sADKXNlhKsxBgAAAAASUVORK5CYII=',
			'B467' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGUNDkMQCpjBMZXR0aBBBFgOqYm1AE5vC6MoKopHcFxq1dOnSqatWZiG5L2CKSCuro0MrA4p5oqGuIJtQ7WhlbQgIYEB1Syujo6MDFjejiA1U+FERYnEfAC6WzNAvNCe9AAAAAElFTkSuQmCC',
			'FF4A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHVqRxQIaRBoYWh2mOqCLTXUICEAXC3R0EEFyX2jU1LCVmZlZ05DcB1LH2ghXhxALDQwNQTcPizpixAYq/KgIsbgPAFIUzcBDUDMcAAAAAElFTkSuQmCC',
			'0ADB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUMdkMRYAxhDWBsdHQKQxESmsLayNgQ6iCCJBbSKNLoCxQKQ3Be1dNrK1FWRoVlI7kNTBxUTDXVFM09kCkSdCIpbgGJobmF0AIqhuXmgwo+KEIv7ADTyzIVa1nnOAAAAAElFTkSuQmCC',
			'FF6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUMdkMQCGkQaGB0dHQLQxFgbHB1EMMQYYerATgqNmhq2dOrK0Cwk94HVYTUvEIt5mGLY3MKA5uaBCj8qQizuAwCbbsySTi2izwAAAABJRU5ErkJggg==',
			'D966' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtcHQQwBBjdEB2X9TSpUtTp65MzUJyX0ArY6CroyOaeQxAvYEOIihiLJhiWNyCzc0DFX5UhFjcBwAFos2wdkxzVQAAAABJRU5ErkJggg==',
			'6CAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIYGIImJTGFtdAhldEBWF9Ai0uDo6Igq1iDSwNoQCBMDOykyatqqpasiQ7OQ3BcyBUUdRG8rUCwUU8wVTR3ILehiIDcDzUNx80CFHxUhFvcBAF0Oy8K7ouYAAAAAAElFTkSuQmCC',
			'6951' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHVqRxUSmsLayNjBMRRYLaBFpdG1gCEURawCKTWWA6QU7KTJq6dLUzKylyO4LmcIY6NAQgGIHkNeIKcYCtCMAwy2MjqjuA7kZ6JLQgEEQflSEWNwHAMVKzLaCLBl0AAAAAElFTkSuQmCC',
			'2910' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QwQ2AMAhFIbEbMBBugEm7RKeoh26A3aAHndLGE1WPGuXfXj7hBdguk+BPecXPCXpQyJaRugweFjZMMs2jBxG73RgrMlm/UmssayzWT3AyvSPIMJ+ZS0Nj/Q1KzUV7lxDQY+DO+av/PZgbvx0F7stT3eNiVAAAAABJRU5ErkJggg==',
			'0D51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHVqRxVgDRFpZGximIouJTBFpdG1gCEUWC2gFik1lgOkFOylq6bSVqZlZS5HdB1LnACTR9aKLQewIwHALoyOq+0BuBrokNGAQhB8VIRb3AQBmOMxvKWGtYwAAAABJRU5ErkJggg==',
			'7E19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBZtFWlgCGEICEATYwxhdBBBFpsC5E2Bi0HcFDU1bNW0VVFhSO4DqwDagayXtQEs1oAsJgIRQ7EjACKG4paABtFQxlAHVDcPUPhREWJxHwAuusrSf3F4ogAAAABJRU5ErkJggg==',
			'D056' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHaY6IIkFTGEMYW1gCAhAFmtlbWVtYHQQQBETaXSdyuiA7L6opdNWpmZmpmYhuQ+kzqEhEM08sJiDCIYdaGJAtzA6OqDoBbmZIZQBxc0DFX5UhFjcBwAf3sz4z4m9ewAAAABJRU5ErkJggg==',
			'C65E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHUMDkMREWllbWRsYHZDVBTSKNGKINYg0sE6Fi4GdFLVqWtjSzMzQLCT3BTSItjI0BKLrbXRAFwPa4YomBnILo6MjihjIzQyhjChuHqjwoyLE4j4AmlzKL8Vxe5cAAAAASUVORK5CYII=',
			'9218' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDCEBCAJBbQKtLoGMLoIIIixtDoMAWuDuykaVNXLV01bdXULCT3sboCbZiCah5DK0MAwxRU8wRaGR3QxYBuaUDXyxogGuoY6oDi5oEKPypCLO4DADs7y1+RHnBEAAAAAElFTkSuQmCC',
			'158E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUMDkMRYHUQaGB0dHZDViQLFWBsCHVD1ioQgqQM7aWXW1KWrQleGZiG5j9GBodERzTyQmCumeVjEWFsx3BLCGILu5oEKPypCLO4DAIxUxvzSyFAVAAAAAElFTkSuQmCC',
			'3811' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7RAMYQximMLQiiwVMYW1lCGGYiqKyVaTRMYQhFEUMpA6hF+yklVErw1ZNW7UUxX2o6uDmORAhFoBFL8jNjKEOoQGDIPyoCLG4DwAlZ8uETuQSZgAAAABJRU5ErkJggg==',
			'DEC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCHUNDkMQCpog0MDoENIggi7WKNLA2CGARA9JI7otaOjVs6apVK7OQ3AdV18qAqXcKpphAAAOGWwIdsLgZRWygwo+KEIv7AHJZzO5AXRmUAAAAAElFTkSuQmCC',
			'E8B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUNDkMQCGlhbWRsdGkRQxEQaXUEkFnUBSO4LjVoZtjR01cosJPdB1bUyYJo3BYtYAAOGHY4OWNyMIjZQ4UdFiMV9AITEzdvPDIJUAAAAAElFTkSuQmCC',
			'8B56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoFWl0bWB0EEBXN5XRAdl9S6Omhi3NzEzNQnIfSB1DQyCGeQ4NgQ4iGHagioH0Mjo6oOgFuZkhlAHFzQMVflSEWNwHADipzEpD8N6hAAAAAElFTkSuQmCC',
			'BB68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtcHQQQVPH2sAAUwd2UmjU1LClU1dNzUJyH1gdVvMCUc3DJobFLdjcPFDhR0WIxX0AlmrOJ+Fi+FEAAAAASUVORK5CYII=',
			'F0E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHaY6IIkFNDCGsDYwBASgiLG2sjYwOoigiIk0uiLEwE4KjZq2MjV0VVQYkvsg6himYuplaBDBsIMBzQ5sbsF080CFHxUhFvcBADkSzFBxgpFbAAAAAElFTkSuQmCC',
			'CF69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WENEQx1CGaY6IImJtIo0MDo6BAQgiQU0ijSwNjg6iCCLNYDEGGFiYCdFrZoatnTqqqgwJPeB1Tk6TMXUCyLR7QhAsQObW1hDgCrQ3DxQ4UdFiMV9AFKLzFuzTtLdAAAAAElFTkSuQmCC',
			'FAA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZAhimMLQiiwU0MIYwhDJMRRVjbWV0dAhFFRNpdG0IgOkFOyk0atrK1FVRS5Hdh6YOKiYa6hqKLoZNHU6x0IBBEH5UhFjcBwD4p88IpTNqDwAAAABJRU5ErkJggg==',
			'1B6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMDkMRYHURaGR0dHZDViTqINLo2oIoxAtWxAklk963Mmhq2dOrK0Cwk94HVOWLoBZoXSIwYpltCMN08UOFHRYjFfQAS2cdkCp111wAAAABJRU5ErkJggg==',
			'BE80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMdUAWaxVpYG0ICAjAUOfoIILkvtCoqWGrQldmTUNyH5o6JPMCsYhhswPVLdjcPFDhR0WIxX0ADPjM6lk1WPEAAAAASUVORK5CYII=',
			'1FE9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxOog0sDYwBAQgiYmCxRiBJLJeFDGwk1ZmTQ1bGroqKgzJfRB1DFMx9TI0YBHDYgeaW0KAYmhuHqjwoyLE4j4AZRHIbKuwUBsAAAAASUVORK5CYII=',
			'42C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37pjCGMIQ6THVAFgthbWV0CAgIQBJjDBFpdG0QdBBBEmOdwgAUY4CpAztp2rRVS5euWjU1C8l9AVMYprAi1IFhaChDAGsDI4p5QLc4sKLZAdKJ7haGKaKhDuhuHqjwox7E4j4AsYnL1RL3sBsAAAAASUVORK5CYII=',
			'A765' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsRHDMAhFoWADZZ9PoZ4UNJoGF97AzgYuoimjEsUukzvzu3cf7h3UTxN0p/zFj/FwOLslJkaLqiL3ykZLjZnZSqsEVyS/dvTXsb9bS36jZ6KIknbdGRI2MRvXJJ6YWQlWmH0xctpxg//9MBd+H2gky+kmeTwsAAAAAElFTkSuQmCC',
			'41C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjAEMIQ6tKKIhTAGMDoETHVAEmMMYQ1gbRAICEASYwXqZW1gdBBBct+0aauilq5amTUNyX0BqOrAMDQUU4wBrA7VDpAYulsYprCGYrh5oMKPehCL+wCWS8lr5+gr7QAAAABJRU5ErkJggg==',
			'2F1E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIYGIImJTBFpYAhhdEBWF9Aq0sCIJsYAFGOYAheDuGna1LBV01aGZiG7LwBFHRgyOmCKsTZgiolgEQsNBbol1BHFzQMVflSEWNwHAPmSyN3xEkpOAAAAAElFTkSuQmCC',
			'FC0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMEwNQBILaGBtdAhlCBBBERNpcHR0dGBBE2NtCHRAdl9o1LRVS1dFZiG7D00dXjFMO7C5BdPNAxV+VIRY3AcAFNnM8KVe6QIAAAAASUVORK5CYII=',
			'F795' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUMDkMSA7EZHR0cHBjQx14ZAdLFW1oZAVwck94VGrZq2MjMyKgrJfUB1AQwhAQ0iKHoZHUAkqhhrAyPQDlQxkQZGR4eAADQxhlCGqQ6DIPyoCLG4DwC1OMyvMxs9fAAAAABJRU5ErkJggg==',
			'3541' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQxkaHVqRxQKmiDQwtDpMRVHZChSb6hCKIjZFJIQhEK4X7KSVUVOXrszMWorivikMja5odjC0AsVCA9DERBodMNzC2oruPtEAxhCgWGjAIAg/KkIs7gMAMaXNGEMCessAAAAASUVORK5CYII=',
			'EBF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA0IdkMQCGkRaWRsYHQJQxRpdgbQIhjoQjXBfaNTUsKWhq5ZmIbkPTR0+83DYgeoWsJsbGFDcPFDhR0WIxX0AMz/NyXUCYSUAAAAASUVORK5CYII=',
			'49EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpI37pjCGsIY6hoYgi4WwtrI2MDogq2MMEWl0RRNjnYIiBnbStGlLl6aGrgzNQnJfwBTGQHS9oaEMGOYxTGHBIobpFqibUcUGKvyoB7G4DwBQqckMUtrupwAAAABJRU5ErkJggg==',
			'692B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0iDS6NgQ6iCCLNYg0OgDFApDcFxm1dGnWyszQLCT3hUxhDHRoZUQ1r5Wh0WEKI6p5rSyNDgGoYmC3OKDqBbmZNTQQxc0DFX5UhFjcBwBafMtyUoZgBwAAAABJRU5ErkJggg==',
			'C489' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYWhlCGaY6IImJtDJMZXR0CAhAEgtoZAhlbQh0EEEWa2B0ZXR0hImBnRS1aunSVaGrosKQ3BcANBFo3lRUvaKhriAZVDtaWRsCUOwAuqUV3S3Y3DxQ4UdFiMV9AIGsy8VI1nZ7AAAAAElFTkSuQmCC',
			'92BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0ijS6NgQ6iKCIMTS6AtWJILlv2tRVS5eGrsyahuQ+VleGKawIdRDYyhDAimaeQCujA7oY0C0N6G5hDRANdUVz80CFHxUhFvcBAHF+y3s1qF1XAAAAAElFTkSuQmCC',
			'C71A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQx2mMLQii4m0MjQ6hDBMdUASC2hkaHQMYQgIQBZrAOqbwuggguS+qFWrpq2atjJrGpL7gOoCkNRBxRgdgGKhISh2sDagqxNpFcEQYw0RaWAMdUQRG6jwoyLE4j4Adi7LYWP7oYYAAAAASUVORK5CYII=',
			'12B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgdWFtZGx2mOiCJiTqINLo2BAQEoOhlaHRtdHQQQXLfyqxVS5eGrsyahuQ+oLoprAh1MLEA1oZANDFGB1YMO1gbMNwSIhrqiubmgQo/KkIs7gMAROTJ4Jr5QIMAAAAASUVORK5CYII=',
			'B3BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGUNDkMQCpoi0sjY6OiCrC2hlaHRtCEQVm8KArA7spNCoVWFLQ1eGZiG5D00dbvOw2oHpFqibUcQGKvyoCLG4DwDtt8vjbmA66gAAAABJRU5ErkJggg==',
			'B6AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQximMIaGIIkFTGFtZQhldEBWF9Aq0sjo6IgqNkWkgbUhECYGdlJo1LSwpasiQ7OQ3BcwRbQVSR3cPNdQLGLo6oBuQdcLcjO62ECFHxUhFvcBAHFKy8HkATE8AAAAAElFTkSuQmCC',
			'3A3E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGUMDkMQCpjCGsDY6OqCobGVtZWgIRBWbItLogFAHdtLKqGkrs6auDM1Cdh+qOqh5oqEO6Oa1AtWhiQUA9bqi6RUNEGl0RHPzQIUfFSEW9wEA8bTLfB7vVwgAAAAASUVORK5CYII=',
			'315E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUMDkMQCpjAGsDYwOqCobGXFFJsC1DsVLgZ20sqoVVFLMzNDs5DdB1TH0BCIZh52MVY0sQCgXkZHRxQxUaCLGUIZUdw8UOFHRYjFfQAV9cdOXp3PtQAAAABJRU5ErkJggg==',
			'A847' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUNDkMRYA1hbGVodGkSQxESmiDQ6TEUVC2gFqgt0aAhAcl/U0pVhKzOzVmYhuQ+kjrXRoRXZ3tBQkUbX0IApDCjmAe1odAhgQLej0dEBVQzsZhSxgQo/KkIs7gMAmbvNZpp+tn8AAAAASUVORK5CYII=',
			'33FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RANYQ1hDA0MDkMQCpoi0sjYwOqCobGVodEUXm8KArA7spJVRq8KWhq4MzUJ2H6o63OZhEcPmFrCbGxhR3DxQ4UdFiMV9AFIQyQ8bWs8VAAAAAElFTkSuQmCC',
			'8ED2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDqIoKtrCGgQQXLf0qipYUtXRQEhwn1QdY0OGOYFtDJgik1hwOIWTDczhoYMgvCjIsTiPgBs3c0mkulT0wAAAABJRU5ErkJggg==',
			'8D92' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EEFVBxQLaBBBct/SqGkrMzOjVkUhuQ+kziEkoNEBzTwHIMmAJubYEDCFAYtbMN3MGBoyCMKPihCL+wDbUM1+HyCJBQAAAABJRU5ErkJggg==',
			'B4DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYWllDGUMDkMQCpjBMZW10dEBWF9DKEMraEIgqNoXRFUkM7KTQqKVLl66KDM1Ccl/AFJFWDL2toqGuGGIMmOqmAMXQ3ILNzQMVflSEWNwHAO5IzAfKx2AKAAAAAElFTkSuQmCC',
			'0733' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1DGUIdkMRYAxgaXRsdHQKQxESmMDQ6NAQ0iCCJBbQytEJEEe6LWrpq2qqpq5ZmIbkPqC4ASR1UjNGBAc08kSmsDehirAEiDaxobmF0EGlgRHPzQIUfFSEW9wEAo+TNPk8RLRYAAAAASUVORK5CYII=',
			'CFC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WENEQx1CHaY6IImJtIo0MDoEBAQgiQU0ijSwNgg6iCCLNYDEGGFiYCdFrZoathRIhSG5D6KOYSqmXqBdGHYIoNiBzS2sIUAVaG4eqPCjIsTiPgBO1cxNdiBXxgAAAABJRU5ErkJggg==',
			'D462' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGaY6IIkFTGGYyujoEBCALAZUxdrg6CCCIsboygqkRZDcF7UUCKYCaST3BbSKtLI6OjSi2NEqGuoKMhXVjlZWkO2obmkFuQXTzYyhIYMg/KgIsbgPAPCKzZgfk5whAAAAAElFTkSuQmCC',
			'558B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGUMdkMQCGkQaGB0dHQLQxFgbAh1EkMQCA0RCkNSBnRQ2berSVaErQ7OQ3dfK0OiIZh5IzBXNvIBWEQwxkSmsrehuYQ1gDEF380CFHxUhFvcBAFO3y26rV+xgAAAAAElFTkSuQmCC',
			'8CAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIaGIImJTGFtdAhldEBWF9Aq0uDo6IgiJjJFpIG1IRAmBnbS0qhpq5auigzNQnIfmjq4eayhmGKuaOpAbkEXA7kZ3byBCj8qQizuAwDo0ctG+PaP6AAAAABJRU5ErkJggg==',
			'211F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYAhhdEBWF9DKGsCIJsbQCtYLE4O4adqqqFXTVoZmIbsvAEUdGAJ5GGKsDZhiIljEQkNZQxlDHVHdMkDhR0WIxX0AuDrGK4wLWpkAAAAASUVORK5CYII=',
			'83DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGUMdkMREpoi0sjY6OgQgiQW0MjS6NgQ6iKCoY2hlBYoFILlvadSqsKWrIkOzkNyHpg6nedjtwHQLNjcPVPhREWJxHwCIwMyDKSrgSQAAAABJRU5ErkJggg==',
			'730C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNZQximMEwNQBZtFWllCGUIEEERY2h0dHR0YEEWm8LQytoQ6IDivqhVYUtXRWYhu4/RAUUdGLI2MDS6ookB7cOwI6AB0y0BDVjcPEDhR0WIxX0AKrXKstmoXzIAAAAASUVORK5CYII=',
			'BB19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQximMEx1QBILmCLSyhDCEBCALNYq0ugYwugggq5uClwM7KTQqKlhq6atigpDch9EHcNUETTzHKYwNGARw2IHqltAbmYMdUBx80CFHxUhFvcBAIZazWmi2NcIAAAAAElFTkSuQmCC',
			'1AE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHVqRxVgdGENYGximIouJOrC2AsVCUfWKNLo2MMD0gp20MmvaytTQVUuR3YemDiomGoophk0dpphoCFAs1CE0YBCEHxUhFvcBAC6SyV8ZWlHCAAAAAElFTkSuQmCC',
			'639B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUMdkMREpoi0Mjo6OgQgiQW0MDS6NgQ6iCCLNTC0sgLFApDcFxm1KmxlZmRoFpL7QqYwtDKEBKKa18rQ6IBuHlDMEU0Mm1uwuXmgwo+KEIv7AGCLy5WRU3iEAAAAAElFTkSuQmCC',
			'C8A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEMYQximMIaGIImJtLK2MoQCaSSxgEaRRkdHB1SxBtZWViAZgOS+qFUrw5auilqZheQ+qLpWBhS9Io2uoQFTGNDscG0ICGBAcwtrQ6ADupvRxQYq/KgIsbgPAFKBzRwkxECbAAAAAElFTkSuQmCC',
			'1FD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGUMDkMRYHUQaWBsdHZDViYLEGgIdUPWCxVwdkNy3Mmtq2NJVkVFRSO6DqAtoEMHQi00s0AFDrNEhANl9oiFAsVCGqQ6DIPyoCLG4DwCTW8mBOiYS1gAAAABJRU5ErkJggg==',
			'7544' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxkaHRoCkEVbRYDYoRFDbKpDK4rYFJEQhkCHKQHI7ouaunRlZlZUFJL7GB0YGl0bHR2Q9bI2AMVCA0NDkMREGkQaHdDcEtDA2oruvoAGxhAMNw9Q+FERYnEfAD+Qzs+Jm/MrAAAAAElFTkSuQmCC',
			'D8A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQximMEx1QBILmMLayhDKEBCALNYq0ujo6OgggCLG2sraEOiA7L6opSvDlq6KTM1Cch9UHYZ5rqGBDiLoYg1oYlNAegNQ9ILcDBRDcfNAhR8VIRb3AQCBtM5RnwA17AAAAABJRU5ErkJggg==',
			'CC0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WEMYQxmmMIaGIImJtLI2OoQyOiCrC2gUaXB0dEQVaxBpYG0IhImBnRS1atqqpasiQ7OQ3IemDrcYFjuwuQXqZhSxgQo/KkIs7gMAT03Kj3rcqAQAAAAASUVORK5CYII=',
			'5ACE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHUMDkMQCGhhDGB0CHRhQxFhbWRsEUcQCA0QaXRsYYWJgJ4VNm7YyddXK0Cxk97WiqIOKiYaiiwWA1aHaITJFpNERzS2sQHsd0Nw8UOFHRYjFfQBH9crNBIuPsQAAAABJRU5ErkJggg==',
			'1E07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIaGIImxOog0MIQyNIggiYkCxRgdHVDEGIFirA0BQIhw38qsqWFLV0WtzEJyH1RdKwOm3inoYkA7AtDFGEIZHZDFREPAbkYRG6jwoyLE4j4Ak6XIUERN9v8AAAAASUVORK5CYII=',
			'D500' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMLQiiwVMEWlgCGWY6oAs1irSwOjoEBCAKhbC2hDoIILkvqilU5cuXRWZNQ3JfQGtDI2uCHV4xEQaHdHtmMLaiu6W0ADGEHQ3D1T4URFicR8APN3N3xOUUXkAAAAASUVORK5CYII=',
			'7719' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMEx1QBZtZWh0CGEICEATcwxhdBBBFpsCFJ0CF4O4KWrVNCCMCkNyH6MDQwBQ7VRkvawg0SkMDchiIkBRoBiKHQEgFVNQ3QISYwx1QHXzAIUfFSEW9wEA2LTLNuxOIawAAAAASUVORK5CYII=',
			'5ED2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGaY6IIkFNIg0sDY6BASgizUEOoggiQUGgMRAMgj3hU2bGrZ0VRQQIrmvFayuEdkOqFgrslsCIGJTkMVEpkDcgizGGgByM2NoyCAIPypCLO4DAIHdzRDC4fRjAAAAAElFTkSuQmCC',
			'9199' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYHR0CAhAEgtoZQ1gbQh0EEERY0AWAztp2tRVUSszo6LCkNzH6gq0IyRgKrJeBqBeoAkNyGICQDHGhgAUO0SmMGC4BeiSUHQ3D1T4URFicR8AiRTJQFYNATEAAAAASUVORK5CYII=',
			'1DE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHRoCkMRYHURaWRsYGpHFRB1EGl0bGFoDUPSCxaYEILlvZda0lamhq6KikNwHUcfogKmXMTQE07wGNHUgt6CIiYZgunmgwo+KEIv7APcNy0c/o/9mAAAAAElFTkSuQmCC',
			'B98F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUNDkMQCprC2Mjo6OiCrC2gVaXRtCEQVmyLS6IhQB3ZSaNTSpVmhK0OzkNwXMIUx0BHDPAZM81pZsNiB6Raom1HEBir8qAixuA8ACxbLJ3m7SNEAAAAASUVORK5CYII=',
			'03E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHaY6IImxBoi0sjYwBAQgiYlMYWh0BaoWQRILaGVAVgd2UtTSVWFLQ1dNzUJyH5o6mBiGedjswOYWbG4eqPCjIsTiPgDLUMsZ8U+QLAAAAABJRU5ErkJggg==',
			'A02E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMDkMRYAxhDGB0dHZDViUxhbWVtCEQRC2gVaXRAiIGdFLV02sqslZmhWUjuA6trZUTRGxoKFJvCiGYeaytDALoY0C0O6GIMAayhgShuHqjwoyLE4j4AqVzJlTedjVMAAAAASUVORK5CYII=',
			'2B8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaYGIImJTBFpZXR0CBBBEgtoFWl0bQh0YEHW3QpS5+iA4r5pU8NWha7MQnFfAIo6MGR0gJiH4pYGTDtEGjDdEhqK6eaBCj8qQizuAwAxisqi54yLnwAAAABJRU5ErkJggg==',
			'09A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLo6OjoIIAkFtAq0ujaEOiA7L6opUuXpq6KTM1Ccl9AK2MgUB2KeQGtDI2uoYEOIih2sIDNE0FzC2tDAIpekJuBYihuHqjwoyLE4j4A/87MS2dDSTsAAAAASUVORK5CYII=',
			'54F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYWllDA0NDkMQCGhimsgJpEVSxUHSxwABGV1aIHNx9YdOWLl0aumplFrL7WkVagepaUWxuFQ11bWCYgiwW0MoAUheALCYyBSTG6IAsxhqAKTZQ4UdFiMV9AApsyuQUoFevAAAAAElFTkSuQmCC',
			'F3C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNZQxhCHaY6IIkFNIi0MjoEBASgiDE0ujYIOoigirWyNjDA1IGdFBq1KmzpqlVTs5Dch6YOyTxGdPOw2IHNLZhuHqjwoyLE4j4AvvnNe1zUAOkAAAAASUVORK5CYII=',
			'8B09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQKtLo6OjoIIKmjrUhECYGdtLSqKlhS1dFRYUhuQ+iLmCqCJp5rg0BDehiQCsw7EB3CzY3D1T4URFicR8AfDbMhksv+kAAAAAASUVORK5CYII=',
			'1971' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgdWIH8gKnIYqIOIo0ODQGhqHqBYo0OML1gJ63MWro0a+mqpcjuA9oR6DCFoRVVL0OjQwC6GEujowO6GGsrawOqmGgI0M0NDKEBgyD8qAixuA8AL0HJoICsYaUAAAAASUVORK5CYII=',
			'30FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA0MDkMQCpjCGsDYwOqCobGVtxRCbItLoihADO2ll1LSVqaErQ7OQ3YeqDmoeNjFMO7C5BezmBkYUNw9U+FERYnEfAOuMyLqn6N7lAAAAAElFTkSuQmCC',
			'B4B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYWllDGUIdkMQCpjBMZW10dAhAFmtlCGVtCGgQQVHH6Mra6NAQgOS+0KilS5eGrlqaheS+gCkirUjqoOaJhrqim9cKdAuGHUAxNLdgc/NAhR8VIRb3AQBbYc7KfvIWWQAAAABJRU5ErkJggg==',
			'6352' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANYQ1hDHaY6IImJTBFpZW1gCAhAEgtoYWh0bWB0EEEWa2BoZZ0KVI/kvsioVWFLM7NWRSG5L2QKQytQdSOyHQGtDEA+kEQTc20ImMKA5hZGR4cAdDczhDKGhgyC8KMixOI+ALYhzJcG9MjVAAAAAElFTkSuQmCC',
			'2E8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaYGIImJTBFpYHR0CBBBEgtoFWlgbQh0YEHW3QpS5+iA4r5pU8NWha7MQnFfAIo6MGR0gJiH4pYGTDtEGjDdEhqK6eaBCj8qQizuAwAuYMnNXT2NmgAAAABJRU5ErkJggg==',
			'D343' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNYQxgaHUIdkMQCpoi0MrQ6OgQgi7UCVU11aBBBFWtlCHRoCEByX9TSVWErM7OWZiG5D6SOtRGuDm6ea2gAunmNDo1odoDc0ojqFmxuHqjwoyLE4j4AVNnPiLfKzioAAAAASUVORK5CYII=',
			'F18C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaYGIIkFNDAGMDo6BIigiLEGsDYEOrCgiDEA1Tk6ILsvNGpV1KrQlVnI7kNTBxcDmYdNDNMODLeEort5oMKPihCL+wAmP8nU1Tqx4AAAAABJRU5ErkJggg==',
			'1164' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGRoCkMRYHRgDGB0dGpHFRB1YA1gbHFoD0PSyNjBMCUBy38qsVVFLp66KikJyH1ido6MDpt7A0BAMsYAGdHVAt6CIiYawhqK7eaDCj4oQi/sAhpTIkBrfw54AAAAASUVORK5CYII=',
			'8C28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGaY6IImJTGFtdHR0CAhAEgtoFWlwbQh0EEFRB+IFwNSBnbQ0atqqVSuzpmYhuQ+srpUBwzyGKYwo5oHEHAIY0ewAusUBVS/IzayhAShuHqjwoyLE4j4A107MpKxN7mgAAAAASUVORK5CYII=',
			'1B6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMdkMRYHURaGR0dHQKQxEQdRBpdGxwdRFD0irSyAskAJPetzJoatnTqytAsJPeB1aGZxwg2LxDdPGximG4JwXTzQIUfFSEW9wEAxGDIwGKBTtsAAAAASUVORK5CYII=',
			'CEA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WENEQxmmMLQii4m0ijQwhDJMRRYLaBRpYHR0CEURaxBpYG0IgOkFOylq1dSwpauiliK7D00dQiwUTawRUx3ILehiIDcDxUIDBkH4URFicR8AyKzM81jzl1kAAAAASUVORK5CYII=',
			'7964' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGRoCkEVbWVsZHR0aUcVEGl0bHFpRxKaAxBimBCC7L2rp0tSpq6KikNzH6MAY6Oro6ICsl7WBAag3MDQESUykgQUoFoDiloAGsFvQxLC4eYDCj4oQi/sAIHnN4KrTMUQAAAAASUVORK5CYII=',
			'0ADD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUMdkMRYAxhDWBsdHQKQxESmsLayNgQ6iCCJBbSKNLoixMBOilo6bWXqqsisaUjuQ1MHFRMNRRcTmYKpjjUAKIbmFkYHoBiamwcq/KgIsbgPAO7uzE4gfmtzAAAAAElFTkSuQmCC',
			'3658' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHaY6IIkFTGFtZW1gCAhAVtkq0sjawOgggiw2RaSBdSpcHdhJK6OmhS3NzJqahey+KaKtQFMxzHNoCEQ1DyjmiiYGcgujowOKXpCbGUIZUNw8UOFHRYjFfQCuT8vYUepb1gAAAABJRU5ErkJggg==',
			'1BB7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUNDkMRYHURaWRsdGkSQxEQdRBpdGwJQxBih6gKQ3Lcya2rY0lAgheQ+qLpWVHvB5k3BIhbAgGGHowOymGgI2M0oYgMVflSEWNwHAMEbyhbREE5uAAAAAElFTkSuQmCC',
			'8FB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaY6IImJTBFpYG10CAhAEgtoBYo1BDqI4FYHdtLSqKlhS0NXTc1Cch+x5hFhB9TNQDE0Nw9U+FERYnEfAKlyzV+mY8KbAAAAAElFTkSuQmCC',
			'1740' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHVqRxVgdGIAiDlMdkMREQWJTHQICUPQytDIEOjqIILlvZdaqaSszM7OmIbkPqC6AtRGuDirG6MAaGogmxtoAtAXNDpEGsM3IbgkBi6G4eaDCj4oQi/sAtszKI31lNp4AAAAASUVORK5CYII=',
			'D60A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGWY6oAs1irSyOjoEBCAKtbA2hDoIILkvqil08KWrorMmobkvoBW0VYkdXDzXBsCQ0PQxBwdHVHVgd3CiCIGcTOq2ECFHxUhFvcBADd5zOYDFjycAAAAAElFTkSuQmCC',
			'F194' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGRoCkMQCGhgDGB0dGlHFWANYGwJaUcUYQGJTApDcFxq1KmplZlRUFJL7QOoYQgId0PUyNASGhqCJMQJJdHVAt6CJsYaiu3mgwo+KEIv7ANhzzMqOZjoOAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>