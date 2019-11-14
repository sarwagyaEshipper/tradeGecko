<?php
header('p3p: CP="ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV"');
ob_start();
session_start();
set_time_limit(0);
ini_set("display_errors", 1);
ini_set('session.gc_maxlifetime', 36000);
session_set_cookie_params(36000);
error_reporting(E_ALL);
// echo $_SESSION['token'];

if (! isset($_SESSION['shop'])) {
    // code...
    header('location:/admin/apps');
}
require_once 'TradeGecoClient.php';
require_once 'util/loggerUtil.php';

require_once 'properties/application.properties.php';
require_once 'properties/' . $profile . '-application.properties.php';

define('TRADEGECO_CLIENTID', $clientId);
define('TRADEGECO_SECRET', $clientSecret);
define('TRADEGECO_SCOPE', $tradegecoScope);
define('TRADEGECO_URL', $tradegecoURL);

include_once 'config.php';
include 'header.php';
require_once 'functions.php';
// ////////////////////////

$shop = $_SESSION['shop'];
$tradegecoClient = new TradeGecoClient(TRADEGECO_URL,  $_SESSION['token'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);
$shopDetails = $tradegecoClient->call('GET','/accounts/current');

// ///////////////////////
$query = 'SELECT AccessToken from TradeStoreDetail WHERE StoreUrl="'. $_SESSION['shop'] .'" AND IsAuthenticated = "1" LIMIT 1';
$obj = mysqli_query($con, $query);
$resultArray = mysqli_fetch_assoc($obj);

if ($resultArray['AccessToken'] !== $_SESSION['token']) {
    // code...
    $isAuth = false;
} else {
    $isAuth = true;
}

// require 'config.php';

if (isAuthenticated() && $isAuth) {
    header('location:welcomeAboard.php');
}

?>
<link rel="stylesheet" type="text/css"
	href="assets/css/style-welcome.css">
<link rel="stylesheet" type="text/css"
	href="assets/css/font-awesome.min.css">
<!-- NEW FORM  -->




<body class="customer_sign_up">
	<input type="hidden"
		value="<?php if(isset($shopDetails['account']['country'])){ echo $shopDetails['account']['country']; } ?>"
		id="getCountrySelector">
	<input type="hidden"
		value="<?php if(isset($shopDetails['account']['region'])){ echo $shopDetails['account']['region']; } ?>"
		id="getProvinceSelector">
	<section class="sign_up_sec">
		<form action="login.php">
			<button class="btn btn-info btn-lg sign_up_button back_button">&lt;
				Back to Login Page</button>
		</form>
		<div class="container">
			<div class="row">
				<div class="new_customer">
					<div class="sign_up_content">
						<h3>Sign Up</h3>
					</div>
					<div class="sign_up_detail">
						<form class="form_detail" id="registrationForm">
							<div class="detail_content">
								<h4>Account Details</h4>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-6 form_detail_div">
									<label class="control-label" for="username">Username <span
										class="required">*</span></label>
									<div class="input-group input_field_detail">
										<span class="input-group-addon"><img
											src="assets/images/man-user.png" alt="user_icon"></span> <input
											type="text" class="form-control" ata-required="true"
											name="username"
											value="<?php if(isset($shopDetails['account']['name'])){ echo $shopDetails['account']['name']; } ?>">
									</div>
								</div>
								<div class="col-sm-6">
									<label class="control-label" for="email">Password <span
										class="required">*</span></label>
									<div class="input-group input_field_detail">
										<span class="input-group-addon"><img
											src="assets/images/lock.png" alt="user_icon"></span> <input
											type="password" class="form-control" id="regPassword"
											name="regPassword">
									</div>
								</div>

							</div>
							<div class="form-group form_field row">
								<div class="col-sm-12 form_detail_div">
									<label class="control-label" for="regEmail">Email <span
										class="required">*</span></label>
									<div class="input-group input_field_detail">
										<span class="input-group-addon"><img
											src="assets/images/close-envelope.png" alt="user_icon"></span>
										<input type="email" class="form-control" data-required="true"
											name="regEmail" id="regEmail" required="required"
											value="<?php if(isset($shopDetails['account']['contact_email'])){ echo $shopDetails['account']['contact_email']; } ?>">
									</div>
								</div>

							</div>


							<div class="detail_content">
								<h4>Business Information</h4>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-6 form_detail_div">
									<label class="control-label" for="email">Business Name <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<input type="text" class="form-control" name="regCompanyName"
											id="regCompanyName"
											value="<?php if(isset($shopDetails['account']['name'])){ echo $shopDetails['account']['name']; } ?>">
									</div>
								</div>
								<div class="col-sm-6">
									<label class="control-label" for="email">Contact Number <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<input type="text" class="form-control" name="regPhone"
											id="regPhone"
											value="<?php if(isset($shopDetails['account']['contact_phone'])){ echo $shopDetails['account']['contact_phone']; } ?>">
									</div>
								</div>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-12 form_detail_div">
									<label class="control-label" for="email">Address <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<input type="text" class="form-control" name="regadd1"
											id="regadd1"
											value="">
									</div>
								</div>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-6 form_detail_div">
									<label class="control-label" for="email">City <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<input type="text" class="form-control" name="regCity"
											id="regCity"
											value="">
									</div>
								</div>
								<!-- -->
								<div class="col-sm-6 form_detail_div">
									<label class="control-label" for="email">Country <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<select class="form-control" name="customerCountry"
											id="checkout_shipping_address_country">
											<option data-code="-1" value="-1">Select a country</option>\
											<option value="US" data-code="United States">United States</option>
											<option value="CA" data-code="Canada">Canada</option>
											<option value="IT" data-code="Italy">Italy</option>
											<option value="FR" data-code="France">France</option>
											<option value="AF" data-code="Afghanistan">Afghanistan</option>
											<option value="AX" data-code="Aland Islands">Åland Islands</option>
											<option value="AL" data-code="Albania">Albania</option>
											<option value="DZ" data-code="Algeria">Algeria</option>
											<option value="AD" data-code="Andorra">Andorra</option>
											<option value="AO" data-code="Angola">Angola</option>
											<option value="AI" data-code="Anguilla">Anguilla</option>
											<option value="AG" data-code="Antigua And Barbuda">Antigua
												&amp; Barbuda</option>
											<option value="AR" data-code="Argentina">Argentina</option>
											<option value="AM" data-code="Armenia">Armenia</option>
											<option value="AW" data-code="Aruba">Aruba</option>
											<option value="AU" data-code="Australia">Australia</option>
											<option value="AT" data-code="Austria">Austria</option>
											<option value="AZ" data-code="Azerbaijan">Azerbaijan</option>
											<option value="BS" data-code="Bahamas">Bahamas</option>
											<option value="BH" data-code="Bahrain">Bahrain</option>
											<option value="BB" data-code="Barbados">Barbados</option>
											<option value="BY" data-code="Belarus">Belarus</option>
											<option value="BE" data-code="Belgium">Belgium</option>
											<option value="BZ" data-code="Belize">Belize</option>
											<option value="BJ" data-code="Benin">Benin</option>
											<option value="BM" data-code="Bermuda">Bermuda</option>
											<option value="BT" data-code="Bhutan">Bhutan</option>
											<option value="BO" data-code="Bolivia">Bolivia</option>
											<option value="BA" data-code="Bosnia And Herzegovina">Bosnia
												&amp; Herzegovina</option>
											<option value="BW" data-code="Botswana">Botswana</option>
											<option value="BV" data-code="Bouvet Island">Bouvet Island</option>
											<option value="IO" data-code="British Indian Ocean Territory">British
												Indian Ocean Territory</option>
											<option value="VG" data-code="Virgin Islands, British">British
												Virgin Islands</option>
											<option value="BN" data-code="Brunei">Brunei</option>
											<option value="BG" data-code="Bulgaria">Bulgaria</option>
											<option value="BF" data-code="Burkina Faso">Burkina Faso</option>
											<option value="BI" data-code="Burundi">Burundi</option>
											<option value="KH" data-code="Cambodia">Cambodia</option>
											<option value="CM" data-code="Republic of Cameroon">Cameroon</option>
											<option value="CA" data-code="Canada">Canada</option>
											<option value="CV" data-code="Cape Verde">Cape Verde</option>
											<option value="KY" data-code="Cayman Islands">Cayman Islands</option>
											<option value="CF" data-code="Central African Republic">Central
												African Republic</option>
											<option value="TD" data-code="Chad">Chad</option>
											<option value="CL" data-code="Chile">Chile</option>
											<option value="CN" data-code="China">China</option>
											<option value="CX" data-code="Christmas Island">Christmas
												Island</option>
											<option value="CC" data-code="Cocos (Keeling) Islands">Cocos
												(Keeling) Islands</option>
											<option value="CO" data-code="Colombia">Colombia</option>
											<option value="KM" data-code="Comoros">Comoros</option>
											<option value="CG" data-code="Congo">Congo - Brazzaville</option>
											<option value="CD"
												data-code="Congo, The Democratic Republic Of The">Congo -
												Kinshasa</option>
											<option value="CK" data-code="Cook Islands">Cook Islands</option>
											<option value="CR" data-code="Costa Rica">Costa Rica</option>
											<option value="HR" data-code="Croatia">Croatia</option>
											<option value="CU" data-code="Cuba">Cuba</option>
											<option value="CW" data-code="Curaçao">Curaçao</option>
											<option value="CY" data-code="Cyprus">Cyprus</option>
											<option value="CZ" data-code="Czech Republic">Czech Republic</option>
											<option value="CI" data-code="Côte d'Ivoire">Côte
												d’Ivoire</option>
											<option value="DK" data-code="Denmark">Denmark</option>
											<option value="DJ" data-code="Djibouti">Djibouti</option>
											<option value="DM" data-code="Dominica">Dominica</option>
											<option value="DO" data-code="Dominican Republic">Dominican
												Republic</option>
											<option value="EC" data-code="Ecuador">Ecuador</option>
											<option value="EG" data-code="Egypt">Egypt</option>
											<option value="SV" data-code="El Salvador">El Salvador</option>
											<option value="GQ" data-code="Equatorial Guinea">Equatorial
												Guinea</option>
											<option value="ER" data-code="Eritrea">Eritrea</option>
											<option value="EE" data-code="Estonia">Estonia</option>
											<option value="ET" data-code="Ethiopia">Ethiopia</option>
											<option value="FK" data-code="Falkland Islands (Malvinas)">Falkland
												Islands</option>
											<option value="FO" data-code="Faroe Islands">Faroe Islands</option>
											<option value="FJ" data-code="Fiji">Fiji</option>
											<option value="FI" data-code="Finland">Finland</option>
											<option value="FR" data-code="France">France</option>
											<option value="GF" data-code="French Guiana">French Guiana</option>
											<option value="PF" data-code="French Polynesia">French
												Polynesia</option>
											<option value="TF" data-code="French Southern Territories">French
												Southern Territories</option>
											<option value="GA" data-code="Gabon">Gabon</option>
											<option value="GM" data-code="Gambia">Gambia</option>
											<option value="GE" data-code="Georgia">Georgia</option>
											<option value="DE" data-code="Germany">Germany</option>
											<option value="GH" data-code="Ghana">Ghana</option>
											<option value="GI" data-code="Gibraltar">Gibraltar</option>
											<option value="GR" data-code="Greece">Greece</option>
											<option value="GL" data-code="Greenland">Greenland</option>
											<option value="GD" data-code="Grenada">Grenada</option>
											<option value="GP" data-code="Guadeloupe">Guadeloupe</option>
											<option value="GT" data-code="Guatemala">Guatemala</option>
											<option value="GG" data-code="Guernsey">Guernsey</option>
											<option value="GN" data-code="Guinea">Guinea</option>
											<option value="GW" data-code="Guinea Bissau">Guinea-Bissau</option>
											<option value="GY" data-code="Guyana">Guyana</option>
											<option value="HT" data-code="Haiti">Haiti</option>
											<option value="HM"
												data-code="Heard Island And Mcdonald Islands">Heard &amp;
												McDonald Islands</option>
											<option value="HN" data-code="Honduras">Honduras</option>
											<option value="HK" data-code="Hong Kong">Hong Kong SAR China</option>
											<option value="HU" data-code="Hungary">Hungary</option>
											<option value="IS" data-code="Iceland">Iceland</option>
											<option value="IN" data-code="India">India</option>
											<option value="ID" data-code="Indonesia">Indonesia</option>
											<option value="IR" data-code="Iran, Islamic Republic Of">Iran</option>
											<option value="IQ" data-code="Iraq">Iraq</option>
											<option value="IE" data-code="Ireland">Ireland</option>
											<option value="IM" data-code="Isle Of Man">Isle of Man</option>
											<option value="IL" data-code="Israel">Israel</option>
											<option value="IT" data-code="Italy">Italy</option>
											<option value="JM" data-code="Jamaica">Jamaica</option>
											<option value="JP" data-code="Japan">Japan</option>
											<option value="JE" data-code="Jersey">Jersey</option>
											<option value="JO" data-code="Jordan">Jordan</option>
											<option value="KZ" data-code="Kazakhstan">Kazakhstan</option>
											<option value="KE" data-code="Kenya">Kenya</option>
											<option value="KI" data-code="Kiribati">Kiribati</option>
											<option value="XK" data-code="Kosovo">Kosovo</option>
											<option value="KW" data-code="Kuwait">Kuwait</option>
											<option value="KG" data-code="Kyrgyzstan">Kyrgyzstan</option>
											<option value="LA"
												data-code="Lao People's Democratic Republic">Laos</option>
											<option value="LV" data-code="Latvia">Latvia</option>
											<option value="LB" data-code="Lebanon">Lebanon</option>
											<option value="LS" data-code="Lesotho">Lesotho</option>
											<option value="LR" data-code="Liberia">Liberia</option>
											<option value="LY" data-code="Libyan Arab Jamahiriya">Libya</option>
											<option value="LI" data-code="Liechtenstein">Liechtenstein</option>
											<option value="LT" data-code="Lithuania">Lithuania</option>
											<option value="LU" data-code="Luxembourg">Luxembourg</option>
											<option value="MO" data-code="Macao">Macau SAR China</option>
											<option value="MK" data-code="Macedonia, Republic Of">Macedonia</option>
											<option value="MG" data-code="Madagascar">Madagascar</option>
											<option value="MW" data-code="Malawi">Malawi</option>
											<option value="MY" data-code="Malaysia">Malaysia</option>
											<option value="MV" data-code="Maldives">Maldives</option>
											<option value="ML" data-code="Mali">Mali</option>
											<option value="MT" data-code="Malta">Malta</option>
											<option value="MQ" data-code="Martinique">Martinique</option>
											<option value="MR" data-code="Mauritania">Mauritania</option>
											<option value="MU" data-code="Mauritius">Mauritius</option>
											<option value="YT" data-code="Mayotte">Mayotte</option>
											<option value="MX" data-code="Mexico">Mexico</option>
											<option value="MD" data-code="Moldova, Republic of">Moldova</option>
											<option value="MC" data-code="Monaco">Monaco</option>
											<option value="MN" data-code="Mongolia">Mongolia</option>
											<option value="ME" data-code="Montenegro">Montenegro</option>
											<option value="MS" data-code="Montserrat">Montserrat</option>
											<option value="MA" data-code="Morocco">Morocco</option>
											<option value="MZ" data-code="Mozambique">Mozambique</option>
											<option value="MM" data-code="Myanmar">Myanmar (Burma)</option>
											<option value="NA" data-code="Namibia">Namibia</option>
											<option value="NR" data-code="Nauru">Nauru</option>
											<option value="NP" data-code="Nepal">Nepal</option>
											<option value="NL" data-code="Netherlands">Netherlands</option>
											<option value="AN" data-code="Netherlands Antilles">Netherlands
												Antilles</option>
											<option value="NC" data-code="New Caledonia">New Caledonia</option>
											<option value="NZ" data-code="New Zealand">New Zealand</option>
											<option value="NI" data-code="Nicaragua">Nicaragua</option>
											<option value="NE" data-code="Niger">Niger</option>
											<option value="NG" data-code="Nigeria">Nigeria</option>
											<option value="NU" data-code="Niue">Niue</option>
											<option value="NF" data-code="Norfolk Island">Norfolk Island</option>
											<option value="KP"
												data-code="Korea, Democratic People's Republic Of">North
												Korea</option>
											<option value="NO" data-code="Norway">Norway</option>
											<option value="OM" data-code="Oman">Oman</option>
											<option value="PK" data-code="Pakistan">Pakistan</option>
											<option value="PS"
												data-code="Palestinian Territory, Occupied">Palestinian
												Territories</option>
											<option value="PA" data-code="Panama">Panama</option>
											<option value="PG" data-code="Papua New Guinea">Papua New
												Guinea</option>
											<option value="PY" data-code="Paraguay">Paraguay</option>
											<option value="PE" data-code="Peru">Peru</option>
											<option value="PH" data-code="Philippines">Philippines</option>
											<option value="PN" data-code="Pitcairn">Pitcairn Islands</option>
											<option value="PL" data-code="Poland">Poland</option>
											<option value="PT" data-code="Portugal">Portugal</option>
											<option value="QA" data-code="Qatar">Qatar</option>
											<option value="RE" data-code="Reunion">Réunion</option>
											<option value="RO" data-code="Romania">Romania</option>
											<option value="RU" data-code="Russia">Russia</option>
											<option value="RW" data-code="Rwanda">Rwanda</option>
											<option value="SX" data-code="Sint Maarten">Saint Martin</option>
											<option value="WS" data-code="Samoa">Samoa</option>
											<option value="SM" data-code="San Marino">San Marino</option>
											<option value="ST" data-code="Sao Tome And Principe">São
												Tomé &amp; Príncipe</option>
											<option value="SA" data-code="Saudi Arabia">Saudi Arabia</option>
											<option value="SN" data-code="Senegal">Senegal</option>
											<option value="RS" data-code="Serbia">Serbia</option>
											<option value="SC" data-code="Seychelles">Seychelles</option>
											<option value="SL" data-code="Sierra Leone">Sierra Leone</option>
											<option value="SG" data-code="Singapore">Singapore</option>
											<option value="SK" data-code="Slovakia">Slovakia</option>
											<option value="SI" data-code="Slovenia">Slovenia</option>
											<option value="SB" data-code="Solomon Islands">Solomon
												Islands</option>
											<option value="SO" data-code="Somalia">Somalia</option>
											<option value="ZA" data-code="South Africa">South Africa</option>
											<option value="GS"
												data-code="South Georgia And The South Sandwich Islands">South
												Georgia &amp; South Sandwich Islands</option>
											<option value="KR" data-code="South Korea">South Korea</option>
											<option value="ES" data-code="Spain">Spain</option>
											<option value="LK" data-code="Sri Lanka">Sri Lanka</option>
											<option value="BL" data-code="Saint Barthélemy">St.
												Barthélemy</option>
											<option value="SH" data-code="Saint Helena">St. Helena</option>
											<option value="KN" data-code="Saint Kitts And Nevis">St.
												Kitts &amp; Nevis</option>
											<option value="LC" data-code="Saint Lucia">St. Lucia</option>
											<option value="MF" data-code="Saint Martin">St. Martin</option>
											<option value="PM" data-code="Saint Pierre And Miquelon">St.
												Pierre &amp; Miquelon</option>
											<option value="VC" data-code="St. Vincent">St. Vincent &amp;
												Grenadines</option>
											<option value="SD" data-code="Sudan">Sudan</option>
											<option value="SR" data-code="Suriname">Suriname</option>
											<option value="SJ" data-code="Svalbard And Jan Mayen">Svalbard
												&amp; Jan Mayen</option>
											<option value="SZ" data-code="Swaziland">Swaziland</option>
											<option value="SE" data-code="Sweden">Sweden</option>
											<option value="CH" data-code="Switzerland">Switzerland</option>
											<option value="SY" data-code="Syria">Syria</option>
											<option value="TW" data-code="Taiwan">Taiwan</option>
											<option value="TJ" data-code="Tajikistan">Tajikistan</option>
											<option value="TZ" data-code="Tanzania, United Republic Of">Tanzania</option>
											<option value="TH" data-code="Thailand">Thailand</option>
											<option value="TL" data-code="Timor Leste">Timor-Leste</option>
											<option value="TG" data-code="Togo">Togo</option>
											<option value="TK" data-code="Tokelau">Tokelau</option>
											<option value="TO" data-code="Tonga">Tonga</option>
											<option value="TT" data-code="Trinidad and Tobago">Trinidad
												&amp; Tobago</option>
											<option value="TN" data-code="Tunisia">Tunisia</option>
											<option value="TR" data-code="Turkey">Turkey</option>
											<option value="TM" data-code="Turkmenistan">Turkmenistan</option>
											<option value="TC" data-code="Turks and Caicos Islands">Turks
												&amp; Caicos Islands</option>
											<option value="TV" data-code="Tuvalu">Tuvalu</option>
											<option value="UM"
												data-code="United States Minor Outlying Islands">U.S.
												Outlying Islands</option>
											<option value="UG" data-code="Uganda">Uganda</option>
											<option value="UA" data-code="Ukraine">Ukraine</option>
											<option value="AE" data-code="United Arab Emirates">United
												Arab Emirates</option>
											<option value="GB" data-code="United Kingdom">United Kingdom</option>
											<option value="US" data-code="United States">United States</option>
											<option value="UY" data-code="Uruguay">Uruguay</option>
											<option value="UZ" data-code="Uzbekistan">Uzbekistan</option>
											<option value="VU" data-code="Vanuatu">Vanuatu</option>
											<option value="VA" data-code="Holy See (Vatican City State)">Vatican
												City</option>
											<option value="VE" data-code="Venezuela">Venezuela</option>
											<option value="VN" data-code="Vietnam">Vietnam</option>
											<option value="WF" data-code="Wallis And Futuna">Wallis &amp;
												Futuna</option>
											<option value="EH" data-code="Western Sahara">Western Sahara</option>
											<option value="YE" data-code="Yemen">Yemen</option>
											<option value="ZM" data-code="Zambia">Zambia</option>
											<option value="ZW" data-code="Zimbabwe">Zimbabwe</option>

										</select>

									</div>
								</div>


								<!-- -->

							</div>
							<div class="form-group form_field row">
								<div class="col-sm-6">
									<label class="control-label" for="email">Province / State <span
										class="required">*</span></label>
									<div class="input-group input_field_detail information_field">
										<select class="form-control dataProvince select form-input"
											id="addProvinces" name="selectedProvince" id="">
											<option value="">Select a province</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<label class="control-label" for="email">Postal Code / Zip Code
										<span class="required">*</span>
									</label>
									<div class="input-group input_field_detail information_field">
										<input type="text" class="form-control" name="regZip"
											id="regZip"value="">
									</div>
								</div>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-12 form_detail_div">
									<div class="form_checked">
										<input class="form-control form_checkbox_field"
											type="checkbox" id="term-condition"> <label
											class="checked_text" for="term-condition">I have read &amp;
											agree to the Terms of Services &amp; Privacy Policy.</label>
									</div>
									<div id="tc-check" class="input_field_detail information_field"></div>
								</div>
							</div>
							<div class="form-group form_field row">
								<div class="col-sm-12 form_detail_div">
									<div class="sign_btn_sec">
										<button type="button" id="eshipperRegister"
											class="btn sign_up_btn">Sign Up</button>
										<button type="button" class="btn reset_btn"
											onclick="$('#registrationForm')[0].reset();">Reset</button>
									</div>
									<ul id="eshippersErrMsg">
									</ul>
								</div>
							</div>
						</form>

					</div>
				</div>
			</div>
		</div>
	</section>

	<div id="form-loading">
		<img src="assets/images/Ajax-loader.gif">
	</div>






	<script type="text/javascript">

$(document).ready(function(){
	////////////////////////////////////////////
// 	var countrySelector = $('#getCountrySelector').val();
// 	if (countrySelector!='' || countrySelector!='-1') {

		
// var countryNameGet = $("#checkout_shipping_address_country option[data-code="+countrySelector+"]").val();
// $('#checkout_shipping_address_country')
//     .val(countryNameGet)
//     .trigger('change');
//     $('#checkout_shipping_address_country').trigger('change')
// 	};

///////////////////////////////////////////////

$(document).on('click','#eshipperRegister', function(e){

e.preventDefault();
/////////////////////////////

      var c = 0;
$('.form-control').each(function(){



if(($(this).val()=='') || ($(this).val()=='-1'))
{
c++;

$(this).css('border','1px solid red');
}
else{
$(this).css('border','solid 1px #3fb4c1')
}
// if($(this).attr('name')=='regCompanyName')
//   {

//     var sl = $(this).val();
//     if(sl.length < 6)
//     {
     
//     $(this).css('border','1px solid red');
   
//       //alert('sl '+c);

//       $('#eshipperRegMsg').html('Company name must be atleast 6 characters long.');
      
//     }
//     else{
//       $(this).css('border','solid 1px #3fb4c1')

//       $('#eshipperRegMsg').html('');
//       c=0;

//     }
//   }

});

if( c > 0){
$('#eshippersErrMsg').html('<li>All fields are required.</li><li>Please make sure that Company name is atleast 6 characters long</li>');

return false;
}
 else{
      $('#eshippersErrMsg').html('');

    }
    if($('#term-condition').prop('checked')==false)

{
$('#tc-check').html('<p>Please agree to Terms of Services & Privacy Policy before continuing.</p>');
return false;
}


///////////////////////////////
  console.log( $( this ).find('form#registrationForm').serialize());
    //alert($('form#registrationForm').serialize());
     $.ajax({
             url: 'register.php',
             type: 'post',
             data: $('form#registrationForm').serialize(),
             beforeSend: function(){
               $("#form-loading").show();
   },
             success: function(r) {
       //           // Re-enable add to cart button.
                    
             var m = JSON.parse(r);
             var data = m.msg;
             var s = m.state;
             if (data=='Success') {
              location.reload();
            } 

            else {
            	$("#form-loading").hide();
              alert(data);

            }
        
           }, 
           error: function() {
            //alert();
            $("#form-loading").hide();
            alert('We are unable to complete your request at the moment,please try again.');
           }
          });


});


////////////////////////// registration javascript code /////////////////////////////////////////

$(document).on('change', '#checkout_shipping_address_country', function(){
var x = window.Countries;
var s ='';
var c = $('#checkout_shipping_address_country option:selected').attr('data-code');
console.log('country==='+ c);
var e = $('#addProvinces');
if(c===''){return false;}
console.log(typeof x.Australia.provinces);
var p = x[c].provinces;
var l = x[c].province_codes;
var p = x[c].provinces;
console.log(p);
if(p){
Object.keys(p).forEach(function(key) {
     var pc = p[key];
     s += '<option value="'+l[pc]+'">'+p[key]+'</option>';
});
if (s ==='') {  }
    e.find('option').remove().end().append(s).show();

}
else
{
  e.find('option').remove().end().append('<option>Select a province</option>');return false;
}


});
///////////////////////////////////////////////

});



  </script>
	<script type="text/javascript">
    ShopifyApp.init({
      apiKey: "<?php echo $api_key; ?>",
      shopOrigin:"<?php echo $_SESSION['shop']; ?>",

      debug: true
    });
    </script>
	<script type="text/javascript">
  ShopifyApp.ready(function(){
    ShopifyApp.Bar.initialize({
      
      title: 'EShipper/Sign Up',
      
          
          callback: function(){ 
            ShopifyApp.Bar.loadingOff();
            
          }
       
      
    });
  });
  $(window).load(function(){

	var countrySelector = $('#getCountrySelector').val();
	console.log('ccc '+countrySelector);
	if (countrySelector!='' || countrySelector!='-1') {

		
 var countryNameGet = $("#checkout_shipping_address_country option[value="+countrySelector+"]").val();
 $('#checkout_shipping_address_country')
     .val(countryNameGet)
    .trigger('change');
    $('#checkout_shipping_address_country').trigger('change');
    var p = $('#getProvinceSelector').val();
    if(p!='' || p!= undefined){
    $('select#addProvinces').val(p);
}
 	};

  });
  </script>

</body>
