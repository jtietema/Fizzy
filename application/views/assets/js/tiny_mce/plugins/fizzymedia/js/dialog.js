tinyMCEPopup.requireLangPack();

var selectedImage = null;
var selectedElement = null;

function select(image, element){
    selectedImage = image;
    if (selectedElement != null){
        selectedElement.style.background = '#ffffff';
    }
    selectedElement = element;
    selectedElement.style.background = '#5555ff';
}

var FizzymediaDialog = {
    init : function(ed) {
        var f = document.forms[0],
            nl = f.elements,
            ed = tinyMCEPopup.editor,
            dom = ed.dom,
            n = ed.selection.getNode();
        
        /* check if there is an image selected. If so we edit that image 
           instead of inserting a new one */
        if (n.nodeName == 'IMG'){
            // set the image properties
            nl.width.value = dom.getAttrib(n, 'width');
            nl.height.value = dom.getAttrib(n, 'height');
            nl.alt.value = dom.getAttrib(n, 'alt');
            nl.insert.value = ed.getLang('update');
            
            // select the image
            var src = dom.getAttrib(n, 'src');
            var image = document.getElementById(src);
            select(src, image);
        }
    },

    insert : function() {
        if (selectedImage === null){
            alert('Please select an Image!');
            return;
        }
        // Insert the contents from the input into the document
        var width = document.getElementById('width').value;
        var height = document.getElementById('height').value;
        //var border = document.getElementById('border').value;
        
        var description = document.getElementById('alt').value;
        var imageTag = '<img src="' + selectedImage + '" alt="' + description + '" ';
        if (width != ''){
            imageTag += 'width="' + width +'" ';
        }
        if (height != ''){
            imageTag += 'height="' + height + '" ';
        }
        /*
        if (border != ''){
            imageTag += 'style="border: solid ' + border + 'px black;" ';
        }*/
        imageTag += '/>';
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, imageTag);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(FizzymediaDialog.init, FizzymediaDialog);
