<?php

  /**
   * A PHP generator example: drawing a IC case with pinout assingment in SVG
   * @package asic-pinout-drawer
   * @author  Dmitry Murzinov (kakstattakim@gmail.com)
   * @link :  https://github.com/iDoka/asic-pinout-drawer
   * @version 1.0
   */

  /* This whole script will draw a SVG image to stdout.
     Example of usage:

       $ php pinout-gen.php filename.json > filename.svg
  */

  // check if CLI-mode
  if (PHP_SAPI != "cli") {
    exit;
  }

  // parsing arg as filename
  if ($argc > 1) {
    $filename = $argv[1];
    $contents = file_get_contents($filename);
    $contents = utf8_encode($contents);
    $pinout = json_decode($contents,TRUE);
  }
  else {
     echo "Usage tool in CLI:\n\t$ php pinout-gen.php {filename.json} > {filename.svg}\n";
     exit;
  }


  // for planar rectangular case:
  // !!! ToDo: pass from JSON (config.json)
  $pins_number["total"] = 100;
  $pins_number["x"]     = 30;
  $pins_number["y"]     = 20;
/*
  $pins_number["total"] = 144;
  $pins_number["x"]     = 36;
  $pins_number["y"]     = 36;

  $pins_number["total"] = 64;
  $pins_number["x"]     = 16;
  $pins_number["y"]     = 16;
*/
  $canvas_case_ratio = 0.6;

  ###########################################
  $font_pin_number_color = "white";
  $font_pin_name_color   = "black";

  $font_pin_number_size  = round(12*125*$canvas_case_ratio/$pins_number["total"]);
  $font_pin_name_size    = 1.7*$font_pin_number_size;

  $font_pin_number_family = "PT Sans";
  $font_pin_name_family   = "PT Sans";

  $font_pin_number_weight = "bold";
  $font_pin_name_weight   = "bold";

  ###########################################

  $pin_color = array(
       "GND"  => "blue",
       "VDD"  => "red",
       "VDDO" => "magenta",
       "CMOS" => "gray",
       "LVDS" => "green",
       "JTAG" => "orange",
       "XTAL" => "pink",
       "ANALOG" => "rgb(0,255,0)");

  ###########################################
  $canvas_x = 1000;
  $canvas_y = 1000;

  $case_x = round($canvas_case_ratio * $canvas_x, 2);
  $case_y = round($case_x * $pins_number["y"]/$pins_number["x"], 2);

  $case_offset_x = round(($canvas_x - $case_x)/2, 2);
  $case_offset_y = round(($canvas_y - $case_y)/2, 2);



  $case_dot_radius = round($case_y * 0.02);
  $case_dot_clearance = $case_dot_radius * 2.5;
  $case_dot_x = round( $case_dot_clearance + $case_offset_x);
  $case_dot_y = round(-$case_dot_clearance + $case_offset_y + $case_y);

  //$pin_x = ceil( $case_x/($pins_number["x"]*2+2) );
  //$pin_y = ceil( $pin_x * 2 );
  $pin_x =  round($case_x/($pins_number["x"]*2+1), 2);
  $pin_y =  round($pin_x * 2, 2);

	// To kick off the SVG document, we need to declare the page doctype, here it is.

	$svg =  <<< HEREDOC
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="$canvas_x" height="$canvas_y">
<!--debug<rect x="0" y="0" width="$canvas_x" height="$canvas_y" fill="rgb(255,255,255)"/>-->
  <rect x="$case_offset_x" y="$case_offset_y" width="$case_x" height="$case_y" fill="rgb(234,234,234)" stroke-width="3" stroke="rgb(0,0,0)"/>
  <circle cx="$case_dot_x" cy="$case_dot_y" r="$case_dot_radius" fill="rgba(0, 0, 0)"/>\n
HEREDOC;
    echo $svg;


###########################################################################################################

$pin_index = 0;
$pin_padding = 1;

// that's fucking magic numbers
$xxx  = $pin_x*3.25/4;
$xxx2 = $pin_x*2;

