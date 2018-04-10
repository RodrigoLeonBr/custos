$(function() {
    //SHADOWBOX
    //Shadowbox.init();

    //MASCARAS
    $(".formDate").mask("99/99/9999 99:99:99", {placeholder: " "});
    $(".formData").mask("99/99/9999", {placeholder: " "});
    $(".formValor").mask("###.###.###.##9,99", {placeholder: " "});


    //TinyMCE
    //EXTENSÂO DE YOUTUBE EM \tiny_mce\plugins\media\js MEDIA.js
    tinyMCE.init({
        // General options
        mode: "specific_textareas",
        editor_selector: "js_editor",
        language: "pt",
        theme: "advanced",
        elements: 'abshosturls',
        relative_urls: false,
        remove_script_host: false,
        skin: "o2k7",
        skin_variant: "silver",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
        theme_advanced_blockformats: "p,h2,h3,h4,pre,address",
        // Theme options
        theme_advanced_buttons1: "fullscreen,|,undo,redo,|,code,|,pastetext,|,removeformat,|,formatselect,bold,italic,underline,|,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,anchor,|,image,|,media,|,blockquote,|,hr,|,outdent,indent,|,charmap",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "center",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: false,
        // Example content CSS (should be your site CSS)
        content_css: "css/tiny.css",
        // Drop lists for link/image/media/template dialogs
        template_external_list_url: "lists/template_list.js",
        external_link_list_url: "lists/link_list.js",
        external_image_list_url: "lists/image_list.js",
        media_external_list_url: "lists/media_list.js",
        file_browser_callback: "tinyBrowser",
        // Style formats
        style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ],
        // Replace values for the template plugin
        template_replace_values: {
            username: "UPINSIDE TECNOLOGIA",
            staffid: "991234"
        }
    });
});
function maskIt(w,e,m,r,a){
// Cancela se o evento for Backspace
if (!e) var e = window.event
if (e.keyCode) code = e.keyCode;
else if (e.which) code = e.which;
// Variáveis da função
var txt  = (!r) ? w.value.replace(/[^\d]+/gi,'') : w.value.replace(/[^\d]+/gi,'').reverse();
var mask = (!r) ? m : m.reverse();
var pre  = (a ) ? a.pre : "";
var pos  = (a ) ? a.pos : "";
var ret  = "";
if(code == 9 || code == 8 || txt.length == mask.replace(/[^#]+/g,'').length) return false;
// Loop na máscara para aplicar os caracteres
for(var x=0,y=0, z=mask.length;x<z && y<txt.length;){
if(mask.charAt(x)!='#'){
ret += mask.charAt(x); x++; } 
else {
ret += txt.charAt(y); y++; x++; } }
// Retorno da função
ret = (!r) ? ret : ret.reverse()	
w.value = pre+ret+pos; }
// Novo método para o objeto 'String'
String.prototype.reverse = function(){
return this.split('').reverse().join(''); };

function number_format( number, decimals, dec_point, thousands_sep ) {
var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
var d = dec_point == undefined ? "," : dec_point;
var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function calcula(operacion){ 
var operando1 = parseFloat( document.calc.operando1.value.replace(/\./g, "").replace(",", ".") );
var operando2 = parseFloat( document.calc.operando2.value.replace(/\./g, "").replace(",", ".") );
var result = eval(operando1 + operacion + operando2);
document.calc.resultado.value = number_format(result,2, ',', '.');
} 