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
    init : function() {
        var f = document.forms[0],
            nl = f.elements,
            ed = tinyMCEPopup.editor,
            dom = ed.dom,
            n = ed.selection.getNode();

        /* check if there is an image selected. If so we edit that image 
           instead of inserting a new one */
        if (n.nodeName == 'IMG'){
            // set the image properties
            nl.width.value          = dom.getAttrib(n, 'width');
            nl.height.value         = dom.getAttrib(n, 'height');
            nl.alt.value            = dom.getAttrib(n, 'alt');
            // we use parseInt to strip the px part
            nl.border.value         = parseInt(n.style.borderWidth);
            nl.marginTop.value      = parseInt(n.style.marginTop);
            nl.marginRight.value    = parseInt(n.style.marginRight);
            nl.marginBottom.value   = parseInt(n.style.marginBottom);
            nl.marginLeft.value     = parseInt(n.style.marginLeft);

            // determine which style attribute is the position
            var position = 'none';
            if (n.style.cssFloat == 'left' || n.style.cssFloat == 'right'){
                position = n.style.cssFloat;
            } else if (n.style.verticalAlign != ''){
                position = n.style.verticalAlign;
            }
            
            // select the current position in the dropdown box
            var selectBox = nl.position.options;
            for (i in selectBox){
                if (selectBox[i].value == position){
                    selectBox.selectedIndex = i;
                }
            }

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
        var width           = document.getElementById('width').value;
        var height          = document.getElementById('height').value;
        var border          = document.getElementById('border').value;
        var marginTop       = document.getElementById('marginTop').value;
        var marginLeft      = document.getElementById('marginLeft').value;
        var marginBottom    = document.getElementById('marginBottom').value;
        var marginRight     = document.getElementById('marginRight').value;
        
        var description = document.getElementById('alt').value;
        var position = document.getElementById('position').value;

        var imageTag = new Image();
        imageTag.src = selectedImage;
        imageTag.alt = description;

        if (width != ''){
            imageTag.width = width;
        }
        if (height != ''){
            imageTag.height = height;
        }
        if (position == 'left' || position == 'right'){
            imageTag.style.cssFloat = position;
        } else if (position !== 'none' && position !== ''){
            imageTag.style.verticalAlign = position;
        }
        if (border !== ''){
            imageTag.style.borderStyle = 'solid';
            imageTag.style.borderColor = 'black';
            imageTag.style.borderWidth = border + 'px';
        }
        if (marginTop !== ''){
            imageTag.style.marginTop = marginTop + 'px';
        }
        if (marginRight !== ''){
            imageTag.style.marginRight = marginRight + 'px';
        }
        if (marginBottom !== ''){
            imageTag.style.marginBottom = marginBottom + 'px';
        }
        if (marginLeft !== ''){
            imageTag.style.marginLeft = marginLeft + 'px';
        }

        // convert domnode to string
        var div = document.createElement('div');
        div.appendChild(imageTag);
        var imageString = div.innerHTML;
        
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, imageString);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(FizzymediaDialog.init, FizzymediaDialog);
