/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// core version + navigation, pagination modules:
import $ from "jquery";




import Application from './Application';
if ($("body").data("site") == "home") {
    Application().home.init();
    Application().header.init();
}
Application().module.init();


// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
var ace = require('brace');
require('brace/mode/javascript');
require('brace/theme/monokai');
// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');




/*var editor = ace.edit('javascript-editor');
editor.getSession().setMode('ace/mode/javascript');
editor.setTheme('ace/theme/monokai');
editor.getSession().setUseWorker(false);
console.log(editor.getValue());
*/