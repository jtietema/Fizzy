/**
 * TinyMCE plugin for the Fizzy Media Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.voidwalkers.nl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@voidwalkers.nl so we can send you a copy immediately.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @copyright Copyright 2009, Voidwalkers, All rights reserved.
 */

tinyMCEPopup.requireLangPack();

var selectedAsset = null;
var selectedElement = null;

function select(asset, element){
    selectedAsset = asset;
    if (selectedElement != null){
        selectedElement.style.background = '#CCCCCC';
    }
    selectedElement = element;
    selectedElement.style.background = '#5555ff';
}

var FizzyassetsDialog = {
    init : function() {
        var f = document.forms[0],
            nl = f.elements,
            ed = tinyMCEPopup.editor,
            dom = ed.dom,
            n = ed.selection.getNode();

        /* check if there is an image selected. If so we edit that image 
           instead of inserting a new one */
        if (n.nodeName == 'A'){
            // set the asset properties
            nl.alt.value = n.firstChild.data;

            nl.insert.value = ed.getLang('update');

            // select the asset
            var href = dom.getAttrib(n, 'href');
            var asset = document.getElementById(href);
            select(href, asset);
        } else if (!ed.selection.isCollapsed()){
            nl.alt.value = ed.selection.getContent();
        }
    },

    insert : function() {
        if (selectedAsset === null){
            alert('Please select an Asset.');
            return;
        }
        // Insert the contents from the input into the document
        var description = document.getElementById('alt').value;
        if (description == ''){
            alert('Please provide a description.');
            return;
        }

        var assetTag = document.createElement('a');
        assetTag.href = selectedAsset;
        assetTag.appendChild(document.createTextNode(description));

        // convert domnode to string
        var div = document.createElement('div');
        div.appendChild(assetTag);
        var assetString = div.innerHTML;

        var n = tinyMCEPopup.editor.selection.getNode();
        if (n.nodeName == 'A'){
            n.parentNode.replaceChild(assetTag, n);
        } else {
            tinyMCEPopup.editor.execCommand('mceInsertContent', false, assetString);
        }
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(FizzyassetsDialog.init, FizzyassetsDialog);
