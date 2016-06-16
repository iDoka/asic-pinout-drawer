<?php

  /**
   * A PHP generator example: drawing a IC case with pinout assingment in SVG
   * @package asic-pinout-drawer
   * @author  Dmitry Murzinov (kakstattakim@gmail.com)
   * @link :  https://github.com/iDoka/asic-pinout-drawer
   * @version 1.0
   */


  ######################################################################################
  function DecidePinColour($pin_attribute){

    GLOBAL $pin_color;

    switch ($pin_attribute["type"]) {
    case "PO": // supply output signal
        $color = $pin_color["VDDO"];
        break;
    case "P":
        if (preg_match("/(GND|VSS)/i",$pin_attribute["name"])) { // several name of ground signal
          $color = $pin_color["GND"];
        } else if (preg_match("/(VDD|VCC)/i",$pin_attribute["name"])) { // several name of supply signal
          $color = $pin_color["VDD"];
        }
        break;
    case  "A": // common analog
    case "AI": // analog input
    case "AO": // analog output
        if (preg_match("/XTAL/",$pin_attribute["info"])) { // customize colour of Quartz pins
          $color = $pin_color["XTAL"];
        } else {
          $color = $pin_color["ANALOG"];
        }
        break;
    case "IO": // digital bidir
    case  "I": // digital input
    case  "O": // digital output
        if (preg_match("/LVDS/",$pin_attribute["std"])) { // customize colour of differencial pairs
          $color = $pin_color["LVDS"];
        } else if (preg_match("/JTAG/",$pin_attribute["info"])) { // customize colour of JTAG signals
          $color = $pin_color["JTAG"];
        } else {
          $color = $pin_color["CMOS"]; // All others digital pins assume as CMOS standard
        }
        break;
    default: // set default colour value
        $color = "rgb(200,200,200)";
        break;
    }
    return $color;
  }
###########################################

?>
