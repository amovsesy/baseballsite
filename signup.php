<?php
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Sign Up &#124; MadDogs</title>
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
	    dateCheck = new FormCheck('signup');
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
      /*
       *
       * This should be an error div that gets set by the js.
       * Each field should have an error string underneath each element
       * in order to surface the errors that matter.
       */
      if(isset($_SESSION['error']))
      {
        echo "<div id=\"error\"><p>" . $_SESSION['error'] . "</p></div>";
        unset($_SESSION['error']);
      }
      	
    ?>
    
    <div id="signuppage">
      <form method="POST" action="signupsubmit.php" class="signup standard-form sided" id="signup">
        <div id="submit"><input class="btn-action" type="submit" value="Sign up"></div>
        <div id="data">
        <div id="leftPanel">
          <ul>
            <li><label for="firstname">* First name:</label> <input type="text" id="firstname" name="firstname" class="validate['required','length[3,20]','nodigit'] text-input"/></li>
            <li><label for="lastname">* Last name:</label> <input type="text" id="lastname" name="lastname" class="validate['required','length[3,20]','nodigit'] text-input"/></li>
            <li><label for="email">* Email:</label> <input type="text" id="email" name="email" class="validate['required','email'] text-input"/></li>
            <li><label for="password">* New password:</label> <input type="password" id="password" name="password" class="validate['required','length[5,-1]','alphanum'] text-input"/></li>
            <li><label for="passwordConfirm">* Confirm Password:</label> <input type="password" id="passwordConfirm" name="passwordConfirm" class="validate['confirm:password'] text-input"/></li>
            <li><label for="teamPassword">* Team Password:</label> <input type="password" id="teamPassword" name="teamPassword" class="validate['required','length[3,20]','alphanum'] text-input"/></li>
            <li><label for="phone">* Phone:</label> <input type="text" id="phone" name="phone" class="validate['required','length[15]','phone'] text-input"/></li>
          </ul>
        </div>
				
        <div id="rightPanel">
          <ul>
            <li><label for="nickname">Nickname:</label> <input type="text" id="nickname" name="nickname" class="validate['length[30]'] text-input"></li>
            <li><label>Positions:</label>
            <div id="positions">
                <input type="checkbox" name="positions[]" value="P" /> P
                <input type="checkbox" name="positions[]" value="C" /> C
                <input type="checkbox" name="positions[]" value="1B" /> 1B
                <input type="checkbox" name="positions[]" value="3B" /> 3B
                <input type="checkbox" name="positions[]" value="2B" /> 2B
                <input type="checkbox" name="positions[]" value="SS" /> SS
                <input type="checkbox" name="positions[]" value="OF" /> OF
                <input type="checkbox" name="positions[]" value="Manager" /> Mngr
              </div>
            </li>
            <li><label for="bats">Bat:</label>
              <select id="bats" name="bats">
                <option>R</option>
                <option>L</option>
              </select>
            </li>
            <li><label for="throws">Throw:</label>
              <select id="throws" name="throws">
                <option>R</option>
                <option>L</option>
              </select>
            </li>
            <li><label for="heightFT">Height:</label> <input type="text" size="2" id="heightFT" name="heightFT" class="validate['length[1]','digit[4,7]']"/>ft <input type="text" id="heightIN" name="heightIN" size="2" class="validate['length[2]','digit[0,12]']"/>in</li>
            <li><label for="weight">Weight:</label> <input type="text" size="2" id="weight" name="weight" class="validate['length[3]','digit[0,500]']"/> lbs</li>
            <li><label for="birthdate">Birthdate:</label> <input type="text" id="birthdate" name="birthdate" size="8" class="validate['~checkBirthDate']" /> MM/DD/YYYY</li>
          </ul>
        </div>
        </div>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>