INSTALLATION
------------
- Run database.sql on a DB
- Put all files in a direcorty on the web server.
- Browse the package's /index.php for detailed samples

INTEGRATION
-----------
- For integration in your app, you might need to consider 3 things.

1) Set DB config

	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("griddemo");

2) The folder "../../lib/js" will be replaced by path where you place core lib.

	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/start/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

3) The $base_path will be the path of lib folder where jqgrid_dist.php is located.

	$base_path = strstr(realpath("."),"demos",true)."lib/";
	include($base_path."inc/jqgrid_dist.php");
	$g = new jqgrid($db_conf);

Also make sure, you call '$g->render();' function before any HTML is rendered (echo'd) to screen. 
As it expect exact JSON format for ajax calls.

Some features are available with full source code, which you can get after appreciating it by little payment. 

See http://www.phpgrid.org/download for more details.

UPGRADE
-------

To upgrade, just override "lib/inc" & "lib/js" folder in previous implementations. For technical support queries, suggestions and wishlist, you can contact at our Support Center (http://www.phpgrid.org/support)

CHANGELOG
---------
v1.4.8
- HTML editor integration (editing/html-editor.php)
- FancyBox integration (appearence/fancy-box.php)
- Loading Grid from phpArray
- Conditional formatting (appearence/conditional-formatting.php)
- Conditional Data display (appearence/conditional-data.php)
- Controlling multiple detail grids from master
- Server side validation (editing/server-validation.php)
- Custom validation (editing/js-validation.php)
- Custom toolbar button (appearence/toolbar-button.php)
- Export to CSV
- Date Time picker control
- Open grid in edit mode by default
- Added more themes (appearence/themes.php)
- Add Data Grouping example
- MySQLi support added

Fixes
- Security fix for demo center
- Null field setting option (isnull)
- Fix for external-link, now working on edit
- Added >,<,>=,<=,!= operators in conditional css formatting
- Changed variable name of data passed from master grid (new:rowid, old:id)
- Sort order fix, when none is specified
- Date format fix when inserted in mysql
- Disabled dblclick, when rowaction is off
- Memory issues in PDF lib fixed (tcpdf)
- Row edit fix for edit only / delete only option in grid
- Demo center fix for IE

v1.4.7
- Integration with Oracle (db-layer-oracle.php)
- Frozen column example (frozen-column.php)
- Documentation (md) file added
- Master-detail example
- Multiple grids on same page
- PDF Export Current page (export-pdf.php)
- Bar Graph in Grid (bar-graph.php)
- Conditional Data display in Cell (custom-button.php)
- Postgres Integration and working example (db-layer-pgsql.php)
- Image display example added (images.php)

Fixes
- PHP 5.4 compatibility
- XLS export fix for non-english characters
- Autofilter fix for search in lookup
- SQL Server Sql Query optimization
- Export column ordering
- HTTPs fix for auto detection

v1.4.6
- Autofilter (dropdown option) (autofilter.php)
- Custom column (with buttons, image, etc) and passing row data as querystring (custom-button.php)
- Custom Layout for Add/Edit Dialogs (custom-layout.php)
- Search on page load (pre-selected filters) (search-onload.php)
- Data Lookup from other table (Combobox) (dropdown.php)
- Alternate Row Coloring (alternate-row.php)
- MSSQL DSN or ODBC sample (db-layer.php)
- Setting Colums for add/edit, not for listing (example-all.php)
- Localization example (localization.php)

Fixes & Minor Additions
- New Events (on_data_display for custom formating, on_after_insert) (custom-events.php)
- Action columns width fix (example-full.php)
- Grouping sample bug fix (short php tag) (grouping.php)
- Several notices and warning fixes
- Excel 2003 support added

v1.4.5 (7:15:34 PM, Sunday, February 12, 2012)
- support for all major databases (mysql,pgsql,mssql,sybase ... and odbc support) (db-layer.php)
- inline row addition (excel_view.php)
- pdf export custom template sample (export-pdf.php)
- export to pdf/excel in same grid
- fix for non integer primary key
- fix for mysql keyword based column name
- Fix for non-integer PK column
- multiselect fix in lib jqgrid 4.3.1


v1.4 (20.12.11)

Features:
- Datepicker integration (example-all.php)
- Group by field (grouping.php)
- Runtime change "group by" field (grouping.php)
- Password mask formatter / edittype
	// To mask password field, apply following attribs
	$col["edittype"] = "password";
	$col["formatter"] = "password";
- Excel export fix for numeric columns

v1.3 (21.07.11)

Features:
- multiple fonts in pdf export
- External link with grid data (external-link.php)
- PDF export feature (export-pdf.php)
- Export filtered/all data option
- Export specific columns (export-pdf.php, $col["export"] = false;)

Fixes:
- persist where filter in export option
- Jquery fix for IE
- utf8 encoding fix
- json fix for php 5.1
- slashes fix

Migration from v1.1 TO v1.2 (31.12.10)
- You need to change your grid column name to table-field or field-alias. 
- "table.field" format (i.invdate) is not supported anymore. instead write "invdate"
e.g.	old -- $col["name"] = "i.id"; 
		new -- $col["name"] = "id"; 

FEEDBACK
--------
- Do post bugs/wishlist at http://www.phpgrid.org/support

LICENSE
-------
- Must read and agree LICENSE.txt before use
