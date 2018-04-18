<script language="Javascript">
var moveIdx=new Array(-1,-1); // [0] is Src, [1] is Dst
function highlightMove(cmd)
{
	/* Clear old highlighting */
	for (i=0;i<2;i++)
		if (moveIdx[i]!=-1) {
			x=moveIdx[i]%8;
			y=parseInt(moveIdx[i]/8);
			if ((y+1+x)%2==0)
				img="wsquare.jpg";
			else
				img="bsquare.jpg";
			obj=window.document.getElementById("btd"+moveIdx[i]);
			if (obj)
				obj.style.backgroundImage="url(./chessset/"+img+")";
			moveIdx[i]=-1;
		}
	
	/* If command is empty don't highlight again */
	if (cmd==null || cmd=="")
		return;

	/* Parse command for source/destination and highlight it */
	moveIdx[0]=(cmd.charCodeAt(2)-49)*8+(cmd.charCodeAt(1)-97);
	if (cmd.length>=6)
		moveIdx[1]=(cmd.charCodeAt(5)-49)*8+(cmd.charCodeAt(4)-97);
	else
		moveIdx[1]=-1;

	/* Set new highlighting */
	for (i=0;i<2;i++)
		if (moveIdx[i]!=-1) {
			x=moveIdx[i]%8;
			y=parseInt(moveIdx[i]/8);
			if ((y+1+x)%2==0)
				img="whsquare.jpg";
			else
				img="bhsquare.jpg";
			obj=window.document.getElementById("btd"+moveIdx[i]);
			if (obj)
				obj.style.backgroundImage="url(./chessset/"+img+")";
		}
}

function checkMoveButton()
{
	if (window.document.commandForm && window.document.commandForm.moveButton) {
		if (window.document.commandForm.move.value.length >= 6)
			window.document.commandForm.moveButton.disabled=false;
		else
			window.document.commandForm.moveButton.disabled=true;
	}
}

/* Assemble command into commandForm.move and submit move if destination is
 * clicked twice. */
function assembleCmd(part)
{
	var cmd = window.document.commandForm.move.value;
	if (cmd == part)
		window.document.commandForm.move.value = "";
	else if (cmd.length == 0 || cmd.length >= 6) {
		if (part.charAt(0) != '-' && part.charAt(0) != 'x')
			window.document.commandForm.move.value = part;
		else if (cmd.length >= 6 && cmd.substring(3,6)==part) {
			onClickMove();
		}
	} else if (part.charAt(0) == '-' || part.charAt(0) == 'x')
		window.document.commandForm.move.value = cmd + part;
	else
		window.document.commandForm.move.value = part;
	highlightMove(window.document.commandForm.move.value);
	checkMoveButton();
	return false;
}

function onClickMove()
{
	if (document.commandForm.move.value!="") {
		var move=document.commandForm.move.value;
		parent.reload_session;
		/* If pawn enters last line ask for promotion */
		if (move.charAt(0)=='P' && (move.charAt(5)=='8' || move.charAt(5)=='1')) {
			if (confirm('Promote to QUEEN? (Click Cancel to promote to ROOK)')){move=move+'Q';}
			else if (confirm('Promote to ROOK? (Click Cancel to promote to BISHOP)')){move=move+'R';}
			else if (confirm('Promote to BISHOP? (Click Cancel to promote to KNIGHT)')){move=move+'B';}
			else if (confirm('Promote to KNIGHT? (Click Cancel to abort move)')){move=move+'N';}
			else{return;}
		}
		document.commandForm.cmd.value=move;
		document.commandForm.submit();
	}
}
</script>