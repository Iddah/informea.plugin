#!/bin/bash
if test -z "$1"; then
	echo "Usage: ./compress.sh <css|js>"
	exit;
fi

if [[ $1 == "CSS" ]] || [[ $1 == "css" ]]; then
	echo "CSS Compression"
	echo
	echo "Compressing wp-content/themes/informea/fix-IE7.css"
	java -jar lib/yuicompressor-2.4.6.jar wp-content/themes/informea/fix-IE7.css -o wp-content/themes/informea/fix-IE7_min.css

	echo "Compressing wp-content/themes/informea/fix-IE8.css"
	java -jar lib/yuicompressor-2.4.6.jar wp-content/themes/informea/fix-IE8.css -o wp-content/themes/informea/fix-IE8_min.css

	echo '' > wp-content/themes/informea/style_min.css
	echo "Composing multiple CSS files into style_min.css"
	for filename in 'style.css' 'tipsy.css' 'bubble.css' 'ui.css' 'bubbletip.css' 'boxy.css' 'feedback.css' 'slider.css'
	do
		echo "/* Begin $filename */ " >> wp-content/themes/informea/style_min.css
		echo "    >> $filename"
		cat wp-content/themes/informea/$filename >> wp-content/themes/informea/style_min.css
		echo "/* End $filename */ " >> wp-content/themes/informea/style_min.css
	done
	echo "Compressing style_min.css"
	java -jar lib/yuicompressor-2.4.6.jar wp-content/themes/informea/style_min.css -o wp-content/themes/informea/style_min.css
fi

if [ $1 == "JS" -o $1 == "js" ]; then
	echo "JavaScript Compression"
	echo
	echo '' > wp-content/themes/informea/scripts/script_min.js
	echo "Composing multiple JS files into script_min.js"
	for filename in 'jquery-min.js' 'cookie.js' 'boxy.js' 'hoverIntent.js' 'bgiframe.min.js' 'tipsy.js' 'bubbletip.js' 'scroll.js' 'functions.js' 'main.js' 'events.js' 'ui.js' 'imea_explorer.js' 'lof-slider.js' 'easing.js' 'vticker.js'
	do
		echo "/* Begin $filename */ " >> wp-content/themes/informea/scripts/script_min.js
		echo "    >> $filename"
		cat wp-content/themes/informea/scripts/$filename >> wp-content/themes/informea/scripts/script_min.js
		echo "/* End $filename */ " >> wp-content/themes/informea/scripts/script_min.js
	done
	echo "Compressing script_min.js"
	java -jar lib/yuicompressor-2.4.6.jar wp-content/themes/informea/scripts/script_min.js -o wp-content/themes/informea/scripts/script_min.js
fi
