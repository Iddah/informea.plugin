(function () {
	var a = tinymce.dom.Event, c = tinymce.each, b = tinymce.DOM;
	tinymce.create("tinymce.plugins.ContextMenu", {init: function (e) {
		var h = this, f, d, i;
		h.editor = e;
		d = e.settings.contextmenu_never_use_native;
		h.onContextMenu = new tinymce.util.Dispatcher(this);
		f = e.onContextMenu.add(function (j, k) {
			if ((i !== 0 ? i : k.ctrlKey) && !d) {
				return
			}
			a.cancel(k);
			if (k.target.nodeName == "IMG") {
				j.selection.select(k.target)
			}
			h._getMenu(j).showMenu(k.clientX || k.pageX, k.clientY || k.pageX);
			a.add(j.getDoc(), "click", function (l) {
				g(j, l)
			});
			j.nodeChanged()
		});
		e.onRemove.add(function () {
			if (h._menu) {
				h._menu.removeAll()
			}
		});
		function g(j, k) {
			i = 0;
			if (k && k.button == 2) {
				i = k.ctrlKey;
				return
			}
			if (h._menu) {
				h._menu.removeAll();
				h._menu.destroy();
				a.remove(j.getDoc(), "click", g)
			}
		}

		e.onMouseDown.add(g);
		e.onKeyDown.add(g);
		e.onKeyDown.add(function (j, k) {
			if (k.shiftKey && !k.ctrlKey && !k.altKey && k.keyCode === 121) {
				a.cancel(k);
				f(j, k)
			}
		})
	}, getInfo: function () {
		return{longname: "Contextmenu", author: "Moxiecode Systems AB", authorurl: "http://tinymce.moxiecode.com", infourl: "http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/contextmenu", version: tinymce.majorVersion + "." + tinymce.minorVersion}
	}, _getMenu: function (h) {
		var l = this, f = l._menu, i = h.selection, e = i.isCollapsed(), d = i.getNode() || h.getBody(), g, k, j;
		if (f) {
			f.removeAll();
			f.destroy()
		}
		k = b.getPos(h.getContentAreaContainer());
		j = b.getPos(h.getContainer());
		f = h.controlManager.createDropMenu("contextmenu", {offset_x: k.x + h.getParam("contextmenu_offset_x", 0), offset_y: k.y + h.getParam("contextmenu_offset_y", 0), constrain: 1, keyboard_focus: true});
		l._menu = f;
		f.add({title: "advanced.cut_desc", icon: "cut", cmd: "Cut"}).setDisabled(e);
		f.add({title: "advanced.copy_desc", icon: "copy", cmd: "Copy"}).setDisabled(e);
		f.add({title: "advanced.paste_desc", icon: "paste", cmd: "Paste"});
		if ((d.nodeName == "A" && !h.dom.getAttrib(d, "name")) || !e) {
			f.addSeparator();
			f.add({title: "advanced.link_desc", icon: "link", cmd: h.plugins.advlink ? "mceAdvLink" : "mceLink", ui: true});
			f.add({title: "advanced.unlink_desc", icon: "unlink", cmd: "UnLink"})
		}
		f.addSeparator();
		f.add({title: "advanced.image_desc", icon: "image", cmd: h.plugins.advimage ? "mceAdvImage" : "mceImage", ui: true});
		f.addSeparator();
		g = f.addMenu({title: "contextmenu.align"});
		g.add({title: "contextmenu.left", icon: "justifyleft", cmd: "JustifyLeft"});
		g.add({title: "contextmenu.center", icon: "justifycenter", cmd: "JustifyCenter"});
		g.add({title: "contextmenu.right", icon: "justifyright", cmd: "JustifyRight"});
		g.add({title: "contextmenu.full", icon: "justifyfull", cmd: "JustifyFull"});
		l.onContextMenu.dispatch(l, f, d, e);
		return f
	}});
	tinymce.PluginManager.add("contextmenu", tinymce.plugins.ContextMenu)
})();