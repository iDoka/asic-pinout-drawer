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

  define('__ROOT__', dirname(__FILE__));
  require_once(__ROOT__.'/DecidePinColour.php');
  require_once(__ROOT__.'/DrawingFunctions.php');


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

  //$partnumber = "DokaChip";
  // uncomment this for you dont wish to draw partnumber:
  $partnumber = "";

  $precission = 2; // two sign after comma

  $canvas_case_ratio = 0.6;

  ###########################################
  $font_partnumber_color = "black";
  $font_pin_number_color = "white";
  $font_pin_name_color   = "black";

  $font_partnumber_size  = round(7*12*125*$canvas_case_ratio/$pins_number["total"]);
  $font_pin_number_size  = round(12*125*$canvas_case_ratio/$pins_number["total"]);
  $font_pin_name_size    = 1.7*$font_pin_number_size;

  $font_partnumber_family = "PT Serif, Helvetica, Tahoma, sans-serif";
  $font_pin_number_family = "PT Sans, Helvetica, Tahoma, sans-serif";
  $font_pin_name_family   = "PT Sans, Helvetica, sans-serif";

  $font_partnumber_weight = "normal";
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

  $case_x = round($canvas_case_ratio * $canvas_x, $precission);
  $case_y = round($case_x * $pins_number["y"]/$pins_number["x"], $precission);

  $case_offset_x = round(($canvas_x - $case_x)/2, $precission);
  $case_offset_y = round(($canvas_y - $case_y)/2, $precission);



  $case_dot_radius = round($case_y * 0.02);
  $case_dot_clearance = $case_dot_radius * 2.5;
  $case_dot_x = round( $case_dot_clearance + $case_offset_x);
  $case_dot_y = round(-$case_dot_clearance + $case_offset_y + $case_y);

  //$pin_x = ceil( $case_x/($pins_number["x"]*2+2) );
  //$pin_y = ceil( $pin_x * 2 );
  $pin_x =  round($case_x/($pins_number["x"]*2+1), $precission);
  $pin_y =  round($pin_x * 2, $precission);

  // this parameter for manual adjasting (set this for non-overlapping with pin-name on bottom side):
  $legend_offset_x = -5*$pin_y;
  $legend_offset_y =  5*$pin_y;


	// To kick off the SVG document, we need to declare the page doctype, here it is.

	$svg =  <<< HEREDOC
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="$canvas_x" height="$canvas_y">
<!--+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->
<!--++++++++++++++++++++++++++[[[ Created by  https://github.com/iDoka/asic-pinout-drawer ]]]++++++++++++++++++++++++++++-->
<!--+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->
<!-- debug: filling white to background <rect x="0" y="0" width="$canvas_x" height="$canvas_y" fill="rgb(255,255,255)"/>-->
<!--++++++++++++++++ Body of IC ++++++++++++++++-->
<rect x="$case_offset_x" y="$case_offset_y" width="$case_x" height="$case_y" fill="rgb(234,234,234)" stroke-width="3" stroke="rgb(0,0,0)"/>
<!--++++++++++++++++ Marking first pin ++++++++++++++++-->
<circle cx="$case_dot_x" cy="$case_dot_y" r="$case_dot_radius" fill="rgb(0, 0, 0)"/>\n
HEREDOC;
    echo $svg;


DrawingPartnumber();

// comment it, if legend is not necessary:
DrawingLegend();


###########################################################################################################

$pin_attribute["index"] = 0;
$pin_padding = 1;

// that's fucking magic numbers
$xxx  = $pin_x*3.25/4;
$xxx2 = $pin_x*2;


###########################################################################################################
#### BOTTOM SIDE
echo "<!--++++++++++++++++ BOTTOM SIDE ++++++++++++++++-->".PHP_EOL;
$pin_attribute["side"] = "BOTTOM";
for ($x=0; $x<$pins_number["x"]; $x++) {
    $pin_attribute["index"]++;
    $pin_attribute["name"]  = $pinout[$pin_attribute["index"]]['name'];
    $pin_attribute["type"]  = $pinout[$pin_attribute["index"]]['type'];
    $pin_attribute["std"]   = $pinout[$pin_attribute["index"]]['standard'];
    $pin_attribute["info"]  = $pinout[$pin_attribute["index"]]['description'];
    $pin_attribute["color"] = DecidePinColour($pin_attribute);
    $pin_attribute["body_offset_x"] = round($pin_x * ($x*2 + 1) + $case_offset_x, $precission);
    $pin_attribute["body_offset_y"] = round($case_offset_y+$case_y +$pin_padding, $precission);
    $pin_attribute["text_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x/2, $precission);
    $pin_attribute["text_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x/2, $precission);
    $pin_attribute["name_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x, $precission);
    $pin_attribute["name_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x, $precission);
    DrawingPinBody($pin_attribute);
    DrawingPinNumber($pin_attribute);
    DrawingPinName($pin_attribute);
}

#### RIGHT SIDE
echo "<!--++++++++++++++++ RIGHT SIDE ++++++++++++++++-->".PHP_EOL;
$pin_attribute["side"] = "RIGHT";
for ($y=0; $y<$pins_number["y"]; $y++) {
    $pin_attribute["index"]++;
    $pin_attribute["name"]  = $pinout[$pin_attribute["index"]]['name'];
    $pin_attribute["type"]  = $pinout[$pin_attribute["index"]]['type'];
    $pin_attribute["std"]   = $pinout[$pin_attribute["index"]]['standard'];
    $pin_attribute["info"]  = $pinout[$pin_attribute["index"]]['description'];
    $pin_attribute["color"] = DecidePinColour($pin_attribute);
    $pin_attribute["body_offset_x"] = round($case_offset_x+$case_x +$pin_padding, $precission);
    $pin_attribute["body_offset_y"] = round($case_offset_y+$case_y - $pin_x*($y*2 + 2), $precission);
    $pin_attribute["text_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x, $precission);
    $pin_attribute["text_offset_y"] = round($pin_attribute["body_offset_y"]+$xxx, $precission);
    $pin_attribute["name_offset_x"] = round($pin_attribute["body_offset_x"]+3*$pin_x, $precission);
    $pin_attribute["name_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x, $precission);
    DrawingPinBody($pin_attribute);
    DrawingPinNumber($pin_attribute);
    DrawingPinName($pin_attribute);
    // alignment-baseline="middle"
}

#### TOP SIDE
echo "<!--++++++++++++++++ TOP SIDE ++++++++++++++++-->".PHP_EOL;
$pin_attribute["side"] = "TOP";
for ($x=0; $x<$pins_number["x"]; $x++) {
    $pin_attribute["index"]++;
    $pin_attribute["name"]  = $pinout[$pin_attribute["index"]]['name'];
    $pin_attribute["type"]  = $pinout[$pin_attribute["index"]]['type'];
    $pin_attribute["std"]   = $pinout[$pin_attribute["index"]]['standard'];
    $pin_attribute["info"]  = $pinout[$pin_attribute["index"]]['description'];
    $pin_attribute["color"] = DecidePinColour($pin_attribute);
    $pin_attribute["body_offset_x"] = round($case_offset_x+$case_x - $pin_x*($x*2 + 2), $precission);
    $pin_attribute["body_offset_y"] = round($case_offset_y-$pin_y -$pin_padding, $precission);
    $pin_attribute["text_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x/2, $precission);
    $pin_attribute["text_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x/2, $precission);
    $pin_attribute["name_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x, $precission);
    $pin_attribute["name_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x, $precission);
    DrawingPinBody($pin_attribute);
    DrawingPinNumber($pin_attribute);
    DrawingPinName($pin_attribute);
}

#### LEFT SIDE
echo "<!--++++++++++++++++ LEFT SIDE ++++++++++++++++-->".PHP_EOL;
$pin_attribute["side"] = "LEFT";
for ($y=0; $y<$pins_number["y"]; $y++) {
    $pin_attribute["index"]++;
    $pin_attribute["name"]  = $pinout[$pin_attribute["index"]]['name'];
    $pin_attribute["type"]  = $pinout[$pin_attribute["index"]]['type'];
    $pin_attribute["std"]   = $pinout[$pin_attribute["index"]]['standard'];
    $pin_attribute["info"]  = $pinout[$pin_attribute["index"]]['description'];
    $pin_attribute["color"] = DecidePinColour($pin_attribute);
    $pin_attribute["body_offset_x"] = round($case_offset_x -$pin_x*2 -$pin_padding, $precission);
    $pin_attribute["body_offset_y"] = round($case_offset_y+ $pin_x*($y*2 + 1), $precission);
    $pin_attribute["text_offset_x"] = round($pin_attribute["body_offset_x"]+$pin_x, $precission);
    $pin_attribute["text_offset_y"] = round($pin_attribute["body_offset_y"]+$xxx, $precission);
    $pin_attribute["name_offset_x"] = round($pin_attribute["body_offset_x"]-$pin_x, $precission);
    $pin_attribute["name_offset_y"] = round($pin_attribute["body_offset_y"]+$pin_x, $precission);
    DrawingPinBody($pin_attribute);
    DrawingPinNumber($pin_attribute);
    DrawingPinName($pin_attribute);
}

    // Output the document close tag.
    echo "</svg>\n";

?>
