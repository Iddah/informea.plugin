(function (a) {
	a.onAddEditor.addToTop(function (c, b) {
		b.settings.inline_styles = false
	});
	a.create("tinymce.plugins.LegacyOutput", {init: function (b) {
		b.onInit.add(function () {
			var c = "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img", e = a.explode(b.settings.font_size_style_values), d = b.schema;
			b.formatter.register({alignleft: {selector: c, attributes: {align: "left"}}, aligncenter: {selector: c, attributes: {align: "center"}}, alignright: {selector: c, attributes: {align: "right"}}, alignfull: {selector: c, attributes: {align: "justify"}}, bold: [
				{inline: "b", remove: "all"},
				{inline: "strong", remove: "all"},
				{inline: "span", styles: {fontWeight: "bold"}}
			], italic: [
				{inline: "i", remove: "all"},
				{inline: "em", remove: "all"},
				{inline: "span", styles: {fontStyle: "italic"}}
			], underline: [
				{inline: "u", remove: "all"},
				{inline: "span", styles: {textDecoration: "underline"}, exact: true}
			], strikethrough: [
				{inline: "strike", remove: "all"},
				{inline: "span", styles: {textDecoration: "line-through"}, exact: true}
			], fontname: {inline: "font", attributes: {face: "%value"}}, fontsize: {inline: "font", attributes: {size: function (f) {
				return a.inArray(e, f.value) + 1
			}}}, forecolor: {inline: "font", styles: {color: "%value"}}, hilitecolor: {inline: "font", styles: {backgroundColor: "%value"}}});
			a.each("b,i,u,strike".split(","), function (f) {
				d.addValidElements(f + "[*]")
			});
			if (!d.getElementRule("font")) {
				d.addValidElements("font[face|size|color|style]")
			}
			a.each(c.split(","), function (f) {
				var h = d.getElementRule(f), g;
				if (h) {
					if (!h.attributes.align) {
						h.attributes.align = {};
						h.attributesOrder.push("align")
					}
				}
			});
			b.onNodeChange.add(function (g, k) {
				var j, f, h, i;
				f = g.dom.getParent(g.selection.getNode(), "font");
				if (f) {
					h = f.face;
					i = f.size
				}
				if (j = k.get("fontselect")) {
					j.select(function (l) {
						return l == h
					})
				}
				if (j = k.get("fontsizeselect")) {
					j.select(function (m) {
						var l = a.inArray(e, m.fontSize);
						return l + 1 == i
					})
				}
			})
		})
	}, getInfo: function () {
		return{longname: "LegacyOutput", author: "Moxiecode Systems AB", authorurl: "http://tinymce.moxiecode.com", infourl: "http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/legacyoutput", version: a.majorVersion + "." + a.minorVersion}
	}});
	a.PluginManager.add("legacyoutput", a.plugins.LegacyOutput)
})(tinymce);