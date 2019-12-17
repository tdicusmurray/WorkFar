<?php   
  require("PaymentGateway/init.php");

    class Marketplace extends Controller {
        public function __construct() {
            parent::__construct();
             \Stripe\Stripe::setApiKey("sk_live_kJSCjjzITnLWuyssIpm0qfau");
        }
        public function create_connect_account($code) {
            $token_request_body = array(
                'grant_type' => 'authorization_code',
                'client_id' => 'ca_BgYL4ZS7RT7YfS3XaYwNPksYolIghe0J',
                'code' => $code,
                'client_secret' => ''
            );
            $req = curl_init("https://connect.stripe.com/oauth/token");
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true );
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

            $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            curl_close($req);

            $oDB = $this->oDB->prepare("
UPDATE `user_payment_information` SET `bank_href` = ? WHERE user_id = ?
");
            $oDB->bindParam(1, $resp['stripe_user_id'],PDO::PARAM_STR);
            $oDB->bindParam(2, $_SESSION['id'],PDO::PARAM_STR);
            $oDB->execute();
        }
        public function create_customer($href) {
            $customer = \Stripe\Customer::create(array(
              "description" => "Customer for ".$_SESSION['email'],
              "source" => $href
            ));
            return $customer->id;
        }
        public function manual_membership($customer_href) {
            $cu = \Stripe\Customer::retrieve($customer_href);
            $subscription = $cu->subscriptions->create(array("plan" => "membership5"));
        }
        public function membership($customer_href) {
            $cu = \Stripe\Customer::retrieve($customer_href);
            $subscription = $cu->subscriptions->create(array("plan" => "plan_FqUzqMp0XgbPOR"));
            return $subscription->id;
        }
        public function worker($city,$state,$line1,$line2,$phone,$tax_id,$first_name,$last_name,$day,$month,$year,$bank_href) {
            $worker = \Stripe\Account::create(
                array(
                    "country" => "US",
                    "managed" => true,
                    "business_name" => $first_name . " " . $last_name,
                    "tos_acceptance" => array("date" => time(),"ip" => $_SERVER['REMOTE_ADDR']),
                    "legal_entity" => array("first_name" => $first_name, "last_name" => $last_name, "type" => "individual", "dob" => array("day" => $day, "month" => $month, "year" => $year))
                )
            );
            $account = \Stripe\Account::retrieve($worker->id);
            $account->legal_entity->first_name = $first_name;
            $account->legal_entity->last_name = $last_name;
            $account->external_accounts->create(array("external_account" => $bank_href));
            $account->save();
            return $worker->id;
        }
        public function order($worker_href) {
            return $order;
        }
        public function hold($customer_href,$amount) {
            try {
                $ch = \Stripe\Charge::create(array("amount" => $amount, "currency" => "usd","customer" => "$customer_href","capture" => false,"description" => "Worker"));
                return $ch->id;
            } catch (Exception $e) {
                var_dump($e);
            }
        }
        public function hold_debit($hold_href,$amount) {
            try {
                $ch = \Stripe\Charge::retrieve($hold_href);
                $hold_capture = $ch->id;
                $ch->capture(array("amount" => $amount));
                return $hold_capture;
            } catch (Exception $e) {
                var_dump($e);
            }
        }
        public function list_charges($customer_href) {
            $charges = \Stripe\Charge::all(array("customer" => $customer_href,"limit" => 10));
            return $charges->__toArray(true);
        }
        public function get_charge($charge_href) {
            $charge = \Stripe\Charge::retrieve($charge_href);
            $limited_charge_view['status'] = $charge['status'];
            $charge = $charge->__toArray(true);
            $limited_charge_view['paid'] = $charge['paid'];
            $limited_charge_view['amount'] = $charge['amount'];
            $limited_charge_view['created'] = $charge['created'];
            return $limited_charge_view;
        }
        public function create_transfer($worker_href) {
            ini_set('max_execution_time', 0);
            $amount = 0;
            $oDB = $this->oDB->prepare("
SELECT 
    `paid`,
    `charge_href`
FROM
    `work_payment`
JOIN
    `work`
ON 
    (`work_payment`.`work_id` = `work`.`id`)
JOIN
    `user_payment_information`
ON
    (`user_payment_information`.`user_id` = `work`.`worker_id`)
WHERE
    `user_payment_information`.`bank_href` = ?
AND
    `work_payment`.`paid` = 0
");
            $oDB->bindParam(1, $worker_href,PDO::PARAM_STR);
            $oDB->execute();
            while ($work_payment = $oDB->fetch(PDO::FETCH_ASSOC)) {
                $charge = $this->get_charge($work_payment['charge_href']);
                if ($charge['status'] == "succeeded" && $charge['paid'] == true) {
                    $oDB2 = $this->oDB->prepare("
UPDATE `work_payment` SET `paid` = 1 WHERE `charge_href` = ?
");
                    $oDB2->bindParam(1,$work_payment['charge_href'],PDO::PARAM_STR);
                    $oDB2->execute();
                    $amount += $charge['amount'];
                }
            }

            $amount = floor($amount - ($amount * 0.01));
            if ($amount == 0)
                exit();
            \Stripe\Transfer::create(array(
              "amount" => $amount,
              "currency" => "usd",
              "description" => "Transfer for ". $worker_href
            ),array("stripe_account" => $worker_href));
            $oDB = $this->oDB->prepare("
SELECT `user_id` FROM `user_payment_information` WHERE `bank_href` = ?
");
            $oDB->bindParam(1, $worker_href,PDO::PARAM_STR);
            $oDB->execute();
            $worker = $oDB->fetch(PDO::FETCH_ASSOC);

            $oDB = $this->oDB->prepare("
UPDATE `user` SET `balance` = 0 WHERE `id` = ?
");
            $oDB->bindParam(1,$worker['user_id'],PDO::PARAM_STR);
            $oDB->execute();
        }
        public function list_transfers ($worker_href) {
            $transfers = \Stripe\Transfer::all(array("limit" => 3,"destination" => $worker_href));
            $transfers_arrays = array();
            for ($i = 0; $i < count($transfers->data); $i++) {
                $transfers_arrays[]['amount'] = $transfers->data[$i]->amount;
                $transfers_arrays[count($transfers_arrays)-1]['created'] = date("Y-m-d H:i:s",$transfers->data[$i]->created);
            }
            return $transfers_arrays;
        }
        public function hold_void($hold_href) {
            $ch = \Stripe\Charge::retrieve($hold_href);
            $ch->metadata["explain"] = $explain;
            $ch->save();
            $re = $ch->refunds->create();
            $re = $ch->refunds->retrieve($re['id']);
            $re->save();
        }
        public function card_pay($customer_href,$amount,$email) {
            try {
                $charge = \Stripe\Charge::create(array(
                  "amount" => $amount * 100,
                  "currency" => "usd",
                  "customer" => $customer_href,
                  "description" => $email));
                $this->send($charge);
            } catch (Exception $e) {
                var_dump($e);
            }
        }
        public function refund($charge_href) {
            include_once 'users.php';
            $user = new User();
            $amount = $user->getPayRate($_POST['work_id']) / 6 * 100;
            try {
                $refund = \Stripe\Refund::create(array(
                  "charge" => $charge_href,
                  "amount" => $amount
                ));
                $oDB = $this->oDB->prepare("
INSERT INTO 
    `work_payment_refund` (`work_payment_id`,`refund_href`,`amount`) 
VALUES 
    (?,?,?)
");
                $work_payment_id = $this->getWorkPaymentId($charge_href);
                $oDB->bindParam(1,$work_payment_id,PDO::PARAM_INT);
                $oDB->bindParam(2,$refund->__toArray()['id'],PDO::PARAM_STR);
                $oDB->bindParam(3,$amount,PDO::PARAM_INT);
                $oDB->execute();
            } catch (Exception $e) {
                var_dump($e);
            }
        }
        public function dispute($work_payment_id,$message) {
            try {
                $oDB = $this->oDB->prepare("
INSERT INTO
    `work_payment_dispute` (`work_payment_id`,`status`) 
VALUES
    (?,0);
");
                $oDB->bindParam(1,$work_payment_id,PDO::PARAM_INT);
                $oDB->execute();
                $oDB = $this->oDB->prepare("
INSERT INTO
    `work_payment_dispute_message` (`work_payment_dispute_id`,`message`,`user_id`) 
VALUES
    (?,?,?);
");
                $work_payment_dispute_id = $this->oDB->lastInsertId();
                $oDB->bindParam(1,$work_payment_dispute_id,PDO::PARAM_INT);
                $oDB->bindParam(2,$message,PDO::PARAM_STR);
                $oDB->bindParam(3,$_SESSION['id'],PDO::PARAM_INT);
                $oDB->execute();
            } catch (Exception $e) {
                var_dump($e);
            }

        }
        public function getWorkPaymentId($charge_href) {
            $oDB = $this->oDB->prepare("
SELECT 
    `work_payment`.`id`
FROM
    `work_payment`
WHERE 
    `work_payment`.`charge_href` = ?
");
            $oDB->bindParam(1,$charge_href,PDO::PARAM_STR);
            $oDB->execute();
            $work_payment = $oDB->fetch(PDO::FETCH_ASSOC);
            return $work_payment['id'];
        }
    }