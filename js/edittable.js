// 2006-08-21 - Created
// 2006-11-05 - Modified - head and body
function addColumn(tblId)
{
  var innings = parseInt(document.getElementById('innings').getAttribute('value'));
  document.getElementById('innings').setAttribute('value', innings+1);
  
  var tblHeadObj = document.getElementById(tblId).tHead;
  for ( var h = 0; h < tblHeadObj.rows.length; h++)
  {
    var newTH = document.createElement('th');
    tblHeadObj.rows[h].appendChild(newTH);
    newTH.innerHTML = '<th>' + (innings+1) + '</th>';
  }

  var tblBodyObj = document.getElementById(tblId).tBodies[0];

  var newCell = tblBodyObj.rows[0].insertCell(-1);
  newCell.innerHTML = '<td><input type="text" name="m' + (innings+1) + '" size="2" class="validate[\'length[2]\',\'digit[0,99]\']"/></td>';
  
  var newCell2 = tblBodyObj.rows[1].insertCell(-1);
  newCell2.innerHTML = '<td><input type="text" name="o' + (innings+1) + '" size="2" class="validate[\'length[2]\',\'digit[0,99]\']"/></td>';
}
function deleteColumn(tblId)
{
  var innings = parseInt(document.getElementById('innings').getAttribute('value'));
  if (innings > 9)
  {
    document.getElementById('innings').setAttribute('value', innings - 1);
    var allRows = document.getElementById(tblId).rows;
    for ( var i = 0; i < allRows.length; i++)
    {
      if (allRows[i].cells.length > 1)
      {
        allRows[i].deleteCell(-1);
      }
    }
  }
}