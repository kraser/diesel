function addImage(files)
{
    var file;
    var imgFiles = [];
    //обработка для выбора многих файлов
    if($.isArray(files) == true) {
        for (i = 0; i < files.length; i++) {
            file = new Object();
            file["path"] = files[i]["path"];
            file["name"] = files[i]["name"];
            imgFiles.push(file);
        }
    } else {
        file = new Object();
        file["path"] = files.path;
        file["name"] = files.name;
        imgFiles.push(file);
    }

    var module = window.frameElement.getAttribute("data-module");// $("#p_module").text();
    var module_id = window.frameElement.getAttribute("data-module_id"); //$("#p_module_id").text();
    $.ajax({
        type: "GET",
        dataType: "html",
        url: "/admin/?module=" + module + "&method=ajaxImages",
        data:
                {
                    files: imgFiles,
                    id: module_id,
                },
        success: function(html)
        {
            var imgSet = $(html).find("div").first().parents("#ImagesSet");
            var inHtml = imgSet.html();
            $('#ImagesSet', parent.document).html(inHtml);
            window.parent.closeImagesDialog();
//            $("#ImagesSet").html(inHtml);
            // для восстановления обработчиков после elfinder`а
//            initImagesTab();
        },
        error: function(html)
        {
            alert("Ошибка добавления изображения.");
        }
    });
}

/**
 * Comment
 */
function refreshHandler(event, elfinderInstance) {
    if (!confirm("Обновить таблицу хранилища картинок?")) {
        return;
    }
    $.ajax({
        type: "GET",
        url: "?module=System&method=ajaxRefreshImagesStorage",
        success: function(html)
        {
            alert("YES");
        },
        error: function(html)
        {
            alert("NO");
        }
    });
    return;
}

/**
 * Comment
 */
function addHandler(event, elfinderInstance) {
    var files = [],
        adds = event.data.added;
    for (var i = 0; i < adds.length; i++) {
        var hash = adds[i].hash;
        files.push(hash);
    }
    $.ajax({
        type: "GET",
        url: "?module=System&method=ajaxAddToImagesStorage",
        data:
        {
            files: files,
        },
        success: function(html)
        {
            alert("YES");
        },
        error: function(html)
        {
            alert("NO");
        }
    });
    return;
}
/**
 * 
 */
function rmHandler(event, elfinderInstance) {
    var files = [],
        rms = event.data.removed;
    for (var i = 0; i < rms.length; i++) {
        var hash = rms[i];
        files.push(hash);
    }
    $.ajax({
        type: "GET",
        url: "?module=System&method=ajaxDelFromImagesStorage",
        data:
        {
            files: files,
        },
        success: function(html)
        {
            alert("YES");
        },
        error: function(html)
        {
            alert("NO");
        }
    });
    return;
}
/**
 * 
 */
function pasteHandler(event, elfinderInstance) {
    var files = [], adds = [], rems = [],
        added = event.data.added,
        removed = event.data.removed;
        // считаем массивы одной длины
    for (var i = 0; i < added.length; i++) {
        adds.push(added[i].hash);
        rems.push(removed[i]);
    }
    $.ajax({
        type: "GET",
        url: "?module=System&method=ajaxMoveInImagesStorage",
        data:
        {
            adds: adds,
            rems: rems,
        },
        success: function(html)
        {
            alert("YES");
        },
        error: function(html)
        {
            alert("NO");
        }
    });
    return;
}
/**
 * 
 */
function renameHandler(event, elfinderInstance) {
    var files = [], adds = [], rems = [];
    adds.push(event.data.added[0].hash);
    rems.push(event.data.removed[0]);
    $.ajax({
        type: "GET",
        url: "?module=System&method=ajaxRenameInImagesStorage",
        data:
        {
            adds: adds,
            rems: rems,
        },
        success: function(html)
        {
            alert("YES");
        },
        error: function(html)
        {
            alert("NO");
        }
    });
    return;
}
/**
 * Comment
 */
function uploadHandler(event, elfinderInstance) {
    return addHandler(event, elfinderInstance);
}
/**
 * Comment
 */
function selectHandler(event, elfinderInstance) {
    var filePath,
        sels = elfinderInstance.selected();
    for (i = 0; i < sels.length; i++) {
        var hash = sels[i];
        filePath = elfinderInstance.path(hash);
        console.log(hash);
        console.log(filePath);
    }
    return filePath;
}

function onDestroy()
{
    $("#elfbutton").focus();
}