#### BOTTOM SIDE
echo "  <!--################ BOTTOM SIDE ################-->".PHP_EOL;
for ($x=0; $x<$pins_number["x"]; $x++) {
    $pin_index++;
    $pinname = $pinout[$pin_index]['name'];
    $pintype = $pinout[$pin_index]['type'];
    $pinstd  = $pinout[$pin_index]['standard'];
    $pininfo = $pinout[$pin_index]['description'];
    $pincolor = DecidePinColour($pin_index, $pinname, $pintype, $pinstd, $pininfo);
    $pin_offset_x  = round($pin_x * ($x*2 + 1) + $case_offset_x, 2);
    $pin_offset_y  = round($case_offset_y+$case_y +$pin_padding, 2);
    $text_offset_x = round($pin_offset_x+$pin_x/2, 2);
    $text_offset_y = round($pin_offset_y+$pin_x/2, 2);
    $name_offset_x = round($pin_offset_x+$pin_x, 2);
    $name_offset_y = round($pin_offset_y+$pin_x, 2);
    //<text x="$text_offset_x" y="$text_offset_y" font-family="PT Mono" font-size="12" fill="gray" transform="rotate(270 $text_offset_x $text_offset_y)">$pin_index</text>\n
    $svg =  <<< HEREDOC
    <rect x="$pin_offset_x" y="$pin_offset_y" width="$pin_x" height="$pin_y" fill="$pincolor" stroke-width="1" stroke="rgb(0,0,0)"/>
    <text x="$pin_offset_x" y="$pin_offset_y" font-family="$font_pin_number_family" font-size="$font_pin_number_size" font-weight="$font_pin_number_weight" fill="$font_pin_number_color"
     text-anchor="middle" transform="translate($xxx) rotate(270 $text_offset_x $text_offset_y)">$pin_index</text>
    <text x="$name_offset_x" y="$name_offset_y" font-family="$font_pin_name_family" font-size="$font_pin_name_size" font-weight="$font_pin_name_weight" fill="$font_pin_name_color"
     text-anchor="end" transform="translate(0 $xxx2) rotate(270 $name_offset_x $name_offset_y)">$pinname</text>\n
HEREDOC;
    echo $svg;
}

#### RIGHT SIDE
echo "<!--################ RIGHT SIDE ################-->".PHP_EOL;
for ($y=0; $y<$pins_number["y"]; $y++) {
    $pin_index++;
    $pinname = $pinout[$pin_index]['name'];
    $pintype = $pinout[$pin_index]['type'];
    $pinstd  = $pinout[$pin_index]['standard'];
    $pininfo = $pinout[$pin_index]['description'];
    $pincolor = DecidePinColour($pin_index, $pinname, $pintype, $pinstd, $pininfo);
    $pin_offset_x  = round($case_offset_x+$case_x +$pin_padding, 2);
    $pin_offset_y  = round($case_offset_y+$case_y - $pin_x*($y*2 + 2), 2);
    $text_offset_x = round($pin_offset_x+$pin_x, 2);
    $text_offset_y = round($pin_offset_y+$xxx, 2);
    $name_offset_x = round($pin_offset_x+3*$pin_x, 2);
    $name_offset_y = round($pin_offset_y+$pin_x, 2);
    // alignment-baseline="middle"
    $svg =  <<< HEREDOC
    <rect x="$pin_offset_x" y="$pin_offset_y" width="$pin_y" height="$pin_x" fill="$pincolor" stroke-width="1" stroke="rgb(0,0,0)"/>
    <text x="$text_offset_x" y="$text_offset_y" font-family="$font_pin_number_family" font-size="$font_pin_number_size" font-weight="$font_pin_number_weight" fill="$font_pin_number_color" text-anchor="middle">$pin_index</text>
    <text x="$name_offset_x" y="$name_offset_y" font-family="$font_pin_name_family" font-size="$font_pin_name_size" font-weight="$font_pin_name_weight" fill="$font_pin_name_color" text-anchor="start">$pinname</text>\n
HEREDOC;
    echo $svg;
}

