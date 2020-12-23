<?php
class ControllerExtensionAccountPurpletreeMultivendorSubscriptioncron extends Controller {
		private $error = array();
		public function index() {
			$this->load->model('extension/purpletree_multivendor/subscriptioncron');
			//Multivendor status Enable
			if($this->config->get('module_purpletree_multivendor_status')==1){
				if($this->config->get('module_purpletree_multivendor_subscription_plans')==1){
					
					if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
						$this->reminderForMultiplePlan();
						$this->multipleEnable();
						} else {
						$this->reminder();
						$this->enable();
					}
					
				}
			}
		}
		//Start reminder 	 
		protected function reminder(){
			// First Reminder
			// $seller_data=array();
			date_default_timezone_set('Asia/Calcutta'); 
			$seller_data=$this->model_extension_purpletree_multivendor_subscriptioncron->cronReminder();
			
			if($this->config->get('module_purpletree_multivendor_reminder_one_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_one_days')){
					$first_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_one_days');
					if($first_reminder>=0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								
								$end_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$first_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									
									$reminder=$value['reminder'];
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateReminder1($value['seller_id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										$this->load->model('extension/purpletree_multivendor/vendor');
										// Seller Mail
										$email_code = 'first_reminder_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_FIRST_REMINDER_' =>$first_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Seller Mail
										
										// Admin Mail
										$email_code = 'first_reminder_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_FIRST_REMINDER_' =>$first_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Admin Mail
										
										// Auto Generate invoice
										$this->load->model('extension/purpletree_multivendor/subscriptionplan');
										$invoice_status=$this->model_extension_purpletree_multivendor_subscriptionplan->getInvoiceStatus($value['seller_id']);
										if(isset($invoice_status)){
											if($invoice_status==2){
												$seller_id=$value['seller_id'];
												$plan_id=$value['plan_id'];
												$this->renewPlan($plan_id,$seller_id);
											}
										} 
										// Auto Generate invoice
										
									}
								}
							}
						}
						
					}
				}
				
			}
			
			// Second Reminder
			if($this->config->get('module_purpletree_multivendor_reminder_two_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_two_days')){
					$second_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_two_days');
					if($second_reminder>=0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								
								$end_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$second_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									
									$reminder=$value['reminder1'];
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateReminder2($value['seller_id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										$this->load->model('extension/purpletree_multivendor/vendor');
										
										// Seller Mail
										$email_code = 'second_reminder_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_SECOND_REMINDER_' =>$second_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Seller Mail
										
										// Admin Mail
										$email_code = 'second_reminder_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_SECOND_REMINDER_' =>$second_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Admin Mail
									}
								}
							}
						}
					}
				}
				
			}
			
			// Third Reminder
			if($this->config->get('module_purpletree_multivendor_reminder_three_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_three_days')){
					$third_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_three_days');
					if($third_reminder>0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								$end_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$third_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									$reminder=$value['reminder2'];
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateReminder3($value['seller_id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										$this->load->model('extension/purpletree_multivendor/vendor');
										
										//Seller Mail
										$email_code = 'third_reminder_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_THIRD_REMINDER_' =>$third_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										//Seller Mail
										
										//Admin Mail
										$email_code = 'third_reminder_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_THIRD_REMINDER_' =>$third_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										//Admin Mail
									}
								}
							}
						}
					}
				}
			}
		}
		
		protected function enable(){
			
			$seller_data=array();
			$seller_data=$this->model_extension_purpletree_multivendor_subscriptioncron->planActive();
			if(isset($seller_data)){
				foreach($seller_data as $value){
					$x=0;
					$grace=0;
					$end_date='';					
					$grace=(int)$this->config->get('module_purpletree_multivendor_grace_period');
					if($grace>0)
					{
						$x=$value['validity']+$grace;					
						$end_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$x.' days'));	
						} else {
						$end_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
					}
					$start_date= date('Y-m-d H:i:s',strtotime($value['start_date']));
					$today_date=date('Y-m-d H:i:s');
					
					/* 	$t_date=date_create($today_date);
						$e_date=date_create($start_date);
						$diff1 =date_diff($t_date,$e_date);
					$diff_date1=$diff1->format("%a");	 */							
					
					$expire_date=($value['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
					$ddd = 0;
					//if($diff_date1>=0){
					
					if($value['status'] == '1'){								
						//if($diff_date3<=0){										
						if($end_date <= $today_date){	
							$this->model_extension_purpletree_multivendor_subscriptioncron->planExpired($value['seller_id'],$value['id']);
							$disableProduct=$this->model_extension_purpletree_multivendor_subscriptioncron->sellerCheckData($value['seller_id'],$value['id'],$value['plan_id']);
							$ddd = 1;
						}						
					}
					//if($diff_date2<=0 and $diff_date3 >=0){
					if($start_date<=$today_date and $today_date<=$expire_date){										
						$this->model_extension_purpletree_multivendor_subscriptioncron->planEnable($value['seller_id'],$value['id'] );
						
						$this->model_extension_purpletree_multivendor_subscriptioncron->planDisable($value['seller_id'],$value['id']);
						} else {
						if($ddd==1){
							$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
							$this->load->model('extension/purpletree_multivendor/vendor');
							
							// Seller mail
                            $email_code = 'subscription_plan_expire_mail_to_seller';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];			
							$email_subject = $subtemplatefromdb;
							$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_EXPIRE_DATE_' =>$expire_date
																	);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						    $reciver = $customer['email'];
							$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
							
							// Admin mail
                            $email_code = 'subscription_plan_expire_mail_to_admin';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];			
							$email_subject = $subtemplatefromdb;
							$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_EXPIRE_DATE_' =>$expire_date
																	);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						    $reciver = $this->config->get('config_email');
							$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
							
							if($disableProduct){
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableProduct($value['id']);
								
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableFeaturedProduct($value['id']);
								
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableCategoryFeaturedProduct($value['id']);
							}
							//$this->model_extension_purpletree_multivendor_subscriptioncron->productDisable($value['seller_id'],$value['id']);
						}
					}
					// }
					
				}	
			} 
			$seller_d=array();
			$seller_d=$this->model_extension_purpletree_multivendor_subscriptioncron->sellerPlanData();
			if(!empty($seller_d)){
				foreach($seller_d as $seller_k=>$seller_result){
					if($this->model_extension_purpletree_multivendor_subscriptioncron->activeSellerPlan($seller_result['seller_id'])){
						$this->model_extension_purpletree_multivendor_subscriptioncron->disableSellerSubscription($seller_result['seller_id']);					
					}
				}
			}
		}
		protected function renewPlan($plan_id,$seller_id) {
			$this->load->language('purpletree_multivendor/subscriptionplan');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('extension/purpletree_multivendor/subscriptionplan');
			$data=array();
			$startt_when ='1';
			$s_date = '1';
			$data['plan_id']=$plan_id;
			$data['seller_id']=$seller_id;
			$data['startt_when']=$startt_when;
			$current_plan=$this->model_extension_purpletree_multivendor_subscriptioncron->getPlan($seller_id);
			$data['start_date'] = ($current_plan['end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($current_plan['end_date'])):date('Y-m-d H:i:s', strtotime($current_plan['start_date']. ' + '.$current_plan['validity'].' days'));
			$data['current_date'] = date('Y-m-d H:i:s');
			$data['end_date']='';
			$old_invoice_id=$this->model_extension_purpletree_multivendor_subscriptioncron->getInvoiceId($seller_id);			
			
			$result=$this->model_extension_purpletree_multivendor_subscriptioncron->getSubscribePlan($plan_id);
			
			$currentplan=$this->model_extension_purpletree_multivendor_subscriptioncron->getCurrentPlan($seller_id);	
			
			if($this->config->get("module_purpletree_multivendor_tax_name")){
				$tax_name=$this->config->get("module_purpletree_multivendor_tax_name");	
				} else {
				$tax_name='';	
			}
			if($this->config->get("module_purpletree_multivendor_tax_value")){
				$tax=$this->config->get("module_purpletree_multivendor_tax_value");	
				} else {
				$tax=0;	
			}
			$current_invoice=$this->model_extension_purpletree_multivendor_subscriptioncron->getSellerCurrentPlan($seller_id);
			$curr_invoice=array();
			if(!empty($current_invoice)){
				foreach($current_invoice as $value){
					$curr_invoice[$value['code']]=$value['price'];
				}
			}
			if($this->config->get("module_purpletree_multivendor_joining_fees")){
				$joining_fee=$result['joining_fee'];
				} else {
				$joining_fee=0;	
			}
			if($this->config->get("module_purpletree_multivendor_subscription_price")){
				$subscription_price=$result['subscription_price'];	
				} else {
				$subscription_price=0;	
			}
			$data['totals']['plan']=array();
			$data['totals']['plan'][]=array(
			'sort_order'=>0,
			'code'=>'subscription_price',
			'title'=>'Subscription Price',
			'value'=>$subscription_price
			);
			$data['totals']['plan'][]=array(
			'sort_order'=>1,
			'code'=>'joining_fee',
			'title'=>'Joining Fee',
			'value'=>$joining_fee
			);
			$a_joiningfee = $joining_fee;
			if(isset($currentplan)){
				$a_joiningfee=$joining_fee-	$current_plan['joining_fee'];
				$data['totals']['plan'][]=array(
				'sort_order'=>2,
				'code'=>'adjustment_Joining_fee',
				'title'=>'Adjustment Joining fee',
				'value'=>$a_joiningfee
				);	
				$subscription_price = $subscription_price-$this->remindPrice($current_plan['start_date'],$current_plan['validity'],$current_plan['subscription_price'],$s_date);
				$data['totals']['plan'][]=array(
				'sort_order'=>3,
				'code'=>'adjustment_subscription_price',
				'title'=>'Adjustment Subscription Price',
				'value'=>$subscription_price
				);
				$previous_balance=0;
				if($subscription_price<0){
					$previous_balance=$subscription_price;
				}
			}
			$subscription_price=$a_joiningfee+$subscription_price ;
			$total_amount= $subscription_price;
			$cal_tax=($total_amount*$tax)/100;
			$data['totals']['plan'][]=array(
			'sort_order'=>4,
			'code'=>'tax',
			'title'=>$tax_name.' ('.$tax.'%)',
			'value'=>$cal_tax
			);
			$current_invo=0;
			if(isset($currentplan)){
				if(isset($curr_invoice['previous_balance'])){
					$current_invo=$curr_invoice['previous_balance'];	
				}				
			}
			$total=$total_amount+$cal_tax+$current_invo;
			$invoice_bal=0;
			if($total<0){
				$invoice_bal=$total;	
			} 
			$data['totals']['plan'][]=array(
			'sort_order'=>5,
			'code'=>'previous_balance',
			'title'=>'Previous Balance',
			'value'=>$invoice_bal
			);
			$total=$total_amount+$cal_tax+$current_invo;
			//if plan free or grand total less then zero
			$data['vendor_invoice_status']=1;
			if($total<=0){
				$data['vendor_invoice_status']=2;
			}
			//end
			$invoice_mail=array();
			foreach($data['totals']['plan'] as $resultPlan){
				if($resultPlan['code']!='previous_balance'){
					$invoice_mail['mail'][]=array(
					'title'=>$resultPlan['title'],
					'price'=>$resultPlan['value']
					);
					} else {
					$invoice_mail['mail'][]=array(
					'title'=>$resultPlan['title'],
					'price'=>$current_invo
					);
				}
			}
			$start=($result['start_date']!='0000-00-00 00:00:00')?date('d/m/Y H:i:s',strtotime($result['start_date'])):'';
			
			$end=($result['end_date']!='0000-00-00 00:00:00')?date('d/m/Y H:i:s',strtotime($result['end_date'])):date('d/m/Y H:i:s', strtotime($result['start_date']. ' + '.$result['validity'].' days'));
			$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($seller_id);			
			$seller_name =$customer['firstname'].' '.$customer['lastname'];
			$this->load->model('extension/purpletree_multivendor/vendor');
			$price_detail = '';
			foreach($invoice_mail['mail'] as $msg){
				$price_detail.=$msg['title'].'- '.$this->currency->format($msg['price'], $this->session->data['currency']).'<br>';	
			}
			$invoice_id=$this->model_extension_purpletree_multivendor_subscriptioncron->addSellerPlan($data);
			$sellerExist=$this->model_extension_purpletree_multivendor_subscriptioncron->SellerExist($seller_id);
			$this->load->language('purpletree_multivendor/subscriptionplan');
			$email_subject='Subscription Invoice generated';
			//if plan free or grand total less then zero
			if($total<=0){
				$this->model_extension_purpletree_multivendor_subscriptioncron->enableSellerSubscription($seller_id);
			}
			//end
			// Mail 		
			
			$email_code = 'renew_subscription_plan_mail_to_seller';
		    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
		    $subtemplatefromdb = $register_template['new_subject'];
		    $messtemplatefromdb = $register_template['new_message'];			
			$email_subject = $subtemplatefromdb;
			$replacevar = array('_SELLER_NAME_' =>$seller_name,
										'_SELLER_EMAIL_' =>$customer['email'],
										'_PLAN_NAME_' =>$result['plan_name'],
										'_NOOFSUBPRODUCT_' =>$result['no_of_product'],
										'_VALIDITY_' =>$result['validity'],
										'_STARTDATE_' =>$start,
										'_ENDDATE_' =>$end,
										'_PRICE_DETAILS_' =>$price_detail,
										'_GRAND_TOTAL_' =>$total
										);
			$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
								
			$reciver = $customer['email'];
			$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
			//end mail
			// Mail 
			$email_code = 'renew_subscription_plan_mail_to_admin';
		    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
		    $subtemplatefromdb = $register_template['new_subject'];
		    $messtemplatefromdb = $register_template['new_message'];			
			$email_subject = $subtemplatefromdb;
			$replacevar = array('_SELLER_NAME_' =>$seller_name,
										'_SELLER_EMAIL_' =>$customer['email'],
										'_PLAN_NAME_' =>$result['plan_name'],
										'_NOOFSUBPRODUCT_' =>$result['no_of_product'],
										'_VALIDITY_' =>$result['validity'],
										'_STARTDATE_' =>$start,
										'_ENDDATE_' =>$end,
										'_PRICE_DETAILS_' =>$price_detail,
										'_GRAND_TOTAL_' =>$total
										);
			$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
								
			$reciver = $this->config->get('config_email');
			$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
		}
		
		protected function reminderForMultiplePlan(){
			// First Reminder
			// $seller_data=array();
			date_default_timezone_set('Asia/Calcutta'); 
			$seller_data=$this->model_extension_purpletree_multivendor_subscriptioncron->cronReminderForMultiplePlan();
			
			if($this->config->get('module_purpletree_multivendor_reminder_one_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_one_days')){
					$first_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_one_days');
					if($first_reminder>=0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								
								$end_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].'days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$first_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									
									$reminder=$value['reminder'];
									
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateMultipleReminder1($value['id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										
										$this->load->model('extension/purpletree_multivendor/vendor');
										// Seller Mail
										$email_code = 'first_reminder_multiple_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_FIRST_REMINDER_' =>$first_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Seller Mail
										
										// Admin Mail
										$email_code = 'first_reminder_multiple_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_FIRST_REMINDER_' =>$first_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Admin Mail
										// Auto Generate invoice
										$this->load->model('extension/purpletree_multivendor/subscriptionplan');
										$invoice_status=$this->model_extension_purpletree_multivendor_subscriptionplan->getInvoiceStatus($value['seller_id']);
										if(isset($invoice_status)){
											if($invoice_status==2){
												$seller_id=$value['seller_id'];
												$plan_id=$value['plan_id'];
												$this->renewMultiplePlan($plan_id,$seller_id);
											}
										} 
										// Auto Generate invoice
									}
								}
							}
						}
						
					}
				}
				
			}
			
			// Second Reminder
			if($this->config->get('module_purpletree_multivendor_reminder_two_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_two_days')){
					$second_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_two_days');
					if($second_reminder>=0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								
								$end_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$second_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									
									$reminder=$value['reminder1'];
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateMultipleReminder2($value['id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										
										$this->load->model('extension/purpletree_multivendor/vendor');
										
										// Seller Mail
										$email_code = 'second_reminder_multiple_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_SECOND_REMINDER_' =>$second_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Seller Mail
										
										// Admin Mail
										$email_code = 'second_reminder_multiple_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_SECOND_REMINDER_' =>$second_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										// Admin Mail
									}
								}
							}
						}
					}
				}
				
			}
			
			// Third Reminder
			if($this->config->get('module_purpletree_multivendor_reminder_three_days')>0){
				if($this->config->get('module_purpletree_multivendor_reminder_three_days')){
					$third_reminder=(int)$this->config->get('module_purpletree_multivendor_reminder_three_days');
					if($third_reminder>0){
						if(isset($seller_data)){
							foreach($seller_data as $value){
								$end_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
								
								$remind_date=date('Y-m-d', strtotime('-'.$third_reminder.' days', strtotime($end_date)));
								
								$today_date=date('Y-m-d');
								
								$date1=date_create($remind_date);
								$date2=date_create($today_date);
								$diff=date_diff($date1,$date2);
								$date_diff=$diff->format("%a");
								
								if($date_diff==0){
									
									$reminder=$value['reminder2'];
									if($reminder < 1){
										$reminder++;
										$this->model_extension_purpletree_multivendor_subscriptioncron->updateMultipleReminder3($value['id'],$reminder);
										
										$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);
										
										$this->load->model('extension/purpletree_multivendor/vendor');
										
										//Seller Mail
										$email_code = 'third_reminder_multiple_subscription_plan_mail_to_seller';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_ENDDATE_' =>$end_date,
																	'_THIRD_REMINDER_' =>$third_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $customer['email'];
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										//Seller Mail
										
										//Admin Mail
										$email_code = 'third_reminder_multiple_subscription_plan_alert_mail_to_admin';
									    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
									    $subtemplatefromdb = $register_template['new_subject'];
									    $messtemplatefromdb = $register_template['new_message'];			
										$email_subject = $subtemplatefromdb;
										$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],
										                            '_PLAN_NAME_' =>$value['plan_name'],
																	'_ENDDATE_' =>$end_date,
																	'_THIRD_REMINDER_' =>$third_reminder
																	);
										$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
										$reciver = $this->config->get('config_email');
										$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
										//Admin Mail
									}
								}
							}
						}
					}
				}
				
			}
		}
		//End reminder 
		
		// product enable
		
		protected function multipleEnable(){	
			
			$seller_data=array();
			$seller_data=$this->model_extension_purpletree_multivendor_subscriptioncron->multiplePlanActive();
			
			if(isset($seller_data)){
				foreach($seller_data as $value){
					$grace=(int)$this->config->get('module_purpletree_multivendor_grace_period');
					if($grace>0)
					{
						$x=$value['validity']+$grace;
						$end_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$x.' days'));	
						} else {
						$end_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
					}
					$start_date= date('Y-m-d H:i:s',strtotime($value['start_date']));
					$today_date=date('Y-m-d H:i:s');
					
					/* $t_date=date_create($today_date);
						$e_date=date_create($start_date);
						$diff1 =date_diff($t_date,$e_date);
					$diff_date1=$diff1->format("%a"); */										
					
					$expire_date=($value['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d',strtotime($value['new_end_date'])):date('Y-m-d H:i:s', strtotime($value['start_date'].' + '.$value['validity'].' days'));
					
					$ddd = 0;
					//if($diff_date1>=0){
					if($value['new_status'] == '1'){
						//if($diff_date3<=0){
						if($end_date <= $today_date){
							$this->model_extension_purpletree_multivendor_subscriptioncron->planExpiredForMultiplePlan($value['seller_id'],$value['id']);
							
							$disableProduct=$this->model_extension_purpletree_multivendor_subscriptioncron->sellerCheckData($value['seller_id'],$value['id'],$value['plan_id']);
							
							$ddd = 1;
						}						
					}
					//if($diff_date2<=0 and $diff_date3 >=0){
					if($start_date<=$today_date and $today_date<=$expire_date){
						$this->model_extension_purpletree_multivendor_subscriptioncron->planEnableForMultiplePlan($value['seller_id'],$value['id']);
						} else {
						if($ddd==1){
							$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($value['seller_id']);							
							$this->load->model('extension/purpletree_multivendor/vendor');
							
							// Seller mail
                            $email_code = 'multiple_subscription_plan_expire_mail_to_seller';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];			
							$email_subject = $subtemplatefromdb;
							$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_EXPIRE_DATE_' =>$expire_date
																	);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						    $reciver = $customer['email'];
							$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
							
							// Admin mail
                            $email_code = 'multiple_subscription_plan_expire_mail_to_admin';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];			
							$email_subject = $subtemplatefromdb;
							$replacevar = array('_SELLER_NAME_' =>$value['seller_name'],																	
																	'_PLAN_NAME_' =>$value['plan_name'],		
																	'_EXPIRE_DATE_' =>$expire_date
																	);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						    $reciver = $this->config->get('config_email');
							$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);				
							if($disableProduct){
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableProduct($value['id']);
								
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableFeaturedProduct($value['id']);
								
								$this->model_extension_purpletree_multivendor_subscriptioncron->SellerDisableCategoryFeaturedProduct($value['id']);
							}
						}
					}
					// }
					
				}	
				
				//$this->model_extension_purpletree_multivendor_subscriptioncron->productDisableForMultiplePlan();
			} 
			$seller_d=array();
			$seller_d=$this->model_extension_purpletree_multivendor_subscriptioncron->sellerPlanData();
			if(!empty($seller_d)){
				foreach($seller_d as $seller_k=>$seller_result){
					if($this->model_extension_purpletree_multivendor_subscriptioncron->activeSellerPlan($seller_result['seller_id'])){
						$this->model_extension_purpletree_multivendor_subscriptioncron->disableSellerSubscription($seller_result['seller_id']);					
					}
				}
			}
		}
		
		protected function renewMultiplePlan($plan_id,$seller_id) {
			$this->load->language('purpletree_multivendor/subscriptionplan');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('extension/purpletree_multivendor/subscriptionplan');
			$data=array();
			$startt_when ='1';
			$s_date = '1';
			$data['plan_id']=$plan_id;
			$data['seller_id']=$seller_id;
			$data['startt_when']=$startt_when;
			$current_plan=$this->model_extension_purpletree_multivendor_subscriptioncron->getMultiplePlan($seller_id);
			$data['start_date'] = ($current_plan['new_end_date']!='0000-00-00 00:00:00')?date('Y-m-d H:i:s',strtotime($current_plan['new_end_date'])):date('Y-m-d H:i:s', strtotime($current_plan['start_date']. ' + '.$current_plan['validity'].' days'));
			$data['current_date'] = date('Y-m-d H:i:s');
			$data['end_date']='';
			$old_invoice_id=$this->model_extension_purpletree_multivendor_subscriptioncron->getInvoiceIdForMultiplePlan($seller_id);			
			
			$result=$this->model_extension_purpletree_multivendor_subscriptioncron->getSubscribeMultiplePlan($plan_id);
			
			$currentplan=$this->model_extension_purpletree_multivendor_subscriptioncron->getCurrentMultiplePlan($seller_id);	
			
			if($this->config->get("module_purpletree_multivendor_tax_name")){
				$tax_name=$this->config->get("module_purpletree_multivendor_tax_name");	
				} else {
				$tax_name='';	
			}
			if($this->config->get("module_purpletree_multivendor_tax_value")){
				$tax=$this->config->get("module_purpletree_multivendor_tax_value");	
				} else {
				$tax=0;	
			}
			$current_invoice=$this->model_extension_purpletree_multivendor_subscriptioncron->getSellerCurrentMultiplePlan($seller_id);
			$curr_invoice=array();
			if(!empty($current_invoice)){
				foreach($current_invoice as $value){
					$curr_invoice[$value['code']]=$value['price'];
				}
			}
			if($this->config->get("module_purpletree_multivendor_joining_fees")){
				$joining_fee=$result['joining_fee'];
				} else {
				$joining_fee=0;	
			}
			if($this->config->get("module_purpletree_multivendor_subscription_price")){
				$subscription_price=$result['subscription_price'];	
				} else {
				$subscription_price=0;	
			}
			$data['totals']['plan']=array();
			$data['totals']['plan'][]=array(
			'sort_order'=>0,
			'code'=>'subscription_price',
			'title'=>'Subscription Price',
			'value'=>$subscription_price
			);
			$data['totals']['plan'][]=array(
			'sort_order'=>1,
			'code'=>'joining_fee',
			'title'=>'Joining Fee',
			'value'=>$joining_fee
			);
			$a_joiningfee = $joining_fee;
			if(isset($currentplan)){
				$a_joiningfee=$joining_fee-	$current_plan['joining_fee'];
				$data['totals']['plan'][]=array(
				'sort_order'=>2,
				'code'=>'adjustment_Joining_fee',
				'title'=>'Adjustment Joining fee',
				'value'=>$a_joiningfee
				);	
				$subscription_price = $subscription_price-$this->remindPrice($current_plan['start_date'],$current_plan['validity'],$current_plan['subscription_price'],$s_date);
				$data['totals']['plan'][]=array(
				'sort_order'=>3,
				'code'=>'adjustment_subscription_price',
				'title'=>'Adjustment Subscription Price',
				'value'=>$subscription_price
				);
				$previous_balance=0;
				if($subscription_price<0){
					$previous_balance=$subscription_price;
				}
			}
			$subscription_price=$a_joiningfee+$subscription_price ;
			$total_amount= $subscription_price;
			$cal_tax=($total_amount*$tax)/100;
			$data['totals']['plan'][]=array(
			'sort_order'=>4,
			'code'=>'tax',
			'title'=>$tax_name.' ('.$tax.'%)',
			'value'=>$cal_tax
			);
			$current_invo=0;
			if(isset($currentplan)){
				if(isset($curr_invoice['previous_balance'])){
					$current_invo=$curr_invoice['previous_balance'];	
				}				
			}
			$total=$total_amount+$cal_tax+$current_invo;
			$invoice_bal=0;
			if($total<0){
				$invoice_bal=$total;	
			} 
			$data['totals']['plan'][]=array(
			'sort_order'=>5,
			'code'=>'previous_balance',
			'title'=>'Previous Balance',
			'value'=>$invoice_bal
			);
			$total=$total_amount+$cal_tax+$current_invo;
			//if plan free or grand total less then zero
			$data['vendor_invoice_status']=1;
			if($total<=0){
				$data['vendor_invoice_status']=2;
			}
			//end
			$invoice_mail=array();
			foreach($data['totals']['plan'] as $resultPlan){
				if($resultPlan['code']!='previous_balance'){
					$invoice_mail['mail'][]=array(
					'title'=>$resultPlan['title'],
					'price'=>$resultPlan['value']
					);
					} else {
					$invoice_mail['mail'][]=array(
					'title'=>$resultPlan['title'],
					'price'=>$current_invo
					);
				}
			}
			$start=($result['start_date']!='0000-00-00 00:00:00')?date('d/m/Y H:i:s',strtotime($result['start_date'])):'';
			
			$end=($result['new_end_date']!='0000-00-00 00:00:00')?date('d/m/Y H:i:s',strtotime($result['new_end_date'])):date('d/m/Y H:i:s', strtotime($result['start_date']. ' + '.$result['validity'].' days'));
			$customer = $this->model_extension_purpletree_multivendor_subscriptioncron->getCustomer($seller_id);
			$seller_name =$customer['firstname'].' '.$customer['lastname'];
			$price_detail = '';
			foreach($invoice_mail['mail'] as $msg){
				$price_detail.=$msg['title'].'- '.$this->currency->format($msg['price'], $this->session->data['currency']).'<br>';	
			}
			$invoice_id=$this->model_extension_purpletree_multivendor_subscriptioncron->addSellerMultiplePlan($data);
			$sellerExist=$this->model_extension_purpletree_multivendor_subscriptioncron->SellerExist($seller_id);
			$this->load->language('purpletree_multivendor/subscriptionplan');
			$email_subject='Subscription Invoice generated';
			//if plan free or grand total less then zero
			if($total<=0){
				$this->model_extension_purpletree_multivendor_subscriptioncron->enableSellerSubscription($seller_id);
			}
			//end
			// Mail 		
			
			$email_code = 'renew_multiple_subscription_plan_mail_to_seller';
		    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
		    $subtemplatefromdb = $register_template['new_subject'];
		    $messtemplatefromdb = $register_template['new_message'];			
			$email_subject = $subtemplatefromdb;
			$replacevar = array('_SELLER_NAME_' =>$seller_name,
										'_SELLER_EMAIL_' =>$customer['email'],
										'_PLAN_NAME_' =>$result['plan_name'],
										'_NOOFSUBPRODUCT_' =>$result['no_of_product'],
										'_VALIDITY_' =>$result['validity'],
										'_STARTDATE_' =>$start,
										'_ENDDATE_' =>$end,
										'_PRICE_DETAILS_' =>$price_detail,
										'_GRAND_TOTAL_' =>$total
										);
			$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
								
			$reciver = $customer['email'];
			$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
			//end mail
			// Admin Mail 
			$email_code = 'renew_multiple_subscription_plan_mail_to_admin';
		    $register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
		    $subtemplatefromdb = $register_template['new_subject'];
		    $messtemplatefromdb = $register_template['new_message'];			
			$email_subject = $subtemplatefromdb;
			$replacevar = array('_SELLER_NAME_' =>$seller_name,
										'_SELLER_EMAIL_' =>$customer['email'],
										'_PLAN_NAME_' =>$result['plan_name'],
										'_NOOFSUBPRODUCT_' =>$result['no_of_product'],
										'_VALIDITY_' =>$result['validity'],
										'_STARTDATE_' =>$start,
										'_ENDDATE_' =>$end,
										'_PRICE_DETAILS_' =>$price_detail,
										'_GRAND_TOTAL_' =>$total
										);
			$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
								
			$reciver = $this->config->get('config_email');
			$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
			// end mail 
		}
		
		public function remindPrice($start_date,$validity,$s_price,$s_date){
			$this->load->language('purpletree_multivendor/subscriptionplan');
			$this->document->setTitle($this->language->get('heading_title'));
			$price=0;
			if($s_date == '1') {
				return $price;
			}
			$date1=date_create(date('Y-m-d H:i:s'));
			$date2=date_create(date('Y-m-d H:i:s',strtotime($start_date)));
			$diff=date_diff($date2,$date1);
			$r_date=$validity-((int)$diff->format("%a"));
			if($r_date>=0){
				$price=($s_price*$r_date)/$validity;
			}
			return $price;
		}
}
?>