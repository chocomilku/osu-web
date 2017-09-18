<?php

/**
 *    Copyright 2015-2017 ppy Pty. Ltd.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Listeners\Fulfillment;

use App\Events\Fulfillment\PaymentCancelled;
use App\Events\Fulfillment\PaymentCompleted;
use App\Libraries\Fulfillments\FulfillmentFactory;
use App\Traits\StoreNotifiable;

class PaymentSubscribers
{
    use StoreNotifiable;

    public function onPaymentCompleted($event)
    {
        $fulfillers = FulfillmentFactory::createFulfillersFor($event->order);
        $count = count($fulfillers);
        $this->notify("onPaymentCompleted: `Order {$event->order->order_id}`, dispatching `{$count}` fulfillers");

        // This should probably be shoved off into a queue processor somewhere...
        foreach ($fulfillers as $fulfiller) {
            $fulfiller->run();
        }
    }

    public function onPaymentCancelled($event)
    {
        $fulfillers = FulfillmentFactory::createFulfillersFor($event->order);
        $count = count($fulfillers);
        $this->notify("onPaymentCancelled: `Order {$event->order->order_id}`, dispatching `{$count}` fulfillers");

        // This should probably be shoved off into a queue processor somewhere...
        foreach ($fulfillers as $fulfiller) {
            $fulfiller->revoke();
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            PaymentCancelled::class,
            static::class.'@onPaymentCancelled'
        );

        $events->listen(
            PaymentCompleted::class,
            static::class.'@onPaymentCompleted'
        );
    }
}
