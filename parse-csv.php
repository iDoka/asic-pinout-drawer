<?php

  /**
   * A PHP generator example: drawing a IC-case with pinout assingment in SVG
   * @package csv-to-json
   * @author  Dmitry Murzinov (kakstattakim@gmail.com)
   * @link :  https://github.com/iDoka/asic-pinout-drawer
   * @version 1.0
   */

  /* This simple script will convert to CSV to JSON, example of usage:

       $ php parse-csv.php filename.csv > filename.json
  */

  // check if CLI-mode
  if (PHP_SAPI === 'cli') {
      if ($argc > 1) {
          $filename = $argv[1];
      } else {
        echo "Usage tool in CLI:\n\t$ php parse-csv.php {filename.csv} > {filename.json}\n";
        exit;
      }
  } else {
      exit;
  }

  ini_set('auto_detect_line_endings',TRUE);

  $row = 1;

  if (($handle = fopen("$filename", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 200, "|")) !== FALSE) {
          $num = count($data);
          /* debug purposes only:
          echo "\n==== $num fields in string $row ====\n";
          for ($c=0; $c < $num; $c++) {
              echo $data[$c]."\n";
          } */
          $pin_number = intval($data[0]); // not yet applicable for BGA pins which numbers contain alphabetical symbols
          //$pinout['number'][$data[0]] = ; // reserved for BGA matrix supporting in future
          $pinout[$pin_number]['name']        = strtoupper($data[1]);
          $pinout[$pin_number]['type']        = strtoupper($data[2]);
          $pinout[$pin_number]['standard']    = $data[3];
          $pinout[$pin_number]['description'] = $data[4];
          $row++;
      }
      fclose($handle);
      echo json_encode($pinout, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES)."\n";
  }

  ini_set('auto_detect_line_endings',FALSE);
?>
