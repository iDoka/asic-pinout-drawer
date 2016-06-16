#  $Id$
#######################################################################################
##  Project    : asic-pinout-drawer
##  Designer   : Dmitry Murzinov (kakstattakim@gmail.com)
##  Link       : https://github.com/iDoka/asic-pinout-drawer
##  Module     : A PHP generator that drawing a IC-case with pinout assingment in SVG
##  Description:
##  Revision   : $Rev
##  Version    : $GlobalRev$
##  Date       : $LastChangedDate$
##  License    : MIT
#######################################################################################

FILE=pinout

all:
	@grep -v "====" $(FILE).adoc | grep -e "^|" | sed 's/^|//' | sed 's/[ ]*|[ ]*/|/g' > $(FILE).csv
	@$(eval PIN_COUNT=$(shell cat $(FILE).csv | wc -l))
	@echo "Amount of pins will be processed (from source ASCIIDOC-table): $(PIN_COUNT)"
	@php parse-csv.php  $(FILE).csv  > $(FILE).json
	@php pinout-gen.php $(FILE).json > $(FILE).svg
	@echo "Drawing done!"

png:
	@convert -background none -antialias $(FILE).svg $(FILE).png

.PHONY: all example $(FILE).adoc
