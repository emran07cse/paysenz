<?php
/**
 * Created by PhpStorm.
 * User: Tareq Mahbub
 * Date: 27-Apr-17
 * Time: 8:45 AM
 */

/**
 * @return array|false|string
 */
function request_ip_address(){
    if (getenv('HTTP_CLIENT_IP'))
        $ipAddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipAddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipAddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipAddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipAddress = getenv('REMOTE_ADDR');
    else
        $ipAddress = 'UNKNOWN';
    return $ipAddress;
}

/**
 * @param \Illuminate\Support\MessageBag $errors
 * @return string
 */
function makeInvalidParameterDetails(\Illuminate\Support\MessageBag $errors) : string {
    $message = "";
    foreach ($errors->getMessages() as $key => $value){
        $message .= ' ' . $value[0];
    }

    return em('PSZ0001') . ' ' . $message;
}

/**
 * @param string $errorCode
 * @return \Illuminate\Config\Repository|mixed
 */
function em(string $errorCode){
    $errorMessage = config('errorcodes.' . $errorCode);
    if(empty($errorMessage)) return "Unknown error happened. Please contact the Admin.";
    else return $errorMessage;
}

function imageOrDummyUrl($imageFileName, $imageLocation = null){
    return empty(trim($imageFileName)) ? asset('photos/dummy-logo.jpg') : asset((empty($imageLocation) ? 'photos/logos/' : $imageLocation) . $imageFileName);
}

function imageOrDummyHtmlElement($imageFileName, $imageLocation = null){
    return '<img style="margin: 0px auto;" src="' . imageOrDummyUrl($imageFileName, $imageLocation) . '" class="img-responsive">';
}

function list_page_data_formatting($htmlTitle, $label, $content, $isFirstElement = false, $isTextSmall = false, $newLineForConent = false){
    if(empty($content)) return null;

    $html = "";
    if(!$isFirstElement)
        $html .= '<br />';
    if(!empty($label))
        $html .= '<span class="boxHeader" title="' . ($htmlTitle ?? '') . '">' . $label . ': </span>';
    if($newLineForConent)
        $html .= '<br />';

    $html .= $isTextSmall ? '<small>' . $content. '</small>' : $content;
    return $html;
}

/**
 * Format number to 2 decimal places and no thousand separator
 * @param $number
 * @return string
 */
function fn($number, $decimals = 2, $dec_point = '.', $thousands_sep = ''){
    return number_format((float)$number, $decimals, $dec_point, $thousands_sep);
}

/**
 * @param $string
 * @return mixed
 */
function strip_images($string){
    return preg_replace("/<img[^>]+\\>/i", "", $string);
}

/**
 * @param $examParticipantAnswers
 * @param $questionId
 * @param $optionId
 * @return bool
 */
function is_option_selected($examParticipantAnswers, $questionId, $optionId){
    if(!is_array($examParticipantAnswers)) return false;
    if(collect($examParticipantAnswers)->where('question_id', $questionId)
            ->where('answer_option_id', $optionId)
            ->count() > 0){
        return true;
    }else return false;
}

/**
 * @param $addedParticipations
 * @param $examId
 * @param $studentId
 * @return bool
 */
function is_participant_added($addedParticipations, $examId, $studentId){
    if(!is_array($addedParticipations)) return false;
    if(collect($addedParticipations)->where('exam_id', $examId)
            ->where('user_id', $studentId)
            ->count() > 0){
        return true;
    }else return false;
}

/**
 * @param $string
 * @param int $limit
 * @param string $end
 * @return string
 */
function truncate_text($string, $limit = 160, $end = ' . . .'){
    return str_limit($string, $limit, $end);
}

/**
 * @param $optionOrder
 * @return string
 */
function letter_equivalent_of_number($optionOrder){
    switch ($optionOrder){
        case 1:
            return "A";
        case 2:
            return "B";
        case 3:
            return "C";
        case 4:
            return "D";
        case 5:
            return "E";
        case 6:
            return "F";
        case 7:
            return "G";
        case 8:
            return "H";
        case 9:
            return "I";
        case 10:
            return "J";
        case 11:
            return "K";
        case 12:
            return "L";
        case 13:
            return "M";
        case 14:
            return "N";
        case 15:
            return "O";
        case 16:
            return "P";
        case 17:
            return "Q";
        case 18:
            return "R";
        case 19:
            return "S";
        case 20:
            return "T";
        case 21:
            return "U";
        case 22:
            return "V";
        case 23:
            return "W";
        case 24:
            return "X";
        case 25:
            return "Y";
        case 26:
            return "Z";
        default:
            return "?";
    }
}

/**
 * @param $number
 * @return string
 */
function number_postfix($number){
    if(ends_with($number, '0')) return 'th';
    if(ends_with($number, '1')) return 'st';
    if(ends_with($number, '2')) return 'nd';
    if(ends_with($number, '3')) return 'rd';
    if(ends_with($number, '4')) return 'th';
    if(ends_with($number, '5')) return 'th';
    if(ends_with($number, '6')) return 'th';
    if(ends_with($number, '7')) return 'th';
    if(ends_with($number, '8')) return 'th';
    if(ends_with($number, '9')) return 'th';
    return 'th';
}