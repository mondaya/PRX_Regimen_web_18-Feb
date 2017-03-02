<?php
    function redirectPage($url) {
        header('Location:' . $url);
        exit;
    }

    function printr($data, $exit = false) {
        print '<pre>';
        print_r($data);
        print '</pre>';
        if ($exit)
            exit();
    }

    function requiredLoginId() {
        global $sessUserType, $sesspUserId, $sessUserId;
        if (isset($sessUserType) && $sessUserType == 's')
            return $sesspUserId;
        else
            return $sessUserId;
    }

    function check_user_login() {
        global $sessUserId;
        if ($sessUserId <= 0) {
            $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => REQUIRE_LOGIN));
            redirectPage(SITE_URL);
        }
    }

    function checkBuyStatus($sessUserId){
        $status = getTableValue('tbl_users','buyStatus',array('id'=>$sessUserId));

        if($status == 'n'){
            $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Please wait for purchase approval."));
            redirectPage(SITE_URL);
        }
    }

    function check_user_not_login() {
        global $sessUserId, $module, $msgType;
        if ($sessUserId > 1) {
            if ($module == 'home-nct') {
                $_SESSION["msgType"] = isset($msgType) ? $msgType : '';
                redirectPage(SITE_URL . 'user_dashboard/');
            } else {
                redirectPage(SITE_URL);
            }
        }
    }

    function domain_details($returnWhat) {
        global $localFolderNm;

        $arrScriptName = explode('/', $_SERVER['SCRIPT_NAME']);
        /* if(in_array('guncertain', $arrScriptName) == true) {
          $arrKey = array_search('com', $arrScriptName);
          unset($arrScriptName[$arrKey]);
          } */
        $sitePath = $localFolderNm;
        $i = 0;
        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}$/', $_SERVER['HTTP_HOST'])) {
            $i = 1;
        } else if ($_SERVER["SERVER_NAME"] == 'demo.ncryptedprojects.com') {
            $i = 0;
        }

        $arrScriptName = array_values($arrScriptName);

        if ($returnWhat == 'module')
            return ($arrScriptName[3 + $i] != "" ? $arrScriptName[3 + $i] : '');
        else if ($returnWhat == 'dir')
            return ($arrScriptName[1 + $i] != "" ? $arrScriptName[1 + $i] : '');
        else if ($returnWhat == 'file')
            return ($arrScriptName[4 + $i] != "" ? $arrScriptName[4 + $i] : '');
        else if ($returnWhat == 'file-module')
            return ($arrScriptName[2 + $i] != "" ? $arrScriptName[2 + $i] : '');
    }

    function check_admin_dir() {
        global $localFolderNm;
        $arrScriptName = explode('/', $_SERVER['SCRIPT_NAME']);
        $sitePath = $localFolderNm;
        $i = 0;
        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}$/', $_SERVER['HTTP_HOST'])) {
            $i = 0;
        } else if ($_SERVER["SERVER_NAME"] == 'worldwidetutors.ncryptedprojects.com') {
            $i = -1;
        }
        $arrScriptName = array_values($arrScriptName);
        return ($arrScriptName[2 + $i] != "" ? $arrScriptName[2 + $i] : '');
    }

    function Authentication($reqAuth = false, $redirect = true) {
        $todays_date = date("Y-m-d");
        global $adminUserId, $sessUserId, $db, $msgType;
        $whichSide = domain_details('dir');
        if ($reqAuth == true) {
            if ($whichSide == 'admin-nct') {
                if ($adminUserId == 0) {
                    $msgType = $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => 'loginRequired'));
                    $_SESSION['req_uri_adm'] = $_SERVER['REQUEST_URI'];
                    if ($redirect)
                        redirectPage(SITE_ADMIN_URL);
                    else
                        return false;
                } else
                    return true;
            }
            else {
                if ($sessUserId <= 0) {
                    $msgType = $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => 'loginRequired'));
                    $_SESSION['req_uri'] = $_SERVER['REQUEST_URI'];
                    if ($redirect)
                        redirectPage(SITE_URL);
                    else
                        return false;
                } else
                    return true;
            }
        }
    }

    function redirectErrorPage($error) {
        echo $error;
        //redirectPage(SITE_URL.'modules/error?u='.base64_encode($error));
    }

    function disMessage($msgArray, $script = true) {
        $message = '';
        $content = '';
        $type = isset($msgArray["type"]) ? $msgArray["type"] : NULL;
        $var = isset($msgArray["var"]) ? $msgArray["var"] : NULL;
        if (!is_null($var)) {
            switch ($var) {
                case 'loginRequired' : { $message = MSG_LOGIN_REQUIRE; break; }
                case 'invaildUsers' : { $message = MSG_INVALID_USER; break; }
                case 'NRF' : { $message = MSG_NRF; break; }
                case 'alreadytaken': { $message = MSG_EMAIL_TAKEN; break; }
                case 'fillAllvalues' : { $message = MSG_FILL_VALUE; break; }
                case 'InvalidEmail' : { $message = MSG_PROP_EMAIL; break; }
                case 'EnterEmail' : { $message = MSG_EMAIL_EMAIL; break; }
                case 'InviteNotSentYourSelf' : { $message = MSG_INVITE_NOT_SENT_YOUR_SELF; break; }
                case 'InviteSentExceptYourSelf' : { $message = MSG_INVITE_SENT_EXCEPT_YOUR_SELF; break; }
                case 'InviteSuc' : { $message = MSG_INVITE_SUC; break; }
                case 'succActivateAccount' : { $message = MSG_SUCC_ACTIVATE; break; }
                case 'inactivatedUser' : { $message = MSG_NOT_ACTIVATE; break; }
                case 'unapprovedUser' : { $message = MSG_NOT_APPROVE; break;};
                case 'succChangePass' : { $message = MSG_SUCC_CHANGEPASS; break; }
                case 'incorectActivate' : { $message = MSG_INCORECT_ACTIVATE; break; }

                ## global admin
                case 'userExist' : { $message = MSG_USER_EXIST; break; }
                case 'emailExist' : { $message = MSG_EMAIL_EXIST; break; }
                case 'sucNewslater' : { $message = "Your have successfully subscribed our newslatter."; break; }
                case 'sucNewslater2' : { $message = "Your have successfully active your subscription."; break; }
                case 'userNameExist' : { $message = MSG_USER_EXIST; break; }
                case 'succLogout' : { $message = MSG_SUCC_LOGOUT; break; }
                case 'succregwithoutact' : { $message = MSG_SUCC_REGISTER; break; }

                case 'recAdded' : { $message = MSG_REC_ADD; break; }
                case 'recEdited' : { $message = MSG_REC_EDIT; break; }
                case 'recActivated' : { $message = MSG_REC_ACTIVE; break; }
                case 'recDeActivated' : { $message = MSG_REC_DEACTIVE; break; }
                case 'recDeleted' : { $message = MSG_REC_DELETE; break; }
                case 'recExist' : { $message = MSG_REC_EXIST; break; }

                case 'wrongPass' : { $message = MSG_WRONGPASS; break; }
                case 'passNotmatch' : { $message = MSG_PASSNOTMATCH; break; }
                case 'NoPermission' : { $message = NO_PERMISSION; break; }
                case 'recImported' : { $message = MSG_REC_IMPORTED; break; }
                case 'succForgotPass' : { $message = MSG_SUCC_FORGOTPASS; break; }
                case 'invalidCaptcha' : { $message = MSG_INVALID_CAPTCHA; break; }
                case 'BlockedUser' : { $message = MSG_BLOCKED_USER; break; }
                case 'RemainEmailVerify' : { $message = MSG_REMAIN_EMAIL_VERIFICATION; break; }
                case 'wrongemail' : { $message = MSG_WRONG_EMAIL; break; }
                case 'incorectReset' : { $message = MSG_INCORECT_RESET; break; }

                ## new constant
                case 'SignupSuccess' : { $message = MSG_SIGNUP_SUCCESS; break; }
                case 'SocialSignupSuccess' : { $message = MSG_SOCIAL_SIGNUP_SUCCESS; break; }
                case 'ProfileUpdated' : { $message = MSG_PROFILE_UPDATED; break; }
                case 'ProfessionalUpdated' : { $message = MSG_PROFESSIONAL_UPDATED; break; }
                case 'wrongOldPass' : { $message = MSG_WRONG_OLD_PASS; break; }
                case 'ProvideDetailUpdated' : { $message = MSG_PROVIDE_DETAIL_UPDATED; break; }
                case 'invalidMembershipPlan' : { $message = MSG_INVALID_MEMBERSHIP_PLAN; break; }
                case 'CancelMembershipPlan' : { $message = MSG_CANCEL_MEMBERSHIP_PLAN; break; }
                case 'SuccessMembershipPlan' : { $message = MSG_SUCCESS_MEMBERSHIP_PLAN; break; }
                case 'LessonExist' : { $message = MSG_LESSON_EXIST; break; }
                case 'LessonAdded' : { $message = MSG_LESSON_ADDED; break; }
                case 'LessonEdited' : { $message = MSG_LESSON_EDITED; break; }
                case 'invalidLink' : { $message = MSG_INVALID_LINK; break; }
                case 'MaterialAdded' : { $message = MSG_MATERIAL_ADDED; break; }
                case 'MaterialEdited' : { $message = MSG_MATERIAL_EDITED; break; }
                case 'NotPurchaseOwnProduct' : { $message = MSG_NOT_PURCHASE_OWN_PRODUCT; break; }
                case 'ReviewAdded' : { $message = MSG_REVIEW_ADDED; break; }
                case 'ReviewEdited' : { $message = MSG_REVIEW_EDITED; break; }
                case 'OwnLessonError' : { $message = CANT_BOOK_OWN_LESSON; break; }
                case 'TutorNotAvailable' : { $message = TUTOR_NOT_AVAILABLE_ON_SELECTED_DATE; break; }
                case 'CancelLessonBooking' : { $message = MSG_CANCEL_LESSON_BOOKING; break; }
                case 'SuccessLessonBooking' : { $message = MSG_SUCCESS_LESSON_BOOKING; break; }
                case 'InvalidTimeDuration' : { $message = TIME_DURATION_MUST_BE_SAME_AS_MIN_DURATION; break; }
                case 'sendemails' : { $message = 'Newsletter has been sent successfully.'; break; }
                case 'existNewslater' : { $message = "Your have already subscribed our newslatter."; break; }
                default : { $message = $var; break; }
            }
        }
        $type1 = $type == 'suc' ? 'success' : 'error';
        if ($script)
            $content = '<script type="text/javascript" language="javascript">toastr["' . $type1 . '"]("' . $message . '");</script>';
        else
            $content = $message;
        return $content;
    }

    function get_time_difference($start, $end) {
        $uts['start'] = strtotime($start);
        $uts['end'] = strtotime($end);
        if ($uts['start'] !== -1 && $uts['end'] !== -1) {
            if ($uts['end'] >= $uts['start']) {
                $diff = $uts['end'] - $uts['start'];
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                return( array('days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $diff) );
            }
            else {
                // trigger_error("Ending date/time is earlier than the start date/time", E_USER_WARNING);
            }
        } else {
            // trigger_error("Invalid date/time data detected", E_USER_WARNING);
        }
        return( false );
    }

    function genrateRandom($length = 8, $seeds = 'alphanum') {
        // Possible seeds
        $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seedings['numeric'] = '0123456789';
        $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $seedings['hexidec'] = '0123456789abcdef';

        // Choose seed
        if (isset($seedings[$seeds])) {
            $seeds = $seedings[$seeds];
        }

        // Seed generator
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        // Generate
        $str = '';
        $seeds_count = strlen($seeds);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seeds_count - 1)};
        }

        return $str;
    }

    function get_link($page, $content='') {
        global $db,$sessUserType;
        switch($page){
            case 'logout' : { $url = SITE_URL.'logout'; break; }
            case 'cms' : { $url = SITE_URL.'cms/'.$content; break; }
            case 'profile' : { $url = SITE_URL.'profile/'.$content; break; }
            case 'edit_profile' : { $url = SITE_URL.'editprofile/'.$content; break; }
            case 'product_url' : {$url = SITE_URL.'product/'.$content; break;}
            case 'account_settings' : {$url = SITE_URL.'settings/'.$content;; break;}
            case 'get_cms_link': {
                $row = $db->select("tbl_content",array("*"),array("isActive"=>'y',"pageName"=>$content))->result();
                $url = get_link('cms', slug($row['pageTitle']).'/'.$row['pId']);
                break;
            }
            default : {$url = SITE_URL; break; }
        }
        return $url;
    }

    function selfURL() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            $serverrequri = $_SERVER['PHP_SELF'];
        } else {
            $serverrequri = $_SERVER['REQUEST_URI'];
        }
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = strLeft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $serverrequri;
    }

    function strLeft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

    // Get IP Address
    function get_ip_address() {
        foreach (array(
    'HTTP_CLIENT_IP',
     'HTTP_X_FORWARDED_FOR',
     'HTTP_X_FORWARDED',
     'HTTP_X_CLUSTER_CLIENT_IP',
     'HTTP_FORWARDED_FOR',
     'HTTP_FORWARDED',
     'REMOTE_ADDR'
        ) as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    /*function getPagerData($numHits, $limit, $page) {
        $numHits = (int) $numHits;
        $limit = max((int) $limit, 1);
        $page = (int) $page;
        $numPages = ceil($numHits / $limit);

        $page = max($page, 1);
        $page = min($page, $numPages);

        $offset = ($page - 1) * $limit;

        $ret = new stdClass;

        $ret->offset = $offset;
        $ret->limit = $limit;
        $ret->numPages = $numPages;
        $ret->page = $page;

        return $ret;
    }*/

    function convertDate($date, $time = false, $what = 'default') {
        if ($what == 'wherecond')
            return date('Y-m-d', strtotime($date));
        else if ($what == 'display')
            return date('M d, Y h:i A', strtotime($date));
        else if ($what == 'onlyDate')
            return date('M d, Y', strtotime($date));
        else if ($what == 'gmail') {
            return date('D, M d, Y - h:i A', strtotime($date));
            //Tue, Jul 16, 2013 at 12:14 PM
        } else if ($what == 'default') {
            if (trim($date) != '' && $date != '0000-00-00' && $date != '1970-01-01') {
                if (!$time) {
                    $retDt = date('d-m-Y', strtotime($date));
                    return $retDt == '01-01-1970' ? '' : $retDt;
                } else {
                    '1970-01-01 01:00:00';
                    '01-01-1970 01:00 AM';
                    $retDt = date('d-m-Y h:i A', strtotime($date));
                    return $retDt == '01-01-1970 01:00 AM' ? '' : $retDt;
                }
            } else
                return '';
        }else if ($what == 'db') {
            if (trim($date) != '' && $date != '0000-00-00' && $date != '1970-01-01') {
                if (!$time) {
                    $retDt = date('Y-m-d', strtotime($date));
                    return $retDt == '1970-01-01' ? '' : $retDt;
                } else {
                    $retDt = date('Y-m-d H:i:s', strtotime($date));
                    return $retDt == '1970-01-01 01:00:00' ? '' : $retDt;
                }
            } else
                return '';
        }
    }

    function downloadFiles($dir, $file) {
        header("Content-type: application/force-download");
        header('Content-Disposition: inline; filename="' . $dir . $file . '"');
        header("Content-Transfer-Encoding: Binary");
        header("Content-length: " . filesize($dir . $file));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile("$dir$file");
    }

    function getMetaTags($metaArray) {
        $content = NULL;
        $content = '<meta name="description" content="' . $metaArray["description"] . '" /><meta name="keywords" content="' . $metaArray["keywords"] . '" /><meta name="author" content="' . $metaArray["author"] . '" />';
        if (isset($metaArray["nocache"]) && $metaArray["nocache"] == true) {
            $content .= '<meta HTTP-EQUIV="CACHE-CONTROL" content="NO-CACHE" />';
        }
        return sanitize_output($content);
    }

    function getTotalRows($tableName, $condition = '', $countField = '*') {
        global $db;
        $qrysel0 = $db->select($tableName, $countField, $condition);
        $totlaRows = $qrysel0->affectedRows();
        return $totlaRows;
    }

    function emptyStringReplace($val, $replaceWith = '-') {
        return trim($val) == '' ? $replaceWith : trim($val);
    }

    function GenerateThumbnail($varPhoto, $uploadDir, $tmp_name, $th_arr = array(), $file_nm = '', $addExt = true, $crop_coords = array()) {
        //$ext=strtoupper(substr($varPhoto,strlen($varPhoto)-4));die;
        $ext = '.' . strtoupper(getExt($varPhoto));
        $tot_th = count($th_arr);
        if (($ext == ".JPG" || $ext == ".GIF" || $ext == ".PNG" || $ext == ".BMP" || $ext == ".JPEG" || $ext == ".ICO")) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777);
            }

            if ($file_nm == '')
                $imagename = rand() . time();
            else
                $imagename = $file_nm;

            if ($addExt || $file_nm == '')
                $imagename = $imagename . $ext;

            $pathToImages = $uploadDir . $imagename;
            $Photo_Source = copy($tmp_name, $pathToImages);

            if ($Photo_Source) {
                for ($i = 0; $i < $tot_th; $i++) {
                    resizeImage($uploadDir . $imagename, $uploadDir . 'th' . ($i + 1) . '_' . $imagename, $th_arr[$i]['width'], $th_arr[$i]['height'], false, $crop_coords);
                }

                return $imagename;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function resizeImage($filename, $newfilename = "", $max_width, $max_height = '', $withSampling = true, $crop_coords = array()) {

        if ($newfilename == "")
            $newfilename = $filename;

        $fileExtension = strtolower(getExt($filename));
        if ($fileExtension == "jpg" || $fileExtension == "jpeg") {
            $img = imagecreatefromjpeg($filename);
        } else if ($fileExtension == "png") {
            $img = imagecreatefrompng($filename);
        } else if ($fileExtension == "gif") {
            $img = imagecreatefromgif($filename);
        } else
            $img = imagecreatefromjpeg($filename);

        $width = imageSX($img);
        $height = imageSY($img);

        // Build the thumbnail
        $target_width = $max_width;
        $target_height = $max_height;
        $target_ratio = $target_width / $target_height;
        $img_ratio = $width / $height;

        if (empty($crop_coords)) {

            if ($target_ratio > $img_ratio) {
                $new_height = $target_height;
                $new_width = $img_ratio * $target_height;
            } else {
                $new_height = $target_width / $img_ratio;
                $new_width = $target_width;
            }

            if ($new_height > $target_height) {
                $new_height = $target_height;
            }
            if ($new_width > $target_width) {
                $new_height = $target_width;
            }
            $new_img = imagecreatetruecolor($target_width, $target_height);

            $white = imagecolorallocate($new_img, 255, 255, 255);
            imagecolortransparent($new_img);
            @imagefilledrectangle($new_img, 0, 0, $target_width - 1, $target_height - 1, $white);
            @imagecopyresampled($new_img, $img, ($target_width - $new_width) / 2, ($target_height - $new_height) / 2, 0, 0, $new_width, $new_height, $width, $height);

            //$new_img = imagecreatetruecolor($new_width, $new_height);
            //@imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        } else {
            $new_img = imagecreatetruecolor($target_width, $target_height);
            $white = imagecolorallocate($new_img, 255, 255, 255);
            @imagefilledrectangle($new_img, 0, 0, $target_width - 1, $target_height - 1, $white);
            @imagecopyresampled($new_img, $img, 0, 0, $crop_coords['x1'], $crop_coords['y1'], $target_width, $target_height, $crop_coords['x2'], $crop_coords['y2']);
        }

        if ($fileExtension == "jpg" || $fileExtension == "jpeg") {
            $createImageSave = imagejpeg($new_img, $newfilename, 90);
        } else if ($fileExtension == 'png') {
            $createImageSave = imagepng($new_img, $newfilename, 9);
        } else if ($fileExtension == "gif") {
            $createImageSave = imagegif($new_img, $newfilename, 90);
        } else
            $createImageSave = imagejpeg($new_img, $newfilename, 90);
    }

    function checkImage($imagePath, $imageName = '') {
        if (is_file(DIR_UPD . $imagePath . $imageName)) {
            return SITE_UPD . $imagePath . $imageName;
        } else {
            return SITE_IMG . 'no_image_thumb.png';
        }
    }

    function getExt($file) {
        $path_parts = pathinfo($file);
        $ext = $path_parts['extension'];
        return $ext;
    }

    function ConverCurrency($amount, $from_currency, $to_currency) {
        $string = $amount . strtolower($from_currency) . "=?" . strtolower($to_currency);
        $google_url = "http://www.google.com/ig/calculator?hl=en&q=" . $string;
        $result = file_get_contents($google_url);
        $result = explode('"', $result);
        $confrom = explode(' ', $result[1]);
        $conto = explode(' ', $result[3]);
        return $conto[0];
    }

    function getHelpImg($title = '') {
        //return '<img src="' . SITE_IMG . 'help-icon.png" alt="help" class="vtip" title="' . $title . '"/>';
        return '';
    }

    function getStrToArray($str, $sep = ',') {
        $retArr = array();
        $pos = strpos($str, $sep);
        if ($str != '') {
            if ($pos !== false) {
                $retArr = explode($sep, $str);
            } else
                $retArr[] = $str;
        } else
            $retArr = array();
        return $retArr;
    }

    function escapeSearchString($srch) {
        if (is_object($srch)) {
            $res = new stdClass();
            foreach ($srch as $k => $v) {
                $res->$k = trim(mysql_real_escape_string(str_replace(array(
                    '_',
                    '%'
                                        ), array(
                    '\_',
                    '\%'
                                        ), $v)));
            }
        } else {
            $res = trim(mysql_real_escape_string(str_replace(array(
                '_',
                '%'
                                    ), array(
                '\_',
                '\%'
                                    ), $srch)));
        }
        return $res;
    }

    function removeAccents($s, $d = true) {
        $s = str_replace('ó', 'o', $s);
        if ($d)
            $s = utf8_encode($s);
        $chars = array(
            '_' => '/`|´|\^|~|¨|ª|º|©|®/',
            'a' => '/à|á|â|ã|ä|å|æ/',
            'e' => '/è|é|ê|ë/',
            'i' => '/ì|í|î|ĩ|ï/',
            'o' => '/ò|ó|ô|õ|ö|ó|ø/',
            'u' => '/ù|ú|û|ű|ü|ů/',
            'A' => '/À|Á|Â|Ã|Ä|Å|Æ/',
            'E' => '/È|É|Ê|Ë/',
            'I' => '/Ì|Í|Î|Ĩ|Ï/',
            'O' => '/Ò|Ó|Ô|Õ|Ö|ó|Ø/',
            'U' => '/Ù|Ú|Û|Ũ|Ü|Ů/',
            'c' => '/ć|ĉ|ç/',
            'C' => '/Ć|Ĉ|Ç/',
            'n' => '/ñ/',
            'N' => '/Ñ/',
            'y' => '/ý|ŷ|ÿ/',
            'Y' => '/Ý|Ŷ|Ÿ/'
        );
        $s = trim($s);
        $s = str_replace("&#39;", "'", $s);
        $s = str_replace("&#34;", "\"", $s);
        $s = str_replace(" ", "_", $s);
        $s = str_replace("-", "", $s);
        $s = str_replace("?", "", $s);
        $s = str_replace("&", "", $s);
        $s = str_replace("'", "", $s);
        $s = str_replace("\"", "", $s);
        return str_replace(" ", "-", strtolower(preg_replace($chars, array_keys($chars), $s)));
    }

    function seoString($s) {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $s), '-'));
    }

    function sanitize_output($buffer) {

        //ob_start("sanitize_output"); -- add to top of code befoer outputing. make sure all js are well defined.
        /*      '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
          '/[^\S ]+\</s',  // strip whitespaces before tags, except space
          '/(\s)+/s'       // shorten multiple whitespace sequences
         */
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s');
        $replace = array('>', '<', '\\1', '');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }

    function loadingImg($width = '') {
        $width = ($width != '') ? ' width="' . $width . '"' : '';
        return '<img src="' . SITE_IMG . 'processing.gif" alt="please wait" title="please wait" class="loading" ' . $width . '/>';
    }

    function load_css($filename = array()) {
        $returnStyle = '';
        $filePath = array();
        if (!empty($filename)) {
            //if(domain_details('dir') == 'com'){
            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_ADM_CSS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_ADM_CSS . $v;
                }
            }
            //}
            /* else{
              foreach($filename as $k=>$v){
              if(is_array($v)){
              if(isset($v[1]) && $v[1]!=""){
              $filePath[] = $v[1].$v[0];
              }
              else{
              $filePath[] = SITE_CSS.$v[0];
              }
              }else{
              $filePath[] = SITE_CSS.$v;
              }
              }
              } */
        }
        foreach ($filePath as $style) {
            $returnStyle .= '<link rel="stylesheet" type="text/css" href="' . $style . '">';
        }
        return $returnStyle;
    }

    function load_js($filename = array()) {
        $returnStyle = '';
        $filePath = array();
        if (!empty($filename)) {

            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_ADM_JS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_ADM_JS . $v;
                }
            }
        }
        foreach ($filePath as $scripts) {
            $returnStyle .= '<script type="text/javascript" src="' . $scripts . '"></script>';
        }
        return $returnStyle;
    }

    function myTruncate($string, $limit, $break = " ", $pad = "...", $onlyText = true) {
        $string = ($onlyText == true) ? str_replace('&nbsp;', ' ', strip_tags($string)) : $string;
        // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit)
            return $string;

        // is $break present between $limit and the end of the string?
        if (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }

        return $string;
    }

    function generateTemplates($greetings, $regards, $subject, $msgContent) {
        $content = '<html><body>';
        $content .= '<div style="background-color:#F9F9F9; border:1px solid #E1E1E1; padding:25px; font-family:Verdana, Geneva, sans-serif">
            <div style="padding:0 0 25px 0; color:#006; font-size:22px;"><strong><u>' . $subject . '</u></strong></div>
            <div style="font-size:12px;">
            <p>Hello' . ($greetings != '' ? '&nbsp;' . $greetings : '') . ',</p>
            <p>&nbsp;</p>
            ' . $msgContent . '
            <p>&nbsp;</p>
            <p>Regards,<br />
            ' . $regards . '</p>
                </div>
            </div></body></html>';
        return $content;
    }

    function generateEmailTemplate($type, $arrayCont) {
        global $sessUserId, $db;

        $q = $db->select('tbl_email_templates', array("subject", "templates"), array("constant" => $type))->result();
        $subject = trim(stripslashes($q["subject"]));
        $subject = str_replace("###SITE_NM###", SITE_NM, $subject);

        $message = trim(stripslashes($q["templates"]));
        $message = str_replace("###SITE_LOGO###", SITE_IMG . SITE_LOGO, $message);
        $message = str_replace("###SITE_URL###", SITE_URL, $message);
        $message = str_replace("###SITE_NM###", SITE_NM, $message);
        $message = str_replace("###YEAR###", date('Y'), $message);
        $message = str_replace("###ABOUT_URL###", SITE_URL . 'about_us', $message);
        $message = str_replace("###CONTACT_URL###", SITE_URL . 'contact_us', $message);

        $array_keys = (array_keys($arrayCont));
        for ($i = 0; $i < count($array_keys); $i++) {
            $message = str_replace("###" . $array_keys[$i] . "###", "" . $arrayCont[$array_keys[$i]] . "", $message);
            $subject = str_replace("###" . $array_keys[$i] . "###", "" . $arrayCont[$array_keys[$i]] . "", $subject);
        }

        $data['message'] = trim($message);
        $data['subject'] = $subject;
        return $data;
    }

    function sendEmailAddress($to, $subject, $message) {
        $headers = "Reply-To: " . SITE_NM . " <" . ADMIN_EMAIL . ">\r\n";
        $headers.= "From: " . SITE_NM . " <" . ADMIN_EMAIL . ">\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        $headers.= "Content-type: text/html; charset=iso-8859-1\r\n";

        require_once("class.phpmailer.php");
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled

        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT; // or 587

        $mail->IsHTML(true);
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        //$mail->SetFrom(SMTP_USERNAME);
        $mail->SetFrom(ADMIN_EMAIL);
        $mail->AddReplyTo(ADMIN_EMAIL, SITE_NM);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AddAddress($to);
        $result = true;
        if (!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            $result = false;
        }

        return true;
    }

    function merge_key_value($array, $sep = ' ') {
        $out = '';
        foreach ($array as $key => $value)
            $out.= $key . $sep . "'" . $value . "'";
        return $out;
    }

    // Send Mail Function
    function sendMail($to, $constant, $karr) {
        require_once("class.phpmailer.php");
        $result = true;
        $from_nm = FROM_NM;
        $from_email = FROM_EMAIL;

        if (is_array($constant)) {
            $mailbody = (isset($constant['message'])) ? $constant['message'] : '';
            $subject = (isset($constant['subject'])) ? $constant['subject'] : '';
        } else {
            $template_array = generateEmailTemplate($constant, $karr);
            $mailbody = (isset($template_array['message'])) ? $template_array['message'] : '';
            $subject = (isset($template_array['subject'])) ? $template_array['subject'] : '';
        }

        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        //$mail->SMTPSecure = "tls";
        $mail->Host = SMTP_HOST;
        $mail->Port = 587; // or 587
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SetFrom($from_email, $from_nm);
        $mail->AddReplyTo($from_email, $from_nm);
        $mail->Subject = $subject;
        $mail->AddAddress($to);
        $mail->Body = $mailbody;
        $mail->addCustomHeader("X-Priority: 3");
        $mail->addCustomHeader("Organization: " . SITE_NM . "");
        //$mail->AddBCC("pratik.padia@ncrypted.com", "Pratik Padia");
        //$mail->AddBCC("farhin.imtiyaz@gmail.com", "Farhin Imtiyaz");
        if (!$mail->Send()) { /* echo "Mailer Error: " . $mail->ErrorInfo; */
            $result = false;
        }
        return $result;
    }

    function getTableValue($table, $field, $wherecon = "") {
        global $db;
        $qrySel = $db->select($table, array($field), $wherecon);
        $qrysel1 = $qrySel->result();
        $totalRow = $qrySel->affectedRows();
        $fetchRes = $qrysel1;

        if ($totalRow > 0) {
            return $fetchRes[$field];
        } else {
            return "";
        }
    }

    function isToken($token) {
        global $db;
        if (isset($token) && $token) {

            //verification values in BD
            $query = "SELECT id FROM tbl_users WHERE registrationNo='$token'";


            $qrysel0 = $db->pdoQuery($query);
            $totalRow = $qrysel0->affectedRows();
            if ($totalRow > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function generateUniqueToken($number = 10) {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');
        $token = "";
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $token .= $arr[$index];
        }

        if (isToken($token)) {
            return generateUniqueToken($number);
        } else {
            return $token;
        }
    }

    function upload_file_store($file_arr = array(), $data_array = array()) {
        global $db, $sessUserId;
        $folder = "";
        $all_types = $image_dimension = array();
        $folder = $img_type = $path_folder = '';
        $all_types = array("jpg", "jpeg", "png", "gif");

        if ($data_array['type'] == 'banner') {
            $folder = DIR_STORE . $data_array['id'] . '/';
            $path_folder = SITE_STORE . $data_array['id'] . '/';
        }

        if (!$file_arr['name'])
            return false;
        $file_title = $file_arr['name'];

        //Get file extension
        $file_name = strtolower(pathinfo($file_arr['name'], PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($file_arr['name'], PATHINFO_EXTENSION));

        //Not really uniqe - but for all practical reasons, it is
        $uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);
        $file_name = $file_name . '_' . $uniqer . '.' . $ext;

        //$all_types = explode(",",strtolower($types));
        if ($all_types) {
            if (in_array($ext, $all_types))
                ;
            else {
                $result = "'" . $file_arr['name'] . "' is not a valid file."; //Show error if any.
                return false;
            }
        }

        //Where the file must be uploaded to
        if ($folder) {
            if (!is_dir($folder))
                mkdir($folder, 0777);
        }

        $uploadfile = $folder . $file_name;
        $result = '';

        //Move the file from the stored location to the new location
        if (($data_array['upload_from'] != 'url' && !move_uploaded_file($file_arr['tmp_name'], $uploadfile)) || ($data_array['upload_from'] == 'url' && !copy($file_arr['tmp_name'], $uploadfile))) {
            $result = "Cannot upload the file '" . $file_arr['name'] . "'"; //Show error if any.
            if (!file_exists($folder)) {
                $result .= " : Folder don't exist.";
            } elseif (!is_writable($folder)) {
                $result .= " : Folder not writable.";
            } elseif (!is_writable($uploadfile)) {
                $result .= " : File not writable.";
            }
            $file_name = '';
            return false;
        } else {
            if (count($image_dimension) > 0) {
                for ($i = 0; $i < count($image_dimension); $i++) {
                    resize($uploadfile, $folder . 'th' . ($i + 1) . '_' . $file_name, $image_dimension[$i]['width'], $image_dimension[$i]['height']);
                }
            }
            if (!empty($data_array['old_file'])) {
                unlink($folder . $data_array['old_file']);
                for ($i = 1; $i <= count($image_dimension); $i++) {
                    unlink($folder . 'th' . $i . '_' . $data_array['old_file']);
                }
            }
        }
        $file = array("filepath" => $path_folder, "file_name" => $file_name);
        return $file;
    }

    function GenerateThumbnailInGD($imageArr, $width, $height, $dirpath, $newName = '') {
        $size = $imageArr["size"];
        $name = $imageArr["name"];
        $tmp_name = $imageArr["tmp_name"];
        $type = $imageArr["type"];
        if ($size > 0) {
            if ($type == "image/pjpeg" || $type == "image/png" || $type == "image/bmp" || $type == "image/jpeg" || $type == "image/gif") {
                $ext = strtoupper(substr($name, strlen($name) - 4));
                $name = $newName . $ext;
                $TmpName_File = $tmp_name;

                resizeImage($tmp_name, $dirpath . $name, $width, $height, false, array());
                return $name;
            } else {
                return "not a valid type";
            }
        } else {
            return "Please select files";
        }
    }

    /*function pagination($pager, $page, $module, $jsFuncParam) {
        $content = $jsFuncVariables = '';
        for ($i = 0; $i < count($jsFuncParam); $i++) {
            $jsFuncVariables .= '\'' . $jsFuncParam[$i] . '\',';
        }

        if ($pager->numPages > 1) {
            if ($pager->numPages > 10) {
                if ($page <= 10)
                    $startPage = 1;
                else if ($page <= 20)
                    $startPage = 11;
                else if ($page <= 30)
                    $startPage = 21;
                else if ($page <= 40)
                    $startPage = 31;
                else if ($page <= 50)
                    $startPage = 41;
                else if ($page <= 60)
                    $startPage = 51;
                else if ($page <= 70)
                    $startPage = 61;
                else if ($page <= 80)
                    $startPage = 71;
                else if ($page <= 90)
                    $startPage = 81;
                else if ($page <= 100)
                    $startPage = 91;
                else if ($page <= 110)
                    $startPage = 101;
                else if ($page <= 120)
                    $startPage = 111;
                else if ($page <= 130)
                    $startPage = 121;
                else
                    $startPage = $pager->numPages;
                $endPage = $startPage + 9;
            }
            else {
                $startPage = 1;
                $endPage = $pager->numPages;
            }

            $content .= '<ul class="oInlineList">';
            if ($page == -1)
                $page = 0;
            if ($page == 1 || $page == 0) // this is the first page - there is no previous page
                $content .= '';
            else if ($page > 1) {        // not the first page, link to the previous page{
                $content .= '<li><a href="' . SITE_URL . $module . '/page/' . $page . '" class="oBtnSecondary oPageBtn"><span>&laquo;</span></a></li>';

                $content .= '<li><a href="' . SITE_URL . $module . '/page/' . ($page - 1) . '" class="oBtnSecondary oPageBtn"><span>&lsaquo;</span></a></li>';
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $pager->page)
                    $content .= '<li><a href="' . SITE_URL . $module . '/page/' . $i . '" class="buttonPageActive">' . $i . '</a></li>';
                else
                    $content .= '<li><a class="buttonPage" href="' . SITE_URL . $module . '/page/' . $i . '">' . $i . '</a></li>';
            }

            if ($page == $pager->numPages) // this is the last page - there is no next page
                $content .= "";
            else {
                $content .= '<li><a href="' . SITE_URL . $module . '/' . 'page/' . ($page + 1) . '" class="oBtnSecondary oPageBtn"><span>&rsaquo;</span></a></li>';

                $content .= '<li><a href="' . SITE_URL . $module . '/' . 'page/' . $pager->numPages . '" class="oBtnSecondary oPageBtn" ><span>&raquo;</span></a></li>';
            }
            $content .= '</ul">';
        }
        return $content;
    }*/

    /*function getPagination($count) {
        $paginationCount = floor($count / LIMIT);
        $paginationModCount = $count % LIMIT;
        if (!empty($paginationModCount)) {
            $paginationCount++;
        }
        return $paginationCount;
    }*/

    function sliderPanel($module = '') {
        global $db;
        $content = NULL;
        //$right_qry=$db->select("tbl_adverts",array("code"),array("site_status"=>"c"))->results();
        $block_1 = $right_qry['1']['code'];
        $content .= '<div class="row">
                        <div class="col-md-4 col-sm-4 topadd_space">
                           <div id="slider" class="nivoSlider">';
        // $selquer = $db->pdoQuery("select fileName from tbl_banner_img where type = 1 and isActive = 'y' and site_status = 'c' order by seq")->results();
        foreach ($selquer as $fetchRes) {
            $image = $fetchRes["fileName"];
            $content .= '<img src="' . COMMON_SITE . 'upload/leftBanner1/th1_' . $image . '">';
        }
        $content.='</div>
                        </div>
                        <div class="col-md-8 col-sm-8 topadd_space">
                           <div id="slider20" class="nivoSlider">';

        $content.=$block_1 . '</div>
                        </div>
                    </div>
                    <div class="row marginbottom-20">
                        <div class="col-md-12">
                          <div class="toptextadd">' . text_add($module) . '</div>
                        </div>
                    </div>';
        return $content;
    }

    function text_add($module = '') {
        global $db;
        $content = NULL;
        if ($module != '') {
            $fields = array('content');
            $where = array('module' => $module, 'domType' => 'com');
            //$result = $db->select('tbl_page_content',$fields,$where)->result();
            $content = $result['content'];
        } else {
            $fields = array('code');
            $where = array('site_status' => 'c');
            //$result = $db->select('tbl_adverts',$fields,$where)->result();
            $content = $result['code'];
        }
        return $content;
    }

    function paging($id, $paginationCount) {
        $content = NULL;
        $last_cls = "";
        $first_cls = "";
        $first_link = '<a href="javascript:void(0)" onclick="changePagination(0,\'0_no\')">&laquo;</a>';
        if ($id == 0) {
            $first_cls = " class='disabled'";
            $first_link = '<a href="javascript:void(0)">&laquo;</a>';
        }
        $content .= '<p class="flash loader_center"></p>';
        $content .= '<div style="text-align:center"><ul class="pagination product_paging">';
        $content .= '<li' . $first_cls . '>' . $first_link . '</li>';
        for ($i = 0; $i < $paginationCount; $i++) {
            $cls = "";
            if ($id == $i) {
                $cls = " class='active'";
            }
            $content .='<li' . $cls . ' id = "' . $i . '_no">
                <a href="javascript:void(0)" onclick="changePagination(\'' . $i . '\',\'' . $i . '_no\')">' . ($i + 1) . '</a>
              </li>';
        }
        $last_link = '<a href="javascript:void(0)" onclick="changePagination(\'' . ($paginationCount - 1) . '\',\'' . ($paginationCount - 1) . '_no\')">&raquo;</a>';
        if ($id == ($paginationCount - 1)) {
            $last_cls = " class='disabled'";
            $last_link = '<a href="javascript:void(0)">&raquo;</a>';
        }
        $content .='<li' . $last_cls . '>' . $last_link . '</li>';
        $content .='</ul></div>';
        return $content;
    }

    function remoteFileExists($url) {
        $ret = false;
        $file_headers = @get_headers($url);
        if ($file_headers[0] == 'HTTP/1.1 200 OK') {
            $ret = true;
        }
        return $ret;
    }

    function show_rating($avg_rate) {
        $star_rate = '';
        for ($x = 1; $x <= $avg_rate; $x++) {
            $star_rate .= '<img src="' . SITE_IMG . "star-full.png" . '" />';
        }
        if (strpos($avg_rate, '.5')) {
            $star_rate .= '<img src="' . SITE_IMG . "star-half.png" . '" />';
            $x++;
        }
        while ($x <= 5) {
            $star_rate .= '<img src="' . SITE_IMG . "star-blank.png" . '" />';
            $x++;
        }
        return $star_rate;
    }

    function escape($val, $strip = true) {
        if ($strip)
            return trim(strip_tags($val));
        else
            return trim($val);
    }

    function prod_image_path($add_from, $image) {
        if ($add_from == 'com') {
            $item_image = SITE_UPD . 'products/' . $image;
            $file_exist = file_exists(DIR_UPD . 'products/' . $image);
        } elseif ($add_from == 'biz') {
            $item_image = BIZ_UPD_PATH . 'products/' . $image;
            $file_exist = remoteFileExists($item_image);
        } elseif ($add_from == 'net') {
            $item_image = NET_SITE . 'upload/products/' . $image;
            $file_exist = remoteFileExists($item_image);
        }
        if ($image != '' && $file_exist == true) {
            $resize_path = SITE_INC . "view.php?image=" . $item_image . "&amp;mode=resize&amp;size=164x109";
        } else {
            $resize_path = SITE_IMG . "no-image.png";
        }
        return $resize_path;
    }

    function Slug($string) {
        //http://stackoverflow.com/questions/2103797/url-friendly-username-in-php
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }

    function remote_file_download($dest, $url) {
        $uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);
        $filenameIn = $url;
        $out_file = $uniqer . '_' . basename($filenameIn);
        //$filenameOut = __DIR__ . '/images/' . basename($_POST['text']);
        $filenameOut = $dest . $out_file;
        $contentOrFalseOnFailure = file_get_contents($filenameIn);
        $byteCountOrFalseOnFailure = file_put_contents($filenameOut, $contentOrFalseOnFailure);
        return $out_file;
    }

    function filtering($value = '', $type = 'output', $valType = 'string', $funcArray = '') {
        global $abuse_array, $abuse_array_value;


        if ($valType != 'int' && $type == 'output') {
            $value = str_ireplace($abuse_array, $abuse_array_value, $value);
        }

        $content = $filterValues = '';
        if ($valType == 'int')
            $filterValues = (isset($value) ? (int) strip_tags(trim($value)) : 0);
        if ($valType == 'float')
            $filterValues = (isset($value) ? (float) strip_tags(trim($value)) : 0);
        else if ($valType == 'string')
            $filterValues = (isset($value) ? (string) strip_tags(trim($value)) : NULL);
        else if ($valType == 'text')
            $filterValues = (isset($value) ? (string) trim($value) : NULL);
        else
            $filterValues = (isset($value) ? trim($value) : NULL);

        if ($type == 'input') {
            $content = $filterValues;
        } else if ($type == 'output') {
            if ($valType == 'string')
                $filterValues = html_entity_decode($filterValues);

            $value = str_replace(array('\r', '\n', ''), array('', '', ''), $filterValues);
            $content = stripslashes($value);
        }
        else {
            $content = $filterValues;
        }

        if ($funcArray != '') {
            $funcArray = explode(',', $funcArray);
            foreach ($funcArray as $functions) {
                if ($functions != '' && $functions != ' ') {
                    if (function_exists($functions)) {
                        $content = $functions($content);
                    }
                }
            }
        }

        return $content;
    }

    function generatePassword($length = 8) {
        // start with a blank password
        $password = "";
        // define possible characters - any character in this string can be
        // picked for use in the password, so if you want to put vowels back in
        // or add special characters such as exclamation marks, this is where
        // you should do it
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);
        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        // set up a counter for how many characters are in the password so far
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

            // have we already used this character in $password?
            if (!strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }
        }
        return $password;
    }

    function date_difference($d1, $d2, $strict = false) {
        $d1 = (is_string($d1) ? strtotime($d1) : $d1);
        $d2 = (is_string($d2) ? strtotime($d2) : $d2);

        $diff_secs = abs($d1 - $d2);
        $base_year = $strict === true ? date("Y", $d2) : min(date("Y", $d1), date("Y", $d2));

        $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
        return array(
            "years" => date("Y", $diff) - $base_year,
            "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
            "months" => date("n", $diff) - 1,
            "days_total" => floor($diff_secs / (3600 * 24)),
            "days" => date("j", $diff) - 1,
            "hours_total" => floor($diff_secs / 3600),
            "hours" => date("G", $diff),
            "minutes_total" => floor($diff_secs / 60),
            "minutes" => (int) date("i", $diff),
            "seconds_total" => $diff_secs,
            "seconds" => (int) date("s", $diff)
        );
    }

    function percentage($val1, $val2, $precision) {
        //echo $val1 ." : ".$val2 ." : ".$precision;
        //echo "<br /> Div :".$val1 ." / ".$val2;
        $division = $val1 / $val2;
        //echo "<br /> Res before round:".$division ." * 100";
        $res = $division * 100;
        $res = (int) round($res, $precision);
        //echo "<br /> Res after round:".$res;
        //exit();
        return $res;
    }

    function closetags($html) {
        #put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);

        $openedtags = $result[1];   #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        # all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        # close tags
        for ($i = 0; $i < $len_opened; $i++) {

            if (!in_array($openedtags[$i], $closedtags)) {

                $html .= '</' . $openedtags[$i] . '>';
            } else {

                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        } return $html;
    }

    function convert_to_csv($input_array, $output_file_name, $delimiter) {
        /** open raw memory as file, no need for temp files */
        $temp_memory = fopen('php://memory', 'w');
        /** loop through array  */
        foreach ($input_array as $line) {
            /** default php csv handler * */
            fputcsv($temp_memory, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines * */
        fseek($temp_memory, 0);
        /** modify header to be downloadable csv file * */
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($temp_memory);
        exit;
    }

    function convert_to_excel($input_array, $output_file_name, $output_file_sheet_name = "Language Constants", $sticky = true) {

        /** Include PHPExcel */
        require_once DIR_INC . 'excel/PHPExcel.php';


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $column_count = count($input_array[0]);
        // Add some data
        foreach ($input_array as $key => $value) {
            foreach ($value as $k => $v) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($k, $key + 1, $v)
                        ->setCellValueByColumnAndRow($k, $key + 1, $v)
                        ->setCellValueByColumnAndRow($k, $key + 1, $v);
            }
        }


        $i = 0;
        foreach (range('A', 'Z') as $columnID) {
            if ($column_count > $i) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                $i++;
            }
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($output_file_sheet_name);

        if ($sticky == true) {
            error_reporting(0);
            $objPHPExcel->getActiveSheet()->freezePane('A2');
            $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->getFont()->setSize(16);
            //$objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->getFont()->setColorIndex("#f0ad4e");
            $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->getFont()->setBold(true);
        }

        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => '5CB85C')
        ));
        $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($styleArray);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $output_file_name . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    function excell_to_array($input_file) {
        require_once DIR_INC . 'excel/PHPExcel/IOFactory.php';
        $objPHPExcel = PHPExcel_IOFactory::load($input_file);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            for ($row = 1; $row <= $highestRow; ++$row) {
                $col_array = array();
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    if ($val != '') {
                        //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                        $col_array = array_merge($col_array, array($val));
                    }
                }
                $final_result[] = $col_array;
            }
        }

        return $final_result;
    }

    function upload_file($file_arr = array(), $data_array = array()) {
        global $db, $sessUserId;
        $folder = "";
        $all_types = $image_dimension = array();
        $folder = $img_type = $path_folder = '';
        $all_types = array("jpg", "jpeg", "png", "gif");

        if ($data_array['type'] == 'banner') {
            $folder = DIR_BANNER . $data_array['id'] . '/';
            $path_folder = SITE_BANNER . $data_array['id'] . '/';
        }

        if (!$file_arr['name'])
            return false;
        $file_title = $file_arr['name'];

        //Get file extension
        $file_name = strtolower(pathinfo($file_arr['name'], PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($file_arr['name'], PATHINFO_EXTENSION));

        //Not really uniqe - but for all practical reasons, it is
        $uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);
        $file_name = $file_name . '_' . $uniqer . '.' . $ext;

        //$all_types = explode(",",strtolower($types));
        if ($all_types) {
            if (in_array($ext, $all_types))
                ;
            else {
                $result = "'" . $file_arr['name'] . "' is not a valid file."; //Show error if any.
                return false;
            }
        }

        //Where the file must be uploaded to
        if ($folder) {
            if (!is_dir($folder))
                mkdir($folder, 0777);
        }

        $uploadfile = $folder . $file_name;
        $result = '';

        //Move the file from the stored location to the new location
        if (($data_array['upload_from'] != 'url' && !move_uploaded_file($file_arr['tmp_name'], $uploadfile)) || ($data_array['upload_from'] == 'url' && !copy($file_arr['tmp_name'], $uploadfile))) {
            $result = "Cannot upload the file '" . $file_arr['name'] . "'"; //Show error if any.
            if (!file_exists($folder)) {
                $result .= " : Folder don't exist.";
            } elseif (!is_writable($folder)) {
                $result .= " : Folder not writable.";
            } elseif (!is_writable($uploadfile)) {
                $result .= " : File not writable.";
            }
            $file_name = '';
            return false;
        } else {
            if (count($image_dimension) > 0) {
                for ($i = 0; $i < count($image_dimension); $i++) {
                    resize($uploadfile, $folder . 'th' . ($i + 1) . '_' . $file_name, $image_dimension[$i]['width'], $image_dimension[$i]['height']);
                }
            }
            if (!empty($data_array['old_file'])) {
                unlink($folder . $data_array['old_file']);
                for ($i = 1; $i <= count($image_dimension); $i++) {
                    unlink($folder . 'th' . $i . '_' . $data_array['old_file']);
                }
            }
        }
        $file = array("filepath" => $path_folder, "file_name" => $file_name);
        return $file;
    }

    function display_image($data_array = array()) {

        $folder = $no_image_file = "";
        $all_types = $image_dimension = array();
        $folder_url = SITE_URL . 'images/';
        if ($data_array['type'] == 'user') {
            $folder = DIR_UPD_USER;
            //$folder_url=SITE_UPD_USER;
            $folder_url .= $data_array['type'] . '/';
            $no_image_file = "user_avtar.jpg";
        } else if ($data_array['type'] == 'home_images') {
            $folder = DIR_UPD_HOMEIMG;
            //$folder_url=SITE_UPD_HOMEIMG;
            $folder_url .= $data_array['type'] . '/';
            $no_image_file = "no_images.png";
        } else if ($data_array['type'] == 'audio') {
            $folder = DIR_UPD_AUDIO;
            //$folder_url=SITE_UPD_AUDIO;
            $folder_url .= $data_array['type'] . '/';
        }

        $image_url = (isset($data_array['file_name']) && $data_array['file_name'] != '' && file_exists($folder . $data_array['prefix'] . $data_array['file_name'])) ? $data_array['prefix'] . $data_array['file_name'] : $data_array['file_name'];
        $image_url = (file_exists($folder . $image_url) && $data_array['file_name'] != '') ? $image_url : $data_array['prefix'] . $no_image_file;
        $image_url = (file_exists($folder . $image_url)) ? $image_url : $no_image_file;

        return $folder_url . $image_url;
    }

    function resize($source_image, $destination, $tn_w, $tn_h, $quality = 100, $wmsource = false) {
        $info = getimagesize($source_image);
        $imgtype = image_type_to_mime_type($info[2]);

        #assuming the mime type is correct
        switch ($imgtype) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($source_image);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($source_image);
                break;
            case 'image/png':
                $source = imagecreatefrompng($source_image);
                break;
            default:
                die('Invalid image type.');
        }

        #Figure out the dimensions of the image and the dimensions of the desired thumbnail
        $src_w = imagesx($source);
        $src_h = imagesy($source);


        #Do some math to figure out which way we'll need to crop the image
        #to get it proportional to the new size, then crop or adjust as needed

        $x_ratio = $tn_w / $src_w;
        $y_ratio = $tn_h / $src_h;

        if (($src_w <= $tn_w) && ($src_h <= $tn_h)) {
            $new_w = $src_w;
            $new_h = $src_h;
        } elseif (($x_ratio * $src_h) < $tn_h) {
            $new_h = ceil($x_ratio * $src_h);
            $new_w = $tn_w;
        } else {
            $new_w = ceil($y_ratio * $src_w);
            $new_h = $tn_h;
        }

        $newpic = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
        $final = imagecreatetruecolor($tn_w, $tn_h);
        $backgroundColor = imagecolorallocate($final, 255, 255, 255);
        imagefill($final, 0, 0, $backgroundColor);
        //imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($tn_w / 2)), ($y_mid - ($tn_h / 2)), $tn_w, $tn_h, $tn_w, $tn_h);
        imagecopy($final, $newpic, (($tn_w - $new_w) / 2), (($tn_h - $new_h) / 2), 0, 0, $new_w, $new_h);

        #if we need to add a watermark
        if ($wmsource) {
            #find out what type of image the watermark is
            $info = getimagesize($wmsource);
            $imgtype = image_type_to_mime_type($info[2]);

            #assuming the mime type is correct
            switch ($imgtype) {
                case 'image/jpeg':
                    $watermark = imagecreatefromjpeg($wmsource);
                    break;
                case 'image/gif':
                    $watermark = imagecreatefromgif($wmsource);
                    break;
                case 'image/png':
                    $watermark = imagecreatefrompng($wmsource);
                    break;
                default:
                    die('Invalid watermark type.');
            }

            #if we're adding a watermark, figure out the size of the watermark
            #and then place the watermark image on the bottom right of the image
            $wm_w = imagesx($watermark);
            $wm_h = imagesy($watermark);
            imagecopy($final, $watermark, $tn_w - $wm_w, $tn_h - $wm_h, 0, 0, $tn_w, $tn_h);
        }
        if (imagejpeg($final, $destination, $quality)) {
            return true;
        }
        return false;
    }

    function getFileDir($type) {
        if ($type == 'user') {
            $folder = DIR_UPD_USER;
            $folder_url = SITE_UPD_USER;
        } else if ($type == 'home_images') {
            $folder = DIR_UPD_HOMEIMG;
            $folder_url = SITE_UPD_HOMEIMG;
        } else if ($type == 'audio') {
            $folder = DIR_UPD_AUDIO;
            $folder_url = SITE_UPD_AUDIO;
        } else if ($type == 'material') {
            $folder = DIR_UPD_MATERIAL;
            $folder_url = SITE_UPD_MATERIAL;
        } else if ($type == 'certificate') {
            $folder = DIR_UPD_CERTIFICATE;
            $folder_url = SITE_UPD_CERTIFICATE;
        } else if ($type == 'service_need') {
            $folder = DIR_UPD_NEED;
            $folder_url = SITE_UPD_NEED;
        }
        $final_result['dir'] = $folder;
        $final_result['site'] = $folder_url;
        return $final_result;
    }

    function checkAvailability($data = array()) {
        global $db;
        $whereCond = '';
        $lesson_id = isset($data['lesson_id']) ? $data['lesson_id'] : 0;
        $date = isset($data['date']) ? $data['date'] : '';
        $from_time = isset($data['from_time']) ? $data['from_time'] : '';
        $to_time = isset($data['to_time']) ? $data['to_time'] : '';

        if ($from_time != '' && $to_time != '') {
            $whereCond .= " and (start_hours<='" . $from_time . "' and end_hours>='" . $to_time . "')";
        }


        $qryRes = $db->pdoQuery("SELECT * FROM tbl_lesson_availability as la LEFT JOIN tbl_lesson as l ON la.lesson_id=l.id  WHERE la.lesson_id='" . $lesson_id . "' and (la.start_date<='" . $date . "' and la.end_date>='" . $date . "' " . $whereCond . ")");
        //$fetchRes= $qryRes->result();
        $total_records = $qryRes->affectedRows();
        return $total_records;
    }

    function notification($notification = '', $notifier = 0, $user_id = 0) {
        global $db;
        $insArr = array(
            'notifier_id' => $notifier,
            'user_id' => $user_id,
            'notification' => $notification,
            'created_date' => date('Y-m-d H:i:s'),
        );
        $db->insert('tbl_notifications', $insArr);
    }

    function getUserDetails($user_id = 0) {
        global $db;
        $result = $db->select('tbl_users', array('fname', 'lname', 'email'), array('id' => $user_id), ' LIMIT 1')->result();
        return $result;
    }

    //Functions for Date & time diffrence
    /*function time_diff($post_date) {
        $totaltime = '';
        // $tdate = split(" ", $post_date);
        $tdate = explode(" ", $post_date);
        $current_time = date("H:i");
        $qdate = $tdate[0];
        $posted_time = $tdate[1];
        $dt = explode("-", $qdate);
        $dt1 = explode(":", $posted_time);
        $today = date("Y-m-d");
        //$cdt=date("d F Y H:i:s", mktime($dt1[0], $dt1[1], $dt1[2], $dt[1], $dt[2], $dt[0]));
        $cdt = date("d F Y", mktime($dt1[0], $dt1[1], $dt1[2], $dt[1], $dt[2], $dt[0]));
        $tdt = date("d F Y H:i:s");

        $daydiff = datediff('d', $cdt, $tdt, false);
        $monthdiff = datediff('m', $cdt, $tdt, false);

        if (date("Y") == $dt[0]) {
            if (date("m") == $dt[1]) {
                if ($today == $qdate) {
                    $diff = get_time_difference($posted_time, $current_time);

                    $t = $diff['hours'] . " - " . $diff['minutes'];

                    $totalhour = $diff['hours'];
                    $totalmin = $diff['minutes'];
                    if ($totalhour > 0) {
                        $totaltime = $totalhour . " Hour Ago";
                    } else {
                        if ($totalmin > 0) {
                            if ($totalmin == 1) {
                                $totaltime = $totalmin . " Minute Ago";
                            } else {
                                $totaltime = $totalmin . " Minutes Ago.";
                            }
                        } else {
                            $totaltime = 'Just second Ago';
                        }
                    }
                } else {
                    $totaltime_temp = date("d") - $dt[2];
                    if ($totaltime_temp == 1) {
                        $totaltime = date("d") - $dt[2] . " Day Ago";
                    } else {
                        $totaltime = date("d") - $dt[2] . " Days Ago";
                    }
                }
            } else {
                $totalmonth = datediff('m', $cdt, $tdt, false);
                if (date("d") == $dt[2]) {

                    $totaltime = $totalmonth . " Month Ago";
                } else if (date("d") > $dt[2]) {
                    $totalday = date("d") - $dt[2]; //datediff('d', $cdt, $tdt, false);
                    if ($totalday == 1) {
                        $totaltime = $totalmonth . " Month and " . $totalday . " day Ago";
                    } else {
                        $totaltime = $totalmonth . " Month and " . $totalday . "  day Ago";
                    }
                } else if (date("d") < $dt[2]) {
                    $end_date = date("d F Y", mktime($dt1[0], $dt1[1], $dt1[2], $dt[1] + ($totalmonth - 1), $dt[2], $dt[0]));
                    $totalday = datediff('d', $end_date, $tdt, false);
                    if (($totalmonth - 1) > 0)
                        $totaltime = ($totalmonth - 1) . " Month and ";
                    if ($totalday == 1) {
                        $totaltime.= $totalday . " Day Ago";
                    } else {
                        $totaltime.= $totalday . " Days Ago";
                    }
                }
            }
        } else {
            if (floor($monthdiff / 12) > 0) {
                if (floor($monthdiff / 12) == 1) {
                    $totaltime = floor($monthdiff / 12) . " Year";
                } else {
                    $totaltime = floor($monthdiff / 12) . " Years";
                }
            }
            if ($totaltime != "" and $monthdiff - (floor($monthdiff / 12) * 12) > 0) {
                $totaltime.='and';
            }
            if ($monthdiff - (floor($monthdiff / 12) * 12) > 0) {
                if ($daydiff > 30)
                    $totaltime.=($monthdiff - (floor($monthdiff / 12) * 12)) . " Month Ago";
                else
                if ($daydiff == 1) {
                    $totaltime.=$daydiff . " Day Ago";
                } else {
                    $totaltime.=$daydiff . " Days Ago";
                }
            }
        }
        return $totaltime;
    }*/
    function time_diff($date)
    {
            $time = strtotime($date);
            $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
            $lengths = array("60","60","24","7","4.35","12","10");
        
            //$now = time();
            $now = strtotime(date('Y-m-d H:i:s'));
            $difference = $now-$time;
            $tense = "ago";
        
            for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                $difference /= $lengths[$j];
            }
            $difference = round($difference);
            if($difference != 1) {$periods[$j].= "s";}
            return "$difference $periods[$j] ago";
    }

    function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
        /*
          $interval can be:
          yyyy - Number of full years
          q - Number of full quarters
          m - Number of full months
          y - Difference between day numbers
          (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
          d - Number of full days
          w - Number of full weekdays
          ww - Number of full weeks
          h - Number of full hours
          n - Number of full minutes
          s - Number of full seconds (default)
         */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; // Difference in seconds

        switch ($interval) {

            case 'yyyy': // Number of full years

                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters

                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months

                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers

                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days

                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays

                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks

                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours

                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes

                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)

                $datediff = $difference;
                break;
        }

        return $datediff;
    }

    function timeAgo($date) {
        $time = strtotime($date);
        $period = array('Second ago', 'Minute ago', 'Hour ago', 'Day ago', 'Week ago', 'Month ago', 'Year ago', 'Decade ago');
        $periods = array('Seconds ago', 'Minutes ago', 'Hours ago', 'Days ago', 'Weeks ago', 'Months ago', 'Years ago', 'Decades ago');
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = strtotime(date('Y-m-d H:i:s'));
        $difference = $now - $time;
        $tense = "ago";

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        return $difference . ' ' . ($difference != 1 ? $periods[$j] : $period[$j]);
    }

    function dispContent($val) {
        $content = strip_tags(trim(stripslashes(stripcslashes(html_entity_decode($val)))));
        return $content;
    }

    function remove_directory($dir) {
        if (is_dir($dir)) {
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }
    }

    function check_membership($user_id = 0, $feature_id) {
        global $db;
        $selDet = $db->pdoQuery("SELECT history.membership_id,history.created_date,plan.plan_period,user.membership_start,user.membership_end FROM tbl_user_payment_history history INNER JOIN tbl_membership plan ON (history.membership_id = plan.id) INNER JOIN tbl_users user ON (history.user_id = user.id) WHERE history.user_id = ? AND history.status = ?  ORDER BY history.id DESC LIMIT 1 ", array($user_id, 'c'));
        if ($selDet->affectedRows() > 0) {
            $memDetail = $selDet->result();
            $endDt = $memDetail['membership_end'];
            /* switch ($memDetail['plan_period']) {
              case 'monthly':
              $period = '+1 months';
              break;

              case 'quarterly':
              $period = '+3 months';
              break;

              case 'half_yearly':
              $period = '+6 months';
              break;
              case 'yearly':
              $period = '+1 year';
              break;

              default:
              $period = '+0 day';
              break;
              }
              $startDt = $memDetail['created_date'];
              $endDt = date('Y-m-d H:i:s', strtotime($startDt.$period)); */
            $today = date('Y-m-d');
            $diff = date_difference($endDt, $today);
            /* if($diff['days_total'] <= 0){
              $memDetail['membership_id'] = 2;
              } */
            if ($diff['days_total'] > 0) {

                $featArr = array(1, 2, 4, 5, 6, 7, 8);
                $featVal = getTableValue('tbl_membership_features', 'feature_value', array('membership_id' => $memDetail['membership_id'], 'feature_id' => $feature_id));
                if ($feature_id > 0 && in_array($feature_id, $featArr)) {
                    if ($featVal != 'y' && $featVal != '1page') {
                        //upgrade
                        $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => UPGRADE_MEMBERSHIP));
                        redirectPage(SITE_URL . 'membership_plan/');
                    }
                } elseif ($feature_id > 0 && $feature_id == 3) {
                    if ($featVal != 'y') {
                        return false;
                    } else
                        return true;
                }
                elseif ($feature_id > 0 && in_array($feature_id, array(9, 10, 11, 12))) {
                    switch ($feature_id) {
                        case 9:
                            $remained_emails = getTableValue('tbl_users', 'remained_email', array('id' => $user_id));
                            if ((int) $remained_emails <= 0) {
                                return false;
                            } else {
                                return true;
                            }
                            break;
                        case 10:
                            $remained_material = getTableValue('tbl_users', 'remained_material', array('id' => $user_id));
                            if ((int) $remained_material <= 0) {
                                $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => UPGRADE_MEMBERSHIP));
                                redirectPage(SITE_URL . 'membership_plan/');
                            }
                            break;
                        default:
                            $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => UPGRADE_MEMBERSHIP));
                            redirectPage(SITE_URL . 'membership_plan/');
                            break;
                    }
                } else {
                    $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => UPGRADE_MEMBERSHIP));
                    redirectPage(SITE_URL . 'membership_plan/');
                }
            } else {
                //expired
                $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => MEMBERSHIP_EXP));
                redirectPage(SITE_URL . 'membership_plan/');
            }
        } else {
            $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => UPGRADE_MEMBERSHIP));
            redirectPage(SITE_URL . 'membership_plan/');
        }
    }

    function getImage($imageName, $folder, $width, $height) {
        global $db;
        $zc=1; $ql=100;
        $src = 'no_image_thumb.png'; $defaultImage='no_image_thumb.png';
        $filepath = $folder.'/'.$imageName;
        if(is_file(DIR_UPD.$filepath)) {
            $src = SITE_URL."image-thumb/".$width."/".$height."/".$zc."/".$ql."/?src=".$filepath;
        } else {
            $filepath = $defaultImage;
            $src = SITE_URL."image-thumb/".$width."/".$height."/".$zc."/".$ql."/?src=".$filepath;
        }
        return $src;
    }

    function getPagerData($numHits, $limit, $page)
    {
        $numHits  = (int) $numHits;
        $limit    = max((int) $limit, 1);
        $page     = (int) $page;
        $numPages = ceil($numHits / $limit);
        
        $page = max($page, 1);
        $page = min($page, $numPages);
        
        $offset = ($page - 1) * $limit;
        
        $ret = new stdClass;
        
        $ret->offset   = $offset;
        $ret->limit    = $limit;
        $ret->numPages = $numPages;
        $ret->page     = $page;
        
        return $ret;
    }

    function pagination($pager, $page, $module,$totalRow) {
        $content = $jsFuncVariables = '';

        if($pager->numPages > 1 && $totalRow > 0)
        {
            if($pager->numPages > 10) {
                if($page <= 10) $startPage = 1;
                else if($page <= 20) $startPage = 11;
                else if($page <= 30) $startPage = 21;
                else if($page <= 40) $startPage = 31;
                else if($page <= 50) $startPage = 41;
                else if($page <= 60) $startPage = 51;
                else if($page <= 70) $startPage = 61;
                else if($page <= 80) $startPage = 71;
                else if($page <= 90) $startPage = 81;
                else if($page <= 100) $startPage = 91;
                else if($page <= 110) $startPage = 101;
                else if($page <= 120) $startPage = 111;
                else if($page <= 130) $startPage = 121;
                else $startPage = $pager->numPages;
                $endPage =  $startPage+9;
            }
            else {
                $startPage = 1;
                $endPage =  $pager->numPages;
            }


            $content .= '<div><ul class="pagination">';
                if($page == -1)
                $page = 0;
                $previousPage = $page-1;
                $nextPage = $page+1;

                if ($page == 1 || $page == 0) // this is the first page - there is no previous page
                    $content .= '';
                else if ($page > 1)  {        // not the first page, link to the previous page{
                    $content .= '<li><a href="javascript:void(0);" data-page="'.$startPage.'" class="oBtnSecondary oPageBtn buttonPage"><span>&laquo;</span></a></li>';
                    
                    $content .= '<li><a href="javascript:void(0);" data-page="'.$previousPage.'" class="oBtnSecondary oPageBtn buttonPage"><span>&lsaquo;</span></a></li>';
                }
                
                for ($i = $startPage; $i <= $endPage; $i++) {
                        if ($i == $pager->page)
                            $content .= '<li class="active"><a href="javascript:void(0);" class="buttonPageActive">'.$i.'</a></li>';
                        else
                            $content .= '<li><a class="buttonPage next" data-page="'.$i.'" href="javascript:void(0);">'.$i.'</a></li>';
                }
                
                if ($page == $pager->numPages) // this is the last page - there is no next page
                    $content .= "";
                else {
                    $content .= '<li><a href="javascript:void(0);" data-page="'.$nextPage.'" class="oBtnSecondary oPageBtn buttonPage"><span>&rsaquo;</span></a></li>';

                    $content .= '<li><a href="javascript:void(0);" data-page="'.$pager->numPages.'" class="oBtnSecondary oPageBtn buttonPage" ><span>&raquo;</span></a></li>';
                }
                $content .= '</ul"></div>';
        }
        return $content;
    }

    function convertCurrency($currencyId,$amount){
        
        $currencyValue = getTableValue('tbl_currency','currencyValue',array("id"=>$currencyId));
        $finalAmount = $amount*$currencyValue;

        return $finalAmount;
    }

    function getDeliveryDays($option,$userId,$pick_point){
        
        if($option == 'd'){
            
            $stateId = getTableValue('tbl_users','stateId',array('id'=>$userId));

        }else if($option == 'p'){

            $stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pick_point));

        }else
        {
            $stateId = getTableValue('tbl_users','stateId',array('id'=>$userId));
        }

        $days = getTableValue('tbl_shipping_amount','deliveryDays',array('stateId'=>$stateId));

        if($days != ''){
            $deliveryDays = $days;
        }else{
            $deliveryDays = DEFAULT_DELIVERY_DAYS;
        }

        return $deliveryDays;
    }

    function getDutiesAmount($price,$countryId){
        global $sessUserId,$db;
        if($countryId=='')
        {
            $countryId = getTableValue('tbl_users','countryId',array('id'=>$sessUserId));
        }

        $fetchRes = $db->select('tbl_duties_amount',array('amount','minimumAmount'),array('countryId'=>$countryId,'isActive'=>'y'))->result();

        if($fetchRes['amount'] > 0){
            $dutiesPer = $fetchRes['amount'];
        }else{
            $dutiesPer = DEFAULT_DUTIES;
        }

        $dutiesAmount = $price * $dutiesPer / 100;
        
        if($dutiesAmount > $fetchRes['minimumAmount']){
            $finalAmount = $dutiesAmount;
        }else{
            $finalAmount = $fetchRes['minimumAmount'];
        }

        return $finalAmount;
    }

    function getAdminCharge($price,$countryId){
        global $sessUserId,$db;
        if($countryId=='')
        {
            $countryId = getTableValue('tbl_users','countryId',array('id'=>$sessUserId));
        }
        
        $fetchRes = $db->select('tbl_admin_charge',array('amount','minimumAmount'),array('countryId'=>$countryId,'isActive'=>'y'))->result();

        if($fetchRes['amount'] > 0){
            $adminChargePer = $fetchRes['amount'];
        }else{
            $adminChargePer = DEFAULT_ADMIN_CHARGE;
        }

        $adminCharge = $price * $adminChargePer / 100;
        
        if($adminCharge > $fetchRes['minimumAmount']){
            $finalAmount = $adminCharge;
        }else{
            $finalAmount = $fetchRes['minimumAmount'];
        }

        return $finalAmount;
    }

    function getd2dShippingAmount($price){
        global $sessUserId,$db;

        $userSateId = getTableValue('tbl_users','stateId',array("id"=>$sessUserId));

        $sData = $db->pdoQuery("select amount,minimumAmount from tbl_shipping_amount where stateId = ".$userSateId." and isActive = 'y'")->result();

        $fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$sessUserId."")->results();

        if($sData['amount'] != ''){
            $shippingPer = $sData['amount'];
        }else{
            $shippingPer = DEFAULT_SHIPPING;
        }

        $shippingAmount = $price * $shippingPer / 100;

        if($shippingAmount > $sData['minimumAmount']){
            $finalAmount = $shippingAmount;
        }else{
            $finalAmount = $sData['minimumAmount'];
        }

        return $finalAmount;

    }

    function getPickShippingAmount($price,$stateId){
        global $sessUserId,$db;

        $finalAmount = 0;
        if($stateId > 0){

            $sData = $db->pdoQuery("select amount,minimumAmount from tbl_shipping_amount where stateId = ".$stateId." and isActive = 'y'")->result();

            if($sData['amount'] != ''){
                $shippingPer = $sData['amount'];
            }else{
                $shippingPer = DEFAULT_SHIPPING;
            }

            $shippingAmount = $price * $shippingPer / 100;

            if($shippingAmount > $sData['minimumAmount']){
                $finalAmount = $shippingAmount;
            }else{
                $finalAmount = $sData['minimumAmount'];
            }

        }

        return $finalAmount;

    }

    function checkAddress($sessUserId){
        $address = getTableValue('tbl_users','address',array('id'=>$sessUserId));

        if($address == ''){
            
            $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Please update your address to continue shopping."));
            redirectPage(SITE_URL.'editprofile/'.$sessUserId);
        }
        
    }

    function getDeliveryAddress($deliveryOption,$pick_point){
        global $sessUserId,$db;
        $address = '';
        $number = getTableValue('tbl_users','mobileNumber',array('id'=>$sessUserId));
        $code = getTableValue('tbl_users','code',array('id'=>$sessUserId));

        if($deliveryOption == 'p'){
            
            $fetchRes = $db->pdoQuery('Select p.pointName,p.pointAddress,co.countryName,s.stateName from tbl_pick_points as p 
                LEFT JOIN tbl_state as s ON(p.stateId = s.id)
                LEFT JOIN tbl_country as co ON(p.countryId = co.id)
                WHERE p.id = '.$pick_point.'')->result();
            
            $address = $fetchRes['pointAddress'].','.$fetchRes['pointName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].'</br>'.$code.'&nbsp;'.$number;

        }else if($deliveryOption == 'd'){

            $fetchRes = $db->pdoQuery('Select u.address,ct.cityName,co.countryName,s.stateName from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$sessUserId.'')->result();
            
            $address = $fetchRes['address'].','.$fetchRes['cityName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].'</br>'.$code.'&nbsp;'.$number;

        }

        return $address;
    }
?>