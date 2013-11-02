<?php
/**
 * @author Sion Duncan
 * @email sion@sionduncan.co.uk
 * @project BreachLAN - Event Management System
 * @filename pay.php
 * @filepurpose Handles payments (via paypal) for the events system
 * @version v1.0
 */

/* Load statics & establish SQL connection */
require('static/headers.php');
require("includes/paypalfunctions.php");
$link = new PDO("mysql:host=".$config['database']['host'].";dbname=".$config['database']['name'].";charset=utf8", $config['database']['user'], $config['database']['pass']);

/* Before we get started, include the Paypal JS */
$content = "<script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>";

switch($_GET["from"]){
    case "paypal":
        /* Paypal's logic here */
        switch($_GET["act"]){
            case "confirm":
                $pid = $_GET["atendee"];
                $res = GetExpressCheckoutDetails( $_REQUEST['token'] );
                $finalPaymentAmount =  $res["PAYMENTREQUEST_0_AMT"];
                //Format the  parameters that were stored or received from GetExperessCheckout call.
                $token = $_REQUEST['token'];
                $payerID = $_REQUEST['PayerID'];
                $paymentType = 'Sale';
                $currencyCodeType = $res['CURRENCYCODE'];
                $items = array();
                $i = 0;
                // adding item details those set in setExpressCheckout
                while(isset($res["L_PAYMENTREQUEST_0_NAME$i"])){
                    $items[] = array('name' => $res["L_PAYMENTREQUEST_0_NAME$i"], 'amt' => $res["L_PAYMENTREQUEST_0_AMT$i"], 'qty' => $res["L_PAYMENTREQUEST_0_QTY$i"]);
                    $i++;
                }
                
                $resArray = ConfirmPayment ( $token, $paymentType, $currencyCodeType, $payerID, $finalPaymentAmount, $items );
                $ack = strtoupper($resArray["ACK"]);
                if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ){
                    $transactionId		= $resArray["PAYMENTINFO_0_TRANSACTIONID"]; // Unique transaction ID of the payment.
                    $transactionType 	= $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"]; // The type of transaction Possible values: l  cart l  express-checkout
                    $paymentType		= $resArray["PAYMENTINFO_0_PAYMENTTYPE"];  // Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
                    $orderTime 			= $resArray["PAYMENTINFO_0_ORDERTIME"];  // Time/date stamp of payment
                    $amt				= $resArray["PAYMENTINFO_0_AMT"];  // The final amount charged, including any  taxes from your Merchant Profile.
                    $currencyCode		= $resArray["PAYMENTINFO_0_CURRENCYCODE"];  // A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
                    $feeAmt				= $resArray["PAYMENTINFO_0_FEEAMT"];  // PayPal fee amount charged for the transaction
                    $taxAmt				= $resArray["PAYMENTINFO_0_TAXAMT"];  // Tax charged on the transaction.
                
                    $paymentStatus = $resArray["PAYMENTINFO_0_PAYMENTSTATUS"];
                    $pendingReason = $resArray["PAYMENTINFO_0_PENDINGREASON"];
                    $reasonCode	= $resArray["PAYMENTINFO_0_REASONCODE"];
                    
                    /* Log the above information */
                    $log = $link->prepare("INSERT INTO :prefixpaymentlog (`pct_instance_ref`, `transactionid`, `paymenttype`, `ordertimestamp`, `totalamount`, `currency`, `paymentstaus`, `pendingreason`, `pendingcode`) VALUES (:transactionid, :paymenttype, :ordertimestamp, :totalamount, :currency, :paymentstaus, :pendingreason, :pendingcode)");
                    $event->bindParam(":pct_instance_ref", $pid);
                    $event->bindParam(":transactionid", $transactionId);
                    $event->bindParam(":paymenttype", $paymentType);
                    $event->bindParam(":ordertimestamp", $orderTime);
                    $event->bindParam(":totalamount", $amt);
                    $event->bindParam(":currency", $currencyCode);
                    $event->bindParam(":paymentstaus", $paymentStatus);
                    $event->bindParam(":pendingreason", $pendingReason);
                    $event->bindParam(":pendingcode", $reasonCode);
                    $event->execute();
                    
                    /* Update event status to allow access to the seat picker */
                    $event = $link->prepare("UPDATE :prefixparticipantstatus SET pct_paid = 1 WHERE pct_instance_ref = :pid");
                    $event->bindParam(":prefix", $config['database']['prefix']);
                    $event->bindParam(":pid", $pid);
                    $event->execute();
                    
                    $content .= 'Your payment has been sucessfully made. You can now select your <a href="selectseat.php">seat</a>.';
                    $paypalcloseflow = true;
                } else {
                    //Display a user friendly Error on the page using any of the following error information returned by PayPal
                    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
                    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
                    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
                    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
                
                    $content .= 'Your payment has failed. Please see the below message for information. If you believe this is an error, please contact Skull or Castiana on the <a href="http://www.breachlan.co.uk/forums/">forums</a> with the below information.<br /><br />';
                    $content .= "Detailed Error Message: ".$ErrorLongMsg."<br />";
                    $content .= "Short Error Message: ".$ErrorShortMsg."<br />";
                    $content .= "Error Code: ".$ErrorCode."<br />";
                    $paypalcloseflow = true;
                }
            break;
            /* Punter pushed the cancel button the paypal site */
            case "cancel":
                $content .= 'The payment has been cancelled. If you wish to attend the event you will need to pay for your ticket.';
                $paypalcloseflow = true;
            break;
        }
    break;
    case "site":
    default:
        /* Site/POST/default logic here */
        switch($_GET["act"]){
            case "checkout":
                $_POST["pct_instance_ref"] == "" ? die("Critical Failure. No atendee id.") : $pct_instance_ref = $_POST["pct_instance_ref"];
                $_POST["evt_id"] == "" ? die("Critical Failure. No event id.") : $evt_id = $_POST["evt_id"];
                $_POST["evt_name"] == "" ? die("Critical Failure. No event name.") : $evt_name = $_POST["evt_name"];
                
                $events = $link->prepare("SELECT * FROM :prefixevents WHERE id != :id ORDER BY DESC");
                $events->bindParam(":prefix", $config['database']['prefix']);
                $events->bindParam(":id", $evt_id);
                $events->execute();
                $data = $events->fetch();
                $discountend = explode($data["evt_end_date"]);
                $discountend = mktime(0,0,0, $discountend[1], $discountend[2], $discountend[0]);
                $now = time();
                $now > $discountend ? $cost = "31.50" : $cost = "26.50";
                
                $currencyCodeType = "GBP";
                $paymentType = "Sale";
                $returnURL = __BASEURL__."pay.php?from=paypal&act=confirm&atendee=".$pct_instance_ref;
                $cancelURL = __BASEURL__."pay.php?from=paypal&act=cancel";
                
                $items = array();
                $items[] = array('name' => $item, 'amt' => $cost, 'qty' => 1);
                
                $resArray = SetExpressCheckoutDG( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items );
                $ack = strtoupper($resArray["ACK"]);
                if($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING"){
                    $token = urldecode($resArray["TOKEN"]);
                    RedirectToPayPalDG( $token );
                } else {
                    // Display a user friendly Error on the page using any of the following error information returned by PayPal
                    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
                    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
                    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
                    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
                    
                    $content .= 'Paypal API call failed. Please report this error to Skull or Castiana on the <a href="http://www.breachlan.co.uk/forums/">BreachLAN Forums</a> <br /><br />';
                    $content .= "Detailed Error Message: " . $ErrorLongMsg."<br />";
                    $content .= "Short Error Message: " . $ErrorShortMsg."<br />";
                    $content .= "Error Code: " . $ErrorCode."<br />";
                    $content .= "Error Severity Code: " . $ErrorSeverityCode."<br />";
                }
            break;
            
            /* Nothing has been called, this is the default page */
            default:
                /* Getting Attendee list and checking to make sure only 1 event has been selected. Multiple selected events will not end well. Also check to see if they have paid already */
                $attendees = $link->prepare("SELCT * FROM :prefixparticipantstatus WHERE pct_instance_ref = :evtid");
                
                $attendees->bindParam(":prefix", $config['database']['prefix']);
                $attendees->bindParam(":evtid", $_GET['event']);
                
                $attendees->execute();
                
                if($attendees->rowCount() != 1){
                    $content .= 'The event you are looking for does not exist.<br /><br />Click <a href="index.php">here</a> to return.';
                } else {
                    $attendees = $attendees->fetchAll();
                    if($attendees["pct_paid"] == "1"){
                        $content .= '<div class="alert alert-error">You have already paid for this event. Please click <a href="index.php">here</a> to return, or click <a href="selectseat.php">here</a> to select your seat.</div>';
                    } else {
                        $event = $link->prepare("SELCT * FROM :prefixevents WHERE evt_id = :evtid");
                        
                        $event->bindParam(":prefix", $config['database']['prefix']);
                        $event->bindParam(":evtid", $_GET['event']);
                        
                        $event->execute();
                        
                        if($event->rowCount() != 1){
                            exit('Something has gone horribly horribly wrong and you appear to have murdered the internet... Our atleast this part of it. Please report this error to Skull or Castiana on the <a href="http://www.breachlan.co.uk/forums/">BreachLAN Forums</a> quoting "AccPayDupEvt-2".<br /><br />Click <a href="index.php">here</a> to return.');
                        } else {
                            $event = $event->fetchAll();
                            if(date("d-m-Y") > $event["evt_end_date"]){
                                $content .= 'Sorry, this event has already ended. Click <a href="index.php">here</a> to return.';
                            }
                        }
                        
                        /* Have they signed up yet? They need to do this before they can pay */
                        if($attendees['pct_signedup'] != 1) {
                        	$content .= '<div class="alert alert-error">You are not signed up for this event yet, please sign up prior to paying.</div>Click <a href="index.php">here</a> to return.';
                        } else {
                            $content .= "
                                <h3>Please confirm you would like to pay for the following event:</h3>
    
                                <p>Event Title:".$event['evt_name']."</p>
                                
                                <p>Event Dates: ".date('d', strtotime($event['evt_start_date']))."-".date('d F Y', strtotime($event['evt_end_date'])).".</p>
                                
                                <br /><br />
                                <form action=\"pay.php?act=checkout\" method=\"POST\">
                                    <input type=\"hidden\" name=\"pct_instance_ref\" value=\"".$attendees['pct_instance_ref']."\" />
                                    <input type=\"hidden\" name=\"evt_name\" value=\"".$event['evt_name']."\" />
                                    <input type=\"hidden\" name=\"evt_id\" value=\"".$event['evt_ID']."\" />
                                    <input type=\"hidden\" name=\"type\" value=\"single\" />
                                	<input type=\"image\" name=\"paypal_submit\" id=\"paypal_submit\" src=\"https://www.paypal.com/en_US/i/btn/btn_dg_pay_w_paypal.gif\" border=\"0\" align=\"top\" alt=\"Pay with PayPal\" />
                                </form>
                                <script>
                                	var dg = new PAYPAL.apps.DGFlow(
                                	{
                                		trigger: 'paypal_submit',
                                		expType: 'instant'
                                	});
                                </script>";
                        }
                    }
                }
            break;
        }
    break;
}

if($paypalcloseflow == true){
    $content .= '
        <script> 
            window.onload = function(){
                if(window.opener){
                    window.close();
                } else {
                    if(top.dg.isOpen() == true){
                        top.dg.closeFlow();
                        return true;
                    }
                }
            };
        </script>';
}

/**
 * Last thing to do before we end this
 */
echo $content;
require('static/footers.php'); ?>