#### TOP SIDE
echo "<!--################ TOP SIDE ################-->".PHP_EOL;
for ($x=0; $x<$pins_number["x"]; $x++) {
    $pin_index++;
    $pinname = $pinout[$pin_index]['name'];
    $pintype = $pinout[$pin_index]['type'];
    $pinstd  = $pinout[$pin_index]['standard'];
    $pininfo = $pinout[$pin_index]['description'];
    $pincolor = DecidePinColour($pin_index, $pinname, $pintype, $pinstd, $pininfo);
    $pin_offset_x  = round($case_offset_x+$case_x - $pin_x*($x*2 + 2), 2);
    $pin_offset_y  = round($case_offset_y-$pin_y -$pin_padding, 2);
    $text_offset_x = round($pin_offset_x+$pin_x/2, 2);
    $text_offset_y = round($pin_offset_y+$pin_x/2, 2);
    $name_offset_x = round($pin_offset_x+$pin_x, 2);
    $name_offset_y = round($pin_offset_y+$pin_x, 2);
    $svg =  <<< HEREDOC
    <rect x="$pin_offset_x" y="$pin_offset_y" width="$pin_x" height="$pin_y" fill="$pincolor" stroke-width="1" stroke="rgb(0,0,0)"/>
    <text x="$pin_offset_x" y="$pin_offset_y" font-family="$font_pin_number_family" font-size="$font_pin_number_size" font-weight="$font_pin_number_weight" fill="$font_pin_number_color"
     text-anchor="middle" transform="translate($xxx) rotate(270 $text_offset_x $text_offset_y)">$pin_index</text>
    <text x="$name_offset_x" y="$name_offset_y" font-family="$font_pin_name_family" font-size="$font_pin_name_size" font-weight="$font_pin_name_weight" fill="$font_pin_name_color"
     text-anchor="start" transform="translate(0 -$xxx2) rotate(270 $name_offset_x $name_offset_y)">$pinname</text>\n
HEREDOC;
    echo $svg;
}

#### LEFT SIDE
echo "<!--################ LEFT SIDE ################-->".PHP_EOL;
for ($y=0; $y<$pins_number["y"]; $y++) {
    $pin_index++;
    $pinname = $pinout[$pin_index]['name'];
    $pintype = $pinout[$pin_index]['type'];
    $pinstd  = $pinout[$pin_index]['standard'];
    $pininfo = $pinout[$pin_index]['description'];
    $pincolor = DecidePinColour($pin_index, $pinname, $pintype, $pinstd, $pininfo);
    $pin_offset_x  = round($case_offset_x -$pin_x*2 -$pin_padding, 2);
    $pin_offset_y  = round($case_offset_y+ $pin_x*($y*2 + 1), 2);
    $text_offset_x = round($pin_offset_x+$pin_x, 2);
    $text_offset_y = round($pin_offset_y+$xxx, 2);
    $name_offset_x = round($pin_offset_x-$pin_x, 2);
    $name_offset_y = round($pin_offset_y+$pin_x, 2);
    $svg =  <<< HEREDOC
    <rect x="$pin_offset_x" y="$pin_offset_y" width="$pin_y" height="$pin_x" fill="$pincolor" stroke-width="1" stroke="rgb(0,0,0)"/>
    <text x="$text_offset_x" y="$text_offset_y" font-family="$font_pin_number_family" font-size="$font_pin_number_size" font-weight="$font_pin_number_weight" fill="$font_pin_number_color" text-anchor="middle">$pin_index</text>
    <text x="$name_offset_x" y="$name_offset_y" font-family="$font_pin_name_family" font-size="$font_pin_name_size" font-weight="$font_pin_name_weight" fill="$font_pin_name_color" text-anchor="end">$pinname</text>\n
HEREDOC;
    echo $svg;
}

    // Output the document close tag.
    echo "</svg>\n";



  ######################################################################################
  function DecidePinColour($pin_index, $pinname, $pintype, $pinstd, $pininfo){

    GLOBAL $pin_color;

    switch ($pintype) {
    case "PO": // supply output signal
        $pincolor = $pin_color["VDDO"];
        break;
    case "P":
        if (preg_match("/VSS/",$pinname) || preg_match("/GND/",$pinname)) { // several name of ground signal
          $pincolor = $pin_color["GND"];
        } else if (preg_match("/VDD/",$pinname) || preg_match("/VCC/",$pinname)) { // several name of supply signal
          $pincolor = $pin_color["VDD"];
        }
        break;
    case  "A": // common analog
    case "AI": // analog input
    case "AO": // analog output
        if (preg_match("/XTAL/",$pininfo)) { // customize colour of Quartz pins
          $pincolor = $pin_color["XTAL"];
        } else {
          $pincolor = $pin_color["ANALOG"];
        }
        break;
    case "IO": // digital bidir
    case  "I": // digital input
    case  "O": // digital output
        if (preg_match("/LVDS/",$pinstd)) { // customize colour of differencial pairs
          $pincolor = $pin_color["LVDS"];
        } else if (preg_match("/JTAG/",$pininfo)) { // customize colour of JTAG signals
          $pincolor = $pin_color["JTAG"];
        } else {
          $pincolor = $pin_color["CMOS"]; // All others digital pins assume as CMOS standard
        }
        break;
    default: // set default colour value
        $pincolor = "rgb(200,200,200)";
        break;
    }
    return $pincolor;
  }
###########################################

?>
