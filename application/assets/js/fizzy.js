var Fizzy = function(){
    var _editors = [];
    
    return {
        "wysiwyg" : {
            "count" : function(){
                return _editors.length;
            },
            "register" : function(id){
                _editors.push(id);
            },
            "editors" : function(){
                return _editors;
            },
            "init" : function() {
                // init all the editors
                var editors = fizzy.wysiwyg.editors().join(",");
                tinyMCE.init({
                    mode : "exact",
                    elements: editors,
                    theme : "advanced",
                    plugins: "fizzymedia,fizzyassets",
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_buttons1: "formatselect,fontselect,fontsizeselect,separator,forecolor,backcolor,separator,removeformat,undo,redo",
                    theme_advanced_buttons2: "bold,italic,underline,sub,sup,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,blockquote,separator,link,unlink,fizzymedia,fizzyassets,separator,charmap,code,cleanup",
                    theme_advanced_buttons3: "",
                    theme_advanced_statusbar_location : "bottom",
                    theme_advanced_resizing : true,
                    theme_advanced_resize_horizontal : false,
                    relative_urls : false
                });
            }
        }
    }
}
var fizzy = Fizzy();

window.onload = fizzy.wysiwyg.init;
