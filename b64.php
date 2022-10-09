<?
chdir( __dir__."/.." );
if( isset($_GET["l"]) && isset($_GET["b64"]) ){
	$GLOBALS["sNAME"] = $_GET["l"];
	$GLOBALS["sFILE"] = strtr( $_GET["b64"] , " -|_." , "++//=" );
}

if( isset($_POST["l"]) && isset($_POST["b64"]) ){
	if( isset($_POST["x"]) ){
		$GLOBALS["sDIR"] = ".ZIP".$_POST["x"];
	}
	else{
		//=================================================================================
		$GLOBALS["sDIR"] = ".ZIP.".$_SERVER["REMOTE_ADDR"];
		if( !file_exists( $GLOBALS["sDIR"] )){
			$aDIR = Array();
			$aMSG = Array();
			exec( "find -maxdepth 1 -type d | grep /\.ZIP --colour=NEVER" , $aDIR );
			for($i=0;$i<count($aDIR);$i++){
				$iP = 8 - fileperms( $aDIR[$i] )%8;
				$iLAST = fileatime( $aDIR[$i] ) + 86400*pow( 100 , $iP );
				if( $iLAST < time() ){
					exec( "rm -r ".$aDIR[$i] , $aMSG );
				}
			}
		
		}
	}
	$GLOBALS["sNAME"] = $GLOBALS["sDIR"]."/".$_POST["l"];
	$GLOBALS["sFILE"] = strtr( $_POST["b64"] , " -|_." , "++//=" );
}



if( isset($GLOBALS["sNAME"]) && isset($GLOBALS["sFILE"]) ){


	//=================================================================================
	if( strpos( $GLOBALS["sFILE"] ,"_" )){
		$GLOBALS["sFILE"] = str_replace("_9","/",$GLOBALS["sFILE"]);
		$GLOBALS["sFILE"] = strtr(str_replace("_8","+",$GLOBALS["sFILE"]),"_","/");
	}
	//=================================================================================
	if( $GLOBALS["sFILE"][0] == "=" ){
		$GLOBALS["CMD"]=substr($GLOBALS["sFILE"],1,3);
		$GLOBALS["sFILE"] = substr($GLOBALS["sFILE"],4);
	}
	//====== Fuer Base64: =============================================================
	while(strlen($GLOBALS["sFILE"])%4 != 0 ){ $GLOBALS["sFILE"] .= "="; }
	//=================================================================================
	$GLOBALS["sNAME"] = strtr($GLOBALS["sNAME"]," ","_");
	//=================================================================================
	if( isset( $GLOBALS["CMD"] )){
			if( $GLOBALS["CMD"] == "del" || $GLOBALS["CMD"] == "new" ){
				if( file_exists( $GLOBALS["sNAME"] ) ){
					unlink( $GLOBALS["sNAME"] );
				}
			}
	}
	if( !file_exists(  $GLOBALS["sNAME"] )){
		touch( $GLOBALS["sNAME"] );
		chmod( $GLOBALS["sNAME"],0777);
	}
	//=================================================================================	
	//=================================================================================	
	if( file_put_contents( $GLOBALS["sNAME"] , base64_decode( $GLOBALS["sFILE"] ) , FILE_APPEND | LOCK_EX)){
		echo filesize( $GLOBALS["sNAME"] );
	}
	else{
		echo "0";
	}
}
else	echo "Error @ ".$GLOBALS["sNAME"].":";

?>


