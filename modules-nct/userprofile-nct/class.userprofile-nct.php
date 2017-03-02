<?php
class UserProfile extends Home {
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_reminders';
    }

    public function getPageContent($userId) {
        $final_result = NULL;
        if ($userId == $this->sessUserId) {
            $main_content = new Templater(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php");
            $final_result = $main_content->parse();
        }

        $userData = $this->db->pdoQuery("SELECT u.*,ct.cityName,s.stateName,c.countryName FROM tbl_users AS u LEFT JOIN tbl_city AS ct ON u.cityId = ct.id  LEFT JOIN tbl_state AS s ON u.stateId = s.id LEFT JOIN tbl_country AS c ON u.countryId = c.id WHERE u.id = ?", array($userId))->result();

        $id = isset($userData['id']) ? $userData['id'] : '';
        $profileImage = isset($userData['profileImage']) ? $userData['profileImage'] : '';
        $salute = isset($userData['salute']) ? $userData['salute'] : '';
        $firstName = isset($userData['firstName']) ? $userData['firstName'] : '';
        $lastName = isset($userData['lastName']) ? $userData['lastName'] : '';
        $email = isset($userData['email']) ? base64_decode($userData['email']) : '';
        $member = isset($userData['member']) ? date('F d, Y', strtotime($userData['member'])) : '';
        $gender = isset($userData['gender']) ? (($userData['gender']=='f')?'Female':'Male') : 'Male';
        $birthDate = $userData['birthDate'] != '0000-00-00' ? date('F  d, Y', strtotime($userData['birthDate'])) : '-';
        $address = isset($userData['address']) ? $userData['address'] : '';
        $country = isset($userData['countryName']) ? $userData['countryName'] : '';
        $state = isset($userData['stateName']) ? $userData['stateName'] : '';
        $city = isset($userData['cityName']) ? $userData['cityName'] : '';
        $zipCode = $userData['zipCode']!=''?$userData['zipCode']:'-';
        $code = isset($userData['code']) ? $userData['code'] : '';
        $mobileNumber = isset($userData['mobileNumber']) ? $userData['mobileNumber'] : '';
        $paypalEmail = isset($userData['paypalEmail']) ? base64_decode($userData['paypalEmail']) : '-';
        $salute = (($userData['salute']=='mr')?'Mr.':(($userData['salute']=='mrs')?'Mrs.':(($userData['salute']=='ms')?'Miss.':'Dr.')));

 		// SITE_UPD_USER.$this->sessUserId.'/'.$userData['profileImage']; // use getImage()
        //$profile = getImage($userData['profileImage'], 'profile/' . $this->sessUserId, 120, 120);
        $profile = checkImage('profile/'.$userId.'/'.$userData['profileImage']);
        $reminder = $this->getReminder();
        $fields = array('%PROFILEIMG%', '%SAL%', '%FNAME%', '%LNAME%', '%EMAIL%', '%MEMBER%', '%GENDER%', '%BIRTH%', '%ADDRESS%', '%COUNTRY%', '%STATE%', '%CITY%', '%ZIP%', '%COUNTRY_CODE%','%MOBILE%', '%PMAIL%', '%USER_ID%', '%REMINDERS%', '%MODEL_PATH%', '%EDIT_PROF_URL%');
        $model_path = SITE_MOD.$this->module.'/ajax.'.$this->module.'.php?action=getReminderModel';
        $fields_replace = array($profile, $salute, $firstName, $lastName, $email, $member, $gender, $birthDate, $address, $country, $state, $city, $zipCode, $code, $mobileNumber, $paypalEmail, $this->sessUserId, $reminder, $model_path, get_link('edit_profile', $this->sessUserId));
        $final_result = str_replace($fields, $fields_replace, $final_result);
        return $final_result;
    }

    public function getReminderModel($rem_id) {
        $final_result = NULL;
        $main_content = new Templater(DIR_TMPL . $this->module . "/reminder-model.tpl.php");
        $final_result = $main_content->parse();
        $rem_title = $rem_note = NULL;  $rem_action = 'setReminder';
        $find = array("%REMINDER_TITLE%", "%REMINDER_NOTE%", "%REM_ACTION%", "%REM_ID%");
        if(!empty($rem_id)) {
            $reminder_data = $this->db->select($this->table, array('*'), array('id'=>$rem_id))->result();
            $replace = array($reminder_data['reminder_title'], date('d-m-Y', strtotime($reminder_data['reminder_date'])), 'editReminder', $rem_id);
            $final_result = str_replace($find, $replace, $final_result);
        } else {
            $replace = array($rem_title, $rem_note, $rem_action, $rem_id);
            $final_result = str_replace($find, $replace, $final_result);
        }
        echo $final_result;
        exit();
    }

    public function getUserData($userId) {
        $final_result = NULL;
        $this->sessUserId = $this->sessUserId;
        $main_content = new Templater(DIR_TMPL . $this->module . "/updateprofile-nct.tpl.php");
        $final_result = $main_content->parse();

        $qrySel = $this->db->select('tbl_users', array('*'), array('id' => $this->sessUserId, 'isActive' => 'y'));

        $userData = $qrySel->result();

        $id = $userData['id'];
        $salute = $userData['salute'];
        $firstName = $userData['firstName'];
        $lastName = $userData['lastName'];
        $email = base64_decode($userData['email']);
        $member = $userData['member'];
        $address = $userData['address'];
        $zipCode = $userData['zipCode']!=''?$userData['zipCode']:'';
        $profileImage = $userData['profileImage'];
        $code = $userData['code'];
        $mobileNumber = $userData['mobileNumber'];
        $birthDate = $userData['birthDate'] != '0000-00-00' ? date('m-d-Y', strtotime($userData['birthDate'])) : '';
        $gender = $userData['gender'];
        $paypalEmail = base64_decode($userData['paypalEmail']);

        $countryid = $userData['countryId'];
        $stateid = $userData['stateId'];
        $cityid = $userData['cityId'];

        $country = $this->getCountry($countryid);
        $states = $this->getStates($countryid, $stateid);
        $city = $this->getCities($stateid, $cityid);

        /* $follower_msg = SITE_URL."compose/".$userId.'/'; */
        $profile = checkImage($userData['profileImage'], 25);

        $salute_mr = (($userData['salute']=='mr')?'selected="selected"':'');
        $salute_mrs = (($userData['salute']=='mrs')?'selected="selected"':'');
        $salute_ms = (($userData['salute']=='ms')?'selected="selected"':'');
        $salute_dr = (($userData['salute']=='dr')?'selected="selected"':'');
        $selected_m = (($userData['gender']=='m')?'selected="selected"':'');
		$selected_f = (($userData['gender']=='f')?'selected="selected"':'');

        $fields = array('%PROFILEIMG%', '%SAL%', '%FNAME%', '%LNAME%', '%EMAIL%', '%MEMBER%', '%GENDER%', '%BIRTH%', '%ADDRESS%', '%COUNTRY%', '%STATE%', '%CITY%', '%ZIP%', '%MOBILE%', '%PMAIL%', '%USER_ID%', "%USER_AVATAR%", "%USER_NAME%", '%CROP_PATH%', '%USER_ID%', "%SELECTED_MR%", "%SELECTED_MRS%", "%SELECTED_MS%", "%SELECTED_DR%", "%SELECTED_M%", "%SELECTED_F%", "%SECRET_WORD%", '%BACK_BTN_URL%',"%COUNTRY_CODE%");
        $fields_replace = array($profile, $salute, $firstName, $lastName, $email, $member, $gender, $birthDate, $address, $country, $states['states'], $city['cities'], $zipCode, $mobileNumber, $paypalEmail, $id, checkImage('profile/'.$userId.'/'.$userData['profileImage']), $firstName . ' ' . $lastName, CROP_PATH, $this->sessUserId, $salute_mr, $salute_mrs, $salute_ms, $salute_dr, $selected_m, $selected_f, $userData['secret'], get_link('profile', $this->sessUserId),$code);
        $final_result = str_replace($fields, $fields_replace, $final_result);
        return $final_result;
    }

    public function getReminder() {
        $final_result = $edit_path = $reminder_html = NULL;
        $final_result = new Templater(DIR_TMPL . $this->module . "/reminder-nct.tpl.php");
        $final_result = $final_result->parse();
        $find = array("%REMINDER_NOTE%", "%DATE%", "%EDIT_LINK%", "%REM_ID%");
        $reminders = $this->db->select($this->table, array('*'), array('userId'=>$this->sessUserId), 'ORDER BY id DESC')->results();
        if(!empty($reminders)) {
            foreach ($reminders as $key => $value) {
                $edit_path = SITE_MOD.$this->module.'/ajax.'.$this->module.'.php?action=getReminderModel&rem_id='.$value['id'];
                $replace = array(($key+1).') '.$value['reminder_title'], date('F d, Y', strtotime($value['reminder_date'])), $edit_path, $value['id']);
                $reminder_html .= str_replace($find, $replace, $final_result);
            }
        } else {
            //$reminder_html = '<div class="clearfix"></div><div class="alert alert-danger"><p class="text-center nrf"><i class="fa fa-exclamation-circle"></i> No reminder found</p></div>';
            $main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
            $reminder_html = $main_content->parse();
        }
        return $reminder_html;
    }
}
?>