<?php
	error_reporting(0);
function bd_nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));
        
        // is this a number?
        if(!is_numeric($n)) return false;
        
        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' trillion';
        else if($n>1000000000) return round(($n/1000000000),1).' billion';
        else if($n>1000000) return round(($n/1000000),1).' million';
        else if($n>1000) return round(($n/1000),1).' thousand';
        
        return number_format($n);
    }
	$max_months = 6;
	$months = 0;
	$MISC_FEES = 40 + // cloud server
				 1  + // SSL certificate
				 1;   // RocketLawyer
	$CLICK_THRU_RATE = 0.03;
	$COST_PER_CLICK  = 0.13;
	$pageviews_user_per_month = 200;
	$membership_fee = 5.00;
	$marketplace_fee = 0.05;
	$payment_gateway_fee = 0.029;
	$test_cost = 2.00;
	$fee_profit = $marketplace_fee - $payment_gateway_fee;

	
	
	/* Expenses */
	$mediation_employees = 0; $mediation_cost = 20.00; $mediation_hours = 0;
	$increase_mediation  = 12; $mediation_hours_per_week = 40;

	$content_writers     = 0; $content_cost   = 20.00; $content_hours   = 0;
	
	$marketing_experts   = 0; $marketing_cost = 20.00; $marketing_hours = 0;
	$increase_marketing  = 30; $marketing_hours_per_week = 40;

	$developers          = 0; $developer_cost = 30.00; $developer_hours = 0;
	
	$avg_contract_price   = 300;

	$employee_month_pay_total = 0; // 1st month no employees
	
	echo "Average Contract Price:$" . $avg_contract_price;
	echo "<br />";
	echo "Membership fee %: " . $membership_fee;
	echo "<br />";
	echo "Marketplace Fee %: " . $marketplace_fee;
	echo "<br />";
	echo "Payment Gateway fee %: " . $payment_gateway_fee;
	echo "<br />";
	echo "Fee Profit %: " . $fee_profit;
	echo "<br />";
	echo "Test Profit:$" . $test_cost;
	echo "<br />";
	echo "Marketing Hours/Week: " . $marketing_hours_per_week;
	echo "<br />";
	echo "Content Hours/Week: " . 20;
	echo "<br />";
	echo "Mediation Hours/Week: " . $mediation_hours_per_week;
	echo "<br />";
	echo "Developers Hours/Week: " . 20;
	echo "<br />";
	echo "Assuming the marketing experts can get 2 clients on the website per hour";
	
	/* Income */
	$new_contracts        = 660; $renew_contracts = 0; $referral_contracts = $new_contracts / 5;
	
	$total_contracts      = $new_contracts + $referral_contracts;
    // 1 in 10 new workers will purchase a test, 3 workers for every contract that exists.
	$worker_test_count  = (($total_contracts * 3) / 10); 
	$worker_test_amount = $worker_test_count * $test_cost;

	$worker_membership_count   = ($total_contracts * 3);
	$worker_membership_amount  = ($worker_membership_count * $membership_fee);

	$ad_impressions  = ($worker_membership_count + ($total_contracts / 2)) * $pageviews_user_per_month; // Average employer will post 2 jobs
	$ad_clicks = $ad_impressions * $CLICK_THRU_RATE;
	$ad_profit = $ad_clicks * $COST_PER_CLICK;

	$total_paid_out_month = $total_contracts * $avg_contract_price;

	$contract_profit = ($total_paid_out_month * $fee_profit);
	$revenue              = $contract_profit + $worker_membership_amount + $ad_profit + $worker_test_amount;
	$profit               = $revenue-$MISC_FEES;

	$total_profit 		  = $profit;
	echo "<table border='1' style='font-size:12px;'>
	<tr>
		<td>Month</td>
		<td>Total Paid</td>
		<td>Employee Cost</td>
		<td>Content Writers</td>
		<td>Content Hours</td>
		<td>Mediation Experts</td>
		<td>Mediation Hours</td>
		<td>Marketing Experts</td>
		<td>Marketing Hours</td>
		<td>Developer Hours</td>
		<td>Developers</td>
		<td>New Contracts</td>
		<td>Referral Contracts</td>
		<td>Renew Contracts</td>
		<td>Total Contracts</td>
		<td>Contract Profit</td>
		<td>Worker Tests</td>
		<td>Test Profit</td>
		<td>Worker Membership Count</td>
		<td>Worker Membership</td>
		<td>Ad Impressions</td>
		<td>Ad Clicks</td>
		<td>Ad Profit</td>
		<td>Revenue</td>
		<td>Profit</td>
		<td>Total Profit</td>
	</tr>";

	while ($months < $max_months) {

		if ( $months != 0) {

			/* New Hires */
			$marketing_experts   += $increase_marketing;
			$marketing_hours     += ( $increase_marketing * $marketing_hours_per_week * 4 ); // hours per month
			
			
			$mediation_employees += $increase_mediation;
			$mediation_hours     += ( $increase_mediation * $mediation_hours_per_week * 4 );
			if ($content_writers < 12) {
				$content_hours   += 60; // hours per month
				$content_writers += 2;
			}

			if ($developers < 5) {
				$developer_hours += 60; // hours per month
				$developers      += 2;
			}
			
			$renew_contracts    = ($total_contracts / 3); // Assuming 1/3 of old contracts will renew, retention
			$new_contracts      = ($marketing_hours*2); // two contracts per hour
			$referral_contracts = $new_contracts / 5;
			$total_contracts    = $new_contracts + $renew_contracts + $referral_contracts;
			

			$total_paid_out_month     = $total_contracts * $avg_contract_price;
			$contract_profit          = $total_paid_out_month * $fee_profit;
			
			$employee_month_pay_total = ($marketing_hours * $marketing_cost) + 
									    ($mediation_hours * $mediation_cost) + 
									    ($content_hours   * $content_cost)   + 
									    ($developer_hours * $developer_cost);

			$worker_test_count         = (($new_contracts * 2) / 10);
			$worker_test_amount		   = ($worker_test_count * $test_cost);


			$worker_membership_count   = (($new_contracts+$referral_contracts) * 3);
			$worker_membership_amount  = ($worker_membership_count * $membership_fee) - ($worker_membership_count / 3);
			
			$ad_impressions  = ($worker_membership_count + ($total_contracts / 2)) *  $pageviews_user_per_month; // Average employer will post 2 jobs
			$ad_clicks = $ad_impressions * $CLICK_THRU_RATE;
			$ad_profit = $ad_clicks * $COST_PER_CLICK;
		
			$revenue       = $contract_profit + $worker_membership_amount + $ad_profit + $worker_test_amount;
			$profit        = ($revenue - ($employee_month_pay_total + $MISC_FEES) );
			$total_profit  += $profit;
		}
		/* Display Future Business Stats and Benchmarks*/ 
echo "
	<tr>
		<td>".$months."</td>
		<td>".$total_paid_out_month."</td>
		<td>".$employee_month_pay_total."</td>
		<td>".$content_writers."</td>
		<td>".$content_hours."</td>
		<td>".$mediation_employees."</td>
		<td>".$mediation_hours."</td>
		<td>".$marketing_experts."</td>
		<td>".$marketing_hours."</td>
		<td>".$developer_hours."</td>
		<td>".$developers."</td>
		<td>".$new_contracts."</td>	
		<td>".$referral_contracts."</td>
		<td>".$renew_contracts."</td>
		<td>".$total_contracts."</td>
		<td><span style='font-weight:bold;'>$".bd_nice_number($contract_profit)."</span></td>
		<td>".$worker_test_count."</td>
		<td><span style='font-weight:bold;'>$".bd_nice_number($worker_test_amount)."</span></td>
		<td>".$worker_membership_count."</td>
		<td><span style='font-weight:bold;'>$".bd_nice_number($worker_membership_amount)."</span></td>
		<td>".$ad_impressions."</td>
		<td>".$ad_clicks."</td>
		<td><span style='font-weight:bold;'>$".bd_nice_number($ad_profit)."</span></td>
		<td>".$revenue."</td>
		<td>".$profit."</td>
		<td><b>$" .bd_nice_number($total_profit) . "</b></td>
	</tr>";
		$months++;
	}
	echo "</table>";