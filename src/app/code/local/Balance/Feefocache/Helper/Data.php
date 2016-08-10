<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Balance
 * @package    Feefocache
 * @copyright  Copyright (c) 2011 Balance
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


class Balance_Feefocache_Helper_Data extends Flint_FeeFo_Helper_Data
{
    public function getReviewProducts(){
        $url = $this->getXMLLink($product = false);
        return $url;
    }

    public function getFeedback($product) { // Get all reviews
        $helper = Mage::helper('flint_feefo/Data');
        $dom = $helper->getReviews($product);
        if($dom){
            $feedbacks = $dom->getElementsByTagName('FEEDBACK');
            return $feedbacks;
        }

        return;
    }

    public function getRatingsAverage($type, $product) { // Get the overall information of product or service reviews
        $feedbacks = $this->getFeedback($product);
        $stars = 0;
        $count = 0;
        foreach($feedbacks as $feedback) {
            if($type === "service" && !$feedback->getElementsByTagName("SERVICERATING")->length) {
                $type = "product";
            }
            if($type === "service") {
                $rating = $feedback->getElementsByTagName("SERVICERATING")->item(0)->nodeValue;
            } else {
                $rating = $feedback->getElementsByTagName("PRODUCTRATING")->item(0)->nodeValue;
            }
            switch($rating) {
                case "--":
                    $stars = $stars + 2;
                    break;
                case "-":
                    $stars = $stars + 3;
                    break;
                case "+":
                    $stars = $stars + 4;
                    break;
                case "++":
                    $stars = $stars + 5;
                    break;
            }
            $count++;
        }
        if($count > 0) {
            $average = round(($stars/$count)*2)/2;
            $backgroundPos = $this->getRatingsBackground($average*20);
            return array("average" => $average, "position" => $backgroundPos, "count" => $count);
        }
    }

    public function getRatingsBackground($average) { // Get the background position of the review stars sprite based on the product average
        $average = round($average/10)*10; // Deal with current average data not being in multiples of 10
        switch($average) {
            case 10:
                $backgroundPos = "left -7px";
                break;
            case 20:
                $backgroundPos = "left -26px";
                break;
            case 30:
                $backgroundPos = "left -45px";
                break;
            case 40:
                $backgroundPos = "left -64px";
                break;
            case 50:
                $backgroundPos = "left -83px";
                break;
            case 60:
                $backgroundPos = "left -102px";
                break;
            case 70:
                $backgroundPos = "left -121px";
                break;
            case 80:
                $backgroundPos = "left -140px";
                break;
            case 90:
                $backgroundPos = "left -159px";
                break;
            default:
                $backgroundPos = "left -178px";
        }
        return $backgroundPos;
    }

    public function getReviewDetails($type, $product) { // Split reviews into service and product reviews
        if($type === "service") {
            $feedbacks = $this->getFeedback($product)->getElementsByTagName("SERVICERATING")->item(0)->nodeValue;
        } else {
            $feedbacks = $this->getFeedback($product)->getElementsByTagName("PRODUCTRATING")->item(0)->nodeValue;
        }
        return $feedbacks;
    }


    public function getSingleReview($feedback, $type) { // Get information of individual reviews
        $comment = $feedback->getElementsByTagName('CUSTOMERCOMMENT')->item(0)->nodeValue;
        $comment = str_replace("Service rating : ", "", $comment);
        if(strpos($comment, "Product : ") != false) {
            $comment = explode("Product : ", $comment);
            if($type == "product") {
                $comment = $comment[1];
            } else {
                $comment = $comment[0];
            }
        }
        $date = new DateTime($feedback->getElementsByTagName('DATE')->item(0)->nodeValue);
        $date = $date->format('jS M Y');
        if($type === "service") {
            $rating = $feedback->getElementsByTagName("SERVICERATING")->item(0)->nodeValue;
        } else {
            $rating = $feedback->getElementsByTagName("PRODUCTRATING")->item(0)->nodeValue;
        }
        $background = "";
        switch($rating) {
            case "--":
                $background = "left -64px";
                break;
            case "-":
                $background = "left -102px";
                break;
            case "+":
                $background = "left -140px";
                break;
            case "++":
                $background = "left -178px";
        }
        return array("comment" => $comment, "date" => $date, "background" => $background);
    }
}