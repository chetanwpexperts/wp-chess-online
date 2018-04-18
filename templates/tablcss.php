<style>

.css3-tabstrip
{
    width: 100%;
    height: 220px;
}    
 
.css3-tabstrip ul,
.css3-tabstrip li
{
    margin: 0;
    padding: 0;
    list-style: none;
}
 
.css3-tabstrip,
.css3-tabstrip input[type="radio"]:checked + label
{
    position: relative;
}
 
.css3-tabstrip li,
.css3-tabstrip input[type="radio"] + label
{
    display: inline-block;
}
 
.css3-tabstrip li > div,
.css3-tabstrip input[type="radio"]
{
    position: absolute;
}
 
.css3-tabstrip li > div,
.css3-tabstrip input[type="radio"] + label
{
    border: solid 1px #ccc;
}
 
.css3-tabstrip
{
    font: normal 11px Arial, Sans-serif;
    color: #404040;
}
 
.css3-tabstrip li
{
    vertical-align: top;
}
 
.css3-tabstrip li:first-child
{
    margin-left: 8px;
}
 
.css3-tabstrip li > div
{
    top: 33px;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 8px;
	height: 530px;
    background: #fff;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
 
.css3-tabstrip input[type="radio"] + label
{
    margin: 0 2px 0 0;
    padding: 0 18px;
    line-height: 32px;
    background: #f1f1f1;
    text-align: center;
    border-radius: 5px 5px 0 0;
    cursor: pointer;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
}
 
.css3-tabstrip input[type="radio"]:checked + label
{
    z-index: 1;
    background: #fff;
    border-bottom-color: #fff;
    cursor: default;
}
 
.css3-tabstrip input[type="radio"]
{
    opacity: 0;
}
 
.css3-tabstrip input[type="radio"] ~ div
{
    display: none;
}
 
.css3-tabstrip input[type="radio"]:checked:not(:disabled) ~ div
{
    display: block;
}
 
.css3-tabstrip input[type="radio"]:disabled + label
{
    opacity: .5;
    cursor: no-drop;
}

.CSSTableGenerator {
	margin:0px;padding:0px;
	width:100%;
	border:1px solid #4c4c4c;
	
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
	
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
	
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
	
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}.CSSTableGenerator table{
    border-collapse: collapse;
        border-spacing: 0;
	width:100%;
	height:100%;
	margin:0px;padding:0px;
}.CSSTableGenerator tr:last-child td:last-child {
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
}
.CSSTableGenerator table tr:first-child td:first-child {
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.CSSTableGenerator table tr:first-child td:last-child {
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
}.CSSTableGenerator tr:last-child td:first-child{
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
}.CSSTableGenerator tr:hover td{
	
}
.CSSTableGenerator tr:nth-child(odd){ background-color:#999999; }
.CSSTableGenerator tr:nth-child(even)    { background-color:#ffffff; }.CSSTableGenerator td{
	vertical-align:middle;
	
	
	border:1px solid #4c4c4c;
	border-width:0px 1px 1px 0px;
	text-align:center;
	padding:9px;
	font-size:10px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}.CSSTableGenerator tr:last-child td{
	border-width:0px 1px 0px 0px;
}.CSSTableGenerator tr td:last-child{
	border-width:0px 0px 1px 0px;
}.CSSTableGenerator tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.CSSTableGenerator tr:first-child td{
		background:-o-linear-gradient(bottom, #999999 5%, #333333 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #999999), color-stop(1, #333333) );
	background:-moz-linear-gradient( center top, #999999 5%, #333333 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#999999", endColorstr="#333333");	background: -o-linear-gradient(top,#999999,333333);

	background-color:#999999;
	border:0px solid #4c4c4c;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#ffffff;
}
.CSSTableGenerator tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #999999 5%, #333333 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #999999), color-stop(1, #333333) );
	background:-moz-linear-gradient( center top, #999999 5%, #333333 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#999999", endColorstr="#333333");	background: -o-linear-gradient(top,#999999,333333);

	background-color:#999999;
}
.CSSTableGenerator tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.CSSTableGenerator tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}
</style>