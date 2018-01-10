<?php
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('player.php');

if (!isset($_SESSION['playerid'])) 
{
  header('Location: index.php');
}

$id = $_SESSION['playerid'];

getSinglePlayer($id);

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Edit Profile &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/signup.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script> 
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/en.js"> </script>
	<script type="text/javascript" src="js/formcheck.js"> </script>
	<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
	<script type="text/javascript">
	  window.addEvent('domready', pageInitial);
	  var dateCheck = null;

	  function pageInitial() {
	    dateCheck = new FormCheck('edit');
	  }

	  var avlock = false;
	  function checkBirthDate(el){
	    if(avlock) return true;
	    avlock = true;

	    var elParent = el.getParent('div');

	    var birthdate = elParent.getElement("[name^=birthdate]");

	    if(dateCheck.validate(birthdate) && !validateUSDate(birthdate.value)) {
		    var errorMsg = "The date must be in format of MM/DD/YYYY";
		    el.errors.push(errorMsg);
		    avlock = false;
		    return false;
	    }

	    avlock = false;
	    return true;
	  }

	  function validateUSDate( strValue ) {
	    var objRegExp = /^\d{1,2}(\/)\d{1,2}\1\d{4}$/;
	     
	      //check to see if in correct format
	      if(!objRegExp.test(strValue)) {
	        return false; //doesn't match pattern, bad date
	      }
	      else{
	        var strSeparator = strValue.substring(2,3) ;
	        var arrayDate = strValue.split(strSeparator); 
	        //create a lookup for months not equal to Feb.
	        var arrayLookup = { '01' : 31,'03' : 31, 
	                            '04' : 30,'05' : 31,
	                            '06' : 30,'07' : 31,
	                            '08' : 31,'09' : 30,
	                            '10' : 31,'11' : 30,'12' : 31};
	        var intDay = parseInt(arrayDate[1],10); 

	        //check if month value and day value agree
	        if(arrayLookup[arrayDate[0]] != null) {
	          if(intDay <= arrayLookup[arrayDate[0]] && intDay != 0)
	            return true; //found in lookup table, good date
	        }
	        
	        //check for February (bugfix 20050322)
	        //bugfix  for parseInt kevin
	        //bugfix  biss year  O.Jp Voutat
	        var intMonth = parseInt(arrayDate[0],10);
	        if (intMonth == 2) { 
	           var intYear = parseInt(arrayDate[2]);
	           if (intDay > 0 && intDay < 29) {
	               return true;
	           }
	           else if (intDay == 29) {
	             if ((intYear % 4 == 0) && (intYear % 100 != 0) || 
	                 (intYear % 400 == 0)) {
	                  // year div by 4 and ((not div by 100) or div by 400) ->ok
	                 return true;
	             }   
	           }
	        }
	      }  
	      return false; //any other values, bad date
	    }
	</script> 
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <?php 
      if(isset($_SESSION['error']))
      {
        echo "<div id=\"error\"><p>" . $_SESSION['error'] . "</p></div>";
        unset($_SESSION['error']);
      }
      	
    ?>
    
    <div id="edit">
      <form method="POST" action="editsubmit.php" class="edit standard-form sided" id="edit">
        <div id="submit"><input class="btn-action"  type="submit" value="Submit"></div>
        <div id="data">
        <div id="leftPanel">
          <ul>
            <li><label>* First name:</label> <input type="text" name="firstname" class="validate['required','length[3,20]','nodigit'] text-input" value="<?php echo $players[$id]['firstname']; ?>"></li>
            <li><label>* Last name:</label> <input type="text" name="lastname" class="validate['required','length[3,20]','nodigit'] text-input" value="<?php echo $players[$id]['lastname']; ?>"></li>
            <li><label>* Email:</label> <input type="text" name="email" size="26" class="validate['required','email']" value="<?php echo $players[$id]['email']; ?>"></li>
            <li><label>Phone:</label> <input type="text" name="phone" class="validate['length[15]','phone'] text-input" value="<?php echo $players[$id]['phone']; ?>"></li>
          </ul>
        </div>
				
        <div id="rightPanel">
          <ul>
            <li><label>Nickname:</label> <input type="text" name="nickname" class="validate['length[30]'] text-input" value="<?php echo $players[$id]['nickname']; ?>"></li>
            <li><label>Positions:</label>
            
              <div id="positions">
                <input type="checkbox" name="positions[]" value="P" <?php if(strpos($players[$id]['positions'],"P")){echo " checked=\"checked\"";} ?> /> P
                <input type="checkbox" name="positions[]" value="C" <?php if(strpos($players[$id]['positions'],"C")){echo " checked=\"checked\"";} ?> /> C
                <input type="checkbox" name="positions[]" value="1B" <?php if(strpos($players[$id]['positions'],"1B")){echo " checked=\"checked\"";} ?> /> 1B
                <input type="checkbox" name="positions[]" value="3B" <?php if(strpos($players[$id]['positions'],"3B")){echo " checked=\"checked\"";} ?> /> 3B
                <input type="checkbox" name="positions[]" value="2B" <?php if(strpos($players[$id]['positions'],"2B")){echo " checked=\"checked\"";} ?> /> 2B
                <input type="checkbox" name="positions[]" value="SS" <?php if(strpos($players[$id]['positions'],"SS")){echo " checked=\"checked\"";} ?> /> SS
                <input type="checkbox" name="positions[]" value="OF" <?php if(strpos($players[$id]['positions'],"OF")){echo " checked=\"checked\"";} ?> /> OF
                <input type="checkbox" name="positions[]" value="Manager" <?php if(strpos($players[$id]['positions'],"Manager")){echo " checked=\"checked\"";} ?> /> Mngr
              </div>
            </li>
            <li><label>Bat:</label>
              <select name="bats">
                <option <?php if($players[$id]['bats'] == "R"){echo " selected=\"selected\"";} ?>>R</option>
                <option <?php if($players[$id]['bats'] == "L"){echo " selected=\"selected\"";} ?>>L</option>
              </select>
            </li>
            <li><label>Throws:</label>
              <select name="throws">
                <option <?php if($players[$id]['throws'] == "R"){echo " selected=\"selected\"";} ?>>R</option>
                <option <?php if($players[$id]['throws'] == "L"){echo " selected=\"selected\"";} ?>>L</option>
              </select>
            </li>
            <li><label>Height:</label> <input type="text" name="heightFT" size="1" class="validate['length[1]','digit[0,7]']" value="<?php echo $players[$id]['heightFT']; ?>">ft <input type="text" name="heightIN" size="2" class="validate['length[2]','digit[0,12]']" value="<?php echo $players[$id]['heightIN']; ?>">in</li>
            <li><label>Weight:</label> <input type="text" name="weight" size="3" class="validate['length[3]','digit[0,500]']" value="<?php echo $players[$id]['weight']; ?>"> lbs</li>
            <li><label for="birthdate">Birthdate:</label> <input type="text" id="birthdate" name="birthdate" size="8" class="validate['~checkBirthDate']" value="<?php echo $players[$id]['birthdate']; ?>" /> MM/DD/YYYY</li>
          </ul>
        </div>
        </div>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>