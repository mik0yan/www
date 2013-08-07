/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
	config.height = 200;
	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		//{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		//{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'basicstyles', groups: ['basicstyles'] },
		{ name: 'links' },
		// { name: 'insert' },
		// { name: 'forms' },
		// { name: 'tools' },
		//{ name: 'others' },
		// '/',
		
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		// { name: 'styles' },
		// { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		// { name: 'colors' },
		//{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Strike,Anchor,Subscript,Superscript,Styles';
	config.extraPlugins='onchange'; 
	config.skin = 'silver';
	config.minimumChangeMilliseconds = 100; // 100 milliseconds (default value)
};
