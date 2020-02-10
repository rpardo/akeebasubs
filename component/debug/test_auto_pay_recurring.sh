#!/bin/bash
##
## Wait for a new subscription and automatically mark it as a paid recurring subscrption, sending both subscription
## events (subscription_created and subscription_payment_succeeded) at the same time.
##
while (true);
do
  TRALALA=`echo "SELECT akeebasubs_subscription_id FROM b00t_akeebasubs_subscriptions where enabled = 0 and processor = 'paddle' and state = 'N' LIMIT 0,1" | mysql -u boot --password=boot boot -N 2> /dev/null`

  if [[ -z $TRALALA ]]; then
    echo "Nothing to do"
    sleep 1;
    continue;
  fi

  echo "Activating recurring subscription ${TRALALA}"

  pushd /Users/nicholas/Sites/boot/cli || exit

  php ./akeebasubs_callback_debug.php --subscription=$TRALALA --webhook=subscription_created &
  php ./akeebasubs_callback_debug.php --subscription=$TRALALA --webhook=subscription_payment_succeeded &

  popd || exit

  sleep 5;
done