///// Вспомогательные //////////
/** Decode path from hash
 * @param  string  file hash
 * @return string
 **/
function decodeByElf(hash) {
    // replace HTML safe base64 to normal
    var path = Base64.decode(replaceElfChars(hash));
    return path;
}
function replaceElfChars(hash) {
    var from = ['-','_','.'], to = ['+','/','='], arr;
    for (var i = 0; i < from.length; i++) {
        arr = hash.split(from[i]);
        hash = arr.join(to[i]);
    }
    return hash;
}

/**
 *
 * Base64 encode/decode
 * http://www.webtoolkit.info
 *
 **/   
var Base64 = {
   _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
   //метод для кодировки в base64 на javascript
    encode : function (input) {
      var output = "";
      var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
      var i = 0
      input = Base64._utf8_encode(input);
         while (i < input.length) {
       chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);
       enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;
       if( isNaN(chr2) ) {
           enc3 = enc4 = 64;
        }else if( isNaN(chr3) ){
          enc4 = 64;
        }
       output = output +
        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
     }
      return output;
    },
 
   //метод для раскодировки из base64
    decode : function (input) {
      var output = "";
      var chr1, chr2, chr3;
      var enc1, enc2, enc3, enc4;
      var i = 0;
   output = Base64._utf8_encode(output);
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
     while (i < input.length) {
       enc1 = this._keyStr.indexOf(input.charAt(i++));
        enc2 = this._keyStr.indexOf(input.charAt(i++));
        enc3 = this._keyStr.indexOf(input.charAt(i++));
        enc4 = this._keyStr.indexOf(input.charAt(i++));
       chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;
       output = output + String.fromCharCode(chr1);
       if( enc3 != 64 ){
          output = output + String.fromCharCode(chr2);
        }
        if( enc4 != 64 ) {
          output = output + String.fromCharCode(chr3);
        }
   }
//   output = Base64._utf8_decode(output);
     return output;
   },
   // метод для кодировки в utf8
    _utf8_encode : function (string) {
      string = string.replace(/\r\n/g,"\n");
      var utftext = "";
      for (var n = 0; n < string.length; n++) {
        var c = string.charCodeAt(n);
       if( c < 128 ){
          utftext += String.fromCharCode(c);
        }else if( (c > 127) && (c < 2048) ){
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        }else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }
     }
      return utftext;
 
    },
 
    //метод для раскодировки из urf8
    _utf8_decode : function (utftext) {
      var string = "";
      var i = 0;
      var c = c1 = c2 = 0;
      while( i < utftext.length ){
        c = utftext.charCodeAt(i);
       if (c < 128) {
          string += String.fromCharCode(c);
          i++;
        }else if( (c > 191) && (c < 224) ) {
          c2 = utftext.charCodeAt(i+1);
          string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
          i += 2;
        }else {
          c2 = utftext.charCodeAt(i+1);
          c3 = utftext.charCodeAt(i+2);
          string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
          i += 3;
        }
     }
     return string;
    }
 }
 
 /** аналог strtr из php, !!! СОМНИТЕЛЬНО */
 function strtr (str, from, to) {
	/*
	* strtr by Kedo
	* 2009
	* Example 1: strtr('hi all, I said hello', {'hi':'hello', 'hello':'hi'}); //hello all, I said hi
	* Example 2: strtr('abcdcdb', 'ab', 'AB')); //ABcdcdB
	*/
    if (typeof from === 'object') {
    	var cmpStr = '';
    	for (var j=0; j < str.length; j++){
    		cmpStr += '0';
    	}
    	var offset = 0;
    	var find = -1;
    	var addStr = '';
        for (fr in from) {
        	offset = 0;
        	while ((find = str.indexOf(fr, offset)) != -1){
				if (parseInt(cmpStr.substr(find, fr.length)) != 0){
					offset = find + 1;
					continue;
				}
				for (var k =0 ; k < from[fr].length; k++){
					addStr += '1';
				}
				cmpStr = cmpStr.substr(0, find) + addStr + cmpStr.substr(find + fr.length, cmpStr.length - (find + fr.length));
				str = str.substr(0, find) + from[fr] + str.substr(find + fr.length, str.length - (find + fr.length));
				offset = find + from[fr].length + 1;
				addStr = '';
        	}
        }
        return str;
    }

	for(var i = 0; i < from.length; i++) {
		str = str.replace(new RegExp(from.charAt(i),'g'), to.charAt(i));
	}

    return str;
}

 