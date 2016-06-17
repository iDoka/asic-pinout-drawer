<?php

  /**
   * A PHP generator example: drawing a IC case with pinout assingment in SVG
   * @package asic-pinout-drawer
   * @author  Dmitry Murzinov (kakstattakim@gmail.com)
   * @link :  https://github.com/iDoka/asic-pinout-drawer
   * @version 1.0
   */


###########################################################
function DrawingPinBody($pin_attribute) {
  GLOBAL $pin_x, $pin_y;
  if (($pin_attribute["side"] === "BOTTOM") || ($pin_attribute["side"] === "TOP")) {
    $width  = $pin_x;
    $height = $pin_y;
  } else if (($pin_attribute["side"] === "RIGHT") || ($pin_attribute["side"] === "LEFT")){
    $width  = $pin_y;
    $height = $pin_x;
  }
  echo "<rect x=\"".$pin_attribute["body_offset_x"]."\" y=\"".$pin_attribute["body_offset_y"]."\" width=\"$width\" height=\"$height\" fill=\"".$pin_attribute["color"]."\" stroke-width=\"1\" stroke=\"rgb(0,0,0)\"/>".PHP_EOL;
}

###########################################################
function DrawingPinNumber($pin_attribute) {

  GLOBAL $font_pin_number_family, $font_pin_number_size, $font_pin_number_weight, $font_pin_number_color, $xxx;

  if (($pin_attribute["side"] === "BOTTOM") || ($pin_attribute["side"] === "TOP")) {
    $transform = "transform=\"translate($xxx) rotate(270 ".$pin_attribute["text_offset_x"]." ".$pin_attribute["text_offset_y"].")\"";
  } else if (($pin_attribute["side"] === "RIGHT") || ($pin_attribute["side"] === "LEFT")){
    $transform = "";
  }

  if (($pin_attribute["side"] === "BOTTOM") || ($pin_attribute["side"] === "TOP")) {
    $x = $pin_attribute["body_offset_x"];
    $y = $pin_attribute["body_offset_y"];
  } else if (($pin_attribute["side"] === "RIGHT") || ($pin_attribute["side"] === "LEFT")){
    $x = $pin_attribute["text_offset_x"];
    $y = $pin_attribute["text_offset_y"];
  }

  $font   = $font_pin_number_family;
  $size   = $font_pin_number_size;
  $weight = $font_pin_number_weight;
  $color  = $font_pin_number_color;

  echo "<text x=\"$x\" y=\"$y\" font-family=\"$font\" font-size=\"$size\" font-weight=\"$weight\" fill=\"$color\" text-anchor=\"middle\" $transform>".$pin_attribute["index"]."</text>".PHP_EOL;
}

###########################################################
function DrawingPinName($pin_attribute) {

  GLOBAL $font_pin_name_family, $font_pin_name_size, $font_pin_name_weight, $font_pin_name_color, $xxx2;

  if (($pin_attribute["side"] === "BOTTOM") || ($pin_attribute["side"] === "LEFT")) {
    $anchor = "end";
  } else if (($pin_attribute["side"] === "TOP") || ($pin_attribute["side"] === "RIGHT")){
    $anchor = "start";
  }

  if ($pin_attribute["side"] === "TOP")
    $sign = "-";
  else
    $sign = "";

  if (($pin_attribute["side"] === "BOTTOM") || ($pin_attribute["side"] === "TOP")) {
    $transform = "transform=\"translate(0  ".$sign.$xxx2.") rotate(270 ".$pin_attribute["name_offset_x"]." ".$pin_attribute["name_offset_y"].")\"";
  } else if (($pin_attribute["side"] === "RIGHT") || ($pin_attribute["side"] === "LEFT")){
    $transform = "";
  }

  $x = $pin_attribute["name_offset_x"];
  $y = $pin_attribute["name_offset_y"];
  $font   = $font_pin_name_family;
  $size   = $font_pin_name_size;
  $weight = $font_pin_name_weight;
  $color  = $font_pin_name_color;

  echo "<text x=\"$x\" y=\"$y\" font-family=\"$font\" font-size=\"$size\" font-weight=\"$weight\" fill=\"$color\" text-anchor=\"$anchor\" $transform>".$pin_attribute["name"]."</text>".PHP_EOL;
}

###########################################################
function DrawingPartnumber() {

  GLOBAL $font_partnumber_family, $font_partnumber_size, $font_partnumber_weight, $font_partnumber_color;
  GLOBAL $partnumber, $case_x, $case_y, $case_offset_x, $case_offset_y;

  if (strlen($partnumber) == 0) {
    return;
  }

  $x = $case_offset_x + $case_x/2;
  $y = $case_offset_y + $case_y/2 + $font_partnumber_size/4;
  $font   = $font_partnumber_family;
  $size   = $font_partnumber_size;
  $weight = $font_partnumber_weight;
  $color  = $font_partnumber_color;

  echo "<text x=\"$x\" y=\"$y\" font-family=\"$font\" font-size=\"$size\" font-weight=\"$weight\" fill=\"$color\" text-anchor=\"middle\" alignment-baseline=\"middle\">$partnumber</text>".PHP_EOL;
}

?>
