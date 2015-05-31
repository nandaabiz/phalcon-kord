var chordtag = '{~}';
chord_regex = new RegExp(/([CDEFGAB](#|b)?)((M|m|Maj|min|aug|dim|sus|add)?(6|7|9|11|13|-5|\+5)?)(\s|-)/g);
$(function () {
	/* Resolve conflict in jQuery UI tooltip with Bootstrap tooltip */
	// $.widget.bridge('uibutton', $.ui.button);

	// admin
	$(document)
		// .one('focus.textarea', '.auto-expand', function(){
		// 	var savedValue = this.value;
		// 	this.value = '';
		// 	this.baseScrollHeight = this.scrollHeight;
		// 	this.value = savedValue;
		// })
		.on('input.textarea', '.auto-expand', function(){
			// var minRows = this.getAttribute('data-min-rows')|0,
			// 	 rows;
			// this.rows = minRows;
			// rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
			// this.rows = minRows + rows;
			$(this).prop('rows',($(this).val().split(/\r|\r\n|\n/).length) + 5);
		});
	$(document).on('click', 'li:not(.active) a[href="#lyricText"]', function(event) {
		$('#textLyric').val(convertChordtoTxt($('#lyricChord ul.selectable-chord')));
	});
	$(document).on('click', 'li:not(.active) a[href="#lyricChord"]', function(event) {
		$('#lyricChord').html(convertTxtToChord($('#textLyric').val()));
	});
	$(document).on('click', 'a#autoConvert', function(event) {
		var result = autoConvertChord($('#textLyric').val());
		$('#textLyric').val(result);
		$('#lyricChord').html(convertTxtToChord(result));
	});
	$(document).on('click', 'ul.selectable-chord li', function(event) {
		$(this).toggleClass('chordline');
	});
	$(document).on('click', '.tabs-lyric', function(event) {
		$('#tabsLyricTop a[href="'+$(this).data('link')+'"').trigger('click');
	});

});

function convertTxtToChord(txt) {
	txt = $.trim(txt).split("\n");
	var dom_ul = '<ul class="selectable-chord">';
	$.each(txt, function(index, val) {
		var cls = '';
		if (val.match(chordtag+"$")) {
			cls = 'chordline';
			val = val.replace(/^(.)|\s(.)/g, function($1){ return $1.toUpperCase( ); });
		};
		dom_ul += '<li class="'+cls+'">'+val.replace(chordtag,'')+'</li>';
	});
	dom_ul += '</ul>';
	return dom_ul;
}

function convertChordtoTxt($dom_ul) {
	var $chord_li = $dom_ul.children('li');
	var txt = '';
	$.each($chord_li, function(index, val) {
		var line = val.innerText;
		if (val.className.match('chordline')) {
			line += chordtag;
		};
		txt += line+"\n";
	});
	return txt;
}

function autoConvertChord(txt) {
	txt = $.trim(txt).split("\n");
	var result = '';
	// var chord_regex = new RegExp(/(([A-G])+([#Mmb0-9]|add|aug|dim)?\s)+/g);
	$.each(txt, function(index, val) {
		if (val.match(chordtag+"$")) {
			val = val.replace(/^(.)|\s(.)/g, function($1){ return $1.toUpperCase( ); });
		} else if (chord_regex.test(val)) {
			val = val.replace(/^(.)|\s(.)/g, function($1){ return $1.toUpperCase( ); })+chordtag;
		}
		result += val+"\n";
	});
	return result;
}