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
        
    },

    insert : function() {
        // Insert the contents from the input into the document
        var width = document.getElementById('width').value;
        var height = document.getElementById('height').value;
        var border = document.getElementById('border').value;
        
        var description = document.getElementById('description').value;
        var imageTag = '<img src="' + selectedImage + '" alt="' + description + '" ';
        if (width != ''){
            imageTag += 'width="' + width +'" ';
        }
        if (height != ''){
            imageTag += 'height="' + height + '" ';
        }
        if (border != ''){
            imageTag += 'style="border: solid ' + border + 'px black;" ';
        }
        imageTag += '/>';
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, imageTag);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(FizzymediaDialog.init, FizzymediaDialog);
