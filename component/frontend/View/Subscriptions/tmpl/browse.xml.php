<?php
/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Xml $this */
?>
<?xml version="1.0" encoding="UTF-8"?>
<subscriptions>
<?php
/** @var \Akeeba\Subscriptions\Site\Model\Subscriptions $item */
foreach($this->items as $item):
?>
	<subscription>
		<akeebasubs_subscription_id><?php echo $item->akeebasubs_subscription_id ?></akeebasubs_subscription_id>
		<user_id><?php echo $item->user_id ?></user_id>
		<akeebasubs_level_id><?php echo $item->akeebasubs_level_id ?></akeebasubs_level_id>
		<publish_up><?php echo $item->publish_up ?></publish_up>
		<publish_down><?php echo $item->publish_down ?></publish_down>
		<notes><![CDATA[ <?php echo str_replace(']]>', '] ] >', $item->notes) ?>]]></notes>
		<enabled><?php echo $item->enabled ?></enabled>
		<processor><?php echo $item->processor ?></processor>
		<processor_key><?php echo $item->processor_key ?></processor_key>
		<state><?php echo $item->state ?></state>
		<net_amount><?php echo $item->net_amount ?></net_amount>
		<tax_amount><?php echo $item->tax_amount ?></tax_amount>
		<gross_amount><?php echo $item->gross_amount ?></gross_amount>
		<recurring_amount><?php echo $item->recurring_amount ?></recurring_amount>
		<tax_percent><?php echo $item->tax_percent ?></tax_percent>
		<created_on><?php echo $item->created_on ?></created_on>
		<params><![CDATA[ <?php echo str_replace(']]>', '] ] >', json_encode($item->params)) ?>]]></params>
		<ip><?php echo $item->ip ?></ip>
		<ip_country><?php echo $item->ip_country ?></ip_country>
		<ua><![CDATA[ <?php echo str_replace(']]>', '] ] >', $item->ua) ?>]]></ua>
		<mobile><?php echo $item->mobile ?></mobile>
		<akeebasubs_coupon_id><?php echo $item->akeebasubs_coupon_id ?></akeebasubs_coupon_id>
		<akeebasubs_upgrade_id><?php echo $item->akeebasubs_upgrade_id ?></akeebasubs_upgrade_id>
		<akeebasubs_affiliate_id><?php echo $item->akeebasubs_affiliate_id ?></akeebasubs_affiliate_id>
		<affiliate_comission><?php echo $item->affiliate_comission ?></affiliate_comission>
		<akeebasubs_invoice_id><?php echo $item->akeebasubs_invoice_id ?></akeebasubs_invoice_id>
		<prediscount_amount><?php echo $item->prediscount_amount ?></prediscount_amount>
		<discount_amount><?php echo $item->discount_amount ?></discount_amount>
		<contact_flag><?php echo $item->contact_flag ?></contact_flag>
		<first_contact><?php echo $item->first_contact ?></first_contact>
		<second_contact><?php echo $item->second_contact ?></second_contact>
		<after_contact><?php echo $item->after_contact ?></after_contact>
		<net_amount_alt><?php echo $item->net_amount_alt ?></net_amount_alt>
		<tax_amount_alt><?php echo $item->tax_amount_alt ?></tax_amount_alt>
		<gross_amount_alt><?php echo $item->gross_amount_alt ?></gross_amount_alt>
		<prediscount_amount_alt><?php echo $item->prediscount_amount_alt ?></prediscount_amount_alt>
		<discount_amount_alt><?php echo $item->discount_amount_alt ?></discount_amount_alt>
		<affiliate_comission_alt><?php echo $item->affiliate_comission_alt ?></affiliate_comission_alt>
		<allow_renew><?php echo $item->affiliate_comission_alt ?></allow_renew>

		<user>
			<name><?php echo $item->juser->name ?></name>
			<username><?php echo $item->juser->username ?></username>
			<email><?php echo $item->juser->email ?></email>
			<isbusiness><?php echo $item->user->isbusiness ?></isbusiness>
			<businessname><?php echo $item->user->businessname ?></businessname>
			<occupation><?php echo $item->user->occupation ?></occupation>
			<vatnumber><?php echo $item->user->occupation ?></vatnumber>
			<viesregistered><?php echo $item->user->viesregistered ?></viesregistered>
			<taxauthority><?php echo $item->user->taxauthority ?></taxauthority>
			<address1><?php echo $item->user->address1 ?></address1>
			<address2><?php echo $item->user->address2 ?></address2>
			<city><?php echo $item->user->city ?></city>
			<state><?php echo $item->user->state ?></state>
			<zip><?php echo $item->user->zip ?></zip>
			<country><?php echo $item->user->country ?></country>
			<notes><![CDATA[ <?php echo str_replace(']]>', '] ] >', $item->user->notes) ?>]]></notes>
			<params><![CDATA[ <?php echo str_replace(']]>', '] ] >', json_encode($item->user->params)) ?>]]></params>
		</user>

		<level>
			<title><?php echo $item->level->title ?></title>
			<description><?php echo $item->level->description ?></description>
			<duration><?php echo $item->level->duration ?></duration>
		</level>
	</subscription>
<?php endforeach ?>
</subscriptions>
