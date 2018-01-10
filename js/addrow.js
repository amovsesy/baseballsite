// Last updated 2006-02-21
function addRowToTable(tblId)
{
  var images = parseInt(document.getElementById('numimages').getAttribute('value'));
  document.getElementById('numimages').setAttribute('value', images+1);
  
  var tbl = document.getElementById(tblId);
  var lastRow = tbl.rows.length;
  // if there's no header row in the table, then iteration = lastRow + 1
  var row = tbl.insertRow(lastRow);
  
  // right cell
  var cellRight = row.insertCell(0);
  var el = document.createElement('input');
  el.type = 'file';
  el.name = 'image' + (images+1);
  cellRight.appendChild(el);
}

function removeRowFromTable(tblId)
{
  var images = parseInt(document.getElementById('numimages').getAttribute('value'));
  
  if (images > 1)
  {
    var tbl = document.getElementById(tblId);
    var lastRow = tbl.rows.length;
    if (lastRow > 1)
      tbl.deleteRow(lastRow - 1);
    document.getElementById('numimages').setAttribute('value', images-1);
  }
}